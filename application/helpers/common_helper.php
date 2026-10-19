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

function sendOTP($contact_no, $otp)
{
	return sendTemplatedWhatsapp('login_otp', $contact_no, ['otp' => $otp]);
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

// Order numbers are shown to customers and used to look orders up, so they
// must not be guessable from the time alone: the timestamp (24-hour clock)
// gets a random 4-digit suffix. The old form, TXN + date('ydmhis'), used a
// 12-hour clock, so it repeated twice a day and could be enumerated, and two
// orders in the same second sent the recursive retry into a tight loop.
function orderIdGenerateUser($table, $column)
{
	return uniqueTimestampCode('TXN', $table, $column);
}

function uniqueTimestampCode($prefix, $table, $column)
{
	for ($attempt = 0; $attempt < 20; $attempt++) {
		$number = $prefix . date('ydmHis') . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
		if (!checkOrderIdExistUser($number, $table, $column)) {
			return $number;
		}
	}
	throw new RuntimeException("Could not generate a unique $column for $table");
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
	// Every caller stores an image under the public upload/ folder, keeping
	// the uploader's file extension - with '*' a vendor, reviewer or app user
	// could upload a .php file there and run it. CI checks both the extension
	// and the sniffed MIME type against config/mimes.php.
	$config['allowed_types'] = 'jpg|jpeg|png|webp|gif';
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

// WhatsApp counterpart of sendTemplatedMail(): same event keys, same
// placeholders. Which approved template an event uses (and the order its
// variables go in) lives in config/whatsapp.php. Never throws - a failed or
// skipped WhatsApp message must not break the order/return flow calling it.
function sendTemplatedWhatsapp($eventKey, $contactNo, $placeholders = [])
{
	$result = whatsappTemplateSend($eventKey, $contactNo, $placeholders);
	if (!$result['success'] && $result['http_code'] !== null) {
		log_message('error', "sendTemplatedWhatsapp: send failed for event '$eventKey' (template '{$result['template']}', HTTP {$result['http_code']}) - " . ($result['curl_error'] ?: $result['response']));
	}
	return $result['success'];
}

// Does the actual send for sendTemplatedWhatsapp() and reports everything that
// happened, so the admin WhatsApp Test page can show the API's exact reply.
// http_code stays null when the message was skipped before any API call, with
// the reason in 'skipped'.
function whatsappTemplateSend($eventKey, $contactNo, $placeholders = [])
{
	$ci = &get_instance();
	$ci->config->load('whatsapp', false, true);
	$templates = (array) $ci->config->item('whatsapp_templates');
	$result = ['success' => false, 'skipped' => '', 'template' => '', 'to' => '', 'http_code' => null, 'request' => null, 'response' => '', 'curl_error' => ''];

	if (notificationsSuppressed()) {
		$result['skipped'] = 'Notifications are suppressed for local testing.';
		return $result;
	}

	if (empty($templates[$eventKey])) {
		$result['skipped'] = "No template is mapped to event '$eventKey' in config/whatsapp.php.";
		return $result;
	}
	$template = $templates[$eventKey];
	$result['template'] = $template['name'];
	if (WHATSAPP_PHONE_NUMBER_ID === '' || WHATSAPP_API_KEY === '') {
		$result['skipped'] = (WHATSAPP_API_KEY === '' ? 'WHATSAPP_API_KEY' : 'WHATSAPP_PHONE_NUMBER_ID') . ' is empty in config/constants.php, so no message is sent.';
		return $result;
	}
	$to = formatWhatsappNumber($contactNo);
	if ($to === false) {
		$result['skipped'] = "'$contactNo' is not a valid mobile number.";
		return $result;
	}
	$result['to'] = $to;

	$parameters = [];
	foreach ($template['params'] as $key) {
		// WhatsApp rejects a template variable that is empty or contains
		// newlines, tabs or runs of spaces.
		$value = trim(preg_replace('/\s+/', ' ', (string) (isset($placeholders[$key]) ? $placeholders[$key] : '')));
		$parameters[] = ['type' => 'text', 'text' => $value !== '' ? $value : '-'];
	}
	$components = [];
	if ($parameters) {
		$components[] = ['type' => 'body', 'parameters' => $parameters];
	}
	if (!empty($template['otp_button'])) {
		$components[] = ['type' => 'button', 'sub_type' => 'url', 'index' => '0', 'parameters' => [$parameters[0]]];
	}

	$result['request'] = [
		'to' => $to,
		'type' => 'template',
		'template' => [
			'name' => $template['name'],
			'language' => ['code' => !empty($template['language']) ? $template['language'] : $ci->config->item('whatsapp_language')],
			'components' => $components,
		],
	];
	$api = whatsappApiRequest('/v1/' . WHATSAPP_PHONE_NUMBER_ID . '/messages', $result['request']);
	$result['http_code'] = $api['http_code'];
	$result['response'] = $api['response'];
	$result['curl_error'] = $api['curl_error'];
	$result['success'] = $api['http_code'] >= 200 && $api['http_code'] < 300;
	return $result;
}

// One call to the Hello WhatsApp API: POST when a payload is given, else GET.
function whatsappApiRequest($path, $payload = null)
{
	$ch = curl_init(WHATSAPP_API_URL . $path);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	if ($payload !== null) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
	}
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . WHATSAPP_API_KEY, 'Content-Type: application/json']);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	$response = curl_exec($ch);
	$api = ['http_code' => (int) curl_getinfo($ch, CURLINFO_HTTP_CODE), 'response' => (string) $response, 'curl_error' => curl_error($ch)];
	curl_close($ch);
	return $api;
}

// The API needs "+<country code><number>"; numbers are stored here as plain
// 10-digit Indian mobiles, so anything without a country code is treated as +91.
function formatWhatsappNumber($contactNo)
{
	$digits = ltrim(preg_replace('/\D+/', '', (string) $contactNo), '0');
	if (strlen($digits) == 10) {
		$digits = '91' . $digits;
	}
	if (strlen($digits) < 11 || strlen($digits) > 15) {
		return false;
	}
	return '+' . $digits;
}

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
	if (notificationsSuppressed()) {
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
	$content = str_replace($search, $replace, $template['body']);
	// Every notification email (vendor registration, order placed, OTP, etc.)
	// shares this one branded HTML wrapper - templates in tbl_mail_template
	// only ever need to hold their own message text, not a full HTML document.
	$body = $ci->load->view('emails/layout', ['subject' => $subject, 'body' => $content], true);

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

// A storefront account is reached by mobile number OR email. Turns whatever
// the customer typed into its stored form (10-digit Indian mobile, lower-case
// email) plus the user_registration column it lives in; false when it is neither.
function parseLoginIdentifier($value)
{
	$value = trim((string) $value);
	if (strpos($value, '@') !== false) {
		if (strlen($value) > 200 || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
			return false;
		}
		return ['type' => 'email', 'value' => strtolower($value), 'column' => 'email_id', 'label' => 'email address'];
	}
	if (!preg_match('/^[\d\s+()-]+$/', $value)) {
		return false;
	}
	$digits = preg_replace('/\D+/', '', $value);
	if (strlen($digits) == 12 && substr($digits, 0, 2) == '91') {
		$digits = substr($digits, 2);
	} elseif (strlen($digits) == 11 && $digits[0] == '0') {
		$digits = substr($digits, 1);
	}
	if (!preg_match('/^[6-9]\d{9}$/', $digits)) {
		return false;
	}
	return ['type' => 'mobile', 'value' => $digits, 'column' => 'contact_no', 'label' => 'mobile number'];
}

// "+91 ******3210" / "ha***@gmail.com" - for "OTP sent to ..." messages.
function maskLoginIdentifier($identifier)
{
	if ($identifier['type'] == 'mobile') {
		return '+91 ******' . substr($identifier['value'], -4);
	}
	list($local, $domain) = explode('@', $identifier['value'], 2);
	return substr($local, 0, 2) . '***@' . $domain;
}

// Accounts created by OTP sign-up, the mobile app or guest checkout have no
// password of their own. Older guest-checkout accounts were given the shared
// dummy password "123456", which must never work as a real credential.
function userHasPassword($user)
{
	return !empty($user['password']) && !password_verify('123456', $user['password']);
}

// Admin "customer mobile / email" filters. A customer may have only one of
// the two, and what they typed on an order can differ from what is on their
// account, so a filter matches the value in either place. $columns are the
// places to look, e.g. ['book_product.contact_no', 'user_registration.contact_no'].
// Returns an " AND (...)" fragment for the listing helpers' raw where string.
function customerContactFilterSql($value, $columns, $isMobile = false)
{
	$value = trim((string) $value);
	if ($isMobile) {
		// "+91 98765 43210" and "9876543210" should find the same customer.
		$value = preg_replace('/\D+/', '', $value);
		if (strlen($value) == 12 && substr($value, 0, 2) == '91') {
			$value = substr($value, 2);
		}
	}
	if ($value === '') {
		return '';
	}
	$ci = &get_instance();
	$like = " LIKE '%" . $ci->db->escape_like_str($value) . "%'";
	return ' AND (' . implode($like . ' OR ', $columns) . $like . ')';
}

// "9876543210 / a@b.com", either one alone, or a dash - for admin listings.
function customerContactLabel($contactNo, $email, $separator = ' / ')
{
	$parts = array_filter([trim((string) $contactNo), trim((string) $email)], 'strlen');
	return $parts ? implode($separator, $parts) : '-';
}

// The account an OTP-verified password reset is in progress for, while the
// short window opened by Auth::forgotPassword() is still open.
function passwordResetUserId()
{
	$ci = &get_instance();
	$userId = $ci->session->userdata('reset_user_id');
	return $userId && $ci->session->userdata('reset_expires') >= time() ? $userId : false;
}

// Fixed-OTP shortcut for a developer machine, where no WhatsApp/SMTP gateway
// is needed to test. OTP_ENABLE alone is decided by the Host header, which a
// client controls, so the request must also come from this machine itself.
function otpDevMode()
{
	$remote = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
	return !OTP_ENABLE && in_array($remote, ['127.0.0.1', '::1'], true);
}

// Lets automated tests on a developer machine drive real order/payment flows
// without WhatsApp/email going to real customers or the admin number: only
// honoured for loopback requests, and only while the flag file exists.
function notificationsSuppressed()
{
	$remote = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
	return in_array($remote, ['127.0.0.1', '::1'], true) && file_exists(APPPATH . 'cache/suppress_notifications');
}

function generateOrderId($id)
{
	return '#ATP' . str_pad($id, 4, '0', STR_PAD_LEFT);
}

// === Product Return Management ===

function returnCodeGenerate()
{
	return uniqueTimestampCode('RET', 'return_request', 'return_code');
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
