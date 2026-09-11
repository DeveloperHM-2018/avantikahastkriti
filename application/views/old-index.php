<?php
include('includes/header-link.php');
include('includes/header.php');
?>



<div class="slider-block style-one bg-linear xl:h-[860px] lg:h-[800px] md:h-[580px] sm:h-[500px] h-[350px] max-[420px]:h-[320px] w-full">
    <div class="slider-main h-full w-full">
        <div class="swiper swiper-slider h-full relative">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="slider-item h-full w-full relative">
                        <div class="container w-full h-full flex items-center relative">
                            <div class="text-content basis-1/2">
                                <div class="text-sub-display">Sale! Up To 50% Off!</div>
                                <div class="text-display md:mt-5 mt-2">Summer Sale Collections</div>
                                <a href="shop-breadcrumb-img.html" class="button-main md:mt-8 mt-3">Shop Now </a>
                            </div>
                            <div class="sub-img absolute sm:w-1/2 w-3/5 2xl:-right-[60px] -right-[16px] bottom-0">
                                <img src="<?= base_url() ?>assets/images/slider/bg1-1.png" alt="bg1-1" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="slider-item h-full w-full relative">
                        <div class="container w-full h-full flex items-center relative">
                            <div class="text-content basis-1/2">
                                <div class="text-sub-display">Sale! Up To 50% Off!</div>
                                <div class="text-display md:mt-5 mt-2">Fashion for Every Occasion</div>
                                <a href="shop-breadcrumb-img.html" class="button-main md:mt-8 mt-3">Shop Now </a>
                            </div>
                            <div class="sub-img absolute w-1/2 2xl:-right-[60px] -right-[0] sm:-bottom-[60px] bottom-0">
                                <img src="<?= base_url() ?>assets/images/slider/bg1-2.png" alt="bg1-2" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="slider-item h-full w-full relative">
                        <div class="container w-full h-full flex items-center relative">
                            <div class="text-content basis-1/2">
                                <div class="text-sub-display">Sale! Up To 50% Off!</div>
                                <div class="text-display md:mt-5 mt-2">Stylish Looks for Any Season</div>
                                <a href="shop-breadcrumb-img.html" class="button-main md:mt-8 mt-3">Shop Now </a>
                            </div>
                            <div class="sub-img absolute sm:w-1/2 w-2/3 2xl:-right-[60px] -right-[36px] sm:bottom-0 -bottom-[30px]">
                                <img src="<?= base_url() ?>assets/images/slider/bg1-3.png" alt="bg1-3" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>

<div class="what-new-block filter-product-block md:pt-20 pt-10">
    <div class="container">
        <div class="heading flex flex-col items-center text-center">
            <div class="heading3">What's new</div>
            <div class="menu-tab bg-surface rounded-2xl mt-6">
                <div class="menu flex items-center gap-2 p-1">
                    <div class="indicator absolute top-1 bottom-1 bg-white rounded-full shadow-md duration-300"></div>
                    <div class="tab-item relative text-secondary text-button-uppercase py-2 px-5 cursor-pointer duration-300 hover:text-black" data-item="top">top</div>
                    <div class="tab-item relative text-secondary text-button-uppercase py-2 px-5 cursor-pointer duration-300 hover:text-black active" data-item="t-shirt">t-shirt</div>
                    <div class="tab-item relative text-secondary text-button-uppercase py-2 px-5 cursor-pointer duration-300 hover:text-black" data-item="dress">dress</div>
                    <div class="tab-item relative text-secondary text-button-uppercase py-2 px-5 cursor-pointer duration-300 hover:text-black" data-item="sets">sets</div>
                    <div class="tab-item relative text-secondary text-button-uppercase py-2 px-5 cursor-pointer duration-300 hover:text-black" data-item="shirt">shirt</div>
                </div>
            </div>
        </div>
        <div class="list-product four-product hide-product-sold grid xl:grid-cols-4 sm:grid-cols-3 grid-cols-2 md:gap-[30px] gap-4 md:mt-10 mt-6">
            <!-- List four product -->
        </div>
    </div>
</div>

<div class="collection-block md:pt-20 pt-10">
    <div class="container">
        <div class="heading3 text-center">Explore Collections</div>
    </div>
    <div class="list-collection relative section-swiper-navigation md:mt-10 mt-6 sm:px-5 px-4">
        <div class="swiper-button-prev lg:left-10 left-6"></div>
        <div class="swiper swiper-collection h-full relative">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <a href="shop-breadcrumb1.html" class="collection-item block relative rounded-2xl overflow-hidden cursor-pointer">
                        <div class="bg-img">
                            <img src="<?= base_url() ?>assets/images/collection/swimwear.png" alt="swimwear" />
                        </div>
                        <div class="collection-name heading5 text-center sm:bottom-8 bottom-4 lg:w-[200px] md:w-[160px] w-[100px] md:py-3 py-1.5 bg-white rounded-xl duration-500">swimwear</div>
                    </a>
                </div>
                <div class="swiper-slide">
                    <a href="shop-breadcrumb1.html" class="collection-item block relative rounded-2xl overflow-hidden cursor-pointer">
                        <div class="bg-img">
                            <img src="<?= base_url() ?>assets/images/collection/top.png" alt="top" />
                        </div>
                        <div class="collection-name heading5 text-center sm:bottom-8 bottom-4 lg:w-[200px] md:w-[160px] w-[100px] md:py-3 py-1.5 bg-white rounded-xl duration-500">top</div>
                    </a>
                </div>
                <div class="swiper-slide">
                    <a href="shop-breadcrumb1.html" class="collection-item block relative rounded-2xl overflow-hidden cursor-pointer">
                        <div class="bg-img">
                            <img src="<?= base_url() ?>assets/images/collection/sets.png" alt="sets" />
                        </div>
                        <div class="collection-name heading5 text-center sm:bottom-8 bottom-4 lg:w-[200px] md:w-[160px] w-[100px] md:py-3 py-1.5 bg-white rounded-xl duration-500">sets</div>
                    </a>
                </div>
                <div class="swiper-slide">
                    <a href="shop-breadcrumb1.html" class="collection-item block relative rounded-2xl overflow-hidden cursor-pointer">
                        <div class="bg-img">
                            <img src="<?= base_url() ?>assets/images/collection/outerwear.png" alt="outerwear" />
                        </div>
                        <div class="collection-name heading5 text-center sm:bottom-8 bottom-4 lg:w-[200px] md:w-[160px] w-[100px] md:py-3 py-1.5 bg-white rounded-xl duration-500">outerwear</div>
                    </a>
                </div>
                <div class="swiper-slide">
                    <a href="shop-breadcrumb1.html" class="collection-item block relative rounded-2xl overflow-hidden cursor-pointer">
                        <div class="bg-img">
                            <img src="<?= base_url() ?>assets/images/collection/underwear.png" alt="underwear" />
                        </div>
                        <div class="collection-name heading5 text-center sm:bottom-8 bottom-4 lg:w-[200px] md:w-[160px] w-[100px] md:py-3 py-1.5 bg-white rounded-xl duration-500">underwear</div>
                    </a>
                </div>
                <div class="swiper-slide">
                    <a href="shop-breadcrumb1.html" class="collection-item block relative rounded-2xl overflow-hidden cursor-pointer">
                        <div class="bg-img">
                            <img src="<?= base_url() ?>assets/images/collection/t-shirt.png" alt="t-shirt" />
                        </div>
                        <div class="collection-name heading5 text-center sm:bottom-8 bottom-4 lg:w-[200px] md:w-[160px] w-[100px] md:py-3 py-1.5 bg-white rounded-xl duration-500">t-shirt</div>
                    </a>
                </div>
            </div>
        </div>
        <div class="swiper-button-next lg:right-10 right-6"></div>
    </div>
</div>

<div class="tab-features-block filter-product-block md:pt-20 pt-10">
    <div class="container">
        <div class="heading flex flex-col items-center text-center">
            <div class="menu-tab bg-surface rounded-2xl">
                <div class="menu flex items-center gap-2 p-1">
                    <div class="indicator absolute top-1 bottom-1 bg-white rounded-full shadow-md duration-300"></div>
                    <div class="tab-item relative text-secondary heading5 py-2 px-5 cursor-pointer duration-500 hover:text-black active" data-item="best sellers">best sellers</div>
                    <div class="tab-item relative text-secondary heading5 py-2 px-5 cursor-pointer duration-500 hover:text-black" data-item="on sale">on sale</div>
                    <div class="tab-item relative text-secondary heading5 py-2 px-5 cursor-pointer duration-500 hover:text-black" data-item="new arrivals">new arrivals</div>
                </div>
            </div>
        </div>
        <div class="list-product six-product hide-product-sold relative section-swiper-navigation style-outline style-small-border md:mt-10 mt-6">
            <div class="swiper-button-prev2 sm:left-10 left-6">
                <i class="ph-bold ph-caret-left text-xl"></i>
            </div>
            <div class="swiper swiper-list-product h-full relative">
                <div class="swiper-wrapper">
                    <!-- List six product -->
                </div>
            </div>
            <div class="swiper-button-next2 sm:right-10 right-6">
                <i class="ph-bold ph-caret-right text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="banner-block style-one grid sm:grid-cols-2 gap-5 md:pt-20 pt-10">
    <a href="shop-breadcrumb-img.html" class="banner-item relative block overflow-hidden duration-500">
        <div class="banner-img">
            <img src="<?= base_url() ?>assets/images/banner/1.png" class="duration-1000" alt="img" />
        </div>
        <div class="banner-content absolute top-0 left-0 w-full h-full flex flex-col items-center justify-center">
            <div class="heading2 text-white">Best Sellers</div>
            <div class="text-button text-white relative inline-block pb-1 border-b-2 border-white duration-500 mt-2">Shop Now</div>
        </div>
    </a>
    <a href="shop-breadcrumb-img.html" class="banner-item relative block overflow-hidden duration-500">
        <div class="banner-img">
            <img src="<?= base_url() ?>assets/images/banner/2.png" class="duration-1000" alt="img" />
        </div>
        <div class="banner-content absolute top-0 left-0 w-full h-full flex flex-col items-center justify-center">
            <div class="heading2 text-white">New Arrivals</div>
            <div class="text-button text-white relative inline-block pb-1 border-b-2 border-white duration-500 mt-2">Shop Now</div>
        </div>
    </a>
</div>

<div class="benefit-block md:pt-20 pt-10">
    <div class="container">
        <div class="list-benefit grid items-start lg:grid-cols-4 grid-cols-2 gap-[30px]">
            <div class="benefit-item flex flex-col items-center justify-center">
                <i class="icon-phone-call lg:text-7xl text-5xl"></i>
                <div class="heading6 text-center mt-5">24/7 Customer Service</div>
                <div class="caption1 text-secondary text-center mt-3">We're here to help you with any questions or concerns you have, 24/7.</div>
            </div>
            <div class="benefit-item flex flex-col items-center justify-center">
                <i class="icon-return lg:text-7xl text-5xl"></i>
                <div class="heading6 text-center mt-5">14-Day Money Back</div>
                <div class="caption1 text-secondary text-center mt-3">If you're not satisfied with your purchase, simply return it within 14 days for a refund.</div>
            </div>
            <div class="benefit-item flex flex-col items-center justify-center">
                <i class="icon-guarantee lg:text-7xl text-5xl"></i>
                <div class="heading6 text-center mt-5">Our Guarantee</div>
                <div class="caption1 text-secondary text-center mt-3">We stand behind our products and services and guarantee your satisfaction.</div>
            </div>
            <div class="benefit-item flex flex-col items-center justify-center">
                <i class="icon-delivery-truck lg:text-7xl text-5xl"></i>
                <div class="heading6 text-center mt-5">Shipping worldwide</div>
                <div class="caption1 text-secondary text-center mt-3">We ship our products worldwide, making them accessible to customers everywhere.</div>
            </div>
        </div>
    </div>
</div>

<div class="testimonial-block md:pt-20 md:pb-16 pt-10 pb-8 md:mt-20 mt-10 bg-surface">
    <div class="container">
        <div class="heading3 text-center">What People Are Saying</div>
        <div class="list-testimonial pagination-mt40 md:mt-10 mt-6">
            <div class="swiper swiper-list-testimonial h-full relative">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="testimonial-item style-one h-full">
                            <div class="testimonial-main bg-white p-8 rounded-2xl h-full">
                                <div class="flex items-center gap-1">
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                </div>
                                <div class="heading6 title mt-4">Variety of Styles!</div>
                                <div class="desc mt-2">"Fantastic shop! Great selection, fair prices, and friendly staff. Highly recommended. The quality of the products is exceptional, and the prices are very reasonable!"</div>
                                <div class="text-button name mt-4">Lisa K.</div>
                                <div class="caption2 date text-secondary2 mt-1">August 13, 2024</div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="testimonial-item style-one h-full">
                            <div class="testimonial-main bg-white p-8 rounded-2xl h-full">
                                <div class="flex items-center gap-1">
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                </div>
                                <div class="heading6 title mt-4">Quality of Clothing!</div>
                                <div class="desc mt-2">"Anvouge's fashion collection is a game-changer! Their unique and trendy pieces have completely transformed my style. It's comfortable, stylish, and always on-trend."</div>
                                <div class="text-button name mt-4">Elizabeth A.</div>
                                <div class="caption2 date text-secondary2 mt-1">August 13, 2024</div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="testimonial-item style-one h-full">
                            <div class="testimonial-main bg-white p-8 rounded-2xl h-full">
                                <div class="flex items-center gap-1">
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                </div>
                                <div class="heading6 title mt-4">Customer Service!</div>
                                <div class="desc mt-2">"I absolutely love this shop! The products are high-quality and the customer service is excellent. I always leave with exactly what I need and a smile on my face."</div>
                                <div class="text-button name mt-4">Christin H.</div>
                                <div class="caption2 date text-secondary2 mt-1">August 13, 2024</div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="testimonial-item style-one h-full">
                            <div class="testimonial-main bg-white p-8 rounded-2xl h-full">
                                <div class="flex items-center gap-1">
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                </div>
                                <div class="heading6 title mt-4">Quality of Clothing!</div>
                                <div class="desc mt-2">"I can't get enough of Anvouge's high-quality clothing. It's comfortable, stylish, and always on-trend. The products are high-quality and the customer service is excellent."</div>
                                <div class="text-button name mt-4">Emily G.</div>
                                <div class="caption2 date text-secondary2 mt-1">August 13, 2024</div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="testimonial-item style-one h-full">
                            <div class="testimonial-main bg-white p-8 rounded-2xl h-full">
                                <div class="flex items-center gap-1">
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                    <i class="ph-fill ph-star text-yellow"></i>
                                </div>
                                <div class="heading6 title mt-4">Customer Service!</div>
                                <div class="desc mt-2">"I love this shop! The products are always top-quality, and the staff is incredibly friendly and helpful. They go out of their way to make sure that I'm satisfied my purchase."</div>
                                <div class="text-button name mt-4">Carolina C.</div>
                                <div class="caption2 date text-secondary2 mt-1">August 13, 2024</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>
</div>

<div class="instagram-block md:pt-20 pt-10">
    <div class="container">
        <div class="heading">
            <div class="heading3 text-center">Anvogue On Instagram</div>
            <div class="text-center mt-3">#Anvougetheme</div>
        </div>
        <div class="list-instagram md:mt-10 mt-6">
            <div class="swiper swiper-list-instagram">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <a href="https://www.instagram.com/" target="_blank" class="item relative block rounded-[32px] overflow-hidden">
                            <img src="<?= base_url() ?>assets/images/instagram/0.png" alt="0" class="h-full w-full duration-500 relative" />
                            <div class="icon w-12 h-12 bg-white hover:bg-black duration-500 flex items-center justify-center rounded-2xl absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[1]">
                                <div class="icon-instagram text-2xl text-black"></div>
                            </div>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="https://www.instagram.com/" target="_blank" class="item relative block rounded-[32px] overflow-hidden">
                            <img src="<?= base_url() ?>assets/images/instagram/1.png" alt="1" class="h-full w-full duration-500 relative" />
                            <div class="icon w-12 h-12 bg-white hover:bg-black duration-500 flex items-center justify-center rounded-2xl absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[1]">
                                <div class="icon-instagram text-2xl text-black"></div>
                            </div>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="https://www.instagram.com/" target="_blank" class="item relative block rounded-[32px] overflow-hidden">
                            <img src="<?= base_url() ?>assets/images/instagram/2.png" alt="2" class="h-full w-full duration-500 relative" />
                            <div class="icon w-12 h-12 bg-white hover:bg-black duration-500 flex items-center justify-center rounded-2xl absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[1]">
                                <div class="icon-instagram text-2xl text-black"></div>
                            </div>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="https://www.instagram.com/" target="_blank" class="item relative block rounded-[32px] overflow-hidden">
                            <img src="<?= base_url() ?>assets/images/instagram/3.png" alt="3" class="h-full w-full duration-500 relative" />
                            <div class="icon w-12 h-12 bg-white hover:bg-black duration-500 flex items-center justify-center rounded-2xl absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[1]">
                                <div class="icon-instagram text-2xl text-black"></div>
                            </div>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="https://www.instagram.com/" target="_blank" class="item relative block rounded-[32px] overflow-hidden">
                            <img src="<?= base_url() ?>assets/images/instagram/4.png" alt="4" class="h-full w-full duration-500 relative" />
                            <div class="icon w-12 h-12 bg-white hover:bg-black duration-500 flex items-center justify-center rounded-2xl absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[1]">
                                <div class="icon-instagram text-2xl text-black"></div>
                            </div>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="https://www.instagram.com/" target="_blank" class="item relative block rounded-[32px] overflow-hidden">
                            <img src="<?= base_url() ?>assets/images/instagram/5.png" alt="5" class="h-full w-full duration-500 relative" />
                            <div class="icon w-12 h-12 bg-white hover:bg-black duration-500 flex items-center justify-center rounded-2xl absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[1]">
                                <div class="icon-instagram text-2xl text-black"></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="brand-block md:py-[60px] py-[32px]">
    <div class="container">
        <div class="list-brand">
            <div class="swiper swiper-list-brand">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="brand-item relative flex items-center justify-center h-[36px]">
                            <img src="<?= base_url() ?>assets/images/brand/1.png" alt="1" class="h-full w-auto duration-500 relative object-cover" />
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="brand-item relative flex items-center justify-center h-[36px]">
                            <img src="<?= base_url() ?>assets/images/brand/2.png" alt="2" class="h-full w-auto duration-500 relative object-cover" />
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="brand-item relative flex items-center justify-center h-[36px]">
                            <img src="<?= base_url() ?>assets/images/brand/3.png" alt="3" class="h-full w-auto duration-500 relative object-cover" />
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="brand-item relative flex items-center justify-center h-[36px]">
                            <img src="<?= base_url() ?>assets/images/brand/4.png" alt="4" class="h-full w-auto duration-500 relative object-cover" />
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="brand-item relative flex items-center justify-center h-[36px]">
                            <img src="<?= base_url() ?>assets/images/brand/5.png" alt="5" class="h-full w-auto duration-500 relative object-cover" />
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="brand-item relative flex items-center justify-center h-[36px]">
                            <img src="<?= base_url() ?>assets/images/brand/6.png" alt="6" class="h-full w-auto duration-500 relative object-cover" />
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="brand-item relative flex items-center justify-center h-[36px]">
                            <img src="<?= base_url() ?>assets/images/brand/7.png" alt="7" class="h-full w-auto duration-500 relative object-cover" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php 
include('includes/footer.php');
include('includes/footer-link.php');
?>