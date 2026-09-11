<?php
include('includes/header-link.php');
include('includes/header.php');
?>

<div class="breadcrumb-block style-img">
    <div class="breadcrumb-main bg-linear overflow-hidden">
        <div class="container lg:pt-[60px] pt-5 pb-5 relative">
            <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                <div class="text-content">
                    <div class="heading4 text-center"><?= $selected_subcategory_type_name ?: ($selected_subcategory_name ?: ($selected_category_name ?: 'All Products')) ?></div>
                    <div class="link flex items-center justify-center gap-1 caption1 mt-2">
                        <a href="<?= base_url() ?>">Homepage</a>
                        <i class="ph ph-caret-right text-sm text-secondary2"></i>
                        <?php if ($selected_category_name): ?>
                            <a href="<?= base_url('products/' . $selected_category) ?>" class="<?= $selected_subcategory ? '' : 'text-secondary2' ?>"><?= $selected_category_name ?></a>
                            <?php if ($selected_subcategory_name): ?>
                                <i class="ph ph-caret-right text-sm text-secondary2"></i>
                                <a href="<?= base_url('products/' . $selected_category . '/' . $selected_subcategory) ?>" class="<?= $selected_subcategory_type ? '' : 'text-secondary2' ?>"><?= $selected_subcategory_name ?></a>
                                <?php if ($selected_subcategory_type_name): ?>
                                    <i class="ph ph-caret-right text-sm text-secondary2"></i>
                                    <div class="text-secondary2 capitalize"><?= $selected_subcategory_type_name ?></div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-secondary2 capitalize">All Products</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="shop-product lg:py-20 md:py-14 py-10">
    <div class="container">
        <div class="list-product-block style-grid relative">
            <div class="filter-heading flex items-center justify-between gap-5 flex-wrap">
                <div class="left flex has-line items-center flex-wrap gap-5">
                    <div class="filter-sidebar-btn flex items-center gap-2 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M4 21V14" stroke="#1F1F1F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M4 10V3" stroke="#1F1F1F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M12 21V12" stroke="#1F1F1F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M12 8V3" stroke="#1F1F1F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M20 21V16" stroke="#1F1F1F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M20 12V3" stroke="#1F1F1F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M1 14H7" stroke="#1F1F1F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M9 8H15" stroke="#1F1F1F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M17 16H23" stroke="#1F1F1F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Filters</span>
                    </div>
                    <div class="choose-layout menu-tab flex items-center gap-2">
                        <div class="item tab-item three-col p-2 border border-line rounded flex items-center justify-center cursor-pointer">
                            <div class="flex items-center gap-0.5">
                                <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                            </div>
                        </div>
                        <div class="item tab-item four-col p-2 border border-line rounded flex items-center justify-center cursor-pointer ">
                            <div class="flex items-center gap-0.5">
                                <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                            </div>
                        </div>
                        <div class="active item tab-item five-col p-2 border border-line rounded flex items-center justify-center cursor-pointer ">
                            <div class="flex items-center gap-0.5">
                                <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                                <span class="w-[3px] h-4 bg-secondary2 rounded-sm"></span>
                            </div>
                        </div>
                    </div>
                    <div class="check-sale flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="filterSale" id="filter-sale" class="border-line" />
                        <label for="filter-sale" class="cation1 cursor-pointer">Show only products on sale</label>
                    </div>
                </div>
                <div class="sort-product right flex items-center gap-3">
                    <label for="select-filter" class="caption1 capitalize">Sort by</label>
                    <div class="select-block relative">
                        <select id="select-filter" name="select-filter" class="caption1 py-2 pl-3 md:pr-20 pr-10 rounded-lg border border-line">
                            <option value="Sorting">Sorting</option>
                            <option value="soldQuantityHighToLow">Best Selling</option>
                            <option value="discountHighToLow">Best Discount</option>
                            <option value="priceHighToLow">Price High To Low</option>
                            <option value="priceLowToHigh">Price Low To High</option>
                        </select>
                        <i class="ph ph-caret-down absolute top-1/2 -translate-y-1/2 md:right-4 right-2"></i>
                    </div>
                </div>
            </div>
            <div class="sidebar style-canvas">
                <div class="sidebar-main">
                    <div class="heading flex items-center justify-between">
                        <div class="heading5">Filters</div>
                        <i class="ph-bold ph-x text-xl cursor-pointer close-sidebar-btn"></i>
                    </div>
                    <?php if ($categories) { ?>
                        <div class="filter-type-block pb-8 border-b border-line mt-7">
                            <div class="heading6">Categories</div>
                            <div class="list-type filter-type menu-tab mt-4">
                                <?php foreach ($categories as $cate) { ?>
                                    <div class="item tab-item flex items-center justify-between cursor-pointer" data-item="<?= url_title($cate['category_name'], '-', true) ?>">
                                        <div class="type-name text-secondary has-line-before hover:text-black capitalize"><?= $cate['category_name'] ?></div>
                                        <div class="text-secondary2 number">0</div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($sub_categories) { ?>
                        <div class="filter-subcategory-block pb-8 border-b border-line mt-7">
                            <div class="heading6">Sub Categories</div>
                            <div class="list-type filter-subcategory menu-tab mt-4">
                                <?php foreach ($sub_categories as $subCate) { ?>
                                    <div class="item tab-item flex items-center justify-between cursor-pointer" data-item="<?= url_title($subCate['sub_category_name'], '-', true) ?>">
                                        <div class="type-name text-secondary has-line-before hover:text-black capitalize"><?= $subCate['sub_category_name'] ?></div>
                                        <div class="text-secondary2 number">0</div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($sub_category_types) { ?>
                        <div class="filter-subcategory-type-block pb-8 border-b border-line mt-7">
                            <div class="heading6">Sub Category Types</div>
                            <div class="list-type filter-subcategory-type menu-tab mt-4">
                                <?php foreach ($sub_category_types as $subCateType) { ?>
                                    <div class="item tab-item flex items-center justify-between cursor-pointer" data-item="<?= url_title($subCateType['sub_category_type_name'], '-', true) ?>">
                                        <div class="type-name text-secondary has-line-before hover:text-black capitalize"><?= $subCateType['sub_category_type_name'] ?></div>
                                        <div class="text-secondary2 number">0</div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="filter-size pb-8 border-b border-line mt-8 hidden">
                        <div class="heading6">Size</div>
                        <div class="list-size flex items-center flex-wrap gap-3 gap-y-4 mt-4">
                            <div class="size-item text-button w-[44px] h-[44px] flex items-center justify-center rounded-full border border-line" data-item="2KG">2KG</div>
                            <div class="size-item text-button w-[44px] h-[44px] flex items-center justify-center rounded-full border border-line" data-item="5KG">5KG</div>
                        </div>
                    </div>
                    <div class="filter-price pb-8 border-b border-line mt-8">
                        <div class="heading6">Price Range</div>
                        <div class="tow-bar-block mt-5">
                            <div class="progress"></div>
                        </div>
                        <div class="range-input">
                            <input class="range-min" type="range" min="0" max="50000" value="0" />
                            <input class="range-max" type="range" min="0" max="50000" value="50000" />
                        </div>
                        <div class="price-block flex items-center justify-between flex-wrap mt-4">
                            <div class="min flex items-center gap-1">
                                <div>Min price:</div>
                                <div class="min-price">₹0</div>
                            </div>
                            <div class="min flex items-center gap-1">
                                <div>Max price:</div>
                                <div class="max-price">₹50000</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="list-filtered flex items-center gap-3 flex-wrap"></div>

            <!-- === Products list here === -->
            <div class="list-product hide-product-sold grid sm:grid-cols-3 grid-cols-2 sm:gap-[30px] gap-[20px] mt-7" data-item="12">

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


            <div class="list-pagination w-full flex items-center justify-center gap-4 mt-10"></div>
        </div>
    </div>
</div>

<script>
    // Seeds shop.js's initial filter state from the path-segment URL
    // (/products/{category}/{subcategory}/{subcategory_type}), already
    // validated against the DB by Web::products(). shop.js falls back to
    // reading the query string directly if this isn't present.
    window.initialProductFilters = {
        category: <?= json_encode($selected_category) ?>,
        subcategory: <?= json_encode($selected_subcategory) ?>,
        subcategory_type: <?= json_encode($selected_subcategory_type) ?>
    };
</script>

<?php
include('includes/footer.php');
include('includes/footer-link.php');
?>