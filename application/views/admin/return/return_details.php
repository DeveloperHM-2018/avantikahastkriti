<?php $this->load->view('admin/template/header', $title); ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0 "><?= $title ?></h2>
                        <div>
                            <?php if ((int) $return['status'] === RETURN_STATUS_REQUESTED || (int) $return['status'] === RETURN_STATUS_UNDER_REVIEW): ?>
                                <?php if (@PREV['return_process'] == 1 || USER_TYPE == '1'): ?>
                                    <button type="button" class="btn btn-success" id="btnApprove">Approve</button>
                                    <button type="button" class="btn btn-danger" id="btnReject">Reject</button>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if ((int) $return['status'] === RETURN_STATUS_APPROVED && (@PREV['return_process'] == 1 || USER_TYPE == '1')): ?>
                                <button type="button" class="btn btn-primary" id="btnSchedulePickup">
                                    <?= $return['shiprocket_return_shipment_id'] ? 'Retry Pickup' : 'Schedule Pickup' ?>
                                </button>
                            <?php endif; ?>
                            <?php if ((int) $return['status'] >= RETURN_STATUS_APPROVED && (int) $return['status'] !== RETURN_STATUS_REJECTED && (int) $return['refund_status'] !== REFUND_STATUS_COMPLETED && (@PREV['return_refund'] == 1 || USER_TYPE == '1')): ?>
                                <button type="button" class="btn btn-warning" id="btnRefund">Refund</button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-light" onclick="window.print()">Print</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5>Order &amp; Customer</h5>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Order Number:</strong> <?= @$order['order_id'] ?></p>
                                    <p class="mb-1"><strong>Customer:</strong> <?= @$customer['name'] ?></p>
                                    <p class="mb-1"><strong>Mobile:</strong> <?= @$customer['contact_no'] ?></p>
                                    <p class="mb-1"><strong>Email:</strong> <?= @$customer['email_id'] ?></p>
                                    <p class="mb-1"><strong>Payment Method:</strong> <?= @$order['payment_mode'] ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Shipping Address:</strong></p>
                                    <p class="mb-1"><?= @$order['address'] ?>, <?= @$order['area'] ?><br>
                                        <?= @$order['city'] ?>, <?= @$order['state'] ?> - <?= @$order['postal_code'] ?></p>
                                </div>
                            </div>
                            <hr>
                            <h5>Product Details</h5>
                            <div class="row mt-3">
                                <div class="col-md-8">
                                    <p class="mb-1"><strong>Product:</strong> <?= @$item['product_name'] ?></p>
                                    <p class="mb-1"><strong>Size / Color:</strong> <?= @$item['variant_size'] ?> / <?= @$item['variant_color'] ?></p>
                                    <p class="mb-1"><strong>Quantity Purchased:</strong> <?= $return['quantity_purchased'] ?></p>
                                    <p class="mb-1"><strong>Quantity to Return:</strong> <?= $return['quantity_return'] ?></p>
                                </div>
                            </div>
                            <hr>
                            <h5>Reason</h5>
                            <p class="mb-1"><strong>Reason:</strong> <?= $return['reason'] ?></p>
                            <?php if ($return['reason'] === 'Other' && $return['reason_other_text']): ?>
                                <p class="mb-1"><strong>Description:</strong> <?= $return['reason_other_text'] ?></p>
                            <?php endif; ?>
                            <?php if ($return['remarks']): ?>
                                <p class="mb-1"><strong>Customer Remarks:</strong> <?= $return['remarks'] ?></p>
                            <?php endif; ?>

                            <?php if ($images): ?>
                                <hr>
                                <h5>Uploaded Images</h5>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <?php foreach ($images as $img): ?>
                                        <a href="<?= base_url('upload/returns/') . $img['image_path'] ?>" target="_blank">
                                            <img src="<?= base_url('upload/returns/') . $img['image_path'] ?>" style="width:100px;height:100px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($return['admin_notes'] || $return['reject_reason']): ?>
                                <hr>
                                <h5>Admin Notes</h5>
                                <?php if ($return['admin_notes']): ?><p class="mb-1"><?= $return['admin_notes'] ?></p><?php endif; ?>
                                <?php if ($return['reject_reason']): ?>
                                    <p class="mb-1"><strong>Rejection Reason:</strong> <?= $return['reject_reason'] ?></p>
                                    <?php if ($return['reject_remarks']): ?><p class="mb-1"><?= $return['reject_remarks'] ?></p><?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <?php if ($return['refund_method']): ?>
                        <div class="card">
                            <div class="card-body">
                                <h5>Refund</h5>
                                <p class="mb-1"><strong>Method:</strong> <?= $return['refund_method'] ?></p>
                                <p class="mb-1"><strong>Amount:</strong> ₹<?= number_format($return['refund_amount'], 2) ?></p>
                                <p class="mb-1"><strong>Status:</strong> <?= getRefundStatusLabel($return['refund_status']) ?></p>
                                <?php if ($return['refund_transaction_id']): ?>
                                    <p class="mb-1"><strong>Transaction ID:</strong> <?= $return['refund_transaction_id'] ?></p>
                                <?php endif; ?>
                                <?php if ($return['refund_date']): ?>
                                    <p class="mb-1"><strong>Refund Date:</strong> <?= dateConvertToView($return['refund_date'], 3) ?></p>
                                <?php endif; ?>
                                <?php if ($return['refund_remark']): ?>
                                    <p class="mb-1"><strong>Remark:</strong> <?= $return['refund_remark'] ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($return['shiprocket_return_order_id']): ?>
                        <div class="card">
                            <div class="card-body">
                                <h5>Shiprocket Pickup</h5>
                                <p class="mb-1"><strong>Return Order ID:</strong> <?= $return['shiprocket_return_order_id'] ?></p>
                                <?php if ($return['shiprocket_pickup_awb']): ?>
                                    <p class="mb-1"><strong>AWB:</strong> <?= $return['shiprocket_pickup_awb'] ?></p>
                                <?php endif; ?>
                                <?php if ($return['shiprocket_courier_name']): ?>
                                    <p class="mb-1"><strong>Courier:</strong> <?= $return['shiprocket_courier_name'] ?></p>
                                <?php endif; ?>
                                <?php if ($return['shiprocket_pickup_date']): ?>
                                    <p class="mb-1"><strong>Pickup Date:</strong> <?= dateConvertToView($return['shiprocket_pickup_date'], 2) ?></p>
                                <?php endif; ?>
                                <?php if ($return['shiprocket_pickup_status']): ?>
                                    <p class="mb-1"><strong>Carrier Status:</strong> <?= $return['shiprocket_pickup_status'] ?></p>
                                <?php endif; ?>
                                <?php if ($return['shiprocket_tracking_url']): ?>
                                    <a href="<?= $return['shiprocket_tracking_url'] ?>" target="_blank" class="btn btn-sm btn-warning mt-2">Track Shipment</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="card">
                        <div class="card-body">
                            <h5>Timeline</h5>
                            <ul class="list-unstyled mt-3">
                                <?php foreach ($timeline as $t): ?>
                                    <li class="mb-3">
                                        <strong><?= getReturnStatusLabel($t['status']) ?></strong><br>
                                        <span class="text-muted"><?= dateConvertToView($t['create_date'], 3) ?></span>
                                        <?php if ($t['note']): ?><br><span><?= $t['note'] ?></span><?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Return</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Admin Remark</label>
                <textarea class="form-control" id="approveAdminNotes" rows="4"></textarea>
                <div class="form-check mt-3">
                    <input type="checkbox" class="form-check-input" id="approveCreatePickup">
                    <label class="form-check-label" for="approveCreatePickup">Create Shiprocket reverse pickup now</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="approveSubmitBtn">Approve</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Return</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Reason</label>
                <input type="text" class="form-control mb-3" id="rejectReason" required>
                <label class="form-label">Remarks</label>
                <textarea class="form-control" id="rejectRemarks" rows="4"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="rejectSubmitBtn">Reject</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="refundModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Refund Method</label>
                <select class="form-select mb-3" id="refundMethod">
                    <option value="COD">COD (Cash)</option>
                    <option value="ONLINE">Online (Gateway)</option>
                    <option value="WALLET">Wallet</option>
                    <option value="BANK_TRANSFER">Bank Transfer</option>
                </select>
                <label class="form-label">Refund Amount</label>
                <input type="number" step="0.01" class="form-control mb-3" id="refundAmount" value="<?= $default_refund_amount ?>">
                <label class="form-label">Refund Status</label>
                <select class="form-select mb-3" id="refundStatus">
                    <option value="<?= REFUND_STATUS_PENDING ?>">Pending</option>
                    <option value="<?= REFUND_STATUS_COMPLETED ?>">Completed</option>
                    <option value="<?= REFUND_STATUS_FAILED ?>">Failed</option>
                </select>
                <label class="form-label">Transaction ID <span class="text-muted">(Online / Bank Transfer)</span></label>
                <input type="text" class="form-control mb-3" id="refundTransactionId">
                <label class="form-label">Remark</label>
                <textarea class="form-control" id="refundRemark" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" id="refundSubmitBtn">Save Refund</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/template/footer'); ?>
<script>
    const returnId = "<?= encryptId($return['return_id']) ?>";

    $('#btnApprove').on('click', function() {
        $('#approveModal').modal('show');
    });
    $('#btnReject').on('click', function() {
        $('#rejectModal').modal('show');
    });

    $('#approveSubmitBtn').on('click', function() {
        const btn = $(this);
        btn.prop('disabled', true).text('Saving...');
        $.ajax({
            type: 'POST',
            url: "<?= base_url('approveReturn') ?>",
            data: {
                id: returnId,
                admin_notes: $('#approveAdminNotes').val(),
                create_pickup: $('#approveCreatePickup').is(':checked') ? '1' : '0'
            },
            dataType: 'JSON',
            success: function(data) {
                if (data.status) {
                    toastr.success(data.message);
                    setTimeout(() => location.reload(), 800);
                } else {
                    toastr.error(data.message);
                    btn.prop('disabled', false).text('Approve');
                }
            },
            error: function() {
                toastr.error('Something went wrong. Please try again.');
                btn.prop('disabled', false).text('Approve');
            }
        });
    });

    $('#btnSchedulePickup').on('click', function() {
        const btn = $(this);
        const originalText = btn.text();
        btn.prop('disabled', true).text('Scheduling...');
        $.ajax({
            type: 'POST',
            url: "<?= base_url('schedulePickup') ?>",
            data: {
                id: returnId
            },
            dataType: 'JSON',
            success: function(data) {
                if (data.status) {
                    toastr.success(data.message);
                    setTimeout(() => location.reload(), 800);
                } else {
                    toastr.error(data.message);
                    btn.prop('disabled', false).text(originalText);
                }
            },
            error: function() {
                toastr.error('Something went wrong. Please try again.');
                btn.prop('disabled', false).text(originalText);
            }
        });
    });

    $('#btnRefund').on('click', function() {
        $('#refundModal').modal('show');
    });

    $('#refundSubmitBtn').on('click', function() {
        if (!$('#refundAmount').val() || parseFloat($('#refundAmount').val()) <= 0) {
            toastr.error('Please enter a valid refund amount.');
            return;
        }
        const btn = $(this);
        btn.prop('disabled', true).text('Saving...');
        $.ajax({
            type: 'POST',
            url: "<?= base_url('processRefund') ?>",
            data: {
                id: returnId,
                refund_method: $('#refundMethod').val(),
                refund_amount: $('#refundAmount').val(),
                refund_status: $('#refundStatus').val(),
                transaction_id: $('#refundTransactionId').val(),
                remark: $('#refundRemark').val()
            },
            dataType: 'JSON',
            success: function(data) {
                if (data.status) {
                    toastr.success(data.message);
                    setTimeout(() => location.reload(), 800);
                } else {
                    toastr.error(data.message);
                    btn.prop('disabled', false).text('Save Refund');
                }
            },
            error: function() {
                toastr.error('Something went wrong. Please try again.');
                btn.prop('disabled', false).text('Save Refund');
            }
        });
    });

    $('#rejectSubmitBtn').on('click', function() {
        if (!$('#rejectReason').val()) {
            toastr.error('Please provide a rejection reason.');
            return;
        }
        const btn = $(this);
        btn.prop('disabled', true).text('Saving...');
        $.ajax({
            type: 'POST',
            url: "<?= base_url('rejectReturn') ?>",
            data: {
                id: returnId,
                reject_reason: $('#rejectReason').val(),
                reject_remarks: $('#rejectRemarks').val()
            },
            dataType: 'JSON',
            success: function(data) {
                if (data.status) {
                    toastr.success(data.message);
                    setTimeout(() => location.reload(), 800);
                } else {
                    toastr.error(data.message);
                    btn.prop('disabled', false).text('Reject');
                }
            },
            error: function() {
                toastr.error('Something went wrong. Please try again.');
                btn.prop('disabled', false).text('Reject');
            }
        });
    });
</script>
