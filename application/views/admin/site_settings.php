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
                                <h5>Social Media Links</h5>
                                <p class="text-muted">Shown as icons in the storefront header and footer. Leave a field blank to hide that icon.</p>
                                <div class="row">
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">Facebook URL</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="facebook_url" placeholder="https://www.facebook.com/yourpage" value="<?= $facebook_url ?>">
                                            <?= form_error('facebook_url', '<span class="text-danger">', '</span>') ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">Instagram URL</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="instagram_url" placeholder="https://www.instagram.com/yourpage" value="<?= $instagram_url ?>">
                                            <?= form_error('instagram_url', '<span class="text-danger">', '</span>') ?>
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
