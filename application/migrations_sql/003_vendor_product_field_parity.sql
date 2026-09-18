-- Brings the vendor product-submission form up to field parity with the real
-- admin "Add Product" form (category + sub-category + sub-category type,
-- product type, stock status, SEO meta) and adds staged image storage for
-- vendor-submitted product photos, promoted into tbl_product_image on
-- approval (see AdminVendor::vendorProductReview()).
-- Idempotent (MariaDB 10.4 IF NOT EXISTS support) - safe to re-run.

ALTER TABLE `tbl_vendor_product`
  ADD COLUMN IF NOT EXISTS `sub_category_id` VARCHAR(200) NULL DEFAULT NULL AFTER `category_id`,
  ADD COLUMN IF NOT EXISTS `sub_category_type_id` VARCHAR(200) NULL DEFAULT NULL AFTER `sub_category_id`,
  ADD COLUMN IF NOT EXISTS `product_type` TINYINT(1) NOT NULL DEFAULT 1 AFTER `sub_category_type_id`,
  ADD COLUMN IF NOT EXISTS `proposed_market_price` FLOAT NULL DEFAULT NULL AFTER `vendor_supply_price`,
  ADD COLUMN IF NOT EXISTS `is_out_of_stock` TINYINT(1) NOT NULL DEFAULT 0 AFTER `quantity_supplied`,
  ADD COLUMN IF NOT EXISTS `meta_title` VARCHAR(255) NULL DEFAULT NULL AFTER `admin_notes`,
  ADD COLUMN IF NOT EXISTS `meta_description` VARCHAR(500) NULL DEFAULT NULL AFTER `meta_title`,
  ADD COLUMN IF NOT EXISTS `meta_keywords` VARCHAR(500) NULL DEFAULT NULL AFTER `meta_description`,
  -- Tracks how much of quantity_supplied has already been credited to
  -- tbl_product's stock via applyStockRestock(). Vendors can edit an
  -- already-approved submission (see Vendor::productAdd()), which resets it
  -- to Pending for re-review - without this, re-approving that edit would
  -- re-add the full quantity_supplied as if it were new stock every time,
  -- double-counting inventory that was already credited on a prior approval.
  -- AdminVendor::vendorProductReview() only ever adds
  -- (quantity_supplied - quantity_applied) to stock, then sets
  -- quantity_applied = quantity_supplied.
  ADD COLUMN IF NOT EXISTS `quantity_applied` FLOAT NOT NULL DEFAULT 0 AFTER `quantity_supplied`;

-- Backfill: any vendor product already approved before quantity_applied
-- existed has already had its quantity_supplied credited to stock once -
-- mark it applied so a future edit+re-approval only adds the difference,
-- not the full amount again. Safe to re-run.
UPDATE `tbl_vendor_product` SET `quantity_applied` = `quantity_supplied` WHERE `status` = 1;

CREATE TABLE IF NOT EXISTS `tbl_vendor_product_image` (
  `vendor_product_image_id` INT(11) NOT NULL AUTO_INCREMENT,
  `vendor_product_id` INT(11) NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  -- Set once this image has been copied into tbl_product_image on approval -
  -- a vendor can edit+resubmit an already-approved product (see
  -- Vendor::productAdd()), and without this flag every re-approval would
  -- re-copy every staged image again, duplicating the live product's gallery.
  `promoted` TINYINT(1) NOT NULL DEFAULT 0,
  `create_date` DATETIME NOT NULL,
  PRIMARY KEY (`vendor_product_image_id`),
  KEY `idx_vendor_product_image_vp` (`vendor_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Covers the case where this file already ran once before the `promoted`
-- column existed - CREATE TABLE IF NOT EXISTS above is a no-op against an
-- already-created table, so the column needs its own idempotent ALTER.
ALTER TABLE `tbl_vendor_product_image`
  ADD COLUMN IF NOT EXISTS `promoted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `image_path`;
