<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Admin-side vendor management: approve/reject/suspend vendors, review and
// approve vendor product submissions (setting per-product commission before
// a submission is promoted into the live tbl_product catalog), and process
// vendor payouts. Vendor identity is only ever shown here, never on any
// customer-facing page (see Vendor.php's own header comment).
class AdminVendor extends CI_Controller
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
	}

	private function canView()
	{
		return @PREV['vendor_view'] == 1 || USER_TYPE == '1';
	}

	private function canApproveVendor()
	{
		return @PREV['vendor_approve'] == 1 || USER_TYPE == '1';
	}

	private function canApproveProduct()
	{
		return @PREV['vendor_product_approve'] == 1 || USER_TYPE == '1';
	}

	private function canProcessPayout()
	{
		return @PREV['vendor_payout_process'] == 1 || USER_TYPE == '1';
	}

	public function vendorAll()
	{
		if (!$this->canView()) {
			show_404();
		}

		if (count($_POST) > 0) {
			$postData = $this->input->post();
			$searchValue = $this->db->escape_like_str($postData['search']['value']);
			$searchQuery = '';
			if ($searchValue != '') {
				$searchQuery .= " AND (vendor.business_name LIKE '%" . $searchValue . "%' OR vendor.contact_no LIKE '%" . $searchValue . "%' OR vendor.email_id LIKE '%" . $searchValue . "%')";
			}
			$statusFilter = $this->input->post('searchByStatus');
			if ($statusFilter !== null && $statusFilter !== '') {
				$searchQuery .= " AND vendor.status = '" . (int) $statusFilter . "'";
			}

			$allData = $this->CommonModel->getAjaxDataWithJoin('vendor.*', 'vendor', $searchQuery, $postData, []);
			$statusLabels = [0 => 'Pending', 1 => 'Active', 2 => 'Rejected', 3 => 'Suspended'];
			$statusColors = [0 => 'warning', 1 => 'success', 2 => 'danger', 3 => 'secondary'];

			$data = [];
			foreach ($allData['records'] as $record) {
				$id = encryptId($record['vendor_id']);
				$actions = '<a href="' . base_url('vendorDetails?id=' . $id) . '" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> View</a> ';
				if ($this->canApproveVendor()) {
					$actions .= '<a href="' . base_url('vendorAdd?id=' . $id) . '" class="btn btn-secondary btn-sm"><i class="fa fa-edit"></i> Edit</a> ';
					if ($record['status'] == 0) {
						$actions .= '<a href="' . base_url('vendorApprove/' . $id) . '" class="btn btn-success btn-sm confirm_data" title="Approve Vendor" title-text="Approve this vendor?" icon="question"><i class="fa fa-check"></i> Approve</a> ';
						$actions .= '<a href="' . base_url('vendorReject/' . $id) . '" class="btn btn-danger btn-sm confirm_data" title="Reject Vendor" title-text="Reject this vendor?" icon="warning"><i class="fa fa-times"></i> Reject</a>';
					} elseif ($record['status'] == 1) {
						$actions .= '<a href="' . base_url('vendorSuspend/' . $id) . '" class="btn btn-warning btn-sm confirm_data" title="Suspend Vendor" title-text="Suspend this vendor?" icon="warning"><i class="fa fa-ban"></i> Suspend</a>';
					} elseif ($record['status'] == 3) {
						$actions .= '<a href="' . base_url('vendorActivate/' . $id) . '" class="btn btn-success btn-sm confirm_data" title="Activate Vendor" title-text="Re-activate this vendor?" icon="question"><i class="fa fa-check"></i> Activate</a>';
					}
				}

				$data[] = [
					'business_name' => $record['business_name'],
					'contact_name' => $record['contact_name'] . '<br>' . $record['contact_no'],
					'email_id' => $record['email_id'],
					'create_date' => dateConvertToView($record['create_date'], 3),
					'status' => statusView($statusColors[$record['status']], $statusLabels[$record['status']]),
					'action' => $actions,
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

		$data['title'] = 'Vendors';
		$table_header = [
			['data' => 'business_name'],
			['data' => 'contact_name'],
			['data' => 'email_id'],
			['data' => 'create_date'],
			['data' => 'status'],
			['data' => 'action'],
		];
		$data['ajax_table'] = 'vendorAll';
		$data['table_column'] = json_encode($table_header);
		$data['col_stop'] = '5';
		$this->load->view('admin/vendor/vendor_all', $data);
	}

	// Admin manual vendor entry ("Add/Edit vendor manually" per the vendor
	// module spec) - a vendor created here is admin-vetted by construction,
	// so it's set Active immediately rather than going through the
	// self-registration Pending queue.
	public function vendorAdd()
	{
		if (!$this->canApproveVendor()) {
			show_404();
		}

		$id = $this->input->get('id');
		$vendorId = $id ? decryptId($id) : null;
		$vendor = $vendorId ? $this->CommonModel->getSingleRowById('vendor', ['vendor_id' => $vendorId]) : false;
		if ($id && !$vendor) {
			show_404();
		}

		if (count($_POST) > 0) {
			if ($vendorId) {
				if ($vendor['email_id'] != $this->input->post('email_id')) {
					$this->form_validation->set_rules('email_id', 'Email', 'trim|required|valid_email|is_unique[vendor.email_id]');
				}
				if ($vendor['contact_no'] != $this->input->post('contact_no')) {
					$this->form_validation->set_rules('contact_no', 'Mobile Number', 'trim|required|is_unique[vendor.contact_no]');
				}
			} else {
				$this->form_validation->set_rules('email_id', 'Email', 'trim|required|valid_email|is_unique[vendor.email_id]');
				$this->form_validation->set_rules('contact_no', 'Mobile Number', 'trim|required|is_unique[vendor.contact_no]');
				$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
			}
			$this->form_validation->set_rules('business_name', 'Business Name', 'trim|required|max_length[150]');
			$this->form_validation->set_rules('contact_name', 'Contact Person', 'trim|required|max_length[100]');
			$this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');

			if ($this->form_validation->run()) {
				$post = [
					'business_name' => $this->input->post('business_name'),
					'contact_name' => $this->input->post('contact_name'),
					'contact_no' => $this->input->post('contact_no'),
					'email_id' => $this->input->post('email_id'),
					'gst_number' => $this->input->post('gst_number'),
					'pan_number' => $this->input->post('pan_number'),
					'address' => $this->input->post('address'),
					'city' => $this->input->post('city'),
					'state' => $this->input->post('state'),
					'postal_code' => $this->input->post('postal_code'),
					'bank_account_name' => $this->input->post('bank_account_name'),
					'bank_account_no' => $this->input->post('bank_account_no'),
					'bank_ifsc' => $this->input->post('bank_ifsc'),
					'default_commission_percent' => $this->input->post('default_commission_percent') ?: null,
					'status' => (int) $this->input->post('status'),
				];
				if ($this->input->post('password') != '') {
					$post['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
				}

				if ($vendorId) {
					$this->CommonModel->updateRowById('vendor', 'vendor_id', $vendorId, $post);
					$this->CommonModel->logAdminActivity(ACTOR_TYPE_ADMIN, sessionId('admin_id'), 'vendor_manual_edit', 'vendor', $vendorId);
					flashData('errors', 'Vendor updated successfully.');
				} else {
					$post['status'] = $post['status'] ?: 1;
					if ($post['status'] == 1) {
						$post['approved_by'] = sessionId('admin_id');
						$post['approved_date'] = date('Y-m-d H:i:s');
					}
					$vendorId = $this->CommonModel->insertRowReturnId('vendor', $post);
					$this->CommonModel->logAdminActivity(ACTOR_TYPE_ADMIN, sessionId('admin_id'), 'vendor_manual_create', 'vendor', $vendorId, null, ['business_name' => $post['business_name']]);
					flashData('errors', 'Vendor created successfully.');
				}

				foreach (['gst_certificate' => 'gst_certificate', 'pan_card' => 'pan_card', 'bank_proof' => 'bank_proof'] as $field => $docType) {
					$this->CommonModel->handleVendorDocumentUpload($vendorId, $field, $docType);
				}

				redirect('vendorAll');
				return;
			}
			flashData('errors', validation_errors());
		}

		$data['title'] = $vendorId ? 'Edit Vendor' : 'Add Vendor';
		$data['vendor'] = $vendor ?: [];
		$data['id'] = $id;
		$this->load->view('admin/vendor/vendor_add', $data);
	}

	private function setVendorStatus($encId, $status, $action)
	{
		if (!$this->canApproveVendor()) {
			show_404();
		}
		$vendorId = decryptId($encId);
		$update = ['status' => $status];
		if ($status == 1) {
			$update['approved_by'] = sessionId('admin_id');
			$update['approved_date'] = date('Y-m-d H:i:s');
		}
		$this->CommonModel->updateRowById('vendor', 'vendor_id', $vendorId, $update);
		$this->CommonModel->logAdminActivity(ACTOR_TYPE_ADMIN, sessionId('admin_id'), $action, 'vendor', $vendorId);
		flashData('errors', 'Vendor updated.');
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function vendorApprove($id)
	{
		$this->setVendorStatus($id, 1, 'vendor_approve');
	}

	public function vendorReject($id)
	{
		$this->setVendorStatus($id, 2, 'vendor_reject');
	}

	public function vendorSuspend($id)
	{
		$this->setVendorStatus($id, 3, 'vendor_suspend');
	}

	public function vendorActivate($id)
	{
		$this->setVendorStatus($id, 1, 'vendor_activate');
	}

	public function vendorDetails()
	{
		if (!$this->canView()) {
			show_404();
		}
		$id = decryptId($this->input->get('id'));
		$vendor = $this->CommonModel->getSingleRowById('vendor', ['vendor_id' => $id]);
		if (!$vendor) {
			show_404();
		}

		$data['title'] = 'Vendor - ' . $vendor['business_name'];
		$data['vendor'] = $vendor;
		$data['id'] = $this->input->get('id');
		$data['documents'] = $this->CommonModel->getRowByMoreId('vendor_document', ['vendor_id' => $id]) ?: [];
		$data['products'] = $this->CommonModel->getRowByIdInOrder('vendor_product', ['vendor_id' => $id], 'create_date', 'DESC') ?: [];

		$salesRow = $this->CommonModel->runQuery(
			"SELECT COALESCE(SUM(commission_amount), 0) AS commission, COALESCE(SUM(vendor_payable_amount), 0) AS payable,
			SUM(CASE WHEN payout_status = 2 THEN vendor_payable_amount ELSE 0 END) AS paid
			FROM tbl_vendor_order_item WHERE vendor_id = " . (int) $id,
			2
		);
		$data['total_commission'] = $salesRow ? (float) $salesRow['commission'] : 0;
		$data['total_payable'] = $salesRow ? (float) $salesRow['payable'] : 0;
		$data['total_paid'] = $salesRow ? (float) ($salesRow['paid'] ?: 0) : 0;
		$data['payouts'] = $this->CommonModel->getRowByIdInOrder('vendor_payout', ['vendor_id' => $id], 'create_date', 'DESC') ?: [];

		$this->load->view('admin/vendor/vendor_details', $data);
	}

	// Serves a vendor KYC document only after an authenticated admin-session
	// check - documents live under application/vendor_documents/, which
	// application/.htaccess already fully denies direct web access to, so
	// this controller action is the only way to ever retrieve one.
	public function viewDocument($documentId)
	{
		if (!$this->canView()) {
			show_404();
		}
		$doc = $this->CommonModel->getSingleRowById('vendor_document', ['document_id' => decryptId($documentId)]);
		if (!$doc) {
			show_404();
		}
		$fullPath = VENDOR_DOCUMENT_PATH . $doc['file_path'];
		if (!is_file($fullPath)) {
			show_404();
		}
		$mime = mime_content_type($fullPath);
		header('Content-Type: ' . $mime);
		header('Content-Disposition: inline; filename="' . basename($fullPath) . '"');
		header('X-Content-Type-Options: nosniff');
		readfile($fullPath);
		exit();
	}

	public function vendorProductQueue()
	{
		if (!$this->canView()) {
			show_404();
		}

		if (count($_POST) > 0) {
			$postData = $this->input->post();
			$searchQuery = " AND vendor_product.status = '0'";

			$select = "vendor_product.*, vendor.business_name";
			$join = [['vendor', 'vendor.vendor_id = vendor_product.vendor_id', 'LEFT']];
			$allData = $this->CommonModel->getAjaxDataWithJoin($select, 'vendor_product', $searchQuery, $postData, $join);

			$data = [];
			foreach ($allData['records'] as $record) {
				$id = encryptId($record['vendor_product_id']);
				$action = '<a href="' . base_url('vendorProductReview?id=' . $id) . '" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> Review</a>';
				$data[] = [
					'business_name' => $record['business_name'],
					'product_name' => $record['product_name'],
					'vendor_supply_price' => $record['vendor_supply_price'],
					'quantity_supplied' => $record['quantity_supplied'],
					'create_date' => dateConvertToView($record['create_date'], 3),
					'action' => $action,
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

		$data['title'] = 'Vendor Product Approvals';
		$table_header = [
			['data' => 'business_name'],
			['data' => 'product_name'],
			['data' => 'vendor_supply_price'],
			['data' => 'quantity_supplied'],
			['data' => 'create_date'],
			['data' => 'action'],
		];
		$data['ajax_table'] = 'vendorProductQueue';
		$data['table_column'] = json_encode($table_header);
		$data['col_stop'] = '5';
		$this->load->view('admin/vendor/vendor_product_queue', $data);
	}

	public function vendorProductReview()
	{
		if (!$this->canView()) {
			show_404();
		}
		$id = decryptId($this->input->get('id'));
		$vp = $this->CommonModel->getSingleRowById('vendor_product', ['vendor_product_id' => $id]);
		if (!$vp) {
			show_404();
		}

		if (count($_POST) > 0) {
			if (!$this->canApproveProduct()) {
				flashData('errors', 'You do not have permission to approve vendor products.');
				redirect($_SERVER['HTTP_REFERER']);
			}

			$action = $this->input->post('decision');
			if ($action === 'approve') {
				$this->form_validation->set_rules('commission_percent', 'Commission %', 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]');
				$this->form_validation->set_rules('sale_price', 'Sale Price', 'required|numeric|greater_than[0]');
				$this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
				if (!$this->form_validation->run()) {
					flashData('errors', validation_errors());
					redirect($_SERVER['HTTP_REFERER']);
					return;
				}

				$commissionPercent = $this->input->post('commission_percent');
				$salePrice = $this->input->post('sale_price');

				// Promote into the live catalog. Editable by admin before
				// publishing, per the spec - admin_notes/price/category here
				// can differ from what the vendor originally proposed.
				$productId = $vp['product_id'];
				$productData = [
					'product_name' => $vp['product_name'],
					'category_id' => $vp['category_id'] ?: 1,
					'description' => $vp['description'] ?: $vp['product_name'],
					'product_type' => 1,
					'market_price' => $salePrice,
					'sale_price' => $salePrice,
					'max_quantity' => $vp['quantity_supplied'],
					'quantity' => $vp['quantity_supplied'],
					'quantity_type' => 'pcs',
					'is_delete' => 1,
					'status' => 1,
					'is_out_of_stock' => $vp['quantity_supplied'] > 0 ? 0 : 1,
					'default_vendor_id' => $vp['vendor_id'],
				];

				if ($productId) {
					// Re-supply of an already-linked product: add to existing
					// stock through the normal ledger-writing path instead of
					// overwriting the quantity column directly.
					$this->CommonModel->updateRowById('product', 'product_id', $productId, ['default_vendor_id' => $vp['vendor_id']]);
					$this->CommonModel->applyStockRestock($productId, null, $vp['quantity_supplied'], [
						'change_type' => 'vendor_supply_in',
						'reference_type' => 'vendor_product',
						'reference_id' => $vp['vendor_product_id'],
						'vendor_id' => $vp['vendor_id'],
						'changed_by_type' => ACTOR_TYPE_ADMIN,
						'changed_by_id' => sessionId('admin_id'),
					]);
				} else {
					// Opening quantity is already set on $productData above -
					// this only records the ledger entry, it must not add to
					// the quantity a second time.
					$productId = $this->CommonModel->insertRowReturnId('product', $productData);
					$this->CommonModel->recordInitialStockLedger($productId, null, $vp['quantity_supplied'], 'vendor_supply_in', [
						'reference_type' => 'vendor_product',
						'reference_id' => $vp['vendor_product_id'],
						'vendor_id' => $vp['vendor_id'],
						'changed_by_type' => ACTOR_TYPE_ADMIN,
						'changed_by_id' => sessionId('admin_id'),
					]);
				}

				$this->CommonModel->updateRowById('vendor_product', 'vendor_product_id', $id, [
					'product_id' => $productId,
					'commission_percent' => $commissionPercent,
					'proposed_sale_price' => $salePrice,
					'status' => 1,
					'admin_notes' => $this->input->post('admin_notes'),
					'reviewed_by' => sessionId('admin_id'),
					'reviewed_date' => date('Y-m-d H:i:s'),
				]);
				$this->CommonModel->logAdminActivity(ACTOR_TYPE_ADMIN, sessionId('admin_id'), 'vendor_product_approve', 'vendor_product', $id, null, ['commission_percent' => $commissionPercent, 'sale_price' => $salePrice]);
				flashData('errors', 'Vendor product approved and published.');
			} else {
				$this->CommonModel->updateRowById('vendor_product', 'vendor_product_id', $id, [
					'status' => 2,
					'admin_notes' => $this->input->post('admin_notes'),
					'reviewed_by' => sessionId('admin_id'),
					'reviewed_date' => date('Y-m-d H:i:s'),
				]);
				$this->CommonModel->logAdminActivity(ACTOR_TYPE_ADMIN, sessionId('admin_id'), 'vendor_product_reject', 'vendor_product', $id, null, ['admin_notes' => $this->input->post('admin_notes')]);
				flashData('errors', 'Vendor product rejected.');
			}
			redirect('vendorProductQueue');
			return;
		}

		$data['title'] = 'Review Vendor Product';
		$data['vp'] = $vp;
		$data['id'] = $this->input->get('id');
		$data['vendor'] = $this->CommonModel->getSingleRowById('vendor', ['vendor_id' => $vp['vendor_id']]);
		$data['can_approve'] = $this->canApproveProduct();
		$this->load->view('admin/vendor/vendor_product_review', $data);
	}

	// Vendor-wise unpaid balance, with a "Create Payout" action that snapshots
	// every currently-unpaid line into one tbl_vendor_payout batch.
	public function vendorPayoutAll()
	{
		if (!$this->canView()) {
			show_404();
		}

		$data['title'] = 'Vendor Payouts';
		$data['unpaid_summary'] = $this->CommonModel->runQuery(
			"SELECT voi.vendor_id, vendor.business_name, SUM(voi.vendor_payable_amount) AS unpaid_amount, COUNT(*) AS unpaid_lines
			FROM tbl_vendor_order_item voi
			LEFT JOIN tbl_vendor vendor ON vendor.vendor_id = voi.vendor_id
			WHERE voi.payout_status = 0
			GROUP BY voi.vendor_id
			HAVING unpaid_amount > 0
			ORDER BY unpaid_amount DESC",
			1
		) ?: [];
		$data['recent_payouts'] = $this->CommonModel->runQuery(
			"SELECT vp.*, vendor.business_name FROM tbl_vendor_payout vp
			LEFT JOIN tbl_vendor vendor ON vendor.vendor_id = vp.vendor_id
			ORDER BY vp.create_date DESC LIMIT 50",
			1
		) ?: [];
		$this->load->view('admin/vendor/vendor_payout_all', $data);
	}

	public function vendorPayoutCreate()
	{
		if (!$this->canProcessPayout()) {
			echo json_encode(['status' => false, 'message' => 'You do not have permission to process payouts.']);
			return;
		}
		$vendorId = decryptId($this->input->post('vendor_id'));
		if (!$vendorId) {
			echo json_encode(['status' => false, 'message' => 'Invalid vendor.']);
			return;
		}

		$this->db->trans_start();
		$row = $this->CommonModel->runQuery(
			"SELECT SUM(vendor_payable_amount) AS total, MIN(create_date) AS from_date, MAX(create_date) AS to_date
			FROM tbl_vendor_order_item WHERE vendor_id = " . (int) $vendorId . " AND payout_status = 0",
			2
		);
		if (!$row || (float) $row['total'] <= 0) {
			$this->db->trans_complete();
			echo json_encode(['status' => false, 'message' => 'Nothing unpaid for this vendor.']);
			return;
		}

		$payoutId = $this->CommonModel->insertRowReturnId('vendor_payout', [
			'vendor_id' => $vendorId,
			'period_from' => date('Y-m-d', strtotime($row['from_date'])),
			'period_to' => date('Y-m-d', strtotime($row['to_date'])),
			'total_amount' => $row['total'],
			'status' => 0,
		]);
		$this->db->set(['payout_status' => 1, 'payout_id' => $payoutId])
			->where('vendor_id', $vendorId)
			->where('payout_status', 0)
			->update('vendor_order_item');
		$this->CommonModel->logAdminActivity(ACTOR_TYPE_ADMIN, sessionId('admin_id'), 'vendor_payout_create', 'vendor_payout', $payoutId, null, ['vendor_id' => $vendorId, 'total_amount' => $row['total']]);
		$this->db->trans_complete();

		echo json_encode(['status' => true, 'message' => 'Payout batch created for ₹' . $row['total'] . '.']);
	}

	public function vendorPayoutMarkPaid()
	{
		if (!$this->canProcessPayout()) {
			echo json_encode(['status' => false, 'message' => 'You do not have permission to process payouts.']);
			return;
		}
		$payoutId = decryptId($this->input->post('id'));
		$reference = trim((string) $this->input->post('payment_reference'));
		if (!$payoutId || $reference === '') {
			echo json_encode(['status' => false, 'message' => 'A payment reference is required.']);
			return;
		}

		$this->db->trans_start();
		$this->CommonModel->updateRowById('vendor_payout', 'payout_id', $payoutId, [
			'status' => 1,
			'payment_reference' => $reference,
			'processed_by' => sessionId('admin_id'),
			'processed_date' => date('Y-m-d H:i:s'),
		]);
		$this->db->set('payout_status', 2)->where('payout_id', $payoutId)->update('vendor_order_item');
		$this->db->trans_complete();
		$this->CommonModel->logAdminActivity(ACTOR_TYPE_ADMIN, sessionId('admin_id'), 'vendor_payout_paid', 'vendor_payout', $payoutId, null, ['payment_reference' => $reference]);

		echo json_encode(['status' => true, 'message' => 'Payout marked as paid.']);
	}
}
