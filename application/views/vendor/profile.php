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
                            <h5>Business Profile</h5>
                            <form action="" method="post">
                                <input type="hidden" name="form" value="profile">
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Contact Person</label>
                                    <div class="col-md-9"><input class="form-control" type="text" name="contact_name" value="<?= $vendor['contact_name'] ?>" required></div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Mobile</label>
                                    <div class="col-md-9"><input class="form-control" type="text" name="contact_no" value="<?= $vendor['contact_no'] ?>" required></div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Address</label>
                                    <div class="col-md-9"><textarea class="form-control" name="address" rows="2"><?= $vendor['address'] ?></textarea></div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">City</label>
                                    <div class="col-md-9"><input class="form-control" type="text" name="city" value="<?= $vendor['city'] ?>"></div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">State</label>
                                    <div class="col-md-9"><input class="form-control" type="text" name="state" value="<?= $vendor['state'] ?>"></div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Postal Code</label>
                                    <div class="col-md-9"><input class="form-control" type="text" name="postal_code" value="<?= $vendor['postal_code'] ?>"></div>
                                </div>
                                <h6 class="mt-3">Bank Details</h6>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Account Holder Name</label>
                                    <div class="col-md-9"><input class="form-control" type="text" name="bank_account_name" value="<?= $vendor['bank_account_name'] ?>"></div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Account Number</label>
                                    <div class="col-md-9"><input class="form-control" type="text" name="bank_account_no" value="<?= $vendor['bank_account_no'] ?>"></div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">IFSC</label>
                                    <div class="col-md-9"><input class="form-control" type="text" name="bank_ifsc" value="<?= $vendor['bank_ifsc'] ?>"></div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary w-md">Save Profile</button>
                                </div>
                            </form>

                            <hr class="my-4">
                            <h5>Change Password</h5>
                            <form action="" method="post">
                                <input type="hidden" name="form" value="password">
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Current Password</label>
                                    <div class="col-md-9"><input class="form-control" type="password" name="current_password" required></div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">New Password</label>
                                    <div class="col-md-9"><input class="form-control" type="password" name="new_password" minlength="6" required></div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary w-md">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('vendor/template/footer'); ?>
