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
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post" enctype="multipart/form-data" name="form_submit_common">
                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <div class="row">
                                            <label for="example-text-input" class="col-md-3 col-form-label">Name</label>
                                            <div class="col-md-9">
                                                <input class="form-control" type="text" name="name" required value="<?= $name ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <div class="row">
                                            <label for="example-text-input" class="col-md-3 col-form-label">Email Id</label>
                                            <div class="col-md-9">
                                                <input class="form-control" type="email" name="email_id" required value="<?= $email_id ?>">
                                                <?= form_error('email_id') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <div class="row">
                                            <label for="example-text-input" class="col-md-3 col-form-label">Contact Number</label>
                                            <div class="col-md-9">
                                                <input class="form-control input-mask" type="text" name="contact_no" id="input-repeat" data-inputmask="'mask': '9', 'repeat': 10, 'greedy' : false" required value="<?= $contact_no ?>">
                                                <?= form_error('contact_no') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <div class="row">
                                            <label for="example-text-input" class="col-md-3 col-form-label">Password</label>
                                            <div class="col-md-9">
                                                <input class="form-control" type="password" name="password" <?= isset($id) ? '' : 'required' ?>>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h5 style="text-align: center"><b>-- Privileges -- </b></h5>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Module Name</th>
                                                    <th>View</th>
                                                    <th>Add</th>
                                                    <th>Edit</th>
                                                    <th>Delete</th>
                                                    <th>Other</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Banner</td>
                                                    <td><input type="checkbox" class="form-check-input" name="banner_view" value="1" <?= isset($privileges->banner_view) ? ($privileges->banner_view == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" name="banner_add" value="1" <?= isset($privileges->banner_add) ? ($privileges->banner_add == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" name="banner_edit" value="1" <?= isset($privileges->banner_edit) ? ($privileges->banner_edit == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" name="banner_delete" value="1" <?= isset($privileges->banner_delete) ? ($privileges->banner_delete == 1 ? 'checked' : '') : '' ?>></td>
                                                </tr>
                                                <tr>
                                                    <td>Promo Code</td>
                                                    <td><input type="checkbox" class="form-check-input" name="promo_code_view" value="1" <?= isset($privileges->promo_code_view) ? ($privileges->promo_code_view == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" name="promo_code_add" value="1" <?= isset($privileges->promo_code_add) ? ($privileges->promo_code_add == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" name="promo_code_edit" value="1" <?= isset($privileges->promo_code_edit) ? ($privileges->promo_code_edit == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" name="promo_code_delete" value="1" <?= isset($privileges->promo_code_delete) ? ($privileges->promo_code_delete == 1 ? 'checked' : '') : '' ?>></td>
                                                </tr>
                                                <tr>
                                                    <td>Sub Admin</td>
                                                    <td><input type="checkbox" class="form-check-input" name="sub_admin_view" value="1" <?= isset($privileges->sub_admin_view) ? ($privileges->sub_admin_view == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" name="sub_admin_add" value="1" <?= isset($privileges->sub_admin_add) ? ($privileges->sub_admin_add == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" name="sub_admin_edit" value="1" <?= isset($privileges->sub_admin_edit) ? ($privileges->sub_admin_edit == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td></td>
                                                    <td>
                                                        <span>Enable / Disable <input type="checkbox" class="form-check-input" name="sub_admin_enable" value="1" <?= isset($privileges->sub_admin_enable) ? ($privileges->sub_admin_enable == 1 ? 'checked' : '') : '' ?>></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Product Category</td>
                                                    <td><input type="checkbox" class="form-check-input" name="product_sub_category_view" value="1" <?= isset($privileges->product_sub_category_view) ? ($privileges->product_sub_category_view == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" name="product_sub_category_add" value="1" <?= isset($privileges->product_sub_category_add) ? ($privileges->product_sub_category_add == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" name="product_sub_category_edit" value="1" <?= isset($privileges->product_sub_category_edit) ? ($privileges->product_sub_category_edit == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" name="product_sub_category_delete" value="1" <?= isset($privileges->product_sub_category_delete) ? ($privileges->product_sub_category_delete == 1 ? 'checked' : '') : '' ?>></td>
                                                </tr>
                                                <tr>
                                                    <td>Product</td>
                                                    <td><input type="checkbox" class="form-check-input" name="product_view" value="1" <?= isset($privileges->product_view) ? ($privileges->product_view == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" name="product_add" value="1" <?= isset($privileges->product_add) ? ($privileges->product_add == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" name="product_edit" value="1" <?= isset($privileges->product_edit) ? ($privileges->product_edit == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" name="product_delete" value="1" <?= isset($privileges->product_delete) ? ($privileges->product_delete == 1 ? 'checked' : '') : '' ?>></td>
                                                </tr>
                                                <tr>
                                                    <td>User</td>
                                                    <td><input type="checkbox" class="form-check-input" name="users_view" value="1" <?= isset($privileges->users_view) ? ($privileges->users_view == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" name="users_add" value="1" <?= isset($privileges->users_add) ? ($privileges->users_add == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" name="users_edit" value="1" <?= isset($privileges->users_edit) ? ($privileges->users_edit == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" name="users_delete" value="1" <?= isset($privileges->users_delete) ? ($privileges->users_delete == 1 ? 'checked' : '') : '' ?>></td>
                                                </tr>
                                                <tr>
                                                    <td>User</td>
                                                    <td><input type="checkbox" class="form-check-input" name="users_view" value="1" <?= isset($privileges->users_view) ? ($privileges->users_view == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>Enable / Disable<input type="checkbox" class="form-check-input" name="users_enable" value="1" <?= isset($privileges->users_enable) ? ($privileges->users_enable == 1 ? 'checked' : '') : '' ?>></td>
                                                </tr>
                                                <tr>
                                                    <td>Orders</td>
                                                    <td><input type="checkbox" class="form-check-input" name="orders_view" value="1" <?= isset($privileges->orders_view) ? ($privileges->orders_view == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <span>Order Process <input type="checkbox" class="form-check-input" name="orders_process" value="1" <?= isset($privileges->orders_process) ? ($privileges->orders_process == 1 ? 'checked' : '') : '' ?>></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Returns</td>
                                                    <td><input type="checkbox" class="form-check-input" name="return_view" value="1" <?= isset($privileges->return_view) ? ($privileges->return_view == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <span>Approve/Reject <input type="checkbox" class="form-check-input" name="return_process" value="1" <?= isset($privileges->return_process) ? ($privileges->return_process == 1 ? 'checked' : '') : '' ?>></span>
                                                        <span class="ms-2">Refund <input type="checkbox" class="form-check-input" name="return_refund" value="1" <?= isset($privileges->return_refund) ? ($privileges->return_refund == 1 ? 'checked' : '') : '' ?>></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Payment Request</td>
                                                    <td><input type="checkbox" class="form-check-input" name="payment_request_view" value="1" <?= isset($privileges->payment_request_view) ? ($privileges->payment_request_view == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <span>Order Process <input type="checkbox" class="form-check-input" name="payment_request_process" value="1" <?= isset($privileges->payment_request_process) ? ($privileges->payment_request_process == 1 ? 'checked' : '') : '' ?>></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Inventory</td>
                                                    <td><input type="checkbox" class="form-check-input" name="inventory_view" value="1" <?= isset($privileges->inventory_view) ? ($privileges->inventory_view == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <span>Adjust Stock <input type="checkbox" class="form-check-input" name="inventory_adjust" value="1" <?= isset($privileges->inventory_adjust) ? ($privileges->inventory_adjust == 1 ? 'checked' : '') : '' ?>></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Vendors</td>
                                                    <td><input type="checkbox" class="form-check-input" name="vendor_view" value="1" <?= isset($privileges->vendor_view) ? ($privileges->vendor_view == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <span>Approve Vendor <input type="checkbox" class="form-check-input" name="vendor_approve" value="1" <?= isset($privileges->vendor_approve) ? ($privileges->vendor_approve == 1 ? 'checked' : '') : '' ?>></span>
                                                        <span class="ms-2">Approve Products <input type="checkbox" class="form-check-input" name="vendor_product_approve" value="1" <?= isset($privileges->vendor_product_approve) ? ($privileges->vendor_product_approve == 1 ? 'checked' : '') : '' ?>></span>
                                                        <span class="ms-2">Process Payouts <input type="checkbox" class="form-check-input" name="vendor_payout_process" value="1" <?= isset($privileges->vendor_payout_process) ? ($privileges->vendor_payout_process == 1 ? 'checked' : '') : '' ?>></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Manual Orders</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <span>Create <input type="checkbox" class="form-check-input" name="orders_manual_create" value="1" <?= isset($privileges->orders_manual_create) ? ($privileges->orders_manual_create == 1 ? 'checked' : '') : '' ?>></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Reports</td>
                                                    <td><input type="checkbox" class="form-check-input" name="reports_view" value="1" <?= isset($privileges->reports_view) ? ($privileges->reports_view == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>Admin Activity Log</td>
                                                    <td><input type="checkbox" class="form-check-input" name="audit_log_view" value="1" <?= isset($privileges->audit_log_view) ? ($privileges->audit_log_view == 1 ? 'checked' : '') : '' ?>></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
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