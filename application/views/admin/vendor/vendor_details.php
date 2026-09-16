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
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5>Business Details</h5>
                            <table class="table table-sm table-borderless">
                                <tr><th>Contact Person</th><td><?= $vendor['contact_name'] ?></td></tr>
                                <tr><th>Mobile</th><td><?= $vendor['contact_no'] ?></td></tr>
                                <tr><th>Email</th><td><?= $vendor['email_id'] ?></td></tr>
                                <tr><th>GST</th><td><?= $vendor['gst_number'] ?: '-' ?></td></tr>
                                <tr><th>PAN</th><td><?= $vendor['pan_number'] ?: '-' ?></td></tr>
                                <tr><th>Address</th><td><?= $vendor['address'] ?>, <?= $vendor['city'] ?>, <?= $vendor['state'] ?> - <?= $vendor['postal_code'] ?></td></tr>
                                <tr><th>Bank</th><td><?= $vendor['bank_account_name'] ?> - <?= $vendor['bank_account_no'] ?> (<?= $vendor['bank_ifsc'] ?>)</td></tr>
                                <tr><th>Status</th><td><?= ['Pending', 'Active', 'Rejected', 'Suspended'][$vendor['status']] ?></td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h5>Documents</h5>
                            <?php if (empty($documents)) : ?>
                                <p class="text-muted">No documents uploaded.</p>
                            <?php endif; ?>
                            <?php foreach ($documents as $doc) : ?>
                                <a href="<?= base_url('vendorViewDocument/' . encryptId($doc['document_id'])) ?>" target="_blank" class="d-block mb-2">
                                    <i class="fa fa-file"></i> <?= ucwords(str_replace('_', ' ', $doc['document_type'])) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card"><div class="card-body">
                                <p class="text-muted mb-1">Total Commission Earned</p>
                                <h4>₹<?= number_format($total_commission, 2) ?></h4>
                            </div></div>
                        </div>
                        <div class="col-md-4">
                            <div class="card"><div class="card-body">
                                <p class="text-muted mb-1">Payable to Vendor</p>
                                <h4>₹<?= number_format($total_payable, 2) ?></h4>
                            </div></div>
                        </div>
                        <div class="col-md-4">
                            <div class="card"><div class="card-body">
                                <p class="text-muted mb-1">Already Paid</p>
                                <h4>₹<?= number_format($total_paid, 2) ?></h4>
                            </div></div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5>Products</h5>
                            <table class="table table-bordered">
                                <thead><tr><th>Product</th><th>Supply Price</th><th>Commission %</th><th>Status</th></tr></thead>
                                <tbody>
                                    <?php if (empty($products)) : ?>
                                        <tr><td colspan="4" class="text-center">No products submitted yet.</td></tr>
                                    <?php endif; ?>
                                    <?php $statusLabels = ['Pending', 'Approved', 'Rejected', 'Changes Requested']; ?>
                                    <?php foreach ($products as $p) : ?>
                                        <tr>
                                            <td><?= $p['product_name'] ?></td>
                                            <td><?= $p['vendor_supply_price'] ?></td>
                                            <td><?= $p['commission_percent'] !== null ? $p['commission_percent'] . '%' : '-' ?></td>
                                            <td><?= $statusLabels[$p['status']] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5>Payout History</h5>
                            <table class="table table-bordered">
                                <thead><tr><th>Period</th><th>Amount</th><th>Status</th><th>Reference</th></tr></thead>
                                <tbody>
                                    <?php if (empty($payouts)) : ?>
                                        <tr><td colspan="4" class="text-center">No payouts yet.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($payouts as $p) : ?>
                                        <tr>
                                            <td><?= dateConvertToView($p['period_from'], 1) ?> - <?= dateConvertToView($p['period_to'], 1) ?></td>
                                            <td>₹<?= number_format($p['total_amount'], 2) ?></td>
                                            <td><?= $p['status'] == 1 ? 'Paid' : 'Pending' ?></td>
                                            <td><?= $p['payment_reference'] ?: '-' ?></td>
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
