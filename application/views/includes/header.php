<div id="top-nav" class="top-nav style-eight theme-btn md:h-[44px] h-[30px]">
    <div class="container mx-auto h-full">
        <div class="top-nav-main flex justify-between max-md:justify-center h-full">
            <div class="left-content flex items-center gap-5 max-md:hidden">
                <a href="<?= base_url('contact') ?>" class="choose-type choose-language flex items-center gap-1.5">
                    <i class="ph ph-info text-xs text-white"></i>
                    <div class="select relative">
                        <p class="selected caption2 text-white">Help</p>
                    </div>
                </a>

            </div>
            <div class="right-content flex items-center gap-5 max-md:hidden">
                <?php if (!empty($this->socialFacebookUrl)): ?>
                    <a href="<?= $this->socialFacebookUrl ?>" target="_blank" rel="noopener noreferrer">
                        <i class="icon-facebook text-white"></i>
                    </a>
                <?php endif; ?>
                <?php if (!empty($this->socialInstagramUrl)): ?>
                    <a href="<?= $this->socialInstagramUrl ?>" target="_blank" rel="noopener noreferrer">
                        <i class="icon-instagram text-white"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div id="header" class="relative w-full style-nine">
    <div class="header-menu style-eight relative bg-white w-full md:h-[74px] h-[56px]">
        <div class="container mx-auto h-full">
            <div class="header-main flex items-center justify-between h-full">
                <div class="menu-mobile-icon lg:hidden flex items-center">
                    <i class="icon-category text-2xl"></i>
                </div>
                <a href="<?= base_url() ?>" class="flex items-center">
                    <div class="heading4 header-logo"><img src="<?= base_url() ?>assets/img/logo.jpg" alt="<?= $this->projectName ?>" width="80px"></div>
                </a>

                <div class="form-search w-2/3 pl-8 flex items-center h-[44px] max-lg:hidden">
                    <div class="w-full flex items-center h-full relative">
                        <input type="text" class="search-input h-full px-4 w-full border border-line"
                            placeholder="What are you looking for today?" name="headerSearchBox" />
                        <!-- <button class="search-button button-main theme-btn h-full flex items-center px-7 rounded-none rounded-r">Search</button> -->
                        <div class="product-search-wrapper hidden">
                            <div onclick="document.querySelector('.product-search-wrapper').classList.add('hidden')" class="absolute right-2 top-1 w-6 h-6 rounded-full bg-surface flex items-center justify-center duration-300 cursor-pointer hover:bg-black hover:text-white">
                                <i class="ph ph-x text-sm"></i>
                            </div>
                            <div class="search-data mt-4">
                                <p>You must enter at least 2 characters.</p>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="right flex gap-12">
                    <div class="list-action flex items-center gap-4">
                        <div class="user-icon flex items-center justify-center cursor-pointer">
                            <i class="ph-bold ph-user text-2xl"></i>
                            <div class="login-popup absolute top-[74px] w-[320px] p-7 rounded-xl bg-white">
                                <?php
                                if (!$this->session->has_userdata('login_user_id')) {
                                ?>
                                    <a href="<?= base_url('login') ?>" class="button-main w-full text-center">Login</a>
                                    <div class="text-secondary text-center mt-3 pb-4">
                                        Don’t have an account?
                                        <a href="<?= base_url('register') ?>" class="text-black pl-1 hover:underline">Register </a>
                                    </div>
                                <?php
                                } else {
                                ?>
                                    <a href="<?= base_url('profile') ?>"
                                        class="button-main bg-white text-black border border-black w-full text-center">Dashboard</a>
                                <?php } ?>
                                <div class="bottom mt-4 pt-4 border-t border-line"></div>

                                <a href="<?= base_url('contact') ?>" class="body1 hover:underline">Support</a>
                            </div>
                        </div>
                        <div class="max-md:hidden wishlist-icon flex items-center relative cursor-pointer">
                            <i class="ph-bold ph-heart text-2xl"></i>
                            <span
                                class="quantity wishlist-quantity absolute -right-1.5 -top-1.5 text-xs text-white theme-btn w-4 h-4 flex items-center justify-center rounded-full">0</span>
                        </div>
                        <div class="cart-icon flex items-center relative cursor-pointer">
                            <i class="ph-bold ph-handbag text-2xl"></i>
                            <span
                                class="quantity cart-quantity absolute -right-1.5 -top-1.5 text-xs text-white theme-btn w-4 h-4 flex items-center justify-center rounded-full">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="top-nav-menu relative bg-white border-t border-b border-line h-[44px] max-lg:hidden z-10">
        <div class="container h-full">
            <div class="top-nav-menu-main flex items-center justify-between h-full">
                <div class="left flex items-center h-full" style="justify-content: center;">

                    <div class="menu-main style-eight h-full pl-12 max-lg:hidden">
                        <ul class="flex items-center gap-8 h-full">
                            <li class="h-full relative">
                                <a href="<?= base_url() ?>"
                                    class="text-button-uppercase duration-300 h-full flex items-center justify-center gap-1">
                                    Home </a>
                            </li>
                            <li class="h-full relative">
                                <a href="<?= base_url('products') ?>"
                                    class="text-button-uppercase duration-300 h-full flex items-center justify-center gap-1 ">
                                    Products </a>
                            </li>
                            <?php
                            $featured_categories = $this->CommonModel->getRowByIdInOrder('category', "is_delete = '1' and featured = '1'", 'category_id', 'DESC');
                            if (!empty($featured_categories)) {
                                foreach ($featured_categories as $feaCate) {
                            ?>
                                    <li class="h-full">
                                        <a href="<?= base_url('products/' . url_title($feaCate['category_name'], '-', true)) ?>" class="text-button-uppercase duration-300 h-full flex items-center justify-center"> <?= $feaCate['category_name'] ?></a>
                                        <div class="mega-menu absolute top-[54px] left-0 bg-white w-screen">
                                            <div class="container">
                                                <div class="nav-link w-full flex justify-between py-8">
                                                    <?php
                                                    $sub_categories = $this->CommonModel->getRowByIdInOrder('sub_category', "is_delete = '1' and category_id = {$feaCate['category_id']}", 'sub_category_id', 'DESC');
                                                    if (!empty($sub_categories)) {
                                                        foreach ($sub_categories as $subCate) {
                                                            $sub_category_type = $this->CommonModel->getRowByIdInOrder('sub_category_type', "is_delete = '1' and sub_category_id = {$subCate['sub_category_id']}", 'sub_category_type_id', 'DESC');
                                                            if (!empty($sub_category_type)) {
                                                                $seenTypeNames = [];
                                                                $sub_category_type = array_filter($sub_category_type, function ($t) use (&$seenTypeNames) {
                                                                    $key = strtolower(trim($t['sub_category_type_name']));
                                                                    if (isset($seenTypeNames[$key])) {
                                                                        return false;
                                                                    }
                                                                    $seenTypeNames[$key] = true;
                                                                    return true;
                                                                });
                                                            }
                                                            if (!empty($sub_category_type)) {
                                                    ?>
                                                            <div class="nav-item">
                                                                <?php
                                                                $subUrl = base_url('products/' .
                                                                    url_title($feaCate['category_name'], '-', true) . '/' .
                                                                    url_title($subCate['sub_category_name'], '-', true));
                                                                // Cap the visible craft list here too, same as the
                                                                // mobile menu, so a subcategory with a long craft
                                                                // list doesn't blow out the mega-menu's height.
                                                                $desktopTypeLimit = 8;
                                                                $sub_category_type_total_desktop = count($sub_category_type);
                                                                $sub_category_type_display_desktop = array_slice($sub_category_type, 0, $desktopTypeLimit);
                                                                ?>
                                                                <a href="<?= $subUrl ?>" class="text-button-uppercase pb-2 block"><?= $subCate['sub_category_name'] ?></a>
                                                                <ul>
                                                                    <?php foreach ($sub_category_type_display_desktop as $subCateType) {
                                                                    ?>
                                                                            <li>
                                                                                <?php
                                                                                $typeUrl = base_url('products/' .
                                                                                    url_title($feaCate['category_name'], '-', true) . '/' .
                                                                                    url_title($subCate['sub_category_name'], '-', true) . '/' .
                                                                                    url_title($subCateType['sub_category_type_name'], '-', true));
                                                                                ?>
                                                                                <a href="<?= $typeUrl ?>" class="link text-secondary duration-300"> <?= $subCateType['sub_category_type_name'] ?> </a>
                                                                            </li>
                                                                    <?php } ?>
                                                                    <?php if ($sub_category_type_total_desktop > $desktopTypeLimit): ?>
                                                                        <li>
                                                                            <a href="<?= $subUrl ?>" class="link text-button duration-300">View All</a>
                                                                        </li>
                                                                    <?php endif; ?>
                                                                </ul>
                                                            </div>
                                                    <?php }
                                                        }
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                <?php
                                }
                                ?>

                            <?php
                            }
                            ?>

                        </ul>
                    </div>
                </div>
                <div class="right flex items-center gap-1">
                    <div class="caption1">Email:</div>
                    <div class="text-button" style="text-transform: lowercase"><?= $this->companyEmail; ?></div>
                    <div class=""></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Mobile -->
    <div id="menu-mobile" class="">
        <div class="menu-container bg-white h-full">
            <div class="container h-full">
                <div class="menu-main h-full overflow-hidden">
                    <div class="heading py-2 relative flex items-center justify-center">
                        <div
                            class="close-menu-mobile-btn absolute left-0 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-surface flex items-center justify-center">
                            <i class="ph ph-x text-sm"></i>
                        </div>
                        <a href="<?= base_url() ?>" class="logo text-3xl font-semibold text-center">
                            <img src="<?= base_url() ?>assets/img/logo.jpg" alt="<?= $this->projectName ?>" width="50px">
                        </a>
                    </div>
                    <div class="form-search relative mt-2">
                        <i class="ph ph-magnifying-glass text-xl absolute left-3 top-1/2 -translate-y-1/2 cursor-pointer"></i>
                        <input type="text" placeholder="What are you looking for?"
                            class="h-12 rounded-lg border border-line text-sm w-full pl-10 pr-4" name="headerSearchBox" />
                        <div class="product-search-wrapper hidden">
                            <div onclick="document.querySelector('.product-search-wrapper').classList.add('hidden')" class="absolute right-2 top-1 w-6 h-6 rounded-full bg-surface flex items-center justify-center duration-300 cursor-pointer hover:bg-black hover:text-white">
                                <i class="ph ph-x text-sm"></i>
                            </div>
                            <div class="search-data mt-4">
                                <p>You must enter at least 2 characters.</p>
                            </div>
                        </div>
                    </div>
                    <div class="list-nav mt-6">
                        <ul>
                            <li>
                                <a href="<?= base_url() ?>" class="text-xl font-semibold flex items-center justify-between">Home
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('products') ?>" class="text-xl font-semibold flex items-center justify-between mt-5">Product
                                </a>
                            </li>
                            <?php
                            $featured_categories_mobile = $this->CommonModel->getRowByIdInOrder('category', "is_delete = '1' and featured = '1'", 'category_id', 'DESC');
                            if (!empty($featured_categories_mobile)) {
                                foreach ($featured_categories_mobile as $feaCate) {
                            ?>
                                    <li>
                                        <a href="#!" class="text-xl font-semibold flex items-center justify-between mt-5"><?= $feaCate['category_name'] ?>
                                            <span class="text-right">
                                                <i class="ph ph-caret-right text-xl"></i>
                                            </span>
                                        </a>
                                        <div class="sub-nav-mobile">
                                            <div class="back-btn flex items-center gap-3">
                                                <i class="ph ph-caret-left text-xl"></i>
                                                Back
                                            </div>
                                            <div class="list-nav-item w-full pt-2 pb-6">
                                                <div class="nav-link grid grid-cols-2 gap-5 gap-y-6">
                                                    <?php
                                                    $sub_categories = $this->CommonModel->getRowByIdInOrder('sub_category', "is_delete = '1' and category_id = {$feaCate['category_id']}", 'sub_category_id', 'DESC');
                                                    if (!empty($sub_categories)) {
                                                        foreach ($sub_categories as $subCate) {
                                                            $sub_category_type = $this->CommonModel->getRowByIdInOrder('sub_category_type', "is_delete = '1' and sub_category_id = {$subCate['sub_category_id']}", 'sub_category_type_id', 'DESC');
                                                            if (!empty($sub_category_type)) {
                                                                $seenTypeNamesMobile = [];
                                                                $sub_category_type = array_filter($sub_category_type, function ($t) use (&$seenTypeNamesMobile) {
                                                                    $key = strtolower(trim($t['sub_category_type_name']));
                                                                    if (isset($seenTypeNamesMobile[$key])) {
                                                                        return false;
                                                                    }
                                                                    $seenTypeNamesMobile[$key] = true;
                                                                    return true;
                                                                });
                                                            }
                                                            if (empty($sub_category_type)) {
                                                                continue;
                                                            }
                                                            $subUrl = base_url('products/' .
                                                                url_title($feaCate['category_name'], '-', true) . '/' .
                                                                url_title($subCate['sub_category_name'], '-', true));
                                                            // Cap the visible craft list on mobile so the menu
                                                            // doesn't turn into a long scroll - the rest are
                                                            // still reachable via "View All".
                                                            $mobileTypeLimit = 8;
                                                            $sub_category_type_total = count($sub_category_type);
                                                            $sub_category_type_display = array_slice($sub_category_type, 0, $mobileTypeLimit);
                                                    ?>
                                                            <div class="nav-item">
                                                                <a href="<?= $subUrl ?>" class="text-button-uppercase pb-1 block"><?= $subCate['sub_category_name'] ?></a>
                                                                <ul>
                                                                    <?php foreach ($sub_category_type_display as $subCateType) {
                                                                            $typeUrl = base_url('products/' .
                                                                                url_title($feaCate['category_name'], '-', true) . '/' .
                                                                                url_title($subCate['sub_category_name'], '-', true) . '/' .
                                                                                url_title($subCateType['sub_category_type_name'], '-', true));
                                                                    ?>
                                                                            <li>
                                                                                <a href="<?= $typeUrl ?>" class="link text-secondary duration-300"> <?= $subCateType['sub_category_type_name'] ?> </a>
                                                                            </li>
                                                                    <?php } ?>
                                                                    <?php if ($sub_category_type_total > $mobileTypeLimit): ?>
                                                                        <li>
                                                                            <a href="<?= $subUrl ?>" class="link text-button duration-300">View All</a>
                                                                        </li>
                                                                    <?php endif; ?>
                                                                </ul>
                                                            </div>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                            <?php
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu bar -->
    <div class="menu_bar fixed bg-white bottom-0 left-0 w-full h-[70px] hidden z-[101]">
        <div class="menu_bar-inner grid grid-cols-4 items-center h-full">
            <a href="<?= base_url() ?>" class="menu_bar-link flex flex-col items-center gap-1">
                <span class="ph-bold ph-house text-2xl block"></span>
                <span class="menu_bar-title caption2 font-semibold">Home</span>
            </a>
            <a href="javascript: void(0);" class="menu_bar-link flex flex-col items-center gap-1">
                <span class="ph-bold ph-list text-2xl block"></span>
                <span class="menu_bar-title caption2 font-semibold">Category</span>
            </a>
            <a href="javascript: void(0);" class="menu_bar-link flex flex-col items-center gap-1">
                <span class="ph-bold ph-magnifying-glass text-2xl block"></span>
                <span class="menu_bar-title caption2 font-semibold">Search</span>
            </a>
            <a href="<?= base_url('cart') ?>" class="menu_bar-link flex flex-col items-center gap-1">
                <div class="cart-icon relative">
                    <span class="ph-bold ph-handbag text-2xl block"></span>
                    <span
                        class="quantity cart-quantity absolute -right-1.5 -top-1.5 text-xs text-white bg-black w-4 h-4 flex items-center justify-center rounded-full">0</span>
                </div>
                <span class="menu_bar-title caption2 font-semibold">Cart</span>
            </a>
        </div>
    </div>

</div>