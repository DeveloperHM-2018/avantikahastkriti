<?php
$this->load->view('admin/template/header', $title);
$status = $this->input->get('status');
?>

<style>
    @media print {
        .row {
            display: flex !important;
            /* Force flex on print */
            flex-direction: row !important;
        }

        .box {
            width: 50% !important;
            box-shadow: none;
            border: 1px solid black;
        }

        #getData h5 {
            font-size: 20px !important;
        }
    }

    /* ===== Snapshot cards =====
       Deliberately not using the .mini-stats-wid/.mb-0 combo the rest of the
       admin views use for this kind of widget - this project's global
       header_link.php redefines the generic Bootstrap .mb-0 utility class
       into a fixed 60x60 floated circle, which only works by luck for a
       single short number. Any second line of text (e.g. this page's
       "N orders today" under the revenue figure) reuses that same class and
       gets forced into its own competing circle, wrecking the layout. This
       card style is self-contained so it can't collide with that override. */
    .snapshot-card {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fff;
        border: 1px solid #eef0f4;
        border-radius: 10px;
        padding: 14px 16px;
        height: 100%;
        cursor: pointer;
        transition: box-shadow .15s ease, border-color .15s ease;
    }

    .snapshot-card:hover {
        box-shadow: 0 4px 14px rgba(0, 0, 0, .07);
        border-color: #dfe3ea;
    }

    .snapshot-card.is-active {
        border-color: #556ee6;
        box-shadow: 0 0 0 1px #556ee6 inset;
    }

    .snapshot-icon {
        width: 38px;
        height: 38px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        flex-shrink: 0;
    }

    /* Soft icon backgrounds - this admin theme only ships .badge-soft-*,
       not a .bg-soft-* utility, so these are defined locally. */
    .snapshot-icon.bg-soft-dark { background-color: rgba(52, 58, 64, .1); }
    .snapshot-icon.bg-soft-warning { background-color: rgba(241, 180, 76, .15); }
    .snapshot-icon.bg-soft-primary { background-color: rgba(85, 110, 230, .12); }
    .snapshot-icon.bg-soft-info { background-color: rgba(80, 165, 241, .12); }
    .snapshot-icon.bg-soft-success { background-color: rgba(52, 195, 143, .12); }
    .snapshot-icon.bg-soft-danger { background-color: rgba(244, 106, 106, .12); }

    .snapshot-text {
        display: flex;
        flex-direction: column;
        min-width: 0;
        line-height: 1.25;
    }

    .snapshot-label {
        font-size: 11.5px;
        color: #878a99;
        text-transform: uppercase;
        letter-spacing: .4px;
        white-space: nowrap;
    }

    .snapshot-value {
        font-size: 19px;
        font-weight: 700;
        color: #343a40;
    }

    .snapshot-sub {
        font-size: 11px;
        color: #878a99;
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

            <?php if (isset($snapshot)): ?>
                <!-- ===== Snapshot cards =====
                     Click a card to jump the status filter straight to that
                     bucket - so the queue's shape is visible before staff
                     even touch the filters below. -->
                <?php
                // Matches the dropdown's own default-selection logic just
                // below (unset status = "New" is selected), so the card row
                // and the dropdown agree on load instead of the dropdown
                // silently disagreeing with an unhighlighted card row.
                $currentStatusFilter = isset($status) ? $status : '0';
                $isActiveStatus = function ($value) use ($currentStatusFilter) {
                    return $currentStatusFilter === $value ? ' is-active' : '';
                };
                ?>
                <div class="row g-2 mb-3" id="snapshotRow">
                    <div class="col-6 col-md-4 col-xl-3 col-xxl">
                        <div class="snapshot-card<?= $isActiveStatus('') ?>" data-status="">
                            <div class="snapshot-icon bg-soft-dark text-dark"><i class="bx bx-list-ul"></i></div>
                            <div class="snapshot-text">
                                <span class="snapshot-label">Total Orders</span>
                                <span class="snapshot-value counter" data-counter="<?= $snapshot['total'] ?>">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-3 col-xxl">
                        <div class="snapshot-card<?= $isActiveStatus('0') ?>" data-status="0">
                            <div class="snapshot-icon bg-soft-warning text-warning"><i class="bx bx-receipt"></i></div>
                            <div class="snapshot-text">
                                <span class="snapshot-label">New</span>
                                <span class="snapshot-value counter" data-counter="<?= $snapshot['new'] ?>">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-3 col-xxl">
                        <div class="snapshot-card<?= $isActiveStatus('1') ?>" data-status="1">
                            <div class="snapshot-icon bg-soft-primary text-primary"><i class="bx bx-check"></i></div>
                            <div class="snapshot-text">
                                <span class="snapshot-label">Accepted</span>
                                <span class="snapshot-value counter" data-counter="<?= $snapshot['accepted'] ?>">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-3 col-xxl">
                        <div class="snapshot-card<?= $isActiveStatus('3') ?>" data-status="3">
                            <div class="snapshot-icon bg-soft-info text-info"><i class="bx bx-package"></i></div>
                            <div class="snapshot-text">
                                <span class="snapshot-label">Dispatched</span>
                                <span class="snapshot-value counter" data-counter="<?= $snapshot['dispatched'] ?>">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-3 col-xxl">
                        <div class="snapshot-card<?= $isActiveStatus('4') ?>" data-status="4">
                            <div class="snapshot-icon bg-soft-success text-success"><i class="bx bx-badge-check"></i></div>
                            <div class="snapshot-text">
                                <span class="snapshot-label">Completed</span>
                                <span class="snapshot-value counter" data-counter="<?= $snapshot['completed'] ?>">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-3 col-xxl">
                        <div class="snapshot-card<?= $isActiveStatus('2') ?>" data-status="2">
                            <div class="snapshot-icon bg-soft-danger text-danger"><i class="bx bx-x-circle"></i></div>
                            <div class="snapshot-text">
                                <span class="snapshot-label">Cancelled</span>
                                <span class="snapshot-value counter" data-counter="<?= $snapshot['cancelled'] ?>">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-3 col-xxl">
                        <a href="<?= base_url('allOrders') ?>" class="text-decoration-none">
                            <div class="snapshot-card">
                                <div class="snapshot-icon bg-soft-danger text-danger"><i class="bx bx-error"></i></div>
                                <div class="snapshot-text">
                                    <span class="snapshot-label">Unpaid</span>
                                    <span class="snapshot-value counter" data-counter="<?= $snapshot['unpaid'] ?>">0</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-xl-3 col-xxl">
                        <div class="snapshot-card" id="snapshotTodayCard">
                            <div class="snapshot-icon bg-soft-success text-success"><i class="bx bx-rupee"></i></div>
                            <div class="snapshot-text">
                                <span class="snapshot-label">Today's Revenue</span>
                                <span class="snapshot-value">₹<span class="counter" data-counter="<?= $snapshot['today_revenue'] ?>" data-decimals="2">0</span></span>
                                <span class="snapshot-sub"><?= $snapshot['today_orders'] ?> order<?= $snapshot['today_orders'] == 1 ? '' : 's' ?> today</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row align-items-end">
                <div class="col-lg-2 mb-3">
                    <label>Order Status</label>
                    <select name="searchByStatus" class="form-select" id="searchByStatus">
                        <option value="">-----All-----</option>
                        <option value="0" <?= isset($status) ? ($status == '0' ? 'selected' : '') : 'selected' ?>>New Orders</option>
                        <option value="1" <?= $status == '1' ? 'selected' : '' ?>>Accepted Orders</option>
                        <option value="3" <?= $status == '3' ? 'selected' : '' ?>>Dispatched Orders</option>
                        <option value="2" <?= $status == '2' ? 'selected' : '' ?>>Canceled Orders</option>
                        <option value="4" <?= $status == '4' ? 'selected' : '' ?>>Completed Orders</option>
                    </select>
                </div>
                <div class="col-lg-2 mb-3" id="datepickerFrom">
                    <label>From Date</label>
                    <input type="text" class="form-control" id="searchByDateFrom" name="searchByDateFrom" readonly placeholder="dd-mm-yyyy" data-date-format="dd-mm-yyyy" data-date-container='#datepickerFrom' data-provide="datepicker" data-date-autoclose="true">
                </div>
                <div class="col-lg-2 mb-3" id="datepickerTo">
                    <label>To Date</label>
                    <input type="text" class="form-control" id="searchByDateTo" name="searchByDateTo" readonly placeholder="dd-mm-yyyy" data-date-format="dd-mm-yyyy" data-date-container='#datepickerTo' data-provide="datepicker" data-date-autoclose="true">
                </div>
                <div class="col-lg-4 mb-3">
                    <label class="d-block">Quick Range</label>
                    <div class="btn-group" role="group" id="quickRangeGroup">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-range="today">Today</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-range="week">This Week</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-range="month">This Month</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-range="clear">Clear</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="ajax_table" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Sr no.</th>
                                        <th>Date</th>
                                        <th>Order ID</th>
                                        <th>Name</th>
                                        <th>Contact</th>
                                        <th>Amount</th>
                                        <th>User Code</th>
                                        <th>Order Details</th>
                                        <th>Booking Items</th>
                                        <th>Booking Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="acceptModal" tabindex="-1" role="dialog" aria-labelledby="acceptModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title acceptHead"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?= base_url("acceptOrder") ?>">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">Estimated Time</label>
                                    <div class="col-sm-12">
                                        <div class="input-group" id="datepicker1">
                                            <input type="text" class="form-control" data-date-format="dd-mm-yyyy" readonly data-date-container='#datepicker1' data-provide="datepicker" name="estimated_date" value="<?= date('d-m-Y') ?>">
                                            <input type="time" class="form-control" name="estimated_time" required>
                                            <input name="id" type="hidden" class="booking_id">
                                            <div class="input-group-addon">
                                                <i class="fa fa-clock-o"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12" style="text-align: center; margin-top: 30px">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title acceptHead"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?= base_url("cancelOrder") ?>">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Cancel Message</label>
                                    <div class="col-sm-12">
                                        <textarea class="form-control" name="cancel_msg" rows="5" required></textarea>
                                        <input name="id" type="hidden" class="booking_id">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12" style="text-align: center; margin-top: 30px">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="show_modal" tabindex="-1" role="dialog" aria-labelledby="show_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title header-message"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" class="id">
                <div id="getData"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="printSpecificDiv()">Print</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="shiprocketSyncModal" tabindex="-1" role="dialog" aria-labelledby="shiprocketSyncModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ship with Shiprocket - <span id="sync_order_id_display"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="shiprocketSyncForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="sync_id">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label>Pickup Location</label>
                            <input type="text" name="pickup_location" id="sync_pickup_location" class="form-control" value="work" required>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label>Payment Method</label>
                            <select name="payment_method" id="sync_payment_method" class="form-control">
                                <option value="Prepaid">Prepaid</option>
                                <option value="COD">COD</option>
                            </select>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label>Customer Name</label>
                            <input type="text" name="name" id="sync_name" class="form-control" required>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label>Email</label>
                            <input type="email" name="email" id="sync_email" class="form-control" required>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" id="sync_phone" class="form-control" required>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label>Length (cm)</label>
                            <input type="number" name="length" class="form-control" value="10" step="0.1" required>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label>Breadth (cm)</label>
                            <input type="number" name="breadth" class="form-control" value="10" step="0.1" required>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label>Width (cm)</label>
                            <input type="number" name="width" class="form-control" value="10" step="0.1" required>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label>Height (cm)</label>
                            <input type="number" name="height" class="form-control" value="10" step="0.1" required>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label>Weight (kg)</label>
                            <input type="number" name="weight" class="form-control" value="0.5" step="0.01" required>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label>Shipping Address Line 1</label>
                            <input type="text" name="address" id="sync_address" class="form-control" required>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label>Shipping Address Line 2 (Optional)</label>
                            <input type="text" name="address_2" id="sync_address_2" class="form-control">
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label>City</label>
                            <input type="text" name="city" id="sync_city" class="form-control" required>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label>Pincode</label>
                            <input type="text" name="pincode" id="sync_pincode" class="form-control" required>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label>State</label>
                            <input type="text" name="state" id="sync_state" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="sync_submit_btn">Sync to Shiprocket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('admin/template/footer'); ?>
<script>
    function printSpecificDiv() {
    let dataEl = document.getElementById("getData");
    if (!dataEl) {
        alert("No order data found to print.");
        return;
    }
    let divContent = dataEl.innerHTML;

    let printWindow = window.open('', '', 'width=800,height=600');
    if (!printWindow) {
        alert("Popup blocked! Please allow popups for this site to print.");
        return;
    }

    printWindow.document.write(`
        <html>
        <head>
            <title>Order Details</title>
            <style>
                * { box-sizing: border-box; }
                body {
                    font-family: 'Segoe UI', Arial, sans-serif;
                    color: #2b2b2b;
                    font-size: 14px;
                    padding: 0;
                    margin: 0;
                }
                .print-header {
                    background: #4a1f1a;
                    color: #ffffff;
                    padding: 18px 24px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .print-header h3 {
                    margin: 0;
                    font-size: 18px;
                    font-weight: 600;
                    letter-spacing: 0.3px;
                }
                .print-header span {
                    font-size: 12px;
                    opacity: 0.85;
                }
                .print-body {
                    padding: 20px 24px;
                }
                .section-title {
                    font-size: 13px;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    color: #4a1f1a;
                    border-bottom: 2px solid #4a1f1a;
                    padding-bottom: 6px;
                    margin: 18px 0 12px;
                }
                .section-title:first-child {
                    margin-top: 0;
                }
                .row {
                    display: flex;
                    flex-wrap: wrap;
                    margin: 0 0 4px 0;
                }
                .row > div,
                [class*="col-lg-6"],
                [class*="col-lg-4"],
                [class*="col-lg-2"],
                [class*="col-lg-12"] {
                    padding: 6px 10px;
                }
                [class*="col-lg-6"] { flex: 0 0 50%; max-width: 50%; }
                [class*="col-lg-4"] { flex: 0 0 33.333%; max-width: 33.333%; }
                [class*="col-lg-2"] { flex: 0 0 16.666%; max-width: 16.666%; }
                [class*="col-lg-12"] { flex: 0 0 100%; max-width: 100%; }
                h5 {
                    margin: 0 0 2px 0;
                    font-size: 12px;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.3px;
                    color: #8a6d52;
                }
                h6 {
                    margin: 0;
                    font-size: 14px;
                    font-weight: 500;
                    color: #2b2b2b;
                }
                a { display: none !important; }
                hr {
                    border: none;
                    border-top: 1px solid #e5ddd7;
                    margin: 14px 0;
                }
                .badge, [class*="badge"] {
                    display: inline-block;
                    padding: 3px 10px;
                    border-radius: 20px;
                    font-size: 11px;
                    font-weight: 600;
                    background: #fdeccb;
                    color: #8a5a00;
                    border: none;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 8px;
                }
                table th, table td {
                    border: 1px solid #e5ddd7;
                    padding: 8px 10px;
                    font-size: 13px;
                }
                table th {
                    background: #f7f1ec;
                    color: #4a1f1a;
                    text-align: left;
                }
                input[type="hidden"] { display: none !important; }
                .print-footer {
                    text-align: center;
                    font-size: 11px;
                    color: #a89b8c;
                    padding: 14px 24px;
                    border-top: 1px solid #e5ddd7;
                }
                @media print {
                    .print-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                    .badge { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h3>Order Details</h3>
                <span>${new Date().toLocaleDateString('en-GB')}</span>
            </div>
            <div class="print-body">
                ${divContent}
            </div>
            <div class="print-footer">Thank you for shopping with us</div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.onload = function () {
        printWindow.print();
    };
}

    $(document).on('click', '.addData', function() {
        var id = $(this).attr('data-id');
        var rid = $(this).attr('data-rid');
        var header = $(this).attr('data-header');
        var data_type = $(this).attr('data-type');

        $('#show_modal').modal('show');
        $('.header-message').text(header);
        $('.id').val(id);
        $("#getData").html("<h3 class='text-center'>Please Wait..</h3>");
        $.ajax({
            type: "GET",
            url: "<?= base_url($form_route) ?>",
            data: {
                id: id,
                data_type: data_type,
            },
            beforeSend: function() {
                $('.loader' + data_type + rid).addClass('fa-spin fa-spinner');
            },
            success: function(data) {
                $("#getData").html(data);
                $('.loader' + data_type + rid).removeClass('fa-spin fa-spinner');
            }
        });
    });

    $(document).on('click', '.accept', function() {
        let id = $(this).attr('id');
        let order_id = $(this).attr('datafld');
        $('.booking_id').val(id);
        $('.acceptHead').text(order_id);
        $('#acceptModal').modal('show');
    });

    $(document).on('click', '.cancel', function() {
        let id = $(this).attr('id');
        let order_id = $(this).attr('datafld');
        $('.booking_id').val(id);
        $('.acceptHead').text(order_id);
        $('#cancelModal').modal('show');
    });

    var dataTable = $('#ajax_table').DataTable({
        "scrollX": true,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'responsive': true,
        'aLengthMenu': [
            [10, 25, 50, 100, 200, 300, 400, 500],
            [10, 25, 50, 100, 200, 300, 400, 500]
        ],
        'order': [
            [1, 'desc']
        ],
        'ajax': {
            'url': '<?= $ajax_table ?>',
            'data': function(data) {
                data.searchByDateFrom = $('#searchByDateFrom').val();
                data.searchByDateTo = $('#searchByDateTo').val();
                data.searchByStatus = $('#searchByStatus').val();
            }
        },
        <?php if (isset($col_stop)) { ?> "aoColumnDefs": [{
                "bSortable": false,
                "aTargets": [<?= $col_stop ?>]
            }]
        <?php echo ",";
        } ?> 'columns': <?= $table_column ?>,
    });

    // ---- Snapshot cards: jump straight to that status bucket ----
    $('#snapshotRow').on('click', '.snapshot-card[data-status]', function() {
        $('#searchByStatus').val($(this).data('status'));
        $('#snapshotRow .snapshot-card[data-status]').removeClass('is-active');
        $(this).addClass('is-active');
        dataTable.draw();
        $('html, body').animate({
            scrollTop: $('#ajax_table').offset().top - 100
        }, 300);
    });

    // "Today's Revenue" is a shortcut into the existing quick-range buttons
    // rather than its own filter, so the two stay in sync.
    $('#snapshotTodayCard').on('click', function() {
        $('#quickRangeGroup [data-range="today"]').trigger('click');
    });

    // ---- Quick date-range shortcuts ----
    function formatDMY(d) {
        var dd = String(d.getDate()).padStart(2, '0');
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        return dd + '-' + mm + '-' + d.getFullYear();
    }

    $('#quickRangeGroup button').on('click', function() {
        var range = $(this).data('range');
        var today = new Date();
        var from = null;

        if (range === 'today') {
            from = today;
        } else if (range === 'week') {
            var day = today.getDay(); // 0 = Sunday
            var diffToMonday = (day === 0 ? 6 : day - 1);
            from = new Date(today);
            from.setDate(today.getDate() - diffToMonday);
        } else if (range === 'month') {
            from = new Date(today.getFullYear(), today.getMonth(), 1);
        }

        if (range === 'clear') {
            $('#searchByDateFrom').val('');
            $('#searchByDateTo').val('');
        } else {
            $('#searchByDateFrom').val(formatDMY(from));
            $('#searchByDateTo').val(formatDMY(today));
        }

        $('#quickRangeGroup button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        if (range !== 'clear') {
            $(this).removeClass('btn-outline-secondary').addClass('btn-secondary');
        }

        dataTable.draw();
    });

    // ---- Count-up snapshot numbers ----
    document.querySelectorAll('.counter').forEach(function(el) {
        var target = parseFloat(el.getAttribute('data-counter')) || 0;
        var decimals = parseInt(el.getAttribute('data-decimals')) || 0;
        var duration = 900;
        var start = null;

        function step(timestamp) {
            if (!start) start = timestamp;
            var progress = Math.min((timestamp - start) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = (target * eased).toLocaleString('en-IN', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals,
            });
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = target.toLocaleString('en-IN', {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals,
                });
            }
        }
        requestAnimationFrame(step);
    });

    $('#searchByDateFrom').change(function() {
        dataTable.draw();
    });

    $('#searchByDateTo').change(function() {
        dataTable.draw();
    });

    $('#searchByStatus').change(function() {
        dataTable.draw();
    });

    $(document).on('click', '.sync_payment', function() {
        let btn = $(this);
        let id = btn.attr('data-id');
        let orderId = btn.attr('data-order-id');

        if (!confirm('Check Razorpay for order ' + orderId + ' and mark it paid if the payment was actually captured?')) {
            return;
        }

        btn.prop('disabled', true).text('Syncing...');

        $.ajax({
            type: "POST",
            url: "<?= base_url('syncPaymentStatus') ?>",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(response) {
                alert(response.message);
                if (response.success) {
                    dataTable.draw();
                } else {
                    btn.prop('disabled', false).text('Sync Payment');
                }
            },
            error: function() {
                alert('An error occurred while syncing payment status. Please try again.');
                btn.prop('disabled', false).text('Sync Payment');
            }
        });
    });

    $(document).on('click', '.shiprocket_sync', function() {
        let id = $(this).attr('data-id');
        let order_id = $(this).attr('data-order-id');
        $('#sync_id').val(id);
        $('#sync_order_id_display').text(order_id);

        // Fetch order details for pre-filling
        $.ajax({
            type: "GET",
            url: "<?= base_url('shiprocketOrderDetails') ?>",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const parsed = response.data.parsed_address;
                    $('#sync_name').val(response.data.name);
                    $('#sync_email').val(response.data.email_id || 'customer@example.com');
                    $('#sync_phone').val(response.data.contact_no);
                    $('#sync_address').val(parsed.address);
                    $('#sync_address_2').val('');
                    $('#sync_city').val(parsed.city);
                    $('#sync_pincode').val(parsed.pincode);
                    $('#sync_state').val(parsed.state);
                    $('#sync_payment_method').val(response.data.payment_mode === 'COD' ? 'COD' : 'Prepaid');
                    $('#shiprocketSyncModal').modal('show');
                } else {
                    alert('Error fetching order details: ' + response.message);
                }
            }
        });
    });

    $('#shiprocketSyncForm').submit(function(e) {
        e.preventDefault();
        let btn = $('#sync_submit_btn');
        btn.prop('disabled', true).text('Syncing...');

        $.ajax({
            type: "POST",
            url: "<?= base_url('shipWithShiprocket') ?>",
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#shiprocketSyncModal').modal('hide');
                    dataTable.draw();
                } else {
                    alert('Sync failed: ' + response.message);
                    btn.prop('disabled', false).text('Sync to Shiprocket');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                btn.prop('disabled', false).text('Sync to Shiprocket');
            }
        });
    });
</script>