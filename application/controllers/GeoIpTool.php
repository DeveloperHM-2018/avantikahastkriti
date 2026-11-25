<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Command-line maintenance for the visitor-location GeoIP database. CLI only.
// CI needs these env vars on the command line or config.php prints nothing:
//
//   HTTP_HOST=localhost REQUEST_URI=/index.php php index.php GeoIpTool update
//   HTTP_HOST=localhost REQUEST_URI=/index.php php index.php GeoIpTool backfill
//
// `update` downloads this month's DB-IP City Lite file (free, CC BY 4.0, new
// edition each month - a monthly cron is enough) and swaps it in atomically.
// `backfill` fills location (and map coordinates) on logged visits that have
// an IP but no lookup yet.
class GeoIpTool extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!is_cli()) {
			show_404();
		}
		$this->load->library('GeoIp');
	}

	public function update()
	{
		$dir = dirname(GeoIp::DB_PATH);
		if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
			$this->fail("Cannot create $dir");
		}

		// The new edition can appear a day or two into the month, so fall
		// back to last month's if this month's isn't published yet.
		$gz = $dir . '/download.mmdb.gz';
		$ok = false;
		foreach ([date('Y-m'), date('Y-m', strtotime('first day of last month'))] as $edition) {
			$url = "https://download.db-ip.com/free/dbip-city-lite-$edition.mmdb.gz";
			echo "Downloading $url\n";
			if ($this->download($url, $gz)) {
				$ok = true;
				break;
			}
		}
		if (!$ok) {
			@unlink($gz);
			$this->fail('Download failed');
		}

		$tmp = $dir . '/download.mmdb';
		$in = gzopen($gz, 'rb');
		$out = fopen($tmp, 'wb');
		if (!$in || !$out) {
			$this->fail('Cannot unpack download');
		}
		while (!gzeof($in)) {
			fwrite($out, gzread($in, 1 << 20));
		}
		gzclose($in);
		fclose($out);
		unlink($gz);

		// Make sure the file actually opens before it replaces a working one.
		try {
			$reader = new \MaxMind\Db\Reader($tmp);
			$type = $reader->metadata()->databaseType;
			$reader->close();
		} catch (\Exception $e) {
			@unlink($tmp);
			$this->fail('Downloaded file is not a valid database: ' . $e->getMessage());
		}
		rename($tmp, GeoIp::DB_PATH);
		echo "Installed $type (" . round(filesize(GeoIp::DB_PATH) / 1048576) . " MB) at " . GeoIp::DB_PATH . "\n";
	}

	public function backfill()
	{
		if (!$this->geoip->isAvailable()) {
			$this->fail('GeoIP database not installed - run `GeoIpTool update` first');
		}
		$done = 0;
		$lastId = 0; // keyset paging, so a row the database still can't place isn't re-read forever
		$cache = [];
		do {
			$rows = $this->CommonModel->runQuery(
				"SELECT id, ip_address FROM tbl_page_session_log
				WHERE ip_address IS NOT NULL
				AND (country_code IS NULL OR (country_code != '" . GeoIp::UNKNOWN . "' AND latitude IS NULL))
				AND id > $lastId
				ORDER BY id LIMIT 500",
				1
			) ?: [];
			foreach ($rows as $row) {
				$ip = $row['ip_address'];
				if (!isset($cache[$ip])) {
					$cache[$ip] = $this->geoip->lookup($ip);
				}
				$this->db->where('id', $row['id'])->update('tbl_page_session_log', $cache[$ip]);
				$lastId = (int) $row['id'];
				$done++;
			}
		} while (count($rows) === 500);
		echo "Resolved $done visit(s)\n";
	}

	private function download($url, $dest)
	{
		$fp = fopen($dest, 'wb');
		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_FILE => $fp,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_FAILONERROR => true,
			CURLOPT_CONNECTTIMEOUT => 20,
			CURLOPT_TIMEOUT => 600,
		]);
		$ok = curl_exec($ch);
		curl_close($ch);
		fclose($fp);
		return $ok && filesize($dest) > 0;
	}

	private function fail($message)
	{
		fwrite(STDERR, $message . "\n");
		exit(1);
	}
}
