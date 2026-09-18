<?php $this->load->view('vendor/template/header', $title); ?>

<style>
    .image-container {
        display: inline-block;
        position: relative;
        margin: 10px;
    }

    .preview-image {
        width: 160px;
        height: 120px;
        display: block;
        border-radius: 5px;
        object-fit: contain;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .delete-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: red;
        color: white;
        border: none;
        cursor: pointer;
        padding: 2px 6px;
        font-size: 14px;
        border-radius: 3px;
    }
</style>

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
                    <div class="col-12">
                        <div class="alert alert-info"><?= $this->session->flashdata('errors'); ?></div>
                    </div>
                </div>
            <?php } ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <?php if ($vp) : ?>
                                <div class="alert alert-secondary">
                                    Pricing can't be changed once submitted - contact admin if a price needs to
                                    change. Any other change here sends this product back to Pending for admin to
                                    re-review before it goes live<?= $vp['status'] == 1 ? ' (it stays live with its current details until then)' : '' ?>.
                                </div>
                            <?php else : ?>
                                <div class="alert alert-secondary">
                                    Your product will not go live immediately - an admin reviews every submission, may
                                    edit details, sets the commission, and publishes it once approved. Your identity is
                                    never shown to customers.
                                </div>
                            <?php endif; ?>
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="product_name" value="<?= @$vp['product_name'] ?>" required>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label">Category <span class="text-danger">*</span></label>
                                        <select class="form-select select2" name="category_id" id="category" required onchange="getCategory(this.value)">
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $c) : ?>
                                                <option value="<?= $c['category_id'] ?>" <?= @$vp['category_id'] == $c['category_id'] ? 'selected' : '' ?>><?= $c['category_name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label">Sub Category <span class="text-danger">*</span></label>
                                        <select class="form-select select2" name="sub_category_id[]" multiple data-placeholder="Select sub category" id="sub_category" required onchange="getSubCategoryType()">
                                            <?php foreach ($sub_categories as $c) : ?>
                                                <option value="<?= $c['sub_category_id'] ?>" <?= in_array($c['sub_category_id'], $selected_sub_category_id) ? 'selected' : '' ?>><?= $c['sub_category_name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label">Sub Category Type</label>
                                        <select class="form-select select2" name="sub_category_type_id[]" multiple id="sub_category_type">
                                            <?php foreach ($sub_category_type_options as $c) : ?>
                                                <option value="<?= $c['sub_category_type_id'] ?>" <?= in_array($c['sub_category_type_id'], $selected_sub_category_type_id) ? 'selected' : '' ?>><?= $c['sub_category_type_name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label">Product Type</label>
                                        <select class="form-select" name="product_type">
                                            <option value="1" <?= @$vp['product_type'] == 1 || !$vp ? 'selected' : '' ?>>Normal</option>
                                            <option value="2" <?= @$vp['product_type'] == 2 ? 'selected' : '' ?>>Featured</option>
                                            <option value="3" <?= @$vp['product_type'] == 3 ? 'selected' : '' ?>>Both</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label">Market Price (MRP) <span class="text-danger">*</span></label>
                                        <input class="form-control" type="number" step="0.01" min="0.01" name="proposed_market_price" value="<?= @$vp['proposed_market_price'] ?>" <?= $vp ? 'disabled' : 'required' ?>>
                                        <?php if ($vp) : ?><small class="text-muted">Locked after submission.</small><?php endif; ?>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" name="is_out_of_stock" id="is_out_of_stock" value="1" <?= @$vp['is_out_of_stock'] == 1 ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="is_out_of_stock">Mark as Out of Stock</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label">Your Supply Price (what we pay you, per unit) <span class="text-danger">*</span></label>
                                        <input class="form-control" type="number" step="0.01" min="0.01" name="vendor_supply_price" value="<?= @$vp['vendor_supply_price'] ?>" <?= $vp ? 'disabled' : 'required' ?>>
                                        <?php if ($vp) : ?><small class="text-muted">Locked after submission.</small><?php endif; ?>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label">Stock Available in Unit <span class="text-danger">*</span></label>
                                        <input class="form-control" type="number" step="1" min="1" name="quantity_supplied" value="<?= @$vp['quantity_supplied'] ?>" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12 mb-3">
                                        <label class="form-label">Description <span class="text-danger">*</span></label>
                                        <textarea name="description" id="editor" rows="8" required><?= @$vp['description'] ?></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12 mt-2 mb-3">
                                        <hr>
                                        <h5>SEO / Meta (optional)</h5>
                                    </div>
                                    <div class="col-lg-12 mb-3">
                                        <label class="form-label">Meta Title</label>
                                        <input class="form-control" type="text" name="meta_title" value="<?= @$vp['meta_title'] ?>" maxlength="255">
                                    </div>
                                    <div class="col-lg-12 mb-3">
                                        <label class="form-label">Meta Description</label>
                                        <textarea class="form-control" name="meta_description" rows="2" maxlength="500"><?= @$vp['meta_description'] ?></textarea>
                                    </div>
                                    <div class="col-lg-12 mb-3">
                                        <label class="form-label">Meta Keywords</label>
                                        <input class="form-control" type="text" name="meta_keywords" value="<?= @$vp['meta_keywords'] ?>" maxlength="500">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label style="color: gray">Note: Image size 600x400 recommended (max 5 MB per image)</label>
                                        <label class="form-label d-block">Product Images <?= $vp ? '(add more)' : '<span class="text-danger">*</span>' ?></label>
                                        <input type="file" class="form-control image" accept="image/*" multiple <?= $vp ? '' : 'required' ?> name="image[]">
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="gallery"></div>
                                    </div>
                                    <?php if ($vp && !empty($images)) : ?>
                                        <div class="col-lg-12 mt-2">
                                            <label class="form-label d-block">Already Uploaded</label>
                                            <div id="existing-images">
                                                <?php foreach ($images as $img) : ?>
                                                    <div class="image-container" id="vp-image-<?= $img['vendor_product_image_id'] ?>">
                                                        <img src="<?= base_url('upload/product/') . $img['image_path'] ?>" class="preview-image">
                                                        <button type="button" class="delete-btn btn-delete-existing-image" data-id="<?= encryptId($img['vendor_product_image_id']) ?>">X</button>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="text-center mt-3">
                                    <button type="submit" class="btn btn-primary w-md"><?= $vp ? 'Save Changes' : 'Submit for Review' ?></button>
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
<script>
    $(function() {
        if (typeof initSample === 'function') {
            initSample();
        }
    });

    function getCategory(val) {
        $.ajax({
            type: 'POST',
            url: "<?= base_url('vendor/getSubCategory') ?>",
            data: 'category_id=' + val,
            success: function(data) {
                $('#sub_category').html(data);
                getSubCategoryType();
            }
        });
    }

    function getSubCategoryType() {
        var val = $('#sub_category').val();
        $.ajax({
            type: 'POST',
            url: "<?= base_url('vendor/getSubCategoryType') ?>",
            data: {
                sub_category_id: val
            },
            success: function(data) {
                $('#sub_category_type').html(data);
            }
        });
    }

    var imagesPreview = function(input, placeToInsertImagePreview) {
        if (input.files) {
            for (let i = 0; i < input.files.length; i++) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    let imageContainer = $('<div>').addClass('image-container');
                    let image = $('<img>').attr('src', event.target.result).addClass('preview-image');
                    let deleteBtn = $('<button type="button">X</button>').addClass('delete-btn').click(function() {
                        imageContainer.remove();
                    });
                    imageContainer.append(image).append(deleteBtn);
                    $(placeToInsertImagePreview).append(imageContainer);
                };
                reader.readAsDataURL(input.files[i]);
            }
        }
    };

    $('.image').on('change', function() {
        $('.gallery').html('');
        imagesPreview(this, 'div.gallery');
    });

    $(document).on('click', '.btn-delete-existing-image', function() {
        var btn = $(this);
        if (!confirm('Delete this image?')) return;
        $.ajax({
            type: 'POST',
            url: "<?= base_url('vendor/productImageDelete') ?>",
            data: {
                id: btn.data('id')
            },
            dataType: 'JSON',
            success: function(res) {
                if (res.status) {
                    btn.closest('.image-container').remove();
                } else {
                    alert(res.message);
                }
            }
        });
    });
</script>
