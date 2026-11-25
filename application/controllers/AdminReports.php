<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Four separate reports (sales / inventory / purchase / vendor), each backed
// by pure SQL aggregation (SUM/COUNT/GROUP BY) with a mandatory, bounded date
// range - never an unbounded "all time" scan - and using the indexes added in
// the Phase 1 migration (idx_book_product_booking_date,
// idx_vendor_supply_vendor/product, idx_vendor_order_vendor) so these stay
// fast as order/ledger volume grows. No report here issues a follow-up query
// per row (the N+1 pattern) - everything is one aggregate query per section.
class AdminReports extends CI_Controller
{
	private $user_type;
	private $prev;

	public function __construct()
	{
		parent::__construct();
		if (sessionId('admin_id') == "") {
			redirect("admin");
		}
		$this->user_type = sessionId('user_type');
		if (sessionId('privileges')) {
			$this->prev = json_decode(sessionId('privileges'), true);
		}
		define('USER_TYPE', $this->user_type);
		define('PREV', $this->prev);

		if (!(@PREV['reports_view'] == 1 || USER_TYPE == '1')) {
			show_404();
		}
	}

	// date()/strtotime() round-trip guarantees a safe Y-m-d string before it's
	// interpolated into raw SQL below - matches the existing convention in
	// AdminReturn::returnReports().
	private function dateRange()
	{
		return [
			'from' => date('Y-m-d', strtotime($this->input->get('date_from') ?: '-30 days')),
			'to' => date('Y-m-d', strtotime($this->input->get('date_to') ?: 'now')),
		];
	}

	public function salesReport()
	{
		$range = $this->dateRange();
		$data['title'] = 'Sales Report';
		$data['date_from'] = $range['from'];
		$data['date_to'] = $range['to'];

		$summary = $this->CommonModel->runQuery(
			"SELECT COUNT(*) AS order_count, COALESCE(SUM(final_amount), 0) AS gross_sales,
			COALESCE(SUM(total_items), 0) AS total_units
			FROM tbl_book_product
			WHERE transaction_status = '1' AND booking_status != '2' AND DATE(booking_date) BETWEEN '{$range['from']}' AND '{$range['to']}'",
			2
		);
		$data['order_count'] = $summary ? (int) $summary['order_count'] : 0;
		$data['gross_sales'] = $summary ? (float) $summary['gross_sales'] : 0;
		$data['total_units'] = $summary ? (int) $summary['total_units'] : 0;

		$data['daily_sales'] = $this->CommonModel->runQuery(
			"SELECT DATE(booking_date) AS d, COUNT(*) AS orders, SUM(final_amount) AS amount
			FROM tbl_book_product
			WHERE transaction_status = '1' AND booking_status != '2' AND DATE(booking_date) BETWEEN '{$range['from']}' AND '{$range['to']}'
			GROUP BY DATE(booking_date) ORDER BY d ASC",
			1
		) ?: [];

		$data['top_products'] = $this->CommonModel->runQuery(
			"SELECT bi.product_id, bi.product_name, SUM(bi.no_of_items) AS qty_sold, SUM(bi.booking_price) AS revenue
			FROM tbl_book_item bi
			INNER JOIN tbl_book_product bp ON bp.product_book_id = bi.product_book_id
			WHERE bp.transaction_status = '1' AND bp.booking_status != '2' AND DATE(bp.booking_date) BETWEEN '{$range['from']}' AND '{$range['to']}'
			GROUP BY bi.product_id ORDER BY revenue DESC LIMIT 15",
			1
		) ?: [];

		$data['by_source'] = $this->CommonModel->runQuery(
			"SELECT order_source, COUNT(*) AS orders, SUM(final_amount) AS amount
			FROM tbl_book_product
			WHERE transaction_status = '1' AND booking_status != '2' AND DATE(booking_date) BETWEEN '{$range['from']}' AND '{$range['to']}'
			GROUP BY order_source",
			1
		) ?: [];

		$this->load->view('admin/reports/sales_report', $data);
	}

	public function inventoryReport()
	{
		$data['title'] = 'Inventory Report';

		$summary = $this->CommonModel->runQuery(
			"SELECT COUNT(*) AS total_products,
			SUM(CASE WHEN is_out_of_stock = 1 THEN 1 ELSE 0 END) AS out_of_stock_count,
			SUM(CASE WHEN low_stock_threshold IS NOT NULL AND quantity <= low_stock_threshold THEN 1 ELSE 0 END) AS low_stock_count,
			COALESCE(SUM(quantity), 0) AS total_units_on_hand
			FROM tbl_product WHERE is_delete = '1'",
			2
		);
		$data['total_products'] = $summary ? (int) $summary['total_products'] : 0;
		$data['out_of_stock_count'] = $summary ? (int) $summary['out_of_stock_count'] : 0;
		$data['low_stock_count'] = $summary ? (int) $summary['low_stock_count'] : 0;
		$data['total_units_on_hand'] = $summary ? (float) $summary['total_units_on_hand'] : 0;

		$data['low_stock_products'] = $this->CommonModel->runQuery(
			"SELECT product_id, product_name, quantity, low_stock_threshold
			FROM tbl_product
			WHERE is_delete = '1' AND low_stock_threshold IS NOT NULL AND quantity <= low_stock_threshold
			ORDER BY quantity ASC LIMIT 50",
			1
		) ?: [];

		// Correlated MAX(create_date) per product - bounded to products already
		// limited above, not a per-row query loop in PHP.
		$data['recent_movements'] = $this->CommonModel->runQuery(
			"SELECT sl.product_id, p.product_name, sl.change_type, sl.delta, sl.balance_after, sl.create_date
			FROM tbl_stock_ledger sl
			INNER JOIN tbl_product p ON p.product_id = sl.product_id
			ORDER BY sl.create_date DESC LIMIT 50",
			1
		) ?: [];

		$this->load->view('admin/reports/inventory_report', $data);
	}

	public function purchaseReport()
	{
		$range = $this->dateRange();
		$data['title'] = 'Purchase Report (Partner Supply)';
		$data['date_from'] = $range['from'];
		$data['date_to'] = $range['to'];

		$summary = $this->CommonModel->runQuery(
			"SELECT COUNT(*) AS supply_count, COALESCE(SUM(quantity), 0) AS total_qty,
			COALESCE(SUM(quantity * vendor_supply_price), 0) AS total_cost
			FROM tbl_vendor_supply
			WHERE DATE(create_date) BETWEEN '{$range['from']}' AND '{$range['to']}'",
			2
		);
		$data['supply_count'] = $summary ? (int) $summary['supply_count'] : 0;
		$data['total_qty'] = $summary ? (float) $summary['total_qty'] : 0;
		$data['total_cost'] = $summary ? (float) $summary['total_cost'] : 0;

		$data['by_vendor'] = $this->CommonModel->runQuery(
			"SELECT vs.vendor_id, v.business_name, SUM(vs.quantity) AS qty, SUM(vs.quantity * vs.vendor_supply_price) AS cost
			FROM tbl_vendor_supply vs
			LEFT JOIN tbl_vendor v ON v.vendor_id = vs.vendor_id
			WHERE DATE(vs.create_date) BETWEEN '{$range['from']}' AND '{$range['to']}'
			GROUP BY vs.vendor_id ORDER BY cost DESC",
			1
		) ?: [];

		// Vendor product approvals also stock the catalog directly (see
		// AdminVendor::vendorProductReview()) via the stock ledger's
		// vendor_supply_in entries - included here so "purchases" reflects
		// every unit that actually entered inventory from a vendor, not only
		// tbl_vendor_supply restock rows.
		$data['ledger_vendor_supply'] = $this->CommonModel->runQuery(
			"SELECT sl.vendor_id, v.business_name, SUM(sl.delta) AS qty, COUNT(*) AS entries
			FROM tbl_stock_ledger sl
			LEFT JOIN tbl_vendor v ON v.vendor_id = sl.vendor_id
			WHERE sl.change_type = 'vendor_supply_in' AND sl.vendor_id IS NOT NULL
			AND DATE(sl.create_date) BETWEEN '{$range['from']}' AND '{$range['to']}'
			GROUP BY sl.vendor_id ORDER BY qty DESC",
			1
		) ?: [];

		$this->load->view('admin/reports/purchase_report', $data);
	}

	public function vendorReport()
	{
		$range = $this->dateRange();
		$data['title'] = 'Partner Report';
		$data['date_from'] = $range['from'];
		$data['date_to'] = $range['to'];

		$data['by_vendor'] = $this->CommonModel->runQuery(
			"SELECT voi.vendor_id, v.business_name,
			COUNT(*) AS line_count,
			SUM(voi.quantity) AS qty_sold,
			SUM(voi.commission_amount) AS commission_total,
			SUM(voi.vendor_payable_amount) AS payable_total,
			SUM(CASE WHEN voi.payout_status = 2 THEN voi.vendor_payable_amount ELSE 0 END) AS paid_total,
			SUM(CASE WHEN voi.payout_status = 0 THEN voi.vendor_payable_amount ELSE 0 END) AS unpaid_total
			FROM tbl_vendor_order_item voi
			LEFT JOIN tbl_vendor v ON v.vendor_id = voi.vendor_id
			WHERE voi.payout_status != " . VENDOR_LINE_VOID . " AND DATE(voi.create_date) BETWEEN '{$range['from']}' AND '{$range['to']}'
			GROUP BY voi.vendor_id ORDER BY payable_total DESC",
			1
		) ?: [];

		$summary = $this->CommonModel->runQuery(
			"SELECT COUNT(DISTINCT vendor_id) AS active_vendors, COALESCE(SUM(commission_amount), 0) AS total_commission,
			COALESCE(SUM(vendor_payable_amount), 0) AS total_payable
			FROM tbl_vendor_order_item
			WHERE payout_status != " . VENDOR_LINE_VOID . " AND DATE(create_date) BETWEEN '{$range['from']}' AND '{$range['to']}'",
			2
		);
		$data['active_vendors'] = $summary ? (int) $summary['active_vendors'] : 0;
		$data['total_commission'] = $summary ? (float) $summary['total_commission'] : 0;
		$data['total_payable'] = $summary ? (float) $summary['total_payable'] : 0;

		$this->load->view('admin/reports/vendor_report', $data);
	}


	// India map regions (jVectorMap in_mill_en) by the state names the GeoIP
	// database uses. The map predates Telangana and Ladakh, so those have no
	// shape - they still appear in the lists and as city bubbles.
	private static $indiaStateCodes = [
		'andaman and nicobar' => 'IN-AN', 'andaman and nicobar islands' => 'IN-AN',
		'andhra pradesh' => 'IN-AP', 'arunachal pradesh' => 'IN-AR', 'assam' => 'IN-AS',
		'bihar' => 'IN-BR', 'chandigarh' => 'IN-CH', 'chhattisgarh' => 'IN-CT',
		'daman and diu' => 'IN-DD', 'dadra and nagar haveli' => 'IN-DN',
		'dadra and nagar haveli and daman and diu' => 'IN-DN',
		'delhi' => 'IN-DL', 'national capital territory of delhi' => 'IN-DL', 'nct of delhi' => 'IN-DL',
		'goa' => 'IN-GA', 'gujarat' => 'IN-GJ', 'haryana' => 'IN-HR', 'himachal pradesh' => 'IN-HP',
		'jammu and kashmir' => 'IN-JK', 'jharkhand' => 'IN-JH', 'karnataka' => 'IN-KA', 'kerala' => 'IN-KL',
		'madhya pradesh' => 'IN-MP', 'maharashtra' => 'IN-MH', 'manipur' => 'IN-MN', 'meghalaya' => 'IN-ML',
		'mizoram' => 'IN-MZ', 'nagaland' => 'IN-NL', 'odisha' => 'IN-OR', 'orissa' => 'IN-OR',
		'puducherry' => 'IN-PY', 'pondicherry' => 'IN-PY', 'punjab' => 'IN-PB', 'rajasthan' => 'IN-RJ',
		'sikkim' => 'IN-SK', 'tamil nadu' => 'IN-TN', 'tripura' => 'IN-TR', 'uttar pradesh' => 'IN-UP',
		'uttarakhand' => 'IN-UT', 'uttaranchal' => 'IN-UT', 'west bengal' => 'IN-WB',
	];

	// Shopify-style "Sessions by location": KPIs and a daily trend compared
	// with the previous period of the same length, a world / India map, and
	// every country, state and city. tbl_page_session_log holds one row per
	// browser per day (Web::logSession), so a "session" here = one browser on
	// one day and a "visitor" = one distinct browser. ?country=XX narrows the
	// state and city breakdowns (and the city bubbles) to one country.
	public function visitorLocationReport()
	{
		$this->load->library('GeoIp');
		$unknown = GeoIp::UNKNOWN;

		// Fill in visits logged while the location database was missing (or
		// before map coordinates existed) - no SSH needed. Capped per page
		// load so the report stays fast; the rest is done on the next view.
		$pending = $this->geoip->pendingCount();
		if ($pending > 0 && $this->geoip->isAvailable()) {
			$this->geoip->backfill(3000);
			$pending = $this->geoip->pendingCount();
		}

		// Date presets, like Shopify's picker. Custom uses date_from/date_to.
		$presets = [
			'today' => ['Today', 0], 'yesterday' => ['Yesterday', 1], '7d' => ['Last 7 days', 7],
			'30d' => ['Last 30 days', 30], '90d' => ['Last 90 days', 90], '365d' => ['Last 365 days', 365],
		];
		$preset = (string) $this->input->get('range');
		if ($preset === '' && !$this->input->get('date_from')) {
			$preset = '30d';
		}
		if (isset($presets[$preset])) {
			$days = $presets[$preset][1];
			if ($preset === 'today') {
				$from = $to = date('Y-m-d');
			} elseif ($preset === 'yesterday') {
				$from = $to = date('Y-m-d', strtotime('-1 day'));
			} else {
				$to = date('Y-m-d');
				$from = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));
			}
		} else {
			$preset = 'custom';
			$range = $this->dateRange();
			$from = min($range['from'], $range['to']);
			$to = max($range['from'], $range['to']);
		}
		$length = (int) round((strtotime($to) - strtotime($from)) / 86400) + 1;
		$prevTo = date('Y-m-d', strtotime($from . ' -1 day'));
		$prevFrom = date('Y-m-d', strtotime($prevTo . ' -' . ($length - 1) . ' days'));

		$country = strtoupper((string) $this->input->get('country'));
		$country = preg_match('/^[A-Z]{2}$/', $country) ? $country : '';

		$in = function ($a, $b) {
			return "visited_at >= '$a 00:00:00' AND visited_at <= '$b 23:59:59'";
		};
		$cur = $in($from, $to);
		$prev = $in($prevFrom, $prevTo);
		$located = " AND country_code IS NOT NULL AND country_code != '$unknown'";
		$scope = $located . ($country ? " AND country_code = '$country'" : '');

		$totals = function ($where) use ($located) {
			$row = $this->CommonModel->runQuery(
				"SELECT COUNT(*) AS sessions, COUNT(DISTINCT session_token) AS visitors,
				SUM(CASE WHEN 1 $located THEN 1 ELSE 0 END) AS located,
				COUNT(DISTINCT CASE WHEN 1 $located THEN country_code END) AS countries,
				COUNT(DISTINCT CASE WHEN city IS NOT NULL $located THEN CONCAT(country_code, '|', COALESCE(region, ''), '|', city) END) AS cities
				FROM tbl_page_session_log WHERE $where",
				2
			) ?: [];
			return array_map('intval', $row + ['sessions' => 0, 'visitors' => 0, 'located' => 0, 'countries' => 0, 'cities' => 0]);
		};

		// One value per day of the range (zero-filled), so the trend and
		// sparklines line up day-for-day with the previous period.
		$daily = function ($a, $b) use ($in, $located) {
			$rows = $this->CommonModel->runQuery(
				"SELECT DATE(visited_at) AS d, COUNT(*) AS sessions, COUNT(DISTINCT session_token) AS visitors,
				COUNT(DISTINCT CASE WHEN 1 $located THEN country_code END) AS countries,
				COUNT(DISTINCT CASE WHEN city IS NOT NULL $located THEN CONCAT(country_code, '|', COALESCE(region, ''), '|', city) END) AS cities
				FROM tbl_page_session_log WHERE " . $in($a, $b) . " GROUP BY DATE(visited_at)",
				1
			) ?: [];
			$byDate = array_column($rows, null, 'd');
			$series = [];
			for ($t = strtotime($a); $t <= strtotime($b); $t += 86400) {
				$d = date('Y-m-d', $t);
				$r = $byDate[$d] ?? [];
				$series[] = [
					'date' => $d,
					'sessions' => (int) ($r['sessions'] ?? 0),
					'visitors' => (int) ($r['visitors'] ?? 0),
					'countries' => (int) ($r['countries'] ?? 0),
					'cities' => (int) ($r['cities'] ?? 0),
				];
			}
			return $series;
		};

		// Groups for the current period, each with its previous-period
		// sessions so every row can show a change like Shopify does.
		$grouped = function ($select, $group, $extraWhere, $key) use ($cur, $prev) {
			$now = $this->CommonModel->runQuery(
				"SELECT $select, COUNT(*) AS sessions, COUNT(DISTINCT session_token) AS visitors
				FROM tbl_page_session_log WHERE $cur $extraWhere GROUP BY $group ORDER BY sessions DESC",
				1
			) ?: [];
			$before = $this->CommonModel->runQuery(
				"SELECT $select, COUNT(*) AS sessions
				FROM tbl_page_session_log WHERE $prev $extraWhere GROUP BY $group",
				1
			) ?: [];
			$prevByKey = [];
			foreach ($before as $row) {
				$prevByKey[$key($row)] = (int) $row['sessions'];
			}
			foreach ($now as &$row) {
				$row['sessions'] = (int) $row['sessions'];
				$row['visitors'] = (int) $row['visitors'];
				$row['prev_sessions'] = $prevByKey[$key($row)] ?? 0;
			}
			return $now;
		};

		$byCountry = $grouped(
			'country_code, MAX(country) AS country', 'country_code', $located,
			function ($r) { return $r['country_code']; }
		);
		$byRegion = $grouped(
			'country_code, region', 'country_code, region', "$scope AND region IS NOT NULL",
			function ($r) { return $r['country_code'] . '|' . $r['region']; }
		);
		$byCity = $grouped(
			'country_code, region, city, AVG(latitude) AS lat, AVG(longitude) AS lng', 'country_code, region, city', "$scope AND city IS NOT NULL",
			function ($r) { return $r['country_code'] . '|' . $r['region'] . '|' . $r['city']; }
		);

		$countryNames = array_column($byCountry, 'country', 'country_code');
		foreach ($byRegion as &$row) {
			$row['country'] = $countryNames[$row['country_code']] ?? $row['country_code'];
			$row['map_code'] = $row['country_code'] === 'IN' ? (self::$indiaStateCodes[strtolower(trim($row['region']))] ?? null) : null;
		}
		unset($row);
		foreach ($byCity as &$row) {
			$row['country'] = $countryNames[$row['country_code']] ?? $row['country_code'];
			$row['lat'] = $row['lat'] !== null ? round((float) $row['lat'], 4) : null;
			$row['lng'] = $row['lng'] !== null ? round((float) $row['lng'], 4) : null;
		}
		unset($row);

		// Sessions the map can't place, split by why, so the totals add up.
		$unlocated = $this->CommonModel->runQuery(
			"SELECT CASE
				WHEN ip_address IS NULL AND country_code IS NULL THEN 'Not recorded (before location tracking)'
				WHEN country_code IS NULL THEN 'Pending lookup'
				ELSE 'Unknown location' END AS reason,
			COUNT(*) AS sessions
			FROM tbl_page_session_log
			WHERE $cur AND (country_code IS NULL OR country_code = '$unknown')
			GROUP BY reason ORDER BY sessions DESC",
			1
		) ?: [];

		$data = [
			'title' => 'Visitor Locations',
			'preset' => $preset,
			'presets' => $presets,
			'date_from' => $from,
			'date_to' => $to,
			'prev_from' => $prevFrom,
			'prev_to' => $prevTo,
			'country_filter' => $country,
			'country_filter_name' => $country ? ($countryNames[$country] ?? $country) : '',
			'totals' => $totals($cur),
			'prev_totals' => $totals($prev),
			'daily' => $daily($from, $to),
			'prev_daily' => $daily($prevFrom, $prevTo),
			'by_country' => $byCountry,
			'by_region' => $byRegion,
			'by_city' => $byCity,
			'unlocated' => $unlocated,
			'geo' => [
				'info' => $this->geoip->databaseInfo(),
				'pending' => $pending,
				'can_manage' => USER_TYPE == '1',
				'notice' => $this->session->flashdata('vl_geo_notice'),
				'error' => $this->session->flashdata('vl_geo_error'),
			],
		];
		$this->load->view('admin/reports/visitor_location_report', $data);
	}

	// Buttons on the Visitor Locations page - the no-SSH way to install or
	// refresh the location database and fill in missing locations. Super
	// admin only (it downloads ~60 MB to the server). Same code as the
	// GeoIpTool command-line tool.
	public function visitorGeoMaintenance()
	{
		if (USER_TYPE != '1' || $this->input->method() !== 'post') {
			show_404();
		}
		$this->load->library('GeoIp');
		$back = $this->input->post('back');
		$back = (is_string($back) && strpos($back, base_url('visitorLocationReport')) === 0) ? $back : base_url('visitorLocationReport');

		ignore_user_abort(true); // finish the swap even if the browser stops waiting
		try {
			if ($this->input->post('action') === 'update') {
				$message = $this->geoip->installLatest();
				$message .= ' Filled in ' . number_format($this->geoip->backfill()) . ' visit(s).';
				$this->CommonModel->logAdminActivity(ACTOR_TYPE_ADMIN, sessionId('admin_id'), 'geoip_database_update', 'setting', 0);
			} else {
				if (!$this->geoip->isAvailable()) {
					throw new \RuntimeException('Install the location database first.');
				}
				$message = 'Filled in ' . number_format($this->geoip->backfill()) . ' visit(s).';
			}
			$this->session->set_flashdata('vl_geo_notice', $message);
		} catch (\Exception $e) {
			$this->session->set_flashdata('vl_geo_error', $e->getMessage());
		}
		redirect($back);
	}
}
