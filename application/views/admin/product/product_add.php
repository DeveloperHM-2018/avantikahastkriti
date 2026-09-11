<?php $this->load->view('admin/template/header', $title); ?>

<style>
    .image-container {
        display: inline-block;
        position: relative;
        margin: 10px;
    }

    .preview-image {
        width: 200px;
        height: 150px;
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

    .delete-btn:hover {
        background: darkred;
    }
</style>

<?php
$id = $this->input->get('id');
$read_only = "";
if ($view_type) {
    $read_only = "readonly disabled";
}
?>
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
            <form action="" method="post" enctype="multipart/form-data" name="form_submit_common">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 mb-3">
                                        <div class="row">
                                            <label for="example-text-input" class="col-md-5 col-form-label">Product Name <span class="text-danger">*</span></label>
                                            <div class="col-md-12">
                                                <input class="form-control" type="text" <?= $read_only ?> name="product_name" required value="<?= $product_name ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <div class="row">
                                            <label class="col-md-5 control-label">Category<span class="text-danger">*</span></label>
                                            <div class="col-md-12">
                                                <!-- <input type="hidden" value="1" name="sub_category_id"> -->
                                                <select class="form-select select2" name="category_id" <?= $read_only ?> required data-placeholder="Select Category" id="category" onchange="getCategory(this.value)">
                                                    <?php
                                                    $subCate = getRowsByMoreIdWithOrder('category', ['is_delete' => '1'], 'category_name', 'ASC');
                                                    foreach ($subCate as $c) {
                                                    ?>
                                                        <option value=" <?= $c['category_id'] ?>" <?= $c['category_id'] == $category_id ? 'selected' : '' ?>><?= $c['category_name'] ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <div class="row">
                                            <label class="col-md-5 control-label">Sub Category<span class="text-danger">*</span></label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2" name="sub_category_id[]" multiple
                                                    data-placeholder="Select sub category" id="sub_category" required onchange="getSubCategoryType()">
                                                    <?php
                                                    $sub_category_id = is_array($sub_category_id) ? $sub_category_id : [];
                                                    $subCate = getRowsByMoreIdWithOrder('sub_category', "category_id = '$category_id' AND is_delete = '1'", 'sub_category_name', 'ASC');
                                                    foreach ($subCate as $c) {
                                                    ?>
                                                        <option value="<?= $c['sub_category_id'] ?>"
                                                            <?= in_array($c['sub_category_id'], $sub_category_id) ? 'selected' : '' ?>>
                                                            <?= $c['sub_category_name'] ?>
                                                        </option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <div class="row">
                                            <label for="example-text-input" class="col-md-5 col-form-label">Sub Category Type <span class="text-danger">*</span></label>
                                            <div class="col-md-7">
                                                <select class="form-control select2" name="sub_category_type_id[]" multiple id="sub_category_type">
                                                    <?php
                                                    $sub_category_type_id = is_array($sub_category_type_id) ? $sub_category_type_id : [];
                                                    $subCate = getRowsByMoreIdWithOrder('sub_category_type', "is_delete = '1'", 'sub_category_type_name', 'ASC');
                                                    if ($sub_category_id) {
                                                        $subCate = $this->CommonModel->getRowByWhereIn('sub_category_type', 'sub_category_id', $sub_category_id);
                                                    }
                                                    if ($subCate) {
                                                        foreach ($subCate as $c) {
                                                    ?>
                                                            <option value="<?= $c['sub_category_type_id'] ?>"
                                                                <?= in_array($c['sub_category_type_id'], $sub_category_type_id) ? 'selected' : '' ?>>
                                                                <?= $c['sub_category_type_name'] ?>
                                                            </option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <div class="row">
                                            <label for="example-text-input" class="col-md-5 col-form-label">Product Type <span class="text-danger">*</span></label>
                                            <div class="col-md-7">
                                                <select class="form-control select2" name="product_type" <?= $read_only ?>>
                                                    <option value="1" <?= $product_type == '1' ? 'selected' : '' ?>>Normal</option>
                                                    <option value="2" <?= $product_type == '2' ? 'selected' : '' ?>>Featured</option>
                                                    <option value="3" <?= $product_type == '3' ? 'selected' : '' ?>>Both</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <div class="row">
                                            <label for="example-text-input" class="col-md-5 col-form-label">Stock Avilable in Unit <span class="text-danger">*</span></label>
                                            <div class="col-md-7">
                                                <input class="form-control" type="number" name="max_quantity" <?= $read_only ?> required value="<?= $max_quantity ? $max_quantity : 1 ?>" min="1">
                                            </div>
                                        </div>
                                    </div>
                                    <!--<div class="col-lg-6 mb-3">-->
                                    <!--    <div class="row">-->
                                    <!--        <label for="example-text-input" class="col-md-5 col-form-label">Max Order Quantity <span class="text-danger">*</span></label>-->
                                    <!--        <div class="col-md-7">-->
                                    <!--<input class="form-control" type="hidden" name="max_quantity" <?= $read_only ?> required value="<?= $max_quantity ? $max_quantity : 1 ?>" min="1">-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <!--<div class="col-lg-6 mb-3">-->
                                    <!--    <div class="row">-->
                                    <!--        <label for="example-text-input" class="col-md-5 col-form-label">Quantity Type <span class="text-danger">*</span></label>-->
                                    <!--        <div class="col-md-7">-->
                                    <input class="form-control" type="hidden" name="quantity_type" <?= $read_only ?> required value="<?= $quantity_type ? $quantity_type : 'kg' ?>" min="1">
                                    <!--<select class="form-select" name="quantity_type" <?= $read_only ?>>-->
                                    <!--    <option value="">Select Type</option>-->
                                    <!--    <option value="gm" <?= $quantity_type == 'gm' ? 'selected' : '' ?>>gm</option>-->
                                    <!--    <option value="kg" <?= $quantity_type == 'kg' ? 'selected' : '' ?>>kg</option>-->
                                    <!--    <option value="ml" <?= $quantity_type == 'ml' ? 'selected' : '' ?>>ml</option>-->
                                    <!--    <option value="L" <?= $quantity_type == 'L' ? 'selected' : '' ?>>L</option>-->
                                    <!--    <option value="Pc" <?= $quantity_type == 'Pc' ? 'selected' : '' ?>>Pc</option>-->
                                    <!--</select>-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <!--<div class="col-lg-6 mb-3">-->
                                    <!--    <div class="row">-->
                                    <!--        <label for="example-text-input" class="col-md-5 col-form-label">Quantity <span class="text-danger">*</span></label>-->
                                    <!--        <div class="col-md-7">-->
                                    <input class="form-control" type="hidden" name="quantity" <?= $read_only ?> required value="<?= $quantity ? $quantity : "1" ?>">
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <div class="col-lg-6 mb-3">
                                        <div class="row">
                                            <label for="example-text-input" class="col-md-5 col-form-label">Market Price <span class="text-danger">*</span></label>
                                            <div class="col-md-7">
                                                <input class="form-control" type="number" name="market_price" <?= $read_only ?> required value="<?= $market_price ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <div class="row">
                                            <label for="example-text-input" class="col-md-5 col-form-label">Sale Price</label>
                                            <div class="col-md-7">
                                                <input class="form-control" type="number" name="sale_price" <?= $read_only ?> required value="<?= $sale_price ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <div class="row">
                                            <label class="col-md-5 col-form-label">Stock Status</label>
                                            <div class="col-md-7">
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" name="is_out_of_stock" id="is_out_of_stock" value="1" <?= $read_only ?> <?= $is_out_of_stock == 1 ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="is_out_of_stock">Mark as Out of Stock</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 mb-3">
                                        <div class="row">
                                            <label for="example-text-input" class="col-md-1 col-form-label">Description</label>
                                            <div class="col-md-11">
                                                <textarea name="description" style="width: 100%;" id="editor" <?= $read_only ?> rows="10"><?= $description ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12 mt-2 mb-3">
                                        <hr>
                                        <h5>SEO / Meta</h5>
                                    </div>
                                    <div class="col-lg-12 mb-3">
                                        <div class="row">
                                            <label class="col-md-1 col-form-label">Meta Title</label>
                                            <div class="col-md-11">
                                                <input class="form-control" type="text" name="meta_title" <?= $read_only ?> value="<?= $meta_title ?>" maxlength="255">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-3">
                                        <div class="row">
                                            <label class="col-md-1 col-form-label">Meta Description</label>
                                            <div class="col-md-11">
                                                <textarea class="form-control" name="meta_description" <?= $read_only ?> rows="3" maxlength="500"><?= $meta_description ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-3">
                                        <div class="row">
                                            <label class="col-md-1 col-form-label">Meta Keywords</label>
                                            <div class="col-md-11">
                                                <input class="form-control" type="text" name="meta_keywords" <?= $read_only ?> value="<?= $meta_keywords ?>" maxlength="500">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label style="color: gray">Note:- Image Size 600X400 (Max 5 MB per image)</label>
                                        <div class="row">
                                            <label for="example-text-input" class="col-md-5 col-form-label">Product Image <span class="text-danger">*</span></label>
                                            <div class="col-md-7">
                                                <input type="file" class="form-control image" accept="image/*" multiple <?= $read_only ?> <?= isset($id) ? '' : 'required' ?> name="image[]">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-3">
                                        <div class="gallery"></div>
                                    </div>
                                    <div class="col-lg-12 mt-2">
                                        <div class="row" id="existing-images">
                                            <?php
                                            if (isset($id)) {
                                                // $numImage = getNumRows('product_image', "product_id = '" . decryptId($id) . "'"); // Unused variable
                                                if ($image_all) {
                                                    foreach ($image_all as $img) {
                                                        $imgId = encryptId($img['product_image_id']);
                                                        $imgData = $img['image_path'];
                                                        $isMain = (isset($img['is_main']) && $img['is_main'] == 1);
                                                        $pId = encryptId($img['product_id']);
                                            ?>
                                                        <div class="col-lg-3 mb-2" id="product-image-<?= $imgId ?>">
                                                            <div class="image-container" style="<?= $isMain ? 'border: 3px solid green; padding: 5px; border-radius: 5px;' : '' ?>">
                                                                <img src="<?= base_url("upload/product/") . $imgData ?>" class="preview-image">
                                                                <?php if (!$view_type) { ?>
                                                                    <div style="margin-top: 10px; text-align: center">
                                                                        <?php if (!$isMain) { ?>
                                                                            <button type="button" class="btn btn-sm btn-success btn-set-main" style="margin-right: 5px" data-id="<?= $imgId ?>" data-product-id="<?= $pId ?>">
                                                                                <i class="fa fa-star"></i> Main
                                                                            </button>
                                                                        <?php } else { ?>
                                                                            <span class="badge bg-success" style="margin-right: 5px; padding: 8px;"><i class="fa fa-check"></i> Main Image</span>
                                                                        <?php } ?>
                                                                        <button type="button" class="btn btn-sm btn-danger btn-delete-image" data-id="<?= $imgId ?>">
                                                                            <i class="fa fa-trash"></i> Delete
                                                                        </button>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                            <?php
                                                    }
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if (!$view_type) { ?>
                                <div class="text-center mb-3">
                                    <div class="progress mb-2 d-none" id="upload-progress-wrap" style="height: 20px; max-width: 400px; margin: 0 auto;">
                                        <div class="progress-bar" id="upload-progress-bar" role="progressbar" style="width: 0%">0%</div>
                                    </div>
                                    <button type="submit" id="save_common" class="btn btn-primary w-md">Save</button>
                                </div>
                            <?php }  ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



<?php $this->load->view('admin/template/footer'); ?>
<script>
    $(document).ready(function() {
        initSample();
        let id = "<?= $id ?>";
        if (id == "") {
            getCategory('1');
        }
    });

    function getCategory(val) {
        $.ajax({
            type: "POST",
            url: "<?= base_url("getSubCategory") ?>",
            data: 'category_id=' + val,
            beforeSend: function() {
                $(".loader").show();
            },
            success: function(data) {
                $("#sub_category").html(data);
                $(".loader").hide();
            }
        });
    }

    function getSubCategoryType() {
        var val = $("#sub_category").val();
        $.ajax({
            type: "POST",
            url: "<?= base_url("getSubCategoryType") ?>",
            data: { sub_category_id: val },
            beforeSend: function() {
                $(".loader").show();
            },
            success: function(data) {
                $("#sub_category_type").html(data);
                $(".loader").hide();
            }
        });
    }

    var imagesPreview = function(input, placeToInsertImagePreview) {
        if (input.files) {
            var filesAmount = input.files.length;

            for (let i = 0; i < filesAmount; i++) {
                let reader = new FileReader();

                reader.onload = function(event) {
                    let imageContainer = $('<div>').addClass('image-container');

                    let image = $('<img>').attr('src', event.target.result).addClass('preview-image');

                    let deleteBtn = $('<button>X</button>').addClass('delete-btn').click(function() {
                        imageContainer.remove(); // Remove image on delete button click
                    });

                    imageContainer.append(image).append(deleteBtn);
                    $(placeToInsertImagePreview).append(imageContainer);
                };

                reader.readAsDataURL(input.files[i]);
            }
        }
    };

    var MAX_IMAGE_SIZE = 5 * 1024 * 1024; // 5 MB, keep in sync with MAX_PRODUCT_IMAGE_SIZE server-side

    $('.image').on('change', function() {
        var input = this;
        var validFiles = [];
        var rejected = [];

        for (let i = 0; i < input.files.length; i++) {
            if (input.files[i].size > MAX_IMAGE_SIZE) {
                rejected.push(input.files[i].name);
            } else {
                validFiles.push(input.files[i]);
            }
        }

        if (rejected.length) {
            toastr.error(rejected.join(', ') + ' — each image must be 5 MB or smaller and was not added.');
            // Rebuild the input's FileList excluding the oversized files.
            var dt = new DataTransfer();
            validFiles.forEach(file => dt.items.add(file));
            input.files = dt.files;
        }

        $('.gallery').html('');
        imagesPreview(input, 'div.gallery');
    });

    // Delete an already-saved product image via AJAX (no page reload, no broken links).
    $(document).on('click', '.btn-delete-image', function() {
        var btn = $(this);
        var imgId = btn.data('id');
        swal({
            title: 'Delete this image?',
            text: 'This cannot be undone.',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((confirmed) => {
            if (!confirmed) return;
            $.ajax({
                type: 'POST',
                url: "<?= base_url('productImageD') ?>",
                data: {
                    id: imgId
                },
                dataType: 'JSON',
                success: function(data) {
                    if (data.status) {
                        toastr.success(data.message);
                        $('#product-image-' + imgId).remove();
                    } else {
                        toastr.error(data.message);
                    }
                },
                error: function() {
                    toastr.error('Could not delete image. Please try again.');
                }
            });
        });
    });

    // Mark an already-saved product image as the main image via AJAX.
    $(document).on('click', '.btn-set-main', function() {
        var btn = $(this);
        $.ajax({
            type: 'POST',
            url: "<?= base_url('productImageMain') ?>",
            data: {
                id: btn.data('id'),
                product_id: btn.data('product-id')
            },
            dataType: 'JSON',
            beforeSend: function() {
                btn.prop('disabled', true);
            },
            success: function(data) {
                if (data.status) {
                    toastr.success(data.message);
                    location.reload();
                } else {
                    toastr.error(data.message);
                    btn.prop('disabled', false);
                }
            },
            error: function() {
                toastr.error('Could not update main image. Please try again.');
                btn.prop('disabled', false);
            }
        });
    });

    // Submit the product form over AJAX so slow connections get a real upload
    // progress bar and the Save button can be disabled to prevent double submits.
    $('form[name="form_submit_common"]').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        var fileInput = $('.image')[0];

        if (fileInput) {
            for (let i = 0; i < fileInput.files.length; i++) {
                if (fileInput.files[i].size > MAX_IMAGE_SIZE) {
                    toastr.error('"' + fileInput.files[i].name + '" is larger than 5 MB. Remove it before saving.');
                    return;
                }
            }
        }

        var formData = new FormData(form);
        var saveBtn = $('#save_common');
        var progressWrap = $('#upload-progress-wrap');
        var progressBar = $('#upload-progress-bar');

        $.ajax({
            type: 'POST',
            url: window.location.href,
            data: formData,
            dataType: 'JSON',
            cache: false,
            contentType: false,
            processData: false,
            timeout: 300000, // 5 minutes, matches server-side max_execution_time
            xhr: function() {
                var xhr = $.ajaxSettings.xhr();
                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', function(evt) {
                        if (evt.lengthComputable) {
                            var percent = Math.round((evt.loaded / evt.total) * 100);
                            progressBar.css('width', percent + '%').text(percent + '%');
                        }
                    }, false);
                }
                return xhr;
            },
            beforeSend: function() {
                saveBtn.prop('disabled', true).html('Saving... <i class="fa fa-spin fa-spinner"></i>');
                progressWrap.removeClass('d-none');
                progressBar.css('width', '0%').text('0%');
            },
            success: function(data) {
                if (data.status) {
                    if (data.skipped && data.skipped.length) {
                        toastr.warning('Skipped: ' + data.skipped.join(', '));
                    }
                    toastr.success(data.message);
                    window.location = data.redirect;
                } else {
                    toastr.error(data.message || 'Please check the form for errors.');
                    saveBtn.prop('disabled', false).html('Save');
                    progressWrap.addClass('d-none');
                }
            },
            error: function(xhr, status) {
                if (status === 'timeout') {
                    toastr.error('Upload timed out. Please check your connection and try again.');
                } else {
                    toastr.error('Something went wrong while saving. Please try again.');
                }
                saveBtn.prop('disabled', false).html('Save');
                progressWrap.addClass('d-none');
            }
        });
    });
</script>