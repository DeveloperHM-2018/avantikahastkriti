<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Language every template below is created in on the Hello WhatsApp panel.
$config['whatsapp_language'] = 'en';

// Maps an internal notification event (same keys as tbl_mail_template) to
// the approved WhatsApp template it is sent with.
//   name       - template name exactly as created on the panel
//   params     - placeholder keys, in the order of the template's {{1}}, {{2}}...
//   otp_button - true for AUTHENTICATION templates, whose copy-code button
//                needs the code passed a second time as a button parameter
// Removing (or commenting out) an entry switches that WhatsApp message off
// without touching the email for the same event.
$config['whatsapp_templates'] = [
	// Customer
	'login_otp' => ['name' => 'login_otp', 'params' => ['otp'], 'otp_button' => true],
	'forgot_password_otp' => ['name' => 'login_otp', 'params' => ['otp'], 'otp_button' => true],
	'user_registered_user' => ['name' => 'user_welcome', 'params' => ['name']],
	'order_placed_user' => ['name' => 'order_placed_user', 'params' => ['name', 'order_id', 'final_amount']],
	'order_status_updated_user' => ['name' => 'order_status_update', 'params' => ['name', 'order_id', 'status']],
	'return_requested_user' => ['name' => 'return_requested_user', 'params' => ['name', 'product_name', 'order_id', 'return_code']],
	'return_approved_user' => ['name' => 'return_approved_user', 'params' => ['name', 'return_code', 'order_id']],
	'return_rejected_user' => ['name' => 'return_rejected_user', 'params' => ['name', 'return_code', 'order_id', 'reject_reason']],
	'return_refund_completed_user' => ['name' => 'return_refund_completed', 'params' => ['name', 'refund_amount', 'return_code', 'order_id', 'refund_method']],

	// Vendor / partner
	'vendor_registered_vendor' => ['name' => 'vendor_application_received', 'params' => ['contact_name', 'business_name']],
	'order_placed_vendor' => ['name' => 'vendor_new_order', 'params' => ['vendor_name', 'order_id']],
	'vendor_password_reset' => ['name' => 'vendor_password_reset', 'params' => ['contact_name', 'reset_link', 'expiry_minutes']],

	// Admin (sent to WHATSAPP_ADMIN_NUMBER)
	'user_registered_admin' => ['name' => 'admin_new_user', 'params' => ['name', 'email']],
	'order_placed_admin' => ['name' => 'admin_new_order', 'params' => ['order_id', 'name', 'final_amount']],
	'return_requested_admin' => ['name' => 'admin_return_requested', 'params' => ['return_code', 'name', 'product_name', 'order_id']],
	'vendor_registered_admin' => ['name' => 'admin_new_vendor', 'params' => ['business_name', 'contact_name', 'email']],
];
