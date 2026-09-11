<?php $this->load->view('admin/template/header', $title); ?>
<?php $id = $this->input->get('id'); ?>
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
                <div class="col-6 offset-3">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post" enctype="multipart/form-data">
                                <h5>Shipping Charges</h5>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label for="example-text-input" class="col-form-label">Minimum Order Amount</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="min_amount" value="<?= $min_amount ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="example-text-input" class="col-form-label">Shipping Charge</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="amount" value="<?= $amount ?>">
                                        </div>
                                    </div>
                                    <hr class="mt-3">
                                    <div class="col-lg-12" style="display:none">
                                        <label for="example-text-input" class="col-form-label">Pincode (no longer restricts the website &mdash; delivery is offered across all of India; only kept for the mobile app, if used)</label>
                                        <div class="col-md-12">
                                            <textarea name="is_delivery_available" class="form-control" rows="5" required><?= $is_delivery_available ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="example-text-input" class="col-form-label">Packaging Charge</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="packaging_charge" value="<?= $packaging_charge ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-6" style="display:none">
                                        <label for="example-text-input" class="col-form-label">Max COD Available (not enforced &mdash; COD is enabled for all orders)</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="min_cod_available" value="<?= $min_cod_available ?>" disabled>
                                            <input type="hidden" name="min_cod_available" value="<?= $min_cod_available ?>">
                                        </div>
                                    </div>

                                </div>
                                <div class="text-center mt-3">
                                    <button type="submit" id="save" class="btn btn-primary w-md">Save</button>
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