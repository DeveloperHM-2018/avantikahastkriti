-- Partner (vendor) "forgot password" via emailed reset link (see
-- Vendor::forgotPassword() / Vendor::resetPassword()). Only a SHA-256 hash of
-- the token is stored, so a DB leak can't be replayed as a reset link; the
-- raw token only ever exists in the email. Single-use: cleared on reset.
-- Idempotent (MariaDB 10.4 IF NOT EXISTS / INSERT IGNORE) - safe to re-run.

ALTER TABLE `tbl_vendor`
  ADD COLUMN IF NOT EXISTS `reset_token_hash` CHAR(64) NULL DEFAULT NULL AFTER `locked_until`,
  ADD COLUMN IF NOT EXISTS `reset_token_expires` DATETIME NULL DEFAULT NULL AFTER `reset_token_hash`,
  ADD INDEX IF NOT EXISTS `idx_reset_token_hash` (`reset_token_hash`);

INSERT IGNORE INTO `tbl_mail_template` (`event_key`, `subject`, `body`, `is_enabled`) VALUES
('vendor_password_reset', 'Reset your {{app_name}} partner password',
 'Hi {{contact_name}},<br><br>We received a request to reset the password for your partner account.<br><br><a href="{{reset_link}}" style="display:inline-block;padding:10px 20px;background:#556ee6;color:#fff;text-decoration:none;border-radius:4px;">Reset Password</a><br><br>Or copy this link into your browser:<br>{{reset_link}}<br><br>This link expires in {{expiry_minutes}} minutes and can only be used once. If you didn''t request this, you can safely ignore this email.', 1);
