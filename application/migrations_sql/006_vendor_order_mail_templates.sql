-- New mail_template event_keys for vendor registration (to the applicant and
-- to admin) and order-placed notifications to the vendor whose stock a sale
-- was attributed to (see CommonModel::attributeVendorSale() /
-- notifyVendorsOrderPlaced()). All bodies are rendered inside the shared
-- application/views/emails/layout.php wrapper by sendTemplatedMail(), so
-- they only need their own message text, not a full HTML document.
-- Idempotent: INSERT IGNORE is a no-op if the event_key already exists
-- (event_key has a UNIQUE key) - safe to re-run.

INSERT IGNORE INTO `tbl_mail_template` (`event_key`, `subject`, `body`, `is_enabled`) VALUES
('vendor_registered_vendor', 'Your partner application to {{app_name}} has been received',
 'Hi {{contact_name}},<br><br>Thanks for registering <b>{{business_name}}</b> as a partner on {{app_name}}. Our team will review your application and get back to you shortly.', 1),

('vendor_registered_admin', 'New partner application: {{business_name}}',
 'A new vendor application has been submitted.<br><br>Business Name: <b>{{business_name}}</b><br>Contact Person: {{contact_name}}<br>Email: {{email}}<br><br>Please review it in the admin panel.', 1),

('order_placed_vendor', 'New order for your product - {{order_id}}',
 'Hi {{vendor_name}},<br><br>A new order <b>{{order_id}}</b> includes one or more of your products. Please check your partner dashboard on {{app_name}} for the details.', 1);
