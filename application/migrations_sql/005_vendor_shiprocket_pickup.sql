-- Vendor warehouse/pickup address (for Shiprocket order sync) + the house's
-- own default pickup location nickname. Shiprocket order creation requires a
-- `pickup_location` value matching a location already registered by name in
-- the Shiprocket account - this doesn't register anything automatically
-- (the Shiprocket API used here has no such endpoint), it only stores the
-- address for reference and the exact nickname admin registers on their end.
-- Idempotent (MariaDB 10.4 IF NOT EXISTS support) - safe to re-run.

ALTER TABLE `tbl_vendor`
  ADD COLUMN IF NOT EXISTS `pickup_address` VARCHAR(255) NULL DEFAULT NULL AFTER `postal_code`,
  ADD COLUMN IF NOT EXISTS `pickup_city` VARCHAR(100) NULL DEFAULT NULL AFTER `pickup_address`,
  ADD COLUMN IF NOT EXISTS `pickup_state` VARCHAR(100) NULL DEFAULT NULL AFTER `pickup_city`,
  ADD COLUMN IF NOT EXISTS `pickup_pincode` VARCHAR(10) NULL DEFAULT NULL AFTER `pickup_state`,
  ADD COLUMN IF NOT EXISTS `pickup_phone` VARCHAR(20) NULL DEFAULT NULL AFTER `pickup_pincode`,
  -- Admin-only (set from Vendors > Edit Vendor, not vendor-editable) - must
  -- exactly match a pickup location nickname already registered for this
  -- vendor in the Shiprocket dashboard. NULL until admin registers one there
  -- and fills it in; order sync falls back to the house default until then.
  ADD COLUMN IF NOT EXISTS `shiprocket_pickup_nickname` VARCHAR(100) NULL DEFAULT NULL AFTER `pickup_phone`;

ALTER TABLE `tbl_setting`
  ADD COLUMN IF NOT EXISTS `shiprocket_pickup_nickname` VARCHAR(100) NOT NULL DEFAULT 'work' AFTER `warehouse_email`;
