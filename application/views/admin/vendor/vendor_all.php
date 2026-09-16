<?php $this->load->view('admin/template/header', $title); ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0 "><?= $title ?></h2>
                        <a href="<?= base_url('vendorAdd') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Add Vendor</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 mb-3">
                    <label>Status</label>
                    <select class="form-select" id="searchByStatus">
                        <option value="">-----All-----</option>
                        <option value="0">Pending</option>
                        <option value="1">Active</option>
                        <option value="2">Rejected</option>
                        <option value="3">Suspended</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="ajax_table" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Business Name</th>
                                        <th>Contact</th>
                                        <th>Email</th>
                                        <th>Registered</th>
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
        'ajax': {
            'url': '<?= $ajax_table ?>',
            'data': function(data) {
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

    $('#searchByStatus').change(function() {
        dataTable.draw();
    });
</script>
