<?php
include('includes/header-link.php');
include('includes/header.php');
?>
<style>
    .height-banner {
        width: 100%;
        aspect-ratio: 1200 / 480;
        height: auto !important;
    }

    /* Fallback for older browsers */
    @supports not (aspect-ratio: 1200 / 480) {
        .height-banner {
            height: 40vw !important;
        }
    }

    .video-section {
        background-color: #f8f9fa;
        padding: 5rem 0;
    }

    .video-card {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: transform 0.4s ease, box-shadow 0.4s ease;
        position: relative;
    }

    .video-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    }

    .video-container {
        position: relative;
        width: 100%;
        aspect-ratio: 16/9;
        background: #000;
        overflow: hidden;
    }

    .video-container video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0;
        transition: opacity 1s ease;
    }

    .video-container video.loaded {
        opacity: 1;
    }

    .video-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 1.5rem;
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
        color: white;
        z-index: 2;
    }

    .video-badge {
        position: absolute;
        top: 1rem;
        left: 1rem;
        background: rgba(255, 255, 255, 0.9);
        color: #000;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        z-index: 2;
        letter-spacing: 0.05em;
    }
</style>

<!-- Slider -->
<div class="slider-block style-two bg-linear height-banner w-full">
    <div class="slider-main h-full w-full">
        <div class="swiper swiper-slider h-full relative">
            <div class="swiper-wrapper">
                <?php if (!empty($banners)): ?>
                    <?php foreach ($banners as $banner): ?>
                        <div class="swiper-slide">
                            <a href="<?= BASE_URL . 'products' ?>" class="slider-item h-full w-full relative overflow-hidden">
                                <div class="container w-full h-full flex items-center">
                                    <div class="sub-img absolute left-0 top-0 w-full h-full z-[-1]">
                                        <img src="<?= base_url('upload/banner/') . $banner['image_path'] ?>" alt="banner" class="w-full h-full object-cover" />
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="swiper-slide">
                        <a href="<?= BASE_URL . 'products' ?>" class="slider-item h-full w-full relative overflow-hidden">
                            <div class="container w-full h-full flex items-center">
                                <div class="sub-img absolute left-0 top-0 w-full h-full z-[-1]">
                                    <img src="<?= BASE_URL ?>assets/img/banners/1.webp" alt="banner" class="w-full h-full object-cover" />
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="<?= BASE_URL . 'products' ?>" class="slider-item h-full w-full relative overflow-hidden">
                            <div class="container w-full h-full flex items-center">
                                <div class="sub-img absolute left-0 top-0 w-full h-full z-[-1]">
                                    <img src="<?= BASE_URL ?>assets/img/banners/2.webp" alt="banner" class="w-full h-full object-cover" />
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="<?= BASE_URL . 'products' ?>" class="slider-item h-full w-full relative overflow-hidden">
                            <div class="container w-full h-full flex items-center">
                                <div class="sub-img absolute left-0 top-0 w-full h-full z-[-1]">
                                    <img src="<?= BASE_URL ?>assets/img/banners/3.webp" alt="banner" class="w-full h-full object-cover" />
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <!-- <div class="swiper-pagination"></div> -->
        </div>
    </div>
</div>
</div>

<?php if ($categories) { ?>
    <div class="trending-block style-six md:pt-20 pt-10">
        <div class="container">
            <div class="heading3 text-center">Top Categories</div>
            <div class="list-trending relative section-swiper-navigation style-small-border style-outline md:mt-10 mt-6">
                <div class="swiper-button-prev"></div>
                <div class="swiper swiper-list-trending h-full relative">
                    <div class="swiper-wrapper">
                        <?php foreach ($categories as $cate) { ?>
                            <div class="swiper-slide">
                                <a href="<?= BASE_URL . 'products/' . url_title($cate['category_name'], '-', true) ?>" class="trending-item block relative cursor-pointer">
                                    <div class="bg-img rounded-2xl overflow-hidden aspect-[3/4]">
                                        <img src="<?= setImage($cate['image'], CATEGORY_IMAGE) ?>" alt="outerwear" class="w-full h-full object-contain" />
                                    </div>
                                    <div class="trending-name text-center mt-5 duration-500">
                                        <span class="heading5"><?= $cate['category_name'] ?></span>
                                        <span class="text-secondary"></span>
                                    </div>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    </div>
<?php } ?>


<div class="what-new-block filter-product-block md:pt-20 pt-10">
    <div class="container">
        <div class="heading flex flex-col items-center text-center">
            <div class="heading3">Popular products</div>
            <div class="menu-tab bg-surface rounded-2xl mt-6">
                <div class="menu flex items-center gap-2 p-1">
                    <div class="indicator absolute top-1 bottom-1 bg-white rounded-full shadow-md duration-300"></div>
                    <?php $i = 0;
                    foreach ($featuredCategories as $fcate) {
                        $i++;
                        $fcateId = encryptId($fcate['category_id']);
                    ?>
                        <div class="tab-item relative text-secondary text-button-uppercase py-2 px-5 cursor-pointer duration-300 hover:text-black <?= $i == '1' ? 'active' : '' ?>" data-item="<?= url_title($fcate['category_name'], '-', true) ?>"><?= $fcate['category_name'] ?></div>
                    <?php  } ?>
                </div>
            </div>
        </div>
        <div class="list-product four-product hide-product-sold grid xl:grid-cols-5 sm:grid-cols-3 grid-cols-2 md:gap-[30px] gap-4 md:mt-10 mt-6">
            <div class="skeleton-item product-main cursor-pointer block animate-pulse bg-white rounded-2xl overflow-hidden">
                <div class="relative">
                    <div class="absolute top-3 left-3 h-5 w-14 bg-gray-300 rounded-full"></div>
                    <div class="aspect-[3/4] bg-gray-200 w-full"></div>
                </div>
                <div class="py-4 space-y-3">
                    <div class="h-4 w-3/4 bg-gray-300 rounded"></div>
                    <div class="h-4 w-1/2 bg-gray-200 rounded mt-2"></div>
                    <div class="flex items-center gap-2 mt-3">
                        <div class="h-5 w-16 bg-gray-300 rounded"></div>
                        <div class="h-5 w-10 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
            <div class="skeleton-item product-main cursor-pointer block animate-pulse bg-white rounded-2xl overflow-hidden">
                <div class="relative">
                    <div class="absolute top-3 left-3 h-5 w-14 bg-gray-300 rounded-full"></div>
                    <div class="aspect-[3/4] bg-gray-200 w-full"></div>
                </div>
                <div class="py-4 space-y-3">
                    <div class="h-4 w-3/4 bg-gray-300 rounded"></div>
                    <div class="h-4 w-1/2 bg-gray-200 rounded mt-2"></div>
                    <div class="flex items-center gap-2 mt-3">
                        <div class="h-5 w-16 bg-gray-300 rounded"></div>
                        <div class="h-5 w-10 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
            <div class="skeleton-item product-main cursor-pointer block animate-pulse bg-white rounded-2xl overflow-hidden">
                <div class="relative">
                    <div class="absolute top-3 left-3 h-5 w-14 bg-gray-300 rounded-full"></div>
                    <div class="aspect-[3/4] bg-gray-200 w-full"></div>
                </div>
                <div class="py-4 space-y-3">
                    <div class="h-4 w-3/4 bg-gray-300 rounded"></div>
                    <div class="h-4 w-1/2 bg-gray-200 rounded mt-2"></div>
                    <div class="flex items-center gap-2 mt-3">
                        <div class="h-5 w-16 bg-gray-300 rounded"></div>
                        <div class="h-5 w-10 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
            <div class="skeleton-item product-main cursor-pointer block animate-pulse bg-white rounded-2xl overflow-hidden">
                <div class="relative">
                    <div class="absolute top-3 left-3 h-5 w-14 bg-gray-300 rounded-full"></div>
                    <div class="aspect-[3/4] bg-gray-200 w-full"></div>
                </div>
                <div class="py-4 space-y-3">
                    <div class="h-4 w-3/4 bg-gray-300 rounded"></div>
                    <div class="h-4 w-1/2 bg-gray-200 rounded mt-2"></div>
                    <div class="flex items-center gap-2 mt-3">
                        <div class="h-5 w-16 bg-gray-300 rounded"></div>
                        <div class="h-5 w-10 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
            <div class="skeleton-item product-main cursor-pointer block animate-pulse bg-white rounded-2xl overflow-hidden">
                <div class="relative">
                    <div class="absolute top-3 left-3 h-5 w-14 bg-gray-300 rounded-full"></div>
                    <div class="aspect-[3/4] bg-gray-200 w-full"></div>
                </div>
                <div class="py-4 space-y-3">
                    <div class="h-4 w-3/4 bg-gray-300 rounded"></div>
                    <div class="h-4 w-1/2 bg-gray-200 rounded mt-2"></div>
                    <div class="flex items-center gap-2 mt-3">
                        <div class="h-5 w-16 bg-gray-300 rounded"></div>
                        <div class="h-5 w-10 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Craftsmanship in Motion Section -->
<div class="video-section md:mt-20 mt-10">
    <div class="container">
        <div class="heading text-center mb-10">
            <h2 class="heading3">The Art of Creation</h2>
            <p class="text-secondary mt-3">Witness the journey of excellence, from our workshop to your doorstep.</p>
        </div>
        <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-8 mt-10">
            <!-- Video 1 -->
            <div class="video-card group">
                <div class="video-badge">Handcrafting</div>
                <div class="video-container">
                    <video class="lazy-video" data-src="<?= base_url() ?>assets/one.mp4" muted loop playsinline></video>
                </div>
                <div class="video-overlay">
                    <h4 class="heading6 mb-1">Authentic Artistry</h4>
                    <p class="caption2 opacity-80">Preserving tradition through meticulous hand-carved details.</p>
                </div>
            </div>

            <!-- Video 2 -->
            <div class="video-card group">
                <div class="video-badge">Engineering</div>
                <div class="video-container">
                    <video class="lazy-video" data-src="<?= base_url() ?>assets/two.mp4" muted loop playsinline></video>
                </div>
                <div class="video-overlay">
                    <h4 class="heading6 mb-1">Modern Precision</h4>
                    <p class="caption2 opacity-80">Blending ancient wisdom with cutting-edge production technology.</p>
                </div>
            </div>

            <!-- Video 3 -->
            <div class="video-card group">
                <div class="video-badge">Quality</div>
                <div class="video-container">
                    <video class="lazy-video" data-src="<?= base_url() ?>assets/three.mp4" muted loop playsinline></video>
                </div>
                <div class="video-overlay">
                    <h4 class="heading6 mb-1">Final Perfection</h4>
                    <p class="caption2 opacity-80">Every piece undergoes rigorous inspection for timeless durability.</p>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="buy-pack-block md:py-20 py-10 md:mt-20 mt-10 relative hidden">
    <div class="bg-img absolute top-0 left-0 w-full h-full z-[-1]">
        <img src="assets/images/banner/bg-buy-pack-organic.png" alt="bg-img" class="w-full h-full object-cover" />
    </div>
    <div class="container">
        <div class="heading">
            <div class="heading3 text-center text-white">Vegetables packs</div>
            <div class="block text-center text-white mt-3">Sign up for early sale access, new in, promotions and more</div>
        </div>
        <div class="grid sm:grid-cols-2 max-sm:flex max-sm:w-full flex-col max-sm:flex-col-reverse items-center md:mt-10 mt-6">
            <!-- <div class="main-content text-white lg:pr-[84px] sm:pr-12 w-full">
                <div class="list-product md:mt-10 mt-6">
                    <div class="product-item pb-5 border-b border-line" data-item="123">
                        <div class="product-main flex items-center justify-between cursor-pointer">
                            <div class="left flex items-center gap-7">
                                <img src="assets/images/product/organic/1-1.png" alt="1-1" class="w-20 rounded-lg overflow-hidden" />
                                <div class="infor">
                                    <div class="product-name text-title">green cabbage</div>
                                    <div class="caption2 product-brand text-secondary2 uppercase mt-1">Glurmarket</div>
                                </div>
                            </div>
                            <div class="right">
                                <div class="text-title">$<span class="product-price">4</span>,000/Kg</div>
                            </div>
                        </div>
                    </div>
                    <div class="product-item pb-5 border-b border-line mt-5" data-item="124">
                        <div class="product-main flex items-center justify-between cursor-pointer">
                            <div class="left flex items-center gap-7">
                                <img src="assets/images/product/organic/2-1.png" alt="1-2" class="w-20 rounded-lg overflow-hidden" />
                                <div class="infor">
                                    <div class="product-name text-title">purple cabbage</div>
                                    <div class="caption2 product-brand text-secondary2 uppercase mt-1">Glurmarket</div>
                                </div>
                            </div>
                            <div class="right">
                                <div class="text-title">$<span class="product-price">3</span>,000/Kg</div>
                            </div>
                        </div>
                    </div>
                    <div class="product-item pb-5 border-b border-line mt-5" data-item="125">
                        <div class="product-main flex items-center justify-between cursor-pointer">
                            <div class="left flex items-center gap-7">
                                <img src="assets/images/product/organic/3-1.png" alt="1-3" class="w-20 rounded-lg overflow-hidden" />
                                <div class="infor">
                                    <div class="product-name text-title">Tinted Moisturiser</div>
                                    <div class="caption2 product-brand text-secondary2 uppercase mt-1">Glurmarket</div>
                                </div>
                            </div>
                            <div class="right">
                                <div class="text-title">$<span class="product-price">4</span>,000/Kg</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="block-button mt-8">
                    <div class="button-main add-cart-btn bg-green text-black w-full text-center" onClick="{handleAddToCart}">11$ / add set to cart</div>
                </div>
            </div> -->
            <div class="popular-product sm:pl-4 max-sm:pb-6">
                <div class="item relative">
                    <img src="<?= base_url() ?>assets/img/bg-pack-organic.jpg" alt="./assets/images/bg-pack-organic.png" class="w-full object-cover" />
                    <div class="dots absolute top-[15%] left-[35%] cursor-pointer">
                        <div class="top-dot w-8 h-8 rounded-full bg-outline flex items-center justify-center">
                            <span class="bg-white w-3 h-3 rounded-full duration-300"></span>
                        </div>
                        <div class=" product-infor bg-white rounded-2xl p-4 cursor-pointer">
                            <div class="text-title name">green cabbage</div>
                            <div class="price text-center">$4.00</div>
                            <div class="text-center underline mt-1 text-button-uppercase duration-300 text-secondary2 hover:text-black">View</div>
                        </div>
                    </div>
                    <div class="dots absolute top-[40%] left-[15%] cursor-pointer">
                        <div class="top-dot w-8 h-8 rounded-full bg-outline flex items-center justify-center">
                            <span class="bg-white w-3 h-3 rounded-full duration-300"></span>
                        </div>
                        <div class=" product-infor bg-white rounded-2xl p-4 cursor-pointer">
                            <div class="text-title name">purple cabbage</div>
                            <div class="price text-center">$3.00</div>
                            <div class="text-center underline mt-1 text-button-uppercase duration-300 text-secondary2 hover:text-black">View</div>
                        </div>
                    </div>
                    <div class="dots bottom-dot absolute bottom-[25%] left-[62%] cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-outline flex items-center justify-center">
                            <span class="bg-white w-3 h-3 rounded-full duration-300"></span>
                        </div>
                        <div class=" product-infor bg-white rounded-2xl p-4 cursor-pointer">
                            <div class="text-title name">Tinted Moisturiser</div>
                            <div class="price text-center">$4.00</div>
                            <div class="text-center underline mt-1 text-button-uppercase duration-300 text-secondary2 hover:text-black">View</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container">
    <div class="benefit-block md:py-20 py-10">
        <div class="list-benefit grid items-start lg:grid-cols-2 grid-cols-2 gap-[30px]">
            <!--<div class="benefit-item flex flex-col items-center justify-center">-->
            <!--    <i class="icon-phone-call lg:text-7xl text-5xl"></i>-->
            <!--    <div class="heading6 text-center mt-5">24/7 Customer Service</div>-->
            <!--    <div class="caption1 text-secondary text-center mt-3">We're here to help you with any questions or concerns you have, 24/7.</div>-->
            <!--</div>-->
            <!-- <div class="benefit-item flex flex-col items-center justify-center">
                <i class="icon-return lg:text-7xl text-5xl"></i>
                <div class="heading6 text-center mt-5">14-Day Money Back</div>
                <div class="caption1 text-secondary text-center mt-3">If you're not satisfied with your purchase, simply return it within 14 days for a refund.</div>
            </div> -->
            <div class="benefit-item flex flex-col items-center justify-center">
                <i class="icon-guarantee lg:text-7xl text-5xl"></i>
                <div class="heading6 text-center mt-5">Our Guarantee</div>
                <div class="caption1 text-secondary text-center mt-3">We stand behind our products and services and guarantee your satisfaction.</div>
            </div>
            <div class="benefit-item flex flex-col items-center justify-center">
                <i class="icon-delivery-truck lg:text-7xl text-5xl"></i>
                <div class="heading6 text-center mt-5">Shipping Worldwide</div>
                <div class="caption1 text-secondary text-center mt-3">We ship only worldwide, bringing our products directly to you!</div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const lazyVideos = document.querySelectorAll(".lazy-video");

        const videoObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const video = entry.target;
                    const source = video.getAttribute("data-src");

                    if (source) {
                        video.src = source;
                        video.load();
                        video.play().then(() => {
                            video.classList.add('loaded');
                        }).catch(error => {
                            console.error("Autoplay failed:", error);
                        });
                        
                        // Stop observing once video starts loading
                        observer.unobserve(video);
                    }
                }
            });
        }, {
            rootMargin: "0px 0px 400px 0px", // Load 400px before viewport
            threshold: 0.1
        });

        lazyVideos.forEach(video => {
            videoObserver.observe(video);
        });
    });
</script>

<?php
include('includes/footer.php');
include('includes/footer-link.php');
?>