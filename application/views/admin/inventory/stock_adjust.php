<?php $this->load->view('admin/template/header', $title); ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0 "><?= $title ?></h2>
                        <a href="<?= base_url('stockLedger?id=' . $id) ?>" class="btn btn-info"><i class="fa fa-history"></i> View History</a>
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
                            <table class="table table-sm table-borderless mb-4">
                                <tr>
                                    <th style="width:220px;">Product</th>
                                    <td><?= ucwords($product['product_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Current Quantity</th>
                                    <td><span class="fs-5 fw-bold"><?= $product['quantity'] ?> <?= $product['quantity_type'] ?></span></td>
                                </tr>
                                <tr>
                                    <th>Stock Status</th>
                                    <td><?= $product['is_out_of_stock'] == 1 ? statusView('danger', 'Out of Stock') : statusView('success', 'In Stock') ?>
                                        <?php if ($product['is_out_of_stock_override'] !== null) : ?>
                                            <span class="badge bg-secondary">Manually overridden</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>

                            <form action="" method="post">
                                <input type="hidden" name="product_id" value="<?= $id ?>">
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Variant (optional)</label>
                                    <div class="col-md-9">
                                        <select class="form-select" name="variant_id">
                                            <option value="">-- Product-level stock (no variant) --</option>
                                            <?php foreach ($variants as $v) : ?>
                                                <option value="<?= encryptId($v['variant_id']) ?>">
                                                    <?= $v['size'] ?> / <?= $v['color'] ?> - current: <?= $v['stock_quantity'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Direction</label>
                                    <div class="col-md-9">
                                        <select class="form-select" name="direction" required>
                                            <option value="increase">Increase (restock)</option>
                                            <option value="decrease">Decrease (damage/loss/correction)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Quantity</label>
                                    <div class="col-md-9">
                                        <input class="form-control" type="number" step="any" min="0.01" name="adjust_quantity" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Reason</label>
                                    <div class="col-md-9">
                                        <textarea class="form-control" name="reason" rows="2" maxlength="255" required placeholder="e.g. Physical stock count correction, damaged goods written off, new supplier delivery"></textarea>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary w-md">Save Adjustment</button>
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
