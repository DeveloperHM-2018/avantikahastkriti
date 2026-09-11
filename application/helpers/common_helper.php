<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function setDateTime()
{
	return date('Y-m-d H:i:s');
}

function setDateOnly()
{
	return date('Y-m-d');
}

function clean($string)
{
	return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string);
}

function dateConvertToView($date, $type)
{
	if ($date == "" || $date == '0000-00-00') {
		return "";
	} else {
		if ($type == 1) {
			return date('d-M-Y', strtotime($date));
		} else if ($type == 2) {
			return date('d-m-Y', strtotime($date));
		} else {
			return date('d-M-Y h:i A', strtotime($date));
		}
	}
}

function dateConvertToDb($date)
{
	return date('Y-m-d', strtotime($date));
}

function sessionId($id)
{
	$ci = &get_instance();
	return $ci->session->userdata($id);
}

function setSession($data)
{
	$ci = &get_instance();
	return $ci->session->set_userdata($data);
}

function setAlert($title, $alert_type, $message)
{
	$ci = &get_instance();
	return $ci->session->set_flashdata('alert_errors', ['title' => $title, 'color' => $alert_type, 'message' => $message]);
}

function randomCode($length_of_string)
{
	$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	return substr(str_shuffle($str_result), 0, $length_of_string);
}

function getRowById($table, $column, $id)
{
	$ci = &get_instance();
	$get = $ci->db->get_where($table, array($column => $id));
	if ($get->num_rows() > 0) {
		return $get->result_array();
	} else {
		return false;
	}
}

function getSingleRowById($table, $where)
{
	$ci = &get_instance();
	$get = $ci->db->select()
		->from($table)
		->where($where)
		->get();
	if ($get->num_rows() > 0) {
		return $get->row_array();
	} else {
		return false;
	}
}

function getAllRow($table)
{
	$ci = &get_instance();
	$get = $ci->db->select()
		->from($table)
		->get();
	if ($get->num_rows() > 0) {
		return $get->result_array();
	} else {
		return false;
	}
}

function getAllRowInOrder($table, $column, $type)
{
	$ci = &get_instance();
	$select = $ci->db->order_by($column, $type)->get($table);
	if ($select->num_rows() > 0) {
		return $select->result_array();
	} else {
		return false;
	}
}

function getRowsByMoreIdWithOrder($table, $where, $column, $type)
{
	$ci = &get_instance();
	$select = $ci->db->order_by($column, $type)->get_where($table, $where);
	if ($select->num_rows() > 0) {
		return $select->result_array();
	} else {
		return false;
	}
}
function getRowsByMoreIdWithOrderlimit($table, $where, $column, $type, $limit)
{
	$ci = &get_instance();
	$select = $ci->db->limit($limit)->order_by($column, $type)->get_where($table, $where);
	if ($select->num_rows() > 0) {
		return $select->result_array();
	} else {
		return false;
	}
}

function getDataByIdInOrder($table, $column, $id, $orderColumn, $type)
{
	$ci = &get_instance();
	$select = $ci->db->order_by($orderColumn, $type)->get_where($table, array($column => $id));
	return $select->result_array();
}

function getAllDataWithLimitInOrder($table, $orderColumn, $type, $start, $end)
{
	$ci = &get_instance();
	$select = $ci->db->order_by($orderColumn, $type)->limit($start, $end)->get($table);
	return $select->result_array();
}

function getRowByMoreId($table, $where)
{
	$ci = &get_instance();
	$get = $ci->db->select()
		->from($table)
		->where($where)
		->get();
	if ($get->num_rows() > 0) {
		return $get->result_array();
	} else {
		return false;
	}
}

function getNumRows($table, $where)
{
	$ci = &get_instance();
	$get = $ci->db->select()
		->from($table)
		->where($where)
		->get();
	return $get->num_rows();
}

function getRowByLikeInOrder($table, $where, $like, $name, $orderBy, $orderType)
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

function encryptId($string)
{
	$key = ENC_DEC_HASH_KEY;
	$rand = 'E3N^&&%*)&*u^N%42^n4^5U%$N3%$6K@4b&A(JF@H(&M$%m&@I';
	$rand = sha1($rand);
	$enc = '';
	for ($i = 0; $i < strlen($string); $i++) {
		$enc .= substr($rand, ($i % strlen($rand)), 1) . (substr($rand, ($i % strlen($rand)), 1) ^ substr($string, $i, 1));
	}
	$string = $enc;
	$hash = sha1($key);

	$str = '';
	for ($i = 0; $i < strlen($string); $i++) {
		$str .= substr($string, $i, 1) ^ substr($hash, ($i % strlen($hash)), 1);
	}
	$string =  strtr(bin2hex($str), '+/=', '-_#');
	$string =  old_enc_url($string);
	$string = $string . '_vnr';
	return $string;
}

function old_enc_url($string)
{
	$jen = json_encode("$string");
	$b64 = base64_encode($jen);
	$uen = urlencode($b64);
	$encrypt = htmlspecialchars($uen);
	$strerpl = str_replace("%", "_", $encrypt);
	return $strerpl;
}

function getDecId($string)
{
	error_reporting(0);
	$string = old_dec_url($string);
	$key = ENC_DEC_HASH_KEY;
	$dec = hex2bin(strtr($string, '-_#', '+/='));
	$hash = sha1($key);

	$str = '';
	for ($i = 0; $i < strlen($dec); $i++) {
		$str .= substr($dec, $i, 1) ^ substr($hash, ($i % strlen($hash)), 1);
	}

	$dec = '';
	for ($i = 0; $i < strlen($str); $i++) {
		$dec .= (substr($str, $i++, 1) ^ substr($str, $i, 1));
	}
	return intval($dec);
}


function old_dec_url($string)
{
	$strerpl = str_replace("_", "%", $string);
	$uen = urldecode($strerpl);
	$b64 = base64_decode($uen);
	$decrypt = json_decode($b64);
	return $decrypt;
}

function decryptId($string)
{
	$res = 0;
	if (substr($string, -4) == '_vnr') {
		$string = substr($string, 0, -4);
		$new_dec_fun = getDecId($string);
		if ($new_dec_fun > 0) {
			$res = $new_dec_fun;
		}
	} else {
		$res = intval(old_dec_url($string));
	}
	return $res;
}

function lastReplace($search, $replace, $subject)
{
	$pos = strrpos($subject, $search);
	if ($pos !== false) {
		$subject = substr_replace($subject, $replace, $pos, strlen($search));
	}
	return $subject;
}

function getSumInRow($table, $where, $sumColumn)
{
	$ci = &get_instance();
	$get = $ci->db->select_sum($sumColumn)
		->from($table)
		->where($where)
		->get();
	if ($get->num_rows() > 0) {
		$total = $get->row_array();
		return $total[$sumColumn];
	} else {
		return false;
	}
}

function dateDiffInDays($date1, $date2)
{
	$diff = strtotime($date2) - strtotime($date1);
	return abs(round($diff / 86400));
}

function flashData($var, $message)
{
	$ci = &get_instance();
	return $ci->session->set_flashdata($var, $message);
}

function sendOTP($contact_no, $message)
{
	$url = 'https://www.wpsenders.in/api/sendTextMessage';

	$dataArray = [
		'api_key' => '',
		'message' => $message,
		'number' => $contact_no,
	];

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $dataArray);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 1);
	$data = curl_exec($ch);
	return $data;
}



function getUserId($token)
{
	$ci = &get_instance();
	$ip = $ci->input->ip_address();
	$get = $ci->db->select()
		->from('user_registration')
		->where([
			'user_registration.user_id' => $token['data']->id,
			'user_status' => '1',
			'unique_hash' => $token['data']->unique_hash,
		])
		->get();
	if ($get->num_rows() > 0) {
		return $get->row_array();
	} else {
		return false;
	}
}

function orderIdGenerateUser($table, $column)
{
	$number = "TXN" . date('ydmhis');
	if (checkOrderIdExistUser($number, $table, $column)) {
		return orderIdGenerateUser($table, $column);
	} else {
		return $number;
	}
}

function checkOrderIdExistUser($number, $table, $column)
{
	$ci = &get_instance();
	$get = $ci->db->select()
		->from($table)
		->where("$column = '$number'")
		->get();
	if ($get->num_rows() > 0) {
		return true;
	} else {
		return false;
	}
}

function isStatusActive($status)
{
	global $bookingStatus;
	return in_array($bookingStatus, $status);
}

function referralCode()
{
	$number = 'SM-' . rand(9999, 99999);
	if (checkReferralCodeExist($number)) {
		return referralCode();
	} else {
		return $number;
	}
}

function checkReferralCodeExist($number)
{
	$ci = &get_instance();
	$get = $ci->db->select()
		->from('students')
		->where("student_id = '$number'")
		->get();
	if ($get->num_rows() > 0) {
		return true;
	} else {
		return false;
	}
}

function multi_array_search($search_for, $search_in)
{
	foreach ($search_in as $element) {
		if (($element === $search_for) || (is_array($element) && multi_array_search($search_for, $element))) {
			return $element;
		}
	}
	return false;
}

function searchForId($column, $id, $array)
{
	if (!empty($array)) {
		foreach ($array as $key => $val) {
			if ($val[$column] === $id) {
				return $array[$key];
			}
		}
	}
	return false;
}

function imageUpload($imageName, $path, $temp_image)
{
	if (!file_exists($path)) {
		mkdir($path, 0777, true);
	}
	$ci = &get_instance();
	$config['file_name'] = uniqid();
	$config['allowed_types'] = 'jpg|png|jpeg';
	$config['upload_path'] = $path;
	$target_path = $path;
	$config['remove_spaces'] = true;
	$config['overwrite'] = false;
	$ci->load->library('upload', $config);
	$ci->upload->initialize($config);
	if ($ci->upload->do_upload($imageName)) {
		$data = array('upload_data' => $ci->upload->data());
		$path = $data['upload_data']['full_path'];
		$picture = $data['upload_data']['file_name'];
		$configi['image_library'] = 'gd2';
		$configi['quality'] = '100%';
		$configi['create_thumb'] = FALSE;
		$configi['source_image'] = $path;
		$configi['new_image'] = $target_path;
		$configi['maintain_ratio'] = TRUE;
		$configi['width'] = 380;
		$configi['height'] = 260;
		$ci->load->library('image_lib');
		$ci->image_lib->initialize($configi);
		$ci->image_lib->resize();
		if ($temp_image != "") {
			unlink($target_path . '/' . $temp_image);
		}
		return $picture;
	} else {
		return false;
		// return $ci->upload->display_errors();
	}
}

function imageUploadWithRatio($imageName, $path, $width, $height, $temp_image)
{
	if (!file_exists($path)) {
		mkdir($path, 0777, true);
	}
	$ci = &get_instance();
	$config['file_name'] = uniqid();
	$config['allowed_types'] = 'jpg|png|jpeg';
	$config['upload_path'] = $path;
	$target_path = $path;
	$config['remove_spaces'] = true;
	$config['overwrite'] = false;
	$ci->load->library('upload', $config);
	$ci->upload->initialize($config);
	if ($ci->upload->do_upload($imageName)) {
		$data = array('upload_data' => $ci->upload->data());
		$path = $data['upload_data']['full_path'];
		$picture = $data['upload_data']['file_name'];

		// Skip resizing if the uploaded image is already within the target
		// box - otherwise maintain_ratio resize scales it UP to fit, which
		// blurs/pixelates any photo smaller than the target dimensions.
		$sourceSize = @getimagesize($path);
		$needsResize = !$sourceSize || $sourceSize[0] > $width || $sourceSize[1] > $height;

		if ($needsResize) {
			$configi['image_library'] = 'gd2';
			$configi['quality'] = '100%';
			$configi['create_thumb'] = FALSE;
			$configi['source_image'] = $path;
			$configi['new_image'] = $target_path;
			$configi['maintain_ratio'] = TRUE;
			$configi['width'] = $width;
			$configi['height'] = $height;
			$ci->load->library('image_lib');
			$ci->image_lib->initialize($configi);
			$ci->image_lib->resize();
		}
		if ($temp_image != "") {
			unlink($target_path . '/' . $temp_image);
		}
		return $picture;
	} else {
		return false;
	}
}

function fullImage($imageName, $path, $temp_image, $maxSizeBytes = 0)
{
	if (!file_exists($path)) {
		mkdir($path, 0777, true);
	}
	$ci = &get_instance();
	$config['file_name'] = uniqid();
	$config['allowed_types'] = '*';
	$config['upload_path'] = $path;
	$target_path = $path;
	$config['remove_spaces'] = true;
	$config['overwrite'] = false;
	if ($maxSizeBytes > 0) {
		$config['max_size'] = $maxSizeBytes / 1024; // CI upload library expects KB
	}
	$ci->load->library('upload', $config);
	$ci->upload->initialize($config);
	if ($ci->upload->do_upload($imageName)) {
		$data = array('upload_data' => $ci->upload->data());
		$path = $data['upload_data']['full_path'];
		$picture = $data['upload_data']['file_name'];
		if ($temp_image != "") {
			unlink($target_path . '/' . $temp_image);
		}
		return $picture;
	} else {
		return false;
		// return $ci->upload->display_errors();
	}
}

function documentUpload($imageName, $path, $temp_image)
{
	if (!file_exists($path)) {
		mkdir($path, 0777, true);
	}
	$ci = &get_instance();
	$config['file_name'] = uniqid();
	$config['allowed_types'] = '*';
	$config['upload_path'] = $path;
	$target_path = $path;
	$config['remove_spaces'] = true;
	$config['overwrite'] = false;
	$ci->load->library('upload', $config);
	$ci->upload->initialize($config);
	if ($ci->upload->do_upload($imageName)) {
		$data = array('upload_data' => $ci->upload->data());
		$path = $data['upload_data']['full_path'];
		$picture = $data['upload_data']['file_name'];
		if ($temp_image != "") {
			unlink($target_path . '/' . $temp_image);
		}
		return $picture;
	} else {
		// return false;
		return $ci->upload->display_errors();
	}
}

function compressImage($file, $path, $temp_file_name)
{
	$image_parts = explode(";base64,", $file);
	$image_base64 = base64_decode($image_parts[1]);
	$file_name = uniqid() . '.png';
	$aadhaarB =  $path . $file_name;
	file_put_contents($aadhaarB, $image_base64);
	if ($temp_file_name != "") {
		unlink($path . $temp_file_name);
	}
	return $file_name;
}

function curlResponse($url, $dataArray)
{
	$ch = curl_init();
	$url =  $url;
	$data = http_build_query($dataArray);
	$getUrl = $url . "?" . $data;
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_URL, $getUrl);
	curl_setopt($ch, CURLOPT_TIMEOUT, 80);
	$response = curl_exec($ch);
	return json_decode($response, true);
}

function sendMessageToWhatsapp($message, $contact_no) {}

function multi_array_in_search($column, $id, $array)
{
	if (!empty($array)) {
		$i = 0;
		foreach ($array as $key => $val) {
			$val['total_user'] =  ++$i;
			if ($val[$column] === $id) {
				return $val;
			}
		}
	}
	return false;
}

function setImage($image_nm, $location)
{
	if ($image_nm != '') {
		if (is_file($location . $image_nm)) {
			return base_url() . $location . $image_nm;
		} else {
			return base_url() . 'assets/placeholder.svg';
		}
	} else {
		return base_url() . 'assets/placeholder.svg';
	}
}

function sendTemplatedMail($eventKey, $toEmail, $placeholders = [])
{
	$ci = &get_instance();

	if (empty($toEmail)) {
		log_message('error', "sendTemplatedMail: no recipient email for event '$eventKey'");
		return false;
	}

	$template = $ci->CommonModel->getSingleRowById('mail_template', ['event_key' => $eventKey]);
	if (!$template || $template['is_enabled'] == 0) {
		log_message('error', "sendTemplatedMail: template '$eventKey' missing or disabled");
		return false;
	}

	$search = [];
	$replace = [];
	foreach ($placeholders as $key => $value) {
		$search[] = '{{' . $key . '}}';
		$replace[] = $value;
	}
	$subject = str_replace($search, $replace, $template['subject']);
	$body = str_replace($search, $replace, $template['body']);

	$smtp = $ci->CommonModel->getSingleRowById('mail_smtp_setting', ['id' => 1]);
	if (!$smtp || empty($smtp['smtp_host']) || empty($smtp['smtp_user'])) {
		log_message('error', "sendTemplatedMail: SMTP settings not configured, event '$eventKey'");
		return false;
	}

	$config['protocol'] = $smtp['protocol'] ?: 'smtp';
	$config['smtp_host'] = $smtp['smtp_host'];
	$config['smtp_port'] = $smtp['smtp_port'];
	$config['smtp_user'] = $smtp['smtp_user'];
	$config['smtp_pass'] = $smtp['smtp_pass'];
	$config['smtp_crypto'] = $smtp['smtp_crypto'];
	$config['charset'] = 'utf-8';
	$config['newline'] = "\r\n";
	$config['mailtype'] = 'html';
	$config['validation'] = TRUE;

	$ci->load->library('email');
	$ci->email->initialize($config);
	$ci->email->from($smtp['from_email'] ?: $smtp['smtp_user'], $smtp['from_name'] ?: APP_NAME);
	$ci->email->to($toEmail);
	$ci->email->subject($subject);
	$ci->email->message($body);

	if ($ci->email->send()) {
		return true;
	} else {
		log_message('error', "sendTemplatedMail: send failed for event '$eventKey' - " . $ci->email->print_debugger(['headers']));
		return false;
	}
}

function referIdGenerate()
{
	$number = randomCode(10);
	if (checkReferIdGenerate($number)) {
		return referIdGenerate();
	} else {
		return $number;
	}
}

function checkReferIdGenerate($number)
{
	$ci = &get_instance();
	$get = $ci->db->select()
		->from('user_registration')
		->where("user_code = '$number'")
		->get();
	if ($get->num_rows() > 0) {
		return true;
	} else {
		return false;
	}
}

function statusView($color, $name)
{
	return '<span class="badge badge-soft-' . $color . ' fs-6 mr-1">' . ($name == "" ? 'Pending' : $name) . '</span>';
}

function clickButton($url, $text, $target = 1)
{
	$ci = &get_instance();
	return '<a href="' . $url . '" ' . ($target == 1 ? 'target="_blank"' : '') . ' class="btn btn-primary btn-sm">' . $text . '</a>';
}


// === Common Functions ===
function userSession($has = true)
{
	$ci = &get_instance();
	if ($has) {
		if (!$ci->session->has_userdata('login_user_id')) {
			redirect('login');
		}
	} else {
		if ($ci->session->has_userdata('login_user_id')) {
			redirect('profile');
		}
	}
}

function generateOrderId($id)
{
	return '#ATP' . str_pad($id, 4, '0', STR_PAD_LEFT);
}

// === Product Return Management ===

function returnCodeGenerate()
{
	$number = 'RET' . date('ydmhis');
	if (checkOrderIdExistUser($number, 'return_request', 'return_code')) {
		return returnCodeGenerate();
	}
	return $number;
}

function getReturnWindowDays()
{
	$ci = &get_instance();
	$setting = $ci->CommonModel->getSingleRowById('setting', ['id' => 1]);
	return $setting && $setting['return_window_days'] > 0 ? (int) $setting['return_window_days'] : 4;
}

function getReturnStatusLabel($status)
{
	$labels = [
		RETURN_STATUS_REQUESTED          => 'Requested',
		RETURN_STATUS_UNDER_REVIEW       => 'Under Review',
		RETURN_STATUS_APPROVED           => 'Approved',
		RETURN_STATUS_PICKUP_SCHEDULED   => 'Pickup Scheduled',
		RETURN_STATUS_PICKED_UP          => 'Picked Up',
		RETURN_STATUS_RECEIVED_WAREHOUSE => 'Received At Warehouse',
		RETURN_STATUS_REFUND_PROCESSED   => 'Refund Processed',
		RETURN_STATUS_REJECTED           => 'Rejected',
	];
	return isset($labels[$status]) ? $labels[$status] : 'Unknown';
}

// Returns eligibility for a single order line ($bookItem row from tbl_book_item,
// $bookProduct row from tbl_book_product it belongs to). Centralized so the web
// storefront, the mobile API, and the eligibility re-check on submit all agree.
function isReturnEligible($bookItem, $bookProduct)
{
	$ci = &get_instance();

	if (!$bookProduct || $bookProduct['booking_status'] != '4' || empty($bookProduct['delivery_date'])) {
		return ['eligible' => false, 'days_left' => 0, 'reason' => 'not_delivered'];
	}

	$existing = $ci->CommonModel->getSingleRowById('return_request', ['book_item_id' => $bookItem['book_item_id']]);
	if ($existing) {
		return ['eligible' => false, 'days_left' => 0, 'reason' => 'already_requested', 'return_status' => (int) $existing['status'], 'return_id' => (int) $existing['return_id']];
	}

	$windowDays = getReturnWindowDays();
	$deliveryDate = new DateTime($bookProduct['delivery_date']);
	$expiry = (clone $deliveryDate)->modify("+{$windowDays} days");
	$today = new DateTime(date('Y-m-d'));

	if ($today > $expiry) {
		return ['eligible' => false, 'days_left' => 0, 'reason' => 'window_expired'];
	}

	// +1 so the expiry day itself still shows as "1 Day Left" rather than 0.
	$daysLeft = (int) $today->diff($expiry)->days + 1;
	return ['eligible' => true, 'days_left' => $daysLeft, 'reason' => ''];
}

// Single choke point for every return status transition: writes the timeline
// row and updates the parent status in one place, so nothing can change a
// return's status without also logging it.
function logReturnStatus($returnId, $status, $note = '', $changedByType = 0, $changedById = null)
{
	$ci = &get_instance();
	$ci->db->trans_start();

	$ci->db->where('return_id', $returnId)->update('return_request', [
		'status' => $status,
		'update_date' => setDateTime(),
	]);

	$ci->db->insert('return_status_log', [
		'return_id' => $returnId,
		'status' => $status,
		'note' => $note,
		'changed_by_type' => $changedByType,
		'changed_by_id' => $changedById,
		'create_date' => setDateTime(),
	]);

	$ci->db->trans_complete();
	return $ci->db->trans_status();
}

function getRefundStatusLabel($status)
{
	$labels = [
		REFUND_STATUS_PENDING => 'Pending',
		REFUND_STATUS_COMPLETED => 'Completed',
		REFUND_STATUS_FAILED => 'Failed',
	];
	return isset($labels[$status]) ? $labels[$status] : 'Unknown';
}

// Credits a customer's wallet balance and records the ledger entry in one
// transaction, reusing tbl_wallet_history (previously dormant - no other
// code path wrote to it) instead of introducing a second wallet table.
function creditWallet($userId, $amount, $message)
{
	$ci = &get_instance();
	$ci->db->trans_start();

	$ci->db->insert('wallet_history', [
		'user_id' => $userId,
		'amount' => $amount,
		'type' => 1, // credit
		'message' => $message,
		'create_date' => setDateTime(),
	]);

	$ci->db->set('wallet_amount', 'wallet_amount + ' . (float) $amount, false)
		->where('user_id', $userId)
		->update('user_registration');

	$ci->db->trans_complete();
	return $ci->db->trans_status();
}

// Debits a customer's wallet balance for use against an order, guarding
// against a negative balance if two requests race past the earlier
// application-level balance check at the same time.
function debitWallet($userId, $amount, $message)
{
	$ci = &get_instance();
	if ($amount <= 0) {
		return true;
	}
	$ci->db->trans_start();

	$ci->db->set('wallet_amount', 'wallet_amount - ' . (float) $amount, false)
		->where('user_id', $userId)
		->where('wallet_amount >=', $amount)
		->update('user_registration');
	$debited = $ci->db->affected_rows() > 0;

	if ($debited) {
		$ci->db->insert('wallet_history', [
			'user_id' => $userId,
			'amount' => $amount,
			'type' => 2, // debit
			'message' => $message,
			'create_date' => setDateTime(),
		]);
	}

	$ci->db->trans_complete();
	return $debited && $ci->db->trans_status();
}

// Converts a rupee amount to words using the Indian numbering system
// (crore/lakh/thousand), for the "Amount Chargeable (in words)" line on
// invoices. e.g. 169 -> "Rupees One Hundred Sixty Nine Only",
// 150000.50 -> "Rupees One Lakh Fifty Thousand and Fifty Paise Only".
function AmountInWords($amount)
{
	$amount = round((float) $amount, 2);
	$rupees = (int) floor($amount);
	$paise = (int) round(($amount - $rupees) * 100);

	$words = trim(numberToIndianWords($rupees));
	$result = 'Rupees ' . ($words === '' ? 'Zero' : $words);

	if ($paise > 0) {
		$result .= ' and ' . trim(numberToIndianWords($paise)) . ' Paise';
	}

	return $result . ' Only';
}

function numberToIndianWords($number)
{
	$ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
	$tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

	$twoDigits = function ($n) use ($ones, $tens) {
		if ($n < 20) {
			return $ones[$n];
		}
		return trim($tens[intdiv($n, 10)] . ' ' . $ones[$n % 10]);
	};

	$threeDigits = function ($n) use ($ones, $twoDigits) {
		$str = '';
		if ($n >= 100) {
			$str .= $ones[intdiv($n, 100)] . ' Hundred ';
			$n %= 100;
		}
		return trim($str . $twoDigits($n));
	};

	$number = (int) $number;
	if ($number === 0) {
		return '';
	}

	$crore = intdiv($number, 10000000);
	$number %= 10000000;
	$lakh = intdiv($number, 100000);
	$number %= 100000;
	$thousand = intdiv($number, 1000);
	$number %= 1000;
	$hundred = $number;

	$parts = [];
	if ($crore > 0) {
		$parts[] = $threeDigits($crore) . ' Crore';
	}
	if ($lakh > 0) {
		$parts[] = $threeDigits($lakh) . ' Lakh';
	}
	if ($thousand > 0) {
		$parts[] = $threeDigits($thousand) . ' Thousand';
	}
	if ($hundred > 0) {
		$parts[] = $threeDigits($hundred);
	}

	return implode(' ', $parts);
}
