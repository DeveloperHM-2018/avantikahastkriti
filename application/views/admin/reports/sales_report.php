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

            <form method="get" class="row mb-3">
                <div class="col-lg-3">
                    <label>From Date</label>
                    <input type="text" class="form-control" name="date_from" value="<?= $date_from ?>" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-autoclose="true">
                </div>
                <div class="col-lg-3">
                    <label>To Date</label>
                    <input type="text" class="form-control" name="date_to" value="<?= $date_to ?>" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-autoclose="true">
                </div>
                <div class="col-lg-2 align-self-end">
                    <button type="submit" class="btn btn-primary">Apply</button>
                </div>
            </form>

            <div class="row">
                <div class="col-md-4">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Orders (Paid)</p>
                        <h3><?= $order_count ?></h3>
                    </div></div>
                </div>
                <div class="col-md-4">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Gross Sales</p>
                        <h3>₹<?= number_format($gross_sales, 2) ?></h3>
                    </div></div>
                </div>
                <div class="col-md-4">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Units Sold</p>
                        <h3><?= $total_units ?></h3>
                    </div></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Daily Sales</h5>
                            <table class="table table-bordered">
                                <thead><tr><th>Date</th><th>Orders</th><th>Amount</th></tr></thead>
                                <tbody>
                                    <?php if (empty($daily_sales)) : ?>
                                        <tr><td colspan="3" class="text-center">No sales in this range.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($daily_sales as $row) : ?>
                                        <tr>
                                            <td><?= $row['d'] ?></td>
                                            <td><?= $row['orders'] ?></td>
                                            <td>₹<?= number_format($row['amount'], 2) ?></td>
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
                            <h5>Sales by Order Source</h5>
                            <?php $sourceLabels = [0 => 'Web', 1 => 'Mobile App', 2 => 'Magic Checkout', 3 => 'Admin Manual']; ?>
                            <table class="table table-bordered">
                                <thead><tr><th>Source</th><th>Orders</th><th>Amount</th></tr></thead>
                                <tbody>
                                    <?php foreach ($by_source as $row) : ?>
                                        <tr>
                                            <td><?= $sourceLabels[$row['order_source']] ?? $row['order_source'] ?></td>
                                            <td><?= $row['orders'] ?></td>
                                            <td>₹<?= number_format($row['amount'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Top Products</h5>
                            <table class="table table-bordered">
                                <thead><tr><th>Product</th><th>Qty Sold</th><th>Revenue</th></tr></thead>
                                <tbody>
                                    <?php if (empty($top_products)) : ?>
                                        <tr><td colspan="3" class="text-center">No sales in this range.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($top_products as $row) : ?>
                                        <tr>
                                            <td><?= $row['product_name'] ?></td>
                                            <td><?= $row['qty_sold'] ?></td>
                                            <td>₹<?= number_format($row['revenue'], 2) ?></td>
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
