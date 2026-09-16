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
                            <div class="alert alert-secondary">
                                Your product will not go live immediately - an admin reviews every submission, may
                                edit details, sets the commission, and publishes it once approved. Your identity is
                                never shown to customers.
                            </div>
                            <form action="" method="post">
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Product Name</label>
                                    <div class="col-md-9">
                                        <input class="form-control" type="text" name="product_name" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Category</label>
                                    <div class="col-md-9">
                                        <select class="form-select" name="category_id">
                                            <option value="">-- Select --</option>
                                            <?php foreach ($categories as $c) : ?>
                                                <option value="<?= $c['category_id'] ?>"><?= $c['category_name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Description</label>
                                    <div class="col-md-9">
                                        <textarea class="form-control" name="description" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Your Supply Price (per unit)</label>
                                    <div class="col-md-9">
                                        <input class="form-control" type="number" step="0.01" min="0.01" name="vendor_supply_price" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Suggested Sale Price (optional)</label>
                                    <div class="col-md-9">
                                        <input class="form-control" type="number" step="0.01" min="0" name="proposed_sale_price">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Quantity Available</label>
                                    <div class="col-md-9">
                                        <input class="form-control" type="number" step="1" min="1" name="quantity_supplied" required>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary w-md">Submit for Review</button>
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
