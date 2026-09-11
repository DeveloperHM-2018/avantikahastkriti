<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AdminReturn extends CI_Controller
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
		if (sessionId('privileges')) {
			$this->prev = json_decode(sessionId('privileges'), true);
		}
		define('USER_TYPE', $this->user_type);
		define('PREV', $this->prev);
	}

	private static $returnReasons = [
		'Wrong Product Received',
		'Damaged Product',
		'Defective Product',
		'Size Issue',
		'Color Mismatch',
		'Product Not As Expected',
		'Missing Parts',
		'Other',
	];

	public function returnDashboard()
	{
		$data['title'] = 'Returns Dashboard';
		$data['counts'] = [
			'requested' => $this->CommonModel->getNumRows('return_request', ['status' => RETURN_STATUS_REQUESTED]),
			'under_review' => $this->CommonModel->getNumRows('return_request', ['status' => RETURN_STATUS_UNDER_REVIEW]),
			'approved' => $this->CommonModel->getNumRows('return_request', ['status' => RETURN_STATUS_APPROVED]),
			'rejected' => $this->CommonModel->getNumRows('return_request', ['status' => RETURN_STATUS_REJECTED]),
			'pickup_scheduled' => $this->CommonModel->getNumRows('return_request', ['status' => RETURN_STATUS_PICKUP_SCHEDULED]),
			'picked_up' => $this->CommonModel->getNumRows('return_request', ['status' => RETURN_STATUS_PICKED_UP]),
			'refund_pending' => $this->CommonModel->getNumRows('return_request', ['status' => RETURN_STATUS_RECEIVED_WAREHOUSE]),
			'refund_completed' => $this->CommonModel->getNumRows('return_request', ['status' => RETURN_STATUS_REFUND_PROCESSED]),
		];
		$this->load->view('admin/return/dashboard', $data);
	}

	public function returnReports()
	{
		$data['title'] = 'Return Reports';

		// date()/strtotime() round-trip guarantees a safe Y-m-d string before
		// it's interpolated into raw SQL below - never trust the GET value directly.
		$dateFrom = date('Y-m-d', strtotime($this->input->get('date_from') ?: '-11 months'));
		$dateTo = date('Y-m-d', strtotime($this->input->get('date_to') ?: 'now'));
		$data['date_from'] = $dateFrom;
		$data['date_to'] = $dateTo;

		$totalReturnsRow = $this->CommonModel->runQuery(
			"SELECT COUNT(*) AS cnt FROM tbl_return_request WHERE DATE(create_date) BETWEEN '$dateFrom' AND '$dateTo'",
			2
		);
		$data['total_returns'] = $totalReturnsRow ? (int) $totalReturnsRow['cnt'] : 0;

		$deliveredOrdersRow = $this->CommonModel->runQuery(
			"SELECT COUNT(*) AS cnt FROM tbl_book_product WHERE booking_status = '4' AND delivery_date BETWEEN '$dateFrom' AND '$dateTo'",
			2
		);
		$deliveredOrders = $deliveredOrdersRow ? (int) $deliveredOrdersRow['cnt'] : 0;
		$data['delivered_orders'] = $deliveredOrders;
		$data['return_percentage'] = $deliveredOrders > 0 ? round($data['total_returns'] / $deliveredOrders * 100, 2) : 0;

		$refundRow = $this->CommonModel->runQuery(
			"SELECT COALESCE(SUM(refund_amount), 0) AS total FROM tbl_return_request WHERE refund_status = " . REFUND_STATUS_COMPLETED . " AND DATE(refund_date) BETWEEN '$dateFrom' AND '$dateTo'",
			2
		);
		$data['total_refund_amount'] = $refundRow ? (float) $refundRow['total'] : 0;

		$data['top_products'] = $this->CommonModel->runQuery(
			"SELECT tbl_return_request.product_id, tbl_book_item.product_name, COUNT(*) AS cnt
			FROM tbl_return_request
			LEFT JOIN tbl_book_item ON tbl_book_item.book_item_id = tbl_return_request.book_item_id
			WHERE DATE(tbl_return_request.create_date) BETWEEN '$dateFrom' AND '$dateTo'
			GROUP BY tbl_return_request.product_id
			ORDER BY cnt DESC
			LIMIT 10",
			1
		) ?: [];

		$data['top_reasons'] = $this->CommonModel->runQuery(
			"SELECT reason, COUNT(*) AS cnt FROM tbl_return_request
			WHERE DATE(create_date) BETWEEN '$dateFrom' AND '$dateTo'
			GROUP BY reason ORDER BY cnt DESC",
			1
		) ?: [];

		// Trailing-12-month trend is independent of the date filter above (it's
		// a fixed-window chart, not a filtered report).
		$data['monthly_returns'] = $this->CommonModel->runQuery(
			"SELECT DATE_FORMAT(create_date, '%Y-%m') AS ym, COUNT(*) AS cnt
			FROM tbl_return_request
			WHERE create_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
			GROUP BY ym ORDER BY ym ASC",
			1
		) ?: [];

		$data['courier_performance'] = $this->CommonModel->runQuery(
			"SELECT shiprocket_courier_name, COUNT(*) AS cnt FROM tbl_return_request
			WHERE shiprocket_courier_name IS NOT NULL AND shiprocket_courier_name != ''
			GROUP BY shiprocket_courier_name ORDER BY cnt DESC",
			1
		) ?: [];

		$data['customer_frequency'] = $this->CommonModel->runQuery(
			"SELECT tbl_return_request.user_id, tbl_user_registration.name, tbl_user_registration.contact_no, COUNT(*) AS cnt
			FROM tbl_return_request
			LEFT JOIN tbl_user_registration ON tbl_user_registration.user_id = tbl_return_request.user_id
			GROUP BY tbl_return_request.user_id
			ORDER BY cnt DESC
			LIMIT 10",
			1
		) ?: [];

		$this->load->view('admin/return/reports', $data);
	}

	// Shared filter-building logic for both the DataTables listing and the CSV
	// export, so the two can never drift out of sync on what "filtered" means.
	private function buildReturnSearchQuery($filters)
	{
		$searchQuery = "";
		if (!empty($filters['search'])) {
			$searchQuery .= " AND (book_product.order_id LIKE '%" . $this->db->escape_like_str($filters['search']) . "%' OR user_registration.name LIKE '%" . $this->db->escape_like_str($filters['search']) . "%' OR return_request.return_code LIKE '%" . $this->db->escape_like_str($filters['search']) . "%')";
		}

		$dateFrom = $filters['date_from'] ?? '';
		$dateTo = $filters['date_to'] ?? '';
		if ($dateFrom && $dateTo) {
			$searchQuery .= " AND (date(return_request.create_date) >= '" . dateConvertToDb($dateFrom) . "') AND (date(return_request.create_date) <= '" . dateConvertToDb($dateTo) . "')";
		} else if ($dateFrom) {
			$searchQuery .= " AND (date(return_request.create_date) = '" . dateConvertToDb($dateFrom) . "')";
		} else if ($dateTo) {
			$searchQuery .= " AND (date(return_request.create_date) = '" . dateConvertToDb($dateTo) . "')";
		}
		$searchQuery .= (isset($filters['status']) && $filters['status'] !== '') ? " AND (return_request.status = '" . (int) $filters['status'] . "')" : "";
		$searchQuery .= !empty($filters['reason']) ? " AND (return_request.reason = '" . $this->db->escape_str($filters['reason']) . "')" : "";
		$searchQuery .= !empty($filters['payment_mode']) ? " AND (book_product.payment_mode = '" . $this->db->escape_str($filters['payment_mode']) . "')" : "";
		$searchQuery .= !empty($filters['order_id']) ? " AND (book_product.order_id LIKE '%" . $this->db->escape_like_str($filters['order_id']) . "%')" : "";
		$searchQuery .= !empty($filters['mobile']) ? " AND (user_registration.contact_no LIKE '%" . $this->db->escape_like_str($filters['mobile']) . "%')" : "";

		return $searchQuery;
	}

	public function returnList()
	{
		if (count($_POST) > 0) {
			$postData = $this->input->post();

			$searchQuery = $this->buildReturnSearchQuery([
				'search' => $postData['search']['value'],
				'date_from' => $this->input->post('searchByDateFrom'),
				'date_to' => $this->input->post('searchByDateTo'),
				'status' => $this->input->post('searchByStatus'),
				'reason' => $this->input->post('searchByReason'),
				'payment_mode' => $this->input->post('searchByPaymentMode'),
				'order_id' => $this->input->post('searchByOrderId'),
				'mobile' => $this->input->post('searchByMobile'),
			]);

			$select = "return_request.*, book_product.order_id, book_product.payment_mode, book_item.product_name, user_registration.name AS customer_name, user_registration.contact_no";
			$join = [
				['book_product', 'book_product.product_book_id = return_request.product_book_id', 'LEFT'],
				['book_item', 'book_item.book_item_id = return_request.book_item_id', 'LEFT'],
				['user_registration', 'user_registration.user_id = return_request.user_id', 'LEFT'],
			];
			$allData = $this->CommonModel->getAjaxDataWithJoin($select, 'return_request', $searchQuery, $postData, $join);

			$draw = $postData['draw'];
			$data = [];
			foreach ($allData['records'] as $record) {
				$id = encryptId($record['return_id']);
				$statusColors = [
					RETURN_STATUS_REQUESTED => 'warning',
					RETURN_STATUS_UNDER_REVIEW => 'info',
					RETURN_STATUS_APPROVED => 'primary',
					RETURN_STATUS_PICKUP_SCHEDULED => 'primary',
					RETURN_STATUS_PICKED_UP => 'primary',
					RETURN_STATUS_RECEIVED_WAREHOUSE => 'primary',
					RETURN_STATUS_REFUND_PROCESSED => 'success',
					RETURN_STATUS_REJECTED => 'danger',
				];
				$status = statusView($statusColors[$record['status']] ?? 'secondary', getReturnStatusLabel($record['status']));
				$action = '<a href="' . base_url("returnDetails?id=$id") . '" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> View</a>';

				$data[] = [
					'return_code' => $record['return_code'],
					'order_id' => $record['order_id'],
					'customer_name' => $record['customer_name'] . '<br>' . $record['contact_no'],
					'product_name' => $record['product_name'],
					'quantity_return' => $record['quantity_return'],
					'reason' => $record['reason'],
					'create_date' => dateConvertToView($record['create_date'], 3),
					'status' => $status,
					'action' => (@PREV['return_view'] == 1 || USER_TYPE == '1') ? $action : '',
				];
			}

			$response = [
				"draw" => intval($draw),
				"iTotalRecords" => $allData['totalRecords'],
				"iTotalDisplayRecords" => $allData['totalRecordwithFilter'],
				"aaData" => $data,
			];
			echo json_encode($response);
		} else {
			$data['title'] = 'Return Requests';
			$data['reasons'] = self::$returnReasons;
			$table_header = [
				['data' => 'return_code'],
				['data' => 'order_id'],
				['data' => 'customer_name'],
				['data' => 'product_name'],
				['data' => 'quantity_return'],
				['data' => 'reason'],
				['data' => 'create_date'],
				['data' => 'status'],
				['data' => 'action'],
			];
			$data['ajax_table'] = "returnList";
			$data['table_column'] = json_encode($table_header);
			$data['col_stop'] = "8";
			$this->load->view('admin/return/return_list', $data);
		}
	}

	// CSV export honors the exact same filters as the on-screen listing
	// (passed as query params rather than POST, since this is a plain
	// download link), so "export what I'm looking at" always holds true.
	public function exportReturnsCsv()
	{
		if (!(@PREV['return_view'] == 1 || USER_TYPE == '1')) {
			show_404();
		}

		$searchQuery = $this->buildReturnSearchQuery([
			'search' => $this->input->get('search'),
			'date_from' => $this->input->get('searchByDateFrom'),
			'date_to' => $this->input->get('searchByDateTo'),
			'status' => $this->input->get('searchByStatus'),
			'reason' => $this->input->get('searchByReason'),
			'payment_mode' => $this->input->get('searchByPaymentMode'),
			'order_id' => $this->input->get('searchByOrderId'),
			'mobile' => $this->input->get('searchByMobile'),
		]);

		$select = "return_request.*, book_product.order_id, book_product.payment_mode, book_item.product_name, user_registration.name AS customer_name, user_registration.contact_no";
		$this->db->select($select)
			->from('return_request')
			->join('book_product', 'book_product.product_book_id = return_request.product_book_id', 'LEFT')
			->join('book_item', 'book_item.book_item_id = return_request.book_item_id', 'LEFT')
			->join('user_registration', 'user_registration.user_id = return_request.user_id', 'LEFT');
		if ($searchQuery != '') {
			$this->db->where("1 " . $searchQuery);
		}
		$rows = $this->db->order_by('return_request.create_date', 'DESC')->get()->result_array();

		$filename = 'returns_' . date('Y-m-d_His') . '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="' . $filename . '"');

		$out = fopen('php://output', 'w');
		fputcsv($out, ['Return ID', 'Order ID', 'Customer', 'Mobile', 'Product', 'Qty', 'Reason', 'Request Date', 'Status', 'Payment Method', 'Refund Method', 'Refund Amount', 'Refund Status']);
		foreach ($rows as $row) {
			fputcsv($out, [
				$row['return_code'],
				$row['order_id'],
				$row['customer_name'],
				$row['contact_no'],
				$row['product_name'],
				$row['quantity_return'],
				$row['reason'],
				dateConvertToView($row['create_date'], 3),
				getReturnStatusLabel($row['status']),
				$row['payment_mode'],
				$row['refund_method'],
				$row['refund_amount'],
				$row['refund_status'] !== null ? getRefundStatusLabel($row['refund_status']) : '',
			]);
		}
		fclose($out);
		exit();
	}

	public function returnDetails()
	{
		$id = decryptId($this->input->get('id'));
		$returnRow = $this->CommonModel->getSingleRowById('return_request', ['return_id' => $id]);
		if (!$returnRow) {
			show_404();
		}

		$data['title'] = 'Return Details - ' . $returnRow['return_code'];
		$data['return'] = $returnRow;
		$data['order'] = $this->CommonModel->getSingleRowById('book_product', ['product_book_id' => $returnRow['product_book_id']]);
		$data['customer'] = $this->CommonModel->getSingleRowById('user_registration', ['user_id' => $returnRow['user_id']]);
		$data['item'] = $this->CommonModel->getSingleRowById('book_item', ['book_item_id' => $returnRow['book_item_id']]);
		$data['images'] = $this->CommonModel->getRowById('return_image', 'return_id', $id);
		$data['timeline'] = $this->CommonModel->getRowByIdInOrder('return_status_log', ['return_id' => $id], 'create_date', 'ASC');
		$data['default_refund_amount'] = $data['item'] ? round($data['item']['user_price'] * $returnRow['quantity_return'], 2) : 0;
		$this->load->view('admin/return/return_details', $data);
	}

	public function approveReturn()
	{
		if (!(@PREV['return_process'] == 1 || USER_TYPE == '1')) {
			echo json_encode(['status' => false, 'message' => 'You do not have permission to approve returns.']);
			return;
		}

		$id = decryptId($this->input->post('id'));
		$adminNotes = trim((string) $this->input->post('admin_notes'));
		$returnRow = $this->CommonModel->getSingleRowById('return_request', ['return_id' => $id]);
		if (!$returnRow) {
			echo json_encode(['status' => false, 'message' => 'Return request not found.']);
			return;
		}
		if (in_array((int) $returnRow['status'], [RETURN_STATUS_APPROVED, RETURN_STATUS_REJECTED], true) || $returnRow['status'] > RETURN_STATUS_APPROVED) {
			echo json_encode(['status' => false, 'message' => 'This return has already been processed.']);
			return;
		}

		$this->CommonModel->updateRowByIdWithOutXss('return_request', "return_id = '$id'", ['admin_notes' => $adminNotes]);
		logReturnStatus($id, RETURN_STATUS_APPROVED, $adminNotes, 1, sessionId('admin_id'));

		$order = $this->CommonModel->getSingleRowById('book_product', ['product_book_id' => $returnRow['product_book_id']]);
		if ($order) {
			sendTemplatedMail('return_approved_user', $order['email'], [
				'name' => $order['name'],
				'order_id' => $order['order_id'],
				'return_code' => $returnRow['return_code'],
				'app_name' => APP_NAME,
			]);
		}

		$message = 'Return request approved.';
		if ($this->input->post('create_pickup') == '1') {
			$pickupResult = $this->attemptShiprocketPickup($id, $returnRow, $order);
			$message .= ' ' . $pickupResult['message'];
		}

		echo json_encode(['status' => true, 'message' => $message]);
	}

	// Standalone retry/entry point for scheduling a Shiprocket reverse pickup
	// on a return that's already Approved (e.g. approval happened without
	// requesting pickup, or an earlier pickup attempt failed).
	public function schedulePickup()
	{
		if (!(@PREV['return_process'] == 1 || USER_TYPE == '1')) {
			echo json_encode(['status' => false, 'message' => 'You do not have permission to schedule pickups.']);
			return;
		}

		$id = decryptId($this->input->post('id'));
		$returnRow = $this->CommonModel->getSingleRowById('return_request', ['return_id' => $id]);
		if (!$returnRow) {
			echo json_encode(['status' => false, 'message' => 'Return request not found.']);
			return;
		}
		if ((int) $returnRow['status'] !== RETURN_STATUS_APPROVED) {
			echo json_encode(['status' => false, 'message' => 'Pickup can only be scheduled for an Approved return.']);
			return;
		}

		$order = $this->CommonModel->getSingleRowById('book_product', ['product_book_id' => $returnRow['product_book_id']]);
		if (!$order) {
			echo json_encode(['status' => false, 'message' => 'Order not found.']);
			return;
		}

		$result = $this->attemptShiprocketPickup($id, $returnRow, $order);
		echo json_encode(['status' => $result['success'], 'message' => $result['message']]);
	}

	// Creates the Shiprocket reverse-pickup order, requests an AWB, and
	// requests pickup - mirroring AdminHome::shipWithShiprocket()/dispatchOrder()'s
	// forward-shipment flow. Never throws: a Shiprocket failure here must not
	// block the return's Approved status, since pickup can always be retried
	// via schedulePickup().
	private function attemptShiprocketPickup($returnId, $returnRow, $order)
	{
		$item = $this->CommonModel->getSingleRowById('book_item', ['book_item_id' => $returnRow['book_item_id']]);
		$setting = $this->CommonModel->getSingleRowById('setting', ['id' => 1]);

		if (empty($setting['warehouse_address']) || empty($setting['warehouse_pincode'])) {
			return ['success' => false, 'message' => 'Warehouse address is not configured. Set it under Returns > Settings before scheduling pickup.'];
		}

		try {
			$this->load->library('shiprocket');

			$fullName = preg_replace('/[^a-zA-Z0-9\s]/', '', trim($order['name']));
			$nameParts = explode(' ', $fullName, 2);
			$firstName = $nameParts[0] ?: 'Customer';
			$lastName = (isset($nameParts[1]) && $nameParts[1] !== '') ? $nameParts[1] : 'Customer';

			$shiprocketData = [
				'order_id' => 'RET-' . $returnRow['return_code'],
				'order_date' => date('Y-m-d H:i'),
				// Pickup happens at the customer's address for a return.
				'pickup_customer_name' => $firstName,
				'pickup_last_name' => $lastName,
				'pickup_address' => $order['address'],
				'pickup_address_2' => '',
				'pickup_city' => $order['city'],
				'pickup_pincode' => (int) $order['postal_code'],
				'pickup_state' => $order['state'],
				'pickup_country' => 'India',
				'pickup_email' => $order['email'],
				'pickup_phone' => preg_replace('/\D/', '', $order['contact_no']),
				'pickup_isd_code' => '+91',
				// Delivered back to our configured warehouse/RTO address.
				'shipping_customer_name' => APP_NAME,
				'shipping_address' => $setting['warehouse_address'],
				'shipping_city' => $setting['warehouse_city'],
				'shipping_pincode' => (int) $setting['warehouse_pincode'],
				'shipping_state' => $setting['warehouse_state'],
				'shipping_country' => 'India',
				'shipping_email' => $setting['warehouse_email'] ?: $order['email'],
				'shipping_phone' => preg_replace('/\D/', '', $setting['warehouse_phone'] ?: $order['contact_no']),
				'order_items' => [[
					'name' => $item['product_name'],
					'sku' => $item['product_id'] . ($item['variant_id'] ? '-' . $item['variant_id'] : ''),
					'units' => (int) $returnRow['quantity_return'],
					'selling_price' => (float) $item['user_price'],
					'discount' => 0,
					'tax' => 0,
					'hsn' => 0,
				]],
				'payment_method' => 'Prepaid',
				'sub_total' => (float) $item['user_price'] * (int) $returnRow['quantity_return'],
				'length' => 10,
				'breadth' => 10,
				'height' => 10,
				'weight' => 0.5,
			];

			$response = $this->shiprocket->create_return_order($shiprocketData);
			if (!isset($response['order_id'])) {
				logReturnStatus($returnId, RETURN_STATUS_APPROVED, 'Shiprocket pickup creation failed: ' . json_encode($response), 1, sessionId('admin_id'));
				return ['success' => false, 'message' => 'Shiprocket did not accept the return order. Please retry or check the Shiprocket panel.'];
			}

			$update = [
				'shiprocket_return_order_id' => $response['order_id'],
				'shiprocket_return_shipment_id' => $response['shipment_id'] ?? null,
				'shiprocket_pickup_status' => 'ORDER_CREATED',
			];

			if (!empty($response['shipment_id'])) {
				$awbResponse = $this->shiprocket->generate_awb($response['shipment_id']);
				if (isset($awbResponse['awb_code'])) {
					$update['shiprocket_pickup_awb'] = $awbResponse['awb_code'];
					$update['shiprocket_courier_name'] = $awbResponse['courier_name'] ?? null;
					$update['shiprocket_tracking_url'] = 'https://shiprocket.co/tracking/' . $awbResponse['awb_code'];
					$update['shiprocket_pickup_status'] = 'AWB_GENERATED';

					$pickupResponse = $this->shiprocket->request_pickup($response['shipment_id']);
					if (isset($pickupResponse['pickup_scheduled_date'])) {
						$update['shiprocket_pickup_date'] = date('Y-m-d', strtotime($pickupResponse['pickup_scheduled_date']));
						$update['shiprocket_pickup_status'] = 'PICKUP_SCHEDULED';
					}
				}
			}

			$this->CommonModel->updateRowByIdWithOutXss('return_request', "return_id = '$returnId'", $update);

			if ($update['shiprocket_pickup_status'] === 'PICKUP_SCHEDULED') {
				logReturnStatus($returnId, RETURN_STATUS_PICKUP_SCHEDULED, 'Shiprocket pickup scheduled' . (!empty($update['shiprocket_pickup_awb']) ? ' - AWB ' . $update['shiprocket_pickup_awb'] : ''), 1, sessionId('admin_id'));
				return ['success' => true, 'message' => 'Shiprocket pickup scheduled successfully.'];
			}

			return ['success' => true, 'message' => 'Shiprocket return order created; AWB/pickup is still pending - check back shortly or retry.'];
		} catch (\Throwable $e) {
			log_message('error', 'attemptShiprocketPickup failed for return_id ' . $returnId . ': ' . $e->getMessage());
			return ['success' => false, 'message' => 'Could not reach Shiprocket. Please retry shortly.'];
		}
	}

	public function rejectReturn()
	{
		if (!(@PREV['return_process'] == 1 || USER_TYPE == '1')) {
			echo json_encode(['status' => false, 'message' => 'You do not have permission to reject returns.']);
			return;
		}

		$id = decryptId($this->input->post('id'));
		$rejectReason = trim((string) $this->input->post('reject_reason'));
		$rejectRemarks = trim((string) $this->input->post('reject_remarks'));
		if ($rejectReason === '') {
			echo json_encode(['status' => false, 'message' => 'Please provide a rejection reason.']);
			return;
		}

		$returnRow = $this->CommonModel->getSingleRowById('return_request', ['return_id' => $id]);
		if (!$returnRow) {
			echo json_encode(['status' => false, 'message' => 'Return request not found.']);
			return;
		}
		if (in_array((int) $returnRow['status'], [RETURN_STATUS_APPROVED, RETURN_STATUS_REJECTED], true) || $returnRow['status'] > RETURN_STATUS_APPROVED) {
			echo json_encode(['status' => false, 'message' => 'This return has already been processed.']);
			return;
		}

		$this->CommonModel->updateRowByIdWithOutXss('return_request', "return_id = '$id'", [
			'reject_reason' => $rejectReason,
			'reject_remarks' => $rejectRemarks,
		]);
		logReturnStatus($id, RETURN_STATUS_REJECTED, $rejectReason . ($rejectRemarks ? " - $rejectRemarks" : ''), 1, sessionId('admin_id'));

		$order = $this->CommonModel->getSingleRowById('book_product', ['product_book_id' => $returnRow['product_book_id']]);
		if ($order) {
			sendTemplatedMail('return_rejected_user', $order['email'], [
				'name' => $order['name'],
				'order_id' => $order['order_id'],
				'return_code' => $returnRow['return_code'],
				'reject_reason' => $rejectReason,
				'app_name' => APP_NAME,
			]);
		}

		echo json_encode(['status' => true, 'message' => 'Return request rejected.']);
	}

	private static $refundMethods = ['COD', 'ONLINE', 'WALLET', 'BANK_TRANSFER'];

	public function processRefund()
	{
		if (!(@PREV['return_refund'] == 1 || USER_TYPE == '1')) {
			echo json_encode(['status' => false, 'message' => 'You do not have permission to process refunds.']);
			return;
		}

		$id = decryptId($this->input->post('id'));
		$returnRow = $this->CommonModel->getSingleRowById('return_request', ['return_id' => $id]);
		if (!$returnRow) {
			echo json_encode(['status' => false, 'message' => 'Return request not found.']);
			return;
		}
		if ((int) $returnRow['status'] === RETURN_STATUS_REJECTED || (int) $returnRow['status'] < RETURN_STATUS_APPROVED) {
			echo json_encode(['status' => false, 'message' => 'Refund can only be processed for an approved return.']);
			return;
		}
		if ((int) $returnRow['refund_status'] === REFUND_STATUS_COMPLETED) {
			echo json_encode(['status' => false, 'message' => 'This return has already been refunded.']);
			return;
		}

		$refundMethod = $this->input->post('refund_method');
		$refundAmount = (float) $this->input->post('refund_amount');
		$refundStatus = (int) $this->input->post('refund_status');
		$transactionId = trim((string) $this->input->post('transaction_id'));
		$remark = trim((string) $this->input->post('remark'));

		if (!in_array($refundMethod, self::$refundMethods, true)) {
			echo json_encode(['status' => false, 'message' => 'Invalid refund method.']);
			return;
		}
		if ($refundAmount <= 0) {
			echo json_encode(['status' => false, 'message' => 'Refund amount must be greater than 0.']);
			return;
		}
		if (!in_array($refundStatus, [REFUND_STATUS_PENDING, REFUND_STATUS_COMPLETED, REFUND_STATUS_FAILED], true)) {
			echo json_encode(['status' => false, 'message' => 'Invalid refund status.']);
			return;
		}

		$update = [
			'refund_method' => $refundMethod,
			'refund_amount' => $refundAmount,
			'refund_status' => $refundStatus,
			'refund_transaction_id' => $transactionId !== '' ? $transactionId : null,
			'refund_remark' => $remark !== '' ? $remark : null,
			'refund_date' => $refundStatus === REFUND_STATUS_COMPLETED ? setDateTime() : null,
		];
		$this->CommonModel->updateRowByIdWithOutXss('return_request', "return_id = '$id'", $update);

		$order = $this->CommonModel->getSingleRowById('book_product', ['product_book_id' => $returnRow['product_book_id']]);
		$note = "Refund $refundMethod - " . getRefundStatusLabel($refundStatus) . ($transactionId ? " (txn: $transactionId)" : '') . ($remark ? " - $remark" : '');

		if ($refundStatus === REFUND_STATUS_COMPLETED) {
			if ($refundMethod === 'WALLET') {
				creditWallet($returnRow['user_id'], $refundAmount, 'Refund for return ' . $returnRow['return_code']);
			}
			logReturnStatus($id, RETURN_STATUS_REFUND_PROCESSED, $note, 1, sessionId('admin_id'));

			if ($order) {
				sendTemplatedMail('return_refund_completed_user', $order['email'], [
					'name' => $order['name'],
					'order_id' => $order['order_id'],
					'return_code' => $returnRow['return_code'],
					'refund_amount' => number_format($refundAmount, 2),
					'refund_method' => $refundMethod,
					'app_name' => APP_NAME,
				]);
			}
		} else {
			// Pending/Failed: record the attempt without moving the coarse
			// return status forward - the return stays at its current stage
			// (e.g. Received At Warehouse) until a refund actually completes.
			logReturnStatus($id, (int) $returnRow['status'], $note, 1, sessionId('admin_id'));
		}

		echo json_encode(['status' => true, 'message' => 'Refund details saved.']);
	}

	public function returnSetting()
	{
		$get = $this->CommonModel->getSingleRowById('setting', ['id' => 1]);
		$data['title'] = 'Return Settings';
		$data['return_window_days'] = set_value('return_window_days') == false ? @$get['return_window_days'] : set_value('return_window_days');
		$data['warehouse_address'] = set_value('warehouse_address') == false ? @$get['warehouse_address'] : set_value('warehouse_address');
		$data['warehouse_city'] = set_value('warehouse_city') == false ? @$get['warehouse_city'] : set_value('warehouse_city');
		$data['warehouse_state'] = set_value('warehouse_state') == false ? @$get['warehouse_state'] : set_value('warehouse_state');
		$data['warehouse_pincode'] = set_value('warehouse_pincode') == false ? @$get['warehouse_pincode'] : set_value('warehouse_pincode');
		$data['warehouse_phone'] = set_value('warehouse_phone') == false ? @$get['warehouse_phone'] : set_value('warehouse_phone');
		$data['warehouse_email'] = set_value('warehouse_email') == false ? @$get['warehouse_email'] : set_value('warehouse_email');

		if (count($_POST) > 0) {
			$this->form_validation->set_rules('return_window_days', 'Return Window (Days)', 'required|numeric|greater_than[0]');
			if ($this->form_validation->run()) {
				$post['return_window_days'] = $this->input->post('return_window_days');
				$post['warehouse_address'] = $this->input->post('warehouse_address');
				$post['warehouse_city'] = $this->input->post('warehouse_city');
				$post['warehouse_state'] = $this->input->post('warehouse_state');
				$post['warehouse_pincode'] = $this->input->post('warehouse_pincode');
				$post['warehouse_phone'] = $this->input->post('warehouse_phone');
				$post['warehouse_email'] = $this->input->post('warehouse_email');
				$this->CommonModel->updateRowByIdWithOutXss('setting', "id = '1'", $post);
				flashData('errors', 'Return Settings Updated Successfully');
				redirect('returnSetting');
			}
		}
		$this->load->view('admin/return/return_setting', $data);
	}
}
