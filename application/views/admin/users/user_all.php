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
                                        <!--<th>Wallet Amount</th>-->
                                        <th>Action</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($all_data) {
                                        $i = 0;
                                        foreach ($all_data as $all) {
                                            $id = encryptId($all['user_id']);
                                    ?>
                                            <tr>
                                                <td><?= ++$i; ?></td>
                                                <td><?= ucwords($all['name']) ?></td>
                                                <td><?= $all['email_id'] ?></td>
                                                <td><?= $all['contact_no'] ?></td>
                                                <!--<td><?= $all['wallet_amount'] ?></td>-->
                                                <td>
                                                    <a href="<?= base_url("userDetails/$id") ?>" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> View</a>
                                                    <?php if (@PREV['users_enable'] == 1 || USER_TYPE == '1') { ?>
                                                        <?php if ($is_register == 1) {
                                                            if ($all['verify_status'] == '0') { ?>
                                                                <a class="btn btn-success btn-sm" onclick="return confirm('Are You sure ?')" href="<?= base_url("userVerify/$id/1") ?>">
                                                                    Verify
                                                                </a>
                                                                <a class="btn btn-danger btn-sm" onclick="return confirm('Are You sure ?')" href="<?= base_url("userVerify/$id/2") ?>">
                                                                    Cancel
                                                                </a>
                                                            <?php } else { ?>
                                                                <a class="btn btn-success btn-sm" onclick="return confirm('Are You sure ?')" href="<?= base_url("userVerify/$id/1") ?>">
                                                                    Verify
                                                                </a>
                                                            <?php }
                                                        } else {
                                                            if ($all['user_status'] == '1') { ?>
                                                                <a class="btn btn-success btn-sm" onclick="return confirm('Are You sure ?')" href="<?= base_url("userStatus/$id/1") ?>">
                                                                    Inactive
                                                                </a>
                                                            <?php } else { ?>
                                                                <a class="btn btn-danger btn-sm" onclick="return confirm('Are You sure ?')" href="<?= base_url("userStatus/$id/2") ?>">
                                                                    Active
                                                                </a>
                                                        <?php }
                                                        } ?>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($all['user_status'] == '1') {
                                                    ?>
                                                        <span class="badge badge-pill badge-soft-success font-size-14">Active</span>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <span class="badge badge-pill badge-soft-danger font-size-14">Inactive</span>
                                                    <?php
                                                    } ?>
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