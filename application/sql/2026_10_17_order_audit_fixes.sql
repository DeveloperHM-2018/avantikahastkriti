-- Order / payment / settlement audit fixes (2026-10-17)
--
-- Safe to run more than once (MariaDB 10.0+ IF [NOT] EXISTS syntax) and
-- changes no existing rows. Run this check first - the index can't be added
-- while two orders share a number:
--   SELECT order_id, COUNT(*) c FROM tbl_book_product GROUP BY order_id HAVING c > 1;

-- Order numbers are how customers, the thank-you page and the Razorpay
-- promotion callbacks find an order, so two orders must never share one.
-- orderIdGenerateUser() now adds a random suffix, but its check-then-insert
-- still leaves a tiny race; the database is the real guarantee.
ALTER TABLE `tbl_book_product` DROP INDEX IF EXISTS `idx_book_product_order_id`;
ALTER TABLE `tbl_book_product` ADD UNIQUE INDEX IF NOT EXISTS `uniq_book_product_order_id` (`order_id`);

-- No schema change, documented here so it is found with the rest:
--   tbl_vendor_order_item.payout_status gains value 3 = void (order cancelled
--     or item returned before payout) - see VENDOR_LINE_* in config/constants.php.
--   tbl_book_item.stock_applied gains value 2 = handled but no stock taken
--     (oversold line pinned for review) - see CommonModel::decrementOrderStock().
