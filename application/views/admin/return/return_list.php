<?php $this->load->view('admin/template/header', $title); ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0 "><?= $title ?></h2>
                        <button type="button" class="btn btn-success" id="btnExportCsv"><i class="fa fa-download"></i> Export CSV</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-2 mb-3">
                    <label>Status</label>
                    <select name="searchByStatus" class="form-select" id="searchByStatus">
                        <option value="">-----All-----</option>
                        <option value="0">Requested</option>
                        <option value="1">Under Review</option>
                        <option value="2">Approved</option>
                        <option value="3">Pickup Scheduled</option>
                        <option value="4">Picked Up</option>
                        <option value="5">Received At Warehouse</option>
                        <option value="6">Refund Processed</option>
                        <option value="7">Rejected</option>
                    </select>
                </div>
                <div class="col-lg-2 mb-3">
                    <label>Reason</label>
                    <select name="searchByReason" class="form-select" id="searchByReason">
                        <option value="">-----All-----</option>
                        <?php foreach ($reasons as $reason): ?>
                            <option value="<?= $reason ?>"><?= $reason ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 mb-3">
                    <label>Payment Method</label>
                    <select name="searchByPaymentMode" class="form-select" id="searchByPaymentMode">
                        <option value="">-----All-----</option>
                        <option value="COD">COD</option>
                        <option value="ONLINE">Online</option>
                    </select>
                </div>
                <div class="col-lg-2 mb-3">
                    <label>Order Number</label>
                    <input type="text" class="form-control" id="searchByOrderId" placeholder="Order ID">
                </div>
                <div class="col-lg-2 mb-3">
                    <label>Mobile</label>
                    <input type="text" class="form-control" id="searchByMobile" placeholder="Customer mobile">
                </div>
                <div class="col-lg-2 mb-3" id="datepicker2">
                    <label>From Date</label>
                    <input type="text" class="form-control" id="searchByDateFrom" readonly placeholder="dd-mm-yyyy" data-date-format="dd-mm-yyyy" data-date-container='#datepicker2' data-provide="datepicker" data-date-autoclose="true">
                </div>
                <div class="col-lg-2 mb-3" id="datepicker3">
                    <label>To Date</label>
                    <input type="text" class="form-control" id="searchByDateTo" readonly placeholder="dd-mm-yyyy" data-date-format="dd-mm-yyyy" data-date-container='#datepicker3' data-provide="datepicker" data-date-autoclose="true">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="ajax_table" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Return ID</th>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Reason</th>
                                        <th>Request Date</th>
                                        <th>Current Status</th>
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

<?php $this->load->view('admin/template/footer'); ?>
<script>
    var dataTable = $('#ajax_table').DataTable({
        "scrollX": true,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'responsive': true,
        'aLengthMenu': [
            [10, 25, 50, 100, 200],
            [10, 25, 50, 100, 200]
        ],
        'order': [
            [6, 'desc']
        ],
        'ajax': {
            'url': '<?= $ajax_table ?>',
            'data': function(data) {
                data.searchByDateFrom = $('#searchByDateFrom').val();
                data.searchByDateTo = $('#searchByDateTo').val();
                data.searchByStatus = $('#searchByStatus').val();
                data.searchByReason = $('#searchByReason').val();
                data.searchByPaymentMode = $('#searchByPaymentMode').val();
                data.searchByOrderId = $('#searchByOrderId').val();
                data.searchByMobile = $('#searchByMobile').val();
            }
        },
        <?php if (isset($col_stop)) { ?> "aoColumnDefs": [{
                "bSortable": false,
                "aTargets": [<?= $col_stop ?>]
            }]
        <?php echo ",";
        } ?> 'columns': <?= $table_column ?>,
    });

    $('#searchByDateFrom, #searchByDateTo').change(function() {
        dataTable.draw();
    });
    $('#searchByStatus, #searchByReason, #searchByPaymentMode').change(function() {
        dataTable.draw();
    });
    let orderIdTimer, mobileTimer;
    $('#searchByOrderId').on('keyup', function() {
        clearTimeout(orderIdTimer);
        orderIdTimer = setTimeout(() => dataTable.draw(), 400);
    });
    $('#searchByMobile').on('keyup', function() {
        clearTimeout(mobileTimer);
        mobileTimer = setTimeout(() => dataTable.draw(), 400);
    });

    $('#btnExportCsv').on('click', function() {
        var params = new URLSearchParams({
            searchByDateFrom: $('#searchByDateFrom').val(),
            searchByDateTo: $('#searchByDateTo').val(),
            searchByStatus: $('#searchByStatus').val(),
            searchByReason: $('#searchByReason').val(),
            searchByPaymentMode: $('#searchByPaymentMode').val(),
            searchByOrderId: $('#searchByOrderId').val(),
            searchByMobile: $('#searchByMobile').val(),
        });
        window.location = "<?= base_url('returnsExportCsv') ?>?" + params.toString();
    });
</script>
