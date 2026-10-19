<?php $this->load->view('admin/template/header', $title); ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0 "><?= $title ?></h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Unpaid Balances</h5>
                            <p class="text-muted mb-2">A sale becomes payable once the order is completed, its <?= (int) $return_window_days ?>-day return window has passed, and no return request on it is awaiting a decision.</p>
                            <table class="table table-bordered">
                                <thead><tr><th>Partner</th><th>Payable Lines</th><th>Payable Now</th><th>Not Yet Eligible</th><th>Action</th></tr></thead>
                                <tbody>
                                    <?php if (empty($unpaid_summary)) : ?>
                                        <tr><td colspan="5" class="text-center">Nothing outstanding.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($unpaid_summary as $row) : ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['business_name']) ?></td>
                                            <td><?= $row['unpaid_lines'] ?></td>
                                            <td>₹<?= number_format($row['unpaid_amount'], 2) ?></td>
                                            <td>₹<?= number_format($row['pending_amount'], 2) ?></td>
                                            <td>
                                                <?php if ($row['unpaid_amount'] > 0) : ?>
                                                <button type="button" class="btn btn-primary btn-sm createPayoutBtn" data-vendor="<?= encryptId($row['vendor_id']) ?>">
                                                    <i class="fa fa-money-bill"></i> Create Payout
                                                </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5>Recent Payouts</h5>
                            <table class="table table-bordered">
                                <thead><tr><th>Partner</th><th>Period</th><th>Amount</th><th>Status</th><th>Reference</th><th>Action</th></tr></thead>
                                <tbody>
                                    <?php if (empty($recent_payouts)) : ?>
                                        <tr><td colspan="6" class="text-center">No payouts created yet.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($recent_payouts as $p) : ?>
                                        <tr>
                                            <td><?= $p['business_name'] ?></td>
                                            <td><?= dateConvertToView($p['period_from'], 1) ?> - <?= dateConvertToView($p['period_to'], 1) ?></td>
                                            <td>₹<?= number_format($p['total_amount'], 2) ?></td>
                                            <td><?= $p['status'] == 1 ? statusView('success', 'Paid') : statusView('warning', 'Pending') ?></td>
                                            <td><?= $p['payment_reference'] ?: '-' ?></td>
                                            <td>
                                                <?php if ($p['status'] == 0) : ?>
                                                    <button type="button" class="btn btn-success btn-sm markPaidBtn" data-id="<?= encryptId($p['payout_id']) ?>">
                                                        <i class="fa fa-check"></i> Mark Paid
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/template/footer'); ?>
<script>
    $(document).on('click', '.createPayoutBtn', function() {
        var vendorId = $(this).data('vendor');
        if (!confirm('Create a payout batch for every payable line for this partner?')) return;
        $.post('<?= base_url('vendorPayoutCreate') ?>', {
            vendor_id: vendorId
        }, function(res) {
            alert(res.message);
            if (res.status) location.reload();
        }, 'json');
    });

    $(document).on('click', '.markPaidBtn', function() {
        var id = $(this).data('id');
        var reference = prompt('Payment reference / transaction ID:');
        if (!reference) return;
        $.post('<?= base_url('vendorPayoutMarkPaid') ?>', {
            id: id,
            payment_reference: reference
        }, function(res) {
            alert(res.message);
            if (res.status) location.reload();
        }, 'json');
    });
</script>
