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
                <div class="col-8 offset-2">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post">
                                <h5>Return Window</h5>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label class="col-form-label">Return Window (Days after delivery) *</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="number" min="1" name="return_window_days" value="<?= $return_window_days ?>">
                                            <?= form_error('return_window_days', '<span class="text-danger">', '</span>') ?>
                                        </div>
                                    </div>
                                </div>
                                <hr class="mt-4">
                                <h5>Warehouse / RTO Address</h5>
                                <p class="text-muted">Used as the delivery destination when creating a Shiprocket reverse-pickup order for approved returns.</p>
                                <div class="row">
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">Address</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="warehouse_address" value="<?= $warehouse_address ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">City</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="warehouse_city" value="<?= $warehouse_city ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">State</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="warehouse_state" value="<?= $warehouse_state ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">Pincode</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="warehouse_pincode" value="<?= $warehouse_pincode ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">Phone</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="warehouse_phone" value="<?= $warehouse_phone ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">Email</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="warehouse_email" value="<?= $warehouse_email ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">Shiprocket Pickup Location <span class="text-danger">*</span></label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="shiprocket_pickup_nickname" value="<?= $shiprocket_pickup_nickname ?>" required>
                                            <small class="text-muted">Must exactly match a pickup location nickname already registered for this warehouse address in your Shiprocket account. Used as the default when syncing house-stocked orders.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary w-md">Save</button>
                                </div>
                            </form>

                            <hr class="mt-4">
                            <h6><?= empty($shiprocket_pickup_nickname) ? 'Register This Address in Shiprocket' : 'Register a New Pickup Location' ?></h6>
                            <p class="text-muted">
                                Pushes the warehouse address above straight into Shiprocket instead of adding it by
                                hand in their dashboard. Save the address fields first if you haven't yet.
                                <?php if (!empty($shiprocket_pickup_nickname)) : ?>
                                    Shiprocket doesn't support editing an existing pickup location via this - registering
                                    again under a new nickname adds a separate one rather than updating <code><?= $shiprocket_pickup_nickname ?></code>.
                                <?php endif; ?>
                            </p>
                            <form id="registerHousePickupForm" class="row g-2">
                                <div class="col-12">
                                    <input type="text" class="form-control form-control-sm" name="nickname" id="house_pickup_nickname"
                                        value="<?= empty($shiprocket_pickup_nickname) ? 'work' : '' ?>"
                                        placeholder="Nickname for this pickup location" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-truck"></i> Register in Shiprocket
                                    </button>
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
    $('#registerHousePickupForm').on('submit', function(e) {
        e.preventDefault();
        var btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> Registering...');
        $.post('<?= base_url('registerHouseShiprocketPickup') ?>', $(this).serialize(), function(res) {
            alert(res.message);
            if (res.status) {
                location.reload();
            } else {
                btn.prop('disabled', false).html('<i class="fa fa-truck"></i> Register in Shiprocket');
            }
        }, 'json').fail(function() {
            alert('Could not reach Shiprocket. Please try again.');
            btn.prop('disabled', false).html('<i class="fa fa-truck"></i> Register in Shiprocket');
        });
    });
</script>
