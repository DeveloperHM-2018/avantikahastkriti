<?php
$this->load->view('admin/template/header', $title);
?>
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0 "><?= $title ?></h2>
                        <button class="btn btn-success mb-2 addData" data-rid="0" data-id="0" data-header="Add <?= $title ?>">
                            <i class="fa fa-plus loader2<?= 0 ?>"></i> Add
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="datatable" class="table table-bordered  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Sr no.</th>
                                        <th>Question</th>
                                        <th>Order</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($all_data) {
                                        $i = 0;
                                        foreach ($all_data as $all) {
                                            $id = encryptId($all['id']);
                                    ?>
                                            <tr>
                                                <td><?= ++$i; ?></td>
                                                <td><?= $all['question'] ?></td>
                                                <td><?= $all['sort_order'] ?></td>
                                                <td><?= $all['status'] == '1' ? 'Enable' : 'Disable' ?></td>
                                                <td>
                                                    <button class="btn btn-success addData" data-rid="<?= $i ?>" data-id="<?= $id ?>" data-header="Edit <?= $title ?>">
                                                        <i class="fa fa-edit loader2<?= $i ?>"></i> Edit
                                                    </button>
                                                    <a href="<?= base_url("faqAdd?dID=$id"); ?>" class="btn btn-danger confirm_data" title="FAQ Delete" title-text="Are you sure ?" icon="warning">Delete</a>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="show_modal" tabindex="-1" role="dialog" aria-labelledby="show_modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="post" action="<?= $form_route ?>" name="form_submit" enctype="multipart/form-data">
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
                    <button type="submit" class="btn btn-primary" id="save">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->load->view('admin/template/footer'); ?>
<script>
    $(document).on('click', '.addData', function() {
        var id = $(this).attr('data-id');
        var rid = $(this).attr('data-rid');
        var header = $(this).attr('data-header');

        $('#show_modal').modal('show');
        $('.header-message').text(header);
        $('.id').val(id);

        $.ajax({
            type: "GET",
            url: "<?= base_url($form_route) ?>",
            data: {
                id: id
            },
            beforeSend: function() {
                $('.loader2' + rid).addClass('fa-spin fa-spinner');
            },
            success: function(data) {
                $("#getData").html(data);
                $('.loader2' + rid).removeClass('fa-spin fa-spinner');
            }
        });
    });

    $("form[name='form_submit']").validate({
        errorClass: "error fail-alert",
        validClass: "valid success-alert",
        submitHandler: function(form) {
            $("#save").text("").html("Loading.. <i class='fa fa-spin fa-spinner'></i>").attr('disabled', true);
            form.submit();
        }
    });
</script>
