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
			WHERE transaction_status = '1' AND DATE(booking_date) BETWEEN '{$range['from']}' AND '{$range['to']}'",
			2
		);
		$data['order_count'] = $summary ? (int) $summary['order_count'] : 0;
		$data['gross_sales'] = $summary ? (float) $summary['gross_sales'] : 0;
		$data['total_units'] = $summary ? (int) $summary['total_units'] : 0;

		$data['daily_sales'] = $this->CommonModel->runQuery(
			"SELECT DATE(booking_date) AS d, COUNT(*) AS orders, SUM(final_amount) AS amount
			FROM tbl_book_product
			WHERE transaction_status = '1' AND DATE(booking_date) BETWEEN '{$range['from']}' AND '{$range['to']}'
			GROUP BY DATE(booking_date) ORDER BY d ASC",
			1
		) ?: [];

		$data['top_products'] = $this->CommonModel->runQuery(
			"SELECT bi.product_id, bi.product_name, SUM(bi.no_of_items) AS qty_sold, SUM(bi.booking_price) AS revenue
			FROM tbl_book_item bi
			INNER JOIN tbl_book_product bp ON bp.product_book_id = bi.product_book_id
			WHERE bp.transaction_status = '1' AND DATE(bp.booking_date) BETWEEN '{$range['from']}' AND '{$range['to']}'
			GROUP BY bi.product_id ORDER BY revenue DESC LIMIT 15",
			1
		) ?: [];

		$data['by_source'] = $this->CommonModel->runQuery(
			"SELECT order_source, COUNT(*) AS orders, SUM(final_amount) AS amount
			FROM tbl_book_product
			WHERE transaction_status = '1' AND DATE(booking_date) BETWEEN '{$range['from']}' AND '{$range['to']}'
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
		$data['title'] = 'Purchase Report (Vendor Supply)';
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
		$data['title'] = 'Vendor Report';
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
			WHERE DATE(voi.create_date) BETWEEN '{$range['from']}' AND '{$range['to']}'
			GROUP BY voi.vendor_id ORDER BY payable_total DESC",
			1
		) ?: [];

		$summary = $this->CommonModel->runQuery(
			"SELECT COUNT(DISTINCT vendor_id) AS active_vendors, COALESCE(SUM(commission_amount), 0) AS total_commission,
			COALESCE(SUM(vendor_payable_amount), 0) AS total_payable
			FROM tbl_vendor_order_item
			WHERE DATE(create_date) BETWEEN '{$range['from']}' AND '{$range['to']}'",
			2
		);
		$data['active_vendors'] = $summary ? (int) $summary['active_vendors'] : 0;
		$data['total_commission'] = $summary ? (float) $summary['total_commission'] : 0;
		$data['total_payable'] = $summary ? (float) $summary['total_payable'] : 0;

		$this->load->view('admin/reports/vendor_report', $data);
	}
}
