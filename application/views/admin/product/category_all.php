<?php $this->load->view('admin/template/header', $title); ?>
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0 "><?= $title ?></h2>
                        <a href="<?= base_url("categoryAdd"); ?>" class="btn btn-success"><i class="fa fa-plus"></i> Add</a>
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
                                        <th>Sr no.</th>
                                        <th>Category Name</th>
                                        <th>Image</th>
                                        <!-- <th>Total Sub Category</th> -->
                                        <th>Featured</th>
                                        <th style="width: 20%">Edit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($category_all) {
                                        $i = 0;
                                        foreach ($category_all as $all) {
                                            $id = encryptId($all['category_id']);
                                            $getTotalSubCate = getNumRows('sub_category', "category_id = '" . $all['category_id'] . "'");
                                    ?>
                                            <tr>
                                                <td><?= ++$i; ?></td>
                                                <td><?= $all['category_name'] ?></td>
                                                    <td>
                                                        <a href="<?= base_url("upload/category/") . $all['image']; ?>">
                                                            <img src="<?= base_url("upload/category/") . $all['image']; ?>" style="width: 60px; height: 50px">
                                                        </a>
                                                    </td>
                                                <!-- <td>
                                                    <span class="badge rounded-pill bg-warning" style="font-size: 18px;"><?= $getTotalSubCate; ?></span>
                                                </td> -->
                                                <td>
                                                    <a href="<?= base_url("categoryFeatured/$id/" . (($all['featured'] == '0')? 1 : 0)) ?>/" class="badge rounded-pill bg-<?= (($all['featured'] == '0')? 'light' : 'danger') ?>" style="font-size: 18px;"><?= (($all['featured'] == '0')? 'No' : 'Yes') ?></a>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url("categoryAdd?id=$id"); ?>" class="btn btn-success"><i class="fa fa-edit"></i> Edit</a>
                                                    <a onclick="return confirm('Are you want to sure ?')" href="<?= base_url("categoryAdd?dID=$id"); ?>" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</a>
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