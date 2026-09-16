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
                            <h5>Profile Details</h5>
                            <form action="<?= base_url('updateProfile') ?>" method="post">
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Name</label>
                                    <div class="col-md-9"><input class="form-control" type="text" name="name" value="<?= $admin['name'] ?>" required></div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Contact Number</label>
                                    <div class="col-md-9"><input class="form-control" type="text" name="contact_no" value="<?= $admin['contact_no'] ?>" required></div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Email</label>
                                    <div class="col-md-9"><input class="form-control" type="email" name="email_id" value="<?= $admin['email_id'] ?>"></div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Role</label>
                                    <div class="col-md-9"><input class="form-control" type="text" value="<?= $admin['user_type'] == 1 ? 'Super Admin' : 'Sub Admin' ?>" disabled></div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary w-md">Save Profile</button>
                                </div>
                            </form>

                            <hr class="my-4">
                            <h5>Change Password</h5>
                            <form action="<?= base_url('changePassword') ?>" method="post">
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Current Password</label>
                                    <div class="col-md-9"><input class="form-control" type="password" name="current_password" required></div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">New Password</label>
                                    <div class="col-md-9"><input class="form-control" type="password" name="new_password" minlength="6" required></div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Confirm New Password</label>
                                    <div class="col-md-9"><input class="form-control" type="password" name="confirm_password" minlength="6" required></div>
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

<?php $this->load->view('admin/template/footer'); ?>
