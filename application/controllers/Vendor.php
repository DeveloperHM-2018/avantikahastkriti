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
					'quantity_supplied' => $record['quantity_supplied'],
					'status' => statusView($statusColors[$record['status']], $statusLabels[$record['status']]),
					'admin_notes' => $record['admin_notes'] ?: '-',
					'action' => '<a href="' . base_url('vendor/productAdd?id=' . encryptId($record['vendor_product_id'])) . '" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>',
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
			['data' => 'quantity_supplied'],
			['data' => 'status'],
			['data' => 'admin_notes'],
			['data' => 'action'],
		];
		$data['ajax_table'] = 'vendor/products';
		$data['table_column'] = json_encode($table_header);
		$data['col_stop'] = '4,5';
		$this->load->view('vendor/product_all', $data);
	}

	// Field set deliberately mirrors AdminProduct::productAdd()/product_add.php
	// (category -> sub-category -> sub-category type cascade, product type,
	// stock status, description, SEO meta, multiple images) so a vendor
	// submission and an admin-entered product collect the same information -
	// the only additions are the vendor's own supply price and quantity,
	// which have no admin-side equivalent. Images are staged into
	// tbl_vendor_product_image (same upload/product/ folder admin products
	// use) and promoted into tbl_product_image on approval - see
	// AdminVendor::vendorProductReview().
	//
	// ?id=<encrypted vendor_product_id> switches this into edit mode for an
	// existing submission. Pricing (vendor_supply_price, proposed_market_price)
	// is locked once submitted - the form renders those fields disabled and,
	// regardless of what a tampered request sends, the save below never
	// writes to them in edit mode; only a brand-new submission sets prices.
	// The actual sale price is never vendor-set at all - see
	// AdminVendor::vendorProductReview(). Editing anything else on an already-approved
	// product doesn't touch the live catalog - it resets this submission to
	// Pending so admin re-reviews the change before it goes live.
	public function productAdd()
	{
		$this->requireLogin();
		$vendorId = sessionId('vendor_id');

		$id = $this->input->get('id');
		$vendorProductId = $id ? decryptId($id) : null;
		$existing = null;
		if ($vendorProductId) {
			$existing = $this->CommonModel->getSingleRowById('vendor_product', ['vendor_product_id' => $vendorProductId]);
			// Scoped to the caller's own submissions - never trust a
			// client-supplied vendor_product_id alone (IDOR).
			if (!$existing || $existing['vendor_id'] != $vendorId) {
				show_404();
			}
		}

		if (count($_POST) > 0) {
			$this->form_validation->set_rules('product_name', 'Product Name', 'trim|required|max_length[191]');
			$this->form_validation->set_rules('category_id', 'Category', 'trim|required');
			$this->form_validation->set_rules('sub_category_id[]', 'Sub Category', 'required');
			$this->form_validation->set_rules('description', 'Description', 'trim|required');
			$this->form_validation->set_rules('quantity_supplied', 'Stock Available', 'trim|required|numeric|greater_than[0]');
			if (!$existing) {
				// Pricing is only ever collected on the original submission.
				// Sale price is never collected here at all - that's entirely
				// admin's call, calculated from commission on the review screen
				// (see AdminVendor::vendorProductReview()).
				$this->form_validation->set_rules('proposed_market_price', 'Market Price', 'trim|required|numeric|greater_than[0]');
				$this->form_validation->set_rules('vendor_supply_price', 'Your Supply Price', 'trim|required|numeric|greater_than[0]');
			}
			$this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');

			if ($this->form_validation->run()) {
				$subCategoryId = $this->input->post('sub_category_id');
				$subCategoryTypeId = $this->input->post('sub_category_type_id');

				$post = [
					'vendor_id' => $vendorId,
					'product_name' => $this->input->post('product_name'),
					'category_id' => $this->input->post('category_id'),
					'sub_category_id' => !empty($subCategoryId) ? (is_array($subCategoryId) ? implode(',', $subCategoryId) : $subCategoryId) : null,
					'sub_category_type_id' => !empty($subCategoryTypeId) ? (is_array($subCategoryTypeId) ? implode(',', $subCategoryTypeId) : $subCategoryTypeId) : null,
					'product_type' => $this->input->post('product_type') ?: 1,
					'description' => $this->input->post('description'),
					'quantity_supplied' => $this->input->post('quantity_supplied'),
					'is_out_of_stock' => $this->input->post('is_out_of_stock') ? 1 : 0,
					'meta_title' => trim((string) $this->input->post('meta_title')),
					'meta_description' => trim((string) $this->input->post('meta_description')),
					'meta_keywords' => trim((string) $this->input->post('meta_keywords')),
				];

				if ($existing) {
					// Pricing keys are deliberately absent from $post here, so
					// updateRowById() leaves vendor_supply_price/
					// proposed_market_price exactly as they were - no path in
					// edit mode ever writes to them.
					$post['status'] = 0;
					$this->CommonModel->updateRowById('vendor_product', 'vendor_product_id', $vendorProductId, $post);
					$this->CommonModel->logAdminActivity(ACTOR_TYPE_VENDOR, $vendorId, 'vendor_product_edit', 'vendor_product', $vendorProductId);
					$message = 'Product updated and resubmitted for admin review.';
				} else {
					$post['vendor_supply_price'] = $this->input->post('vendor_supply_price');
					$post['proposed_market_price'] = $this->input->post('proposed_market_price');
					$post['status'] = 0;
					$vendorProductId = $this->CommonModel->insertRowReturnId('vendor_product', $post);
					$this->CommonModel->logAdminActivity(ACTOR_TYPE_VENDOR, $vendorId, 'vendor_product_submit', 'vendor_product', $vendorProductId, null, ['product_name' => $post['product_name']]);
					$message = 'Product submitted for admin review.';
				}

				if ($vendorProductId && !empty($_FILES['image']['name'][0])) {
					$filesCount = count($_FILES['image']['name']);
					for ($i = 0; $i < $filesCount; $i++) {
						if ($_FILES['image']['name'][$i] === '' || $_FILES['image']['size'][$i] > MAX_PRODUCT_IMAGE_SIZE || $_FILES['image']['error'][$i] !== UPLOAD_ERR_OK) {
							continue;
						}
						$extension = pathinfo($_FILES['image']['name'][$i], PATHINFO_EXTENSION);
						$_FILES['files']['name'] = round(microtime(true) * 1000) . '_' . $i . '.' . $extension;
						$_FILES['files']['type'] = $_FILES['image']['type'][$i];
						$_FILES['files']['tmp_name'] = $_FILES['image']['tmp_name'][$i];
						$_FILES['files']['error'] = $_FILES['image']['error'][$i];
						$_FILES['files']['size'] = $_FILES['image']['size'][$i];

						$picture = fullImage('files', PRODUCT_IMAGE, "", MAX_PRODUCT_IMAGE_SIZE);
						if ($picture) {
							$this->CommonModel->insertRow('vendor_product_image', [
								'vendor_product_id' => $vendorProductId,
								'image_path' => $picture,
							]);
						}
					}
				}

				flashData('errors', $message);
				redirect('vendor/products');
				return;
			}
			flashData('errors', validation_errors());
		}

		$data['title'] = $existing ? 'Edit Product' : 'Submit New Product';
		$data['categories'] = $this->CommonModel->getRowByIdInOrder('category', "is_delete = '1'", 'category_name', 'ASC') ?: [];
		$data['vp'] = $existing;
		$data['id'] = $id;
		$data['sub_categories'] = [];
		$data['sub_category_type_options'] = [];
		$data['selected_sub_category_id'] = [];
		$data['selected_sub_category_type_id'] = [];
		if ($existing) {
			$data['selected_sub_category_id'] = !empty($existing['sub_category_id']) ? explode(',', $existing['sub_category_id']) : [];
			$data['selected_sub_category_type_id'] = !empty($existing['sub_category_type_id']) ? explode(',', $existing['sub_category_type_id']) : [];
			$data['sub_categories'] = $this->CommonModel->getRowByIdInOrder('sub_category', ['category_id' => $existing['category_id'], 'is_delete' => '1'], 'sub_category_name', 'ASC') ?: [];
			if ($data['selected_sub_category_id']) {
				$data['sub_category_type_options'] = $this->CommonModel->getRowByWhereIn('sub_category_type', 'sub_category_id', $data['selected_sub_category_id']) ?: [];
			}
			$data['images'] = $this->CommonModel->getRowByMoreId('vendor_product_image', ['vendor_product_id' => $vendorProductId]) ?: [];
		}
		$this->load->view('vendor/product_add', $data);
	}

	// Delete one of the vendor's own staged product images (before or after
	// submission). Scoped through a join back to vendor_product so a vendor
	// can never delete another vendor's image by guessing an id.
	public function productImageDelete()
	{
		$this->requireLogin();
		$vendorId = sessionId('vendor_id');
		$imageId = decryptId($this->input->post('id'));

		$image = $this->CommonModel->getRowWithMultiJoin(
			'vendor_product_image.*',
			'vendor_product_image',
			"vendor_product_image.vendor_product_image_id = '$imageId' AND vendor_product.vendor_id = '$vendorId'",
			[['vendor_product', 'vendor_product.vendor_product_id = vendor_product_image.vendor_product_id']],
			'',
			'',
			2
		);
		if (!$image) {
			echo json_encode(['status' => false, 'message' => 'Image not found.']);
			return;
		}

		$path = FCPATH . PRODUCT_IMAGE . $image['image_path'];
		if (file_exists($path)) {
			unlink($path);
		}
		$this->CommonModel->deleteRowById('vendor_product_image', ['vendor_product_image_id' => $imageId]);
		echo json_encode(['status' => true, 'message' => 'Image deleted.']);
	}

	// Mirrors AdminProduct::getSubCategory()/getSubCategoryType() so the
	// vendor submission form's category cascade works identically - kept as
	// its own vendor-session-gated copy rather than reusing AdminProduct's
	// endpoints directly, since those require an admin session a vendor
	// never has. Reuses the exact same fragment views (plain <option> lists,
	// not admin-specific markup).
	public function getSubCategory()
	{
		$this->requireLogin();
		$data['type'] = 1;
		$data['all_data'] = $this->CommonModel->getRowByIdInOrder('sub_category', ['category_id' => $this->input->post('category_id'), 'is_delete' => '1'], 'sub_category_name', 'ASC');
		$this->load->view('admin/product/sub_category_list', $data);
	}

	public function getSubCategoryType()
	{
		$this->requireLogin();
		$subCategoryId = $this->input->post('sub_category_id');
		if (is_array($subCategoryId)) {
			$data['all_data'] = $this->CommonModel->getRowByWhereIn('sub_category_type', 'sub_category_id', $subCategoryId);
		} else {
			$data['all_data'] = $this->CommonModel->getRowByIdInOrder('sub_category_type', ['sub_category_id' => $subCategoryId, 'is_delete' => '1'], 'sub_category_type_name', 'ASC');
		}
		$this->load->view('admin/product/sub_category_type_list', $data);
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
					// Warehouse/pickup address (used for Shiprocket order sync -
					// see AdminHome::shiprocketOrderDetails()). May differ from
					// the business address above. The exact Shiprocket pickup
					// nickname is admin-only (set from Vendors > Edit Vendor)
					// since only admin has access to register it there.
					'pickup_address' => $this->input->post('pickup_address'),
					'pickup_city' => $this->input->post('pickup_city'),
					'pickup_state' => $this->input->post('pickup_state'),
					'pickup_pincode' => $this->input->post('pickup_pincode'),
					'pickup_phone' => $this->input->post('pickup_phone'),
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
