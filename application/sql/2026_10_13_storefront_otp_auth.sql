-- Storefront OTP login / registration / password reset (2026-10-13)
--
-- Safe to run more than once: it only adds what is missing and changes no
-- existing rows. OtpModel creates the same two things itself on first use,
-- so this file is for servers where the DB user has no CREATE permission,
-- or where you prefer to apply schema changes by hand.

-- One-time codes for the website's OTP flows (see application/models/OtpModel.php).
-- Separate from tbl_temp_otp, which the mobile app keeps using unchanged.
CREATE TABLE IF NOT EXISTS `tbl_auth_otp` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Email sent for login, registration and profile contact-change OTPs.
-- (Password-reset OTPs keep using the existing forgot_password_otp template.)
INSERT IGNORE INTO `tbl_mail_template` (`event_key`, `subject`, `body`, `is_enabled`) VALUES (
  'login_otp',
  'Your verification code for {{app_name}}',
  'Hi {{name}},<br><br>Your verification code is <b>{{otp}}</b>. It expires in 5 minutes.<br>Please do not share this code with anyone.',
  1
);
