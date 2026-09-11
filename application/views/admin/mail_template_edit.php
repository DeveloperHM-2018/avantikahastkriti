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
                            <p>Available placeholders:
                                <?php foreach ($placeholders as $ph): ?>
                                    <code>{{<?= $ph ?>}}</code>
                                <?php endforeach; ?>
                            </p>
                            <form action="" method="post">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label class="col-form-label">Subject *</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="subject" value="<?= $subject ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mt-3">
                                        <label class="col-form-label">Message Body *</label>
                                        <div class="col-md-12">
                                            <textarea class="form-control" name="body" rows="8"><?= $body ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mt-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_enabled" id="is_enabled" value="1" <?= $is_enabled == 1 ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="is_enabled">Enabled</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary w-md">Save</button>
                                    <a href="<?= base_url('mailTemplateAll') ?>" class="btn btn-secondary w-md">Back</a>
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
