<?php $this->load->view('vendor/template/header', $title); ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0"><?= $title ?></h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Period</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Reference</th>
                                        <th>Processed Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($payouts)) : ?>
                                        <tr><td colspan="5" class="text-center">No payouts recorded yet.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($payouts as $p) : ?>
                                        <tr>
                                            <td><?= dateConvertToView($p['period_from'], 1) ?> - <?= dateConvertToView($p['period_to'], 1) ?></td>
                                            <td>₹<?= number_format($p['total_amount'], 2) ?></td>
                                            <td><?= $p['status'] == 1 ? statusView('success', 'Paid') : statusView('warning', 'Pending') ?></td>
                                            <td><?= $p['payment_reference'] ?: '-' ?></td>
                                            <td><?= $p['processed_date'] ? dateConvertToView($p['processed_date'], 3) : '-' ?></td>
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

<?php $this->load->view('vendor/template/footer'); ?>
