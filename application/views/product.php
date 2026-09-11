<?php
include('includes/header-link.php');
include('includes/header.php');

// Fetch reviews
$select = "r.*, u.name as user_name, u.profile_image";
$join = [
    ['user_registration u', 'r.user_id = u.user_id', 'INNER']
];
$reviews = $this->CommonModel->getRowWithMultiJoin($select, 'product_reviews r', "r.product_id = '$product_id' AND r.status = '1'", $join, 'r.create_date', 'DESC');

// Calculate average rating
$avg_rating = 0;
$total_reviews = 0;
$rating_counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

if (!empty($reviews)) {
    $total_reviews = count($reviews);
    $sum_rating = 0;
    foreach ($reviews as $rev) {
        $sum_rating += $rev['rating'];
        $rating_counts[$rev['rating']]++;
    }
    $avg_rating = round($sum_rating / $total_reviews, 1);
}

// Check if user has purchased this product
$userId = sessionId('login_user_id');
$hasPurchased = false;
$hasReviewed = false;

if ($userId) {
    // Check if user purchased this product
    $purchaseCheck = $this->CommonModel->getRowWithMultiJoin(
        '1',
        'book_product bp',
        "bp.user_id = '$userId' AND bi.product_id = '$product_id' AND bp.transaction_status = '1'",
        [['book_item bi', 'bp.product_book_id = bi.product_book_id', 'INNER']],
        '',
        '',
        2
    );
    if ($purchaseCheck) {
        $hasPurchased = true;
    }

    // Check if already reviewed
    $reviewCheck = $this->CommonModel->getSingleRowById('product_reviews', [
        'user_id' => $userId,
        'product_id' => $product_id
    ]);
    if ($reviewCheck) {
        $hasReviewed = true;
    }
}
?>

<style>
    #selectedQuantityLabel {
        margin-left: -11px;
        margin-top: 3px;
        font-size: 14px;
    }
</style>
<!-- Menu bar -->
<div class="breadcrumb-product">
    <div class="main bg-surface md:pt-[88px] pt-3 pb-[14px]">
        <div class="container flex items-center justify-between flex-wrap gap-3">
            <div class="left flex items-center gap-1">
                <a href="<?= base_url() ?>" class="caption1 text-secondary2 hover:underline">Homepage</a>
                <i class="ph ph-caret-right text-xs text-secondary2"></i>
                <div class="caption1 text-secondary2">Product</div>
                <i class="ph ph-caret-right text-xs text-secondary2"></i>
                <div class="caption1 capitalize">Product Default</div>
            </div>
            <div class="right flex items-center gap-3">
                <div class="prev-btn flex items-center cursor-pointer text-secondary hover:text-black pr-3 border-r border-line">
                    <i class="ph ph-caret-circle-left text-2xl text-black"></i>
                    <span class="caption1 pl-1">Previous Product</span>
                </div>
                <div class="next-btn flex items-center cursor-pointer text-secondary hover:text-black">
                    <span class="caption1 pr-1">Next Product</span>
                    <i class="ph ph-caret-circle-right text-2xl text-black"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="product-detail default">
    <div class="featured-product underwear filter-product-img md:py-20 py-14">
        <div class="container flex justify-between gap-y-6 flex-wrap">
            <div class="list-img md:w-1/2 md:pr-[45px] w-full flex-shrink-0">
                <div class="sticky">
                    <div class="swiper mySwiper2 rounded-2xl overflow-hidden">
                        <div class="swiper-wrapper">
                            <div class="h-[100px] bg-gray-300 rounded animate-pulse" style="height: 500px; width: 600px"></div>
                        </div>
                    </div>
                    <div class="swiper mySwiper mt-5 select-none">
                        <div class="swiper-wrapper">
                            <!--  -->
                        </div>
                    </div>
                </div>
                <div class="swiper popup-img">
                    <span class="close-popup-btn absolute top-4 right-4 z-[2]">
                        <i class="ph ph-x text-3xl text-white"></i>
                    </span>
                    <div class="swiper-wrapper">
                        <!--  -->
                    </div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>
            <div class="product-item productDetails product-infor md:w-1/2 w-full lg:pl-[15px] md:pl-2" data-item="<?= $product_id ?>" style="cursor: inherit !important;">
                <div class="flex justify-between">
                    <div>
                        <div class="product-category hidden caption2 text-secondary font-semibold uppercase"></div>
                        <div class="product-name heading4 mt-1">
                            <div class="h-4 w-[320px] bg-gray-300 rounded animate-pulse"></div>
                        </div>
                    </div>
                    <div class="add-wishlist-btn w-10 h-10 flex-shrink-0 flex items-center justify-center border border-line cursor-pointer rounded-lg duration-300 hover:bg-black hover:text-white">
                        <i class="ph ph-heart text-xl"></i>
                    </div>
                </div>
                <div class="flex items-center gap-1 mt-3 <?php echo ($total_reviews == 0) ? 'hidden' : ''; ?>" id="headerRatingBlock">
                    <div class="rate flex text-yellow">
                        <?php
                        $full_stars = floor($avg_rating);
                        $has_half = ($avg_rating - $full_stars) >= 0.5;
                        for ($s = 1; $s <= 5; $s++) {
                            if ($s <= $full_stars) {
                                echo '<i class="ph-fill ph-star text-sm text-yellow"></i>';
                            } else if ($s == $full_stars + 1 && $has_half) {
                                echo '<i class="ph-fill ph-star-half text-sm text-yellow"></i>';
                            } else {
                                echo '<i class="ph ph-star text-sm text-secondary2"></i>';
                            }
                        }
                        ?>
                    </div>
                    <span class="caption1 text-secondary">(<?= $avg_rating ?> / 5 from <?= $total_reviews ?> reviews)</span>
                </div>
                <div class="flex items-center gap-3 flex-wrap mt-5 pb-6 border-b border-line">
                    <div class="product-price heading5" style="color: #7fa041;
    font-size: 20px;">
                        <div class="h-4 w-[100px] bg-gray-300 rounded animate-pulse"></div>
                    </div>
                    <div class="w-px h-4 bg-line"></div>
                    <div class="product-origin-price font-normal text-secondary2">
                        <div class="h-4 w-[100px] bg-gray-300 rounded animate-pulse"></div>
                    </div>
                    <div class="product-sale caption2 font-semibold bg-green px-3 py-0.5 inline-block rounded-full">
                        <div class="h-4 w-[100px] bg-gray-300 rounded animate-pulse"></div>
                    </div>
                    <div class="product-description text-secondary mt-3"></div>
                </div>
                <div class="list-action mt-6 quick-shop-block">

                    <div class=" choose-size mt-5">
                        <div class="heading flex items-center justify-between">
                            <div class="text-title">Quantity: <span class="text-title size"></span></div>
                        </div>
                        <div class="flex gap-2">

                            <div class="quantity-block md:p-3 max-md:py-1.5 max-md:px-3 flex items-center justify-between rounded-lg border border-line sm:w-[140px] w-[120px] flex-shrink-0" product_id="<?= $product_id ?>">
                                <i class="ph-bold ph-minus cursor-pointer body1"></i>
                                <div id="quantityINput<?= $product_id ?>" class="quantity body1 font-semibold">1</div>
                                <i class="ph-bold ph-plus cursor-pointer body1"></i>
                            </div>

                        </div>
                        <div class="list-size flex items-center gap-2 flex-wrap mt-3 hidden">
                            <!-- size-item -->
                        </div>
                    </div>

                    <!-- Variant Selection Section -->
                    <div class="choose-variant mt-5" id="variant-section" style="display: none;">
                        <div class="heading flex items-center justify-between mb-3">
                            <div class="text-title">Select Options:</div>
                        </div>

                        <!-- Size Selection -->
                        <div class="size-selection" style="display: none;">
                            <div class="text-secondary mb-2 text-sm">Size:</div>
                            <div class="list-size flex items-center gap-2 flex-wrap mb-4" id="size-list">
                                <!-- Sizes will be populated by JavaScript -->
                            </div>
                        </div>

                        <!-- Color Selection -->
                        <div class="color-selection" style="display: none;">
                            <div class="text-secondary mb-2 text-sm">Color:</div>
                            <div class="list-color flex items-center gap-2 flex-wrap mb-4" id="color-list">
                                <!-- Colors will be populated by JavaScript -->
                            </div>
                        </div>

                        <!-- Selected Variant Info -->
                        <div class="selected-variant-info p-4 mt-3 bg-surface rounded-lg border border-line" id="variant-info" style="display: none;">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-secondary text-sm">Selected:</span>
                                    <span id="selected-variant-text" class="font-semibold ml-2"></span>
                                </div>
                                <div class="product-price heading6" id="variant-price">₹0.00</div>
                            </div>
                            <div class="text-sm mt-2" id="variant-stock"></div>
                        </div>
                    </div>

                    <div class="choose-quantity flex items-center lg:justify-between gap-5 mt-3">

                        <div class="add-cart-btn button-main whitespace-nowrap w-full text-center bg-white text-black border border-black cursor-pointer">Add To Cart</div>
                        <div class="buy-now-btn button-main whitespace-nowrap w-full text-center cursor-pointer">Buy Now</div>
                    </div>
                    <div class="more-infor mt-6">
                        <!-- <div class="flex items-center gap-1 mt-3">
                            <i class="ph ph-timer body1"></i>
                            <div class="text-title">Estimated Delivery:</div>
                            <div class="text-secondary">Within 4 hours</div>
                        </div> -->
                        <div class="flex items-center gap-1 mt-3 hidden">
                            <div class="text-title">SKU:</div>
                            <div class="text-secondary">53453412</div>
                        </div>
                        <div class="flex items-center gap-1 mt-3 hidden">
                            <div class="text-title">Categories:</div>
                            <div class="list-category text-secondary">fashion, women</div>
                        </div>
                        <div class="flex items-center gap-1 mt-3">
                            <div class="text-title">Categories:</div>
                            <div class="list-tag text-secondary">top</div>
                        </div>
                    </div>
                </div>
                <div class="get-it mt-6 pb-8 border-b border-line">
                    <div class="heading5">Get it today</div>
                    <div class="item flex items-center gap-3 mt-4">
                        <div class="icon-delivery-truck text-4xl"></div>
                        <div>
                            <div class="text-title">Free shipping</div>
                            <div class="caption1 text-secondary mt-1">Free shipping on orders over ₹<?= (int) ($this->deliveryCharges['min_amount'] ?? 999) ?>.</div>
                        </div>
                    </div>
                    <div class="item flex items-center gap-3 mt-4">
                        <div class="icon-phone-call text-4xl"></div>
                        <div>
                            <div class="text-title">Support hours</div>
                            <div class="caption1 text-secondary mt-1">Support from 9:00 AM to 6:00 PM, Monday - Sunday</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="desc-tab md:pb-20 pb-10">
        <div class="container">
            <div class="flex items-center justify-center w-full">
                <div class="menu-tab flex items-center md:gap-[60px] gap-8">
                    <div class="tab-item heading5 has-line-before text-secondary2 hover:text-black duration-300 active">Description</div>
                    <div class="tab-item heading5 has-line-before text-secondary2 hover:text-black duration-300">Reviews</div>
                </div>
            </div>
            <div class="desc-block mt-8">
                <div class="desc-item description active" data-item="Description">
                    <div class="grid md:grid-cols-1 gap-8 gap-y-5">
                        <div class="left">
                            <div class="heading6">Description</div>
                            <div class="text-secondary mt-2 productDesc">
                                Keep your home organized, yet elegant with storage cabinets by Onita Patio Furniture. These cabinets not only make a great storage units, but also bring a great decorative accent to your decor. Traditionally designed, they are perfect to be used in the hallway, living room, bedroom, office or any place where you need to store or display things. Made of high quality materials, they are sturdy and durable for years. Bring one-of-a-kind look to your interior
                                with furniture from Onita Furniture!
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reviews Tab Content -->
                <div class="desc-item reviews" data-item="Reviews">
                    <!-- Reviews summary dashboard -->
                    <div class="grid md:grid-cols-3 gap-8 pb-10 border-b border-line">
                        <!-- Left: Rating Score -->
                        <div class="flex flex-col items-center justify-center text-center p-6 bg-surface rounded-2xl border border-line">
                            <div class="text-5xl font-bold text-black mb-2"><?= $avg_rating ?></div>
                            <div class="rate flex text-yellow mb-2">
                                <?php
                                $full_stars = floor($avg_rating);
                                $has_half = ($avg_rating - $full_stars) >= 0.5;
                                for ($s = 1; $s <= 5; $s++) {
                                    if ($s <= $full_stars) {
                                        echo '<i class="ph-fill ph-star text-2xl text-yellow"></i>';
                                    } else if ($s == $full_stars + 1 && $has_half) {
                                        echo '<i class="ph-fill ph-star-half text-2xl text-yellow"></i>';
                                    } else {
                                        echo '<i class="ph ph-star text-2xl text-secondary2"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <div class="text-secondary text-sm font-medium">Based on <?= $total_reviews ?> reviews</div>
                        </div>

                        <!-- Middle: Rating distribution bars -->
                        <div class="p-4 rounded-2xl border border-line bg-surface flex flex-col justify-center">
                            <?php
                            for ($star = 5; $star >= 1; $star--) {
                                $count = isset($rating_counts[$star]) ? $rating_counts[$star] : 0;
                                $percentage = $total_reviews > 0 ? round(($count / $total_reviews) * 100) : 0;
                            ?>
                            <div class="flex items-center gap-3 mt-1.5 first:mt-0">
                                <span class="text-sm font-semibold w-3 text-black"><?= $star ?></span>
                                <i class="ph-fill ph-star text-sm text-yellow"></i>
                                <div class="flex-1 bg-white h-2 rounded-full overflow-hidden border border-line">
                                    <div class="h-full rounded-full" style="width: <?= $percentage ?>%; background-color: #7fa041;"></div>
                                </div>
                                <span class="text-xs text-secondary2 w-8 text-right"><?= $count ?></span>
                            </div>
                            <?php } ?>
                        </div>

                        <!-- Right: Action Button -->
                        <div class="flex flex-col items-center justify-center text-center p-6 bg-surface rounded-2xl border border-line">
                            <?php if (!$userId) : ?>
                                <i class="ph ph-lock-key text-4xl text-secondary2 mb-2"></i>
                                <div class="text-sm text-secondary font-medium mb-3">Please log in to write a review.</div>
                                <a href="<?= base_url('login') ?>" class="button-main bg-black text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-opacity-80 transition duration-300">Login Now</a>
                            <?php elseif (!$hasPurchased) : ?>
                                <i class="ph ph-shield-warning text-4xl text-secondary2 mb-2"></i>
                                <div class="text-sm text-secondary font-medium px-4">Only verified purchasers of this product can write a review.</div>
                            <?php elseif ($hasReviewed) : ?>
                                <i class="ph ph-check-circle text-4xl text-green mb-2" style="color: #7fa041;"></i>
                                <div class="text-sm text-secondary font-medium">You have already submitted a review for this product. Thank you!</div>
                            <?php else : ?>
                                <i class="ph ph-note-pencil text-4xl text-secondary2 mb-2"></i>
                                <div class="text-sm text-secondary font-medium mb-3">Share your thoughts with other customers</div>
                                <button type="button" onclick="document.getElementById('writeReviewBlock').scrollIntoView({behavior: 'smooth'});" class="button-main bg-black text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-opacity-80 transition duration-300">Write a Review</button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Reviews List -->
                    <div class="reviews-list-container py-10">
                        <h4 class="heading5 text-black mb-6">Customer Reviews (<?= $total_reviews ?>)</h4>
                        <?php if (empty($reviews)) : ?>
                            <div class="text-center py-10 bg-surface rounded-2xl border border-line">
                                <i class="ph ph-chat-circle-dots text-5xl text-secondary2 mb-3 block"></i>
                                <p class="text-secondary font-medium">No reviews yet. Be the first to share your experience!</p>
                            </div>
                        <?php else : ?>
                            <div class="flex flex-col gap-6">
                                <?php foreach ($reviews as $rev) : ?>
                                    <div class="p-6 bg-white rounded-2xl border border-line flex flex-col md:flex-row gap-4 justify-between transition hover:shadow-sm">
                                        <div class="flex gap-4">
                                            <!-- Avatar -->
                                            <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 flex-shrink-0 border border-line flex items-center justify-center text-lg font-bold text-secondary">
                                                <?php if ($rev['profile_image']) : ?>
                                                    <img src="<?= base_url('upload/profile_image/') . $rev['profile_image'] ?>" alt="user" class="w-full h-full object-cover">
                                                <?php else : ?>
                                                    <?= strtoupper(substr($rev['user_name'], 0, 1)) ?>
                                                <?php endif; ?>
                                            </div>

                                            <div>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="font-bold text-black text-sm"><?= htmlspecialchars($rev['user_name']) ?></span>
                                                    <span class="inline-flex items-center gap-1 text-xs text-green font-semibold bg-green-50 px-2 py-0.5 rounded-full" style="color: #7fa041; background-color: #f1f8e9;">
                                                        <i class="ph-fill ph-check-circle"></i> Verified Buyer
                                                    </span>
                                                    <span class="text-xs text-secondary2"><?= dateConvertToView($rev['create_date'], 1) ?></span>
                                                </div>

                                                <!-- Review Rating Stars -->
                                                <div class="rate flex text-yellow mt-1">
                                                    <?php
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        if ($i <= $rev['rating']) {
                                                            echo '<i class="ph-fill ph-star text-xs text-yellow"></i>';
                                                        } else {
                                                            echo '<i class="ph ph-star text-xs text-secondary2"></i>';
                                                        }
                                                    }
                                                    ?>
                                                </div>

                                                <?php if ($rev['review_title']) : ?>
                                                    <h5 class="font-bold text-black text-sm mt-3"><?= htmlspecialchars($rev['review_title']) ?></h5>
                                                <?php endif; ?>
                                                
                                                <p class="text-secondary text-sm mt-1.5 leading-relaxed"><?= nl2br(htmlspecialchars($rev['review_text'])) ?></p>

                                                <!-- Review Images -->
                                                <?php 
                                                $rev_imgs = $this->CommonModel->getRowById('product_review_images', 'review_id', $rev['id']);
                                                if (!empty($rev_imgs)) :
                                                ?>
                                                    <div class="flex flex-wrap gap-2 mt-4">
                                                        <?php foreach ($rev_imgs as $img_row) : ?>
                                                            <div class="w-16 h-16 rounded-lg overflow-hidden border border-line cursor-pointer hover:opacity-80 transition duration-200" onclick="openReviewImageModal('<?= base_url(REVIEW_IMAGE) . $img_row['image_path'] ?>')">
                                                                <img src="<?= base_url(REVIEW_IMAGE) . $img_row['image_path'] ?>" alt="review" class="w-full h-full object-cover">
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Write a Review Form -->
                    <?php if ($userId && $hasPurchased && !$hasReviewed) : ?>
                        <div id="writeReviewBlock" class="p-8 bg-surface rounded-2xl border border-line mt-6 scroll-mt-24">
                            <h4 class="heading5 text-black mb-2">Write a Customer Review</h4>
                            <p class="text-sm text-secondary2 mb-6">Your feedback helps other buyers make better choices. Upload up to 5 photos to showcase the product.</p>
                            
                            <form id="submitReviewForm" class="flex flex-col gap-5" enctype="multipart/form-data">
                                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                
                                <!-- Rating Selector -->
                                <div>
                                    <label class="block text-sm font-semibold text-black mb-2">Overall Rating <span class="text-red">*</span></label>
                                    <div class="rating-stars flex gap-1 cursor-pointer">
                                        <i class="ph ph-star text-3xl text-secondary2 hover:text-yellow transition-colors duration-200" data-val="1"></i>
                                        <i class="ph ph-star text-3xl text-secondary2 hover:text-yellow transition-colors duration-200" data-val="2"></i>
                                        <i class="ph ph-star text-3xl text-secondary2 hover:text-yellow transition-colors duration-200" data-val="3"></i>
                                        <i class="ph ph-star text-3xl text-secondary2 hover:text-yellow transition-colors duration-200" data-val="4"></i>
                                        <i class="ph ph-star text-3xl text-secondary2 hover:text-yellow transition-colors duration-200" data-val="5"></i>
                                    </div>
                                    <input type="hidden" name="rating" id="reviewRatingInput" value="" required>
                                    <div class="error-msg text-red text-xs mt-1 hidden" id="ratingError">Please select a rating star.</div>
                                </div>

                                <!-- Review Title -->
                                <div>
                                    <label for="reviewTitle" class="block text-sm font-semibold text-black mb-2">Review Title (Optional)</label>
                                    <input type="text" id="reviewTitle" name="review_title" class="w-full bg-white border border-line rounded-lg p-2 text-sm text-black focus:outline-none focus:border-black transition" placeholder="e.g. Beautiful fabric, great quality!">
                                </div>

                                <!-- Review Description -->
                                <div>
                                    <label for="reviewText" class="block text-sm font-semibold text-black mb-2">Review Details <span class="text-red">*</span></label>
                                    <textarea id="reviewText" name="review_text" rows="5" class="w-full bg-white border border-line rounded-lg p-2 text-sm text-black focus:outline-none focus:border-black transition" placeholder="Write your review here. What did you like or dislike? How was the fit and quality?" required></textarea>
                                </div>

                                <!-- Image Upload Box -->
                                <div>
                                    <label class="block text-sm font-semibold text-black mb-2">Add Photos (Optional)</label>
                                    <div id="imageUploadContainer" class="border-2 border-dashed border-line bg-white rounded-xl p-6 text-center cursor-pointer hover:border-black transition duration-300">
                                        <i class="ph ph-image-square text-4xl text-secondary2 mb-2 block"></i>
                                        <span class="text-sm text-secondary2 font-medium">Click to upload photos or drag and drop</span>
                                        <span class="text-xs text-secondary2 block mt-1">(Up to 5 images, Max 2MB each)</span>
                                        <input type="file" id="reviewImages" name="review_images[]" class="hidden" multiple accept="image/*">
                                    </div>
                                    <div id="imagePreviews" class="flex flex-wrap gap-2 mt-4"></div>
                                </div>

                                <!-- Form Message / Notification -->
                                <div id="reviewFormMessage" class="hidden p-4 rounded-lg text-sm font-medium"></div>

                                <!-- Submit Button -->
                                <div class="mt-2">
                                    <button type="submit" id="submitReviewBtn" class="button-main bg-black text-white px-8 py-3 rounded-lg text-sm font-semibold hover:bg-opacity-80 transition duration-300 flex items-center justify-center gap-2">
                                        Submit Review
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>



<?php
include('includes/footer.php');
include('includes/footer-link.php');
?>

<script src="<?= base_url('assets/js/product-variants.js') ?>"></script>
<script>
    handleActiveSizeChange();
</script>

<!-- Review Image Modal Lightbox -->
<div id="reviewImageModal" class="fixed inset-0 bg-black bg-opacity-95 z-[9999] hidden flex items-center justify-center p-4 overflow-auto" onclick="closeReviewImageModal()">
    <span class="absolute top-6 right-6 text-white text-3xl cursor-pointer hover:opacity-80 z-[10000]"><i class="ph ph-x"></i></span>
    <div class="max-w-full max-h-full flex items-center justify-center p-4">
        <img id="reviewModalImg" src="" alt="Zoomed Review Photo" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl transition-transform duration-300 scale-95 cursor-zoom-in" onclick="toggleZoomModalImage(event)">
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .animate-fade-in {
        animation: fadeIn 0.25s ease-out forwards;
    }
    .rating-stars i.ph-fill {
        color: #FBC02D;
    }
    #headerRatingBlock i.ph-fill, .reviews-list-container i.ph-fill {
        color: #FBC02D;
    }
</style>

<script>
    // Lightbox modal logic
    function openReviewImageModal(src) {
        const modal = document.getElementById('reviewImageModal');
        const img = document.getElementById('reviewModalImg');
        img.src = src;
        img.classList.remove('scale-[1.8]');
        img.classList.remove('scale-100');
        img.classList.add('scale-95');
        img.style.cursor = 'zoom-in';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            img.classList.remove('scale-95');
            img.classList.add('scale-100');
        }, 10);
    }

    function toggleZoomModalImage(e) {
        e.stopPropagation();
        const img = document.getElementById('reviewModalImg');
        if (img.classList.contains('scale-[1.8]')) {
            img.classList.remove('scale-[1.8]');
            img.classList.add('scale-100');
            img.style.cursor = 'zoom-in';
        } else {
            img.classList.remove('scale-100');
            img.classList.add('scale-[1.8]');
            img.style.cursor = 'zoom-out';
        }
    }

    function closeReviewImageModal() {
        const modal = document.getElementById('reviewImageModal');
        const img = document.getElementById('reviewModalImg');
        img.classList.remove('scale-100');
        img.classList.remove('scale-[1.8]');
        img.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 200);
    }

    // Write review form logic
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.rating-stars i');
        const ratingInput = document.getElementById('reviewRatingInput');
        const ratingError = document.getElementById('ratingError');

        // Star selection behavior
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const val = this.getAttribute('data-val');
                ratingInput.value = val;
                ratingError.classList.add('hidden');
                
                // Highlight stars
                stars.forEach(s => {
                    const sVal = s.getAttribute('data-val');
                    if (sVal <= val) {
                        s.classList.remove('ph');
                        s.classList.add('ph-fill');
                    } else {
                        s.classList.remove('ph-fill');
                        s.classList.add('ph');
                    }
                });
            });

            // Hover behavior
            star.addEventListener('mouseenter', function() {
                const val = this.getAttribute('data-val');
                stars.forEach(s => {
                    if (s.getAttribute('data-val') <= val) {
                        s.style.color = '#FBC02D'; // Amber hover color
                    } else {
                        s.style.color = '';
                    }
                });
            });

            star.addEventListener('mouseleave', function() {
                const currentVal = ratingInput.value;
                stars.forEach(s => {
                    s.style.color = '';
                    const sVal = s.getAttribute('data-val');
                    if (currentVal && sVal <= currentVal) {
                        s.classList.remove('ph');
                        s.classList.add('ph-fill');
                    } else if (!currentVal) {
                        s.classList.remove('ph-fill');
                        s.classList.add('ph');
                    }
                });
            });
        });

        // Image upload and preview logic
        const uploadContainer = document.getElementById('imageUploadContainer');
        const fileInput = document.getElementById('reviewImages');
        const previewsContainer = document.getElementById('imagePreviews');
        let selectedFiles = [];

        if (uploadContainer) {
            uploadContainer.addEventListener('click', () => fileInput.click());

            // Drag and drop events
            ['dragenter', 'dragover'].forEach(eventName => {
                uploadContainer.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    uploadContainer.style.borderColor = '#000000';
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadContainer.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    uploadContainer.style.borderColor = '';
                }, false);
            });

            uploadContainer.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles(files);
            });

            fileInput.addEventListener('change', function() {
                handleFiles(this.files);
            });
        }

        function handleFiles(files) {
            const filesArr = Array.from(files);
            
            // Limit to 5 images total
            if (selectedFiles.length + filesArr.length > 5) {
                alert('You can only upload up to 5 images.');
                return;
            }

            filesArr.forEach(file => {
                // Validate file type
                if (!file.type.match('image.*')) {
                    alert('Please select image files only.');
                    return;
                }
                
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert(`File ${file.name} is too large. Max size is 2MB.`);
                    return;
                }

                selectedFiles.push(file);
                renderPreviews();
            });
            
            syncFilesToInput();
        }

        function renderPreviews() {
            previewsContainer.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative w-20 h-20 rounded-lg overflow-hidden border border-line';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover" />
                        <span class="absolute top-1 right-1 bg-black bg-opacity-70 text-white rounded-full w-5 h-5 flex items-center justify-center cursor-pointer text-xs hover:bg-opacity-95" data-index="${index}">&times;</span>
                    `;
                    previewsContainer.appendChild(div);

                    // Add remove listener
                    div.querySelector('span').addEventListener('click', function(e) {
                        e.stopPropagation();
                        const idx = parseInt(this.getAttribute('data-index'));
                        selectedFiles.splice(idx, 1);
                        renderPreviews();
                        syncFilesToInput();
                    });
                };
                reader.readAsDataURL(file);
            });
        }

        function syncFilesToInput() {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            fileInput.files = dataTransfer.files;
        }

        // Form submission via AJAX
        const form = document.getElementById('submitReviewForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!ratingInput.value) {
                    ratingError.classList.remove('hidden');
                    ratingError.scrollIntoView({behavior: 'smooth', block: 'center'});
                    return;
                }

                const formData = new FormData(this);
                
                // Clear file input and append files from our selectedFiles array
                formData.delete('review_images[]');
                selectedFiles.forEach(file => {
                    formData.append('review_images[]', file);
                });

                const btn = document.getElementById('submitReviewBtn');
                const msgBox = document.getElementById('reviewFormMessage');

                $.ajax({
                    type: 'POST',
                    url: '<?= base_url("submitReview") ?>',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        btn.disabled = true;
                        btn.innerHTML = 'Submitting...';
                        msgBox.classList.add('hidden');
                    },
                    success: function(res) {
                        if (res.success) {
                            msgBox.className = 'p-4 rounded-lg text-sm font-medium bg-green-50 text-green-800 border border-green-200 mt-4';
                            msgBox.innerHTML = res.message;
                            msgBox.classList.remove('hidden');
                            form.reset();
                            previewsContainer.innerHTML = '';
                            selectedFiles = [];
                            
                            msgBox.scrollIntoView({behavior: 'smooth', block: 'center'});
                            
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            btn.disabled = false;
                            btn.innerHTML = 'Submit Review';
                            msgBox.className = 'p-4 rounded-lg text-sm font-medium bg-red-50 text-red-800 border border-red-200 mt-4';
                            
                            if (res.validation === false) {
                                let errMsg = 'Validation errors:<br>';
                                Object.keys(res.message).forEach(key => {
                                    errMsg += `- ${res.message[key]}<br>`;
                                });
                                msgBox.innerHTML = errMsg;
                            } else {
                                msgBox.innerHTML = res.message;
                            }
                            msgBox.classList.remove('hidden');
                            msgBox.scrollIntoView({behavior: 'smooth', block: 'center'});
                        }
                    },
                    error: function() {
                        btn.disabled = false;
                        btn.innerHTML = 'Submit Review';
                        msgBox.className = 'p-4 rounded-lg text-sm font-medium bg-red-50 text-red-800 border border-red-200 mt-4';
                        msgBox.innerHTML = 'Something went wrong. Please try again later.';
                        msgBox.classList.remove('hidden');
                        msgBox.scrollIntoView({behavior: 'smooth', block: 'center'});
                    }
                });
            });
        }

        // Product image zoom magnifier lens effect
        const zoomContainer = document.querySelector('.mySwiper2');
        if (zoomContainer) {
            const lens = document.createElement('div');
            lens.id = 'product-image-zoom-lens';
            lens.style.position = 'absolute';
            lens.style.border = '2px solid #fff';
            lens.style.boxShadow = '0 0 12px rgba(0,0,0,0.3)';
            lens.style.borderRadius = '50%';
            lens.style.width = '180px';
            lens.style.height = '180px';
            lens.style.pointerEvents = 'none';
            lens.style.display = 'none';
            lens.style.zIndex = '99';
            lens.style.backgroundRepeat = 'no-repeat';
            
            zoomContainer.style.position = 'relative';
            zoomContainer.appendChild(lens);
            
            const zoomFactor = 2.5;
            
            zoomContainer.addEventListener('mouseenter', function() {
                const activeSlide = zoomContainer.querySelector('.swiper-slide-active');
                if (!activeSlide) return;
                const img = activeSlide.querySelector('img');
                if (!img) return;
                
                lens.style.backgroundImage = `url('${img.src}')`;
                lens.style.display = 'block';
            });
            
            zoomContainer.addEventListener('mousemove', function(e) {
                const activeSlide = zoomContainer.querySelector('.swiper-slide-active');
                if (!activeSlide) return;
                const img = activeSlide.querySelector('img');
                if (!img) return;
                
                lens.style.backgroundImage = `url('${img.src}')`;
                
                const rect = img.getBoundingClientRect();
                const containerRect = zoomContainer.getBoundingClientRect();
                
                let x = e.clientX - rect.left;
                let y = e.clientY - rect.top;
                
                // Prevent lens from going outside the image boundaries
                if (x < 0) x = 0;
                if (x > rect.width) x = rect.width;
                if (y < 0) y = 0;
                if (y > rect.height) y = rect.height;
                
                const lensX = e.clientX - containerRect.left - (lens.offsetWidth / 2);
                const lensY = e.clientY - containerRect.top - (lens.offsetHeight / 2);
                
                lens.style.left = lensX + 'px';
                lens.style.top = lensY + 'px';
                
                const bgWidth = rect.width * zoomFactor;
                const bgHeight = rect.height * zoomFactor;
                
                lens.style.backgroundSize = `${bgWidth}px ${bgHeight}px`;
                
                const posX = -(x * zoomFactor - lens.offsetWidth / 2);
                const posY = -(y * zoomFactor - lens.offsetHeight / 2);
                
                lens.style.backgroundPosition = `${posX}px ${posY}px`;
            });
            
            zoomContainer.addEventListener('mouseleave', function() {
                lens.style.display = 'none';
            });
        }
    });
</script>