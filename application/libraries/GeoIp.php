<?php

defined('BASEPATH') or exit('No direct script access allowed');

// composer_autoload is off in config.php, so load it here like Web.php does.
require_once APPPATH . '../vendor/autoload.php';

use MaxMind\Db\Reader;

// IP -> country / region / city, read from a local DB-IP City Lite database
// (CC BY 4.0, https://db-ip.com) so visitor IPs are never sent to a third
// party and there is no rate limit. The .mmdb file is not in git (about
// 130 MB) - `php index.php GeoIpTool update` downloads it on each server.
//
// Used for analytics only. The client IP below honours proxy headers, which
// a visitor can forge - that can only skew their own row in a report, so it
// must never be reused for anything security-related.
class GeoIp
{
	const DB_PATH = APPPATH . 'geoip/dbip-city-lite.mmdb';
	const UNKNOWN = 'ZZ';

	private $reader = null;
	private $opened = false;

	public function isAvailable()
	{
		return is_file(self::DB_PATH);
	}

	// Cloudflare's header first, then the first public address in
	// X-Forwarded-For / X-Real-IP (load balancers), then the socket address.
	public function clientIp()
	{
		$candidates = [];
		if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
			$candidates[] = $_SERVER['HTTP_CF_CONNECTING_IP'];
		}
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			foreach (explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']) as $ip) {
				$candidates[] = $ip;
			}
		}
		if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
			$candidates[] = $_SERVER['HTTP_X_REAL_IP'];
		}
		foreach ($candidates as $ip) {
			$ip = trim($ip);
			if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
				return $ip;
			}
		}
		$remote = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		return filter_var($remote, FILTER_VALIDATE_IP) ? $remote : null;
	}

	// Returns country_code, country, region, city, latitude, longitude - or null when the
	// database isn't installed (caller leaves the row pending for backfill).
	// An address the database can't place comes back as country_code 'ZZ'.
	public function lookup($ip)
	{
		$unknown = ['country_code' => self::UNKNOWN, 'country' => null, 'region' => null, 'city' => null, 'latitude' => null, 'longitude' => null];
		$reader = $this->reader();
		if (!$reader) {
			return null;
		}
		if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
			return $unknown;
		}
		try {
			$record = $reader->get($ip);
		} catch (\Exception $e) {
			return $unknown;
		}
		if (empty($record['country']['iso_code'])) {
			return $unknown;
		}
		return [
			'country_code' => strtoupper($record['country']['iso_code']),
			'country' => $this->name($record, 'country'),
			'region' => isset($record['subdivisions'][0]['names']['en']) ? mb_substr($record['subdivisions'][0]['names']['en'], 0, 100) : null,
			'city' => $this->name($record, 'city'),
			'latitude' => isset($record['location']['latitude']) ? round((float) $record['location']['latitude'], 6) : null,
			'longitude' => isset($record['location']['longitude']) ? round((float) $record['location']['longitude'], 6) : null,
		];
	}

	private function name($record, $key)
	{
		return isset($record[$key]['names']['en']) ? mb_substr($record[$key]['names']['en'], 0, 100) : null;
	}

	private function reader()
	{
		if (!$this->opened) {
			$this->opened = true;
			if ($this->isAvailable()) {
				try {
					$this->reader = new Reader(self::DB_PATH);
				} catch (\Exception $e) {
					log_message('error', 'GeoIp: cannot open ' . self::DB_PATH . ': ' . $e->getMessage());
				}
			}
		}
		return $this->reader;
	}

	// ---- maintenance: used by GeoIpTool (command line) and by the buttons
	// on the Visitor Locations report, so both run exactly the same code.

	// Edition date and size of the installed database, or null if missing.
	public function databaseInfo()
	{
		if (!$this->isAvailable()) {
			return null;
		}
		$info = ['size_mb' => round(filesize(self::DB_PATH) / 1048576), 'built' => null];
		try {
			$r = new Reader(self::DB_PATH);
			$info['built'] = date('Y-m-d', $r->metadata()->buildEpoch);
			$r->close();
		} catch (\Exception $e) {
			$info['built'] = null;
		}
		return $info;
	}

	// Downloads this month's DB-IP City Lite (or last month's, if this one
	// isn't published yet - it can appear a day or two in), checks it opens,
	// then swaps it in. Returns a one-line summary; throws on failure and
	// leaves the working database untouched.
	public function installLatest()
	{
		@set_time_limit(600);
		$dir = dirname(self::DB_PATH);
		if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
			throw new \RuntimeException("Cannot create folder $dir");
		}
		$gz = $dir . '/download.mmdb.gz';
		$tmp = $dir . '/download.mmdb';
		$ok = false;
		foreach ([date('Y-m'), date('Y-m', strtotime('first day of last month'))] as $edition) {
			if ($this->download("https://download.db-ip.com/free/dbip-city-lite-$edition.mmdb.gz", $gz)) {
				$ok = true;
				break;
			}
		}
		if (!$ok) {
			@unlink($gz);
			throw new \RuntimeException('Could not download the location database from db-ip.com.');
		}

		$in = gzopen($gz, 'rb');
		$out = fopen($tmp, 'wb');
		if (!$in || !$out) {
			@unlink($gz);
			throw new \RuntimeException('Could not unpack the download (is the folder writable?).');
		}
		while (!gzeof($in)) {
			fwrite($out, gzread($in, 1 << 20));
		}
		gzclose($in);
		fclose($out);
		@unlink($gz);

		try {
			$r = new Reader($tmp);
			$type = $r->metadata()->databaseType;
			$r->close();
		} catch (\Exception $e) {
			@unlink($tmp);
			throw new \RuntimeException('The downloaded file is not a valid database: ' . $e->getMessage());
		}
		if (!rename($tmp, self::DB_PATH)) {
			@unlink($tmp);
			throw new \RuntimeException('Could not replace the old database file.');
		}
		$this->reader = null;
		$this->opened = false;
		return "Installed $type (" . round(filesize(self::DB_PATH) / 1048576) . ' MB).';
	}

	// Logged visits that have an IP but no location (or no map coordinates)
	// yet. Callers check this before backfill().
	public function pendingCount()
	{
		$CI = &get_instance();
		$row = $CI->db->query(
			"SELECT COUNT(*) AS n FROM tbl_page_session_log
			WHERE ip_address IS NOT NULL
			AND (country_code IS NULL OR (country_code != '" . self::UNKNOWN . "' AND latitude IS NULL))"
		)->row_array();
		return (int) $row['n'];
	}

	// Fills location on up to $limit pending visits (null = all) and returns
	// how many it resolved. Keyset paging, so a row the database still can't
	// place isn't re-read forever. Returns 0 if the database isn't installed.
	public function backfill($limit = null)
	{
		if (!$this->isAvailable()) {
			return 0;
		}
		@set_time_limit(300);
		$CI = &get_instance();
		$done = 0;
		$lastId = 0;
		$cache = [];
		do {
			$batch = $limit === null ? 500 : min(500, $limit - $done);
			if ($batch <= 0) {
				break;
			}
			$rows = $CI->db->query(
				"SELECT id, ip_address FROM tbl_page_session_log
				WHERE ip_address IS NOT NULL
				AND (country_code IS NULL OR (country_code != '" . self::UNKNOWN . "' AND latitude IS NULL))
				AND id > " . (int) $lastId . "
				ORDER BY id LIMIT " . (int) $batch
			)->result_array();
			foreach ($rows as $row) {
				$ip = $row['ip_address'];
				if (!array_key_exists($ip, $cache)) {
					$cache[$ip] = $this->lookup($ip);
				}
				if ($cache[$ip] !== null) {
					$CI->db->where('id', $row['id'])->update('tbl_page_session_log', $cache[$ip]);
					$done++;
				}
				$lastId = (int) $row['id'];
			}
		} while (count($rows) === $batch);
		return $done;
	}

	private function download($url, $dest)
	{
		$fp = fopen($dest, 'wb');
		if (!$fp) {
			return false;
		}
		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_FILE => $fp,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_FAILONERROR => true,
			CURLOPT_CONNECTTIMEOUT => 20,
			CURLOPT_TIMEOUT => 540,
		]);
		$ok = curl_exec($ch);
		curl_close($ch);
		fclose($fp);
		return $ok && filesize($dest) > 0;
	}
}
