<?php
include('includes/header-link.php');
include('includes/header.php');
?>

<style>
    .header-logo img {
        width: 70px !important;
    }
</style>
<div class="checkout-block md:py-20 py-10">
    <div class="container">
        <a href="javascript:history.back()" class="checkout-back-btn flex items-center gap-2 mb-5 w-fit">
            <i class="ph ph-arrow-left"></i>
            <div class="text-button">Back</div>
        </a>
        <div class="heading banner mt-5 h-0 w-0 hidden">
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
        <div class="empty-cart-block text-center hidden py-10">
            <div class="icon-cart text-6xl text-secondary mb-4 flex justify-center"><i class="ph ph-handbag text-1xl"></i></div>
            <div class="heading4">Your cart is empty!</div>
            <div class="text-secondary mt-3">You do not have any products in your cart.</div>
            <a href="<?= base_url('products') ?>" class="button-main mt-5 inline-block">Shop Now</a>
        </div>
        <div class="content-main flex max-lg:flex-col-reverse gap-y-10 justify-between" id="checkout-main-content">
            <div class="left lg:w-1/2">
                <?php
                if ($this->session->has_userdata('login_user_id')) {
                ?>
                    <div class="login bg-surface py-3 px-4 rounded-lg">
                        <div class="flex items-center justify-between" style="border-bottom: 1px solid rgba(0,0,0,0.1); margin-bottom: 2px;">
                            <div class="text-on-surface-variant1 pr-4 border-bottom">Account </div>
                            <a href="<?= base_url('logout?redirect=checkout') ?>" class="font-semibold hover:underline">Logout </a>
                        </div>
                        <div class=""><?= htmlspecialchars($profileData['name'] ?? '') ?></div>
                        <div class=""><?= htmlspecialchars($profileData['contact_no'] ?? '') ?></div>
                        <div class=""><?= htmlspecialchars($profileData['email_id'] ?? '') ?></div>
                    </div>
                <?php } ?>
                <div class="information mt-5">
                    <div class="heading5">Information</div>
                    <div class="form-checkout mt-5">
                        <form id="checkout-form" method="POST">

                            <div class="formMsg">
                            </div>
                            <div class="grid sm:grid-cols-2 gap-4 gap-y-5 flex-wrap">
                                <div class="">
                                    <input class="border-line px-4 py-3 w-full rounded-lg" name="name" id="name" type="text" placeholder="Name *" maxlength="100" required value="<?= htmlspecialchars($profileData['name'] ?? '') ?>" />
                                </div>
                                <div class="">
                                    <input class="border-line px-4 py-3 w-full rounded-lg" name="number" id="number" type="tel" inputmode="numeric" pattern="[0-9]{10}" maxlength="10" placeholder="Phone Numbers *" required value="<?= htmlspecialchars($profileData['contact_no'] ?? '') ?>" />
                                </div>
                                <div class="col-span-full">
                                    <input class="border-line px-4 py-3 w-full rounded-lg" name="email" id="email" type="email" maxlength="254" placeholder="Email Address *" required value="<?= htmlspecialchars($profileData['email_id'] ?? '') ?>" />
                                </div>
                                <div class="col-span-full">
                                    <input class="border-line px-4 py-3 w-full rounded-lg" name="address" id="address" type="text" maxlength="150" placeholder="Address *" required />
                                </div>
                                <div class="">
                                    <input class="border-line px-4 py-3 w-full rounded-lg" name="house_no" id="house_no" type="text" maxlength="150" placeholder="House/Flat No., Building Name *" />
                                </div>
                                <div class="">
                                    <input class="border-line px-4 py-3 w-full rounded-lg" name="landmark" id="landmark" type="text" maxlength="150" placeholder="Landmark (optional)" />
                                </div>
                                <div class="">
                                    <input class="border-line px-4 py-3 w-full rounded-lg" name="city" id="city" type="text" maxlength="30" placeholder="City *" required />
                                </div>
                                <div class="">
                                    <input class="border-line px-4 py-3 w-full rounded-lg" name="state" id="state" type="text" maxlength="30" placeholder="State *" required />
                                </div>
                                <div class="">
                                    <input class="border-line px-4 py-3 w-full rounded-lg" name="postal_code" id="postal_code" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="Postal Code *" required />
                                </div>

                                <div class="col-span-full">
                                    <textarea class="border border-line px-4 py-3 w-full rounded-lg" id="note" name="note" maxlength="500" placeholder="Write note..."></textarea>
                                </div>
                            </div>
                            <div class="payment-block md:mt-10 mt-6">
                                <div class="heading5">Choose payment Option:</div>
                                <div class="list-payment mt-5">
                                    <label class="type bg-surface p-5 border border-line cursor-pointer rounded-lg block" for="online-payment">
                                        <input class="cursor-pointer" type="radio" id="online-payment" name="payment" value="0" />
                                        <label class="text-button pl-2 cursor-pointer w-100" for="online-payment">Online Payment</label>
                                    </label>

                                    <label class="type bg-surface p-5 border border-line cursor-pointer rounded-lg mt-5 block" for="cod-payment">
                                        <input class="cursor-pointer" type="radio" id="cod-payment" name="payment" value="1" checked />
                                        <label class="text-button pl-2 w-100 d-block" for="cod-payment">
                                            Cash on Delivery
                                        </label>
                                        <p class="pl-6">Pay with cash when your order is delivered.</p>
                                    </label>
                                </div>
                            </div>

                            <div class="block-button md:mt-10 mt-6">
                                <button id="checkoutbutton" class="button-main w-full">Payment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="right lg:w-5/12">
                <div class="checkout-block">
                    <div class="heading5 pb-3">Your Order</div>
                    <div class="list-product-main list-product-checkout"></div>
                </div>
                <div class="checkout-block bg-surface p-6 rounded-2xl">
                    <div class="heading5">Order Summary</div>
                    <div class="discount-block py-5 flex justify-between border-b border-line">
                        <div class="text-title">Discounts</div>
                        <div class="text-title">-₹<span class="discount">0.00</span></div>
                    </div>
                    <div class="total-block py-5 flex justify-between border-b border-line">
                        <div class="text-title">Subtotal</div>
                        <div class="text-title">₹<span class="total-product">0.00</span></div>
                    </div>
                    <div class="ship-block py-5 flex justify-between border-b border-line">
                        <div class="text-title">Shipping</div>
                        <div class="choose-type flex gap-12">
                            <div class="right">
                                <div class="ship">₹0.00</div>
                            </div>
                        </div>
                    </div>
                    <div class="ship-block py-5 flex justify-between border-b border-line">
                        <div class="text-title">Packaging Charges</div>
                        <div class="choose-type flex gap-12">
                            <div class="right">
                                <div class="packaging">₹9.00</div>
                            </div>
                        </div>
                    </div>
                    <div class="total-cart-block pt-4 pb-4 flex justify-between">
                        <div class="heading5">Total</div>
                        <div class="heading5">₹<span class="total-cart">0.00</span></div>
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
        </div>
    </div>
</div>


<?php
include('includes/footer.php');
include('includes/footer-link.php');
?>

