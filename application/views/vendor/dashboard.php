<?php $this->load->view('vendor/template/header', $title); ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0"><?= $title ?></h2>
                    </div>
                    <?php if ($vendor['status'] == 0) : ?>
                        <div class="alert alert-warning">Your account is pending admin approval. Some features are limited until you're approved.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Total Products</p>
                        <h3><?= $total_products ?></h3>
                        <small class="text-muted"><?= $pending_products ?> pending, <?= $live_products ?> live</small>
                    </div></div>
                </div>
                <div class="col-md-3">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Total Orders</p>
                        <h3><?= $total_orders ?></h3>
                        <small class="text-muted"><?= $pending_orders ?> unpaid</small>
                    </div></div>
                </div>
                <div class="col-md-3">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Total Sales</p>
                        <h3>₹<?= number_format($total_sales, 2) ?></h3>
                    </div></div>
                </div>
                <div class="col-md-3">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Payable / Paid</p>
                        <h3>₹<?= number_format($payable_amount, 2) ?></h3>
                        <small class="text-muted">₹<?= number_format($paid_amount, 2) ?> already paid</small>
                    </div></div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Recent Orders</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Payable</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_orders)) : ?>
                                        <tr><td colspan="5" class="text-center">No orders yet.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($recent_orders as $o) : ?>
                                        <tr>
                                            <td><?= $o['order_id'] ?></td>
                                            <td><?= $o['product_name'] ?></td>
                                            <td><?= $o['quantity'] ?></td>
                                            <td>₹<?= number_format($o['vendor_payable_amount'], 2) ?></td>
                                            <td><?= dateConvertToView($o['create_date'], 3) ?></td>
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
