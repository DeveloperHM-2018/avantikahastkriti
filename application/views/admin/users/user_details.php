<?php $this->load->view('admin/template/header', $title); ?>
<style>
    .details label {
        font-size: 16px;
    }

    .details span {
        font-size: 16px;
    }
</style>
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
                            <div class="row">
                                <div class="col-lg-12 details">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <label>Name : </label>
                                            <span><?= ucwords($all_data['name']) ?></span>
                                        </div>
                                        <div class="col-lg-6">
                                            <label>Email Id : </label>
                                            <span><?= htmlspecialchars((string) $all_data['email_id']) ?: '----' ?></span>
                                        </div>
                                        <div class="col-lg-6">
                                            <label>Contact Number : </label>
                                            <span><?= htmlspecialchars((string) $all_data['contact_no']) ?: '----' ?></span>
                                        </div>
                                        <div class="col-lg-6">
                                            <label>Registered On : </label>
                                            <span><?= dateConvertToView($all_data['create_date'], 3) ?></span>
                                        </div>
                                        <div class="col-lg-6">
                                            <label>Password Login : </label>
                                            <span><?= userHasPassword($all_data) ? 'Set' : 'Not set (OTP only)' ?></span>
                                        </div>
                                        <div class="col-lg-6">
                                            <label>Orders : </label>
                                            <span><?= (int) $order_stats['order_count'] ?> (paid total &#8377;<?= number_format((float) $order_stats['total_spent'], 2) ?>)</span>
                                        </div>
                                        <div class="col-lg-6">
                                            <label>Last Order : </label>
                                            <span><?= $order_stats['last_order_date'] ? dateConvertToView($order_stats['last_order_date'], 3) : '----' ?></span>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-lg-12">
                                            <label>Address : </label>
                                            <span><?= $all_data['address'] ?></span>
                                        </div>
                                        <div class="col-lg-6">
                                            <label>Area : </label>
                                            <span><?= $all_data['area'] ?></span>
                                        </div>
                                        <div class="col-lg-6">
                                            <label>Postal Code : </label>
                                            <span><?= $all_data['postal_code'] ?></span>
                                        </div>
                                        <div class="col-lg-6">
                                            <label>State : </label>
                                            <span><?= $all_data['state'] ?></span>
                                        </div>
                                        <div class="col-lg-6">
                                            <label>City : </label>
                                            <span><?= $all_data['city'] ?></span>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-lg-6">
                                            <label>Image : </label>
                                            <?php
                                            if ($all_data['profile_image'] != "") {
                                            ?>
                                                <img src="<?= base_url('upload/profile_image/') . $all_data['profile_image'] ?>" style="width: 200px; height: 200px;">
                                            <?php
                                            } else {
                                                echo "----";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/template/footer'); ?>