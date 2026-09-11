<?php
include('includes/header-link.php');
include('includes/header.php');
?>



<div class="breadcrumb-block style-shared">
    <div class="breadcrumb-main bg-linear overflow-hidden">
        <div class="container lg:pt-[60px] pt-5 pb-5 relative">
            <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                <div class="text-content">
                    <div class="heading2 text-center">User Profile</div>
                    <div class="link flex items-center justify-center gap-1 caption1 mt-2">
                        <a href="<?= base_url() ?>">Homepage</a>
                        <i class="ph ph-caret-right text-sm text-secondary2"></i>
                        <div class="text-secondary2 capitalize">User Profile</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="my-account-block md:py-20 py-10">
    <div class="container">
        <div class="content-main lg:px-[60px] md:px-4 flex gap-y-8 max-md:flex-col w-full">
            <div class="left md:w-1/3 w-full xl:pr-[3.125rem] lg:pr-[28px] md:pr-[16px]">
                <div class="user-infor bg-surface md:px-8 px-5 md:py-10 py-6 md:rounded-[20px] rounded-xl">
                    <div class="heading flex flex-col items-center justify-center">
                        <div class="avatar">
                            <img src="<?= base_url() ?>assets/img/avatar.png" alt="avatar" class="md:w-[140px] w-[120px] md:h-[140px] h-[120px] rounded-full" />
                        </div>
                        <div class="name heading6 mt-4 text-center"><?= $profileData['name'] ?></div>
                        <div class="mail heading6 font-normal normal-case text-secondary text-center mt-1"><?= $profileData['email_id'] ?></div>
                    </div>
                    <div class="menu-tab list-category w-full max-w-none lg:mt-10 mt-6">
                        <a href="#dashboard" class="category-item flex items-center gap-3 w-full px-5 py-4 rounded-lg cursor-pointer duration-300 hover:bg-white active" data-item="dashboard">
                            <span class="ph ph-house-line text-xl"></span>
                            <strong class="heading6">Dashboard</strong>
                        </a>
                        <a href="#orders" class="category-item flex items-center gap-3 w-full px-5 py-4 rounded-lg cursor-pointer duration-300 hover:bg-white mt-1.5" data-item="orders">
                            <span class="ph ph-package text-xl"></span>
                            <strong class="heading6">Order History</strong>
                        </a>

                        <a href="#edit" class="category-item flex items-center gap-3 w-full px-5 py-4 rounded-lg cursor-pointer duration-300 hover:bg-white mt-1.5" data-item="setting">
                            <span class="ph ph-gear-six text-xl"></span>
                            <strong class="heading6">Settings</strong>
                        </a>
                        <a href="<?= base_url('logout') ?>" onclick="localStorage.removeItem('wishlistStore');" class="category-item flex items-center gap-3 w-full px-5 py-4 rounded-lg cursor-pointer duration-300 hover:bg-white mt-1.5">
                            <span class="ph ph-sign-out text-xl"></span>
                            <strong class="heading6">Logout</strong>
                        </a>
                    </div>
                </div>
            </div>
            <div class="right list-filter md:w-2/3 w-full pl-2.5">
                <div class="filter-item text-content w-full active" data-item="dashboard">
                    <div class="overview grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div class="overview-item flex items-center justify-between p-5 border border-line rounded-lg box-shadow-xs">
                            <div class="counter">
                                <span class="text-secondary">Current Orders</span>
                                <h5 class="heading5 mt-1"><?= $numOfActiveOrders ?></h5>
                            </div>
                            <span class="ph ph-truck text-4xl"></span>
                        </div>
                        <div class="overview-item flex items-center justify-between p-5 border border-line rounded-lg box-shadow-xs">
                            <div class="counter">
                                <span class="text-secondary">Awaiting Pickup</span>
                                <h5 class="heading5 mt-1"><?= $numOfAwaitedOrders ?></h5>
                            </div>
                            <span class="ph ph-hourglass-medium text-4xl"></span>
                        </div>
                        <div class="overview-item flex items-center justify-between p-5 border border-line rounded-lg box-shadow-xs">
                            <div class="counter">
                                <span class="text-secondary">Cancelled Orders</span>
                                <h5 class="heading5 mt-1"><?= $numOfCancelledOrders ?></h5>
                            </div>
                            <span class="ph ph-receipt-x text-4xl"></span>
                        </div>
                        <div class="overview-item flex items-center justify-between p-5 border border-line rounded-lg box-shadow-xs">
                            <div class="counter">
                                <span class="text-secondary">Total Number of Orders</span>
                                <h5 class="heading5 mt-1"><?= $numOfOrders ?></h5>
                            </div>
                            <span class="ph ph-package text-4xl"></span>
                        </div>
                    </div>
                </div>
                <div class="filter-item tab_order text-content overflow-hidden w-full p-7 border border-line rounded-xl" data-item="orders">
                    <h6 class="heading6">Your Orders</h6>
                    <form method="GET" action="<?= base_url('profile') ?>#orders" class="order_date_filter flex flex-wrap items-end gap-4 mt-4">
                        <div>
                            <label for="order_from_date" class="caption1 capitalize">From</label>
                            <input class="border-line mt-2 px-4 py-2 rounded-lg" id="order_from_date" name="from_date" type="date" value="<?= $orderFromDate ?>" max="<?= date('Y-m-d') ?>" />
                        </div>
                        <div>
                            <label for="order_to_date" class="caption1 capitalize">To</label>
                            <input class="border-line mt-2 px-4 py-2 rounded-lg" id="order_to_date" name="to_date" type="date" value="<?= $orderToDate ?>" max="<?= date('Y-m-d') ?>" />
                        </div>
                        <button type="submit" class="button-main py-2">Filter</button>
                        <a href="<?= base_url('profile') ?>#orders" class="button-main bg-surface border border-line text-black py-2">Reset to Current Month</a>
                    </form>
                    <div class="list_order">
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order):
                                $items = $this->CommonModel->getRowById('tbl_book_item', 'product_book_id', $order['product_book_id']);
                            ?>
                                <div class="order_item mt-5 border border-line rounded-lg box-shadow-xs">
                                    <div class="flex flex-wrap items-center justify-between gap-4 p-5 border-b border-line">
                                        <div class="flex items-center gap-2">
                                            <strong class="text-title">Order Number:</strong>
                                            <strong class="order_number text-button uppercase"><?= $order['order_id'] ?></strong>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <strong class="text-title">Order status:</strong>
                                            <span class="tag px-4 py-1.5 rounded-full bg-opacity-10 bg-purple text-purple caption1 font-semibold">
                                                <?php
                                                $statusText = 'Cancelled';
                                                if ($order['booking_status'] == '0') $statusText = 'New Order';
                                                else if ($order['booking_status'] == '1') $statusText = 'Order Accepted';
                                                else if ($order['booking_status'] == '2') $statusText = 'Order Cancelled';
                                                else if ($order['booking_status'] == '3') $statusText = 'Dispatched';
                                                else if ($order['booking_status'] == '4') $statusText = 'Completed';
                                                echo $statusText;
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="list_prd px-5">
                                        <?php foreach ($items as $item):
                                            $getProuctImage = $this->CommonModel->getSingleRowById('tbl_product_image', ['product_id' => $item['product_id']]);
                                            $returnInfo = isReturnEligible($item, $order);
                                        ?>
                                            <div class="prd_item flex flex-wrap items-center justify-between gap-3 py-5 border-b border-line">
                                                <a href="<?= BASE_URL ?>product/<?= url_title($item['product_name'], '-', true) . '-' . $item['product_id'] ?>" class="flex items-center gap-5">
                                                    <div class="bg-img flex-shrink-0 md:w-[100px] w-20 aspect-square rounded-lg overflow-hidden">
                                                        <img src="<?= base_url() ?>upload/product/<?= $getProuctImage['image_path'] ?>" alt="<?= $item['product_name'] ?>" class="w-full h-full object-cover" />
                                                    </div>
                                                    <div>
                                                        <div class="prd_name text-title"><?= $item['product_name'] ?></div>
                                                        <div class="caption1 text-secondary">Size: <?= $item['variant_size'] ?> | Color: <?= $item['variant_color'] ?></div>
                                                    </div>
                                                </a>
                                                <div class="text-title">
                                                    <span class="prd_quantity"><?= $item['no_of_items'] ?></span>
                                                    <span> X </span>
                                                    <span class="prd_price">₹<?= $item['user_price'] ?></span>
                                                </div>
                                            </div>
                                            <?php if ($returnInfo['eligible']): ?>
                                                <div class="return_info_row flex flex-wrap items-center justify-between gap-3 pb-5 border-b border-line">
                                                    <span class="caption1 text-secondary">Return Available for: <?= $returnInfo['days_left'] ?> Day<?= $returnInfo['days_left'] == 1 ? '' : 's' ?> Left</span>
                                                    <button type="button"
                                                        class="button-main btn_return_product"
                                                        book-item-id="<?= $item['book_item_id'] ?>"
                                                        data-product-name="<?= htmlspecialchars($item['product_name']) ?>"
                                                        data-order-number="<?= htmlspecialchars($order['order_id']) ?>"
                                                        data-qty="<?= (int) $item['no_of_items'] ?>"
                                                        data-image="<?= base_url('upload/product/') . ($getProuctImage['image_path'] ?? '') ?>">
                                                        Return Product
                                                    </button>
                                                </div>
                                            <?php elseif ($returnInfo['reason'] === 'already_requested'): ?>
                                                <div class="return_info_row flex flex-wrap items-center justify-between gap-3 pb-5 border-b border-line">
                                                    <span class="tag px-4 py-1.5 rounded-full bg-opacity-10 bg-purple text-purple caption1 font-semibold">Return Status: <?= getReturnStatusLabel($returnInfo['return_status']) ?></span>
                                                    <div class="flex items-center gap-2">
                                                        <button type="button" class="button-main bg-surface border border-line hover:bg-black text-black hover:text-white btn_track_return" return-id="<?= $returnInfo['return_id'] ?>">Track Return</button>
                                                        <a href="<?= base_url('Web/returnSlip/' . $returnInfo['return_id']) ?>" target="_blank" class="button-main bg-surface border border-line hover:bg-black text-black hover:text-white">Download Return Slip</a>
                                                    </div>
                                                </div>
                                            <?php elseif ($returnInfo['reason'] === 'window_expired'): ?>
                                                <div class="return_info_row pb-5 border-b border-line">
                                                    <span class="caption1 text-secondary">Return Window Expired</span>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="flex flex-wrap gap-4 p-5">
                                        <button class="button-main btn_order_detail" order-id="<?= $order['product_book_id'] ?>">Order Details</button>
                                        <?php if (!empty($order['shiprocket_awb_code'])): ?>
                                            <a href="https://shiprocket.co/tracking/<?= $order['shiprocket_awb_code'] ?>" target="_blank" class="button-main bg-surface border border-line hover:bg-black text-black hover:text-white">Track Order</a>
                                        <?php endif; ?>
                                        <!-- <button class="button-main bg-surface border border-line hover:bg-black text-black hover:text-white btn_cancel_order" order-id="<?= $order['product_book_id'] ?>">Cancel Order</button> -->
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>You have no orders.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="filter-item tab_address text-content w-full p-7 border border-line rounded-xl" data-item="address">
                    <form>
                        <button type="button" class="tab_btn flex items-center justify-between w-full pb-1.5 border-b border-line active" data-item="billing">
                            <strong class="heading6">Billing address</strong>
                            <span class="ph ph-caret-down text-2xl ic_down duration-300"></span>
                        </button>
                        <div class="form_address active" data-item="billing">
                            <div class="grid sm:grid-cols-2 gap-4 gap-y-5 mt-5">
                                <div class="first-name">
                                    <label for="billingFirstName" class="caption1 capitalize">First Name <span class="text-red">*</span></label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="billingFirstName" type="text" required />
                                </div>
                                <div class="last-name">
                                    <label for="billingLastName" class="caption1 capitalize">Last Name <span class="text-red">*</span></label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="billingLastName" type="text" required />
                                </div>
                                <div class="company">
                                    <label for="billingCompany" class="caption1 capitalize">Company name (optional)</label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="billingCompany" type="text" required />
                                </div>
                                <div class="country">
                                    <label for="billingCountry" class="caption1 capitalize">Country / Region <span class="text-red">*</span></label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="billingCountry" type="text" required />
                                </div>
                                <div class="street">
                                    <label for="billingStreet" class="caption1 capitalize">street address <span class="text-red">*</span></label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="billingStreet" type="text" required />
                                </div>
                                <div class="city">
                                    <label for="billingCity" class="caption1 capitalize">Town / city <span class="text-red">*</span></label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="billingCity" type="text" required />
                                </div>
                                <div class="state">
                                    <label for="billingState" class="caption1 capitalize">state <span class="text-red">*</span></label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="billingState" type="text" required />
                                </div>
                                <div class="zip">
                                    <label for="billingZip" class="caption1 capitalize">ZIP <span class="text-red">*</span></label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="billingZip" type="text" required />
                                </div>
                                <div class="phone">
                                    <label for="billingPhone" class="caption1 capitalize">Phone <span class="text-red">*</span></label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="billingPhone" type="text" required />
                                </div>
                                <div class="email">
                                    <label for="billingEmail" class="caption1 capitalize">Email <span class="text-red">*</span></label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="billingEmail" type="email" required />
                                </div>
                            </div>
                        </div>
                        <button type="button" class="tab_btn flex items-center justify-between w-full mt-10 pb-1.5 border-b border-line" data-item="shipping">
                            <strong class="heading6">Shipping address</strong>
                            <span class="ph ph-caret-down text-2xl ic_down duration-300"></span>
                        </button>
                        <div class="form_address" data-item="shipping">
                            <div class="grid sm:grid-cols-2 gap-4 gap-y-5 mt-5">
                                <div class="first-name">
                                    <label for="shippingFirstName" class="caption1 capitalize">First Name <span class="text-red">*</span></label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="shippingFirstName" type="text" required />
                                </div>
                                <div class="last-name">
                                    <label for="shippingLastName" class="caption1 capitalize">Last Name <span class="text-red">*</span></label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="shippingLastName" type="text" required />
                                </div>
                                <div class="company">
                                    <label for="shippingCompany" class="caption1 capitalize">Company name (optional)</label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="shippingCompany" type="text" required />
                                </div>
                                <div class="country">
                                    <label for="shippingCountry" class="caption1 capitalize">Country / Region <span class="text-red">*</span></label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="shippingCountry" type="text" required />
                                </div>
                                <div class="street">
                                    <label for="shippingStreet" class="caption1 capitalize">street address <span class="text-red">*</span></label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="shippingStreet" type="text" required />
                                </div>
                                <div class="city">
                                    <label for="shippingCity" class="caption1 capitalize">Town / city <span class="text-red">*</span></label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="shippingCity" type="text" required />
                                </div>
                                <div class="state">
                                    <label for="shippingState" class="caption1 capitalize">state <span class="text-red">*</span></label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="shippingState" type="text" required />
                                </div>
                                <div class="zip">
                                    <label for="shippingZip" class="caption1 capitalize">ZIP <span class="text-red">*</span></label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="shippingZip" type="text" required />
                                </div>
                                <div class="phone">
                                    <label for="shippingPhone" class="caption1 capitalize">Phone <span class="text-red">*</span></label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="shippingPhone" type="text" required />
                                </div>
                                <div class="email">
                                    <label for="shippingEmail" class="caption1 capitalize">Email <span class="text-red">*</span></label>
                                    <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="shippingEmail" type="email" required />
                                </div>
                            </div>
                        </div>
                        <div class="block-button lg:mt-10 mt-6">
                            <button type="submit" class="button-main bg-black">Update Address</button>
                        </div>
                    </form>
                </div>
                <div class="filter-item text-content w-full p-7 border border-line rounded-xl" data-item="setting">
                    <form id="profile-form" method="POST">
                        <div class="formMsg"></div>
                        <div class="heading5 pb-4">Information</div>
                        <div class="grid sm:grid-cols-2 gap-4 gap-y-5 mt-5">
                            <div class="first-name">
                                <label for="firstName" class="caption1 capitalize">First Name <span class="text-red">*</span></label>
                                <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="name" type="text" value="<?= $profileData['name'] ?>" name="name" placeholder="First name" required />
                            </div>
                            <div class="phone-number">
                                <label for="contact_no" class="caption1 capitalize">Phone Number <span class="text-red">*</span></label>
                                <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="number" name="number" type="text" value="<?= $profileData['contact_no'] ?>" placeholder="Phone number" required />
                            </div>
                            <div class="email col-span-full">
                                <label for="email" class="caption1 capitalize">Email Address <span class="text-red">*</span></label>
                                <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="email" name="email" type="email" value="<?= $profileData['email_id'] ?>" placeholder="Email address" required />
                            </div>
                        </div>
                        <div class="block-button lg:mt-10 mt-6">
                            <button class="button-main">Save Change</button>
                        </div>
                    </form>
                    <form id="change-password-form" method="POST" class="mt-10 pt-10 border-t border-line">
                        <div class="formMsg"></div>
                        <div class="heading5 pb-4">Change Password</div>
                        <div class="grid sm:grid-cols-2 gap-4 gap-y-5 mt-5">
                            <div class="current-password col-span-full">
                                <label for="current_password" class="caption1 capitalize">Current Password <span class="text-red">*</span></label>
                                <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="current_password" name="current_password" type="password" placeholder="Current password" required />
                            </div>
                            <div class="new-password">
                                <label for="new_password" class="caption1 capitalize">New Password <span class="text-red">*</span></label>
                                <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="new_password" name="new_password" type="password" placeholder="New password" required />
                            </div>
                            <div class="confirm-password">
                                <label for="confirm_password" class="caption1 capitalize">Confirm New Password <span class="text-red">*</span></label>
                                <input class="border-line mt-2 px-4 py-3 w-full rounded-lg" id="confirm_password" name="confirm_password" type="password" placeholder="Confirm new password" required />
                            </div>
                        </div>
                        <div class="block-button lg:mt-10 mt-6">
                            <button class="button-main">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-order-detail-block flex items-center justify-center">
    <div class="modal-order-detail-main grid md:grid-cols-2 w-[1160px] max-w-[90vw] max-h-[90vh] overflow-x-auto bg-white rounded-2xl">
        <div class="info md:p-10 p-6 md:border-r md:border-line">
            <h5 class="heading5">Order Details</h5>
            <div class="list_info grid grid-cols-2 gap-10 gap-y-8 mt-5">
                <div class="info_item">
                    <strong class="text-button-uppercase text-secondary">Contact Information</strong>
                    <h6 class="heading6 order_name mt-2">Tony nguyen</h6>
                    <h6 class="heading6 order_phone mt-2">(+12) 345 - 678910</h6>
                    <h6 class="heading6 normal-case order_email mt-2">hi.avitex@gmail.com</h6>
                </div>
                <div class="info_item">
                    <strong class="text-button-uppercase text-secondary">Payment method</strong>
                    <h6 class="heading6 order_payment mt-2">cash delivery</h6>
                </div>
                <div class="info_item">
                    <strong class="text-button-uppercase text-secondary">Order Status</strong>
                    <h6 class="heading6 order_status mt-2">New Order</h6>
                </div>
                <div class="info_item">
                    <strong class="text-button-uppercase text-secondary">Shipping address</strong>
                    <h6 class="heading6 order_shipping_address mt-2">2163 Phillips Gap Rd, West Jefferson, North Carolina, US</h6>
                </div>
                <div class="info_item order_coupon_section hidden">
                    <strong class="text-button-uppercase text-secondary">Coupon</strong>
                    <h6 class="heading6 order_coupon mt-2"></h6>
                </div>
                <div class="info_item shiprocket_tracking_section hidden">
                    <strong class="text-button-uppercase text-secondary">Tracking Details</strong>
                    <h6 class="heading6 mt-2">ID: <span class="shiprocket_order_id"></span></h6>
                    <h6 class="heading6 mt-1">AWB: <span class="shiprocket_awb_code"></span></h6>
                    <h6 class="heading6 mt-1 underline"><a href="#" target="_blank" class="shiprocket_tracking_url text-blue-600">Track Shipment</a></h6>
                </div>
            </div>
        </div>
        <div class="list md:p-10 p-6">
            <h5 class="heading5">Items</h5>
            <div class="list_prd list_prd_two modal_order_items">
            </div>
            <div class="flex items-center justify-between mt-5">
                <strong class="text-title">Sub Total</strong>
                <strong class="order_subtotal text-title">₹0</strong>
            </div>
            <div class="flex items-center justify-between mt-4 order_coupon_row hidden">
                <strong class="text-title">Coupon Discount</strong>
                <strong class="order_discounts text-title">-₹0</strong>
            </div>
            <div class="flex items-center justify-between mt-4">
                <strong class="text-title">Shipping</strong>
                <strong class="order_ship text-title">Free</strong>
            </div>
            <div class="flex items-center justify-between mt-4">
                <strong class="text-title">Packaging</strong>
                <strong class="order_packaging text-title">₹0</strong>
            </div>
            <div class="flex items-center justify-between mt-5 pt-5 border-t border-line">
                <h5 class="heading5">Total</h5>
                <h5 class="order_total heading5">₹0</h5>
            </div>
        </div>
    </div>
</div>

<div class="modal-order-detail-block flex items-center justify-center" id="modalReturnRequestBlock">
    <div class="modal-order-detail-main w-[600px] max-w-[90vw] max-h-[90vh] overflow-y-auto bg-white rounded-2xl" id="modalReturnRequestMain">
        <div class="md:p-10 p-6">
            <h5 class="heading5">Request Return</h5>
            <div class="flex items-center gap-4 mt-5 pb-5 border-b border-line">
                <div class="bg-img flex-shrink-0 w-20 aspect-square rounded-lg overflow-hidden">
                    <img id="returnProductImage" src="" alt="" class="w-full h-full object-cover" />
                </div>
                <div>
                    <div class="text-title" id="returnProductName"></div>
                    <div class="caption1 text-secondary">Order Number: <span id="returnOrderNumber"></span></div>
                    <div class="caption1 text-secondary">Quantity Purchased: <span id="returnQtyPurchased"></span></div>
                </div>
            </div>
            <form id="returnRequestForm" class="mt-5">
                <input type="hidden" name="book_item_id" id="returnBookItemId" value="">
                <div class="mt-4">
                    <label class="caption1 capitalize">Quantity to Return <span class="text-red">*</span></label>
                    <input type="number" name="quantity_return" id="returnQuantity" min="1" class="border-line mt-2 px-4 py-3 w-full rounded-lg" required />
                </div>
                <div class="mt-4">
                    <label class="caption1 capitalize">Return Reason <span class="text-red">*</span></label>
                    <select name="reason" id="returnReason" class="border-line mt-2 px-4 py-3 w-full rounded-lg" required>
                        <option value="">Select a reason</option>
                        <option>Wrong Product Received</option>
                        <option>Damaged Product</option>
                        <option>Defective Product</option>
                        <option>Size Issue</option>
                        <option>Color Mismatch</option>
                        <option>Product Not As Expected</option>
                        <option>Missing Parts</option>
                        <option>Other</option>
                    </select>
                </div>
                <div class="mt-4 hidden" id="returnOtherReasonWrap">
                    <label class="caption1 capitalize">Please describe the reason</label>
                    <textarea name="reason_other_text" id="returnOtherReasonText" rows="3" class="border-line mt-2 px-4 py-3 w-full rounded-lg"></textarea>
                </div>
                <div class="mt-4">
                    <label class="caption1 capitalize">Upload Images (JPG, PNG, WEBP - max 5)</label>
                    <input type="file" name="return_images[]" id="returnImages" accept="image/jpeg,image/png,image/webp" multiple class="border-line mt-2 px-4 py-3 w-full rounded-lg" />
                    <div class="caption2 text-secondary mt-1">Up to 5 images, 5 MB each.</div>
                </div>
                <div class="mt-4">
                    <label class="caption1 capitalize">Additional Remarks</label>
                    <textarea name="remarks" id="returnRemarks" rows="3" class="border-line mt-2 px-4 py-3 w-full rounded-lg"></textarea>
                </div>
                <div class="mt-6 flex items-center gap-4">
                    <button type="submit" class="button-main" id="returnSubmitBtn">Submit Request</button>
                    <button type="button" class="button-main bg-surface border border-line text-black" id="returnCancelBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal-order-detail-block flex items-center justify-center" id="modalTrackReturnBlock">
    <div class="modal-order-detail-main w-[600px] max-w-[90vw] max-h-[90vh] overflow-y-auto bg-white rounded-2xl" id="modalTrackReturnMain">
        <div class="md:p-10 p-6">
            <h5 class="heading5">Return Status</h5>
            <div class="caption1 text-secondary mt-2">Return Code: <span id="trackReturnCode"></span></div>
            <div class="caption1 text-secondary mt-1 hidden" id="trackReturnRefund"></div>
            <div class="mt-5" id="trackReturnTimeline"></div>
            <div class="mt-6 flex items-center gap-4">
                <button type="button" class="button-main bg-surface border border-line text-black" id="trackReturnCloseBtn">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Check if the URL has #orders at the end
    if (window.location.hash === '#orders') {
        
        // 2. Find the exact Order History tab button
        let ordersTabButton = document.querySelector('.category-item[data-item="orders"]');
        
        if (ordersTabButton) {
            // 3. Wait a split second for the theme to load, then click the tab
            setTimeout(function() {
                ordersTabButton.click();
                
                // Optional: Smoothly scroll down so the user sees the orders immediately
                document.querySelector('.my-account-block').scrollIntoView({ behavior: 'smooth' });
            }, 150);
        }
    }
});
</script>
<?php
include('includes/footer.php');
include('includes/footer-link.php');
?>