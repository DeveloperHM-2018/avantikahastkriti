<option value="">Select Sub Category Type</option>
<?php


if ($all_data) {
    foreach ($all_data as $c) {
?>
        <option value="<?php echo $c['sub_category_type_id']; ?>"><?= ucwords($c['sub_category_type_name']) ?></option>
    <?php
    }
} else {
    ?>
    <option value="">No data available</option>
<?php
}
?>