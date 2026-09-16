<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Viewer for tbl_admin_activity_log - who did what, when, across every admin
// and vendor action wired to CommonModel::logAdminActivity(). Always
// date-bounded (default last 7 days) and paginated via the standard
// DataTables server-side pattern - this table is the fastest-growing one in
// the system, so it must never be queried without a bound.
class AdminAudit extends CI_Controller
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

		if (!(@PREV['audit_log_view'] == 1 || USER_TYPE == '1')) {
			show_404();
		}
	}

	public function activityLogAll()
	{
		if (count($_POST) > 0) {
			$postData = $this->input->post();
			$dateFrom = $this->input->post('searchByDateFrom') ?: date('Y-m-d', strtotime('-7 days'));
			$dateTo = $this->input->post('searchByDateTo') ?: date('Y-m-d');
			$searchQuery = " AND DATE(admin_activity_log.create_date) BETWEEN '" . date('Y-m-d', strtotime($dateFrom)) . "' AND '" . date('Y-m-d', strtotime($dateTo)) . "'";

			$actionFilter = $this->input->post('searchByAction');
			if (!empty($actionFilter)) {
				$searchQuery .= " AND admin_activity_log.action = '" . $this->db->escape_str($actionFilter) . "'";
			}

			$select = "admin_activity_log.*, admin_login.name AS admin_name, vendor.business_name AS vendor_name";
			$join = [
				['admin_login', 'admin_login.admin_id = admin_activity_log.admin_id', 'LEFT'],
				['vendor', 'vendor.vendor_id = admin_activity_log.vendor_id', 'LEFT'],
			];
			$allData = $this->CommonModel->getAjaxDataWithJoin($select, 'admin_activity_log', $searchQuery, $postData, $join);

			$data = [];
			foreach ($allData['records'] as $record) {
				$actor = $record['actor_type'] == ACTOR_TYPE_VENDOR
					? ($record['vendor_name'] ?: ('Vendor #' . $record['vendor_id']))
					: ($record['admin_name'] ?: ('Admin #' . $record['admin_id']));
				$data[] = [
					'create_date' => dateConvertToView($record['create_date'], 3),
					'actor' => $actor,
					'action' => ucwords(str_replace('_', ' ', $record['action'])),
					'entity' => ucwords(str_replace('_', ' ', $record['entity_type'])) . ($record['entity_id'] ? ' #' . $record['entity_id'] : ''),
					'ip_address' => $record['ip_address'],
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

		$data['title'] = 'Admin Activity Log';
		$table_header = [
			['data' => 'create_date'],
			['data' => 'actor'],
			['data' => 'action'],
			['data' => 'entity'],
			['data' => 'ip_address'],
		];
		$data['ajax_table'] = 'activityLogAll';
		$data['table_column'] = json_encode($table_header);
		$this->load->view('admin/activity_log_all', $data);
	}
}
