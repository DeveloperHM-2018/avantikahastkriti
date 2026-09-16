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
                <div class="col-lg-3 mb-3" id="datepicker2">
                    <label>From Date</label>
                    <input type="text" class="form-control" id="searchByDateFrom" readonly placeholder="dd-mm-yyyy" data-date-format="dd-mm-yyyy" data-date-container='#datepicker2' data-provide="datepicker" data-date-autoclose="true">
                </div>
                <div class="col-lg-3 mb-3" id="datepicker3">
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
                                        <th>Date</th>
                                        <th>Actor</th>
                                        <th>Action</th>
                                        <th>Entity</th>
                                        <th>IP Address</th>
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
        'order': [
            [0, 'desc']
        ],
        'ajax': {
            'url': '<?= $ajax_table ?>',
            'data': function(data) {
                data.searchByDateFrom = $('#searchByDateFrom').val();
                data.searchByDateTo = $('#searchByDateTo').val();
            }
        },
        'columns': <?= $table_column ?>,
    });

    $('#searchByDateFrom, #searchByDateTo').change(function() {
        dataTable.draw();
    });
</script>
