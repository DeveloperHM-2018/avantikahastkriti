<!doctype html>
<html lang="en">


<head>
    <meta charset="utf-8" />
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php $this->load->view('admin/template/header_link'); ?>
    <style>
        label#password-error {
            position: absolute;
            top: 40px;
        }
    </style>
</head>

<body>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">Welcome Back !</h5>
                                        <p>Sign in to continue to</p>
                                        <h4 class="text-primary"><?= APP_NAME ?></h4>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="<?= SMALL_LOGO ?>" alt="Logo" class="img-fluid" style="border-radius: 50%;">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0 mt-3">

                            <?php if ($this->session->flashdata('login_error') != '') {
                            ?>
                                <div class="alert alert-danger">
                                    <span><?= $this->session->flashdata('login_error'); ?></span>
                                </div>
                            <?php
                            } ?>
                            <div class="p-2">
                                <form class="form-horizontal" action="" method="post" name="form_submit_common">
                                    <div class="mb-3">
                                        <label class="form-label">Contact Number</label>
                                        <input type="text" class="form-control input-mask" id="input-repeat" data-inputmask="'mask': '9', 'repeat': 10, 'greedy' : false" placeholder="Enter Contact Number" required name="contact_no" value="<?= set_value('contact_no') ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <div class="input-group auth-pass-inputgroup">
                                            <input type="password" class="form-control" placeholder="Enter password" required aria-label="Password" name="password" value="<?= set_value('password') ?>" aria-describedby="password-addon">
                                            <button class="btn btn-light " type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                        </div>
                                    </div>

                                    <div class="mt-5 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light" id="save_common" type="submit">Log In</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $this->load->view('admin/template/footer_link'); ?>
</body>

</html>