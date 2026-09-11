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
                <div class="col-8 offset-2">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post">
                                <h5>Return Window</h5>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label class="col-form-label">Return Window (Days after delivery) *</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="number" min="1" name="return_window_days" value="<?= $return_window_days ?>">
                                            <?= form_error('return_window_days', '<span class="text-danger">', '</span>') ?>
                                        </div>
                                    </div>
                                </div>
                                <hr class="mt-4">
                                <h5>Warehouse / RTO Address</h5>
                                <p class="text-muted">Used as the delivery destination when creating a Shiprocket reverse-pickup order for approved returns.</p>
                                <div class="row">
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">Address</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="warehouse_address" value="<?= $warehouse_address ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">City</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="warehouse_city" value="<?= $warehouse_city ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">State</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="warehouse_state" value="<?= $warehouse_state ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">Pincode</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="warehouse_pincode" value="<?= $warehouse_pincode ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">Phone</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="warehouse_phone" value="<?= $warehouse_phone ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">Email</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="warehouse_email" value="<?= $warehouse_email ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary w-md">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/template/footer'); ?>
