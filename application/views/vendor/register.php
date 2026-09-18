<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Vendor Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/template/header_link'); ?>
</head>

<body>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8">
                    <div class="card overflow-hidden">
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">Become a Partner</h5>
                                        <p>Register your business to start selling on</p>
                                        <h4 class="text-primary"><?= APP_NAME ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0 mt-3">
                            <?php if ($this->session->flashdata('errors') != '') { ?>
                                <div class="alert alert-info"><?= $this->session->flashdata('errors'); ?></div>
                            <?php } ?>
                            <form action="" method="post" enctype="multipart/form-data">
                                <h6 class="mt-3">Business Details</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Business / Vendor Name</label>
                                        <input class="form-control" type="text" name="business_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Contact Person</label>
                                        <input class="form-control" type="text" name="contact_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mobile Number</label>
                                        <input class="form-control" type="text" name="contact_no" maxlength="10" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input class="form-control" type="email" name="email_id" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Password</label>
                                        <input class="form-control" type="password" name="password" minlength="6" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">GST Number</label>
                                        <input class="form-control" type="text" name="gst_number">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">PAN Number</label>
                                        <input class="form-control" type="text" name="pan_number">
                                    </div>
                                </div>

                                <h6 class="mt-3">Business Address</h6>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" name="address" rows="2" required></textarea>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">City</label>
                                        <input class="form-control" type="text" name="city">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">State</label>
                                        <input class="form-control" type="text" name="state">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Postal Code</label>
                                        <input class="form-control" type="text" name="postal_code">
                                    </div>
                                </div>

                                <h6 class="mt-3">Bank Details (for payouts)</h6>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Account Holder Name</label>
                                        <input class="form-control" type="text" name="bank_account_name">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Account Number</label>
                                        <input class="form-control" type="text" name="bank_account_no">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">IFSC Code</label>
                                        <input class="form-control" type="text" name="bank_ifsc">
                                    </div>
                                </div>

                                <h6 class="mt-3">Documents (PDF/JPG/PNG, max 5MB each)</h6>
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
                                        <label class="form-label">Bank Proof (cancelled cheque / passbook)</label>
                                        <input class="form-control" type="file" name="bank_proof" accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                </div>

                                <div class="mt-4 d-grid">
                                    <button class="btn btn-primary waves-effect waves-light" type="submit">Submit for Review</button>
                                </div>
                                <div class="mt-3 text-center">
                                    <a href="<?= base_url('vendor/login') ?>">Already registered? Log in</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $this->load->view('admin/template/footer_link'); ?>
</body>

</html>
