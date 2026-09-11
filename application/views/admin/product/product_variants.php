<?php $this->load->view('admin/template/header', $title); ?>

<style>
    .color-preview {
        display: inline-block;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 2px solid #ddd;
        vertical-align: middle;
        margin-right: 5px;
    }

    .variant-badge {
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 12px;
        font-weight: bold;
        display: inline-block;
        margin: 2px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.3;
    }
</style>


<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0"><a href="<?= base_url("productAll"); ?>" class="btn btn-secondary"><i class="fa fa-arrow-left"></i></a> <?= $title ?></h2>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVariantModal">
                            <i class="fa fa-plus"></i> Add New Variant
                        </button>
                    </div>
                </div>
            </div>

            <!-- Variants Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-2"><?= $product['product_name'] ?></h4>
                            <?php if ($variants && count($variants) > 0) : ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">Sr No.</th>
                                                <th width="15%">Size</th>
                                                <th width="15%">Color</th>
                                                <th width="15%">Price</th>
                                                <th width="12%">Stock</th>
                                                <th width="12%">SKU</th>
                                                <th width="10%">Status</th>
                                                <th width="16%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 1;
                                            $uniqueColors = [];
                                            foreach ($variants as $variant) :
                                                $colorVal = trim($variant['color']);
                                                if (!in_array($colorVal, $uniqueColors) && !empty($colorVal)) {
                                                    $uniqueColors[] = $colorVal;
                                                }
                                                $colorName = isset($variant['color_name']) ? $variant['color_name'] : '';
                                            ?>
                                                <tr>
                                                    <td><?= $i++ ?></td>
                                                    <td>
                                                        <span class="variant-badge" style="background: #e3f2fd; color: #1976d2;">
                                                            <?= htmlspecialchars($variant['size']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="color-preview" style="background-color: <?= htmlspecialchars($variant['color']) ?>;"></span>
                                                        <?= htmlspecialchars($colorName) ?>
                                                    </td>
                                                    <td>
                                                        <strong style="color: #28a745; font-size: 16px;">₹<?= number_format($variant['price'], 2) ?></strong>
                                                    </td>
                                                    <td><?= $variant['stock_quantity'] ? $variant['stock_quantity'] . ' units' : 'N/A' ?></td>
                                                    <td><?= $variant['sku'] ?: 'N/A' ?></td>
                                                    <td>
                                                        <?php if ($variant['is_active'] == 1) : ?>
                                                            <a href="<?= base_url('variantToggleStatus/' . encryptId($variant['variant_id']) . '/0') ?>" class="btn btn-success text-white">
                                                                <i class="fa fa-check-circle"></i> Active
                                                            </a>
                                                        <?php else : ?>
                                                            <a href="<?= base_url('variantToggleStatus/' . encryptId($variant['variant_id']) . '/1') ?>" class="btn btn-danger text-white"> Deactive
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary edit-variant"
                                                            data-id="<?= encryptId($variant['variant_id']) ?>"
                                                            data-size="<?= htmlspecialchars($variant['size']) ?>"
                                                            data-color="<?= htmlspecialchars($variant['color']) ?>"
                                                            data-color-name="<?= htmlspecialchars($colorName) ?>"
                                                            data-price="<?= $variant['price'] ?>"
                                                            data-stock="<?= $variant['stock_quantity'] ?>"
                                                            data-sku="<?= htmlspecialchars($variant['sku']) ?>">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <a href="<?= base_url('variantDelete?id=' . encryptId($variant['variant_id']) . '&pid=' . $product_id) ?>"
                                                            class="btn btn-sm btn-danger confirm_data"
                                                            title="Delete Variant"
                                                            title-text="Are you sure you want to delete this variant?"
                                                            icon="warning">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else : ?>
                                <div class="empty-state">
                                    <i class="fa fa-list"></i>
                                    <h4>No Variants Added Yet</h4>
                                    <p>Click "Add New Variant" to create your first product variant.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Color Image Management -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Color Image Management</h4>
                            <p class="card-title-desc">Upload multiple images for each product color. These will be displayed when the customer selects the color.</p>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($uniqueColors)) : ?>
                                <?php foreach ($uniqueColors as $color) : ?>
                                    <div class="border p-3 mb-3 rounded">
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="color-preview" style="background-color: <?= htmlspecialchars($color) ?>;"></span>
                                            <h5 class="m-0"><?= htmlspecialchars($colorName) ?></h5>
                                        </div>

                                        <!-- Previous Images -->
                                        <div class="row mb-3">
                                            <?php
                                            if (isset($color_images) && !empty($color_images)) :
                                                foreach ($color_images as $img) :
                                                    if ($img['color'] == $color) :
                                            ?>
                                                        <div class="col-md-2 col-sm-4 col-6 mb-2">
                                                            <div class="position-relative">
                                                                <img src="<?= base_url('upload/product_color_images/' . $img['image']) ?>" class="img-fluid rounded border" alt="<?= $color ?>">
                                                                <button class="btn btn-sm btn-danger position-absolute top-0 end-0 delete-color-image" data-id="<?= $img['id'] ?>" style="padding: 0px 5px;">&times;</button>
                                                            </div>
                                                        </div>
                                            <?php
                                                    endif;
                                                endforeach;
                                            endif;
                                            ?>
                                        </div>

                                        <!-- Upload Form -->
                                        <form class="color-image-form" enctype="multipart/form-data">
                                            <input type="hidden" name="product_id" value="<?= decryptId($product_id) ?>">
                                            <input type="hidden" name="color" value="<?= htmlspecialchars($color) ?>">
                                            <div class="input-group">
                                                <input type="file" class="form-control" name="images[]" multiple accept="image/*" required>
                                                <button class="btn btn-primary" type="submit">Upload Images</button>
                                            </div>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No colors found. Please add variants with colors first.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Variant Modal -->
<div class="modal fade" id="addVariantModal" tabindex="-1" aria-labelledby="addVariantModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVariantModalLabel">Add New Variant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addVariantForm">
                <div class="modal-body">
                    <input type="hidden" name="product_id" value="<?= $product_id ?>">

                    <div class="mb-3">
                        <label class="form-label">Size <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="size" required placeholder="e.g., S, M, L, XL, Free Size">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Color Name</label>
                        <input type="text" class="form-control" name="color_name" placeholder="e.g., Navy Blue, Cherry Red">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Color Code (Hex) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="colorPicker" style="max-width: 60px;">
                            <input type="text" class="form-control" name="color" id="colorInput" required placeholder="#000000">
                        </div>
                        <small class="text-muted">You can use hex code (#FF5733)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Price (₹) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="price" required step="0.01" min="0" placeholder="0.00">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stock Quantity</label>
                        <input type="number" class="form-control" name="stock_quantity" step="0.01" min="0" placeholder="Optional">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">SKU</label>
                        <input type="text" class="form-control" name="sku" placeholder="Optional - Stock Keeping Unit">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Variant</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Variant Modal -->
<div class="modal fade" id="editVariantModal" tabindex="-1" aria-labelledby="editVariantModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editVariantModalLabel">Edit Variant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editVariantForm">
                <div class="modal-body">
                    <input type="hidden" name="variant_id" id="edit_variant_id">

                    <div class="mb-3">
                        <label class="form-label">Size <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="size" id="edit_size" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Color Name</label>
                        <input type="text" class="form-control" name="color_name" id="edit_color_name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Color Code (Hex) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="editColorPicker" style="max-width: 60px;">
                            <input type="text" class="form-control" name="color" id="edit_color" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Price (₹) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="price" id="edit_price" required step="0.01" min="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stock Quantity</label>
                        <input type="number" class="form-control" name="stock_quantity" id="edit_stock" step="0.01" min="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">SKU</label>
                        <input type="text" class="form-control" name="sku" id="edit_sku">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Variant</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('admin/template/footer'); ?>

<script>
    $(document).ready(function() {
        // Color picker sync for add modal
        $('#colorPicker').on('input', function() {
            $('#colorInput').val($(this).val());
        });

        $('#colorInput').on('input', function() {
            if ($(this).val().startsWith('#')) {
                $('#colorPicker').val($(this).val());
            }
        });

        // Color picker sync for edit modal
        $('#editColorPicker').on('input', function() {
            $('#edit_color').val($(this).val());
        });

        $('#edit_color').on('input', function() {
            if ($(this).val().startsWith('#')) {
                $('#editColorPicker').val($(this).val());
            }
        });

        // Add Variant Form Submit
        $('#addVariantForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: '<?= base_url("variantAdd") ?>',
                data: $(this).serialize(),
                dataType: 'json',
                beforeSend: function() {
                    $('.btn-primary').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                        $('.btn-primary').prop('disabled', false).html('Save Variant');
                    }
                },
                error: function() {
                    alert('Something went wrong. Please try again.');
                    $('.btn-primary').prop('disabled', false).html('Save Variant');
                }
            });
        });

        // Edit Variant Button Click
        $('.edit-variant').on('click', function() {
            $('#edit_variant_id').val($(this).data('id'));
            $('#edit_size').val($(this).data('size'));
            $('#edit_color').val($(this).data('color'));
            $('#edit_color_name').val($(this).data('color-name'));
            $('#edit_price').val($(this).data('price'));
            $('#edit_stock').val($(this).data('stock'));
            $('#edit_sku').val($(this).data('sku'));

            // Set color picker if hex value
            if ($(this).data('color').startsWith('#')) {
                $('#editColorPicker').val($(this).data('color'));
            }

            $('#editVariantModal').modal('show');
        });

        // Edit Variant Form Submit
        $('#editVariantForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: '<?= base_url("variantEdit") ?>',
                data: $(this).serialize(),
                dataType: 'json',
                beforeSend: function() {
                    $('.btn-primary').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                        $('.btn-primary').prop('disabled', false).html('Update Variant');
                    }
                },
                error: function() {
                    alert('Something went wrong. Please try again.');
                    $('.btn-primary').prop('disabled', false).html('Update Variant');
                }
            });
        });

        // Color Image Upload
        $('.color-image-form').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var btn = $(this).find('button[type="submit"]');

            $.ajax({
                type: 'POST',
                url: '<?= base_url("variantColorImageAdd") ?>',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                beforeSend: function() {
                    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        location.reload();
                    } else {
                        alert(response.message);
                        btn.prop('disabled', false).html('Upload Images');
                    }
                },
                error: function() {
                    alert('Something went wrong. Please try again.');
                    btn.prop('disabled', false).html('Upload Images');
                }
            });
        });

        // Delete Color Image
        $('.delete-color-image').on('click', function() {
            if (!confirm('Are you sure?')) return;
            var id = $(this).data('id');
            var btn = $(this);

            $.ajax({
                type: 'POST',
                url: '<?= base_url("variantColorImageDelete") ?>',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        btn.closest('.col-md-2').remove();
                    } else {
                        alert(response.message);
                    }
                }
            });
        });
    });
</script>