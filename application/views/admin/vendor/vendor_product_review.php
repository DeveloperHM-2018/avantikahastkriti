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

            <?php if ($this->session->flashdata('errors') != '') { ?>
                <div class="row">
                    <div class="col-8 offset-2">
                        <div class="alert alert-info"><?= $this->session->flashdata('errors'); ?></div>
                    </div>
                </div>
            <?php } ?>

            <div class="row">
                <div class="col-8 offset-2">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-4">
                                <tr><th style="width:220px;">Vendor</th><td><?= $vendor['business_name'] ?></td></tr>
                                <tr><th>Product Name</th><td><?= $vp['product_name'] ?></td></tr>
                                <tr><th>Description</th><td><?= $vp['description'] ?: '-' ?></td></tr>
                                <tr><th>Vendor Supply Price</th><td>₹<?= $vp['vendor_supply_price'] ?></td></tr>
                                <tr><th>Vendor's Proposed Sale Price</th><td><?= $vp['proposed_sale_price'] ? '₹' . $vp['proposed_sale_price'] : '-' ?></td></tr>
                                <tr><th>Quantity Offered</th><td><?= $vp['quantity_supplied'] ?></td></tr>
                            </table>

                            <?php if ($can_approve) : ?>
                                <form action="" method="post" id="approveForm">
                                    <div class="row mb-3">
                                        <label class="col-md-3 col-form-label">Commission %</label>
                                        <div class="col-md-9">
                                            <input class="form-control" type="number" step="0.01" min="0" max="100" name="commission_percent" id="commission_percent" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-md-3 col-form-label">Final Sale Price</label>
                                        <div class="col-md-9">
                                            <input class="form-control" type="number" step="0.01" min="0.01" name="sale_price" id="sale_price" value="<?= $vp['proposed_sale_price'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-md-3 col-form-label">Admin Notes</label>
                                        <div class="col-md-9">
                                            <textarea class="form-control" name="admin_notes" rows="2"></textarea>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <!-- Only the activated submit button's name/value is ever sent, so
                                             "decision" arrives as exactly one of these two regardless of
                                             which is clicked - no hidden-field/duplicate-name ambiguity. -->
                                        <button type="submit" class="btn btn-success" name="decision" value="approve"><i class="fa fa-check"></i> Approve &amp; Publish</button>
                                        <button type="submit" class="btn btn-danger" name="decision" value="reject" formnovalidate onclick="document.getElementById('commission_percent').required = false; document.getElementById('sale_price').required = false;"><i class="fa fa-times"></i> Reject</button>
                                    </div>
                                </form>
                            <?php else : ?>
                                <div class="alert alert-warning">You do not have permission to approve vendor products.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/template/footer'); ?>
