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

		return $applied;
	}
}
