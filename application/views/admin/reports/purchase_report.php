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
                        <p class="text-muted mb-1">Supply Batches</p>
                        <h3><?= $supply_count ?></h3>
                    </div></div>
                </div>
                <div class="col-md-4">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Units Purchased</p>
                        <h3><?= number_format($total_qty) ?></h3>
                    </div></div>
                </div>
                <div class="col-md-4">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Total Cost</p>
                        <h3>₹<?= number_format($total_cost, 2) ?></h3>
                    </div></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Restocks by Vendor</h5>
                            <table class="table table-bordered">
                                <thead><tr><th>Vendor</th><th>Qty</th><th>Cost</th></tr></thead>
                                <tbody>
                                    <?php if (empty($by_vendor)) : ?>
                                        <tr><td colspan="3" class="text-center">No restocks in this range.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($by_vendor as $row) : ?>
                                        <tr>
                                            <td><?= $row['business_name'] ?></td>
                                            <td><?= $row['qty'] ?></td>
                                            <td>₹<?= number_format($row['cost'], 2) ?></td>
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
                            <h5>Initial Product Listings from Vendors</h5>
                            <p class="text-muted small">Stock that entered the catalog via a newly-approved vendor product submission, not a restock of an existing product.</p>
                            <table class="table table-bordered">
                                <thead><tr><th>Vendor</th><th>Qty</th><th>Entries</th></tr></thead>
                                <tbody>
                                    <?php if (empty($ledger_vendor_supply)) : ?>
                                        <tr><td colspan="3" class="text-center">None in this range.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($ledger_vendor_supply as $row) : ?>
                                        <tr>
                                            <td><?= $row['business_name'] ?></td>
                                            <td><?= $row['qty'] ?></td>
                                            <td><?= $row['entries'] ?></td>
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
