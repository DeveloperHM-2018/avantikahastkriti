<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . '../vendor/autoload.php'; // Include Composer's autoloader

use Razorpay\Api\Api;

class AdminHome extends CI_Controller
{

	private $user_id;
	private $user_type;
	private $prev;
	public function __construct()
	{
		parent::__construct();
		if (sessionId('admin_id') == "") {
			redirect("admin");
		}
		$this->user_id = sessionId('user_id');
		$this->user_type = sessionId('user_type');
		$privileges = sessionId('privileges');
		$this->prev = !empty($privileges) ? json_decode($privileges, true) : [];
		define('USER_TYPE', $this->user_type);
		define('PREV', $this->prev);
	}

	public function dashboard()
	{
		$getRows['active_user'] = $this->CommonModel->getNumRows("user_registration", "user_status = '1'");
		$getRows['inactive_user'] = $this->CommonModel->getNumRows("user_registration", "user_status = '0'");
		$getRows['product_category'] = $this->CommonModel->getNumRows("category", "is_delete = '1'");
		$getRows['product_sub_category'] = $this->CommonModel->getNumRows("sub_category", "is_delete = '1'");
		$getRows['total_product'] = $this->CommonModel->getNumRows("product", "is_delete = '1'");
		$getRows['recent_orders'] = $this->CommonModel->getNumRows("book_product", "booking_status = '0'");
		$getRows['accepted_orders'] = $this->CommonModel->getNumRows("book_product", "booking_status = '1'");
		$getRows['dispatch_orders'] = $this->CommonModel->getNumRows("book_product", "booking_status = '3'");
		$getRows['completed_orders'] = $this->CommonModel->getNumRows("book_product", "booking_status = '4'");
		$getRows['canceled_orders'] = $this->CommonModel->getNumRows("book_product", "booking_status = '2'");
		$getRows['title'] = "Home";

		list($dateFrom, $dateTo, $range) = $this->resolveDateRange();
		$days = (strtotime($dateTo) - strtotime($dateFrom)) / 86400 + 1;
		$prevTo = date('Y-m-d', strtotime($dateFrom . ' -1 day'));
		$prevFrom = date('Y-m-d', strtotime($prevTo . ' -' . ($days - 1) . ' days'));

		$getRows['dateFrom'] = $dateFrom;
		$getRows['dateTo'] = $dateTo;
		$getRows['range'] = $range;

		$current = $this->getSalesSummary($dateFrom, $dateTo);
		$previous = $this->getSalesSummary($prevFrom, $prevTo);
		$currentSessions = $this->getSessionCount($dateFrom, $dateTo);
		$previousSessions = $this->getSessionCount($prevFrom, $prevTo);

		$getRows['gross_sale'] = $current['gross_sale'];
		$getRows['discounts'] = $current['discounts'];
		$getRows['shipping'] = $current['shipping'];
		$getRows['net_sale'] = $current['gross_sale'] - $current['discounts'];
		$getRows['total_orders'] = $current['total_orders'];
		$getRows['total_sessions'] = $currentSessions;

		$getRows['gross_sale_change'] = $this->percentChange($current['gross_sale'], $previous['gross_sale']);
		$getRows['net_sale_change'] = $this->percentChange($getRows['net_sale'], $previous['gross_sale'] - $previous['discounts']);
		$getRows['orders_change'] = $this->percentChange($current['total_orders'], $previous['total_orders']);
		$getRows['sessions_change'] = $this->percentChange($currentSessions, $previousSessions);

		$getRows['daily_series'] = $this->getDailySalesSeries($dateFrom, $dateTo);
		$getRows['sessions_series'] = $this->getDailySessionSeries($dateFrom, $dateTo);
		$getRows['sessions_series_prev'] = $this->getDailySessionSeries($prevFrom, $prevTo);

		$getRows['top_products'] = $this->getTopProducts($dateFrom, $dateTo, $prevFrom, $prevTo);

		$this->load->view('admin/index', $getRows);
	}

	private function resolveDateRange()
	{
		$range = $this->input->post('range') ?: 'last7';
		$today = date('Y-m-d');

		switch ($range) {
			case 'today':
				return [$today, $today, $range];
			case 'yesterday':
				$y = date('Y-m-d', strtotime('-1 day'));
				return [$y, $y, $range];
			case 'last30':
				return [date('Y-m-d', strtotime('-29 days')), $today, $range];
			case 'this_month':
				return [date('Y-m-01'), $today, $range];
			case 'custom':
				$from = $this->input->post('searchByDateFrom');
				$to = $this->input->post('searchByDateTo');
				if (empty($from) || empty($to)) {
					return [date('Y-m-d', strtotime('-6 days')), $today, 'last7'];
				}
				return [date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to)), $range];
			case 'last7':
			default:
				return [date('Y-m-d', strtotime('-6 days')), $today, 'last7'];
		}
	}

	private function getSalesSummary($dateFrom, $dateTo)
	{
		$row = $this->CommonModel->runQuery(
			"SELECT
				COALESCE(SUM(total_item_amount), 0) AS gross_sale,
				COALESCE(SUM(promocode_amount), 0) AS discounts,
				COALESCE(SUM(delivery_charges), 0) AS shipping,
				COUNT(*) AS total_orders
			FROM tbl_book_product
			WHERE booking_status != '2'
				AND DATE(create_date) BETWEEN '$dateFrom' AND '$dateTo'",
			2
		);

		return $row ?: ['gross_sale' => 0, 'discounts' => 0, 'shipping' => 0, 'total_orders' => 0];
	}

	private function getSessionCount($dateFrom, $dateTo)
	{
		$row = $this->CommonModel->runQuery(
			"SELECT COUNT(DISTINCT session_token) AS total
			FROM tbl_page_session_log
			WHERE DATE(visited_at) BETWEEN '$dateFrom' AND '$dateTo'",
			2
		);

		return $row ? (int) $row['total'] : 0;
	}

	private function percentChange($current, $previous)
	{
		if ($previous == 0) {
			return $current > 0 ? 100 : 0;
		}
		return round((($current - $previous) / $previous) * 100, 1);
	}

	private function getDailySalesSeries($dateFrom, $dateTo)
	{
		$rows = $this->CommonModel->runQuery(
			"SELECT
				DATE(create_date) AS sale_date,
				COALESCE(SUM(total_item_amount), 0) AS gross_sale,
				COALESCE(SUM(promocode_amount), 0) AS discounts,
				COUNT(*) AS total_orders
			FROM tbl_book_product
			WHERE booking_status != '2'
				AND DATE(create_date) BETWEEN '$dateFrom' AND '$dateTo'
			GROUP BY DATE(create_date)",
			1
		) ?: [];

		$byDate = [];
		foreach ($rows as $row) {
			$byDate[$row['sale_date']] = $row;
		}

		$series = [];
		$cursor = $dateFrom;
		while ($cursor <= $dateTo) {
			$row = isset($byDate[$cursor]) ? $byDate[$cursor] : ['gross_sale' => 0, 'discounts' => 0, 'total_orders' => 0];
			$series[] = [
				'date' => $cursor,
				'gross' => (float) $row['gross_sale'],
				'net' => (float) $row['gross_sale'] - (float) $row['discounts'],
				'orders' => (int) $row['total_orders'],
			];
			$cursor = date('Y-m-d', strtotime($cursor . ' +1 day'));
		}

		return $series;
	}

	private function getDailySessionSeries($dateFrom, $dateTo)
	{
		$rows = $this->CommonModel->runQuery(
			"SELECT DATE(visited_at) AS visit_date, COUNT(DISTINCT session_token) AS session_count
			FROM tbl_page_session_log
			WHERE DATE(visited_at) BETWEEN '$dateFrom' AND '$dateTo'
			GROUP BY DATE(visited_at)",
			1
		) ?: [];

		$byDate = [];
		foreach ($rows as $row) {
			$byDate[$row['visit_date']] = (int) $row['session_count'];
		}

		$series = [];
		$cursor = $dateFrom;
		while ($cursor <= $dateTo) {
			$series[] = ['date' => $cursor, 'sessions' => isset($byDate[$cursor]) ? $byDate[$cursor] : 0];
			$cursor = date('Y-m-d', strtotime($cursor . ' +1 day'));
		}

		return $series;
	}

	private function getTopProducts($dateFrom, $dateTo, $prevFrom, $prevTo)
	{
		$products = $this->CommonModel->runQuery(
			"SELECT
				book_item.product_id,
				book_item.product_name,
				SUM(book_item.no_of_items) AS total_qty,
				COUNT(DISTINCT book_item.product_book_id) AS total_orders,
				SUM(book_item.booking_price) AS total_revenue,
				image.image_path
			FROM tbl_book_item AS book_item
			INNER JOIN tbl_book_product AS book_product ON book_product.product_book_id = book_item.product_book_id
			LEFT JOIN (SELECT product_id, MIN(product_image_id) AS img_id FROM tbl_product_image GROUP BY product_id) AS main_img
				ON main_img.product_id = book_item.product_id
			LEFT JOIN tbl_product_image AS image ON image.product_image_id = main_img.img_id
			WHERE book_product.booking_status != '2'
				AND DATE(book_product.create_date) BETWEEN '$dateFrom' AND '$dateTo'
			GROUP BY book_item.product_id, book_item.product_name, image.image_path
			ORDER BY total_revenue DESC
			LIMIT 50",
			1
		) ?: [];

		$prevRevenue = $this->CommonModel->runQuery(
			"SELECT book_item.product_id, SUM(book_item.booking_price) AS prev_revenue
			FROM tbl_book_item AS book_item
			INNER JOIN tbl_book_product AS book_product ON book_product.product_book_id = book_item.product_book_id
			WHERE book_product.booking_status != '2'
				AND DATE(book_product.create_date) BETWEEN '$prevFrom' AND '$prevTo'
			GROUP BY book_item.product_id",
			1
		) ?: [];

		$prevByProduct = [];
		foreach ($prevRevenue as $row) {
			$prevByProduct[$row['product_id']] = (float) $row['prev_revenue'];
		}

		foreach ($products as &$product) {
			$prev = isset($prevByProduct[$product['product_id']]) ? $prevByProduct[$product['product_id']] : 0;
			$product['growth'] = $prev > 0 ? round((($product['total_revenue'] - $prev) / $prev) * 100, 1) : null;
		}

		return $products;
	}

	public function banner()
	{
		extract($this->input->get());
		$id = $this->input->get('bID');
		$BdID = $this->input->get('BdID');

		$sId = $id ? decryptId($id) : '';
		$get = $this->CommonModel->getSingleRowById('banner', ['banner_id' => $sId]);
		$data['image_path'] = set_value('image_path') == false ? @$get['image_path'] : set_value('image_path');
		$data['all_banner'] = $this->CommonModel->getAllRowsInOrder('banner', 'create_date', 'DESC');

		if (isset($BdID) != '') {
			$delete = $this->CommonModel->deleteRowById('banner', array('banner_id' => decryptId($BdID)));
			unlink('upload/banner/' . $img);
			redirect('banner');
			exit;
		}

		if (isset($id)) {
			$data['title'] = 'Banner Edit';
		} else {
			$data['title'] = 'Banner add';
		}
		if (count($_POST) > 0) {

			if (!empty($_FILES['image_path']['name'])) {
				$p = fullImage('image_path', BANNER_IMAGE, $data['image_path']);
				$post['image_path'] = $p;
			}

			if (isset($id)) {
				$this->CommonModel->updateRowById('banner', 'banner_id', $sId, $post);
				flashData('errors', 'Banner Update Successfully');
			} else {
				$this->CommonModel->insertRow('banner', $post);
				flashData('errors', 'Banner Add successfully.');
			}
			redirect('banner');
		}
		$this->load->view('admin/banner', $data);
	}

	public function promoCode()
	{
		$id = $this->input->get('promo');
		$dID = $this->input->get('dID');
		$sId = decryptId($id);

		if (isset($dID)) {
			$this->CommonModel->deleteRowById('promocode', array('promocode_id' => decryptId($dID)));
			flashData('errors', 'Promo code Delete Successfully');
			redirect('promoCode');
		}

		if (isset($id)) {
			$getPlans = $this->CommonModel->getSingleRowById('promocode', ['promocode_id' => $sId]);
			$data['title'] = 'Promo code Edit';
		} else {
			$data['title'] = 'Promo code add';
		}

		$data['promocode'] = set_value('promocode') == false ? @$getPlans['promocode'] : set_value('promocode');
		$data['expiry_date'] = set_value('expiry_date') == false ? @$getPlans['expiry_date'] : set_value('expiry_date');
		$data['minimum_order'] = set_value('minimum_order') == false ? @$getPlans['minimum_order'] : set_value('minimum_order');
		$data['amount'] = set_value('amount') == false ? @$getPlans['amount'] : set_value('amount');
		$data['type'] = set_value('type') == false ? @$getPlans['type'] : set_value('type');
		$data['discount_type'] = set_value('discount_type') == false ? (@$getPlans['discount_type'] ?: 'fixed') : set_value('discount_type');

		if (count($_POST) > 0) {
			extract($this->input->post());
			$post['promocode'] = strtoupper($promocode);
			$post['discount_type'] = ($discount_type === 'percentage') ? 'percentage' : 'fixed';

			// A percentage must be a sane 1-100 rate, not a raw amount - clamp
			// rather than trust the client, since this also feeds Razorpay
			// Magic Checkout's promotion value calculation.
			$post['amount'] = ($post['discount_type'] === 'percentage')
				? max(1, min(100, (float) $amount))
				: max(0, (float) $amount);

			$post['minimum_order'] = $minimum_order;
			$post['expiry_date'] = date('Y-m-d', strtotime($expiry_date));
			$post['type'] = $type;

			// A code that's still active (not yet expired) elsewhere must not
			// be reused - evaluateCoupon()/calculateCouponDiscount() look a
			// code up by its text alone with no ORDER BY, so two live rows
			// with the same code would make lookups pick an arbitrary one.
			// Excludes the row being edited so an unrelated field can still
			// be updated without tripping over its own code. A code is free
			// to reuse again once its earlier instance has expired.
			$duplicateWhere = "promocode = '" . $this->db->escape_str($post['promocode']) . "' AND expiry_date >= '" . date('Y-m-d') . "'";
			if (isset($id)) {
				$duplicateWhere .= ' AND promocode_id != ' . (int) $sId;
			}
			$duplicate = $this->CommonModel->getSingleRowById('promocode', $duplicateWhere);

			if ($duplicate) {
				flashData('errors', 'Promo code "' . $post['promocode'] . '" is already active until ' . date('d-M-Y', strtotime($duplicate['expiry_date'])) . '. Choose a different code, or wait until it expires before reusing it.');
				redirect(isset($id) ? 'promoCode?promo=' . $id : 'promoCode');
				return;
			}

			if (isset($id)) {
				$update = $this->CommonModel->updateRowById('promocode', 'promocode_id', $sId, $post);
				flashData('errors', 'Promo code Update Successfully');
			} else {
				$insert = $this->CommonModel->insertRow('promocode', $post);
				flashData('errors', 'Promo code Add Successfully');
			}
			redirect('promoCode');
		} else {
			$data['title'] = 'Promo Code';
			$this->load->view('admin/user_promo_code', $data);
		}
	}

	public function setDeliveryCharges()
	{
		extract($this->input->post());
		$get = $this->CommonModel->getSingleRowById('delivery_charge', "delivery_charge_id = '1'");
		$data['min_amount'] = set_value('min_amount') == false ? @$get['min_amount'] : set_value('min_amount');
		$data['amount'] = set_value('amount') == false ? @$get['amount'] : set_value('amount');
		$data['is_delivery_available'] = set_value('is_delivery_available') == false ? @$get['is_delivery_available'] : set_value('is_delivery_available');
		$data['packaging_charge'] = set_value('packaging_charge') == false ? @$get['packaging_charge'] : set_value('packaging_charge');
		$data['min_cod_available'] = set_value('min_cod_available') == false ? @$get['min_cod_available'] : set_value('min_cod_available');

		$data['title'] = 'Delivery Charge';
		if (count($_POST) > 0) {
			$this->form_validation->set_rules('min_amount', 'minimum amount', 'trim|required');
			$this->form_validation->set_rules('amount', 'amount', 'trim|required');
			if ($this->form_validation->run()) {

				$post['min_amount'] = $min_amount;
				$post['amount'] = $amount;
				$post['is_delivery_available'] = $is_delivery_available;
				$post['packaging_charge'] = $packaging_charge;
				$post['min_cod_available'] = $min_cod_available;

				$updateRow = $this->CommonModel->updateRowByMoreId('delivery_charge', ['delivery_charge_id' => '1'], $post);
				if ($updateRow) {
					flashData('errors', 'Delivery Charges Update Successfully');
				} else {
					flashData('errors', 'Delivery Charges Not Add.');
				}
				redirect('setDeliveryCharges');
			}
		}
		$this->load->view('admin/delivery_charges', $data);
	}

	public function activeUser()
	{
		$data['title'] = "All Active Users";
		$data['all_data'] = $this->CommonModel->getRowByIdInOrder('user_registration', "verify_status = '1' AND user_status = '1'", 'create_date', 'desc');
		$data['is_register'] = 0;
		$this->load->view('admin/users/user_all', $data);
	}

	public function inactiveUser()
	{
		$data['title'] = "All Inactive Users";
		$data['all_data'] = $this->CommonModel->getRowByIdInOrder('user_registration', "verify_status = '1' AND user_status = '0'", 'create_date', 'desc');
		$data['is_register'] = 0;
		$this->load->view('admin/users/user_all', $data);
	}

	public function userStatus($user_id, $status)
	{
		if ($status == 1) {
			$post = array('user_status' => '0');
			$msg = 'User inactive successfully';
		} else {
			$post = array('user_status' => '1');
			$msg = 'User active successfully';
		}
		$update = $this->CommonModel->updateRowById('user_registration', 'user_id', decryptId($user_id), $post);
		if ($update) {
			flashData('errors', $msg);
		} else {
			flashData('errors', 'Something went wrong. Please try again');
		}
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function userDetails($id)
	{
		$data['title'] = "User Details";
		$data['all_data'] = $this->CommonModel->getSingleRowById('user_registration', ['user_id' => decryptId($id)]);
		$this->load->view('admin/users/user_details', $data);
	}

	// Sub Admin

	public function subAdmin()
	{
		$data['title'] = "All Sub Admin";
		$data['all_data'] = $this->CommonModel->getRowByIdInOrder('admin_login', "user_type = '2'", 'name', 'ASC');
		$this->load->view('admin/sub_admin_all', $data);
	}

	public function addSubAdmin()
	{
		extract($this->input->post());
		$id = $this->input->get('id');
		$decrypt_id = decryptId($this->input->get('id'));
		if (isset($id)) {
			$get = $this->CommonModel->getSingleRowById('admin_login', ['admin_id' => $decrypt_id]);
		} else {
			$get = false;
		}
		$data['name'] = set_value('name') == false ? @$get['name'] : set_value('name');
		$data['email_id'] = set_value('email_id') == false ? @$get['email_id'] : set_value('email_id');
		$data['contact_no'] = set_value('contact_no') == false ? @$get['contact_no'] : set_value('contact_no');
		$data['privileges'] = set_value('privileges') == false ? @json_decode($get['privileges']) : set_value('privileges');
		if (isset($id)) {
			$data['title'] = 'Edit Sub Admin Profile';
		} else {
			$data['title'] = 'Add Sub Admin';
		}

		if (count($_POST) > 0) {
			if (isset($id)) {
				if ($get['email_id'] != $email_id) {
					$this->form_validation->set_rules('email_id', 'Email Id', 'trim|required|is_unique[admin_login.email_id]', ['is_unique' => 'Email Id already exist.']);
				}
				if ($get['contact_no'] != $contact_no) {
					$this->form_validation->set_rules('contact_no', 'Contact Number', 'trim|required|is_unique[admin_login.contact_no]', ['is_unique' => 'Contact Number already exist.']);
				}
			} else {
				$this->form_validation->set_rules('email_id', 'Email Id', 'trim|is_unique[admin_login.email_id]', ['is_unique' => 'Email Id already exist.']);
				$this->form_validation->set_rules('contact_no', 'Contact Number', 'trim|required|is_unique[admin_login.contact_no]', ['is_unique' => 'Contact Number already exist.']);
			}
			$this->form_validation->set_rules('name', 'name', 'required|trim');
			$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
			if ($this->form_validation->run()) {
				$post['name'] = $name;
				$post['contact_no'] = $contact_no;
				$post['email_id'] = $email_id;
				$post['user_type'] = 2;

				if ($password != "") {
					$post['password'] = encryptId($password);
				}

				$post2['banner_view'] = isset($banner_view) ? 1 : 0;
				$post2['banner_add'] = isset($banner_add) ? 1 : 0;
				$post2['banner_edit'] = isset($banner_edit) ? 1 : 0;
				$post2['banner_delete'] = isset($banner_delete) ? 1 : 0;

				$post2['promo_code_view'] = isset($promo_code_view) ? 1 : 0;
				$post2['promo_code_add'] = isset($promo_code_add) ? 1 : 0;
				$post2['promo_code_edit'] = isset($promo_code_edit) ? 1 : 0;
				$post2['promo_code_delete'] = isset($promo_code_delete) ? 1 : 0;

				$post2['sub_admin_view'] = isset($sub_admin_view) ? 1 : 0;
				$post2['sub_admin_add'] = isset($sub_admin_add) ? 1 : 0;
				$post2['sub_admin_edit'] = isset($sub_admin_edit) ? 1 : 0;
				$post2['sub_admin_enable'] = isset($sub_admin_enable) ? 1 : 0;

				$post2['product_sub_category_view'] = isset($product_sub_category_view) ? 1 : 0;
				$post2['product_sub_category_add'] = isset($product_sub_category_add) ? 1 : 0;
				$post2['product_sub_category_edit'] = isset($product_sub_category_edit) ? 1 : 0;
				$post2['product_sub_category_delete'] = isset($product_sub_category_delete) ? 1 : 0;

				$post2['product_view'] = isset($product_view) ? 1 : 0;
				$post2['product_add'] = isset($product_add) ? 1 : 0;
				$post2['product_edit'] = isset($product_edit) ? 1 : 0;
				$post2['product_delete'] = isset($product_delete) ? 1 : 0;

				$post2['users_view'] = isset($users_view) ? 1 : 0;
				$post2['users_enable'] = isset($users_enable) ? 1 : 0;

				$post2['orders_view'] = isset($orders_view) ? 1 : 0;
				$post2['orders_process'] = isset($orders_process) ? 1 : 0;

				$post2['return_view'] = isset($return_view) ? 1 : 0;
				$post2['return_process'] = isset($return_process) ? 1 : 0;
				$post2['return_refund'] = isset($return_refund) ? 1 : 0;

				$post2['payment_request_view'] = isset($payment_request_view) ? 1 : 0;
				$post2['payment_request_process'] = isset($payment_request_process) ? 1 : 0;

				$post['privileges'] = json_encode($post2);

				if (isset($id)) {
					$update = $this->CommonModel->updateRowById('admin_login', 'admin_id', $decrypt_id, $post);
					flashData('errors', 'Sub Admin Profile Update Successfully');
				} else {
					$insert = $this->CommonModel->insertRow('admin_login', $post);
					flashData('errors', 'Sub Admin Add Successfully');
				}
				redirect('subAdmin');
			}
		}
		$this->load->view('admin/sub_admin_add', $data);
	}

	public function subAdminStatus($user_id, $status)
	{
		if ($status == 1) {
			$post = array('status' => '0');
			$msg = 'Sub Admin inactive successfully';
		} else {
			$post = array('status' => '1');
			$msg = 'Sub Admin active successfully';
		}
		$update = $this->CommonModel->updateRowById('admin_login', 'admin_id', decryptId($user_id), $post);
		flashData('errors', $msg);
		redirect($_SERVER['HTTP_REFERER']);
	}

	// Order 

	// public function allOrders()
	// {
	// 	if (count($_POST) > 0) {
	// 		extract($this->input->post());
	// 		$postData = $this->input->post();
	// 		$searchValue = $postData['search']['value'];

	// 		$searchQuery = "";
	// 		if ($searchValue != '') {
	// 			$searchQuery .= " AND (tbl_checkouts.create_date = '" . dateConvertToDb($searchValue) . "' || tbl_user_registration.user_code LIKE '%" . $searchValue . "%' || tbl_checkouts.id LIKE '%" . $searchValue . "%')";
	// 		}

	// 		if ((isset($searchByDateFrom) && $searchByDateFrom != "") && (isset($searchByDateTo) && $searchByDateTo != "")) {
	// 			$searchQuery .= " AND (date(tbl_checkouts.create_date) >= '" . date('Y-m-d', strtotime($searchByDateFrom)) . "') AND (date(tbl_checkouts.create_date) <= '" . date('Y-m-d', strtotime($searchByDateTo)) . "')";
	// 		} else if ((isset($searchByDateFrom) && $searchByDateFrom != "")) {
	// 			$searchQuery .= (isset($searchByDateFrom) && $searchByDateFrom != "") ? " AND (date(tbl_checkouts.create_date) = '" . date('Y-m-d', strtotime($searchByDateFrom)) . "')" : "";
	// 		} else if ((isset($searchByDateTo) && $searchByDateTo != "")) {
	// 			$searchQuery .= (isset($searchByDateTo) && $searchByDateTo != "") ? " AND (date(tbl_checkouts.create_date) = '" . date('Y-m-d', strtotime($searchByDateTo)) . "')" : "";
	// 		}
	// 		$searchQuery .= (isset($searchByStatus) && $searchByStatus != "") ? " AND (tbl_checkouts.order_status = '$searchByStatus')" : "";

	// 		$join = [['tbl_user_registration', 'tbl_user_registration.user_id = tbl_checkouts.user_id', 'LEFT']];
	// 		$select = "tbl_checkouts.*, tbl_user_registration.user_code";
	// 		$allData = $this->CommonModel->getAjaxDataWithJoin($select, 'tbl_checkouts', $searchQuery, $postData, $join);

	// 		$draw = $postData['draw'];
	// 		$data = array();
	// 		$i = $postData['start'];
	// 		foreach ($allData['records'] as $record) {
	// 			++$i;

	// 			$action = "";
	// 			$id = encryptId($record['id']);

	// 			if ($record['order_status'] == '0') {
	// 				$status = statusView('warning', 'New Order');
	// 				$action .= '<button type="button" id="' . $id . '" datafld="' . $record['id'] . '" class="btn btn-primary btn-sm accept">Accept</button>';
	// 				$action .= '<button type="button" id="' . $id . '" datafld="' . $record['id'] . '" class="btn btn-danger btn-sm cancel ml-1">Cancel</button>';
	// 			} else if ($record['order_status'] == '3') {
	// 				$action .= '<button type="button" id="' . $id . '" datafld="' . $record['id'] . '" class="btn btn-primary btn-sm accept">Complete</button>';
	// 				$action .= '<button type="button" id="' . $id . '" datafld="' . $record['id'] . '" class="btn btn-danger btn-sm cancel ml-1">Cancel</button>';
	// 			} else if ($record['order_status'] == '1') {
	// 				$status = statusView('success', 'Order Delivered') . "<br>" . dateConvertToView($record['create_date'], 3);
	// 			} else if ($record['order_status'] == '2') {
	// 				$status = statusView('danger', 'Order Cancelled') . "<br>" . $record['cancel_reason'];
	// 			}
	// 			$order_details = '<button class="btn btn-success btn-sm addData " data-type="1" data-rid="' . $i . '" data-id="' . $id . '" data-header="Order Details - ' . $record['id'] . '"><i class="fa fa-eye loader1' . $i . '"></i> View</button>';
	// 			$item_details = '<button class="btn btn-success btn-sm addData" data-type="2" data-rid="' . $i . '" data-id="' . $id . '" data-header="Order Items Details - ' . $record['id'] . '"><i class="fa fa-eye loader2' . $i . '"></i> View</button>';

	// 			$data[] = array(
	// 				"sr_no" => $i,
	// 				"create_date" => dateConvertToView($record['create_date'], 3),
	// 				"order_id" => $record['id'],
	// 				"name" => $record['name'],
	// 				"contact_no" => $record['contact_no'],
	// 				"order_details" => $order_details,
	// 				"item_details" => $item_details,
	// 				"order_status" => $status,
	// 				"action" => @PREV['banner_add'] == 1 || USER_TYPE == '1' ? $action : '',
	// 			);
	// 		}

	// 		## Response
	// 		$response = array(
	// 			"draw" => intval($draw),
	// 			"iTotalRecords" => $allData['totalRecords'],
	// 			"iTotalDisplayRecords" => $allData['totalRecordwithFilter'],
	// 			"aaData" => $data
	// 		);

	// 		echo json_encode($response);
	// 	} else {
	// 		$data['title'] = 'All Orders';
	// 		$table_header = [
	// 			['data' => 'sr_no'],
	// 			['data' => 'create_date'],
	// 			['data' => 'order_id'],
	// 			['data' => 'name'],
	// 			['data' => 'contact_no'],
	// 			['data' => 'order_details'],
	// 			['data' => 'item_details'],
	// 			['data' => 'order_status'],
	// 			['data' => 'action'],
	// 		];

	// 		$data['ajax_table'] = "allOrders";
	// 		$data['table_column'] = json_encode($table_header);
	// 		$data['col_stop'] = "0,5,6,8";
	// 		$data['form_route'] = "getOrderDetails";
	// 		$this->load->view('admin/orders', $data);
	// 	}
	// }

	public function allOrders()
	{
		if (count($_POST) > 0) {
			extract($this->input->post());
			$postData = $this->input->post();
			$searchValue = $postData['search']['value'];

			$searchQuery = "";
			if ($searchValue != '') {
				// Must be OR, not || - CodeIgniter's raw where() parser only
				// splits/identifier-protects on the literal words AND/OR, so
				// || leaves everything after it unprotected against the
				// dbprefix'd table names, and the search value must be
				// escaped since it's concatenated directly into the query.
				$searchQuery .= " AND (book_product.create_date = '" . dateConvertToDb($searchValue) . "' OR book_product.order_id LIKE '%" . $this->db->escape_like_str($searchValue) . "%')";
			}

			// Deliberately NOT date(book_product.create_date) here - wrapping the
			// column in a function call defeats CodeIgniter's raw where() parser,
			// which bails out of adding the tbl_ prefix the moment it sees any
			// parentheses around an identifier (see protect_identifiers() in
			// DB_driver.php). That silently produced "Unknown column
			// book_product.create_date" for every request with a date filter set.
			// A plain >=/<= range against the datetime column sidesteps it while
			// still filtering whole calendar days inclusive of both ends.
			if ((isset($searchByDateFrom) && $searchByDateFrom != "") && (isset($searchByDateTo) && $searchByDateTo != "")) {
				$searchQuery .= " AND (book_product.create_date >= '" . date('Y-m-d', strtotime($searchByDateFrom)) . " 00:00:00') AND (book_product.create_date <= '" . date('Y-m-d', strtotime($searchByDateTo)) . " 23:59:59')";
			} else if ((isset($searchByDateFrom) && $searchByDateFrom != "")) {
				$searchQuery .= " AND (book_product.create_date >= '" . date('Y-m-d', strtotime($searchByDateFrom)) . " 00:00:00')";
			} else if ((isset($searchByDateTo) && $searchByDateTo != "")) {
				$searchQuery .= " AND (book_product.create_date <= '" . date('Y-m-d', strtotime($searchByDateTo)) . " 23:59:59')";
			}
			$searchQuery .= (isset($searchByStatus) && $searchByStatus != "") ? " AND (book_product.booking_status = '$searchByStatus')" : "";

			$join = [['user_registration', 'user_registration.user_id = book_product.user_id', 'LEFT']];
			$select = "book_product.*, user_registration.user_code";
			$allData = $this->CommonModel->getAjaxDataWithJoin($select, 'book_product', $searchQuery, $postData, $join);

			$draw = $postData['draw'];
			$data = array();
			$i = $postData['start'];
			foreach ($allData['records'] as $record) {
				++$i;

				$action = "";
				$id = encryptId($record['product_book_id']);

				// Booking progress drives status/actions regardless of payment
				// state now - Accept/Cancel must be available on every New order
				// (including online orders still shown Unpaid below) so staff can
				// action a stale/abandoned online order instead of it being stuck
				// with no available action if the payment never reconciles.
				if ($record['booking_status'] == '0') {
					$status = statusView('warning', 'New Order');
					$action .= '<button type="button" id="' . $id . '" datafld="' . $record['order_id'] . '" class="btn btn-primary btn-sm accept">Accept</button>';
					$action .= '<button type="button" id="' . $id . '" datafld="' . $record['order_id'] . '" class="btn btn-danger btn-sm cancel ml-1">Cancel</button>';
				} else if ($record['booking_status'] == '1') {
					$status = statusView('success', 'Order Accept') . "<br>" . dateConvertToView($record['estimated_time'], 3);
					if (!$record['shiprocket_order_id']) {
						$action .= '<button type="button" class="btn btn-info btn-sm shiprocket_sync ml-1" data-id="' . $id . '" data-order-id="' . $record['order_id'] . '">Ship with Shiprocket</button>';
					} else {
						$status .= "<br>" . statusView('primary', 'Shiprocket Synced');
						if (!empty($record['shiprocket_awb_code'])) {
							$action .= '<a href="https://shiprocket.co/tracking/' . $record['shiprocket_awb_code'] . '" target="_blank" class="btn btn-warning btn-sm ml-1">Track</a>';
						}
					}
					$action .= '<a class="btn btn-success btn-sm confirm_data ml-1" title="Order Dispatch" title-text="Are you sure ?" icon="warning" href="' . base_url("dispatchOrder/$id/3") . '">Dispatch</a>';
				} else if ($record['booking_status'] == '2') {
					$status = statusView('danger', 'Order Cancel') . "<br>" . $record['cancel_message'];
				} else if ($record['booking_status'] == '3') {
					$status = statusView('info', 'Order Dispatch');
					if (!empty($record['shiprocket_awb_code'])) {
						$action .= '<a href="https://shiprocket.co/tracking/' . $record['shiprocket_awb_code'] . '" target="_blank" class="btn btn-warning btn-sm ml-1">Track</a>';
					}
					$action .= '<a class="btn btn-success btn-sm confirm_data" href="' . base_url("dispatchOrder/$id/4") . '" title="Order Complete" title-text="Are you sure ?" icon="warning">Complete</a>';
				} else if ($record['booking_status'] == '4') {
					$status = statusView('success', 'Order Complete') . "<br>" . dateConvertToView($record['order_complete_date'], 3);
					if (!empty($record['shiprocket_awb_code'])) {
						$action .= '<a href="https://shiprocket.co/tracking/' . $record['shiprocket_awb_code'] . '" target="_blank" class="btn btn-warning btn-sm ml-1">Track</a>';
					}
				}

				// COD orders are marked paid the instant they're placed
				// (Web::place_order()), so this badge only ever applies to
				// online orders - either successfully captured (Paid), still
				// genuinely awaiting payment, or ones Razorpay actually
				// captured but whose confirmation never reached us (browser
				// tab closed before the callback fired, and the webhook was
				// not yet configured). "Sync Payment" lets staff pull the
				// real status from Razorpay one time instead of the order
				// being stuck Unpaid forever.
				if ($record['payment_mode'] != 'COD') {
					if ($record['transaction_status'] == '1') {
						$status .= '<br>' . statusView('success', 'Paid');
					} else {
						$status .= '<br>' . statusView('warning', 'Unpaid');
						if (!empty($record['razorpay_order_id'])) {
							$action .= '<button type="button" class="btn btn-outline-dark btn-sm sync_payment ml-1" data-id="' . $id . '" data-order-id="' . $record['order_id'] . '" title="Check Razorpay for this payment and mark paid if it was actually captured">Sync Payment</button>';
						}
					}
				}

				$order_details = '<button class="btn btn-success btn-sm addData " data-type="1" data-rid="' . $i . '" data-id="' . $id . '" data-header="Order Details - ' . $record['order_id'] . '"><i class="fa fa-eye loader1' . $i . '"></i> View</button>';
				$item_details = '<button class="btn btn-success btn-sm addData" data-type="2" data-rid="' . $i . '" data-id="' . $id . '" data-header="Order Items Details - ' . $record['order_id'] . '"><i class="fa fa-eye loader2' . $i . '"></i> View</button>';

				// Surfaced directly in the grid (amount + payment mode, contact)
				// so staff can triage an order without opening the details modal
				// for every single row.
				if ($record['payment_mode'] == 'COD') {
					$paymentBadge = statusView('warning', 'COD');
				} else if (!empty($record['payment_mode'])) {
					$paymentBadge = statusView('info', 'Online');
				} else {
					$paymentBadge = statusView('secondary', 'Unknown');
				}
				$amountCell = '₹' . number_format((float) $record['final_amount'], 2) . '<br>' . $paymentBadge;

				$data[] = array(
					"sr_no" => $i,
					"create_date" => dateConvertToView($record['create_date'], 3),
					"order_id" => $record['order_id'],
					"name" => $record['name'],
					"contact_no" => $record['contact_no'],
					"amount" => $amountCell,
					"user_code" => $record['user_code'],
					"order_details" => $order_details,
					"item_details" => $item_details,
					"booking_status" => $status,
					"action" => @PREV['banner_add'] == 1 || USER_TYPE == '1' ? $action : '',
				);
			}

			## Response
			$response = array(
				"draw" => intval($draw),
				"iTotalRecords" => $allData['totalRecords'],
				"iTotalDisplayRecords" => $allData['totalRecordwithFilter'],
				"aaData" => $data
			);

			echo json_encode($response);
		} else {
			$data['title'] = 'All New Orders';
			$table_header = [
				['data' => 'sr_no'],
				['data' => 'create_date'],
				['data' => 'order_id'],
				['data' => 'name'],
				['data' => 'contact_no'],
				['data' => 'amount'],
				['data' => 'user_code'],
				['data' => 'order_details'],
				['data' => 'item_details'],
				['data' => 'booking_status'],
				['data' => 'action'],
			];

			$data['ajax_table'] = "allOrders";
			$data['table_column'] = json_encode($table_header);
			$data['col_stop'] = "0,7,8,10";
			$data['form_route'] = "getOrderDetails";

			// Snapshot counters for the cards at the top of the page - lets
			// staff see the shape of the order queue without guessing from a
			// pre-filtered table.
			$data['snapshot'] = [
				'total' => $this->CommonModel->getNumRows('book_product', []),
				'new' => $this->CommonModel->getNumRows('book_product', "booking_status = '0'"),
				'accepted' => $this->CommonModel->getNumRows('book_product', "booking_status = '1'"),
				'dispatched' => $this->CommonModel->getNumRows('book_product', "booking_status = '3'"),
				'completed' => $this->CommonModel->getNumRows('book_product', "booking_status = '4'"),
				'cancelled' => $this->CommonModel->getNumRows('book_product', "booking_status = '2'"),
				'unpaid' => $this->CommonModel->getNumRows('book_product', "transaction_status = '0'"),
			];
			$todayRevenue = $this->CommonModel->runQuery(
				"SELECT COUNT(*) AS order_count, COALESCE(SUM(final_amount), 0) AS revenue FROM tbl_book_product WHERE transaction_status = '1' AND DATE(create_date) = CURDATE()",
				2
			);
			$data['snapshot']['today_orders'] = $todayRevenue ? (int) $todayRevenue['order_count'] : 0;
			$data['snapshot']['today_revenue'] = $todayRevenue ? (float) $todayRevenue['revenue'] : 0;

			$this->load->view('admin/orders', $data);
		}
	}

	// public function getOrderDetails()
	// {
	// 	$id = decryptId($this->input->get('id'));
	// 	$data_type = $this->input->get('data_type');
	// 	if ($data_type == 1) {
	// 		// Fetch order details from tbl_checkouts
	// 		$data['all_details'] = $this->CommonModel->getSingleRowById('tbl_checkouts', "id = '$id'");
	// 	} else {
	// 		// Fetch order items from checkout_items
	// 		$join = [['tbl_product', 'tbl_product.product_id = checkout_items.product_id', 'LEFT']];
	// 		$select = "checkout_items.*, tbl_product.product_name";
	// 		$data['all_items'] = $this->CommonModel->getRowWithMultiJoin($select, 'checkout_items', "checkout_id = '$id'", $join);
	// 	}

	// 	$data['type'] = $data_type;
	// 	$this->load->view('admin/order_details', $data);
	// }

	public function getOrderDetails()
	{
		$id = decryptId($this->input->get('id'));
		$data_type = $this->input->get('data_type');
		if ($data_type == 1) {
			$data['all_details'] = $this->CommonModel->getSingleRowById('book_product', ['product_book_id' => $id]);
		} else {
			// Product names are not unique in the catalog (multiple different
			// products can share the same name), so the modal also needs the
			// product_id and a thumbnail to tell them apart at a glance.
			$data['all_items'] = $this->CommonModel->runQuery(
				"SELECT
					book_item.*,
					product.product_name,
					image.image_path
				FROM tbl_book_item AS book_item
				LEFT JOIN tbl_product AS product ON product.product_id = book_item.product_id
				LEFT JOIN (SELECT product_id, MIN(product_image_id) AS img_id FROM tbl_product_image GROUP BY product_id) AS main_img
					ON main_img.product_id = book_item.product_id
				LEFT JOIN tbl_product_image AS image ON image.product_image_id = main_img.img_id
				WHERE book_item.product_book_id = $id",
				1
			);

			// Return/refund status per line item, so admins can see it without
			// leaving the order details modal.
			$data['returns_by_item'] = [];
			if ($data['all_items']) {
				foreach ($data['all_items'] as $item) {
					$returnRow = $this->CommonModel->getSingleRowById('return_request', ['book_item_id' => $item['book_item_id']]);
					if ($returnRow) {
						$data['returns_by_item'][$item['book_item_id']] = $returnRow;
					}
				}
			}
		}

		$data['type'] = $data_type;
		$this->load->view('admin/order_details', $data);
	}

	public function acceptOrder()
	{
		$estimated_time = $this->input->post('estimated_time');
		$estimated_date = $this->input->post('estimated_date');
		$id = $this->input->post('id');
		if ($estimated_time != '' and $id != '') {
			$update = $this->CommonModel->updateRowById('book_product', 'product_book_id', decryptId($id), array('booking_status' => '1', 'estimated_time' => $estimated_date . ' ' . date('h:i A', strtotime($estimated_time))));
			$this->notifyOrderStatus(decryptId($id), 'Accepted');
			flashData('errors', 'Order accept successfully');
		} else {
			flashData('errors', 'Something went wrong.');
		}
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function cancelOrder()
	{
		$cancel_msg = $this->input->post('cancel_msg');
		$id = $this->input->post('id');
		if ($cancel_msg != '' and $id != '') {
			$update = $this->CommonModel->updateRowById('book_product', 'product_book_id', decryptId($id), array('booking_status' => '2', 'cancel_message' => $cancel_msg, 'cancel_date' => date('d.m.Y')));
			$this->notifyOrderStatus(decryptId($id), 'Cancelled');
			flashData('errors', 'Order Cancel successfully');
		} else {
			flashData('errors', 'Something went wrong.');
		}
		redirect($_SERVER['HTTP_REFERER']);
	}

	// Manual reconciliation for online orders stuck "Unpaid" even though
	// Razorpay actually captured the payment - happens when the browser tab
	// closed/lost network before Web::handle_payment_response() fired and the
	// server-to-server webhook (Web::razorpayWebhook()) wasn't yet able to
	// catch it either (e.g. RAZORPAY_WEBHOOK_SECRET not configured). Pulls
	// the real status directly from Razorpay and, if captured, funnels
	// through the same CommonModel::markOrderPaid() every other confirmation
	// path uses. That method's own "transaction_status != '1'" guard is what
	// makes this naturally a one-time action - once applied, the order is no
	// longer Unpaid and this button stops being shown for it.
	public function syncPaymentStatus()
	{
		$id = $this->input->post('id');
		if (!$id) {
			echo json_encode(['success' => false, 'message' => 'Missing order id.']);
			return;
		}

		$order = $this->CommonModel->getSingleRowById('book_product', ['product_book_id' => decryptId($id)]);
		if (!$order) {
			echo json_encode(['success' => false, 'message' => 'Order not found.']);
			return;
		}

		if ($order['transaction_status'] == '1') {
			echo json_encode(['success' => false, 'message' => 'This order is already marked as paid.']);
			return;
		}

		if ($order['payment_mode'] == 'COD' || empty($order['razorpay_order_id'])) {
			echo json_encode(['success' => false, 'message' => 'This order has no online payment to sync.']);
			return;
		}

		$api = new Api(RAZOR_PYA_KEY, RAZOR_PYA_SECRET);

		try {
			$razorpayOrderEntity = $api->order->fetch($order['razorpay_order_id']);
			$payments = $razorpayOrderEntity->payments()->items;
		} catch (Exception $e) {
			log_message('error', 'syncPaymentStatus: could not fetch Razorpay order ' . $order['razorpay_order_id'] . ': ' . $e->getMessage());
			echo json_encode(['success' => false, 'message' => 'Could not reach Razorpay: ' . $e->getMessage()]);
			return;
		}

		$captured = null;
		foreach ($payments as $payment) {
			if ($payment['status'] === 'captured') {
				$captured = $payment;
				break;
			}
		}

		if (!$captured) {
			echo json_encode(['success' => false, 'message' => 'Razorpay does not show a captured payment for this order yet.']);
			return;
		}

		$applied = $this->CommonModel->markOrderPaid($order, $captured->toArray(), $razorpayOrderEntity);

		echo json_encode([
			'success' => true,
			'message' => $applied ? 'Payment confirmed with Razorpay - order marked as paid.' : 'Order was already marked as paid.',
		]);
	}

	public function dispatchOrder($id, $type)
	{
		$decrypt_id = decryptId($id);
		if ($type == '3') {
			$post['booking_status'] = '3';
			$message = "Order Dispatch successfully";

			// Shiprocket Integration: Generate AWB and Request Pickup
			$order = $this->CommonModel->getSingleRowById('book_product', ['product_book_id' => $decrypt_id]);
			if ($order && $order['shiprocket_shipment_id']) {
				$this->load->library('shiprocket');

				// 1. Generate AWB
				$awb_response = $this->shiprocket->generate_awb($order['shiprocket_shipment_id']);
				if (isset($awb_response['awb_code'])) {
					$update_data = [
						'shiprocket_awb_code' => $awb_response['awb_code'],
						'shiprocket_status' => 'AWB_GENERATED'
					];

					// 2. Request Pickup
					$pickup_response = $this->shiprocket->request_pickup($order['shiprocket_shipment_id']);
					if (isset($pickup_response['pickup_scheduled_date'])) {
						$update_data['shiprocket_status'] = 'PICKUP_SCHEDULED';
					}

					$this->CommonModel->updateRowById('book_product', 'product_book_id', $decrypt_id, $update_data);
				}
			}
		} else {
			$post['booking_status'] = '4';
			$post['order_complete_date'] = setDateTime();
			// Delivery date drives the product-return eligibility window, so it
			// has to be stamped at the exact moment an order is marked Completed.
			$post['delivery_date'] = date('Y-m-d');
			$message = "Order Complete successfully";
		}
		$update = $this->CommonModel->updateRowById('book_product', 'product_book_id', $decrypt_id, $post);
		$this->notifyOrderStatus($decrypt_id, $type == '3' ? 'Dispatched' : 'Completed');
		flashData('errors', $message);
		redirect($_SERVER['HTTP_REFERER']);
	}

	private function notifyOrderStatus($product_book_id, $status)
	{
		$order = $this->CommonModel->getSingleRowById('book_product', ['product_book_id' => $product_book_id]);
		if ($order) {
			sendTemplatedMail('order_status_updated_user', @$order['email'], ['name' => $order['name'], 'order_id' => $order['order_id'], 'status' => $status, 'app_name' => APP_NAME]);
		}
	}

	// New Order (manual admin order creation)

	public function addOrder()
	{
		$data['title'] = 'New Order';
		$data['all_customers'] = $this->CommonModel->getRowByIdInOrder('user_registration', "user_status = '1'", 'name', 'ASC');
		$data['all_products'] = $this->CommonModel->getRowByIdInOrder('product', "is_delete = '1'", 'product_name', 'ASC');
		$data['all_states'] = $this->CommonModel->getAllRowsInOrder('state', 'state_name', 'ASC');
		$data['delivery_charge'] = $this->CommonModel->getSingleRowById('delivery_charge', "delivery_charge_id = '1'");
		$this->load->view('admin/order_add', $data);
	}

	public function addOrderSave()
	{
		extract($this->input->post());

		$this->form_validation->set_rules('address', 'Address', 'trim|required');
		$this->form_validation->set_rules('postal_code', 'Postal Code', 'trim|required');
		$this->form_validation->set_rules('state', 'State', 'trim|required');
		$this->form_validation->set_rules('city', 'City', 'trim|required');
		$this->form_validation->set_rules('payment_mode', 'Payment Mode', 'trim|required');
		$this->form_validation->set_rules('product_id[]', 'Product', 'required');

		if (@$customer_id == 'new') {
			$this->form_validation->set_rules('new_customer_name', 'Customer Name', 'trim|required');
			$this->form_validation->set_rules('new_customer_contact', 'Contact Number', 'trim|required|numeric');
		} else {
			$this->form_validation->set_rules('customer_id', 'Customer', 'trim|required');
		}

		if (!$this->form_validation->run()) {
			flashData('errors', strip_tags(validation_errors()));
			redirect('addOrder');
			exit;
		}

		if ($customer_id == 'new') {
			$existingUser = $this->CommonModel->getSingleRowById('user_registration', ['contact_no' => $new_customer_contact]);
			if ($existingUser) {
				$userId = $existingUser['user_id'];
			} else {
				$userId = $this->CommonModel->insertRowReturnId('user_registration', [
					'name' => $new_customer_name,
					'contact_no' => $new_customer_contact,
					'email_id' => !empty($new_customer_email) ? $new_customer_email : null,
					'password' => password_hash(uniqid(), PASSWORD_DEFAULT),
					'first_order' => 1,
				]);
			}
			$custName = $new_customer_name;
			$custContact = $new_customer_contact;
			$custEmail = !empty($new_customer_email) ? $new_customer_email : '';
		} else {
			$user = $this->CommonModel->getSingleRowById('user_registration', ['user_id' => $customer_id]);
			if (!$user) {
				flashData('errors', 'Invalid customer selected.');
				redirect('addOrder');
				exit;
			}
			$userId = $user['user_id'];
			$custName = $user['name'];
			$custContact = $user['contact_no'];
			$custEmail = @$user['email_id'] ?: '';
		}

		$productIds = (array) @$product_id;
		$quantities = (array) @$quantity;
		$prices = (array) @$price;

		$totalItemAmount = 0;
		$totalItems = 0;
		$items = [];
		foreach ($productIds as $i => $pid) {
			if (empty($pid)) {
				continue;
			}
			$product = $this->CommonModel->getSingleRowById('product', ['product_id' => $pid]);
			if (!$product) {
				continue;
			}
			$qty = !empty($quantities[$i]) ? floatval($quantities[$i]) : 1;
			$unitPrice = !empty($prices[$i]) ? floatval($prices[$i]) : $product['sale_price'];
			$lineTotal = $unitPrice * $qty;
			$totalItemAmount += $lineTotal;
			$totalItems += $qty;
			$items[] = [
				'product_id' => $pid,
				'product_name' => $product['product_name'],
				'base_price' => $product['sale_price'],
				'user_price' => $unitPrice,
				'no_of_items' => $qty,
				'booking_price' => $lineTotal,
			];
		}

		if (empty($items)) {
			flashData('errors', 'Please add at least one valid product.');
			redirect('addOrder');
			exit;
		}

		$deliveryCharges = !empty($delivery_charges) ? floatval($delivery_charges) : 0;
		$packagingCharge = !empty($packaging_charge) ? floatval($packaging_charge) : 0;
		$finalAmount = $totalItemAmount + $deliveryCharges + $packagingCharge;

		$orderId = orderIdGenerateUser('book_product', 'order_id');
		$orderData = [
			'order_id' => $orderId,
			'user_id' => $userId,
			'name' => $custName,
			'email' => $custEmail,
			'contact_no' => $custContact,
			'address' => $address,
			'area' => @$area,
			'postal_code' => $postal_code,
			'state' => $state,
			'city' => $city,
			'latitude' => '0',
			'longitude' => '0',
			'total_item_amount' => $totalItemAmount,
			'total_items' => $totalItems,
			'delivery_charges' => $deliveryCharges,
			'packaging_charge' => $packagingCharge,
			'promocode_status' => 0,
			'wallet_amount' => 0,
			'final_amount' => $finalAmount,
			'payment_mode' => $payment_mode,
			'transaction_status' => '1',
			'booking_status' => '0',
			'booking_date' => date('Y-m-d H:i:s'),
			'delivery_possible' => '1',
		];

		$productBookId = $this->CommonModel->insertRowReturnId('book_product', $orderData);
		if ($productBookId) {
			foreach ($items as $item) {
				$item['product_book_id'] = $productBookId;
				$this->CommonModel->insertRow('book_item', $item);
			}

			$smtp = $this->CommonModel->getSingleRowById('mail_smtp_setting', ['id' => 1]);
			sendTemplatedMail('order_placed_user', $custEmail, ['name' => $custName, 'order_id' => $orderId, 'final_amount' => $finalAmount, 'app_name' => APP_NAME]);
			sendTemplatedMail('order_placed_admin', @$smtp['notify_email'], ['name' => $custName, 'order_id' => $orderId, 'final_amount' => $finalAmount, 'app_name' => APP_NAME]);

			flashData('errors', 'Order created successfully: ' . $orderId);
			redirect('allOrders');
		} else {
			flashData('errors', 'Something went wrong. Order not created.');
			redirect('addOrder');
		}
	}

	// Addon

	public function addOnData()
	{
		$get['title'] = 'View Data';
		$id = $this->input->get('id');
		if (isset($id)) {
			$get['data'] = $this->CommonModel->getSingleRowById('add_on_data', ['id' => decryptId($id)]);
			$this->load->view('admin/add_on_data_view', $get);
		} else {
			$get['all_data'] = $this->CommonModel->getAllRows('add_on_data');
			$this->load->view('admin/add_on_data', $get);
		}
	}

	public function addOnDataAdd()
	{
		extract($this->input->post());
		$id = $this->input->get('id');
		$decrypt_id = decryptId($this->input->get('id'));
		$get = $this->CommonModel->getSingleRowById('add_on_data', ['id' => $decrypt_id]);
		$data['title_data'] = set_value('title') == false ? @$get['title'] : set_value('title');
		$data['description'] = set_value('description') == false ? @$get['description'] : set_value('description');
		if (isset($id)) {
			$data['title'] = 'Edit ' . $get['title'];
		} else {
			$data['title'] = 'Add Category';
		}

		if (count($_POST) > 0) {
			$post['title'] = trim($title);
			$post['description'] = trim($description);
			if (isset($id)) {
				$post['update_date'] = setDateTime();
				$update = $this->CommonModel->updateRowByIdWithOutXss('add_on_data', "id = '$decrypt_id'", $post);
				if ($update) {
					flashData('errors', 'Data Update Successfully');
				} else {
					flashData('errors', 'Data Not Add');
				}
			}
			redirect('addOnData');
		}
		$this->load->view('admin/add_on_data_add', $data);
	}

	// Meta Data (SEO)

	private $staticMetaPages = [
		'home' => 'Home',
		'about' => 'About Us',
		'contact' => 'Contact Us',
		'faqs' => 'FAQs',
		'terms-and-condition' => 'Terms & Conditions',
		'privacy' => 'Privacy Policy',
		'return-and-refund' => 'Return & Refund Policy',
		'shipping-policy' => 'Shipping Policy',
		'products' => 'Products',
		'wishlist' => 'Wishlist',
		'cart' => 'Cart',
		'login' => 'Login',
		'register' => 'Register',
		'profile' => 'User Profile',
		'checkout' => 'Checkout',
	];

	public function metaData()
	{
		$data['title'] = 'Meta Data';
		// List every known static page, not just the ones that already have a
		// tbl_meta_data row - otherwise a page added to $staticMetaPages never
		// shows up here until someone happens to save it once via the edit form.
		$existingRows = $this->CommonModel->getAllRows('meta_data') ?: [];
		$existingByKey = [];
		foreach ($existingRows as $row) {
			$existingByKey[$row['page_key']] = $row;
		}
		$allData = [];
		foreach ($this->staticMetaPages as $key => $label) {
			$allData[] = $existingByKey[$key] ?? ['page_key' => $key, 'meta_title' => ''];
		}
		$data['all_data'] = $allData;
		$data['page_labels'] = $this->staticMetaPages;
		$this->load->view('admin/meta_data', $data);
	}

	public function metaDataEdit()
	{
		extract($this->input->post());
		$key = $this->input->get('key');
		if (!isset($this->staticMetaPages[$key])) {
			flashData('errors', 'Invalid page.');
			redirect('metaData');
			exit;
		}
		$get = $this->CommonModel->getSingleRowById('meta_data', ['page_key' => $key]);

		$data['title'] = 'Edit Meta Data - ' . (@$this->staticMetaPages[$key] ?: $key);
		$data['page_key'] = $key;
		$data['meta_title'] = set_value('meta_title') == false ? @$get['meta_title'] : set_value('meta_title');
		$data['meta_description'] = set_value('meta_description') == false ? @$get['meta_description'] : set_value('meta_description');
		$data['meta_keywords'] = set_value('meta_keywords') == false ? @$get['meta_keywords'] : set_value('meta_keywords');

		if (count($_POST) > 0) {
			$post['meta_title'] = trim($meta_title);
			$post['meta_description'] = trim($meta_description);
			$post['meta_keywords'] = trim($meta_keywords);
			if ($get) {
				$update = $this->CommonModel->updateRowByMoreId('meta_data', ['page_key' => $key], $post);
			} else {
				$post['page_key'] = $key;
				$update = $this->CommonModel->insertRow('meta_data', $post);
			}
			if ($update) {
				flashData('errors', 'Meta Data Updated Successfully');
			} else {
				flashData('errors', 'Nothing was updated.');
			}
			redirect('metaDataEdit?key=' . $key);
		}
		$this->load->view('admin/meta_data_edit', $data);
	}

	// Site-wide social links, shown in the storefront header/footer.
	public function siteSettings()
	{
		$get = $this->CommonModel->getSingleRowById('setting', ['id' => 1]);
		$data['title'] = 'Social Links';
		$data['facebook_url'] = set_value('facebook_url') == false ? @$get['facebook_url'] : set_value('facebook_url');
		$data['instagram_url'] = set_value('instagram_url') == false ? @$get['instagram_url'] : set_value('instagram_url');

		if (count($_POST) > 0) {
			$this->form_validation->set_rules('facebook_url', 'Facebook URL', 'valid_url');
			$this->form_validation->set_rules('instagram_url', 'Instagram URL', 'valid_url');
			if ($this->form_validation->run()) {
				$post['facebook_url'] = $this->input->post('facebook_url');
				$post['instagram_url'] = $this->input->post('instagram_url');
				$this->CommonModel->updateRowByIdWithOutXss('setting', "id = '1'", $post);
				flashData('errors', 'Social Links Updated Successfully');
				redirect('siteSettings');
			}
		}
		$this->load->view('admin/site_settings', $data);
	}

	// Mail: SMTP Settings & Templates

	private $mailEventPlaceholders = [
		'user_registered_admin' => ['name', 'email', 'app_name'],
		'user_registered_user' => ['name', 'app_name'],
		'order_placed_admin' => ['order_id', 'name', 'final_amount', 'app_name'],
		'order_placed_user' => ['name', 'order_id', 'final_amount', 'app_name'],
		'order_status_updated_user' => ['name', 'order_id', 'status', 'app_name'],
		'forgot_password_otp' => ['name', 'otp', 'app_name'],
		'test_mail' => ['name', 'app_name'],
		'return_requested_user' => ['name', 'order_id', 'return_code', 'product_name', 'app_name'],
		'return_requested_admin' => ['name', 'order_id', 'return_code', 'product_name', 'app_name'],
		'return_approved_user' => ['name', 'order_id', 'return_code', 'app_name'],
		'return_rejected_user' => ['name', 'order_id', 'return_code', 'reject_reason', 'app_name'],
		'return_refund_completed_user' => ['name', 'order_id', 'return_code', 'refund_amount', 'refund_method', 'app_name'],
	];

	public function mailSmtpSetting()
	{
		extract($this->input->post());
		$get = $this->CommonModel->getSingleRowById('mail_smtp_setting', ['id' => 1]);
		$data['title'] = 'SMTP Settings';
		$data['protocol'] = set_value('protocol') == false ? @$get['protocol'] : set_value('protocol');
		$data['smtp_host'] = set_value('smtp_host') == false ? @$get['smtp_host'] : set_value('smtp_host');
		$data['smtp_port'] = set_value('smtp_port') == false ? @$get['smtp_port'] : set_value('smtp_port');
		$data['smtp_user'] = set_value('smtp_user') == false ? @$get['smtp_user'] : set_value('smtp_user');
		$data['smtp_pass'] = set_value('smtp_pass') == false ? @$get['smtp_pass'] : set_value('smtp_pass');
		$data['smtp_crypto'] = set_value('smtp_crypto') == false ? @$get['smtp_crypto'] : set_value('smtp_crypto');
		$data['from_email'] = set_value('from_email') == false ? @$get['from_email'] : set_value('from_email');
		$data['from_name'] = set_value('from_name') == false ? @$get['from_name'] : set_value('from_name');
		$data['notify_email'] = set_value('notify_email') == false ? @$get['notify_email'] : set_value('notify_email');

		if (count($_POST) > 0) {
			$this->form_validation->set_rules('smtp_host', 'SMTP Host', 'required');
			$this->form_validation->set_rules('smtp_port', 'SMTP Port', 'required|numeric');
			$this->form_validation->set_rules('smtp_user', 'SMTP Username', 'required');
			$this->form_validation->set_rules('from_email', 'From Email', 'required|valid_email');
			$this->form_validation->set_rules('notify_email', 'Admin Notification Email', 'required|valid_email');
			if ($this->form_validation->run()) {
				$post['protocol'] = $protocol ?: 'smtp';
				$post['smtp_host'] = $smtp_host;
				$post['smtp_port'] = $smtp_port;
				$post['smtp_user'] = $smtp_user;
				if (!empty($smtp_pass)) {
					$post['smtp_pass'] = $smtp_pass;
				}
				$post['smtp_crypto'] = $smtp_crypto;
				$post['from_email'] = $from_email;
				$post['from_name'] = $from_name;
				$post['notify_email'] = $notify_email;
				$update = $this->CommonModel->updateRowByMoreId('mail_smtp_setting', ['id' => '1'], $post);
				if ($update) {
					flashData('errors', 'SMTP Settings Updated Successfully');
				} else {
					flashData('errors', 'Nothing was updated.');
				}
				redirect('mailSmtpSetting');
			}
		}
		$this->load->view('admin/mail_smtp_setting', $data);
	}

	public function mailTemplateAll()
	{
		$data['title'] = 'Mail Templates';
		$data['all_data'] = $this->CommonModel->getAllRows('mail_template');
		$this->load->view('admin/mail_template_all', $data);
	}

	public function mailTemplateEdit()
	{
		extract($this->input->post());
		$key = $this->input->get('key');
		$get = $this->CommonModel->getSingleRowById('mail_template', ['event_key' => $key]);
		if (!$get) {
			flashData('errors', 'Invalid mail template.');
			redirect('mailTemplateAll');
			exit;
		}

		$data['title'] = 'Edit Mail Template';
		$data['event_key'] = $key;
		$data['placeholders'] = @$this->mailEventPlaceholders[$key] ?: [];
		$data['subject'] = set_value('subject') == false ? @$get['subject'] : set_value('subject');
		$data['body'] = set_value('body') == false ? @$get['body'] : set_value('body');
		$data['is_enabled'] = @$get['is_enabled'];

		if (count($_POST) > 0) {
			$this->form_validation->set_rules('subject', 'Subject', 'required');
			$this->form_validation->set_rules('body', 'Body', 'required');
			if ($this->form_validation->run()) {
				$post['subject'] = trim($subject);
				$post['body'] = $body;
				$post['is_enabled'] = isset($is_enabled) ? 1 : 0;
				$update = $this->CommonModel->updateRowByMoreId('mail_template', ['event_key' => $key], $post);
				if ($update) {
					flashData('errors', 'Mail Template Updated Successfully');
				} else {
					flashData('errors', 'Nothing was updated.');
				}
				redirect('mailTemplateEdit?key=' . $key);
			}
		}
		$this->load->view('admin/mail_template_edit', $data);
	}

	public function testMail()
	{
		$toEmail = $this->input->post('to_email');
		if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
			echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
			exit;
		}
		$result = sendTemplatedMail('test_mail', $toEmail, ['name' => 'Admin', 'app_name' => APP_NAME]);
		echo json_encode([
			'success' => $result,
			'message' => $result ? 'Test email sent successfully.' : 'Failed to send test email. Check SMTP settings and logs.',
		]);
		exit;
	}

	// Delivery Locations

	public function deliveryLocation()
	{
		$data['title'] = 'Delivery Location';
		$data['form_route'] = "deliveryLocationAdd";
		$data['all_data'] = $this->CommonModel->getAllRows('delivery_locations');
		$this->load->view('admin/delivery_locations', $data);
	}

	public function deliveryLocationAdd()
	{
		extract($this->input->post());
		$dID = $this->input->get('dID');
		if (count($_GET) > 0) {
			$id = $this->input->get('id');
			$decrypt_id = decryptId($this->input->get('id'));
		} else {
			$decrypt_id = decryptId($id);
		}

		if (isset($dID)) {
			$this->CommonModel->deleteRowById('delivery_locations', ['id' => decryptId($dID)]);
			setAlert('Delivery Location Delete', 'success', 'Delivery Location Delete Successfully');
			redirect('deliveryLocation');
			exit;
		}

		if (isset($id)) {
			$get = $this->CommonModel->getSingleRowById('delivery_locations', ['id' => $decrypt_id]);
		} else {
			$get = false;
		}

		$data['location_name'] = set_value('location_name') == false ? @$get['location_name'] : set_value('location_name');
		$data['latitude'] = set_value('latitude') == false ? @$get['latitude'] : set_value('latitude');
		$data['longitude'] = set_value('longitude') == false ? @$get['longitude'] : set_value('longitude');
		$data['status'] = set_value('status') == false ? @$get['status'] : set_value('status');

		if (count($_POST) > 0) {
			$this->form_validation->set_rules('location_name', 'location_name', 'required');
			if ($this->form_validation->run()) {
				$post['location_name'] = $location_name;
				$post['latitude'] = $latitude;
				$post['longitude'] = $longitude;
				$post['status'] = $status;


				if ($id != '0') {
					$update = $this->CommonModel->updateRowById('delivery_locations', 'id', $decrypt_id, $post);
					setAlert('Delivery Location Update', 'success', 'Delivery Location Update Successfully');
				} else {
					$insert = $this->CommonModel->insertRow('delivery_locations', $post);
					setAlert('Delivery Location Add', 'success', 'Delivery Location Create Successfully');
					$post['id']  = $insert;
				}
				redirect('deliveryLocation');
			}
		}
		$this->load->view('admin/delivery_location_add', $data);
	}

	// FAQs

	public function faqAll()
	{
		$data['title'] = 'FAQs';
		$data['form_route'] = "faqAdd";
		$data['all_data'] = $this->CommonModel->getAllRowsInOrder('faqs', 'sort_order', 'ASC');
		$this->load->view('admin/faqs', $data);
	}

	public function faqAdd()
	{
		extract($this->input->post());
		$dID = $this->input->get('dID');
		if (count($_GET) > 0) {
			$id = $this->input->get('id');
			$decrypt_id = decryptId($this->input->get('id'));
		} else {
			$decrypt_id = decryptId($id);
		}

		if (isset($dID)) {
			$this->CommonModel->deleteRowById('faqs', ['id' => decryptId($dID)]);
			setAlert('FAQ Delete', 'success', 'FAQ Delete Successfully');
			redirect('faqAll');
			exit;
		}

		if (isset($id) && $id != '0') {
			$get = $this->CommonModel->getSingleRowById('faqs', ['id' => $decrypt_id]);
		} else {
			$get = false;
		}

		$data['question'] = set_value('question') == false ? @$get['question'] : set_value('question');
		$data['answer'] = set_value('answer') == false ? @$get['answer'] : set_value('answer');
		$data['sort_order'] = set_value('sort_order') == false ? (@$get['sort_order'] ?? 0) : set_value('sort_order');
		$data['status'] = set_value('status') == false ? (@$get['status'] ?? '1') : set_value('status');

		if (count($_POST) > 0) {
			$this->form_validation->set_rules('question', 'Question', 'required');
			$this->form_validation->set_rules('answer', 'Answer', 'required');
			if ($this->form_validation->run()) {
				$post['question'] = trim($question);
				$post['answer'] = trim($answer);
				$post['sort_order'] = (int) $sort_order;
				$post['status'] = $status;

				if ($id != '0') {
					$post['update_date'] = setDateTime();
					$this->CommonModel->updateRowByIdWithOutXss('faqs', "id = '$decrypt_id'", $post);
					setAlert('FAQ Update', 'success', 'FAQ Update Successfully');
				} else {
					$this->CommonModel->insertRowWithXSS('faqs', $post);
					setAlert('FAQ Add', 'success', 'FAQ Create Successfully');
				}
				redirect('faqAll');
			}
		}
		$this->load->view('admin/faq_add', $data);
	}

	public function contact_query()
	{
		$data['contact'] = $this->CommonModel->getRowByIdInOrder('contact_query', [], 'cid', 'DESC');
		// $data['setting'] = $this->setting;
		$data['title'] = 'Contact ';
		if (isset($_GET['BdID'])) {
			$BdID = $this->input->get('BdID');
			if (decryptId($BdID) != '') {
				$delete = $this->CommonModel->deleteRowById('contact_query', array('cid' => decryptId($BdID)));

				redirect('contact_query');
				exit;
			}
		}
		$this->load->view('admin/contact', $data);
	}

	public function adminNotificationCheck()
	{
		$getNotification = $this->CommonModel->getNumRows('book_product', "is_seen = '0'");
		if ($getNotification > 0) {
			$this->CommonModel->updateRowByMoreId('book_product', "is_seen = '0'", ['is_seen' => 1]);
		}
	}
	public function shiprocketOrderDetails()
	{
		$id = decryptId($this->input->get('id'));
		$order = $this->CommonModel->getSingleRowById('book_product', ['product_book_id' => $id]);
		if ($order) {
			$address = $order['address'];
			$address_main = $address;

			// The structured columns are the source of truth. Only fall back to
			// splitting the freeform address string when a column is empty, and
			// even then only trust the split if a 6-digit pincode is actually
			// found where the state is expected - addresses don't reliably follow
			// a fixed "Address, City, State Pincode, Country" shape, and guessing
			// blindly shifts fields (a street name lands in "city", the real city
			// lands in "state"), which is what was producing Shiprocket's
			// "Invalid Data" sync failures.
			$city = trim($order['city'] ?? '');
			$state = trim($order['state'] ?? '');
			$pincode = trim($order['postal_code'] ?? '');

			if (!$city || !$state || !$pincode) {
				$parts = array_map('trim', explode(',', $address));
				$count = count($parts);
				$state_pin = $parts[$count - 2] ?? '';

				if (preg_match('/(\d{6})/', $state_pin, $matches)) {
					$parsed_pincode = $matches[1];
					$parsed_state = trim(str_replace($parsed_pincode, '', $state_pin));
					$parsed_city = $parts[$count - 3] ?? '';

					$pincode = $pincode ?: $parsed_pincode;
					$state = $state ?: $parsed_state;
					$city = $city ?: $parsed_city;

					if ($count > 3) {
						$address_main = implode(', ', array_slice($parts, 0, $count - 3));
					}
				}
			}

			$order['parsed_address'] = [
				'address' => $address_main,
				'city' => $city ?: 'Bhopal',
				'state' => $state ?: 'Madhya Pradesh',
				'pincode' => $pincode
			];

			echo json_encode(['success' => true, 'data' => $order]);
		} else {
			echo json_encode(['success' => false, 'message' => 'Order not found']);
		}
	}

	public function shipWithShiprocket()
	{
		$id = decryptId($this->input->post('id'));
		$this->load->library('shiprocket');

		$order = $this->CommonModel->getSingleRowById('book_product', ['product_book_id' => $id]);
		if (!$order) {
			echo json_encode(['success' => false, 'message' => 'Order not found']);
			return;
		}

		$items = $this->CommonModel->getRowById('book_item', 'product_book_id', $id);
		$order_items = [];
		foreach ($items as $item) {
			$order_items[] = [
				'name' => $item['product_name'],
				'sku' => $item['product_id'] . ($item['variant_id'] ? '-' . $item['variant_id'] : ''),
				'units' => (int)$item['no_of_items'],
				'selling_price' => (float)$item['user_price'],
				'discount' => 0,
				'tax' => 0,
				'hsn' => 0
			];
		}

		// Handle billing name from POST or Order
		$full_name = trim($this->input->post('name') ?: $order['name']);
		// Remove special characters from name as Shiprocket is picky
		$full_name = preg_replace('/[^a-zA-Z0-9\s]/', '', $full_name);

		$name_parts = explode(' ', $full_name, 2);
		$first_name = $name_parts[0];
		$last_name = (isset($name_parts[1]) && !empty($name_parts[1])) ? $name_parts[1] : 'Customer';

		$billing_email = $this->input->post('email') ?: ($order['email'] ?: 'customer@example.com');
		$billing_phone = $this->input->post('phone') ?: $order['contact_no'];
		$billing_address = trim($this->input->post('address') ?: $order['address']);
		$billing_address = $billing_address ?: 'Address not provided, India';

		$billing_city = trim($this->input->post('city') ?: $order['city']);
		$billing_state = trim($this->input->post('state') ?: $order['state']);

		$billing_phone = preg_replace('/\D/', '', $this->input->post('phone') ?: $order['contact_no']);

		if (strlen($billing_address) < 10) {
			$billing_address .= ', Near Market';
		}

		$billing_pincode = trim($this->input->post('pincode'));


		$shiprocket_data = [
			'order_id' => $order['order_id'],
			'order_date' => date('Y-m-d H:i', strtotime($order['booking_date'])),
			'pickup_location' => $this->input->post('pickup_location') ?: 'home',
			'billing_customer_name' => $first_name,
			'billing_last_name' => $last_name,
			'billing_address' => $billing_address,
			'billing_address_2' => $this->input->post('address_2') ?: '',
			'billing_city' => $billing_city,
			'billing_pincode' => (int)$billing_pincode,
			'billing_state' => $billing_state,
			'billing_country' => 'India',
			'billing_email' => $billing_email,
			'billing_phone' => (int)$billing_phone,
			'billing_isd_code' => '+91',
			'shipping_is_billing' => true,
			'shipping_customer_name' => $first_name,
			'shipping_last_name' => $last_name,
			'shipping_address' => $billing_address,
			'shipping_address_2' => $this->input->post('address_2') ?: '',
			'shipping_city' => $billing_city,
			'shipping_pincode' => (int)$billing_pincode,
			'shipping_state' => $billing_state,
			'shipping_country' => 'India',
			'shipping_email' => $billing_email,
			'shipping_phone' => (int)$billing_phone,
			'longitude' =>  '',
			'latitude' => '',
			'channel_id' => '8855412',
			'comment' => '',
			'reseller_name' => '',
			'company_name' => '',
			'billing_alternate_phone' => '',
			'shipping_charges' => 0,
			'giftwrap_charges' => 0,
			'transaction_charges' => 0,
			'total_discount' => 0,
			'customer_gstin' => 0,
			'invoice_number' => '',
			'order_type' => '',
			'sub_total' => (float)$order['total_item_amount'],
			'order_items' => $order_items,
			'payment_method' => $this->input->post('payment_method') ?: ($order['payment_mode'] == 'COD' ? 'COD' : 'Prepaid'),
			'length' => (float)$this->input->post('length'),
			'width' => (float)$this->input->post('width'),
			'height' => (float)$this->input->post('height'),
			'breadth' => (float)$this->input->post('breadth'),
			'weight' => (float)$this->input->post('weight'),
			'ewaybill_no' => '',
			'checkout_shipping_method' => '',
			'what3words_address' => '',
			'is_insurance_opt' => '',
			'is_document' => '',
			'order_tags' => '',
		];

		$response = $this->shiprocket->create_order($shiprocket_data);

		if (isset($response['order_id'])) {
			$this->CommonModel->updateRowById('book_product', 'product_book_id', $id, [
				'shiprocket_order_id' => $response['order_id'],
				'shiprocket_shipment_id' => $response['shipment_id'],
				'shiprocket_status' => 'CREATED',
				'address' => $this->input->post('address'),
				'city' => $this->input->post('city'),
				'postal_code' => $this->input->post('pincode'),
				'state' => $this->input->post('state')
			]);
			echo json_encode(['success' => true, 'message' => 'Order synced to Shiprocket successfully']);
		} else {
			echo json_encode(['success' => false, 'message' => @$response['message'] ?: 'Unknown Shiprocket error', 'data' => $shiprocket_data]);
		}
	}
}
