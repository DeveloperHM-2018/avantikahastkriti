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

            <?php if ($this->session->flashdata('errors') != '') { ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info"><?= $this->session->flashdata('errors'); ?></div>
                    </div>
                </div>
            <?php } ?>

            <?php if (@PREV['promo_code_add'] == 1 || USER_TYPE == '1') { ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="post" enctype="multipart/form-data" name="form_submit_common">
                                    <div class="row">
                                        <div class="col-lg-3 mb-2">
                                            <label for="example-text-input" class="col-form-label">Promo Code</label>
                                            <input class="form-control" type="text" name="promocode" required value="<?= $promocode ?>">
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <label for="example-text-input" class="col-form-label">Discount Type</label>
                                            <select name="discount_type" id="discount_type" class="form-select">
                                                <option value="fixed" <?= $discount_type == 'fixed' ? 'selected' : '' ?>>Fixed Amount (₹)</option>
                                                <option value="percentage" <?= $discount_type == 'percentage' ? 'selected' : '' ?>>Percentage (%)</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <label for="example-text-input" class="col-form-label" id="amount_label"><?= $discount_type == 'percentage' ? 'Percentage (1-100)' : 'Amount' ?></label>
                                            <input class="form-control" type="number" step="0.01" min="0" id="amount_input" name="amount" required value="<?= $amount ?>">
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <label for="example-text-input" class="col-form-label">Minimum Order</label>
                                            <input class="form-control" type="text" name="minimum_order" required value="<?= $minimum_order ?>">
                                        </div>
                                        <div class="col-lg-3 mb-2" id="datepicker2">
                                            <label for="example-text-input" class="col-form-label">Expiry Date</label>
                                            <input type="text" class="form-control" placeholder="dd-mm-yyyy" required readonly data-date-format="dd-mm-yyyy" data-date-container='#datepicker2' data-provide="datepicker" data-date-autoclose="true" value="<?= $expiry_date != "" ? date('d-m-Y', strtotime($expiry_date)) : '' ?>" name="expiry_date">
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <label for="example-text-input" class="col-form-label">Promo Code For</label>
                                            <select name="type" class="form-select">
                                                <option value="1" <?= (@$type ?: '1') == '1' ? 'selected' : '' ?>>For Product Order</option>
                                                <option value="2" <?= @$type == '2' ? 'selected' : '' ?>>For Wallet</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="text-center mt-3">
                                        <button type="submit" id="save_common" class="btn btn-primary w-md">Save</button>
                                    </div>
                                </form>
                                <script>
                                    (function() {
                                        var typeSelect = document.getElementById('discount_type');
                                        var amountLabel = document.getElementById('amount_label');
                                        var amountInput = document.getElementById('amount_input');

                                        function syncDiscountTypeUI() {
                                            var isPercentage = typeSelect.value === 'percentage';
                                            amountLabel.textContent = isPercentage ? 'Percentage (1-100)' : 'Amount';
                                            amountInput.max = isPercentage ? 100 : '';
                                        }

                                        typeSelect.addEventListener('change', syncDiscountTypeUI);
                                        syncDiscountTypeUI();
                                    })();
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">All Promo Code</h4>
                            <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Sr no.</th>
                                        <th>Promo Code</th>
                                        <th>Discount</th>
                                        <th>Expiry</th>
                                        <th>Min Order</th>
                                        <th>Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $allPromo = getAllRowInOrder('promocode', 'promocode', 'ASC');
                                    if ($allPromo) {
                                        $i = 0;
                                        foreach ($allPromo as $all) {
                                            $id = encryptId($all['promocode_id']);
                                    ?>
                                            <tr>
                                                <td><?= ++$i; ?></td>
                                                <td><?= ucwords($all['promocode']) ?></td>
                                                <td><?= @$all['discount_type'] == 'percentage' ? $all['amount'] . '%' : '₹' . $all['amount'] ?></td>
                                                <td><?= date('d-M-Y', strtotime($all['expiry_date'])) ?></td>
                                                <td><?= $all['minimum_order'] ?></td>
                                                <td><?= $all['type'] == '1' ? 'For Product' : 'For Wallet' ?></td>
                                                <td>
                                                    <?php if (@PREV['promo_code_edit'] == 1 || USER_TYPE == '1') { ?>
                                                        <a href="<?= base_url("promoCode?promo=$id"); ?>" class="btn btn-success"><i class="fa fa-edit"></i> Edit</a>
                                                    <?php } ?>
                                                    <?php if (@PREV['promo_code_delete'] == 1 || USER_TYPE == '1') { ?>
                                                        <a onclick="return confirm('Are you want to sure?')" href="<?= base_url("promoCode?dID=$id"); ?>" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                    <?php

                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/template/footer'); ?>