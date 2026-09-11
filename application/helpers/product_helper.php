<?php
function product($row, $ss)
{
    $data = getSingleRowById('product_image', array('product_id' => $row['product_id']));
?>
    <div class="product-card">
        <div class="product-media">
            <div class="product-label">
                <?php if ($ss == 'new') {  ?>
                    <label class="label-text new">new</label>
                <?php  } else if ($ss == 'rate') { ?>
                    <label class="label-text rate">4.8</label>

                <?php  } else  if ($ss == 'discount') { ?>
                    <label class="label-text off">-10%</label>

                <?php } else {  ?>
                    <!-- <label class="label-text sale"><?= $row['quantity']; ?> <?= $row['quantity_type']; ?></label> -->
                <?php } ?>
            </div>
            <!-- <button class="product-wish wish"><i class="fas fa-heart"></i></button> -->

            <a class="product-image" href="<?= base_url('product-details/' . encryptId($row['product_id']) . '/' . url_title($row['product_name'])) ?>">
                <img src="<?= setImage(@$data['image_path'], 'upload/product/') ?>" alt="product" style="height:185px;object-fit:contain">
            </a>
        </div>
        <div class="product-content">
            <div class="product-rating"><i class="active icofont-star"></i><i class="active icofont-star"></i><i class="active icofont-star"></i><i class="active icofont-star"></i><i class="icofont-star"></i><a href="<?= base_url('product-details/' . encryptId($row['product_id']) . '/' . url_title($row['product_name'], 'dash', true)) ?>">(3)</a></div>
            <h6 class="product-name"><a href="<?= base_url('product-details/' . encryptId($row['product_id']) . '/' . url_title($row['product_name'], 'dash', true)) ?>"><?= $row['product_name']; ?></a></h6>
            <h6 class="product-price"><del>₹<?= $row['market_price']; ?></del><span>₹<?= $row['sale_price']; ?><small></small></span></h6>

            <div class="product-action">
                <button class="action-minus" title="Quantity Minus" data-rowid="<?= $row['product_id'] ?>" data-type="sidecart"><i class="icofont-minus"></i></button>
                <input class="action-input" title="Quantity Number" id="qtysidecart<?= $row['product_id'] ?>" type="text" name="quantity" value="1">
                <button class="action-plus" title="Quantity Plus" data-rowid="<?= $row['product_id'] ?>" data-type="sidecart"><i class="icofont-plus"></i></button>
            </div>
            <button class="product-add  addCart  crtbtn-<?= $row['product_id'] ?>" data-id="<?= $row['product_id'] ?>" title="Add to Cart"><i class="fas fa-shopping-basket"></i><span>add</span></button>
        </div>
    </div>
<?php
}

function feature_product($row)
{
    $data = getSingleRowById('product_image', array('product_id' => $row['product_id']));

?>
    <div class="col">
        <div class="feature-card">
            <div class="feature-media">
                <div class="feature-label"><label class="label-text feat">feature</label></div>
                <a class="feature-image" href="<?= base_url('product-details/' . encryptId($row['product_id']) . '/' . url_title($row['product_name'])) ?>">
                    <img src="<?= setImage($data['image_path'], 'upload/product/') ?>" alt="product">
                </a>
            </div>
            <div class="feature-content">
                <h6 class="feature-name"><a href="<?= base_url('product-details/' . encryptId($row['product_id']) . '/' . url_title($row['product_name'])) ?>"><?= $row['product_name']; ?></a></h6>
                <div class="feature-rating"><i class="active icofont-star"></i><i class="active icofont-star"></i><i class="active icofont-star"></i><i class="active icofont-star"></i><i class="icofont-star"></i><a href="<?= base_url('product-details/' . encryptId($row['product_id']) . '/' . url_title($row['product_name'])) ?>">(3 Reviews)</a></div>
                <h6 class="feature-price"><del>₹<?= $row['market_price']; ?></del><span>₹<?= $row['sale_price']; ?><small>/piece</small></span></h6>
                <p class="feature-desc">
                    <?= substr(strip_tags($row['description']), '0', '80') ?>...
                </p>
                <div class="product-action">
                    <button class="action-minus" title="Quantity Minus" data-rowid="<?= $row['product_id'] ?>" data-type="sidecart"><i class="icofont-minus"></i></button>
                    <input class="action-input" title="Quantity Number" id="qtysidecart<?= $row['product_id'] ?>" type="text" name="quantity" value="1">
                    <button class="action-plus" title="Quantity Plus" data-rowid="<?= $row['product_id'] ?>" data-type="sidecart"><i class="icofont-plus"></i></button>
                </div>
                <button class="product-add  addCart  crtbtn-<?= $row['product_id'] ?>" data-id="<?= $row['product_id'] ?>" title="Add to Cart"><i class="fas fa-shopping-basket"></i><span>add</span></button>
            </div>
        </div>
    </div>


<?php
}

?>