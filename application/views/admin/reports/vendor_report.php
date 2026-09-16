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
                        <p class="text-muted mb-1">Active Vendors</p>
                        <h3><?= $active_vendors ?></h3>
                    </div></div>
                </div>
                <div class="col-md-4">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Total Commission Earned</p>
                        <h3>₹<?= number_format($total_commission, 2) ?></h3>
                    </div></div>
                </div>
                <div class="col-md-4">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Total Payable to Vendors</p>
                        <h3>₹<?= number_format($total_payable, 2) ?></h3>
                    </div></div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Vendor-wise Sales</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Vendor</th>
                                        <th>Lines Sold</th>
                                        <th>Qty Sold</th>
                                        <th>Commission</th>
                                        <th>Payable</th>
                                        <th>Paid</th>
                                        <th>Unpaid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($by_vendor)) : ?>
                                        <tr><td colspan="7" class="text-center">No vendor sales in this range.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($by_vendor as $row) : ?>
                                        <tr>
                                            <td><?= $row['business_name'] ?></td>
                                            <td><?= $row['line_count'] ?></td>
                                            <td><?= $row['qty_sold'] ?></td>
                                            <td>₹<?= number_format($row['commission_total'], 2) ?></td>
                                            <td>₹<?= number_format($row['payable_total'], 2) ?></td>
                                            <td>₹<?= number_format($row['paid_total'], 2) ?></td>
                                            <td>₹<?= number_format($row['unpaid_total'], 2) ?></td>
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
