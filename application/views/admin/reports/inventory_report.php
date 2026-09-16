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
                <div class="col-md-3">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Total Products</p>
                        <h3><?= $total_products ?></h3>
                    </div></div>
                </div>
                <div class="col-md-3">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Out of Stock</p>
                        <h3 class="text-danger"><?= $out_of_stock_count ?></h3>
                    </div></div>
                </div>
                <div class="col-md-3">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Low Stock</p>
                        <h3 class="text-warning"><?= $low_stock_count ?></h3>
                    </div></div>
                </div>
                <div class="col-md-3">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Total Units on Hand</p>
                        <h3><?= number_format($total_units_on_hand) ?></h3>
                    </div></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Low Stock Products <a href="<?= base_url('lowStockAll') ?>" class="float-end">View All</a></h5>
                            <table class="table table-bordered">
                                <thead><tr><th>Product</th><th>Quantity</th><th>Threshold</th></tr></thead>
                                <tbody>
                                    <?php if (empty($low_stock_products)) : ?>
                                        <tr><td colspan="3" class="text-center">Nothing low on stock.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($low_stock_products as $row) : ?>
                                        <tr>
                                            <td><?= $row['product_name'] ?></td>
                                            <td><?= $row['quantity'] ?></td>
                                            <td><?= $row['low_stock_threshold'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Recent Stock Movements <a href="<?= base_url('stockAll') ?>" class="float-end">View All Stock</a></h5>
                            <table class="table table-bordered">
                                <thead><tr><th>Product</th><th>Type</th><th>Delta</th><th>Date</th></tr></thead>
                                <tbody>
                                    <?php if (empty($recent_movements)) : ?>
                                        <tr><td colspan="4" class="text-center">No movements recorded yet.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($recent_movements as $row) : ?>
                                        <tr>
                                            <td><?= $row['product_name'] ?></td>
                                            <td><?= ucwords(str_replace('_', ' ', $row['change_type'])) ?></td>
                                            <td class="<?= $row['delta'] > 0 ? 'text-success' : ($row['delta'] < 0 ? 'text-danger' : '') ?>">
                                                <?= $row['delta'] > 0 ? '+' : '' ?><?= $row['delta'] ?>
                                            </td>
                                            <td><?= dateConvertToView($row['create_date'], 3) ?></td>
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
