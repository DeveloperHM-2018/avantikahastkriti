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
