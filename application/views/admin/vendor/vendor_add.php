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
                    <div class="col-10 offset-1">
                        <div class="alert alert-info"><?= $this->session->flashdata('errors'); ?></div>
                    </div>
                </div>
            <?php } ?>

            <div class="row">
                <div class="col-10 offset-1">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post" enctype="multipart/form-data">
                                <h6>Business Details</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Business / Vendor Name</label>
                                        <input class="form-control" type="text" name="business_name" value="<?= @$vendor['business_name'] ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Contact Person</label>
                                        <input class="form-control" type="text" name="contact_name" value="<?= @$vendor['contact_name'] ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mobile Number</label>
                                        <input class="form-control" type="text" name="contact_no" value="<?= @$vendor['contact_no'] ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input class="form-control" type="email" name="email_id" value="<?= @$vendor['email_id'] ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Password <?= $id ? '(leave blank to keep current)' : '' ?></label>
                                        <input class="form-control" type="password" name="password" <?= $id ? '' : 'required' ?>>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="1" <?= (!$id || @$vendor['status'] == 1) ? 'selected' : '' ?>>Active</option>
                                            <option value="0" <?= @$vendor['status'] == 0 ? 'selected' : '' ?>>Pending</option>
                                            <option value="3" <?= @$vendor['status'] == 3 ? 'selected' : '' ?>>Suspended</option>
                                            <option value="2" <?= @$vendor['status'] == 2 ? 'selected' : '' ?>>Rejected</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">GST Number</label>
                                        <input class="form-control" type="text" name="gst_number" value="<?= @$vendor['gst_number'] ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">PAN Number</label>
                                        <input class="form-control" type="text" name="pan_number" value="<?= @$vendor['pan_number'] ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Default Commission %</label>
                                        <input class="form-control" type="number" step="0.01" min="0" max="100" name="default_commission_percent" value="<?= @$vendor['default_commission_percent'] ?>">
                                    </div>
                                </div>

                                <h6 class="mt-3">Business Address</h6>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" name="address" rows="2"><?= @$vendor['address'] ?></textarea>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">City</label>
                                        <input class="form-control" type="text" name="city" value="<?= @$vendor['city'] ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">State</label>
                                        <input class="form-control" type="text" name="state" value="<?= @$vendor['state'] ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Postal Code</label>
                                        <input class="form-control" type="text" name="postal_code" value="<?= @$vendor['postal_code'] ?>">
                                    </div>
                                </div>

                                <h6 class="mt-3">Bank Details (for payouts)</h6>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Account Holder Name</label>
                                        <input class="form-control" type="text" name="bank_account_name" value="<?= @$vendor['bank_account_name'] ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Account Number</label>
                                        <input class="form-control" type="text" name="bank_account_no" value="<?= @$vendor['bank_account_no'] ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">IFSC Code</label>
                                        <input class="form-control" type="text" name="bank_ifsc" value="<?= @$vendor['bank_ifsc'] ?>">
                                    </div>
                                </div>

                                <h6 class="mt-3">Documents (PDF/JPG/PNG, max 5MB each - optional)</h6>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">GST Certificate</label>
                                        <input class="form-control" type="file" name="gst_certificate" accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">PAN Card</label>
                                        <input class="form-control" type="file" name="pan_card" accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Bank Proof</label>
                                        <input class="form-control" type="file" name="bank_proof" accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary w-md">Save Vendor</button>
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
