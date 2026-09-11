<?php $this->load->view('admin/template/header', $title);
$p = json_decode(sessionId('privileges')); ?>
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0 "><?= $title ?></h2>
                        <?php if (@PREV['sub_admin_add'] == 1 || USER_TYPE == '1') { ?>
                            <a href="<?= base_url("addSubAdmin"); ?>" class="btn btn-success"><i class="fa fa-plus"></i> Add</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Sr no.</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Contact Number</th>
                                        <th>Action</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($all_data) {
                                        $i = 0;
                                        foreach ($all_data as $all) {
                                            $id = encryptId($all['admin_id']);
                                    ?>
                                            <tr>
                                                <td><?= ++$i; ?></td>
                                                <td><?= ucwords($all['name']) ?></td>
                                                <td><?= $all['email_id'] ?></td>
                                                <td><?= $all['contact_no'] ?></td>
                                                <td>
                                                    <?php if (@PREV['sub_admin_edit'] == 1 || USER_TYPE == '1') { ?>
                                                        <a href="<?= base_url("addSubAdmin?id=$id") ?>" class="btn btn-primary"><i class="fa fa-edit"></i> Edit</a>
                                                    <?php } ?>

                                                    <?php if (@PREV['sub_admin_enable'] == 1 || USER_TYPE == '1') { ?>
                                                        <?php if ($all['status'] == '1') { ?>
                                                            <a class="btn btn-success" href="<?= base_url("subAdminStatus/$id/1") ?>">Inactive</a>
                                                        <?php } else { ?>
                                                            <a class="btn btn-danger" href="<?= base_url("subAdminStatus/$id/2") ?>">Active</a>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($all['status'] == '1') {  ?>
                                                        <span class="badge badge-pill badge-soft-success font-size-14">Active</span>
                                                    <?php } else { ?>
                                                        <span class="badge badge-pill badge-soft-danger font-size-14">Inactive</span>
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