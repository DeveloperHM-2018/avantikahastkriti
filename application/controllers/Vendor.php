<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Vendor-facing self-service portal: register, login, submit products for
// admin review, view own orders/commission/payouts. Vendor identity must
// never leak into any customer-facing view (see AdminVendor for the
// admin-side approval workflow). Every query here is scoped to the caller's
// own sessionId('vendor_id') - never trust a client-supplied vendor/product
// id alone, mirroring the user_id-scoping already used in UserApi.
class Vendor extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	private function requireLogin()
	{
		if (sessionId('vendor_id') == '') {
			redirect('vendor/login');
			exit;
		}
	}

	public function register()
	{
		if (sessionId('vendor_id') != '') {
			redirect('vendor/dashboard');
		}

		if (count($_POST) > 0) {
			$this->form_validation->set_rules('business_name', 'Business Name', 'trim|required|max_length[150]');
			$this->form_validation->set_rules('contact_name', 'Contact Person', 'trim|required|max_length[100]');
			$this->form_validation->set_rules('contact_no', 'Mobile Number', 'trim|required|exact_length[10]|numeric|is_unique[vendor.contact_no]');
			$this->form_validation->set_rules('email_id', 'Email', 'trim|required|valid_email|is_unique[vendor.email_id]');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
			$this->form_validation->set_rules('address', 'Business Address', 'trim|required');
			$this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');

			if ($this->form_validation->run()) {
				$vendorId = $this->CommonModel->insertRowReturnId('vendor', [
					'business_name' => $this->input->post('business_name'),
					'contact_name' => $this->input->post('contact_name'),
					'contact_no' => $this->input->post('contact_no'),
					'email_id' => $this->input->post('email_id'),
					'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
					'gst_number' => $this->input->post('gst_number'),
					'pan_number' => $this->input->post('pan_number'),
					'address' => $this->input->post('address'),
					'city' => $this->input->post('city'),
					'state' => $this->input->post('state'),
					'postal_code' => $this->input->post('postal_code'),
					'bank_account_name' => $this->input->post('bank_account_name'),
					'bank_account_no' => $this->input->post('bank_account_no'),
					'bank_ifsc' => $this->input->post('bank_ifsc'),
					'status' => 0,
				]);

				if ($vendorId) {
					foreach (['gst_certificate' => 'gst_certificate', 'pan_card' => 'pan_card', 'bank_proof' => 'bank_proof'] as $field => $docType) {
						$this->CommonModel->handleVendorDocumentUpload($vendorId, $field, $docType);
					}
					flashData('errors', 'Registration submitted. An admin will review your application shortly.');
					redirect('vendor/login');
					return;
				}
				flashData('errors', 'Something went wrong. Please try again.');
			} else {
				flashData('errors', validation_errors());
			}
		}

		$this->load->view('vendor/register');
	}

	public function login()
	{
		if (sessionId('vendor_id') != '') {
			redirect('vendor/dashboard');
		}

		if (count($_POST) > 0) {
			$this->form_validation->set_rules('email_id', 'Email', 'trim|required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required');
			$this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');

			if ($this->form_validation->run()) {
				$email = $this->input->post('email_id');
				$password = $this->input->post('password');
				$vendor = $this->CommonModel->getSingleRowById('vendor', ['email_id' => $email]);

				if (!$vendor) {
					flashData('errors', 'Invalid email or password.');
				} elseif ($vendor['locked_until'] && strtotime($vendor['locked_until']) > time()) {
					flashData('errors', 'Too many failed attempts. Try again after ' . date('h:i A', strtotime($vendor['locked_until'])) . '.');
				} elseif (!password_verify($password, $vendor['password'])) {
					$failedCount = (int) $vendor['failed_login_count'] + 1;
					$update = ['failed_login_count' => $failedCount];
					if ($failedCount >= 5) {
						$update['locked_until'] = date('Y-m-d H:i:s', strtotime('+15 minutes'));
						$update['failed_login_count'] = 0;
					}
					$this->CommonModel->updateRowById('vendor', 'vendor_id', $vendor['vendor_id'], $update);
					flashData('errors', 'Invalid email or password.');
				} elseif ($vendor['status'] == 0) {
					flashData('errors', 'Your registration is still pending admin approval.');
				} elseif ($vendor['status'] == 2) {
					flashData('errors', 'Your registration was rejected. Contact support for details.');
				} elseif ($vendor['status'] == 3) {
					flashData('errors', 'Your account has been suspended. Contact support.');
				} else {
					$this->CommonModel->updateRowById('vendor', 'vendor_id', $vendor['vendor_id'], ['failed_login_count' => 0, 'locked_until' => null]);
					// Regenerate the session id on privilege escalation, same as
					// AdminAuth - prevents a pre-login session id from carrying
					// into an authenticated vendor session.
					session_regenerate_id(true);
					setSession([
						'vendor_id' => $vendor['vendor_id'],
						'vendor_name' => $vendor['business_name'],
					]);
					$this->CommonModel->logAdminActivity(ACTOR_TYPE_VENDOR, $vendor['vendor_id'], 'vendor_login', 'vendor', $vendor['vendor_id']);
					redirect('vendor/dashboard');
					return;
				}
			} else {
				flashData('errors', validation_errors());
			}
		}

		$this->load->view('vendor/login');
	}

	public function logout()
	{
		$vendorId = sessionId('vendor_id');
		if ($vendorId != '') {
			$this->CommonModel->logAdminActivity(ACTOR_TYPE_VENDOR, $vendorId, 'vendor_logout', 'vendor', $vendorId);
		}
		$this->session->unset_userdata(['vendor_id', 'vendor_name']);
		session_regenerate_id(true);
		redirect('vendor/login');
	}

	public function dashboard()
	{
		$this->requireLogin();
		$vendorId = sessionId('vendor_id');

		$data['title'] = 'Vendor Dashboard';
		$data['vendor'] = $this->CommonModel->getSingleRowById('vendor', ['vendor_id' => $vendorId]);
		$data['total_products'] = $this->CommonModel->getNumRows('vendor_product', ['vendor_id' => $vendorId]);
		$data['pending_products'] = $this->CommonModel->getNumRows('vendor_product', ['vendor_id' => $vendorId, 'status' => 0]);
		$data['live_products'] = $this->CommonModel->getNumRows('vendor_product', ['vendor_id' => $vendorId, 'status' => 1]);
		$data['total_orders'] = $this->CommonModel->getNumRows('vendor_order_item', ['vendor_id' => $vendorId]);
		$data['pending_orders'] = $this->CommonModel->getNumRows('vendor_order_item', ['vendor_id' => $vendorId, 'payout_status' => 0]);

		$salesRow = $this->CommonModel->runQuery(
			"SELECT COALESCE(SUM(vendor_payable_amount), 0) AS payable, COALESCE(SUM(commission_amount + vendor_payable_amount), 0) AS gross
			FROM tbl_vendor_order_item WHERE vendor_id = " . (int) $vendorId,
			2
		);
		$data['total_sales'] = $salesRow ? (float) $salesRow['gross'] : 0;
		$data['payable_amount'] = $salesRow ? (float) $salesRow['payable'] : 0;

		$paidRow = $this->CommonModel->runQuery(
			"SELECT COALESCE(SUM(total_amount), 0) AS paid FROM tbl_vendor_payout WHERE vendor_id = " . (int) $vendorId . " AND status = 1",
			2
		);
		$data['paid_amount'] = $paidRow ? (float) $paidRow['paid'] : 0;

		$data['recent_orders'] = $this->CommonModel->runQuery(
			"SELECT voi.*, bp.order_id, bi.product_name
			FROM tbl_vendor_order_item voi
			LEFT JOIN tbl_book_product bp ON bp.product_book_id = voi.product_book_id
			LEFT JOIN tbl_book_item bi ON bi.book_item_id = voi.book_item_id
			WHERE voi.vendor_id = " . (int) $vendorId . "
			ORDER BY voi.create_date DESC LIMIT 10",
			1
		) ?: [];

		$this->load->view('vendor/dashboard', $data);
	}

	public function products()
	{
		$this->requireLogin();
		$vendorId = sessionId('vendor_id');

		if (count($_POST) > 0) {
			$postData = $this->input->post();
			$searchQuery = " AND vendor_product.vendor_id = " . (int) $vendorId;

			$allData = $this->CommonModel->getAjaxDataWithJoin('vendor_product.*', 'vendor_product', $searchQuery, $postData, []);
			$statusLabels = [0 => 'Pending', 1 => 'Approved', 2 => 'Rejected', 3 => 'Changes Requested'];
			$statusColors = [0 => 'warning', 1 => 'success', 2 => 'danger', 3 => 'info'];

			$data = [];
			foreach ($allData['records'] as $record) {
				$data[] = [
					'product_name' => $record['product_name'],
					'vendor_supply_price' => $record['vendor_supply_price'],
					'proposed_sale_price' => $record['proposed_sale_price'],
					'quantity_supplied' => $record['quantity_supplied'],
					'status' => statusView($statusColors[$record['status']], $statusLabels[$record['status']]),
					'admin_notes' => $record['admin_notes'] ?: '-',
				];
			}

			echo json_encode([
				"draw" => intval($postData['draw']),
				"iTotalRecords" => $allData['totalRecords'],
				"iTotalDisplayRecords" => $allData['totalRecordwithFilter'],
				"aaData" => $data,
			]);
			return;
		}

		$data['title'] = 'My Products';
		$table_header = [
			['data' => 'product_name'],
			['data' => 'vendor_supply_price'],
			['data' => 'proposed_sale_price'],
			['data' => 'quantity_supplied'],
			['data' => 'status'],
			['data' => 'admin_notes'],
		];
		$data['ajax_table'] = 'vendor/products';
		$data['table_column'] = json_encode($table_header);
		$data['col_stop'] = '5';
		$this->load->view('vendor/product_all', $data);
	}

	public function productAdd()
	{
		$this->requireLogin();
		$vendorId = sessionId('vendor_id');

		if (count($_POST) > 0) {
			$this->form_validation->set_rules('product_name', 'Product Name', 'trim|required|max_length[191]');
			$this->form_validation->set_rules('vendor_supply_price', 'Supply Price', 'trim|required|numeric|greater_than[0]');
			$this->form_validation->set_rules('quantity_supplied', 'Quantity', 'trim|required|numeric|greater_than[0]');
			$this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');

			if ($this->form_validation->run()) {
				$this->CommonModel->insertRow('vendor_product', [
					'vendor_id' => $vendorId,
					'product_name' => $this->input->post('product_name'),
					'category_id' => $this->input->post('category_id') ?: null,
					'description' => $this->input->post('description'),
					'vendor_supply_price' => $this->input->post('vendor_supply_price'),
					'proposed_sale_price' => $this->input->post('proposed_sale_price') ?: null,
					'quantity_supplied' => $this->input->post('quantity_supplied'),
					'status' => 0,
				]);
				$this->CommonModel->logAdminActivity(ACTOR_TYPE_VENDOR, $vendorId, 'vendor_product_submit', 'vendor_product', null, null, ['product_name' => $this->input->post('product_name')]);
				flashData('errors', 'Product submitted for admin review.');
				redirect('vendor/products');
				return;
			}
			flashData('errors', validation_errors());
		}

		$data['title'] = 'Submit New Product';
		$data['categories'] = $this->CommonModel->getRowByIdInOrder('category', "is_delete = '1'", 'category_name', 'ASC') ?: [];
		$this->load->view('vendor/product_add', $data);
	}

	public function orders()
	{
		$this->requireLogin();
		$vendorId = sessionId('vendor_id');

		if (count($_POST) > 0) {
			$postData = $this->input->post();
			$searchQuery = " AND vendor_order_item.vendor_id = " . (int) $vendorId;

			$select = "vendor_order_item.*, book_product.order_id, book_product.booking_status, book_item.product_name";
			$join = [
				['book_product', 'book_product.product_book_id = vendor_order_item.product_book_id', 'LEFT'],
				['book_item', 'book_item.book_item_id = vendor_order_item.book_item_id', 'LEFT'],
			];
			$allData = $this->CommonModel->getAjaxDataWithJoin($select, 'vendor_order_item', $searchQuery, $postData, $join);
			$payoutLabels = [0 => 'Unpaid', 1 => 'In Next Payout', 2 => 'Paid'];
			$payoutColors = [0 => 'warning', 1 => 'info', 2 => 'success'];

			$data = [];
			foreach ($allData['records'] as $record) {
				$data[] = [
					'order_id' => $record['order_id'],
					'product_name' => $record['product_name'],
					'quantity' => $record['quantity'],
					'vendor_payable_amount' => $record['vendor_payable_amount'],
					'create_date' => dateConvertToView($record['create_date'], 3),
					'payout_status' => statusView($payoutColors[$record['payout_status']], $payoutLabels[$record['payout_status']]),
				];
			}

			echo json_encode([
				"draw" => intval($postData['draw']),
				"iTotalRecords" => $allData['totalRecords'],
				"iTotalDisplayRecords" => $allData['totalRecordwithFilter'],
				"aaData" => $data,
			]);
			return;
		}

		$data['title'] = 'My Orders';
		$table_header = [
			['data' => 'order_id'],
			['data' => 'product_name'],
			['data' => 'quantity'],
			['data' => 'vendor_payable_amount'],
			['data' => 'create_date'],
			['data' => 'payout_status'],
		];
		$data['ajax_table'] = 'vendor/orders';
		$data['table_column'] = json_encode($table_header);
		$this->load->view('vendor/order_all', $data);
	}

	public function payouts()
	{
		$this->requireLogin();
		$vendorId = sessionId('vendor_id');

		$data['title'] = 'Payout History';
		$data['payouts'] = $this->CommonModel->getRowByIdInOrder('vendor_payout', ['vendor_id' => $vendorId], 'create_date', 'DESC') ?: [];
		$this->load->view('vendor/payout_all', $data);
	}

	public function profile()
	{
		$this->requireLogin();
		$vendorId = sessionId('vendor_id');

		if (count($_POST) > 0) {
			if ($this->input->post('form') === 'password') {
				$this->form_validation->set_rules('current_password', 'Current Password', 'required');
				$this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[6]');
				$this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
				if ($this->form_validation->run()) {
					$vendor = $this->CommonModel->getSingleRowById('vendor', ['vendor_id' => $vendorId]);
					if (password_verify($this->input->post('current_password'), $vendor['password'])) {
						$this->CommonModel->updateRowById('vendor', 'vendor_id', $vendorId, ['password' => password_hash($this->input->post('new_password'), PASSWORD_DEFAULT)]);
						flashData('errors', 'Password changed successfully.');
					} else {
						flashData('errors', 'Current password is incorrect.');
					}
				} else {
					flashData('errors', validation_errors());
				}
			} else {
				$this->CommonModel->updateRowById('vendor', 'vendor_id', $vendorId, [
					'contact_name' => $this->input->post('contact_name'),
					'contact_no' => $this->input->post('contact_no'),
					'address' => $this->input->post('address'),
					'city' => $this->input->post('city'),
					'state' => $this->input->post('state'),
					'postal_code' => $this->input->post('postal_code'),
					'bank_account_name' => $this->input->post('bank_account_name'),
					'bank_account_no' => $this->input->post('bank_account_no'),
					'bank_ifsc' => $this->input->post('bank_ifsc'),
				]);
				flashData('errors', 'Profile updated successfully.');
			}
			redirect('vendor/profile');
			return;
		}

		$data['title'] = 'My Profile';
		$data['vendor'] = $this->CommonModel->getSingleRowById('vendor', ['vendor_id' => $vendorId]);
		$this->load->view('vendor/profile', $data);
	}
}
