<?php
defined('BASEPATH') or exit('No direct script access allowed');


$route['default_controller'] = 'Web';
$route['404_override'] = 'Web/not_found';
$route['translate_uri_dashes'] = FALSE;


// === Website ===

$route['products'] = 'Web/products';
$route['products/(:any)/(:any)/(:any)'] = 'Web/products/$1/$2/$3';
$route['products/(:any)/(:any)'] = 'Web/products/$1/$2';
$route['products/(:any)'] = 'Web/products/$1';
$route['wishlist'] = 'Web/wishlist';
$route['cart'] = 'Web/cart';
$route['login'] = 'Web/login';
$route['register'] = 'Web/register';
$route['forgot-password'] = 'Web/forgotPassword';
$route['profile'] = 'Web/profile';
$route['logout'] = 'Auth/logout';
$route['product/(:any)'] = 'Web/product/$1';
$route['product'] = 'Web/product';
$route['faqs'] = 'Web/faqs';
$route['about'] = 'Web/about';
$route['contact'] = 'Web/contact';
$route['checkout'] = 'Web/checkout';
$route['thankyou'] = 'Web/thankyou';
$route['thank'] = 'Web/thank';
$route['reset-password'] = 'Web/resetPassword';
$route['policy/(:any)'] = 'Web/policy/$1';
$route['api/logSession'] = 'Web/logSession';


/////////////////////     Admin     /////////////////

$route['admin'] = 'AdminAuth/admin';
$route['adminLogout'] = 'AdminAuth/adminLogout';

$route['dashboard'] = 'AdminHome/dashboard';
$route['banner'] = 'AdminHome/banner';
$route['promoCode'] = 'AdminHome/promoCode';
$route['setDeliveryCharges'] = 'AdminHome/setDeliveryCharges';
$route['contact_query'] = 'AdminHome/contact_query';
$route['addOnData'] = 'AdminHome/addOnData';
$route['addOnDataAdd'] = 'AdminHome/addOnDataAdd';
$route['metaData'] = 'AdminHome/metaData';
$route['metaDataEdit'] = 'AdminHome/metaDataEdit';
$route['siteSettings'] = 'AdminHome/siteSettings';
$route['mailSmtpSetting'] = 'AdminHome/mailSmtpSetting';
$route['mailTemplateAll'] = 'AdminHome/mailTemplateAll';
$route['mailTemplateEdit'] = 'AdminHome/mailTemplateEdit';
$route['testMail'] = 'AdminHome/testMail';
$route['addOrder'] = 'AdminHome/addOrder';
$route['addOrderSave'] = 'AdminHome/addOrderSave';


//  =>  User

$route['activeUser'] = 'AdminHome/activeUser';
$route['inactiveUser'] = 'AdminHome/inactiveUser';
$route['userStatus/(:any)/(:any)'] = 'AdminHome/userStatus/$1/$2';
$route['userDetails/(:any)'] = 'AdminHome/userDetails/$1';
$route['vendorProductRateUpdate/(:any)'] = 'AdminProduct/vendorProductRateUpdate/$1';
$route['vendorProductRateUpdateSave/(:any)'] = 'AdminProduct/vendorProductRateUpdateSave/$1';

$route['subAdmin'] = 'AdminHome/subAdmin';
$route['addSubAdmin'] = 'AdminHome/addSubAdmin';
$route['subAdminStatus/(:any)/(:any)'] = 'AdminHome/subAdminStatus/$1/$2';

$route['deliveryLocation'] = 'AdminHome/deliveryLocation';
$route['deliveryLocationAdd'] = 'AdminHome/deliveryLocationAdd';

$route['faqAll'] = 'AdminHome/faqAll';
$route['faqAdd'] = 'AdminHome/faqAdd';

// => Orders

$route['allOrders'] = 'AdminHome/allOrders';
$route['getOrderDetails'] = 'AdminHome/getOrderDetails';
$route['acceptOrder'] = 'AdminHome/acceptOrder';
$route['cancelOrder'] = 'AdminHome/cancelOrder';
$route['acceptedOrders'] = 'AdminHome/acceptedOrders';
$route['dispatchOrder/(:any)/(:any)'] = 'AdminHome/dispatchOrder/$1/$2';
$route['shiprocketOrderDetails'] = 'AdminHome/shiprocketOrderDetails';
$route['shipWithShiprocket'] = 'AdminHome/shipWithShiprocket';
$route['syncPaymentStatus'] = 'AdminHome/syncPaymentStatus';

// => Product

$route['categoryAll'] = 'AdminProduct/categoryAll';
$route['categoryAdd'] = 'AdminProduct/categoryAdd';
$route['categoryFeatured/(:any)/(:any)'] = 'AdminProduct/categoryFeatured/$1/$2';
$route['subCategoryAll'] = 'AdminProduct/subCategoryAll';
$route['subCategoryAdd'] = 'AdminProduct/subCategoryAdd';

$route['subCategoryTypeAll'] = 'AdminProduct/subCategoryTypeAll';
$route['subCategoryTypeAdd'] = 'AdminProduct/subCategoryTypeAdd';

$route['getSubCategory'] = 'AdminProduct/getSubCategory';
$route['getSubCategoryType'] = 'AdminProduct/getSubCategoryType';

$route['productAll'] = 'AdminProduct/productAll';
$route['productDetails'] = 'AdminProduct/productDetails';
$route['productAdd'] = 'AdminProduct/productAdd';
$route['productView'] = 'AdminProduct/productView';
$route['productDelete'] = 'AdminProduct/productDelete';
$route['getProductSubCategory'] = 'AdminProduct/getProductSubCategory';
$route['productImageD'] = 'AdminProduct/productImageD';
$route['productImageMain'] = 'AdminProduct/productImageMain';
$route['productStockToggle/(:any)/(:any)'] = 'AdminProduct/productStockToggle/$1/$2';

// ==> Product Variants
$route['productVariants'] = 'AdminProduct/productVariants';
$route['variantAdd'] = 'AdminProduct/variantAdd';
$route['variantEdit'] = 'AdminProduct/variantEdit';
$route['variantDelete'] = 'AdminProduct/variantDelete';
$route['variantToggleStatus/(:any)/(:any)'] = 'AdminProduct/variantToggleStatus/$1/$2';
$route['variantColorImageAdd'] = 'AdminProduct/variantColorImageAdd';
$route['variantColorImageDelete'] = 'AdminProduct/variantColorImageDelete';
$route['productsExcelImport'] = 'AdminProduct/productsExcelImport';

// ==> Product Reviews
$route['submitReview'] = 'Web/submit_review';
$route['productReviews'] = 'AdminProduct/productReviews';
$route['productReviewToggleStatus/(:any)/(:any)'] = 'AdminProduct/productReviewToggleStatus/$1/$2';

// ==> Vendor Portal (vendor-facing, own auth realm)
$route['vendor/register'] = 'Vendor/register';
$route['vendor/login'] = 'Vendor/login';
$route['vendor/logout'] = 'Vendor/logout';
$route['vendor/dashboard'] = 'Vendor/dashboard';
$route['vendor/products'] = 'Vendor/products';
$route['vendor/productAdd'] = 'Vendor/productAdd';
$route['vendor/getSubCategory'] = 'Vendor/getSubCategory';
$route['vendor/getSubCategoryType'] = 'Vendor/getSubCategoryType';
$route['vendor/productImageDelete'] = 'Vendor/productImageDelete';
$route['vendor/orders'] = 'Vendor/orders';
$route['vendor/payouts'] = 'Vendor/payouts';
$route['vendor/profile'] = 'Vendor/profile';

// ==> Vendor Management (admin-side)
$route['vendorAll'] = 'AdminVendor/vendorAll';
$route['vendorAdd'] = 'AdminVendor/vendorAdd';
$route['registerShiprocketPickup'] = 'AdminVendor/registerShiprocketPickup';
$route['vendorApprove/(:any)'] = 'AdminVendor/vendorApprove/$1';
$route['vendorReject/(:any)'] = 'AdminVendor/vendorReject/$1';
$route['vendorSuspend/(:any)'] = 'AdminVendor/vendorSuspend/$1';
$route['vendorActivate/(:any)'] = 'AdminVendor/vendorActivate/$1';
$route['vendorDetails'] = 'AdminVendor/vendorDetails';
$route['vendorViewDocument/(:any)'] = 'AdminVendor/viewDocument/$1';
$route['vendorProductQueue'] = 'AdminVendor/vendorProductQueue';
$route['vendorProductReview'] = 'AdminVendor/vendorProductReview';
$route['vendorPayoutAll'] = 'AdminVendor/vendorPayoutAll';
$route['vendorPayoutCreate'] = 'AdminVendor/vendorPayoutCreate';
$route['vendorPayoutMarkPaid'] = 'AdminVendor/vendorPayoutMarkPaid';

// ==> Inventory
$route['stockAll'] = 'AdminInventory/stockAll';
$route['stockAdjust'] = 'AdminInventory/stockAdjust';
$route['stockClearOverride/(:any)'] = 'AdminInventory/stockClearOverride/$1';
$route['stockLedger'] = 'AdminInventory/stockLedger';
$route['lowStockAll'] = 'AdminInventory/lowStockAll';
$route['outOfStockAll'] = 'AdminInventory/outOfStockAll';
$route['pinnedOrders'] = 'AdminInventory/pinnedOrders';
$route['resolvePinnedOrder'] = 'AdminInventory/resolvePinnedOrder';

// ==> Reports
$route['salesReport'] = 'AdminReports/salesReport';
$route['inventoryReport'] = 'AdminReports/inventoryReport';
$route['purchaseReport'] = 'AdminReports/purchaseReport';
$route['vendorReport'] = 'AdminReports/vendorReport';

// ==> Admin self-service profile
// NOTE: 'profile' is already taken by the customer-facing Web/profile route
// above - must not reuse that key here or it silently overrides it.
$route['adminProfile'] = 'AdminProfile/profile';
$route['updateProfile'] = 'AdminProfile/updateProfile';
$route['changePassword'] = 'AdminProfile/changePassword';

// ==> Admin activity log
$route['activityLogAll'] = 'AdminAudit/activityLogAll';

// ==> Product Returns
$route['returnDashboard'] = 'AdminReturn/returnDashboard';
$route['returnReports'] = 'AdminReturn/returnReports';
$route['returnsExportCsv'] = 'AdminReturn/exportReturnsCsv';
$route['returnList'] = 'AdminReturn/returnList';
$route['returnDetails'] = 'AdminReturn/returnDetails';
$route['approveReturn'] = 'AdminReturn/approveReturn';
$route['rejectReturn'] = 'AdminReturn/rejectReturn';
$route['schedulePickup'] = 'AdminReturn/schedulePickup';
$route['processRefund'] = 'AdminReturn/processRefund';
$route['returnSetting'] = 'AdminReturn/returnSetting';
$route['registerHouseShiprocketPickup'] = 'AdminReturn/registerShiprocketPickup';


///////////////////// API   ///////////////////////

$route['api/stateApi'] = 'UserApi/stateApi';
$route['api/cityApi/(:any)'] = 'UserApi/cityApi/$1';
$route['api/appContent/(:any)'] = 'UserApi/appContent/$1';

$route['api/userSendOTP'] = 'UserApi/userSendOTP';
$route['api/userLogin'] = 'UserApi/userLogin';
$route['api/userProfileCreate'] = 'UserApi/userProfileCreate';
$route['api/userProfileUpdate'] = 'UserApi/userProfileUpdate';
$route['api/userViewProfile'] = 'UserApi/userViewProfile';

$route['api/userAddress'] = 'UserApi/userAddress';

$route['api/dashboardApi'] = 'UserApi/dashboardApi';
$route['api/getCategory/(:any)'] = 'UserApi/getCategory/$1';
$route['api/getProduct/(:any)'] = 'UserApi/getProduct/$1';
$route['api/getBrandByProduct/(:any)'] = 'UserApi/getBrandByProduct/$1';
$route['api/searchProduct'] = 'UserApi/searchProduct';
$route['api/getSubCategoryWiseProduct'] = 'UserApi/getSubCategoryWiseProduct';
$route['api/getSingleProduct/(:any)'] = 'UserApi/getSingleProduct/$1';


$route['api/getDeliveryCharge'] = 'UserApi/getDeliveryCharge';
$route['api/promoCodeApply'] = 'UserApi/promoCodeApply';
$route['api/createOrder'] = 'UserApi/createOrder';
$route['api/orderTransactionStatus'] = 'UserApi/orderTransactionStatus';
$route['api/orderHistory'] = 'UserApi/orderHistory';
$route['api/submitReturnRequest'] = 'UserApi/submitReturnRequest';
$route['api/returnStatus/(:any)'] = 'UserApi/returnStatus/$1';

$route['api/checkDeliveryLocation'] = 'UserApi/checkDeliveryLocation';

///////////////////// Razorpay Magic Checkout   ///////////////////////
// These three URLs must be public (no auth) and pasted into the Razorpay
// Dashboard -> Magic Checkout -> Setup & Settings.
$route['api/magicCheckout/shippingInfo'] = 'Web/magicShippingInfo';
$route['api/magicCheckout/getPromotions'] = 'Web/magicGetPromotions';
$route['api/magicCheckout/applyPromotions'] = 'Web/magicApplyPromotions';

///////////////////// Razorpay Payment Webhook   ///////////////////////
// Must be public (no auth) - paste into Razorpay Dashboard -> Settings ->
// Webhooks, subscribed to payment.captured, order.paid, payment.failed.
$route['api/razorpay/webhook'] = 'Web/razorpayWebhook';

///////////////////// Shiprocket Return Webhook   ///////////////////////
// Must be public (no auth) - paste into the Shiprocket panel's webhook settings
// for reverse-pickup/return shipment status updates.
$route['api/shiprocket/returnWebhook'] = 'Web/shiprocketReturnWebhook';
