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
            <?php if (@PREV['banner_add'] == 1 || USER_TYPE == '1') { ?>
                <div class="row">
                    <div class="col-6 offset-3">
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="post" enctype="multipart/form-data">
                                    <div class="mb-3 row">
                                        <label for="example-text-input" class="col-md-3 col-form-label">Banner Image</label>
                                        <div class="col-md-9">
                                            <input class="form-control category_image" type="file" name="image_path" id="example-text-input">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <img class="temp_image" src="<?= base_url('upload/banner') . '/' . $image_path ?>" style=" height: 300px;">
                                        <input type="hidden" value="<?= $image_path ?>" name="temp_image">
                                    </div>
                                    <div class="col-lg-12 mt-2">
                                        <span id="uploadImageError"></span>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" id="save" class="btn btn-primary w-md">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">All Banners</h4>
                            <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th style="width: 20%">Sr no.</th>
                                        <th style="width: 30%">Banner Image</th>
                                        <th style="width: 30%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    if ($all_banner) {
                                        $i = 0;
                                        foreach ($all_banner as $all) {
                                            $id = encryptId($all['banner_id']);
                                    ?>
                                            <tr>
                                                <td><?= ++$i; ?></td>
                                                <td>
                                                    <a href="<?= base_url("upload/banner/") . $all['image_path'] ?>">
                                                        <img src="<?= base_url("upload/banner/") . $all['image_path'] ?>" style="width: 50px; height: 50px">
                                                    </a>
                                                </td>

                                                <td>
                                                    <?php if (@PREV['banner_edit'] == 1 || USER_TYPE == '1') { ?>
                                                        <a href="<?= base_url("banner?bID=$id"); ?>" class="btn btn-success"><i class="fa fa-edit"></i> Edit</a>
                                                    <?php } ?>
                                                    
                                                    <?php if (@PREV['banner_delete'] == 1 || USER_TYPE == '1') { ?>
                                                        <a onclick="return confirm('Are you want to sure ?')" href="<?= base_url("banner?BdID=$id&img=" . $all['image_path'] . ""); ?>" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    }
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
<script>
    $(document).ready(function() {
        <?php
        if (isset($id)) {
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
        if ($.inArray(ext, ['jpg', 'jpeg', 'png', 'webp']) == -1) {
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