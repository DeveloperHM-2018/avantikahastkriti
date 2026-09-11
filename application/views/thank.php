<?php
include('includes/header-link.php');
include('includes/header.php');
?>

<style>
    .header-logo {
        margin-top: 0px;
    }

    .header-logo img {
        width: 70px !important;
    }
</style>

<div class="thankyou-block md:py-20 py-10 pb-30">
    <div class="container">
        <div class="content-main flex max-lg:flex-col-reverse  gap-y-10 justify-between">
            <div class="left lg:w-1/2">
                <div class="information">
                    <div class="heading5">Order Confirmation</div>
                    <div class="order-id-wrapper flex items-center gap-3 mt-5">
                        <div class="checkIcon">
                            <i class="ph ph-check-circle" style="color: #64c364"></i>
                        </div>
                        <div class="order-id-label">
                            <div class="text-secondary relative">
                                Order Id: <span id="order-id"><?= $order['id'] ?></span>&nbsp;
                                <i class="ph ph-copy-simple" id="copy-order-id" style="cursor: pointer; cursor: pointer; position: absolute; top: -4px;"></i>
                            </div>
                            <h1 class="heading6">Thank You <?= htmlspecialchars($orderData['name']) ?>!</h1>
                        </div>
                    </div>
                    <div class="order-alerts border-line border p-4 px-6 rounded mt-10">
                        <div class="heading-st-6">Order Updates</div>
                        <p class="text-secondary">You Will recieve order and shipping updates via email.</p>
                    </div>
                    <div class="order-contact-information border-line border mt-10 rounded">
                        <div class="information-box border-line border-b py-3 px-5 flex">
                            <span class="key text-secondary">Contact</span>
                            <span class="value text-secondary"><?= htmlspecialchars($orderData['contact_no']) ?></span>
                        </div>
                        <div class="information-box border-line border-b py-3 px-5 flex">
                            <span class="key text-secondary">Address</span>
                            <span class="value text-secondary"><?= htmlspecialchars($orderData['address']) ?></span>
                        </div>
                        <div class="information-box py-3 px-5 flex">
                            <span class="key text-secondary">Payment</span>
                            <span class="value text-secondary"><?= htmlspecialchars($orderData['payment_mode']) ?></span>
                        </div>
                    </div>
                    <?php if (!empty($orderData['note'])): ?>
                        <div class="order-alerts border-line border p-4 px-6 rounded mt-10">
                            <div class="heading-st-6">Special Request</div>
                            <p class="text-secondary"><?= nl2br(htmlspecialchars($orderData['note'])) ?></p>
                        </div>
                    <?php endif; ?>
                    <div class="flex items-center justify-between mt-5">
                        <div style="font-size: 14px;">
                            <span><i class="ph ph-info"></i>&nbsp;Need Help?</span>&nbsp;<a href="<?= base_url('contact') ?>" class="font-semibold underline">Contact us </a>
                        </div>
                        <div>
                            <a href="<?= base_url('products') ?>" class="button-main w-full">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="right lg:w-5/12">
                <div class="checkout-block">
                    <div class="heading5 pb-3">Ordered Items</div>
                    <div class="list-product-main ">
                        <?php $grandTotal = 0;
                        foreach ($order['products'] as $product):
                            $productImage = $this->CommonModel->getSingleRowById('product_image', ['product_id' => $product['product_id']]);
                            $grandTotal += $product['booking_price'];
                        ?>
                            <div class="item flex items-center justify-between w-full pb-5 border-b border-line gap-6 mt-5">
                                <div class="bg-img w-[100px] aspect-square flex-shrink-0 rounded-lg overflow-hidden">
                                    <img src="<?= setImage($productImage['image_path'], PRODUCT_IMAGE) ?>" alt="img" class="w-full h-full">
                                </div>
                                <div class="flex items-center justify-between w-full">
                                    <div>
                                        <div class="name text-title"><?= htmlspecialchars($product['product_name']) ?></div>
                                        <div class="caption1 text-secondary">
                                            <div class="caption1 text-secondary">Size: <?= htmlspecialchars($product['variant_size']) ?> | Color: <?= htmlspecialchars($product['variant_color']) ?></div>
                                            <span class="size capitalize">₹<?= number_format((float) $product['user_price'], 2) ?></span>
                                        </div>
                                    </div>

                                    <div class="text-title">
                                        <span class="quantity text-secondary" style="font-size: 14px;"><?= $product['no_of_items'] ?></span>
                                        <span class="px-1">x</span>
                                        <span>₹<?= number_format((float) $product['booking_price'], 2) ?></span>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="discount-block py-5 flex justify-between border-b border-line">
                        <div class="text-title">Sub Total</div>
                        <div class="text-title">₹<?= number_format((float) $orderData['total_item_amount'], 2) ?></div>
                    </div>
                    <?php if ($order['coupon_discount'] > 0): ?>
                        <div class="discount-block py-5 flex justify-between border-b border-line">
                            <div class="text-title">Coupon <span class="text-secondary">(<?= htmlspecialchars($order['coupon_code']) ?>)</span></div>
                            <div class="text-title">- ₹<?= number_format((float) $order['coupon_discount'], 2) ?></div>
                        </div>
                    <?php endif; ?>
                    <div class="discount-block py-5 flex justify-between border-b border-line">
                        <div class="text-title">Shipping Charges</div>
                        <div class="text-title"><?= $order['shipping_charge'] == 0 ? 'Free' : '₹' . number_format((float) $order['shipping_charge'], 2) ?></div>
                    </div>
                    <div class="discount-block py-5 flex justify-between border-b border-line">
                        <div class="text-title">Packaging Charges</div>
                        <div class="text-title">₹<?= number_format((float) $order['packaging_charge'], 2) ?></div>
                    </div>
                    <div class="total-cart-block pt-5 flex justify-between">
                        <div class="heading5">Total</div>
                        <div class="heading5 total-cart">₹<?= number_format((float) $order['total_price'], 2) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($order): ?>
    <script>
        // Guarded by a localStorage flag keyed on order id so refreshing or
        // revisiting this page (e.g. via back button) doesn't re-fire Purchase
        // and inflate reported revenue.
        (function() {
            var orderId = <?= json_encode($order['id']) ?>;
            var dedupeKey = 'fbq_purchase_' + orderId;
            if (window.fbq && orderId && !localStorage.getItem(dedupeKey)) {
                fbq('track', 'Purchase', {
                    value: <?= json_encode((float) $order['total_price']) ?>,
                    currency: 'INR',
                    content_ids: <?= json_encode(array_map(function ($p) {
                                        return (string) $p['product_id'];
                                    }, $order['products'])) ?>,
                    content_type: 'product',
                    num_items: <?= json_encode((int) array_sum(array_column($order['products'], 'no_of_items'))) ?>,
                });
                localStorage.setItem(dedupeKey, '1');
            }
        })();
    </script>
<?php endif; ?>

<?php
include('includes/footer.php');
include('includes/footer-link.php');
?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const copyIcon = document.getElementById("copy-order-id");
        const orderIdElement = document.getElementById("order-id");

        copyIcon.addEventListener("click", function() {
            const orderId = orderIdElement.textContent;
            copyToClipboard(orderId);

            // Change icon for touch feedback
            copyIcon.classList.remove("ph-copy-simple");
            copyIcon.classList.add("ph-check-circle");

            // Revert icon back after 2 seconds
            setTimeout(() => {
                copyIcon.classList.remove("ph-check-circle");
                copyIcon.classList.add("ph-copy-simple");
            }, 2000);

            // alert(": " + orderId);
            Toast.show("Order ID copied to clipboard", {
                type: "success",
                icon: true,
                duration: 4000,
            });
        });

        function copyToClipboard(text) {
            const textarea = document.createElement("textarea");
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand("copy");
            document.body.removeChild(textarea);
        }
    });
</script>