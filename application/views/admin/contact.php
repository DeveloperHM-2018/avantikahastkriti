<?php $this->load->view('admin/template/header', $title); ?>
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0 ">
                            <?= $title ?>
                        </h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th style="width: 8%">S.n.</th>
                                        <th style="width: 10%">Date</th>
                                        <th style="width: 15%">Name</th>
                                        <th style="width: 12%">Email</th>
                                        <th style="width: 12%">Phone</th>
                                        <th style="width: 12%">Message</th>
                                        <th style="width: 15%">Action</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($contact) {
                                        $i = 0;
                                        foreach ($contact as $all) { 
                                             $id = encryptId($all['cid']);
                                    ?>
                                            <tr>
                                                <td>
                                                    <?= ++$i; ?>
                                                </td>
                                                <td>
                                                    <?= wordwrap(date('d-M-Y h:i A', strtotime($all['create_date'])), 10, "<br />\n"); ?>
                                                </td>
                                                 
                                                <td>
                                                    <?= $all['name'] ?>
                                                </td>
                                                <td>
                                                    <?= $all['email'] ?>
                                                </td>
                                                <td>
                                                    <?= $all['phone'] ?>
                                                </td>
                                                <td>
                                                    <?= wordwrap($all['message'] , 45, "<br />\n"); ?>
                                                </td>
                                                <td>
                                                    <a onclick="return confirm('Are you want to sure ?')" href="<?= base_url("contact_query?BdID=$id"); ?>" class="btn btn-danger"><i class="fa fa-trash"></i>  </a>
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

<?php $this->load->view('admin/template/footer'); ?>
<script>
    $('.accept').click(function() {
        let id = $(this).attr('id');
        let order_id = $(this).attr('datafld');
        $('.booking_id').val(id);
        $('.acceptHead').text(order_id);
        $('.acceptModal').modal('show');
    });

    function checkCancel(button) {
        let cancelBtn = button;
        let id = cancelBtn.getAttribute('id');
        let order_id = cancelBtn.getAttribute('datafld');
        $('.booking_id').val(id);
        $('.acceptHead').text(order_id);
        $('.cancelModal').modal('show');
    }

    function hideCancelModal() {
        $('.cancelModal').modal('hide');
    }

    // $('.cancel').click(function () {
    //     let id = $(this).attr('id');
    //     let order_id = $(this).attr('datafld');

    // });
</script>