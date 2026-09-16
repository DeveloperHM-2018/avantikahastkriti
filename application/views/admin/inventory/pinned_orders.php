<?php $this->load->view('admin/template/header', $title); ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0 "><?= $title ?></h2>
                    </div>
                    <div class="alert alert-warning">
                        These orders were confirmed paid after stock had already run out (lost a race to another order
                        placed for the same item). Nothing was shipped automatically - review each one and either
                        restock/fulfil it, or cancel &amp; refund it via the order tools, then mark it resolved here.
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="ajax_table" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Reason</th>
                                        <th>Order Date</th>
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
        'ajax': '<?= $ajax_table ?>',
        <?php if (isset($col_stop)) { ?> "aoColumnDefs": [{
                "bSortable": false,
                "aTargets": [<?= $col_stop ?>]
            }]
        <?php echo ",";
        } ?> 'columns': <?= $table_column ?>,
    });

    $(document).on('click', '.resolvePinBtn', function() {
        var id = $(this).data('id');
        var resolution = prompt('Resolution note (e.g. "Restocked and shipped" or "Cancelled and refunded"):');
        if (!resolution) {
            return;
        }
        $.post('<?= base_url('resolvePinnedOrder') ?>', {
            id: id,
            resolution: resolution
        }, function(res) {
            alert(res.message);
            if (res.status) {
                dataTable.draw();
            }
        }, 'json');
    });
</script>
