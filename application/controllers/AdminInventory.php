<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Inventory admin: live stock view/adjustment, low-stock/out-of-stock queues,
// per-product stock history (ledger), and the "pinned" order review queue
// created by CommonModel::decrementOrderStock() when a prepaid order's
// payment was confirmed after stock had already run out (see
// CommonModel::applyStockDecrement()'s oversold_pinned path).
class AdminInventory extends CI_Controller
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
		return @PREV['inventory_view'] == 1 || USER_TYPE == '1';
	}

	private function canAdjust()
	{
		return @PREV['inventory_adjust'] == 1 || USER_TYPE == '1';
	}

	public function stockAll()
	{
		if (!$this->canView()) {
			show_404();
		}

		if (count($_POST) > 0) {
			$postData = $this->input->post();
			$searchValue = $this->db->escape_like_str($postData['search']['value']);

			$searchQuery = " AND product.is_delete = '1'";
			if ($searchValue != '') {
				$searchQuery .= " AND product.product_name LIKE '%" . $searchValue . "%'";
			}
			$stockFilter = $this->input->post('searchByStockStatus');
			if ($stockFilter === '1') {
				$searchQuery .= " AND product.is_out_of_stock = '1'";
			} elseif ($stockFilter === '0') {
				$searchQuery .= " AND product.is_out_of_stock = '0'";
			}

			$select = "product.product_id, product.product_name, product.quantity, product.quantity_type, product.low_stock_threshold, product.is_out_of_stock, product.is_out_of_stock_override, category.category_name";
			$join = [
				['category', 'category.category_id = product.category_id', 'LEFT'],
			];
			$allData = $this->CommonModel->getAjaxDataWithJoin($select, 'product', $searchQuery, $postData, $join);

			$data = [];
			foreach ($allData['records'] as $record) {
				$id = encryptId($record['product_id']);
				$lowStock = $record['low_stock_threshold'] !== null && (float) $record['quantity'] <= (float) $record['low_stock_threshold'];

				if ($record['is_out_of_stock'] == 1) {
					$status = statusView('danger', 'Out of Stock');
				} elseif ($lowStock) {
					$status = statusView('warning', 'Low Stock');
				} else {
					$status = statusView('success', 'In Stock');
				}
				if ($record['is_out_of_stock_override'] !== null) {
					$status .= ' <span class="badge bg-secondary" title="Manually overridden - not following live quantity">Override</span>';
				}

				$actions = '<a href="' . base_url('stockAdjust?id=' . $id) . '" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Adjust</a> ';
				$actions .= '<a href="' . base_url('stockLedger?id=' . $id) . '" class="btn btn-info btn-sm"><i class="fa fa-history"></i> History</a>';
				if ($record['is_out_of_stock_override'] !== null && $this->canAdjust()) {
					$actions .= ' <a href="' . base_url('stockClearOverride/' . $id) . '" class="btn btn-secondary btn-sm confirm_data" title="Clear override" title-text="Follow live quantity again?" icon="warning"><i class="fa fa-undo"></i> Clear Override</a>';
				}

				$data[] = [
					'product_name' => $record['product_name'],
					'category_name' => $record['category_name'],
					'quantity' => $record['quantity'] . ' ' . $record['quantity_type'],
					'low_stock_threshold' => $record['low_stock_threshold'] !== null ? $record['low_stock_threshold'] : '-',
					'status' => $status,
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

		$data['title'] = 'Inventory - Stock';
		$table_header = [
			['data' => 'product_name'],
			['data' => 'category_name'],
			['data' => 'quantity'],
			['data' => 'low_stock_threshold'],
			['data' => 'status'],
			['data' => 'action'],
		];
		$data['ajax_table'] = 'stockAll';
		$data['table_column'] = json_encode($table_header);
		$data['col_stop'] = '5';
		$this->load->view('admin/inventory/stock_all', $data);
	}

	public function lowStockAll()
	{
		if (!$this->canView()) {
			show_404();
		}

		if (count($_POST) > 0) {
			$postData = $this->input->post();
			$searchValue = $this->db->escape_like_str($postData['search']['value']);
			$searchQuery = " AND product.is_delete = '1' AND product.low_stock_threshold IS NOT NULL AND product.quantity <= product.low_stock_threshold";
			if ($searchValue != '') {
				$searchQuery .= " AND product.product_name LIKE '%" . $searchValue . "%'";
			}

			$select = "product.product_id, product.product_name, product.quantity, product.quantity_type, product.low_stock_threshold";
			$allData = $this->CommonModel->getAjaxDataWithJoin($select, 'product', $searchQuery, $postData, []);

			$data = [];
			foreach ($allData['records'] as $record) {
				$id = encryptId($record['product_id']);
				$data[] = [
					'product_name' => $record['product_name'],
					'quantity' => $record['quantity'] . ' ' . $record['quantity_type'],
					'low_stock_threshold' => $record['low_stock_threshold'],
					'action' => '<a href="' . base_url('stockAdjust?id=' . $id) . '" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Restock</a>',
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

		$data['title'] = 'Inventory - Low Stock';
		$table_header = [
			['data' => 'product_name'],
			['data' => 'quantity'],
			['data' => 'low_stock_threshold'],
			['data' => 'action'],
		];
		$data['ajax_table'] = 'lowStockAll';
		$data['table_column'] = json_encode($table_header);
		$data['col_stop'] = '3';
		$this->load->view('admin/inventory/low_stock_all', $data);
	}

	public function outOfStockAll()
	{
		if (!$this->canView()) {
			show_404();
		}

		if (count($_POST) > 0) {
			$postData = $this->input->post();
			$searchValue = $this->db->escape_like_str($postData['search']['value']);
			$searchQuery = " AND product.is_delete = '1' AND product.is_out_of_stock = '1'";
			if ($searchValue != '') {
				$searchQuery .= " AND product.product_name LIKE '%" . $searchValue . "%'";
			}

			$select = "product.product_id, product.product_name, product.quantity, product.quantity_type, product.is_out_of_stock_override";
			$allData = $this->CommonModel->getAjaxDataWithJoin($select, 'product', $searchQuery, $postData, []);

			$data = [];
			foreach ($allData['records'] as $record) {
				$id = encryptId($record['product_id']);
				$data[] = [
					'product_name' => $record['product_name'],
					'quantity' => $record['quantity'] . ' ' . $record['quantity_type'],
					'reason' => $record['is_out_of_stock_override'] !== null ? 'Manual override' : 'Quantity depleted',
					'action' => '<a href="' . base_url('stockAdjust?id=' . $id) . '" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Restock</a>',
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

		$data['title'] = 'Inventory - Out of Stock';
		$table_header = [
			['data' => 'product_name'],
			['data' => 'quantity'],
			['data' => 'reason'],
			['data' => 'action'],
		];
		$data['ajax_table'] = 'outOfStockAll';
		$data['table_column'] = json_encode($table_header);
		$data['col_stop'] = '3';
		$this->load->view('admin/inventory/out_of_stock_all', $data);
	}

	// GET ?id=<encrypted product_id> shows the form; POST saves the adjustment.
	// Always routed through CommonModel::applyStockDecrement()/applyStockRestock()
	// so a manual adjustment gets the exact same row-lock + ledger entry as
	// every other stock mutation in the system - there is no separate "just
	// UPDATE the quantity column" path anywhere in the admin panel.
	public function stockAdjust()
	{
		if (!$this->canView()) {
			show_404();
		}

		if (count($_POST) > 0) {
			if (!$this->canAdjust()) {
				flashData('errors', 'You do not have permission to adjust stock.');
				redirect($_SERVER['HTTP_REFERER']);
			}

			$this->form_validation->set_rules('product_id', 'Product', 'required');
			$this->form_validation->set_rules('direction', 'Direction', 'required|in_list[increase,decrease]');
			$this->form_validation->set_rules('adjust_quantity', 'Quantity', 'required|numeric|greater_than[0]');
			$this->form_validation->set_rules('reason', 'Reason', 'required|max_length[255]');
			$this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');

			if (!$this->form_validation->run()) {
				flashData('errors', validation_errors());
				redirect($_SERVER['HTTP_REFERER']);
			}

			$productId = decryptId($this->input->post('product_id'));
			$variantId = $this->input->post('variant_id') ? decryptId($this->input->post('variant_id')) : null;
			$qty = (float) $this->input->post('adjust_quantity');
			$reason = trim($this->input->post('reason'));
			$context = [
				'change_type' => 'manual_adjustment',
				'reference_type' => 'manual',
				'reference_id' => null,
				'changed_by_type' => ACTOR_TYPE_ADMIN,
				'changed_by_id' => sessionId('admin_id'),
				'note' => $reason,
			];

			if ($this->input->post('direction') === 'increase') {
				$this->CommonModel->applyStockRestock($productId, $variantId, $qty, $context);
			} else {
				$result = $this->CommonModel->applyStockDecrement($productId, $variantId, $qty, $context);
				if (!$result['success']) {
					flashData('errors', 'Cannot decrease stock below zero - only ' . $result['balance_after'] . ' available.');
					redirect($_SERVER['HTTP_REFERER']);
				}
			}

			$this->CommonModel->logAdminActivity(ACTOR_TYPE_ADMIN, sessionId('admin_id'), 'stock_adjust', 'product', $productId, null, [
				'direction' => $this->input->post('direction'),
				'quantity' => $qty,
				'variant_id' => $variantId,
				'reason' => $reason,
			]);

			flashData('errors', 'Stock adjusted successfully.');
			redirect(base_url('stockAdjust?id=' . $this->input->post('product_id')));
			return;
		}

		$id = $this->input->get('id');
		if (!$id) {
			show_404();
		}
		$productId = decryptId($id);
		$product = $this->CommonModel->getSingleRowById('product', ['product_id' => $productId]);
		if (!$product) {
			show_404();
		}

		$data['title'] = 'Adjust Stock - ' . $product['product_name'];
		$data['product'] = $product;
		$data['id'] = $id;
		$data['variants'] = $this->CommonModel->getRowByMoreId('product_variants', ['product_id' => $productId]) ?: [];
		$this->load->view('admin/inventory/stock_adjust', $data);
	}

	public function stockClearOverride($id)
	{
		if (!$this->canAdjust()) {
			show_404();
		}
		$productId = decryptId($id);
		$product = $this->CommonModel->getSingleRowById('product', ['product_id' => $productId]);
		if ($product) {
			$this->CommonModel->updateRowByIdWithOutXss('product', "product_id = '$productId'", [
				'is_out_of_stock_override' => null,
				'is_out_of_stock' => ((float) $product['quantity'] <= 0) ? 1 : 0,
			]);
			$this->CommonModel->logAdminActivity(ACTOR_TYPE_ADMIN, sessionId('admin_id'), 'product_stock_override_clear', 'product', $productId);
			flashData('errors', 'Override cleared - stock status now follows live quantity.');
		}
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function stockLedger()
	{
		if (!$this->canView()) {
			show_404();
		}
		$id = $this->input->get('id');
		if (!$id) {
			show_404();
		}
		$productId = decryptId($id);
		$product = $this->CommonModel->getSingleRowById('product', ['product_id' => $productId]);
		if (!$product) {
			show_404();
		}

		$data['title'] = 'Stock History - ' . $product['product_name'];
		$data['product'] = $product;
		$data['entries'] = $this->CommonModel->getRowByOrderWithLimit('stock_ledger', ['product_id' => $productId], 'create_date', 'DESC', 200) ?: [];
		$this->load->view('admin/inventory/stock_ledger', $data);
	}

	// Orders CommonModel::decrementOrderStock() pinned because a prepaid
	// order's payment was confirmed after another order (COD/admin-manual,
	// which reserve stock instantly at creation) had already taken the last
	// unit. Nothing here auto-cancels anything - an admin decides whether to
	// source/restock and clear the flag, or go cancel + refund the order via
	// the existing order/return tools, then come back and clear the flag.
	public function pinnedOrders()
	{
		if (!$this->canView()) {
			show_404();
		}

		if (count($_POST) > 0) {
			$postData = $this->input->post();
			$searchQuery = " AND book_product.stock_flag = '1'";

			$select = "book_product.product_book_id, book_product.order_id, book_product.name, book_product.final_amount, book_product.stock_flag_reason, book_product.booking_date, book_product.transaction_status";
			$allData = $this->CommonModel->getAjaxDataWithJoin($select, 'book_product', $searchQuery, $postData, []);

			$data = [];
			foreach ($allData['records'] as $record) {
				$id = encryptId($record['product_book_id']);
				$data[] = [
					'order_id' => $record['order_id'],
					'name' => $record['name'],
					'final_amount' => $record['final_amount'],
					'stock_flag_reason' => $record['stock_flag_reason'],
					'booking_date' => dateConvertToView($record['booking_date'], 3),
					'action' => '<a href="' . base_url('getOrderDetails?id=' . $id) . '" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> View Order</a> '
						. '<button type="button" class="btn btn-success btn-sm resolvePinBtn" data-id="' . $id . '"><i class="fa fa-check"></i> Resolve</button>',
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

		$data['title'] = 'Pinned Orders - Stock Review';
		$table_header = [
			['data' => 'order_id'],
			['data' => 'name'],
			['data' => 'final_amount'],
			['data' => 'stock_flag_reason'],
			['data' => 'booking_date'],
			['data' => 'action'],
		];
		$data['ajax_table'] = 'pinnedOrders';
		$data['table_column'] = json_encode($table_header);
		$data['col_stop'] = '5';
		$this->load->view('admin/inventory/pinned_orders', $data);
	}

	public function resolvePinnedOrder()
	{
		if (!$this->canAdjust()) {
			echo json_encode(['status' => false, 'message' => 'You do not have permission to resolve pinned orders.']);
			return;
		}

		$id = decryptId($this->input->post('id'));
		$resolution = trim((string) $this->input->post('resolution'));
		if (!$id || $resolution === '') {
			echo json_encode(['status' => false, 'message' => 'A resolution note is required.']);
			return;
		}

		$order = $this->CommonModel->getSingleRowById('book_product', ['product_book_id' => $id]);
		if (!$order || $order['stock_flag'] != 1) {
			echo json_encode(['status' => false, 'message' => 'Order not found or not pinned.']);
			return;
		}

		$this->CommonModel->updateRowByIdWithOutXss('book_product', "product_book_id = '$id'", [
			'stock_flag' => 0,
			'stock_flag_resolved_by' => sessionId('admin_id'),
			'stock_flag_resolved_date' => date('Y-m-d H:i:s'),
			'stock_flag_resolution' => $resolution,
		]);
		$this->CommonModel->logAdminActivity(ACTOR_TYPE_ADMIN, sessionId('admin_id'), 'pinned_order_resolve', 'order', $id, null, ['resolution' => $resolution]);

		echo json_encode(['status' => true, 'message' => 'Pinned order resolved.']);
	}
}
