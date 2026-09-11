<?php $this->load->view('admin/template/header', $title); ?>
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0 "><?= $title ?></h2>
                        <?php if (@PREV['product_sub_category_add'] == 1 || USER_TYPE == '1') { ?>
                            <a href="<?= base_url("subCategoryTypeAdd"); ?>" class="btn btn-success"><i class="fa fa-plus"></i> Add</a>
                        <?php } ?>
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
                                        <th>Sr No.</th>
                                        <th>Sub Category Type Name</th>
                                        <th>Category Name</th>
                                        <th>Sub Category Name</th>
                                        <th>Image</th>
                                        <th style="width: 15%">View Product</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($sub_category_type) {
                                        $i = 0;
                                        foreach ($sub_category_type as $item) {
                                            $category_name = getSingleRowById('category', "category_id = '" . $item['category_id'] . "'");
                                            $sub_category_name = getSingleRowById('sub_category', "sub_category_id = '" . $item['sub_category_id'] . "'");
                                            $i = $i + 1;
                                            $id = encryptId($item['category_id']);
                                            $getRows = getNumRows('product', "sub_category_id = '" . $item['sub_category_id'] . "' AND is_delete = '1'");
                                    ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td><?= ucwords($item['sub_category_type_name']) ?> </td>
                                                <td><?= ucwords($category_name['category_name']) ?> </td>
                                                <td><?= ucwords($sub_category_name['sub_category_name']) ?> </td>
                                                <td>
                                                    <a href="upload/category/<?= $item['sub_category_type_image'] ?>">
                                                        <img src="upload/category/<?= $item['sub_category_type_image'] ?>" width="60" height="40">
                                                    </a>
                                                </td>
                                                <td>
                                                    <?php if (@PREV['product_view'] == 1 || USER_TYPE == '1') { ?>
                                                        <a href="<?php echo base_url("productAll?sCateId=$id"); ?>" class="btn btn-success"><i class="fa fa-eye"></i> View</a>
                                                    <?php } ?>
                                                    <span class="badge bg-yellow" style="margin-left: 10px"><?= $getRows; ?></span>
                                                </td>
                                                <td>
                                                    <?php if (@PREV['product_sub_category_edit'] == 1 || USER_TYPE == '1') { ?>
                                                        <a href="<?php echo base_url(); ?>subCategoryAdd?id=<?php echo $id; ?>" class="btn btn-success"><i class="fa fa-edit"></i> Edit</a>
                                                    <?php } ?>
                                                    <?php if (@PREV['product_sub_category_delete'] == 1 || USER_TYPE == '1') { ?>
                                                        <a onclick="return confirm('Are you want to sure ?')" href="<?= base_url("subCategoryAdd?dID=$id"); ?>" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } ?>
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