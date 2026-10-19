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
            <?php
            // Which of the two login contacts an account has. The cards are
            // shortcuts into the "Has" filter below.
            $statCards = [
                '' => ['label' => 'All Users', 'count' => $stats['total']],
                'mobile_only' => ['label' => 'Mobile Only', 'count' => $stats['mobile_only']],
                'email_only' => ['label' => 'Email Only', 'count' => $stats['email_only']],
                'both' => ['label' => 'Mobile + Email', 'count' => $stats['both_contacts']],
            ];
            ?>
            <div class="row">
                <?php foreach ($statCards as $type => $card): ?>
                    <div class="col-sm-6 col-lg-3 mb-3">
                        <a href="?<?= http_build_query(['contact' => $type]) ?>" class="card <?= $filters['contact'] === (string) $type ? 'border border-primary' : '' ?>">
                            <div class="card-body py-3">
                                <div class="text-muted"><?= $card['label'] ?></div>
                                <h4 class="mb-0"><?= (int) $card['count'] ?></h4>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <form method="get" class="row align-items-end">
                <div class="col-lg-2 mb-3">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($filters['name']) ?>" placeholder="Customer name">
                </div>
                <div class="col-lg-2 mb-3">
                    <label>Mobile</label>
                    <input type="text" class="form-control" name="mobile" value="<?= htmlspecialchars($filters['mobile']) ?>" placeholder="Mobile number">
                </div>
                <div class="col-lg-3 mb-3">
                    <label>Email</label>
                    <input type="text" class="form-control" name="email" value="<?= htmlspecialchars($filters['email']) ?>" placeholder="Email address">
                </div>
                <div class="col-lg-2 mb-3">
                    <label>Has</label>
                    <select name="contact" class="form-select">
                        <option value="">-----All-----</option>
                        <option value="mobile_only" <?= $filters['contact'] == 'mobile_only' ? 'selected' : '' ?>>Mobile only</option>
                        <option value="email_only" <?= $filters['contact'] == 'email_only' ? 'selected' : '' ?>>Email only</option>
                        <option value="both" <?= $filters['contact'] == 'both' ? 'selected' : '' ?>>Mobile + Email</option>
                    </select>
                </div>
                <div class="col-lg-3 mb-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="<?= current_url() ?>" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
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
                                        <th>Registered</th>
                                        <th>Orders</th>
                                        <th>Paid Total</th>
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
                                                <td><?= htmlspecialchars(ucwords((string) $all['name'])) ?></td>
                                                <td><?= htmlspecialchars((string) $all['email_id']) ?: '-' ?></td>
                                                <td><?= htmlspecialchars((string) $all['contact_no']) ?: '-' ?></td>
                                                <td data-order="<?= $all['create_date'] ?>"><?= dateConvertToView($all['create_date'], 3) ?></td>
                                                <td><?= (int) $all['order_count'] ?></td>
                                                <td data-order="<?= (float) $all['total_spent'] ?>">&#8377;<?= number_format((float) $all['total_spent'], 2) ?></td>
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