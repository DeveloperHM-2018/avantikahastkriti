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

	// Returns ['country_code','country','region','city'], or null when the
	// database isn't installed (caller leaves the row pending for backfill).
	// An address the database can't place comes back as country_code 'ZZ'.
	public function lookup($ip)
	{
		$unknown = ['country_code' => self::UNKNOWN, 'country' => null, 'region' => null, 'city' => null];
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
}
