<?php
include('includes/header-link.php');
include('includes/header.php');
?>


<div class="breadcrumb-block style-shared">
    <div class="breadcrumb-main bg-linear overflow-hidden">
        <div class="container lg:pt-[60px] pt-5 pb-5 relative">
            <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                <div class="text-content">
                    <div class="heading2 text-center">Shopping Cart</div>
                    <div class="link flex items-center justify-center gap-1 caption1 mt-2">
                        <a href="index-2.html">Homepage</a>
                        <i class="ph ph-caret-right text-sm text-secondary2"></i>
                        <div class="text-secondary2 capitalize">Shopping Cart</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="cart-block md:py-20 py-10">
    <div class="container">
        <div class="content-main flex justify-between max-xl:flex-col gap-y-8">
            <div class="xl:w-2/3 xl:pr-3 w-full">
                <div class="heading banner mt-5">
                    <div class="text">
                        Buy
                        <span class="text-button"> ₹<span class="more-price">500</span>.00 </span>
                        <span>more to get </span>
                        <span class="text-button">freeship</span>
                    </div>
                    <div class="tow-bar-block mt-4">
                        <div class="progress-line" style="width: 50%"></div>
                    </div>
                </div>
                <div class="list-product w-full sm:mt-7 mt-5">
                    <div class="w-full">
                        <div class="heading bg-surface bora-4 pt-4 pb-4">
                            <div class="flex">
                                <div class="w-1/2">
                                    <div class="text-button text-center">Products</div>
                                </div>
                                <div class="w-1/12">
                                    <div class="text-button text-center">Price</div>
                                </div>
                                <div class="w-1/6">
                                    <div class="text-button text-center">Quantity</div>
                                </div>
                                <div class="w-1/6">
                                    <div class="text-button text-center">Total Price</div>
                                </div>
                            </div>
                        </div>
                        <div class="list-product-main w-full mt-3"></div>
                    </div>
                </div>
                <div class="coupon-block sm:mt-7 mt-5">
                    <div class="flex gap-3">
                        <input type="text" id="couponCodeInput" placeholder="Enter coupon code" class="w-full h-12 bg-surface pl-4 rounded-lg border border-line uppercase" />
                        <button type="button" class="button-main px-5 rounded-lg whitespace-nowrap" onclick="applyPromocode(document.getElementById('couponCodeInput').value)">Apply</button>
                    </div>
                    <div class="coupon-message caption1 text-red mt-2"></div>
                </div>
            </div>
            <div class="xl:w-1/3 xl:pl-12 w-full">
                <div class="checkout-block bg-surface p-6 rounded-2xl">
                    <div class="heading5">Order Summary</div>
                    <div class="total-block py-5 flex justify-between border-b border-line">
                        <div class="text-title">Subtotal</div>
                        <div class="text-title">₹<span class="total-product">0.00</span></div>
                    </div>
                    <div class="discount-block py-5 flex justify-between border-b border-line">
                        <div class="text-title">Discounts</div>
                        <div class="text-title">-₹<span class="discount">0.00</span></div>
                    </div>
                    <div class="ship-block py-5 flex justify-between border-b border-line">
                        <div class="text-title">Shipping</div>
                        <div class="choose-type flex gap-12">
                            <div class="right">
                                <div class="ship">₹0.00</div>
                            </div>
                        </div>
                    </div>
                    <div class="total-cart-block pt-4 pb-4 flex justify-between">
                        <div class="heading5">Total</div>
                        <div class="heading5">₹<span class="total-cart">0.00</span></div>
                    </div>
                    <div class="block-button flex flex-col items-center gap-y-4 mt-5">
                        <a href="<?= base_url('checkout') ?>" class="checkout-btn button-main text-center w-full"> Process To Checkout</a>
                        <a class="text-button hover-underline" href="<?= base_url('products') ?>">Continue shopping </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include('includes/footer.php');
include('includes/footer-link.php');
?>