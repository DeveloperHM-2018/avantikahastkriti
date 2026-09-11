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
                <div class="col-8 offset-2">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post" enctype="multipart/form-data" name="form_submit_common">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                        <div class="row">
                                            <label class="col-sm-3 control-label">Category <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" name="category_id" onchange="getCategory(this.value)">
                                                    <option>Select Category</option>
                                                    <?php
                                                    $c = getRowsByMoreIdWithOrder('category', "is_delete = '1'", "category_name", 'ASC');
                                                    foreach ($c as $cate) {
                                                    ?>
                                                        <option value="<?= $cate['category_id'] ?>" <?php if ($category_id == $cate['category_id']) {
                                                                                                        echo 'selected';
                                                                                                    } ?>><?= ucwords($cate['category_name']) ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-3">
                                        <div class="row">
                                            <label class="col-md-3 control-label">Sub Category <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" name="sub_category_id"
                                                    data-placeholder="Select sub category" id="sub_category" required>
                                                    <?php
                                                    $subCate = getRowsByMoreIdWithOrder('sub_category', "category_id = '$category_id' AND is_delete = '1'", 'sub_category_name', 'ASC');
                                                    foreach ($subCate as $c) {
                                                    ?>
                                                        <option value="<?= $c['sub_category_id'] ?>"
                                                            <?= $c['sub_category_id'] == $sub_category_id ? 'selected' : '' ?>>
                                                            <?= $c['sub_category_name'] ?>
                                                        </option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 mb-3">
                                        <div class="row">
                                            <label for="example-text-input" class="col-md-3 col-form-label">Sub Category Type Name</label>
                                            <div class="col-md-9">
                                                <input class="form-control" type="text" name="sub_category_type_name" required value="<?= $sub_category_type_name ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-3">
                                        <div class="row">
                                            <label for="example-text-input" class="col-md-3 col-form-label">Sub Category Type Image</label>
                                            <div class="col-md-9">
                                                <input class="form-control category_image" type="file" name="sub_category_type_image" <?= $sub_category_type_image == "" ? 'required' : '' ?>>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <img class="temp_image" src="<?= base_url('upload/category') . '/' . $sub_category_type_image ?>" style=" height: 300px;">
                                        <input type="hidden" value="<?= $sub_category_type_image ?>" name="temp_image">
                                    </div>
                                    <div class="col-lg-12 mt-2">
                                        <span id="uploadImageError"></span>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" id="save_common" class="btn btn-primary w-md">Save</button>
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
<script>
    $(document).ready(function() {
        <?php
        if ($id == '') {
        ?>
            $('.temp_image').hide();
            $('#save').attr('disabled', true);
        <?php
        } else {
        ?>
            $('.temp_image').show();
            $('#save').attr('disabled', false);
        <?php
        }

        ?>
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

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('.temp_image').attr('src', e.target.result);
                // $('.user_image').show();
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function sendFile(file) {
        var ext = file.name.split('.').pop().toLowerCase();
        if ($.inArray(ext, ['jpg', 'jpeg', 'png']) == -1) {
            $('#uploadImageError').show().html('<div class="alert alert-danger" role="alert"> <strong>Error!</strong> Only JPG, JPEG and PNG extension allowed.</div>');
            $('.temp_image').hide();
            $('#save').attr('disabled', true);
        } else {
            $('.temp_image').show();
            $('#save').removeAttr('disabled');
        }
    }

    $(".category_image").change(function() {
        $('#uploadImageError').hide();
        readURL(this);
        sendFile(this.files[0]);
    });
</script>