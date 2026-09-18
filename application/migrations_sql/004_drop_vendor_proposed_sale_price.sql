-- Vendors no longer propose a sale price (only Market Price/MRP and their own
-- supply price) - the actual sale price is entirely admin's call, now
-- auto-calculated from commission % on the review screen (see
-- admin/vendor/vendor_product_review.php) and stored only on the live
-- tbl_product.sale_price once approved. Drops the now-unused column.
-- Idempotent (MariaDB 10.4 IF EXISTS support) - safe to re-run.

ALTER TABLE `tbl_vendor_product`
  DROP COLUMN IF EXISTS `proposed_sale_price`;
