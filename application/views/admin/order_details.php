<?php if ($type == 1) { ?>
    <div class="row">
        <div class="col-lg-6">
            <div class="row">
                <div class="col-lg-4">
                    <h5>Name</h5>
                </div>
                <div class="col-lg-6">
                    <h6><?= htmlspecialchars($all_details['name']) ?></h6>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <h5>Phone</h5>
                </div>
                <div class="col-lg-6">
                    <h6><?= htmlspecialchars($all_details['contact_no']) ?></h6>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <h5>Address</h5>
                </div>
                <div class="col-lg-6">
                    <?php
                    // We no longer capture a map pin at checkout (address is
                    // typed in manually now), so latitude/longitude are just
                    // placeholder '0' values on every order placed since that
                    // change - linking to them would point at the middle of
                    // the ocean. Search Google Maps by the address text
                    // instead, which works for both new orders and legacy
                    // ones that do still have real coordinates.
                    $mapQuery = trim(implode(', ', array_filter([
                        $all_details['address'],
                        $all_details['city'],
                        $all_details['state'],
                        $all_details['postal_code'],
                    ])));
                    ?>
                    <h6><?= htmlspecialchars(str_replace("/", "'", $all_details['address'])) ?> <br><a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($mapQuery) ?>" target="_blank">View on Map <svg width="10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path fill="#5b6cc1" d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l82.7 0L201.4 265.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3l0 82.7c0 17.7 14.3 32 32 32s32-14.3 32-32l0-160c0-17.7-14.3-32-32-32L320 0zM80 32C35.8 32 0 67.8 0 112L0 432c0 44.2 35.8 80 80 80l320 0c44.2 0 80-35.8 80-80l0-112c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 112c0 8.8-7.2 16-16 16L80 448c-8.8 0-16-7.2-16-16l0-320c0-8.8 7.2-16 16-16l112 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L80 32z" />
                            </svg></a></h6>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <h5>Area</h5>
                </div>
                <div class="col-lg-6">
                    <h6><?= $all_details['area'] ?></h6>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="col-lg-4">
                    <h5>Pin Code</h5>
                </div>
                <div class="col-lg-6">
                    <h6><?= htmlspecialchars($all_details['postal_code']) ?></h6>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <h5>State</h5>
                </div>
                <div class="col-lg-6">
                    <h6><?= htmlspecialchars($all_details['state']) ?></h6>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <h5>City</h5>
                </div>
                <div class="col-lg-6">
                    <h6><?= htmlspecialchars($all_details['city']) ?></h6>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="row">
                <div class="col-lg-4">
                    <h5>Order Id</h5>
                </div>
                <div class="col-lg-6">
                    <h6><?= htmlspecialchars($all_details['order_id']) ?></h6>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="col-lg-6">
                    <h5>Booking Status</h5>
                </div>
                <div class="col-lg-6">
                    <h6>
                        <?php if ($all_details['booking_status'] == '0') {
                            echo statusView('warning', 'New Order');
                        } else if ($all_details['booking_status'] == '1') {
                            echo statusView('success', 'Accepted Order');
                        } else if ($all_details['booking_status'] == '2') {
                            echo statusView('info', 'Dispatched Order');
                        } else if ($all_details['booking_status'] == '1') {
                            echo statusView('success', 'Completed Order');
                        }
                        ?>
                    </h6>
                </div>
            </div>
            <?php if ($all_details['shiprocket_order_id']) { ?>
                <div class="row">
                    <div class="col-lg-6">
                        <h5>Shiprocket Order ID</h5>
                    </div>
                    <div class="col-lg-6">
                        <h6><?= htmlspecialchars($all_details['shiprocket_order_id']) ?></h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <h5>AWB Code</h5>
                    </div>
                    <div class="col-lg-6">
                        <h6><?= $all_details['shiprocket_awb_code'] ? htmlspecialchars($all_details['shiprocket_awb_code']) : 'Not Generated' ?></h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <h5>Shipment Status</h5>
                    </div>
                    <div class="col-lg-6">
                        <h6><?= $all_details['shiprocket_status'] ? htmlspecialchars($all_details['shiprocket_status']) : 'CREATED' ?></h6>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="col-lg-12 mt-3">
            <div class="row">
                <div class="col-lg-2">
                    <h5>Special Request</h5>
                </div>
                <div class="col-lg-10">
                    <p><?= nl2br(htmlspecialchars($all_details['note'])) ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <hr style="border: 1px solid #9c9c9c;">
            <h4 style="text-align: center">Transaction Details</h4>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="col-lg-6">
                    <h5>Payment Mode</h5>
                </div>
                <div class="col-lg-6">
                    <h6><?= htmlspecialchars($all_details['payment_mode']) ?></h6>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <h5>Promo Code
                    </h5>
                </div>
                <div class="col-lg-6">
                    <h6>
                        <b><?= $all_details['promocode_status'] == '0' ? ' ---- ' : htmlspecialchars($all_details['promocode']) ?></b>
                    </h6>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <h5>Transaction status</h5>
                </div>
                <div class="col-lg-6">
                    <?php if ($all_details['transaction_status'] == '1') {
                        echo '<h6><span class="badge badge-pill badge-soft-success font-size-14">Paid</span></h6>';
                    } else if ($all_details['transaction_status'] == '0') {
                        echo '<h6><span class="badge badge-pill badge-soft-warning font-size-14">Pending</span></h6>';
                    } else if ($all_details['transaction_status'] == '2') {
                        echo '<h6><span class="badge badge-pill badge-soft-danger font-size-14">Failed</span></h6>';
                    } else {
                        echo '<h6><span class="badge badge-pill badge-soft-danger font-size-14">Unpaid</span></h6>';
                    }
                    ?>
                </div>
            </div>
            <?php if (!empty($all_details['razorpay_order_id'])) { ?>
            <div class="row">
                <div class="col-lg-6">
                    <h5>Payment Gateway</h5>
                </div>
                <div class="col-lg-6">
                    <h6>Razorpay</h6>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <h5>Razorpay Order ID</h5>
                </div>
                <div class="col-lg-6">
                    <h6><?= htmlspecialchars($all_details['razorpay_order_id']) ?></h6>
                </div>
            </div>
            <?php } ?>
            <?php if (!empty($all_details['payment_id'])) { ?>
            <div class="row">
                <div class="col-lg-6">
                    <h5>Payment ID</h5>
                </div>
                <div class="col-lg-6">
                    <h6><?= htmlspecialchars($all_details['payment_id']) ?></h6>
                </div>
            </div>
            <?php } ?>
            <?php if (!empty($all_details['transaction_mode'])) { ?>
            <div class="row">
                <div class="col-lg-6">
                    <h5>Payment Method</h5>
                </div>
                <div class="col-lg-6">
                    <h6><?= htmlspecialchars(strtoupper($all_details['transaction_mode'])) ?></h6>
                </div>
            </div>
            <?php } ?>
            <?php if (!empty($all_details['update_date']) && $all_details['transaction_status'] == '1') { ?>
            <div class="row">
                <div class="col-lg-6">
                    <h5>Transaction Date</h5>
                </div>
                <div class="col-lg-6">
                    <h6><?= htmlspecialchars($all_details['update_date']) ?></h6>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="col-lg-6">
                    <h5>Total Items Amount
                    </h5>
                </div>
                <div class="col-lg-6">
                    <h6><?= number_format((float) $all_details['total_item_amount'], 2) ?>&#8377;</h6>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <h5>Packaging Charge</h5>
                </div>
                <div class="col-lg-6">
                    <h6><?= number_format((float) $all_details['packaging_charge'], 2) ?>&#8377;</h6>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <h5>Shipping Charge</h5>
                </div>
                <div class="col-lg-6">
                    <h6><?= $all_details['shipping_charge'] == 0 ? 'Free' : number_format((float) $all_details['shipping_charge'], 2) . '&#8377;' ?></h6>
                </div>
            </div>
            <?php
            if ($all_details['promocode_status'] == '1') { ?>
                <div class="row">
                    <div class="col-lg-6">
                        <h5>Promo Code Amount</h5>
                    </div>
                    <div class="col-lg-6">
                        <h6>
                            <b>-</b> <?= number_format((float) $all_details['promocode_amount'], 2) ?>&#8377;
                        </h6>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <hr>
                <div class="col-lg-6">
                    <h5>Final Amount</h5>
                </div>
                <div class="col-lg-6">
                    <h6><?= number_format((float) $all_details['final_amount'], 2) ?>&#8377;</h6>
                </div>
            </div>
        </div>
    </div>
<?php } else if ($type == 2) { ?>
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered">
                <tr>
                    <th>Sr. no.</th>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Variant</th>
                    <th>Order Qty</th>
                    <th>Base Price</th>
                    <th>User Price</th>
                    <th>Price</th>
                    <th>Return Status</th>
                </tr>
                <?php
                $final_amount = 0;
                if ($all_items) {
                    $j = 0;
                    foreach ($all_items as $item) {
                        $final_amount += $item['booking_price'];
                        $returnRow = $returns_by_item[$item['book_item_id']] ?? null;
                ?>
                        <tr>
                            <td><?= ++$j; ?></td>
                            <td>
                                <a href="<?= base_url('productDetails?id=' . encryptId($item['product_id'])) ?>" target="_blank">
                                    <img src="<?= setImage($item['image_path'], PRODUCT_IMAGE) ?>" alt="<?= htmlspecialchars($item['product_name']); ?>" style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                                </a>
                            </td>
                            <td>
                                <a href="<?= base_url('productDetails?id=' . encryptId($item['product_id'])) ?>" target="_blank"><?= htmlspecialchars($item['product_name']); ?></a>
                                <br><span class="text-muted" style="font-size:11px;">ID: <?= $item['product_id']; ?></span>
                            </td>
                            <td><?= ($item['variant_size'] || $item['variant_color']) ? 'Size: ' . htmlspecialchars($item['variant_size']) . ' / Color: ' . htmlspecialchars($item['variant_color']) : '-'; ?></td>
                            <td><?= $item['no_of_items']; ?></td>
                            <td><?= number_format((float) $item['base_price'], 2); ?></td>
                            <td><?= number_format((float) $item['user_price'], 2); ?></td>
                            <td><?= number_format((float) $item['booking_price'], 2); ?>&#8377;</td>
                            <td>
                                <?php if ($returnRow): ?>
                                    <a href="<?= base_url('returnDetails?id=' . encryptId($returnRow['return_id'])) ?>" target="_blank">
                                        <?= getReturnStatusLabel($returnRow['status']) ?>
                                    </a>
                                    <?php if ($returnRow['refund_status'] !== null): ?>
                                        <br><span class="text-muted">Refund: <?= getRefundStatusLabel($returnRow['refund_status']) ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <td colspan="8"></td>
                        <td>Total Amount: <?= number_format((float) $final_amount, 2); ?>&#8377;</td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </div>
<?php } ?>