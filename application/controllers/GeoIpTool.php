<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Command-line maintenance for the visitor-location GeoIP database. CLI only.
// The same two actions are also buttons on Reports > Visitor Locations
// (AdminReports::visitorGeoMaintenance), so SSH is optional.
// On the server (uses the live database):
//
//   php index.php GeoIpTool update
//   php index.php GeoIpTool backfill
//
// Locally, prefix HTTP_HOST=localhost so config.php picks the DEV database.
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
		try {
			echo $this->geoip->installLatest(), "\n";
		} catch (\Exception $e) {
			$this->fail($e->getMessage());
		}
	}

	public function backfill()
	{
		if (!$this->geoip->isAvailable()) {
			$this->fail('GeoIP database not installed - run `GeoIpTool update` first');
		}
		echo 'Resolved ', $this->geoip->backfill(), " visit(s)\n";
	}

	private function fail($message)
	{
		fwrite(STDERR, $message . "\n");
		exit(1);
	}
}
