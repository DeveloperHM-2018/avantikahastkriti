-- Phase 3: Vendor module (registration/login, staged product submissions,
-- restocks, per-line commission snapshot + payouts).
-- Idempotent (MariaDB 10.4 IF NOT EXISTS support) - safe to re-run.

CREATE TABLE IF NOT EXISTS `tbl_vendor` (
  `vendor_id` INT(11) NOT NULL AUTO_INCREMENT,
  `business_name` VARCHAR(150) NOT NULL,
  `contact_name` VARCHAR(100) NOT NULL,
  `contact_no` VARCHAR(20) NOT NULL,
  `email_id` VARCHAR(191) NOT NULL,
  `password` VARCHAR(191) NOT NULL,
  `gst_number` VARCHAR(20) NULL DEFAULT NULL,
  `pan_number` VARCHAR(20) NULL DEFAULT NULL,
  `address` VARCHAR(255) NULL DEFAULT NULL,
  `city` VARCHAR(50) NULL DEFAULT NULL,
  `state` VARCHAR(50) NULL DEFAULT NULL,
  `postal_code` VARCHAR(10) NULL DEFAULT NULL,
  `bank_account_name` VARCHAR(150) NULL DEFAULT NULL,
  `bank_account_no` VARCHAR(30) NULL DEFAULT NULL,
  `bank_ifsc` VARCHAR(15) NULL DEFAULT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=pending_approval,1=active,2=rejected,3=suspended',
  `approved_by` INT(11) NULL DEFAULT NULL,
  `approved_date` DATETIME NULL DEFAULT NULL,
  `default_commission_percent` DECIMAL(5,2) NULL DEFAULT NULL,
  `failed_login_count` INT(11) NOT NULL DEFAULT 0,
  `locked_until` DATETIME NULL DEFAULT NULL,
  `create_date` DATETIME NOT NULL,
  `update_date` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`vendor_id`),
  UNIQUE KEY `uniq_vendor_email` (`email_id`),
  UNIQUE KEY `uniq_vendor_contact` (`contact_no`),
  KEY `idx_vendor_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tbl_vendor_document` (
  `document_id` INT(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` INT(11) NOT NULL,
  `document_type` VARCHAR(30) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `verified` TINYINT(1) NOT NULL DEFAULT 0,
  `create_date` DATETIME NOT NULL,
  PRIMARY KEY (`document_id`),
  KEY `idx_vendor_doc_vendor` (`vendor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tbl_vendor_product` (
  `vendor_product_id` INT(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` INT(11) NOT NULL,
  `product_id` INT(11) NULL DEFAULT NULL,
  `product_name` VARCHAR(191) NOT NULL,
  `category_id` INT(11) NULL DEFAULT NULL,
  `sub_category_id` VARCHAR(200) NULL DEFAULT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `vendor_supply_price` FLOAT NOT NULL,
  `proposed_sale_price` FLOAT NULL DEFAULT NULL,
  `commission_percent` DECIMAL(5,2) NULL DEFAULT NULL,
  `quantity_supplied` FLOAT NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=pending,1=approved,2=rejected,3=changes_requested',
  `admin_notes` TEXT NULL DEFAULT NULL,
  `reviewed_by` INT(11) NULL DEFAULT NULL,
  `reviewed_date` DATETIME NULL DEFAULT NULL,
  `create_date` DATETIME NOT NULL,
  `update_date` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`vendor_product_id`),
  KEY `idx_vendor_product_vendor` (`vendor_id`),
  KEY `idx_vendor_product_status` (`status`),
  KEY `idx_vendor_product_linked` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tbl_vendor_supply` (
  `supply_id` INT(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `variant_id` INT(11) NULL DEFAULT NULL,
  `quantity` FLOAT NOT NULL,
  `vendor_supply_price` FLOAT NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=pending,1=accepted_into_stock,2=rejected',
  `reviewed_by` INT(11) NULL DEFAULT NULL,
  `reviewed_date` DATETIME NULL DEFAULT NULL,
  `create_date` DATETIME NOT NULL,
  PRIMARY KEY (`supply_id`),
  KEY `idx_vendor_supply_vendor` (`vendor_id`, `create_date`),
  KEY `idx_vendor_supply_product` (`product_id`, `create_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tbl_vendor_order_item` (
  `vendor_order_item_id` INT(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` INT(11) NOT NULL,
  `book_item_id` INT(11) NOT NULL,
  `product_book_id` INT(11) NOT NULL,
  `quantity` FLOAT NOT NULL,
  `vendor_supply_price` FLOAT NOT NULL,
  `commission_percent` DECIMAL(5,2) NOT NULL,
  `commission_amount` DECIMAL(10,2) NOT NULL,
  `vendor_payable_amount` DECIMAL(10,2) NOT NULL,
  `payout_status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=unpaid,1=included_in_payout,2=paid',
  `payout_id` INT(11) NULL DEFAULT NULL,
  `create_date` DATETIME NOT NULL,
  PRIMARY KEY (`vendor_order_item_id`),
  UNIQUE KEY `uniq_vendor_order_book_item` (`book_item_id`),
  KEY `idx_vendor_order_vendor` (`vendor_id`, `create_date`),
  KEY `idx_vendor_order_payout_status` (`vendor_id`, `payout_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tbl_vendor_payout` (
  `payout_id` INT(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` INT(11) NOT NULL,
  `period_from` DATE NOT NULL,
  `period_to` DATE NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=pending,1=paid,2=failed',
  `payment_reference` VARCHAR(150) NULL DEFAULT NULL,
  `processed_by` INT(11) NULL DEFAULT NULL,
  `processed_date` DATETIME NULL DEFAULT NULL,
  `create_date` DATETIME NOT NULL,
  PRIMARY KEY (`payout_id`),
  KEY `idx_vendor_payout_vendor` (`vendor_id`, `period_from`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Track which vendor most recently supplied a product's stock, so a decrement
-- at sale time knows which vendor to attribute the sale to (see
-- CommonModel::applyStockDecrement()'s vendor_id context / AdminVendor).
ALTER TABLE `tbl_product`
  ADD COLUMN IF NOT EXISTS `default_vendor_id` INT(11) NULL DEFAULT NULL AFTER `stock_version`,
  ADD INDEX IF NOT EXISTS `idx_product_default_vendor` (`default_vendor_id`);
