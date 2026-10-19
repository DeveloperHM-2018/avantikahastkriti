<?php

// One-time codes for the storefront's own (session) auth: OTP login,
// registration, password reset and verifying a new mobile/email on a profile.
// Deliberately separate from tbl_temp_otp, which belongs to the mobile app's
// UserApi login and keeps its codes in plain text with no attempt limit.
//
// A code is tied to the identifier it was sent to and the purpose it was
// requested for, is stored hashed, and dies after TTL_SECONDS, MAX_ATTEMPTS
// wrong guesses, or its first successful use.
class OtpModel extends CI_Model
{
	const TTL_SECONDS = 300;
	const MAX_ATTEMPTS = 5;
	const RESEND_SECONDS = 30;
	const MAX_SENDS = 5; // per identifier, per SEND_WINDOW_SECONDS
	const SEND_WINDOW_SECONDS = 900;
	const MAX_SENDS_PER_IP = 20; // per hour
	const DEV_OTP = '123456';

	public function __construct()
	{
		parent::__construct();
		$this->ensureSchema();
	}

	// $identifier is a parseLoginIdentifier() result. $name only personalises
	// the email. Returns ['success', 'message'] plus 'otp_sent'/'resend_in'.
	public function send(array $identifier, $purpose, $name = '')
	{
		$now = time();
		$this->db->where('create_date <', date('Y-m-d H:i:s', $now - 86400))->delete('auth_otp');

		$recent = $this->db->where('identifier', $identifier['value'])
			->where('create_date >=', date('Y-m-d H:i:s', $now - self::SEND_WINDOW_SECONDS))
			->order_by('id', 'DESC')
			->get('auth_otp')->result_array();
		if ($recent) {
			$wait = self::RESEND_SECONDS - ($now - strtotime($recent[0]['create_date']));
			if ($wait > 0) {
				return ['success' => false, 'message' => "Please wait $wait seconds before requesting another OTP.", 'resend_in' => $wait];
			}
		}
		$ip = $this->input->ip_address();
		$fromIp = $this->db->where('ip_address', $ip)
			->where('create_date >=', date('Y-m-d H:i:s', $now - 3600))
			->count_all_results('auth_otp');
		if (count($recent) >= self::MAX_SENDS || $fromIp >= self::MAX_SENDS_PER_IP) {
			return ['success' => false, 'message' => 'Too many OTP requests. Please try again after some time.'];
		}

		$devMode = otpDevMode();
		$otp = $devMode ? self::DEV_OTP : (string) random_int(100000, 999999);
		$this->db->insert('auth_otp', [
			'create_date' => date('Y-m-d H:i:s', $now),
			'identifier' => $identifier['value'],
			'purpose' => $purpose,
			'otp_hash' => password_hash($otp, PASSWORD_DEFAULT),
			'expires_at' => date('Y-m-d H:i:s', $now + self::TTL_SECONDS),
			'ip_address' => $ip,
		]);

		if ($devMode) {
			return ['success' => true, 'otp_sent' => true, 'resend_in' => self::RESEND_SECONDS, 'message' => 'Dev mode: no message is sent, use OTP ' . self::DEV_OTP . '.'];
		}

		$event = $purpose == 'reset' ? 'forgot_password_otp' : 'login_otp';
		if ($identifier['type'] == 'mobile') {
			$sent = sendTemplatedWhatsapp($event, $identifier['value'], ['otp' => $otp]);
			$failed = 'We could not send the OTP on WhatsApp right now. Please try again, or use your email address instead.';
			$message = 'OTP sent on WhatsApp to ' . maskLoginIdentifier($identifier) . '.';
		} else {
			$sent = sendTemplatedMail($event, $identifier['value'], ['name' => $name ?: 'there', 'otp' => $otp, 'app_name' => APP_NAME]);
			$failed = 'We could not send the OTP email right now. Please try again, or use your mobile number instead.';
			$message = 'OTP sent to ' . maskLoginIdentifier($identifier) . '.';
		}
		if (!$sent) {
			return ['success' => false, 'message' => $failed];
		}
		return ['success' => true, 'otp_sent' => true, 'resend_in' => self::RESEND_SECONDS, 'message' => $message];
	}

	public function verify(array $identifier, $purpose, $otp)
	{
		$otp = trim((string) $otp);
		if (!preg_match('/^\d{6}$/', $otp)) {
			return ['success' => false, 'validation' => false, 'message' => ['otp' => 'Enter the 6-digit OTP sent to you.']];
		}
		$where = ['identifier' => $identifier['value'], 'purpose' => $purpose];
		$row = $this->db->where($where)->order_by('id', 'DESC')->limit(1)->get('auth_otp')->row_array();
		if (!$row) {
			return ['success' => false, 'message' => 'Please request an OTP first.'];
		}
		if (strtotime($row['expires_at']) < time()) {
			return ['success' => false, 'message' => 'OTP has expired. Please request a new one.'];
		}
		if ($row['attempts'] >= self::MAX_ATTEMPTS) {
			return ['success' => false, 'message' => 'Too many incorrect attempts. Please request a new OTP.'];
		}
		if (!password_verify($otp, $row['otp_hash'])) {
			$this->db->set('attempts', 'attempts + 1', false)->where('id', $row['id'])->update('auth_otp');
			return ['success' => false, 'validation' => false, 'message' => ['otp' => 'Incorrect OTP. Please try again.']];
		}
		// Single use - and older codes for the same purpose go with it, so
		// none of them becomes "the latest" again.
		$this->db->where($where)->delete('auth_otp');
		return ['success' => true, 'message' => 'OTP verified.'];
	}

	// The table and the login_otp email template install themselves on first
	// use, so a deploy that only ships code cannot leave login broken.
	private function ensureSchema()
	{
		if ($this->db->table_exists('auth_otp')) {
			return;
		}
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix('auth_otp') . "` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`create_date` datetime NOT NULL,
			`identifier` varchar(200) NOT NULL,
			`purpose` varchar(20) NOT NULL,
			`otp_hash` varchar(255) NOT NULL,
			`attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
			`expires_at` datetime NOT NULL,
			`ip_address` varchar(45) DEFAULT NULL,
			PRIMARY KEY (`id`),
			KEY `identifier` (`identifier`, `purpose`),
			KEY `ip_address` (`ip_address`, `create_date`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
		$this->db->query("INSERT IGNORE INTO `" . $this->db->dbprefix('mail_template') . "` (`event_key`, `subject`, `body`, `is_enabled`) VALUES (
			'login_otp',
			'Your verification code for {{app_name}}',
			'Hi {{name}},<br><br>Your verification code is <b>{{otp}}</b>. It expires in 5 minutes.<br>Please do not share this code with anyone.',
			1
		)");
	}
}
