<?php
if ($searchData != '') {

    foreach ($searchData as $row) {
        $data = getSingleRowById('product_image', array('product_id' => $row['product_id']));
        $productSlug = url_title($row['product_name'], '-', true) . '-' . $row['product_id'];
        ?>
          <a href="<?= base_url('product/' . $productSlug) ?>" class="item flex items-center justify-between w-full pb-5 border-b border-line gap-6 mt-5">
                <div class="bg-img w-[10%] aspect-square flex-shrink-0 rounded-lg overflow-hidden">
                    <img src="<?= base_url() ?>upload/product/<?= $data['image_path'] ?>" alt="img" class="w-full h-full">
                </div>
                <div class="flex items-center justify-between w-full">
                    <div>
                        <div class="name text-title"> <?= $row['product_name'] ?></div>
                    </div>
                    <div class="text-title">
                        <span>₹<?= $row['sale_price']; ?></span>
                        <?php if ($row['is_out_of_stock'] == 1) : ?>
                            <span class="text-red caption2 font-semibold d-block">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        <?php
    }
} else {
    echo 'No Product Found';
} 
?>