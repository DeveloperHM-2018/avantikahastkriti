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
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label class="col-form-label">Meta Title</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="meta_title" value="<?= $meta_title ?>" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mt-3">
                                        <label class="col-form-label">Meta Description</label>
                                        <div class="col-md-12">
                                            <textarea class="form-control" name="meta_description" rows="3" maxlength="500"><?= $meta_description ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mt-3">
                                        <label class="col-form-label">Meta Keywords</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="meta_keywords" value="<?= $meta_keywords ?>" placeholder="comma, separated, keywords" maxlength="500">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary w-md">Save</button>
                                    <a href="<?= base_url('metaData') ?>" class="btn btn-secondary w-md">Back</a>
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
