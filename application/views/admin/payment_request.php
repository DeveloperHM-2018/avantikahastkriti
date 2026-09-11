<?php
$this->load->view('admin/template/header', $title);
?>
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
                <div class="col-lg-2 mb-3">
                    <label>Payment Status</label>
                    <select name="searchByStatus" class="form-select" id="searchByStatus">
                        <option value="">-----All-----</option>
                        <option value="0" selected>Pending</option>
                        <option value="1">Approve</option>
                        <option value="2">Canceled</option>
                    </select>
                </div>
                <div class="col-lg-2 mb-3" id="datepicker2">
                    <label>From Date</label>
                    <input type="text" class="form-control" id="searchByDateFrom" name="searchByDateFrom" readonly placeholder="dd-mm-yyyy" data-date-format="dd-mm-yyyy" data-date-container='#datepicker2' data-provide="datepicker" data-date-autoclose="true">
                </div>
                <div class="col-lg-2 mb-3" id="datepicker2">
                    <label>To Date</label>
                    <input type="text" class="form-control" id="searchByDateTo" name="searchByDateTo" readonly placeholder="dd-mm-yyyy" data-date-format="dd-mm-yyyy" data-date-container='#datepicker2' data-provide="datepicker" data-date-autoclose="true">
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
                                        <th>Name</th>
                                        <th>Contact Number</th>
                                        <th>Vendor Code</th>
                                        <th>Amount</th>
                                        <th>Screen Sort</th>
                                        <th>Status</th>
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
            [10, 25, 50, 100, 200, 300, 400, 500],
            [10, 25, 50, 100, 200, 300, 400, 500]
        ],
        'order': [
            [1, 'asc']
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

    $('#searchByDateFrom').change(function() {
        dataTable.draw();
    });

    $('#searchByDateTo').change(function() {
        dataTable.draw();
    });

    $('#searchByStatus').change(function() {
        dataTable.draw();
    });
</script>