<?php $this->load->view('admin/template/header', $title);

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
                <div class="col-lg-4 mb-3" id="datepicker2">
                    <label>Category</label>
                    <select name="searchBySubCategory" class="form-select select2" id="searchBySubCategory">
                        <option value="">-----All-----</option>
                        <?php
                        if ($all_category) {
                            foreach ($all_category as $s_list) {
                        ?>
                                <option value="<?= encryptId($s_list['category_id']) ?>"><?= $s_list['category_name'] ?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="<?= $form_url ?>" method="post" name="form_submit_alert" enctype="multipart/form-data">
                                <table id="ajax_table" class="table table-bordered dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Product Name</th>
                                            <th>Sub Category</th>
                                            <th>Market Price</th>
                                            <th>Sale Price</th>
                                            <th>Admin Price</th>
                                        </tr>
                                    </thead>
                                </table>
                                <div class="col-lg-12 text-center">
                                    <button class="btn btn-primary" id="save_alert" title="Product Rate Update" title-text="Are you sure ?" icon="warning">Save</button>
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
    $('#save_alert').click(function() {
        let check_user = 0;
        $(".user_sale_price").each(function() {
            let value = $(this).val();
            if (value > 0) {
                ++check_user;
            }
        });
        if (check_user < 1) {
            Command: toastr["warning"]("Add Rate At least One Item");
            return false;
        }
    });

    var dataTable = $('#ajax_table').DataTable({
        "stateSave": true,
        "scrollX": true,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'responsive': true,

        order: [
            [0, 'asc']
        ],
        'ajax': {
            'url': '<?= $ajax_table ?>',
            'data': function(data) {

                var subCategory = $('#searchBySubCategory').val();
                data.searchBySubCategory = subCategory;

            }
        },
        <?php if (isset($col_stop)) { ?> "aoColumnDefs": [{
                "bSortable": false,
                "aTargets": [<?= $col_stop ?>]
            }]
        <?php echo ",";
        } ?> 'columns': <?= $table_column ?>,
    });

    $('#searchBySubCategory').change(function() {
        dataTable.draw();
    });
</script>