-- Phase 1: Stock safety (atomic decrement/restock + ledger) + admin activity log.
-- Idempotent (MariaDB 10.4 IF NOT EXISTS support) - safe to re-run.
-- Run against a local/staging copy first, then production during a maintenance window.

-- ---------------------------------------------------------------
-- tbl_product
-- ---------------------------------------------------------------
ALTER TABLE `tbl_product`
  ADD COLUMN IF NOT EXISTS `low_stock_threshold` FLOAT NULL DEFAULT NULL AFTER `is_out_of_stock`,
  ADD COLUMN IF NOT EXISTS `is_out_of_stock_override` TINYINT(1) NULL DEFAULT NULL COMMENT 'Admin-forced override; when set, wins over the automatic quantity-derived flag' AFTER `low_stock_threshold`,
  ADD COLUMN IF NOT EXISTS `stock_version` INT(11) NOT NULL DEFAULT 0 AFTER `is_out_of_stock_override`,
  ADD INDEX IF NOT EXISTS `idx_product_out_of_stock` (`is_out_of_stock`),
  ADD INDEX IF NOT EXISTS `idx_product_quantity` (`quantity`);

-- ---------------------------------------------------------------
-- tbl_product_variants
-- ---------------------------------------------------------------
ALTER TABLE `tbl_product_variants`
  ADD COLUMN IF NOT EXISTS `low_stock_threshold` FLOAT NULL DEFAULT NULL AFTER `stock_quantity`,
  ADD INDEX IF NOT EXISTS `idx_variant_product_active` (`product_id`, `is_active`);

-- ---------------------------------------------------------------
-- tbl_book_product (orders)
-- ---------------------------------------------------------------
ALTER TABLE `tbl_book_product`
  ADD COLUMN IF NOT EXISTS `order_source` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=web,1=mobile_app,2=magic_checkout,3=admin_manual' AFTER `is_seen`,
  ADD COLUMN IF NOT EXISTS `created_by_admin_id` INT(11) NULL DEFAULT NULL AFTER `order_source`,
  ADD COLUMN IF NOT EXISTS `stock_flag` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=ok,1=pinned_for_review' AFTER `created_by_admin_id`,
  ADD COLUMN IF NOT EXISTS `stock_flag_reason` VARCHAR(255) NULL DEFAULT NULL AFTER `stock_flag`,
  ADD COLUMN IF NOT EXISTS `stock_flag_resolved_by` INT(11) NULL DEFAULT NULL AFTER `stock_flag_reason`,
  ADD COLUMN IF NOT EXISTS `stock_flag_resolved_date` DATETIME NULL DEFAULT NULL AFTER `stock_flag_resolved_by`,
  ADD COLUMN IF NOT EXISTS `stock_flag_resolution` VARCHAR(255) NULL DEFAULT NULL AFTER `stock_flag_resolved_date`,
  ADD INDEX IF NOT EXISTS `idx_book_product_user` (`user_id`),
  ADD INDEX IF NOT EXISTS `idx_book_product_status` (`booking_status`),
  ADD INDEX IF NOT EXISTS `idx_book_product_txn_status` (`transaction_status`),
  ADD INDEX IF NOT EXISTS `idx_book_product_order_id` (`order_id`),
  ADD INDEX IF NOT EXISTS `idx_book_product_booking_date` (`booking_date`),
  ADD INDEX IF NOT EXISTS `idx_book_product_stock_flag` (`stock_flag`);

-- ---------------------------------------------------------------
-- tbl_book_item (order lines)
-- ---------------------------------------------------------------
ALTER TABLE `tbl_book_item`
  ADD COLUMN IF NOT EXISTS `stock_applied` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Set once this line has actually decremented product/variant stock - idempotency guard against double-decrement on a retried confirmation' AFTER `quantity`,
  ADD INDEX IF NOT EXISTS `idx_book_item_product_book` (`product_book_id`),
  ADD INDEX IF NOT EXISTS `idx_book_item_product` (`product_id`);

-- ---------------------------------------------------------------
-- tbl_stock_ledger (new) - append-only history of every stock mutation
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_stock_ledger` (
  `ledger_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `variant_id` INT(11) NULL DEFAULT NULL,
  `vendor_id` INT(11) NULL DEFAULT NULL,
  `change_type` VARCHAR(30) NOT NULL,
  `delta` FLOAT NOT NULL,
  `balance_after` FLOAT NOT NULL,
  `reference_type` VARCHAR(30) NULL DEFAULT NULL,
  `reference_id` INT(11) NULL DEFAULT NULL,
  `note` VARCHAR(500) NULL DEFAULT NULL,
  `changed_by_type` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0=system/customer-order,1=admin,2=vendor',
  `changed_by_id` INT(11) NULL DEFAULT NULL,
  `create_date` DATETIME NOT NULL,
  PRIMARY KEY (`ledger_id`),
  KEY `idx_ledger_product` (`product_id`, `create_date`),
  KEY `idx_ledger_variant` (`variant_id`, `create_date`),
  KEY `idx_ledger_vendor` (`vendor_id`, `create_date`),
  KEY `idx_ledger_reference` (`reference_type`, `reference_id`),
  KEY `idx_ledger_change_type` (`change_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- tbl_admin_activity_log (new) - who did what, when
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_admin_activity_log` (
  `log_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` INT(11) NULL DEFAULT NULL,
  `actor_type` TINYINT(4) NOT NULL DEFAULT 1 COMMENT '1=admin/sub-admin,2=vendor',
  `vendor_id` INT(11) NULL DEFAULT NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT(11) NULL DEFAULT NULL,
  `before_data` MEDIUMTEXT NULL DEFAULT NULL,
  `after_data` MEDIUMTEXT NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `create_date` DATETIME NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `idx_activity_admin` (`admin_id`, `create_date`),
  KEY `idx_activity_entity` (`entity_type`, `entity_id`),
  KEY `idx_activity_action` (`action`),
  KEY `idx_activity_create_date` (`create_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
