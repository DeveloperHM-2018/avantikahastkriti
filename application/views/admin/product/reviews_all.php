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
            
            <?php if ($this->session->flashdata('errors')) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('errors') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">Sr no.</th>
                                        <th style="width: 15%">Product</th>
                                        <th style="width: 12%">Customer</th>
                                        <th style="width: 10%">Rating</th>
                                        <th style="width: 25%">Review</th>
                                        <th style="width: 15%">Images</th>
                                        <th style="width: 10%">Date</th>
                                        <th style="width: 8%">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($reviews) {
                                        $i = 0;
                                        foreach ($reviews as $all) {
                                            $id = encryptId($all['id']);
                                    ?>
                                            <tr>
                                                <td><?= ++$i; ?></td>
                                                <td><?= htmlspecialchars($all['product_name']) ?></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($all['user_name']) ?></strong><br>
                                                    <small class="text-muted"><?= htmlspecialchars($all['contact_no']) ?></small>
                                                </td>
                                                <td>
                                                    <div class="text-warning">
                                                        <?php
                                                        for ($s = 1; $s <= 5; $s++) {
                                                            if ($s <= $all['rating']) {
                                                                echo '<i class="fa fa-star" style="color: #ffc107;"></i>';
                                                            } else {
                                                                echo '<i class="far fa-star text-muted"></i>';
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <small class="text-muted">(<?= $all['rating'] ?>/5)</small>
                                                </td>
                                                <td>
                                                    <?php if ($all['review_title']) { ?>
                                                        <strong><?= $all['review_title'] ?></strong><br>
                                                    <?php } ?>
                                                    <div class="" style=" font-size: 13px;"><?= $all['review_text'] ?></div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                         <?php 
                                                         $rev_imgs = $this->CommonModel->getRowById('product_review_images', 'review_id', $all['id']);
                                                         if (!empty($rev_imgs)) {
                                                             foreach ($rev_imgs as $img_row) {
                                                         ?>
                                                                 <a href="<?= base_url(REVIEW_IMAGE) . $img_row['image_path']; ?>" target="_blank" class="d-inline-block border rounded p-1">
                                                                     <img src="<?= base_url(REVIEW_IMAGE) . $img_row['image_path']; ?>" style="width: 45px; height: 45px; object-fit: cover;">
                                                                 </a>
                                                         <?php
                                                             }
                                                         } else {
                                                             echo '<span class="text-xs">No images</span>';
                                                         }
                                                         ?>
                                                    </div>
                                                </td>
                                                <td><small><?= dateConvertToView($all['create_date'], 3) ?></small></td>
                                                <td>
                                                    <?php if ($all['status'] == 1) { ?>
                                                        <a href="<?= base_url("productReviewToggleStatus/$id/0") ?>" class="badge bg-success" style="font-size: 13px;">Active</a>
                                                    <?php } else { ?>
                                                        <a href="<?= base_url("productReviewToggleStatus/$id/1") ?>" class="badge bg-danger" style="font-size: 13px;">Deactive</a>
                                                    <?php } ?>
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
