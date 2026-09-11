<option value="">Select Sub Category</option>
<?php

if ($type == 1) {
    if ($all_data) {
        foreach ($all_data as $c) {
?>
            <option value="<?php echo $c['sub_category_id']; ?>"><?= ucwords($c['sub_category_name']) ?></option>
        <?php
        }
    } else {
        ?>
        <option value="">No data available</option>
    <?php
    }
} else if ($type == 2) {
    ?>
    <option value="<?= base_url("productAll") ?>">Select Sub Category</option>
    <?php
    if ($get_sub_cate) {
        foreach ($get_sub_cate as $c) {
    ?>
            <option value="<?= base_url("productAll?cate=$category_id&sCateId=") . encryptId($c['sub_category_id']); ?>"><?php echo $c['sub_category_name']; ?></option>
        <?php
        }
    } else {
        ?>
        <option value="">No data available</option>
<?php
    }
}
