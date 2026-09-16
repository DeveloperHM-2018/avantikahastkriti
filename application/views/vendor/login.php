<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Vendor Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/template/header_link'); ?>
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
                                        <h5 class="text-primary">Vendor Login</h5>
                                        <p>Sign in to your vendor account</p>
                                        <h4 class="text-primary"><?= APP_NAME ?></h4>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="<?= SMALL_LOGO ?>" alt="Logo" class="img-fluid" style="border-radius: 50%;">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0 mt-3">
                            <?php if ($this->session->flashdata('errors') != '') { ?>
                                <div class="alert alert-info"><?= $this->session->flashdata('errors'); ?></div>
                            <?php } ?>
                            <div class="p-2">
                                <form class="form-horizontal" action="" method="post">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" required name="email_id" value="<?= set_value('email_id') ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" class="form-control" required name="password">
                                    </div>
                                    <div class="mt-4 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">Log In</button>
                                    </div>
                                </form>
                                <div class="mt-3 text-center">
                                    <a href="<?= base_url('vendor/register') ?>">New vendor? Register here</a>
                                </div>
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
