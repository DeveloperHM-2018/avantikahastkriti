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
                            <table class="table table-sm table-borderless mb-3">
                                <tr><th style="width:220px;">Vendor</th><td><?= $vendor['business_name'] ?></td></tr>
                                <tr><th>Product Name</th><td><?= $vp['product_name'] ?></td></tr>
                                <tr><th>Category</th><td><?= $category ? $category['category_name'] : '-' ?></td></tr>
                                <tr><th>Sub Category</th><td><?= $sub_category_names ? implode(', ', $sub_category_names) : '-' ?></td></tr>
                                <tr><th>Sub Category Type</th><td><?= $sub_category_type_names ? implode(', ', $sub_category_type_names) : '-' ?></td></tr>
                                <tr><th>Product Type</th><td><?= [1 => 'Normal', 2 => 'Featured', 3 => 'Both'][$vp['product_type']] ?? 'Normal' ?></td></tr>
                                <tr><th>Description</th><td><?= $vp['description'] ?: '-' ?></td></tr>
                                <tr><th>Vendor's Supply Price</th><td>₹<?= $vp['vendor_supply_price'] ?></td></tr>
                                <tr><th>Vendor's Proposed Market Price</th><td><?= $vp['proposed_market_price'] ? '₹' . $vp['proposed_market_price'] : '-' ?></td></tr>
                                <tr><th>Quantity Offered</th><td><?= $vp['quantity_supplied'] ?></td></tr>
                                <tr><th>Marked Out of Stock by Vendor</th><td><?= $vp['is_out_of_stock'] == 1 ? 'Yes' : 'No' ?></td></tr>
                            </table>

                            <?php if (!empty($images)) : ?>
                                <div class="mb-4">
                                    <?php foreach ($images as $img) : ?>
                                        <img src="<?= base_url('upload/product/') . $img['image_path'] ?>" style="width:140px;height:105px;object-fit:contain;border:1px solid #ddd;border-radius:5px;margin:0 8px 8px 0;">
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <p class="text-muted">No images submitted.</p>
                            <?php endif; ?>

                            <?php if ($can_approve) : ?>
                                <form action="" method="post" id="approveForm">
                                    <div class="row mb-3">
                                        <label class="col-md-3 col-form-label">Commission %</label>
                                        <div class="col-md-9">
                                            <?php
                                            // Pre-filled from this vendor's profile default (Vendors > Edit Vendor),
                                            // or from what was already set on this product if it's being re-reviewed
                                            // after an edit - either way, still editable per product.
                                            $commissionDefault = $vp['commission_percent'] !== null ? $vp['commission_percent'] : $vendor['default_commission_percent'];
                                            ?>
                                            <input class="form-control" type="number" step="0.01" min="0" max="100" name="commission_percent" id="commission_percent" value="<?= $commissionDefault ?>" required>
                                            <small class="text-muted">Defaulted from <?= $vendor['business_name'] ?>'s profile commission - change it here if this product needs a different rate.</small>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-md-3 col-form-label">Final Market Price</label>
                                        <div class="col-md-9">
                                            <input class="form-control" type="number" step="0.01" min="0.01" name="market_price" id="market_price" value="<?= $vp['proposed_market_price'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-md-3 col-form-label">Final Sale Price</label>
                                        <div class="col-md-9">
                                            <input class="form-control" type="number" step="0.01" min="0.01" name="sale_price" id="sale_price" required>
                                            <small class="text-muted" id="sale_price_hint"></small>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-md-3 col-form-label">Admin Notes</label>
                                        <div class="col-md-9">
                                            <textarea class="form-control" name="admin_notes" rows="2"></textarea>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <!-- Only the activated submit button's name/value is ever sent, so
                                             "decision" arrives as exactly one of these two regardless of
                                             which is clicked - no hidden-field/duplicate-name ambiguity. -->
                                        <button type="submit" class="btn btn-success" name="decision" value="approve"><i class="fa fa-check"></i> Approve &amp; Publish</button>
                                        <button type="submit" class="btn btn-danger" name="decision" value="reject" formnovalidate onclick="document.getElementById('commission_percent').required = false; document.getElementById('market_price').required = false; document.getElementById('sale_price').required = false;"><i class="fa fa-times"></i> Reject</button>
                                    </div>
                                </form>
                            <?php else : ?>
                                <div class="alert alert-warning">You do not have permission to approve vendor products.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/template/footer'); ?>
<script>
    // Sale Price is derived from the vendor's supply price and the commission
    // % you set, so the vendor always nets exactly their asking price:
    // commission is a % of the sale price, so sale_price = supply_price / (1 - commission/100).
    // Still a plain editable field - typing over the computed value sticks
    // until commission changes again, so a manual override isn't undone
    // unless you recalculate.
    var VENDOR_SUPPLY_PRICE = <?= (float) $vp['vendor_supply_price'] ?>;

    function recalcSalePrice() {
        var commission = parseFloat($('#commission_percent').val());
        var hint = $('#sale_price_hint');
        if (isNaN(commission) || commission < 0 || commission >= 100) {
            hint.text('Enter a commission below 100% to auto-calculate the sale price.');
            return;
        }
        var salePrice = VENDOR_SUPPLY_PRICE / (1 - commission / 100);
        salePrice = Math.round(salePrice * 100) / 100;
        $('#sale_price').val(salePrice);
        hint.text('Calculated so the vendor nets ₹' + VENDOR_SUPPLY_PRICE.toFixed(2) + ' after ' + commission + '% commission (₹' + (salePrice - VENDOR_SUPPLY_PRICE).toFixed(2) + '). Edit directly to override.');
    }

    $('#commission_percent').on('input', recalcSalePrice);
    $(function() {
        recalcSalePrice();
    });
</script>
