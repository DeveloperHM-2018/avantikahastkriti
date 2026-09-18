<?php

class CommonModel extends CI_Model
{
	public function insertRow($table, $post)
	{
		$post['create_date'] = setDateTime();
		$clean_post = $this->security->xss_clean($post);
		return $this->db->insert($table, $clean_post);
	}

	public function insertRowWithXSS($table, $post)
	{
		$post['create_date'] = setDateTime();
		return $this->db->insert($table, $post);
	}

	function insertRowReturnId($table, $post)
	{
		$post['create_date'] = setDateTime();
		$clean_post = $this->security->xss_clean($post);
		$this->db->insert($table, $clean_post);
		return $this->db->insert_id();
	}

	function insertRowReturnIdWithClean($table, $post)
	{
		$post['create_date'] = setDateTime();
		$this->db->insert($table, $post);
		return $this->db->insert_id();
	}

	function insertRowInBatch($table, $post)
	{
		$clean_post = $this->security->xss_clean($post);
		return $this->db->insert_batch($table, $clean_post);
	}

	function updateRowById($table, $column, $id, $data)
	{
		$clean_post = $this->security->xss_clean($data);
		$clean_post['update_date'] = setDateTime();
		$this->db->set($clean_post)
			->where($column, $id)
			->update($table);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	function updateRowByMoreId($table, $where, $data)
	{
		$clean_post = $this->security->xss_clean($data);
		$clean_post['update_date'] = setDateTime();
		$this->db->set($clean_post)
			->where($where)
			->update($table);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	function updateRowByIdWithOutXss($table, $where, $data)
	{
		$this->db->set($data)
			->where($where)
			->update($table);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function getAllRows($table)
	{
		$get = $this->db->select()
			->from($table)
			->get();
		if ($get->num_rows() > 0) {
			return $get->result_array();
		} else {
			return false;
		}
	}

	public function getAllRowsInOrder($table, $orderColumn, $orderType)
	{
		$get = $this->db->select()
			->from($table)
			->order_by($orderColumn, $orderType)
			->get();
		if ($get->num_rows() > 0) {
			return $get->result_array();
		} else {
			return false;
		}
	}


	public function getRowById($table, $column, $id)
	{
		$get = $this->db->select()
			->from($table)
			->where($column, $id)
			->get();
		if ($get->num_rows() > 0) {
			return $get->result_array();
		} else {
			return false;
		}
	}

	public function getRowByIdInOrder($table, $where, $orderColumn, $orderType)
	{
		$get = $this->db->select()
			->from($table)
			->where($where)
			->order_by($orderColumn, $orderType)
			->get();
		if ($get->num_rows() > 0) {
			return $get->result_array();
		} else {
			return false;
		}
	}
	public function getAllRowsInOrderWithLimit($table, $limit, $orderColumn, $orderType)
	{
		$get = $this->db->select()
			->from($table)
			->limit($limit)
			->order_by($orderColumn, $orderType)
			->get();
		if ($get->num_rows() > 0) {
			return $get->result_array();
		} else {
			return false;
		}
	}
	public function getRowByIdInMultiOrder($table, $where, $order)
	{
		$get = $this->db->select()
			->from($table)
			->where($where)
			->order_by($order)
			->get();
		if ($get->num_rows() > 0) {
			return $get->result_array();
		} else {
			return false;
		}
	}

	public function getRowByMoreId($table, $where)
	{
		$get = $this->db->select()
			->from($table)
			->where($where)
			->get();
		if ($get->num_rows() > 0) {
			return $get->result_array();
		} else {
			return false;
		}
	}

	public function getSingleRowById($table, $where)
	{
		$get = $this->db->select()
			->from($table)
			->where($where)
			->get();
		if ($get->num_rows() > 0) {
			return $get->row_array();
		} else {
			return false;
		}
	}

	public function deleteRowById($table, $where)
	{
		return $this->db->where($where)->delete($table);
	}

	public function getNumRows($table, $where)
	{
		$ci = &get_instance();
		$get = $ci->db->select()
			->from($table)
			->where($where)
			->get();
		return $get->num_rows();
	}

	public function getColumnById($selectColumn, $table, $where)
	{
		$get = $this->db->select($selectColumn)
			->from($table)
			->where($where)
			->get();
		if ($get->num_rows() > 0) {
			return $get->row_array();
		} else {
			return false;
		}
	}

	public function getRowByLikeInOrder($table, $where, $like, $name, $orderBy, $orderType)
	{
		$ci = &get_instance();
		$get = $ci->db->select()
			->from($table)
			->where($where)
			->like($like, $name, 'both')
			->order_by($orderBy, $orderType)
			->get();
		if ($get->num_rows() > 0) {
			return $get->result_array();
		} else {
			return false;
		}
	}

	public function getRowByLikeInOrderWithLimit($table, $where, $like, $name, $orderBy, $orderType, $limit)
	{
		$ci = &get_instance();
		$get = $ci->db->select()
			->from($table)
			->where($where)
			->like($like, $name, 'both')
			->order_by($orderBy, $orderType)
			->limit($limit)
			->get();
		if ($get->num_rows() > 0) {
			return $get->result_array();
		} else {
			return false;
		}
	}
	public function getRowByOrderWithLimit($table, $where,  $orderBy, $orderType, $limit)
	{
		$ci = &get_instance();
		$get = $ci->db->select()
			->from($table)
			->where($where)

			->order_by($orderBy, $orderType)
			->limit($limit)
			->get();
		if ($get->num_rows() > 0) {
			return $get->result_array();
		} else {
			return false;
		}
	}

	public function getRowByWhereIn($table, $column, $value)
	{
		$get = $this->db->select()
			->from($table)
			->where_in($column, $value)
			->get();
		if ($get->num_rows() > 0) {
			return $get->result_array();
		} else {
			return false;
		}
	}

	public function getSingleRowByIdInOrder($table, $where, $orderColumn, $orderBy)
	{
		$get = $this->db->select()
			->from($table)
			->where($where)
			->order_by($orderColumn, $orderBy)
			->get();
		if ($get->num_rows() > 0) {
			return $get->row_array();
		} else {
			return false;
		}
	}

	public function runQuery($query, $type = 1)
	{
		$query = $this->db->query($query);
		if ($query->num_rows() > 0) {
			return $type == 1 ? $query->result_array() : $query->row_array();
		} else {
			return false;
		}
	}

	public function getRowWithMultiJoin($select, $table, $searchQuery, $join_multi_array, $order_column = "", $order = "", $type = 1)
	{
		$get = $this->db->select($select);
		$this->db->from($table);
		if (count($join_multi_array) != 0 && is_array($join_multi_array)) {
			for ($i = 0; $i <  count($join_multi_array); $i++) {
				if (count($join_multi_array[$i]) == 2) {
					$this->db->join($join_multi_array[$i][0], $join_multi_array[$i][1]);
				} elseif (count($join_multi_array[$i]) == 3) {
					$this->db->join($join_multi_array[$i][0], $join_multi_array[$i][1], $join_multi_array[$i][2]);
				}
			}
		}
		if ($searchQuery != "") {
			$this->db->where($searchQuery);
		}
		if ($order_column != "") {
			$this->db->order_by($order_column, $order);
		}
		$get = $this->db->get();
		if ($get->num_rows() > 0) {
			return $type == 1 ? $get->result_array() : $get->row_array();
		} else {
			return false;
		}
	}

	public function getRowByIdfield($table, $column, $id, $field)
	{
		$get = $this->db->select($field)
			->from($table)
			->where($column, $id)
			->get();
		if ($get->num_rows() > 0) {
			return $get->result_array();
		} else {
			return false;
		}
	}

	public function getRowByOr($table, $where, $or_where)
	{
		$get = $this->db->select()
			->from($table)
			->group_start()
			->where($where)
			->or_where($or_where)
			->group_end()
			->get();
		if ($get->num_rows() > 0) {
			return $get->result_array();
		} else {
			return false;
		}
	}

	function getAjaxDataWithJoin($select, $table, $searchQuery, $postData, $join_multi_array = null, $group_by = "")
	{
		$start = $postData['start'];
		$rowperpage = $postData['length'];
		$columnIndex = $postData['order'][0]['column'];
		$columnName = $postData['columns'][$columnIndex]['data'];
		$columnSortOrder = $postData['order'][0]['dir'] == 'asc' ? 'DESC' : 'ASC';
		## Total number of record with filtering
		$this->db->select('count(*) as allcount');
		if ($searchQuery != '') {
			$this->db->where("1 " . $searchQuery);
		}
		if (count($join_multi_array) != 0 && is_array($join_multi_array)) {
			for ($i = 0; $i <  count($join_multi_array); $i++) {
				if (count($join_multi_array[$i]) == 2) {
					$this->db->join($join_multi_array[$i][0], $join_multi_array[$i][1]);
				} elseif (count($join_multi_array[$i]) == 3) {
					$this->db->join($join_multi_array[$i][0], $join_multi_array[$i][1], $join_multi_array[$i][2]);
				}
			}
		}
		if ($group_by != "") {
			$this->db->group_by($group_by);
		}
		$records = $this->db->get($table)->result();
		$totalRecordwithFilter = $records[0]->allcount;

		## Fetch records. Built as a retryable closure so a stale/invalid sort
		## column (e.g. leftover DataTables stateSave pointing at a computed
		## display column that isn't part of $select) degrades to an unsorted
		## result instead of crashing the whole listing with a DB error.
		$fetch = function ($withOrder) use ($select, $table, $searchQuery, $join_multi_array, $group_by, $columnName, $columnSortOrder, $rowperpage, $start) {
			$this->db->select($select);
			if ($searchQuery != '') {
				$this->db->where("1 " . $searchQuery);
			}
			if (count($join_multi_array) != 0 && is_array($join_multi_array)) {
				for ($i = 0; $i <  count($join_multi_array); $i++) {
					if (count($join_multi_array[$i]) == 2) {
						$this->db->join($join_multi_array[$i][0], $join_multi_array[$i][1]);
					} elseif (count($join_multi_array[$i]) == 3) {
						$this->db->join($join_multi_array[$i][0], $join_multi_array[$i][1], $join_multi_array[$i][2]);
					}
				}
			}
			if ($withOrder) {
				$this->db->order_by($columnName, $columnSortOrder);
			}
			$this->db->limit($rowperpage, $start);
			if ($group_by != "") {
				$this->db->group_by($group_by);
			}
			return $this->db->get($table)->result_array();
		};

		try {
			$records = $fetch(true);
		} catch (\Throwable $e) {
			log_message('error', 'getAjaxDataWithJoin: sort column "' . $columnName . '" invalid on table "' . $table . '" - ' . $e->getMessage());
			// The failed attempt above left select/where/join clauses queued
			// on the query builder (a thrown query doesn't auto-reset them,
			// unlike a successful one) - clear that state before rebuilding,
			// or the retry would duplicate every clause (e.g. the same JOIN
			// twice) and fail for a second, unrelated reason.
			$this->db->reset_query();
			$records = $fetch(false);
		}

		return ['records' => $records, 'totalRecords' => $totalRecordwithFilter, 'totalRecordwithFilter' => $totalRecordwithFilter];
	}
	public function getRowByConditions($table, $conditions = [])
	{
		if (!empty($conditions)) {
			$this->db->where($conditions);
		}

		$query = $this->db->get($table);

		if ($query->num_rows() > 0) {
			return $query->result_array(); // Return single row as an associative array
		}

		return false; // Return false if no matching row found
	}

	public function notifyOrderPlaced($name, $email, $orderId, $finalAmount)
	{
		$smtp = $this->getSingleRowById('mail_smtp_setting', ['id' => 1]);
		sendTemplatedMail('order_placed_user', $email, ['name' => $name, 'order_id' => $orderId, 'final_amount' => $finalAmount, 'app_name' => APP_NAME]);
		sendTemplatedMail('order_placed_admin', @$smtp['notify_email'], ['name' => $name, 'order_id' => $orderId, 'final_amount' => $finalAmount, 'app_name' => APP_NAME]);
	}

	// Single place every payment-confirmation path (Standard Checkout's
	// in-browser handler, the Razorpay webhook, and the admin "Sync Payment"
	// manual reconciliation button) funnels through, so payment_id/method/
	// signature/raw response get persisted identically regardless of which
	// path confirmed the payment - and so two of those racing for the same
	// order can never both fire the confirmation email or double-write.
	public function markOrderPaid($order, array $payment, $razorpayOrderEntity = null)
	{
		$update = [
			'transaction_status' => '1',
			'payment_id' => isset($payment['id']) ? $payment['id'] : null,
			'transaction_mode' => isset($payment['method']) ? $payment['method'] : null,
			'payment_gateway_response' => json_encode($payment),
			// Raw query builder below bypasses updateRowById()'s own
			// update_date stamping - set it explicitly so the admin
			// "Transaction Date" field stays accurate.
			'update_date' => date('Y-m-d H:i:s'),
		];
		if (isset($payment['signature'])) {
			$update['payment_hash'] = $payment['signature'];
		}

		if ($razorpayOrderEntity) {
			$customer = isset($razorpayOrderEntity->customer_details) ? $razorpayOrderEntity->customer_details : null;
			$shipping = ($customer && isset($customer->shipping_address)) ? $customer->shipping_address : null;

			if ($customer) {
				$update['email'] = isset($customer->email) ? $customer->email : $order['email'];
				$update['contact_no'] = isset($customer->contact) ? $customer->contact : $order['contact_no'];
			}
			if ($shipping) {
				$update['name'] = isset($shipping->name) ? $shipping->name : $order['name'];
				$update['address'] = trim((isset($shipping->line1) ? $shipping->line1 : '') . ' ' . (isset($shipping->line2) ? $shipping->line2 : ''));
				$update['city'] = isset($shipping->city) ? $shipping->city : $order['city'];
				$update['state'] = isset($shipping->state) ? $shipping->state : $order['state'];
				$update['postal_code'] = isset($shipping->zipcode) ? $shipping->zipcode : $order['postal_code'];
			}
			if (!empty($razorpayOrderEntity->promotions)) {
				$promo = $razorpayOrderEntity->promotions[0];
				$update['promocode_status'] = 1;
				$update['promocode'] = isset($promo->code) ? $promo->code : '';
				$update['promocode_amount'] = isset($promo->value) ? $promo->value / 100 : 0;
			}
		}

		// No separate payments ledger exists to catch a captured amount that
		// doesn't match what was actually charged - log it for manual
		// reconciliation instead of silently accepting whatever Razorpay sent.
		if (isset($payment['amount']) && (int) round($order['final_amount'] * 100) !== (int) $payment['amount']) {
			log_message('error', 'Razorpay amount mismatch for order ' . $order['order_id'] . ': expected ' . (int) round($order['final_amount'] * 100) . ' paise, captured ' . $payment['amount'] . ' paise');
		}

		// Atomic guard: only the request that actually flips an unpaid order to
		// paid gets affected_rows() > 0, so two of the confirmation paths above
		// racing each other for the same order can never both apply this update.
		$this->db->where('product_book_id', $order['product_book_id']);
		$this->db->where('transaction_status !=', '1');
		$this->db->update('book_product', $update);
		$applied = $this->db->affected_rows() > 0;

		// Guest checkout via Magic Checkout's own modal: mirrors the
		// lookup-or-create place_order() does up front, since this order was
		// created without a resolved user_id.
		if (!$order['user_id'] && $razorpayOrderEntity) {
			$customer = isset($razorpayOrderEntity->customer_details) ? $razorpayOrderEntity->customer_details : null;
			if ($customer && !empty($customer->contact)) {
				$existingUser = $this->getSingleRowById('user_registration', ['contact_no' => $customer->contact]);
				if (!$existingUser) {
					$newUserId = $this->insertRowReturnId('user_registration', [
						'name' => isset($update['name']) ? $update['name'] : $order['name'],
						'email_id' => isset($customer->email) ? $customer->email : '',
						'contact_no' => $customer->contact,
						'password' => password_hash('123456', PASSWORD_DEFAULT),
						'first_order' => 0,
					]);
					$existingUser = ['user_id' => $newUserId];
				}
				$this->updateRowById('book_product', 'product_book_id', $order['product_book_id'], ['user_id' => $existingUser['user_id']]);
			}
		}

		if ($applied) {
			log_message('info', 'Order ' . $order['order_id'] . ' marked paid via payment ' . (isset($payment['id']) ? $payment['id'] : 'unknown'));
			$this->notifyOrderPlaced(
				isset($update['name']) ? $update['name'] : $order['name'],
				isset($update['email']) ? $update['email'] : $order['email'],
				$order['order_id'],
				$order['final_amount']
			);
		} else {
			log_message('info', 'Duplicate payment confirmation ignored for order ' . $order['order_id']);
		}

		if ($applied) {
			$this->decrementOrderStock($order['product_book_id'], true);
		}

		return $applied;
	}

	// ==============================================================
	// Inventory: atomic stock decrement/restock + audit trail
	// ==============================================================

	// Decrements every not-yet-applied line item on an order inside one
	// row-locked transaction. $allowPin=true is for the payment-confirmation
	// path (Razorpay money is already captured by the time this runs, so an
	// oversold line gets pinned for manual review instead of failing);
	// $allowPin=false is for synchronous paths (COD, admin manual orders)
	// where nothing has been charged yet, so the caller can still reject the
	// whole order on insufficient stock. Returns ['success'=>bool,'pinned'=>bool].
	public function decrementOrderStock($productBookId, $allowPin, $changeType = 'order_decrement', $changedByType = 0, $changedById = null)
	{
		$items = $this->getRowByMoreId('book_item', ['product_book_id' => $productBookId, 'stock_applied' => 0]);
		if (!$items) {
			return ['success' => true, 'pinned' => false];
		}

		$this->db->trans_start();
		$anyPinned = false;
		$failedItem = null;
		foreach ($items as $item) {
			$result = $this->applyStockDecrement(
				$item['product_id'],
				$item['variant_id'],
				$item['no_of_items'],
				[
					'change_type' => $changeType,
					'reference_type' => 'book_item',
					'reference_id' => $item['book_item_id'],
					'changed_by_type' => $changedByType,
					'changed_by_id' => $changedById,
					'allow_partial_pin' => $allowPin,
				]
			);

			if (!$result['success']) {
				$failedItem = $item;
				break;
			}

			$this->db->set('stock_applied', 1)->where('book_item_id', $item['book_item_id'])->update('book_item');
			if ($result['pinned']) {
				$anyPinned = true;
			} else {
				// A sale that actually took stock from inventory (not pinned) -
				// if that stock traces to a vendor, snapshot the commission split
				// now so a later commission-rate change never rewrites history.
				$this->attributeVendorSale($item);
			}
		}

		if ($failedItem) {
			$this->db->trans_rollback();
			return ['success' => false, 'pinned' => false, 'product_name' => $failedItem['product_name']];
		}

		if ($anyPinned) {
			$this->db->set([
				'stock_flag' => 1,
				'stock_flag_reason' => 'One or more items had insufficient stock when payment was confirmed - review and cancel/refund if the item cannot be fulfilled.',
			])->where('product_book_id', $productBookId)->update('book_product');
		}

		$this->db->trans_complete();
		return ['success' => $this->db->trans_status() !== false, 'pinned' => $anyPinned];
	}

	// Row-locks the product/variant, decrements if enough stock is available,
	// and writes a ledger entry. On insufficient stock: returns success=false
	// when $context['allow_partial_pin'] is falsy (caller must reject/roll
	// back the whole order, nothing has been charged yet); otherwise records
	// a zero-delta 'oversold_pinned' ledger row and returns success=true,
	// pinned=true so the caller can still confirm the (already-paid-for)
	// order while flagging it for manual review instead of oversell it.
	public function applyStockDecrement($productId, $variantId, $qty, $context = [])
	{
		$qty = (float) $qty;
		if ($qty <= 0) {
			return ['success' => true, 'pinned' => false, 'balance_after' => null];
		}

		$allowPin = !empty($context['allow_partial_pin']);
		$table = $this->db->dbprefix($variantId ? 'product_variants' : 'product');
		$idColumn = $variantId ? 'variant_id' : 'product_id';
		$qtyColumn = $variantId ? 'stock_quantity' : 'quantity';
		$rowId = $variantId ? $variantId : $productId;

		$locked = $this->db->query("SELECT `{$qtyColumn}` AS qty FROM `{$table}` WHERE `{$idColumn}` = ? FOR UPDATE", [$rowId])->row_array();
		$available = ($locked && $locked['qty'] !== null) ? (float) $locked['qty'] : 0;

		if ($available >= $qty) {
			$balanceAfter = $available - $qty;
			$this->db->query("UPDATE `{$table}` SET `{$qtyColumn}` = `{$qtyColumn}` - ?, update_date = ? WHERE `{$idColumn}` = ?", [$qty, date('Y-m-d H:i:s'), $rowId]);
			if (!$variantId) {
				$this->autoToggleOutOfStock($productId, $balanceAfter);
			}
			$this->writeStockLedger($productId, $variantId, -$qty, $balanceAfter, isset($context['change_type']) ? $context['change_type'] : 'order_decrement', $context);
			return ['success' => true, 'pinned' => false, 'balance_after' => $balanceAfter];
		}

		if (!$allowPin) {
			return ['success' => false, 'pinned' => false, 'balance_after' => $available];
		}

		$pinnedContext = $context;
		$pinnedContext['note'] = 'Insufficient stock at confirmation: requested ' . $qty . ', available ' . $available;
		$this->writeStockLedger($productId, $variantId, 0, $available, 'oversold_pinned', $pinnedContext);
		return ['success' => true, 'pinned' => true, 'balance_after' => $available];
	}

	// Reverses a prior decrement (cancel/return). Only ever called for lines
	// that actually had stock_applied=1, so there is nothing to "fail" here -
	// unlike a decrement, a restock can't run out of room to add stock back to.
	public function applyStockRestock($productId, $variantId, $qty, $context = [])
	{
		$qty = (float) $qty;
		if ($qty <= 0) {
			return ['success' => true, 'balance_after' => null];
		}

		$table = $this->db->dbprefix($variantId ? 'product_variants' : 'product');
		$idColumn = $variantId ? 'variant_id' : 'product_id';
		$qtyColumn = $variantId ? 'stock_quantity' : 'quantity';
		$rowId = $variantId ? $variantId : $productId;

		$locked = $this->db->query("SELECT `{$qtyColumn}` AS qty FROM `{$table}` WHERE `{$idColumn}` = ? FOR UPDATE", [$rowId])->row_array();
		$available = ($locked && $locked['qty'] !== null) ? (float) $locked['qty'] : 0;
		$balanceAfter = $available + $qty;

		$this->db->query("UPDATE `{$table}` SET `{$qtyColumn}` = `{$qtyColumn}` + ?, update_date = ? WHERE `{$idColumn}` = ?", [$qty, date('Y-m-d H:i:s'), $rowId]);
		if (!$variantId) {
			$this->autoToggleOutOfStock($productId, $balanceAfter);
		}
		$this->writeStockLedger($productId, $variantId, $qty, $balanceAfter, isset($context['change_type']) ? $context['change_type'] : 'order_restock', $context);

		return ['success' => true, 'balance_after' => $balanceAfter];
	}

	// Restocks every applied line of an order (cancel/return), skipping lines
	// that never actually decremented stock (e.g. a pinned/oversold line -
	// nothing was taken from inventory for it, so there's nothing to give back).
	public function restockOrderItems($productBookId, $changeType, $changedByType, $changedById, $onlyBookItemIds = null)
	{
		$where = ['product_book_id' => $productBookId, 'stock_applied' => 1];
		$items = $this->getRowByMoreId('book_item', $where);
		if (!$items) {
			return;
		}

		$this->db->trans_start();
		foreach ($items as $item) {
			if ($onlyBookItemIds !== null && !in_array($item['book_item_id'], $onlyBookItemIds)) {
				continue;
			}
			$this->applyStockRestock($item['product_id'], $item['variant_id'], $item['no_of_items'], [
				'change_type' => $changeType,
				'reference_type' => 'book_item',
				'reference_id' => $item['book_item_id'],
				'changed_by_type' => $changedByType,
				'changed_by_id' => $changedById,
			]);
			$this->db->set('stock_applied', 0)->where('book_item_id', $item['book_item_id'])->update('book_item');
		}
		$this->db->trans_complete();
	}

	// If the sold product's stock is currently attributed to a vendor
	// (tbl_product.default_vendor_id, set when a vendor's product submission
	// is approved/linked - see AdminVendor::approveVendorProduct()), record
	// this line's commission split. Commission percent/supply price are
	// snapshotted onto the row at sale time - a later change to the vendor's
	// commission rate must never rewrite an already-sold line's payout.
	private function attributeVendorSale($item)
	{
		$product = $this->getSingleRowById('product', ['product_id' => $item['product_id']]);
		if (!$product || empty($product['default_vendor_id'])) {
			return;
		}

		$vendorProduct = $this->getSingleRowById('vendor_product', [
			'vendor_id' => $product['default_vendor_id'],
			'product_id' => $item['product_id'],
			'status' => 1, // approved
		]);
		if (!$vendorProduct) {
			return;
		}

		$commissionPercent = $vendorProduct['commission_percent'] !== null ? (float) $vendorProduct['commission_percent'] : 0;
		$lineTotal = (float) $item['booking_price'];
		$commissionAmount = round($lineTotal * $commissionPercent / 100, 2);

		$this->insertRowWithXSS('vendor_order_item', [
			'vendor_id' => $product['default_vendor_id'],
			'book_item_id' => $item['book_item_id'],
			'product_book_id' => $item['product_book_id'],
			'quantity' => $item['no_of_items'],
			'vendor_supply_price' => $vendorProduct['vendor_supply_price'],
			'commission_percent' => $commissionPercent,
			'commission_amount' => $commissionAmount,
			'vendor_payable_amount' => round($lineTotal - $commissionAmount, 2),
			'payout_status' => 0,
		]);
	}

	// For the one case where a product row is INSERTed with its opening
	// quantity already set in the same statement (a brand-new product created
	// from an approved vendor submission - see AdminVendor::vendorProductReview())
	// there is no prior quantity to decrement/restock from, so this just
	// records the ledger entry without re-touching tbl_product.quantity
	// (calling applyStockRestock() here would double-count the opening stock).
	public function recordInitialStockLedger($productId, $variantId, $openingQty, $changeType, $context = [])
	{
		$this->writeStockLedger($productId, $variantId, $openingQty, $openingQty, $changeType, $context);
	}

	private function writeStockLedger($productId, $variantId, $delta, $balanceAfter, $changeType, $context)
	{
		$this->db->insert('stock_ledger', [
			'product_id' => $productId,
			'variant_id' => $variantId ?: null,
			'vendor_id' => isset($context['vendor_id']) ? $context['vendor_id'] : null,
			'change_type' => $changeType,
			'delta' => $delta,
			'balance_after' => $balanceAfter,
			'reference_type' => isset($context['reference_type']) ? $context['reference_type'] : null,
			'reference_id' => isset($context['reference_id']) ? $context['reference_id'] : null,
			'note' => isset($context['note']) ? $context['note'] : null,
			'changed_by_type' => isset($context['changed_by_type']) ? $context['changed_by_type'] : 0,
			'changed_by_id' => isset($context['changed_by_id']) ? $context['changed_by_id'] : null,
			'create_date' => date('Y-m-d H:i:s'),
		]);
	}

	// is_out_of_stock becomes derived from real stock levels for non-variant
	// products. A manual is_out_of_stock_override (added in Phase 2) always
	// wins over this automatic derivation when set. Variant-level stock does
	// not drive this flag - a multi-variant product can have some variants
	// out while others remain purchasable, which the product-level flag
	// can't represent; that nuance is handled at the variant's own is_active.
	private function autoToggleOutOfStock($productId, $balanceAfter)
	{
		$product = $this->getSingleRowById('product', ['product_id' => $productId]);
		if (!$product) {
			return;
		}
		if (array_key_exists('is_out_of_stock_override', $product) && $product['is_out_of_stock_override'] !== null) {
			return;
		}
		$this->db->set('is_out_of_stock', $balanceAfter <= 0 ? 1 : 0)->where('product_id', $productId)->update('product');
	}

	// Stricter than the generic fullImage()/documentUpload() helpers (which
	// allow any file type) - these are KYC/financial documents, so both the
	// extension and the real (sniffed) MIME type are checked, not just the
	// client-supplied extension, and files land under VENDOR_DOCUMENT_PATH,
	// outside the webroot. Shared by Vendor::register() (vendor self-service)
	// and AdminVendor::vendorAdd() (admin manual entry) - identical rules
	// regardless of who is uploading.
	public function handleVendorDocumentUpload($vendorId, $fieldName, $docType)
	{
		if (empty($_FILES[$fieldName]['name'])) {
			return;
		}
		if ($_FILES[$fieldName]['size'] > MAX_VENDOR_DOCUMENT_SIZE) {
			return;
		}
		$allowedMimes = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'];
		$realMime = mime_content_type($_FILES[$fieldName]['tmp_name']);
		if (!isset($allowedMimes[$realMime])) {
			return;
		}

		$dir = VENDOR_DOCUMENT_PATH . $vendorId . '/';
		if (!file_exists($dir)) {
			mkdir($dir, 0755, true);
		}
		$filename = bin2hex(random_bytes(16)) . '.' . $allowedMimes[$realMime];
		if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $dir . $filename)) {
			$this->insertRow('vendor_document', [
				'vendor_id' => $vendorId,
				'document_type' => $docType,
				'file_path' => $vendorId . '/' . $filename,
			]);
		}
	}

	// Determines who should physically ship an order, for Shiprocket sync
	// (see AdminHome::shiprocketOrderDetails()/shipWithShiprocket()). Reads
	// tbl_vendor_order_item, which only ever gets a row when a line's stock
	// was actually attributed to a vendor at decrement time (see
	// attributeVendorSale() above) - so this reflects who really fulfilled
	// the order, not just which products happen to have a default vendor.
	// Returns:
	//   ['type' => 'house']                          - no vendor-sourced items
	//   ['type' => 'vendor', 'vendor' => <row>]       - every item from one vendor
	//   ['type' => 'mixed', 'vendor_count' => n]      - more than one vendor involved
	// (a mixed order can also include house-stocked items alongside vendor
	// ones - Shiprocket can't split one order across pickup addresses, so the
	// caller falls back to the house pickup location and flags it for admin).
	public function getOrderShippingSource($productBookId)
	{
		$rows = $this->runQuery(
			"SELECT DISTINCT vendor_id FROM tbl_vendor_order_item WHERE product_book_id = " . (int) $productBookId,
			1
		);
		if (!$rows) {
			return ['type' => 'house'];
		}
		if (count($rows) > 1) {
			return ['type' => 'mixed', 'vendor_count' => count($rows)];
		}
		$vendor = $this->getSingleRowById('vendor', ['vendor_id' => $rows[0]['vendor_id']]);
		if (!$vendor) {
			return ['type' => 'house'];
		}
		return ['type' => 'vendor', 'vendor' => $vendor];
	}

	// ==============================================================
	// Admin/vendor activity audit log
	// ==============================================================
	public function logAdminActivity($actorType, $actorId, $action, $entityType, $entityId = null, $before = null, $after = null)
	{
		$this->db->insert('admin_activity_log', [
			'admin_id' => $actorType == 1 ? $actorId : null,
			'actor_type' => $actorType,
			'vendor_id' => $actorType == 2 ? $actorId : null,
			'action' => $action,
			'entity_type' => $entityType,
			'entity_id' => $entityId,
			'before_data' => $before !== null ? json_encode($before) : null,
			'after_data' => $after !== null ? json_encode($after) : null,
			'ip_address' => $this->input->ip_address(),
			'create_date' => date('Y-m-d H:i:s'),
		]);
	}
}
