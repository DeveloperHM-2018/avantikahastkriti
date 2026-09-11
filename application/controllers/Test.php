<?php

class Test extends CI_Controller
{

    public function products()
    {
        // 1. Fetch Products
        // NOTE: no longer requires an existing tbl_product_image row - a
        // product with zero uploaded images is still active/purchasable and
        // must not vanish from the storefront; it falls back to the
        // placeholder image below instead.
        $select = "product.*, category.category_name as category_name, product_image.image_path as product_image";
        $join = [
            ['category', 'category.category_id = product.category_id', 'LEFT'],
            ['product_image', 'product_image.product_id = product.product_id', 'LEFT']
        ];
        $products = $this->CommonModel->getRowWithMultiJoin($select, 'product', "product.is_delete = '1'", $join, 'product.product_id ASC, product_image.is_main DESC', '', '1');

        if (!$products) {
            $products = [];
        }

        // If this request is for one specific product (the product-detail
        // page) and it isn't part of the normal active set above (e.g. it
        // was soft-deleted/discontinued), fetch it anyway so a direct link
        // to it still shows the product's info - flagged as discontinued so
        // the page can disable purchase actions - instead of a dead "not
        // found" page. General catalog/listing requests (no product_id) are
        // unaffected by this.
        $reqProductIdForLookup = $this->input->get('product_id');
        $discontinuedProductIds = [];
        if ($reqProductIdForLookup) {
            $alreadyActive = false;
            foreach ($products as $p) {
                if ($p['product_id'] == $reqProductIdForLookup) {
                    $alreadyActive = true;
                    break;
                }
            }
            if (!$alreadyActive) {
                $discontinuedRow = $this->CommonModel->getRowWithMultiJoin($select, 'product', "product.product_id = '" . (int) $reqProductIdForLookup . "'", $join, '', '', 2);
                if ($discontinuedRow) {
                    $products[] = $discontinuedRow;
                    $discontinuedProductIds[$discontinuedRow['product_id']] = true;
                }
            }
        }

        $groupedProducts = [];
        $defaultImage = base_url() . 'assets/placeholder.svg';

        // 2. Fetch All Variants
        $allVariants = $this->CommonModel->getAllRows('product_variants');
        $variantsByProduct = [];
        if ($allVariants) {
            foreach ($allVariants as $v) {
                $variantsByProduct[$v['product_id']][] = $v;
            }
        }

        // 3. Fetch All Color Images
        $allColorImages = $this->CommonModel->getAllRows('product_color_images');
        $colorImagesByProduct = [];
        if ($allColorImages) {
            foreach ($allColorImages as $img) {
                // Determine image URL
                $url = !empty($img['image']) ? base_url() . 'upload/product_color_images/' . $img['image'] : $defaultImage;
                $colorImagesByProduct[$img['product_id']][$img['color']][] = $url;
            }
        }

        // 3.5 Fetch All Product Images
        $reqProductId = $this->input->get('product_id');
        $allProductImagesRows = [];
        if ($reqProductId) {
            $allProductImagesRows = $this->CommonModel->getRowById('product_image', 'product_id', $reqProductId);
        }

        $allImagesByProduct = [];
        if ($allProductImagesRows) {
            foreach ($allProductImagesRows as $img) {
                $allImagesByProduct[$img['product_id']][] = $img['image_path'];
            }
        }

        // Fetch all subcategory names/slugs needed for all products
        $allScIds = [];
        $allSctIds = [];
        foreach ($products as $p) {
            if (!empty($p['sub_category_id'])) {
                $allScIds = array_merge($allScIds, explode(',', $p['sub_category_id']));
            }
            if (!empty($p['sub_category_type_id'])) {
                $allSctIds = array_merge($allSctIds, explode(',', $p['sub_category_type_id']));
            }
        }
        $allScIds = array_unique(array_filter($allScIds));
        $allSctIds = array_unique(array_filter($allSctIds));

        $scMap = [];
        if (!empty($allScIds)) {
            $scData = $this->CommonModel->getRowByWhereIn('sub_category', 'sub_category_id', $allScIds);
            if ($scData) {
                foreach ($scData as $sc) {
                    $scMap[$sc['sub_category_id']] = [
                        'name' => $sc['sub_category_name'],
                        'slug' => url_title($sc['sub_category_name'], '-', true)
                    ];
                }
            }
        }

        $sctMap = [];
        if (!empty($allSctIds)) {
            $sctData = $this->CommonModel->getRowByWhereIn('sub_category_type', 'sub_category_type_id', $allSctIds);
            if ($sctData) {
                foreach ($sctData as $sct) {
                    $sctMap[$sct['sub_category_type_id']] = [
                        'name' => $sct['sub_category_type_name'],
                        'slug' => url_title($sct['sub_category_type_name'], '-', true)
                    ];
                }
            }
        }

        foreach ($products as $row) {
            $productId = $row['product_id'];
            $productImage = !empty($row['product_image']) ? base_url() . "upload/product/" . $row['product_image'] : $defaultImage;

            if (!isset($groupedProducts[$productId])) {

                // Process Variants to get unique sizes and colors
                $sizes = [];
                $colors = [];
                $variations = [];
                // Checkout (Web::checkout) charges product_variants.price for any
                // product that has variants, and only falls back to
                // product.sale_price when no variant is selected. Admin edits to
                // a product's sale_price never cascade to its variant rows, so
                // the two can drift. Deriving the displayed price from the
                // cheapest active variant here keeps listing/detail price in
                // sync with what checkout will actually charge, instead of
                // showing a stale product-level price.
                $minVariantPrice = null;

                if (isset($variantsByProduct[$productId])) {
                    foreach ($variantsByProduct[$productId] as $variant) {
                        if ($variant['is_active'] == 1) {
                            if ($minVariantPrice === null || (float) $variant['price'] < $minVariantPrice) {
                                $minVariantPrice = (float) $variant['price'];
                            }
                            if (!in_array($variant['size'], $sizes) && !empty($variant['size'])) {
                                $sizes[] = $variant['size'];
                            }
                            // Store color info
                            $color = trim($variant['color']);
                            if (!isset($colors[$color]) && !empty($color)) {
                                $colors[$color] = true;

                                // Find representative image for this color
                                $colorImgUrl = $defaultImage;
                                if (isset($colorImagesByProduct[$productId][$color][0])) {
                                    $colorImgUrl = $colorImagesByProduct[$productId][$color][0];
                                } else {
                                    $colorImgUrl = $productImage;
                                }

                                $variations[] = [
                                    'id' => $variant['variant_id'],
                                    'color' => $color,
                                    'colorName' => isset($variant['color_name']) && !empty($variant['color_name']) ? $variant['color_name'] : $color,
                                    'colorCode' => $color,
                                    'colorImage' => $colorImgUrl,
                                    'image' => $colorImgUrl
                                ];
                            }
                        }
                    }
                }

                // Compile all images
                $allProductImages = [];

                if (isset($allImagesByProduct[$productId])) {
                    foreach ($allImagesByProduct[$productId] as $imgFile) {
                        $allProductImages[] = base_url() . "upload/product/" . $imgFile;
                    }
                }

                if (empty($allProductImages)) {
                    $allProductImages[] = $productImage;
                }

                if (isset($colorImagesByProduct[$productId])) {
                    foreach ($colorImagesByProduct[$productId] as $colorName => $imgs) {
                        foreach ($imgs as $imgUrl) {
                            if (!in_array($imgUrl, $allProductImages)) {
                                $allProductImages[] = $imgUrl;
                            }
                        }
                    }
                }

                $pSubCategoriesNames = [];
                $pSubCategoriesSlugs = [];
                if (!empty($row['sub_category_id'])) {
                    $scIds = explode(',', $row['sub_category_id']);
                    foreach ($scIds as $sid) {
                        if (isset($scMap[$sid])) {
                            $pSubCategoriesNames[] = $scMap[$sid]['name'];
                            $pSubCategoriesSlugs[] = $scMap[$sid]['slug'];
                        }
                    }
                }

                $pSubCategoryTypeNames = [];
                $pSubCategoryTypeSlugs = [];
                if (!empty($row['sub_category_type_id'])) {
                    $sctIds = explode(',', $row['sub_category_type_id']);
                    foreach ($sctIds as $stid) {
                        if (isset($sctMap[$stid])) {
                            $pSubCategoryTypeNames[] = $sctMap[$stid]['name'];
                            $pSubCategoryTypeSlugs[] = $sctMap[$stid]['slug'];
                        }
                    }
                }

                $groupedProducts[$productId] = [
                    'id' => $row['product_id'],
                    'category' => $row['category_name'],
                    'subcategory' => $pSubCategoriesNames,
                    'subcategory_type' => $pSubCategoryTypeNames,
                    'type' => url_title($row['category_name'], '-', true),
                    'subcategory_slug' => $pSubCategoriesSlugs,
                    'subcategory_type_slug' => $pSubCategoryTypeSlugs,
                    'name' => $row['product_name'],
                    'gender' => 'both',
                    'new' => false,
                    'sale' => 0,
                    'rate' => 5,
                    'price' => $minVariantPrice !== null ? $minVariantPrice : $row['sale_price'],
                    'originPrice' => $row['market_price'],
                    'brand' => 'Brand',
                    'sold' => '0',
                    'quantity' => $row['max_quantity'],
                    'quantityPurchase' => 1,
                    "sizes" => $sizes,
                    'thumbImage' => $allProductImages,
                    'images' => $allProductImages,
                    'description' => $row['description'],
                    "action" => "quick shop",
                    "slug" => url_title($row['product_name'], '-', true) . '-' . $row['product_id'],
                    "variation" => $variations,
                    "colorImages" => isset($colorImagesByProduct[$productId]) ? $colorImagesByProduct[$productId] : [],
                    "discontinued" => isset($discontinuedProductIds[$productId]),
                    "outOfStock" => $row['is_out_of_stock'] == 1
                ];
            } else {
                // If product has multiple rows (due to joins), handle here if needed. 
                // Current query groups by product_id so we get one row per product, 
                // but we might miss multiple main images if they were joined.
                // Assuming 1-1 for now based on '1' limit/grouping in model or just taking first.
            }
        }

        echo json_encode(array_values($groupedProducts));
    }
}
