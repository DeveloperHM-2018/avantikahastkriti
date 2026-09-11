<?php $this->load->view('admin/template/header', $title);
$sCateId = $this->input->get('sCateId');
?>
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0 "><?= $title ?></h2>
                        <div>
                            <?php if (@PREV['product_add'] == 1 || USER_TYPE == '1') { ?>
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#upload-excel-modal-center">
                                    <i class="fa fa-file-excel"></i> Bulk Upload
                                </button>
                                <a href="<?= base_url("productAdd"); ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
                            <?php } ?>
                        </div>
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
                                <option value="<?= encryptId($s_list['category_id']) ?>" <?= (decryptId($sCateId) == $s_list['category_id']) || (sessionId('sCateId') == $s_list['category_id']) ? 'selected' : '' ?>><?= $s_list['category_name'] ?></option>
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
                            <table id="ajax_table" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Sr No.</th>
                                        <th>Product Name</th>
                                        <th>Category</th>
                                        <th>Sub Category</th>
                                        <th>Product Type</th>
                                        <th>Market Price</th>
                                        <th>Sale Price</th>
                                        <!--<th>MAX Quantity</th>-->
                                        <!--<th>Quantity</th>-->
                                        <th>Stock</th>
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

<!-- Bulk Upload Modal -->
<div class="modal fade" id="upload-excel-modal-center" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="post" action="" class="form_submit_import" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Import Products with Variants</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <label>Choose Excel File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control mb-2" name="file" id="upload_file" required accept=".csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                            <a href="<?= BASE_URL ?>assets/Avantika_Product_Import_Template.xlsx" download="Avantika_Product_Import_Template.xlsx"><i class="fa fa-download"></i> Download Sample Template</a>
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label>Sheet Name <span class="text-danger">*</span></label>
                            <select class="form-select" id="sheet_select" name="sheet_index" required>
                                <option value="">-- Select Sheet --</option>
                            </select>
                        </div>
                        <div class="col-lg-12 mb-3">
                            <div class="alert alert-info">
                                <strong>Quick Guide:</strong>
                                <ul class="mt-2" style="font-size: 13px;">
                                    <li><strong>Column A:</strong> Product Name (Required)</li>
                                    <li><strong>Column B:</strong> Category ID (Required)</li>
                                    <li><strong>Column E:</strong> Sale Price (Required)</li>
                                    <li><strong>Columns I-M:</strong> Variant fields (Size, Color, Price, Stock, SKU)</li>
                                </ul>
                                <small class=" mt-2 d-block">NOTE: For same product with multiple variants, repeat product name in multiple rows with different variant data.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row d-none wrong-answer-row" style="max-height: 400px; overflow-y: auto;">
                        <div class="col-lg-12">
                            <h6 class="mb-2">Import Results</h6>
                            <table class="table table-bordered table-sm w-100 nowrap">
                                <thead style="position: sticky; top: 0px; background: white;">
                                    <tr>
                                        <th>Row</th>
                                        <th>Product/Variant</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody class="wrong-answer"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="save_import">Upload Products</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('admin/template/footer'); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    $("#upload_file").on("change", function(e) {
        let file = e.target.files[0];
        if (!file) return;

        let reader = new FileReader();

        reader.onload = function(e) {
            let data = new Uint8Array(e.target.result);
            let workbook = XLSX.read(data, {
                type: "array"
            });

            // Clear old options
            $("#sheet_select").empty().append('<option value="">-- Select Sheet --</option>');

            if (file.name.endsWith(".csv")) {
                // CSV file -> only one sheet, index 0
                $("#sheet_select").append(
                    $("<option>", {
                        value: 0,
                        text: "Sheet1"
                    })
                );
            } else {
                // Excel file -> all sheet names with index
                workbook.SheetNames.forEach(function(sheetName, index) {
                    $("#sheet_select").append(
                        $("<option>", {
                            value: index,
                            text: sheetName
                        })
                    );
                });
            }
        };

        reader.readAsArrayBuffer(file);
    });

    $("form.form_submit_import").validate({
        errorClass: "error fail-alert",
        validClass: "valid success-alert",
        submitHandler: function(form) {
            event.preventDefault();
            var formData = new FormData(form);

            $('.wrong-answer-row').addClass('d-none');
            $('.wrong-answer').html('');

            $.ajax({
                url: "<?= BASE_URL . 'productsExcelImport' ?>",
                type: 'POST',
                data: formData,
                dataType: 'JSON',
                beforeSend: function() {
                    $("#save_import").text("").html("Uploading... <i class='fa fa-spin fa-spinner'></i>").attr('disabled', true);
                },
                success: function(data) {
                    toastr[data['color']](data['message']);
                    $("#save_import").text("Upload Products").attr('disabled', false);

                    if (data['html'] && data['html'].trim() !== '') {
                        $('.wrong-answer-row').removeClass('d-none');
                        $('.wrong-answer').html(data['html']);
                    } else {
                        $('.wrong-answer-row').addClass('d-none');
                        $('.wrong-answer').html('');
                    }

                    // Reload table if successful
                    if (data['status']) {
                        setTimeout(function() {
                            dataTable.ajax.reload();
                        }, 2000);
                    }
                },
                error: function() {
                    toastr['error']('Upload failed. Please try again.');
                    $("#save_import").text("Upload Products").attr('disabled', false);
                },
                cache: false,
                contentType: false,
                processData: false,
            });
        },
        errorPlacement: function(error, element) {
            if ($(element).is('select.select2')) {
                element.next().after(error);
            } else {
                error.insertAfter(element);
            }
        }
    });
</script>

<script>
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