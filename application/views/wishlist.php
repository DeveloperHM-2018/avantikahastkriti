<?php
include('includes/header-link.php');
include('includes/header.php');
?>

<div class="breadcrumb-block style-shared">
    <div class="breadcrumb-main bg-linear overflow-hidden">
        <div class="container lg:pt-[60px] pt-5 pb-5 relative">
            <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                <div class="text-content">
                    <div class="heading2 text-center">Wishlist</div>
                    <div class="link flex items-center justify-center gap-1 caption1 mt-2">
                        <a href="<?= base_url() ?>">Homepage</a>
                        <i class="ph ph-caret-right text-sm text-secondary2"></i>
                        <div class="text-secondary2 capitalize">Wishlist</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="wishlist-block lg:py-20 md:py-14 py-10">
    <div class="container">
        <div class="list-product-block">
            <div class="relative">
                <div class="filter-heading flex items-center justify-between gap-5 flex-wrap">
                    <div class="left flex has-line items-center flex-wrap gap-5">
                        <div class="choose-layout menu-tab flex items-center gap-2">
                            <div class="item tab-item three-col p-2 border border-line rounded flex items-center justify-center cursor-pointer">
                                <div class="flex three-col items-center gap-0.5">
                                    <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                    <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                    <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                </div>
                            </div>
                            <div class="item tab-item four-col p-2 border border-line rounded flex items-center justify-center cursor-pointer active">
                                <div class="flex four-col items-center gap-0.5">
                                    <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                    <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                    <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                    <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                </div>
                            </div>
                            <div class="item tab-item five-col p-2 border border-line rounded flex items-center justify-center cursor-pointer">
                                <div class="flex five-col items-center gap-0.5">
                                    <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                    <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                    <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                    <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                    <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="right flex items-center gap-3">
                        <!-- <div class="select-block filter-type relative">
                            <select class="caption1 py-2 pl-3 md:pr-12 pr-8 rounded-lg border border-line capitalize" name="select-type" id="select-type">
                                <option value="Type">Type</option>
                                <option class="item cursor-pointer">t-shirt</option>
                                <option class="item cursor-pointer">dress</option>
                                <option class="item cursor-pointer">top</option>
                                <option class="item cursor-pointer">swimwear</option>
                                <option class="item cursor-pointer">shirt</option>
                                <option class="item cursor-pointer">underwear</option>
                                <option class="item cursor-pointer">sets</option>
                            </select>
                            <i class="ph ph-caret-down absolute top-1/2 -translate-y-1/2 md:right-4 right-2"></i>
                        </div> -->
                        <!-- <div class="select-block relative">
                            <select id="select-filter" name="select-filter" class="caption1 py-2 pl-3 md:pr-20 pr-10 rounded-lg border border-line">
                                <option value="Sorting">Sorting</option>
                                <option value="soldQuantityHighToLow">Best Selling</option>
                                <option value="discountHighToLow">Best Discount</option>
                                <option value="priceHighToLow">Price High To Low</option>
                                <option value="priceLowToHigh">Price Low To High</option>
                            </select>
                            <i class="ph ph-caret-down absolute top-1/2 -translate-y-1/2 md:right-4 right-2"></i>
                        </div> -->
                    </div>
                </div>
            </div>

            <div class="list-product hide-product-sold grid lg:grid-cols-4 sm:grid-cols-3 grid-cols-2 sm:gap-[30px] gap-[20px] mt-7"></div>
        </div>
    </div>
</div>
<?php
include('includes/footer.php');
include('includes/footer-link.php');
?>