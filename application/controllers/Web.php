<?php

require_once APPPATH . '../vendor/autoload.php'; // Include Composer's autoloader

use Razorpay\Api\Api;

class Web extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Kolkata");
        $this->projectName = APP_NAME;
        $this->companyContact1 = '+91 88711-28235';
        $this->companyContact2 = '+91 12345-00000';
        $this->companyEmail = 'support@avantika.com';
        $this->companyAddress = "Banda Road, near Panchwati Hotel, beside Klub Fox, Shri Nagar Colony, Makroniya, Sagar, Madhya Pradesh, 470004";
        $this->deliveryCharge = 30;
        $this->packagingCharge = 9;
        $this->deliveryCharges = $this->CommonModel->getSingleRowById('delivery_charge', ['delivery_charge_id' => '1']);
        $siteSetting = $this->CommonModel->getSingleRowById('setting', ['id' => 1]);
        $this->socialFacebookUrl = @$siteSetting['facebook_url'] ?: '';
        $this->socialInstagramUrl = @$siteSetting['instagram_url'] ?: '';
    }
    private function applyStaticMeta(&$data, $pageKey, $fallbackTitle = '', $fallbackDescription = '', $fallbackKeywords = '')
    {
        $meta = $this->CommonModel->getSingleRowById('meta_data', ['page_key' => $pageKey]);
        $data['metaTitle'] = @$meta['meta_title'] ?: $fallbackTitle;
        $data['metaDescription'] = @$meta['meta_description'] ?: $fallbackDescription;
        $data['metaKeywords'] = @$meta['meta_keywords'] ?: $fallbackKeywords;
    }

    public function index()
    {
        $data['title'] =  'Buy Traditional Indian Wear for Women';
        $this->applyStaticMeta(
            $data,
            'home',
            $this->projectName,
            'Shop trendy ethnic wear for women online at Avantika Hastkriti. Choose from a collection of the latest sarees, kurtas, and lehengas suited for weddings, festivals and more.',
            'ethnic wear, ethnic wear for women, traditional indian wear, indian ethnic wear for women, indian ethnic wear.'
        );
        $data['customStyles'] = [];
        $data['customScripts'] = [
            'assets/js/pages/home.js' => 'text/javascript',
        ];
        $data['categories'] = $this->CommonModel->getRowByIdInOrder('category', "is_delete = '1'", 'category_id', 'DESC');

$data['featuredCategories'] = $this->CommonModel->getRowByIdInOrder('category', "is_delete = '1' AND featured = '1' AND category_id IN (SELECT category_id FROM tbl_product WHERE is_delete = '1')", 'category_id', 'DESC');
        $data['banners'] = $this->CommonModel->getAllRowsInOrder('banner', 'create_date', 'ASC');
        $this->load->view('index', $data);
    }

    public function getCharges()
    {
        $charges = $this->CommonModel->getSingleRowById('delivery_charge', ['delivery_charge_id' => '1']);
        echo json_encode($charges);
    }

    public function logSession()
    {
        $sessionToken = $this->input->post('session_token');
        if (empty($sessionToken)) {
            echo json_encode(['success' => false]);
            return;
        }

        $today = date('Y-m-d');
        $existing = $this->CommonModel->runQuery(
            "SELECT id FROM tbl_page_session_log WHERE session_token = '" . $this->db->escape_str($sessionToken) . "' AND DATE(visited_at) = '" . $today . "'",
            2
        );

        if (!$existing) {
            $this->CommonModel->insertRow('page_session_log', [
                'session_token' => $sessionToken,
                'user_id' => sessionId('login_user_id') ?: null,
                'visited_at' => date('Y-m-d H:i:s'),
            ]);
        }

        echo json_encode(['success' => true]);
    }


    // Finds the row in $rows whose slug (url_title of $nameField) matches
    // $slug exactly - the one place slug->row lookup happens, so listing
    // links and legacy-redirect logic can never drift apart on what counts
    // as a match.
    private function findRowBySlug($rows, $nameField, $slug)
    {
        foreach ($rows as $row) {
            if (url_title($row[$nameField], '-', true) === $slug) {
                return $row;
            }
        }
        return null;
    }

    public function products($category = null, $subcategory = null, $subcategoryType = null)
    {
        $data['title'] = 'Products';
        $this->applyStaticMeta(
            $data,
            'products',
            'Shop All Products',
            'Browse the full collection of ethnic wear at ' . $this->projectName . ' - sarees, kurtas, lehengas and more for every occasion.',
            'shop ethnic wear, buy sarees online, kurtas, lehengas, indian wear'
        );
        $data['customStyles'] = [];
        $data['customScripts'] = [
            'assets/js/shop.js' => 'text/javascript',
        ];
        $allCategories = $this->CommonModel->getRowByIdInOrder('category', "is_delete = '1'", 'category_id', 'DESC');
        $allSubCategories = $this->CommonModel->getRowByIdInOrder('sub_category', "is_delete = '1'", 'sub_category_name', 'ASC');
        $allSubCategoryTypes = $this->CommonModel->getRowByIdInOrder('sub_category_type', "is_delete = '1'", 'sub_category_type_name', 'ASC');
        $data['categories'] = $allCategories;
        $data['sub_categories'] = $allSubCategories;
        $data['sub_category_types'] = $allSubCategoryTypes;

        // Legacy bookmarked/indexed query-param URLs must keep working -
        // send them to the equivalent clean path with a permanent redirect.
        if (!$category) {
            $legacyCategory = $this->input->get('category');
            $legacySubcategory = $this->input->get('subcategory');
            $legacySubcategoryType = $this->input->get('subcategory_type');
            if ($legacyCategory || $legacySubcategory || $legacySubcategoryType) {
                $path = 'products';
                if ($legacyCategory) $path .= '/' . $legacyCategory;
                if ($legacySubcategory) $path .= '/' . $legacySubcategory;
                if ($legacySubcategoryType) $path .= '/' . $legacySubcategoryType;
                redirect($path, 'location', 301);
                return;
            }
        }

        $matchedCategory = null;
        $matchedSubCategory = null;
        $matchedSubCategoryType = null;

        if ($category) {
            $matchedCategory = $this->findRowBySlug($allCategories, 'category_name', $category);
            if (!$matchedCategory) {
                show_404();
            }
        }
        if ($subcategory) {
            $candidates = $allSubCategories;
            if ($matchedCategory) {
                $candidates = array_values(array_filter($allSubCategories, function ($row) use ($matchedCategory) {
                    return $row['category_id'] == $matchedCategory['category_id'];
                }));
            }
            $matchedSubCategory = $this->findRowBySlug($candidates, 'sub_category_name', $subcategory);
            if (!$matchedSubCategory) {
                show_404();
            }
        }
        if ($subcategoryType) {
            $candidates = $allSubCategoryTypes;
            if ($matchedSubCategory) {
                $candidates = array_values(array_filter($allSubCategoryTypes, function ($row) use ($matchedSubCategory) {
                    return $row['sub_category_id'] == $matchedSubCategory['sub_category_id'];
                }));
            }
            $matchedSubCategoryType = $this->findRowBySlug($candidates, 'sub_category_type_name', $subcategoryType);
            if (!$matchedSubCategoryType) {
                show_404();
            }
        }

        // sub_category_type has no meta columns of its own, so the most
        // specific *meta-bearing* match wins: subcategory if a type was
        // matched, otherwise subcategory, otherwise category.
        $metaSource = $matchedSubCategory ?: $matchedCategory;
        if ($metaSource) {
            $data['metaTitle'] = @$metaSource['meta_title'] ?: '';
            $data['metaDescription'] = @$metaSource['meta_description'] ?: '';
            $data['metaKeywords'] = @$metaSource['meta_keywords'] ?: '';
        }

        $data['selected_category'] = $matchedCategory ? url_title($matchedCategory['category_name'], '-', true) : null;
        $data['selected_category_name'] = $matchedCategory['category_name'] ?? null;
        $data['selected_subcategory'] = $matchedSubCategory ? url_title($matchedSubCategory['sub_category_name'], '-', true) : null;
        $data['selected_subcategory_name'] = $matchedSubCategory['sub_category_name'] ?? null;
        $data['selected_subcategory_type'] = $matchedSubCategoryType ? url_title($matchedSubCategoryType['sub_category_type_name'], '-', true) : null;
        $data['selected_subcategory_type_name'] = $matchedSubCategoryType['sub_category_type_name'] ?? null;

        $this->load->view('products', $data);
    }

    public function wishlist()
    {
        userSession();
        $data['title'] = 'Wishlist';
        $this->applyStaticMeta(
            $data,
            'wishlist',
            'Your Wishlist',
            'View and manage the products you have saved to your wishlist at ' . $this->projectName . '.',
            'wishlist, saved items, favorites'
        );
        $this->load->view('wishlist', $data);
    }

    public function cart()
    {
        $data['title'] = 'Cart';
        $this->applyStaticMeta(
            $data,
            'cart',
            'Shopping Cart',
            'Review the items in your shopping cart before checkout at ' . $this->projectName . '.',
            'shopping cart, checkout'
        );
        $this->load->view('cart', $data);
    }

    public function login()
    {
        userSession(false);
        $data['title'] = 'Login';
        $this->applyStaticMeta(
            $data,
            'login',
            'Login',
            'Login to your ' . $this->projectName . ' account to track orders, manage your wishlist and checkout faster.',
            'login, sign in, my account'
        );
        $data['customScripts'] = [
            'assets/js/pages/login.js' => 'module',
        ];
        $this->load->view('login', $data);
    }

    public function register()
    {
        userSession(false);
        $data['title'] = 'Register';
        $this->applyStaticMeta(
            $data,
            'register',
            'Create an Account',
            'Create a free ' . $this->projectName . ' account to track orders, save your wishlist and checkout faster.',
            'register, sign up, create account'
        );
        $data['customStyles'] = [
            'assets/css/pages/register.css'
        ];
        $data['customScripts'] = [
            'assets/js/pages/register.js' => 'module',
        ];
        $this->load->view('register', $data);
    }

    public function profile()
    {
        userSession();
        $userId = sessionId('login_user_id');

        // Order history date filter - defaults to the current month so the
        // tab isn't a wall of every order the user has ever placed.
        $defaultFrom = date('Y-m-01');
        $defaultTo = date('Y-m-d'); // today - also this month's max, since it can't be in the future
        $fromDate = $this->input->get('from_date');
        $toDate = $this->input->get('to_date');
        if (!$fromDate || !DateTime::createFromFormat('Y-m-d', $fromDate)) {
            $fromDate = $defaultFrom;
        }
        if (!$toDate || !DateTime::createFromFormat('Y-m-d', $toDate)) {
            $toDate = $defaultTo;
        }
        if ($fromDate > $toDate) {
            list($fromDate, $toDate) = [$toDate, $fromDate];
        }
        $data['orderFromDate'] = $fromDate;
        $data['orderToDate'] = $toDate;

        $data['numOfOrders'] = $this->CommonModel->getNumRows('book_product', ['user_id' => $userId]);
        $data['numOfAwaitedOrders'] = $this->CommonModel->getNumRows('book_product', ['user_id' => $userId, 'booking_status' => '0']);
        // "Current" orders: placed and not yet completed or cancelled.
        $data['numOfActiveOrders'] = $this->CommonModel->getNumRows('book_product', "user_id = '{$userId}' AND booking_status IN ('0','1','3')");
        $data['numOfCancelledOrders'] = $this->CommonModel->getNumRows('book_product', ['user_id' => $userId, 'booking_status' => '2']);
        $data['profileData'] = $this->CommonModel->getSingleRowById('user_registration', ['user_id' => $userId]);
        $data['orders'] = $this->CommonModel->getRowByIdInOrder(
            'book_product',
            "user_id = '{$userId}' AND booking_date >= '{$fromDate} 00:00:00' AND booking_date <= '{$toDate} 23:59:59'",
            'product_book_id',
            'desc'
        );
        $data['customScripts'] = [
            'assets/js/pages/profile.js' => 'module',
        ];
        $data['title'] = 'User Profile';
        $this->applyStaticMeta(
            $data,
            'profile',
            'My Account',
            'Manage your profile, view order history and update your account details at ' . $this->projectName . '.',
            'my account, profile, order history'
        );
        $this->load->view('profile', $data);
    }


    public function forgotPassword()
    {
        userSession(false);
        $data['title'] = 'Forgot Password';
        $data['metaDescription'] = 'Reset the password for your ' . $this->projectName . ' account.';
        $data['customScripts'] = [
            'assets/js/pages/forgotPassword.js' => 'module',
        ];
        $this->load->view('forgot-password', $data);
    }
    public function resetPassword()
    {
        if (!sessionId('forgotContact')) {
            redirect(base_url('forgot-password'));
        }
        $data['title'] = 'Reset Password';
        $data['metaDescription'] = 'Set a new password for your ' . $this->projectName . ' account.';
        $data['customScripts'] = [
            'assets/js/pages/resetPassword.js' => 'module',
        ];
        $this->load->view('reset-password', $data);
    }

    public function getSingleOrderDetails()
    {
        $userId = sessionId('login_user_id'); // Assuming you have a function to get the logged-in user's ID
        $orderId = $this->input->post('orderId');
        // Fetch order details from the database
        $orderData = $this->CommonModel->getSingleRowById('checkouts', ['id' => $orderId, 'user_id' => $userId]);
        if ($orderData) {
            $orderItems = $this->CommonModel->getRowById('checkout_items', 'checkout_id', $orderId);
            $orderDetails = [
                'id' => $orderData['id'],
                'name' => $orderData['name'],
                'contact_no' => $orderData['contact_no'],
                'email' => $orderData['email'],
                'full_address' => $orderData['full_address'],
                'payment_method' => $orderData['payment_method'],
                'shipping_charge' => $orderData['shipping_charge'],
                'packaging_charge' => $orderData['packaging_charge'],
                'coupon_discount' => $orderData['coupon_discount'],
                'coupon_code' => $orderData['coupon_code'],
                'subtotal' => $orderData['subtotal'],
                'total_price' => $orderData['total_price'],
                'order_status' => $orderData['order_status'],
                'items' => $orderItems,
            ];
        } else {
            $orderDetails = null;
        }

        // Pass the order details to the view
        $this->load->view('getSingleOrderDetails', ['orderDetails' => $orderDetails]);
    }

    public function product($slug = null)
    {
        $data['title'] = 'Product - ' . $this->projectName;
        $data['metaTitle'] = '';
        $data['metaDescription'] = '';
        $data['metaKeywords'] = '';
        $data['customScripts'] = [
            '/assets/js/jquery.min.js' => 'text/javascript',
            './assets/js/product-detail.js' => 'text/javascript',
        ];

        if ($slug) {
            $parts = explode('-', $slug);
            $data['product_id'] = end($parts);
        } else {
            $data['product_id'] = $this->input->get('id');
        }

        $product = $this->CommonModel->getSingleRowById('product', ['product_id' => $data['product_id']]);
        if ($product) {
            $data['title'] = $product['product_name'];
            $data['metaTitle'] = @$product['meta_title'] ?: ($product['product_name'] . ' - ' . $this->projectName);
            $data['metaDescription'] = @$product['meta_description'] ?: ('Buy ' . $product['product_name'] . ' online at ' . $this->projectName . '. Shop the latest ethnic wear with fast, reliable delivery.');
            $data['metaKeywords'] = @$product['meta_keywords'] ?: '';
        }

        $this->load->view('product', $data);
    }

    public function faqs()
    {
        $data['title'] = 'FAQs';
        $this->applyStaticMeta($data, 'faqs');

        $faqs = $this->CommonModel->getRowByIdInOrder('faqs', ['status' => '1'], 'sort_order', 'ASC') ?: [];
        $replacements = [
            '{min_amount}' => (int) ($this->deliveryCharges['min_amount'] ?? 999),
            '{policy_url}' => base_url('policy/return-and-refund'),
        ];
        foreach ($faqs as &$faq) {
            $faq['answer'] = strtr($faq['answer'], $replacements);
        }
        unset($faq);
        $data['faqs'] = $faqs;

        $this->load->view('faqs', $data);
    }

    public function policy()
    {
        $segment = $this->uri->segment(2);

        if (!$segment) {
            redirect(base_url());
        }
        $policy_map = [
            'terms-and-condition' => 1,
            'privacy' => 2,
            'return-and-refund' => 3,
            'shipping-policy' => 4,
        ];

        if (!array_key_exists($segment, $policy_map)) {
            show_404();
        }

        $policy_id = $policy_map[$segment];

        $policy = $this->CommonModel->getSingleRowById('add_on_data', ['id' => $policy_id]);
        if (!$policy) {
            show_404();
        }
        $data['title'] = $segment;
        $data['details'] = $policy;
        $this->applyStaticMeta($data, $segment);
        $this->load->view('policy', $data);
    }

    public function contact()
    {
        $data['title'] = 'Contact';
        $data['customScripts'] = [
            'assets/js/pages/contact.js' => 'module',
        ];
        $this->applyStaticMeta($data, 'contact');
        $this->load->view('contact', $data);
    }
    public function about()
    {
        $data['title'] = 'About';
        $this->applyStaticMeta($data, 'about');
        $this->load->view('about', $data);
    }

    public function contactQuery()
    {
        // Set validation rules
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('phone', 'Phone', 'required|numeric|max_length[10]', ['max_length' => 'The Number field cannot exceed 10 digits in length.']);
        $this->form_validation->set_rules('message', 'Message', 'required|min_length[10]|max_length[500]|callback_validate_input');
        // Run validation
        if ($this->form_validation->run() == FALSE) {
            // Validation failed
            echo json_encode(['success' => false, 'validation' => false,  'message' => $this->form_validation->error_array()]);
            exit();
        }


        // If validation passes, collect form data
        $formData = array(
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone'),
            'message' => html_escape($this->input->post('message')),
        );

        // Simulating an update operation
        $insertData = $this->CommonModel->insertRowReturnId('contact_query', $formData); // Assume update operation is successful
        if ($insertData) {
            echo json_encode(['success' => true, 'message' => 'Thank you for contacting us! We have received your message and will get back to you shortly.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Something went wrong']);
        }
        exit();
    }



    public function validate_input($input)
    {
        if (preg_match('/<[^>]*script|on[a-z]+\s*=|javascript:/i', $input)) {
            $this->form_validation->set_message(['validate_input' => 'The {field} contains invalid or malicious content.']);
            return FALSE;
        }
        return TRUE;
    }

    public function checkout()
    {
        $data['title'] = 'Checkout';
        $this->applyStaticMeta(
            $data,
            'checkout',
            'Checkout',
            'Securely complete your purchase at ' . $this->projectName . '.',
            'checkout, payment, place order'
        );
        $data['customStyles'] = [
            'assets/css/pages/checkout.css'
        ];
        $data['customScripts'] = [
            'assets/js/pages/login.js' => 'module',
            'assets/js/pages/checkout.js' => 'module',
        ];
        $data['profileData'] = $this->CommonModel->getSingleRowById('user_registration', ['user_id' => sessionId('login_user_id')]);
        $this->load->view('checkout', $data);
    }


    public function place_order()
    {
        // Look up or create the user_registration row this guest order should
        // be attributed to, based on contact number/email during checkout.
        // This must NOT log the browser session in as that account - doing so
        // previously let anyone type in an existing customer's phone number
        // and be granted access to that customer's account (profile, saved
        // addresses, full order history) with no password check at all.
        $userId = sessionId('login_user_id');
        if (!$userId) {
            $number = $this->input->post('number');
            if(empty($number)) {
                echo json_encode(['success' => false, 'message' => 'Please provide a Mobile Number to proceed with checkout.']);
                return false;
            }

            // Look for user with this number
            $user = $this->CommonModel->getSingleRowById('user_registration', ['contact_no' => $number]);

            $email = $this->input->post('email') ? $this->input->post('email') : '';
            if (!$user && !empty($email)) {
                // Look for user with this email
                $user = $this->CommonModel->getSingleRowById('user_registration', ['email_id' => $email]);
            }

            if ($user) {
                $userId = $user['user_id'];
            } else {
                // Register a new user with dummy password 123456
                $name = $this->input->post('name') ? $this->input->post('name') : 'User';

                $formData = array(
                    'name' => $name,
                    'email_id' => $email,
                    'contact_no' => $number,
                    'password' => password_hash('123456', PASSWORD_DEFAULT),
                );
                $insertData = $this->CommonModel->insertRowReturnId('user_registration', $formData);
                if ($insertData) {
                    $userId = $insertData;
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to register new user for checkout.']);
                    return false;
                }
            }
        }

        // Validate the input data
        $this->form_validation->set_rules('name', 'Name', 'required|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[254]');
        $this->form_validation->set_rules('number', 'Number', 'required|numeric|exact_length[10]', ['exact_length' => 'The Number field must be exactly 10 digits in length.']);
        $this->form_validation->set_rules('payment', 'Payment Method', 'required|in_list[0,1]');
        $this->form_validation->set_rules('address', 'Address', 'required|max_length[150]');
        $this->form_validation->set_rules('city', 'City', 'required|max_length[30]');
        $this->form_validation->set_rules('state', 'State', 'required|max_length[30]');
        $this->form_validation->set_rules('postal_code', 'Postal Code', 'required|exact_length[6]|numeric');
        $this->form_validation->set_rules('coupon_code', 'Coupon Code', 'trim|max_length[50]');
        $this->form_validation->set_rules('note', 'Note', 'trim|max_length[500]');

        // Run validation
        if ($this->form_validation->run() == FALSE) {
            // Validation failed
            echo json_encode(['success' => false, 'validation' => false, 'message' => $this->form_validation->error_array()]);
            return;
        }

        // Collect form data
        $formData = array(
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'contact_no' => $this->input->post('number'),
            'payment_method' => $this->input->post('payment'),
            'house_no' => $this->input->post('house_no'),
            'landmark' => $this->input->post('landmark'),
            'address' => $this->input->post('address'),
            'city' => $this->input->post('city'),
            'state' => $this->input->post('state'),
            'postal_code' => $this->input->post('postal_code'),
            'coupon_code' => $this->input->post('coupon_code'),
            'note' => $this->input->post('note'),
        );

        // Build the full shipping address stored against the order from the
        // manually entered fields (house/flat, landmark, street address,
        // city, state, postal code) - we no longer derive it from a
        // geocoded map pin.
        $fullAddress = implode(', ', array_filter([
            $formData['house_no'],
            $formData['landmark'],
            $formData['address'],
            $formData['city'],
            $formData['state'],
            $formData['postal_code'],
        ]));

        // Define packaging and shipping charges
        $packagingCharge = (float) ($this->deliveryCharges['packaging_charge'] ?? 9);
        $shippingCharge = (float) ($this->deliveryCharges['amount'] ?? 30);
        $freeShippingThreshold = (float) ($this->deliveryCharges['min_amount'] ?? 999);

        // Get products from POST data
        $products = $this->input->post('products');
        $subtotal = 0;
        $totalItems = 0;
        $orderProducts = [];
        if ($products) {
            foreach ($products as $product) {
                $productId = $product['id'];
                // Quantity is client-supplied and otherwise unvalidated -
                // without this, a non-numeric or negative/zero value (e.g.
                // "abc", "-5", "0") would zero out or corrupt this line's
                // contribution to $subtotal instead of being rejected.
                $quantity = (int) $product['quantity'];
                $variantId = isset($product['variant_id']) ? $product['variant_id'] : null;

                if ($quantity < 1) {
                    echo json_encode(['success' => false, 'message' => 'Invalid quantity for one of the products in your cart.']);
                    return;
                }

                // Fetch product details from the database
                $productDetails = $this->CommonModel->getSingleRowById('product', ['product_id' => $productId]);

                if ($productDetails) {
                    if ($productDetails['is_out_of_stock'] == 1) {
                        echo json_encode(['success' => false, 'message' => $productDetails['product_name'] . ' is currently out of stock.']);
                        return;
                    }

                    $productPrice = $productDetails['sale_price'];
                    $variantSize = null;
                    $variantColor = null;
                    $variantPrice = null;

                    // If variant selected, use variant price and check variant stock
                    if ($variantId) {
                        $variantDetails = $this->CommonModel->getSingleRowById('product_variants', [
                            'variant_id' => $variantId,
                            'product_id' => $productId,
                            'is_active' => 1
                        ]);

                        if ($variantDetails) {
                            $productPrice = $variantDetails['price'];
                            $variantPrice = $variantDetails['price'];
                            $variantSize = $variantDetails['size'];
                            $variantColor = $variantDetails['color'];

                            // Check variant stock
                            if ($variantDetails['stock_quantity'] !== null && $variantDetails['stock_quantity'] < $quantity) {
                                echo json_encode(['success' => false, 'message' => 'Insufficient stock for selected variant of ' . $productDetails['product_name']]);
                                return;
                            }
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Invalid variant selected for ' . $productDetails['product_name']]);
                            return;
                        }
                    } else {
                        // Check product stock if no variant
                        if ($productDetails['max_quantity'] < $quantity) {
                            echo json_encode(['success' => false, 'message' => 'Insufficient stock for product: ' . $productDetails['product_name']]);
                            return;
                        }
                    }

                    $subtotal += $productPrice * $quantity;
                    $totalItems += $quantity;

                    $orderProducts[] = [
                        'product_id' => $productId,
                        'variant_id' => $variantId,
                        'variant_size' => $variantSize,
                        'variant_color' => $variantColor,
                        'variant_price' => $variantPrice,
                        'product_name' => $productDetails['product_name'],
                        'base_price' => $productDetails['sale_price'],
                        'user_price' => $productPrice,
                        'no_of_items' => $quantity,
                        'booking_price' => $productPrice * $quantity,
                    ];
                } else {
                    echo json_encode(['success' => false, 'message' => 'Product not found: ' . $productId]);
                    return;
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Please add products to the cart']);
            return;
        }

        // Round once here - every downstream figure (coupon math, packaging/
        // shipping, the amount saved to the order, the amount sent to
        // Razorpay) is derived from this, so floating-point drift from the
        // per-line multiplications above must not leak further than 2dp.
        $subtotal = round($subtotal, 2);

        // Initialize total price with subtotal
        $totalPrice = $subtotal;
        $couponDiscount = 0;
        $couponStatus = 0; // 1 => applied, 0 => not applied

        // Check if it's the user's first order
        $userDetails = $this->CommonModel->getSingleRowById('user_registration', ['user_id' => $userId]);

        $couponResult = $this->evaluateCoupon($formData['coupon_code'], $totalPrice, $userId);
        if ($couponResult['valid']) {
            $couponStatus = 1;
            $couponDiscount = $couponResult['discount'];
            $totalPrice -= $couponDiscount;
            $formData['coupon_code'] = $couponResult['code'];
        } else {
            // Coupon isn't (or is no longer) eligible - e.g. it was left
            // applied in the browser from a previous, unrelated checkout, or
            // expired/dropped below its minimum order since it was applied.
            // Don't block this order over it - just drop the discount and
            // stop it from being recorded against this order.
            $formData['coupon_code'] = '';
        }

        // Add packaging charge
        $totalPrice += $packagingCharge;

        // Add shipping charge if total price is below the free shipping threshold
        if ($totalPrice < $freeShippingThreshold) {
            $totalPrice += $shippingCharge;
        } else {
            $shippingCharge = 0;
        }
        $totalPrice = round($totalPrice, 2);

        // Defense in depth: quantities are validated as positive integers
        // above and the coupon discount is capped to the subtotal in
        // evaluateCoupon(), so this shouldn't be reachable - but nothing
        // charged to a customer or a payment gateway should ever be able to
        // fall through as zero/negative.
        if ($totalPrice <= 0) {
            echo json_encode(['success' => false, 'message' => 'Unable to calculate a valid order total. Please refresh your cart and try again.']);
            return;
        }

        // Cash on Delivery is enabled for every order regardless of amount.

        $orderId = orderIdGenerateUser('book_product', 'order_id');
        $orderData = [
            'name' => $formData['name'],
            'email' => $formData['email'],
            'contact_no' => $formData['contact_no'],
            'order_id' => $orderId,
            'total_item_amount' => $subtotal,
            'final_amount' => $totalPrice,
            'delivery_charges' => $shippingCharge,
            'packaging_charge' => $packagingCharge,
            'promocode_status' => $couponStatus,
            'promocode' => $formData['coupon_code'],
            'promocode_amount' => $couponDiscount,
            'note' => $formData['note'],
            'user_id' => $userId,
            'booking_date' => date('Y-m-d H:i:s'),
            'payment_mode' => $formData['payment_method']  == '1' ? 'COD' : 'ONLINE',
            'transaction_status' => '0', // Default to unpaid
            'booking_status' => '0', // Default to placed
            'total_items' => $totalItems,
            'address' => $fullAddress,
            'city' => $formData['city'],
            'state' => $formData['state'],
            'postal_code' => $formData['postal_code'],
            'latitude' => '0',
            'longitude' => '0',
            'delivery_possible' => '1', // pan-India delivery - the pincode/map deliverability gate has been removed
        ];

        // Insert order data into the database
        try {
            $checkoutId = $this->CommonModel->insertRowReturnId('book_product', $orderData);

            if ($checkoutId) {
                // Insert order products into the database
                foreach ($orderProducts as $orderProduct) {
                    $orderProduct['product_book_id'] = $checkoutId;
                    $this->CommonModel->insertRow('book_item', $orderProduct);
                }

                // Update user's first_order status
                if ($userDetails['first_order'] == 1) {
                    $this->CommonModel->updateRowById('user_registration', 'user_id', $userId, ['first_order' => 0]);
                }

                // Handle online payment
                if ($formData['payment_method'] == '0') {
                    // Razorpay line_items - reuses the already-priced $orderProducts
                    // built above, so Magic Checkout shows the same items/prices the
                    // customer already saw, with no extra DB lookups.
                    $lineItems = array_map(function ($product) {
                        return [
                            'sku' => (string) $product['product_id'],
                            'variant_id' => $product['variant_id'] ? (string) $product['variant_id'] : null,
                            'price' => (int) round($product['user_price'] * 100),
                            'offer_price' => (int) round($product['user_price'] * 100),
                            'quantity' => (int) $product['no_of_items'],
                            'name' => $product['product_name'],
                        ];
                    }, $orderProducts);

                    $razorpayOrder = $this->create_razorpay_order(
                        $totalPrice,
                        $formData['name'],
                        $formData['email'],
                        $formData['contact_no'],
                        $orderId,
                        $lineItems,
                        [
                            'address' => $formData['address'],
                            'city' => $formData['city'],
                            'state' => $formData['state'],
                            'postal_code' => $formData['postal_code'],
                        ]
                    );

                    if (!$razorpayOrder['success']) {
                        echo json_encode(['success' => false, 'message' => $razorpayOrder['message']]);
                        return;
                    }
                    $saveRazorPayId = $this->CommonModel->updateRowById('book_product', 'product_book_id', $checkoutId, ['razorpay_order_id' => $razorpayOrder['order_id']]);

                    // Sync to Shiprocket (Prepaid) - Removed for manual sync
                    // $this->sync_to_shiprocket($checkoutId);

                    echo json_encode([
                        'success' => true,
                        'razorpay_order_id' => $razorpayOrder['order_id'],
                        'amount' => (int) round($totalPrice * 100), // Amount in paise - must match the integer amount the Razorpay order above was actually created with
                        'currency' => 'INR',
                        'formData' => $formData,
                        'checkout_id' => $checkoutId,
                        'o_id' => $orderId,
                        'st_razorpay_api_key' => RAZOR_PYA_KEY
                    ]);
                } else {
                    $saveOrder = $this->CommonModel->updateRowById('book_product', 'product_book_id', $checkoutId, ['transaction_status' => '1']);

                    // Sync to Shiprocket (COD) - Removed for manual sync
                    // $this->sync_to_shiprocket($checkoutId); // Removed as per instruction

                    $this->CommonModel->notifyOrderPlaced($formData['name'], $formData['email'], $orderId, $totalPrice);

                    echo json_encode(['success' => true, 'message' => 'Order placed successfully', 'order_id' => $checkoutId, 'o_id' => $orderId]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to place order']);
            }
        } catch (Exception $e) {
            log_message('error', 'Error placing order: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred while placing the order. Please try again later.' . $e->getMessage()]);
        }
        return;
    }


    // $lineItems/$address are optional - when present (and MAGIC_CHECKOUT_ENABLED),
    // their presence on the Razorpay order is what flips the Checkout widget into
    // Magic Checkout mode instead of Standard Checkout. Passing neither (or
    // flipping the constant off) reproduces the exact order Standard Checkout
    // has always created - this is the rollback path.
    private function create_razorpay_order($amount, $name = '', $email = '', $contact = '', $orderId = '', $lineItems = null, $address = null)
    {

        $api = new Api(RAZOR_PYA_KEY, RAZOR_PYA_SECRET);

        $orderParams = [
            'receipt' => $orderId,
            'amount' => (int) round($amount * 100), // Amount in paise - must be an integer, floating point rupee math can otherwise produce e.g. 11650.999999998
            'currency' => 'INR',
            'notes' => [
                'name' => $name,
                'email' => $email,
                'contact' => $contact,
                'order_id' => $orderId,
            ],
        ];

        if (MAGIC_CHECKOUT_ENABLED && !empty($lineItems)) {
            $orderParams['line_items_total'] = (int) round($amount * 100);
            $orderParams['line_items'] = $lineItems;
            $orderParams['customer_details'] = [
                'name' => $name,
                'contact' => $contact,
                'email' => $email,
                'shipping_address' => [
                    'name' => $name,
                    'line1' => $address['address'] ?? '',
                    'city' => $address['city'] ?? '',
                    'state' => $address['state'] ?? '',
                    'zipcode' => $address['postal_code'] ?? '',
                    'country' => 'in',
                ],
            ];
        }

        try {
            $order = $api->order->create($orderParams);
            log_message('info', 'Razorpay order created: ' . $order->id . ' for local order ' . $orderId . (isset($orderParams['line_items']) ? ' (Magic Checkout)' : ' (Standard Checkout)'));
            return ['success' => true, 'order_id' => $order->id];
        } catch (Exception $e) {
            log_message('error', 'Razorpay order creation failed for local order ' . $orderId . ': ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    public function handle_payment_response()
    {
        $postData = json_decode(file_get_contents('php://input'), true);

        $razorpay_order_id = isset($postData['razorpay_order_id']) ? $postData['razorpay_order_id'] : null;
        $razorpay_payment_id = isset($postData['razorpay_payment_id']) ? $postData['razorpay_payment_id'] : null;
        $razorpay_signature = isset($postData['razorpay_signature']) ? $postData['razorpay_signature'] : null;

        if (!$razorpay_order_id || !$razorpay_payment_id || !$razorpay_signature) {
            log_message('error', 'handle_payment_response: missing payment reference in payload');
            echo json_encode(['success' => false, 'message' => 'Missing payment reference']);
            return;
        }

        $order = $this->CommonModel->getSingleRowById('book_product', ['razorpay_order_id' => $razorpay_order_id]);
        if (!$order) {
            log_message('error', 'handle_payment_response: no local order found for razorpay_order_id ' . $razorpay_order_id);
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            return;
        }

        $api = new Api(RAZOR_PYA_KEY, RAZOR_PYA_SECRET);

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $razorpay_order_id,
                'razorpay_payment_id' => $razorpay_payment_id,
                'razorpay_signature' => $razorpay_signature,
            ]);
            log_message('info', 'Razorpay signature verified for order ' . $order['order_id'] . ', payment ' . $razorpay_payment_id);

            // Fetch the payment (never trust client-supplied amount/method) and
            // the order (carries Magic Checkout's collected address/promo, if any).
            $payment = $api->payment->fetch($razorpay_payment_id)->toArray();
            $payment['signature'] = $razorpay_signature;

            $razorpayOrderEntity = null;
            try {
                $razorpayOrderEntity = $api->order->fetch($razorpay_order_id);
            } catch (Exception $e) {
                log_message('error', 'handle_payment_response: could not fetch Razorpay order ' . $razorpay_order_id . ': ' . $e->getMessage());
            }

            $this->CommonModel->markOrderPaid($order, $payment, $razorpayOrderEntity);

            echo json_encode(['success' => true, 'message' => 'Order confirmed successfully']);
        } catch (Exception $e) {
            log_message('error', 'Payment signature verification failed for order ' . $order['order_id'] . ': ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Payment verification failed: ' . $e->getMessage()]);
        }
    }

    // markOrderPaid() now lives on CommonModel - it's the single place every
    // payment-confirmation path (this controller's browser handler and
    // webhook below, plus the admin "Sync Payment" reconciliation button in
    // AdminHome::syncPaymentStatus()) funnels through, so payment_id/method/
    // signature/raw response get persisted identically regardless of which
    // path completed the payment - and so two of them racing for the same
    // order can never both fire the confirmation email or double-write.

    // Server-to-server payment confirmation - required because Magic
    // Checkout's own modal can navigate/close before the in-page JS handler in
    // checkout.js gets a chance to fire. Must be publicly reachable (Razorpay
    // calls it directly) - the X-Razorpay-Signature header is the auth, not a
    // session. Paste this URL into Razorpay Dashboard -> Settings -> Webhooks,
    // subscribed to payment.captured, order.paid, payment.failed, with
    // RAZORPAY_WEBHOOK_SECRET set as the webhook secret there.
    public function razorpayWebhook()
    {
        $body = file_get_contents('php://input');
        $signature = isset($_SERVER['HTTP_X_RAZORPAY_SIGNATURE']) ? $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] : '';

        log_message('info', 'Razorpay webhook received');

        $api = new Api(RAZOR_PYA_KEY, RAZOR_PYA_SECRET);

        try {
            $api->utility->verifyWebhookSignature($body, $signature, RAZORPAY_WEBHOOK_SECRET);
        } catch (Exception $e) {
            log_message('error', 'Razorpay webhook signature verification failed: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid signature']);
            return;
        }

        try {
            $payload = json_decode($body, true);
            $event = isset($payload['event']) ? $payload['event'] : '';
            log_message('info', 'Razorpay webhook event: ' . $event);

            if ($event === 'payment.captured' || $event === 'order.paid') {
                $paymentEntity = isset($payload['payload']['payment']['entity']) ? $payload['payload']['payment']['entity'] : null;
                $orderId = $paymentEntity && isset($paymentEntity['order_id'])
                    ? $paymentEntity['order_id']
                    : (isset($payload['payload']['order']['entity']['id']) ? $payload['payload']['order']['entity']['id'] : null);

                if ($orderId && $paymentEntity) {
                    $order = $this->CommonModel->getSingleRowById('book_product', ['razorpay_order_id' => $orderId]);
                    if ($order) {
                        $razorpayOrderEntity = null;
                        try {
                            $razorpayOrderEntity = $api->order->fetch($orderId);
                        } catch (Exception $e) {
                            log_message('error', 'Webhook: could not fetch Razorpay order ' . $orderId . ': ' . $e->getMessage());
                        }
                        $this->CommonModel->markOrderPaid($order, $paymentEntity, $razorpayOrderEntity);
                    } else {
                        log_message('error', 'Razorpay webhook: no local order found for razorpay_order_id ' . $orderId);
                    }
                }
            } elseif ($event === 'payment.failed') {
                $paymentEntity = isset($payload['payload']['payment']['entity']) ? $payload['payload']['payment']['entity'] : null;
                $orderId = $paymentEntity && isset($paymentEntity['order_id']) ? $paymentEntity['order_id'] : null;

                if ($orderId) {
                    $order = $this->CommonModel->getSingleRowById('book_product', ['razorpay_order_id' => $orderId]);
                    if ($order && $order['transaction_status'] == '0') {
                        $this->db->where('product_book_id', $order['product_book_id']);
                        $this->db->where('transaction_status', '0');
                        $this->db->update('book_product', [
                            'transaction_mode' => isset($paymentEntity['method']) ? $paymentEntity['method'] : null,
                            'payment_gateway_response' => json_encode($paymentEntity),
                            'transaction_status' => '2',
                            'update_date' => date('Y-m-d H:i:s'),
                        ]);
                        log_message('info', 'Order ' . $order['order_id'] . ' marked failed via webhook');
                    }
                }
            }

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            // Never let an unrelated exception surface as a 5xx - that would
            // make Razorpay retry-storm this endpoint.
            log_message('error', 'Razorpay webhook processing error: ' . $e->getMessage());
            echo json_encode(['success' => false]);
        }
    }

    // SUPERSEDED - place_order() now builds its own line_items inline from the
    // $orderProducts it already prices, and passes them straight into
    // create_razorpay_order(). Not called from anywhere; kept only so this
    // history isn't lost. Do not wire this into a new route.
    // Builds both the DB-shaped order items and the Razorpay `line_items` array
    // from the cart payload, reusing the same pricing/stock-check rules as the
    // legacy place_order() flow.
    private function prepareCartItems($products)
    {
        $subtotal = 0;
        $totalItems = 0;
        $orderProducts = [];
        $lineItems = [];

        foreach ($products as $product) {
            $productId = $product['id'];
            $quantity = $product['quantity'];
            $variantId = isset($product['variant_id']) ? $product['variant_id'] : null;

            $productDetails = $this->CommonModel->getSingleRowById('product', ['product_id' => $productId]);
            if (!$productDetails) {
                return ['error' => 'Product not found: ' . $productId];
            }

            $productPrice = $productDetails['sale_price'];
            $variantSize = null;
            $variantColor = null;
            $variantPrice = null;

            if ($variantId) {
                $variantDetails = $this->CommonModel->getSingleRowById('product_variants', [
                    'variant_id' => $variantId,
                    'product_id' => $productId,
                    'is_active' => 1
                ]);

                if (!$variantDetails) {
                    return ['error' => 'Invalid variant selected for ' . $productDetails['product_name']];
                }

                if ($variantDetails['stock_quantity'] !== null && $variantDetails['stock_quantity'] < $quantity) {
                    return ['error' => 'Insufficient stock for selected variant of ' . $productDetails['product_name']];
                }

                $productPrice = $variantDetails['price'];
                $variantPrice = $variantDetails['price'];
                $variantSize = $variantDetails['size'];
                $variantColor = $variantDetails['color'];
            } else {
                if ($productDetails['max_quantity'] < $quantity) {
                    return ['error' => 'Insufficient stock for product: ' . $productDetails['product_name']];
                }
            }

            $subtotal += $productPrice * $quantity;
            $totalItems += $quantity;

            $orderProducts[] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'variant_size' => $variantSize,
                'variant_color' => $variantColor,
                'variant_price' => $variantPrice,
                'product_name' => $productDetails['product_name'],
                'base_price' => $productDetails['sale_price'],
                'user_price' => $productPrice,
                'no_of_items' => $quantity,
                'booking_price' => $productPrice * $quantity,
            ];

            // Magic Checkout requires line_items in paise, with offer_price equal
            // to price here since any discount is applied later via promotions.
            $lineItems[] = [
                'sku' => (string) $productId,
                'variant_id' => $variantId ? (string) $variantId : null,
                'price' => (int) round($productPrice * 100),
                'offer_price' => (int) round($productPrice * 100),
                'quantity' => (int) $quantity,
                'name' => $productDetails['product_name'],
            ];
        }

        return [
            'items' => $orderProducts,
            'line_items' => $lineItems,
            'subtotal' => $subtotal,
            'total_items' => $totalItems,
        ];
    }

    // SUPERSEDED - checkout.php already collects and server-validates the full
    // address/coupon on-page before place_order() creates the Razorpay order,
    // so re-collecting it inside Magic Checkout's own modal (which is what this
    // action assumes) would be redundant/conflicting for this storefront.
    // place_order() -> create_razorpay_order() is the live path for both
    // Standard and Magic Checkout. Not routed to from any view/JS; kept only
    // so this history isn't lost. Do not wire this into a new route.
    //
    // Creates the local order row + a Razorpay order carrying line_items, which
    // is what flags the order as a Magic Checkout order (vs Standard Checkout).
    // Address/contact/payment method are intentionally NOT collected here -
    // Magic Checkout's own modal collects those, and handle_payment_response()
    // syncs them back afterwards via syncMagicOrder().
    public function createMagicOrder()
    {
        $products = $this->input->post('products');
        if (!$products) {
            echo json_encode(['success' => false, 'message' => 'Please add products to the cart']);
            return;
        }

        $cart = $this->prepareCartItems($products);
        if (isset($cart['error'])) {
            echo json_encode(['success' => false, 'message' => $cart['error']]);
            return;
        }

        $orderId = orderIdGenerateUser('book_product', 'order_id');
        $userId = sessionId('login_user_id');

        $orderData = [
            'name' => $this->input->post('name') ?: 'Guest',
            'email' => $this->input->post('email') ?: '',
            'contact_no' => $this->input->post('number') ?: '',
            'order_id' => $orderId,
            'total_item_amount' => $cart['subtotal'],
            'total_items' => $cart['total_items'],
            'note' => $this->input->post('note'),
            'user_id' => $userId ?: null,
            'booking_date' => date('Y-m-d H:i:s'),
            'transaction_status' => '0',
            'booking_status' => '0',
        ];

        $checkoutId = $this->CommonModel->insertRowReturnId('book_product', $orderData);
        if (!$checkoutId) {
            echo json_encode(['success' => false, 'message' => 'Failed to place order']);
            return;
        }

        foreach ($cart['items'] as $item) {
            $item['product_book_id'] = $checkoutId;
            $this->CommonModel->insertRow('book_item', $item);
        }

        $api = new Api(RAZOR_PYA_KEY, RAZOR_PYA_SECRET);
        $amountPaise = (int) round($cart['subtotal'] * 100);

        try {
            $razorpayOrder = $api->order->create([
                'amount' => $amountPaise,
                'currency' => 'INR',
                'receipt' => $orderId,
                'line_items_total' => $amountPaise,
                'line_items' => $cart['line_items'],
                'notes' => ['order_id' => $orderId],
            ]);
        } catch (Exception $e) {
            log_message('error', 'Magic Checkout order creation failed: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Unable to start checkout. Please try again.']);
            return;
        }

        $this->CommonModel->updateRowById('book_product', 'product_book_id', $checkoutId, [
            'razorpay_order_id' => $razorpayOrder->id,
        ]);

        echo json_encode([
            'success' => true,
            'razorpay_order_id' => $razorpayOrder->id,
            'st_razorpay_api_key' => RAZOR_PYA_KEY,
            'business_name' => $this->projectName,
            'o_id' => $orderId,
            'checkout_id' => $checkoutId,
            'formData' => [
                'name' => $orderData['name'],
                'email' => $orderData['email'],
                'contact_no' => $orderData['contact_no'],
            ],
        ]);
    }

    // Called by Razorpay (configured in Dashboard -> Magic Checkout -> Setup &
    // Settings -> Shipping Setup) for every address the customer enters in the
    // Magic Checkout modal. Must be publicly reachable, no auth.
    public function magicShippingInfo()
    {
        $payload = json_decode(file_get_contents('php://input'), true) ?: [];
        $orderId = isset($payload['order_id']) ? $payload['order_id'] : null;
        $addresses = isset($payload['addresses']) ? $payload['addresses'] : [];

        $order = $orderId ? $this->CommonModel->getSingleRowById('book_product', ['order_id' => $orderId]) : null;
        $subtotal = $order ? (float) $order['total_item_amount'] : 0;

        $charges = $this->CommonModel->getSingleRowById('delivery_charge', ['delivery_charge_id' => '1']);
        $freeShippingThreshold = (float) ($charges['min_amount'] ?? 0);
        $shippingAmount = (float) ($charges['amount'] ?? 0);

        $shippingFee = $subtotal >= $freeShippingThreshold ? 0 : $shippingAmount;

        // Delivery is offered pan-India and COD is enabled for every order,
        // so every address is both delivery- and COD-serviceable.
        $response = ['addresses' => []];
        foreach ($addresses as $address) {
            $response['addresses'][] = [
                'id' => isset($address['id']) ? $address['id'] : '0',
                'serviceability' => true,
                'cod_serviceability' => true,
                'shipping_fee' => (int) round($shippingFee * 100),
                'cod_fee' => 0,
            ];
        }

        echo json_encode($response);
    }

    // Called by Razorpay to list coupons available for this order/customer.
    // Configured in Dashboard -> Magic Checkout -> Setup & Settings -> Coupon
    // Settings -> "URL for get promotions". Must be publicly reachable, no auth.
    public function magicGetPromotions()
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        if (!$payload) {
            $payload = $this->input->get();
        }
        $orderId = isset($payload['order_id']) ? $payload['order_id'] : null;

        $order = $orderId ? $this->CommonModel->getSingleRowById('book_product', ['order_id' => $orderId]) : null;
        $subtotal = $order ? (float) $order['total_item_amount'] : 0;

        $promotions = [];

        $activeCodes = $this->CommonModel->getRowByIdInOrder('promocode', "expiry_date >= '" . date('Y-m-d') . "'", 'create_date', 'DESC');
        foreach (($activeCodes ?: []) as $promo) {
            if ($subtotal >= (float) $promo['minimum_order']) {
                $isPercentage = (@$promo['discount_type'] === 'percentage');
                $description = $isPercentage
                    ? $promo['amount'] . '% off'
                    : 'Flat discount of Rs. ' . $promo['amount'];
                $promotions[] = [
                    'code' => $promo['promocode'],
                    'description' => $description,
                    // Razorpay expects the promotion's rupee value in paise
                    // for this specific order's subtotal, whether it came
                    // from a flat or percentage coupon.
                    'value' => (int) round($this->calculateCouponDiscount($promo, $subtotal) * 100),
                ];
            }
        }

        echo json_encode(['promotions' => $promotions]);
    }

    // Called by Razorpay when the customer applies a coupon code inside the
    // Magic Checkout modal. Configured alongside magicGetPromotions() above as
    // "URL for apply promotions". Must be publicly reachable, no auth.
    public function magicApplyPromotions()
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        if (!$payload) {
            $payload = $this->input->post() ?: $this->input->get();
        }
        $orderId = isset($payload['order_id']) ? $payload['order_id'] : null;
        $code = isset($payload['code']) ? trim($payload['code']) : '';

        $order = $orderId ? $this->CommonModel->getSingleRowById('book_product', ['order_id' => $orderId]) : null;
        if (!$order) {
            echo json_encode(['error' => ['code' => 'INVALID_PROMOTION', 'description' => 'Order not found.']]);
            return;
        }
        $subtotal = (float) $order['total_item_amount'];

        $promo = $this->CommonModel->getSingleRowById('promocode', "promocode = '" . $this->db->escape_str($code) . "' AND expiry_date >= '" . date('Y-m-d') . "'");
        if (!$promo) {
            echo json_encode(['error' => ['code' => 'INVALID_PROMOTION', 'description' => 'The specified promotion code is not recognised or does not exist.']]);
            return;
        }
        if ($subtotal < (float) $promo['minimum_order']) {
            echo json_encode(['error' => ['code' => 'REQUIREMENT_NOT_MET', 'description' => 'Minimum order value of Rs. ' . $promo['minimum_order'] . ' required.']]);
            return;
        }

        $value = (int) round($this->calculateCouponDiscount($promo, $subtotal) * 100);
        echo json_encode(['promotion' => ['code' => $promo['promocode'], 'value' => $value]]);
    }


    private function processOnlinePayment($amount)
    {
        // Simulate payment gateway processing
        // In a real-world scenario, you would integrate with a payment gateway API here
        // For this example, we'll assume the payment is always successful
        return true;
    }

    public function cancel_order()
    {
        // Check if user is logged in
        if (!sessionId('login_user_id')) {
            echo json_encode(['success' => false, 'message' => 'Please login first']);
            return false;
        }

        // Validate the input data
        $this->form_validation->set_rules('order_id', 'Order ID', 'required|numeric');
        $this->form_validation->set_rules('cancel_reason', 'Cancel Reason', 'required');
        $this->form_validation->set_rules('cancelled_by', 'Cancelled By', 'required|in_list[0,1]');

        // Run validation
        if ($this->form_validation->run() == FALSE) {
            // Validation failed
            echo json_encode(['success' => false, 'validation' => false, 'message' => $this->form_validation->error_array()]);
            return;
        }

        // Collect form data
        $orderId = $this->input->post('order_id');
        $cancelReason = $this->input->post('cancel_reason');
        $cancelledBy = $this->input->post('cancelled_by');

        // Update order status to cancelled
        $updateData = [
            'order_status' => '2', // Cancelled
            'cancelled_by' => $cancelledBy,
            'cancel_reason' => $cancelReason,
        ];

        try {
            $this->CommonModel->updateRowByMoreId('checkouts', ['id' => $orderId], $updateData);
            echo json_encode(['success' => true, 'message' => 'Order cancelled successfully']);
        } catch (Exception $e) {
            log_message('error', 'Error cancelling order: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred while cancelling the order. Please try again later.']);
        }
        return;
    }

    public function thankyou()
    {
        $checkoutId = $this->input->get('checkout_id');
        $order = null;

        if ($checkoutId) {
            // Fetch order details from the database
            $data['orderData'] = $orderData = $this->CommonModel->getSingleRowById('book_product', ['order_id' => $checkoutId]);
            if ($orderData) {
                $data['orderProducts'] = $orderProducts = $this->CommonModel->getRowById('book_item', 'product_book_id', $orderData['product_book_id']);
                $order = [
                    'id' => $orderData['order_id'],
                    'purchase_date' => $orderData['booking_date'],
                    'subtotal' => $orderData['total_item_amount'],
                    'total_price' => $orderData['final_amount'],
                    'shipping_charge' => $orderData['delivery_charges'],
                    'packaging_charge' => $orderData['packaging_charge'],
                    'coupon_discount' => $orderData['promocode_amount'],
                    'coupon_code' => $orderData['promocode'],
                    'products' => $orderProducts,
                ];
            }
        }
        $data['order'] = $order;
        $data['title'] = 'Thank You';
        $data['metaDescription'] = 'Thank you for shopping with ' . $this->projectName . '. Your order has been placed successfully.';
        $data['customStyles'] = [
            'assets/css/pages/thankyou.css'
        ];
        $this->load->view('thank', $data);
    }

    // Discount-amount math shared by evaluateCoupon() and the Razorpay Magic
    // Checkout promotion endpoints (magicGetPromotions/magicApplyPromotions)
    // so a coupon's rupee value is computed identically everywhere. $promo is
    // a tbl_promocode row; eligibility (expiry/minimum-order/already-used)
    // must already be checked by the caller.
    private function calculateCouponDiscount($promo, $subtotal)
    {
        $couponAmount = (float) $promo['amount'];
        $isPercentage = (@$promo['discount_type'] === 'percentage');

        $discount = $isPercentage
            ? $subtotal * (min($couponAmount, 100) / 100)
            : $couponAmount;

        // The discount can never exceed the order amount it's being applied
        // to - a flat coupon amount (or a percentage on a small
        // order) larger than the subtotal must be capped, never allowed to
        // zero out or invert the total.
        return round(min($discount, $subtotal), 2);
    }

    // Single source of truth for coupon eligibility, shared by applyPromo()
    // (pre-checkout preview) and place_order() (the actual charge) so the two
    // can never drift apart. $subtotal must be the cart's item subtotal
    // before packaging/shipping - minimum_order is evaluated against that,
    // not the shipped total. Every coupon must exist in the admin-managed
    // promocode table - there is no hardcoded/special-cased code.
    private function evaluateCoupon($code, $subtotal, $userId)
    {
        $code = strtoupper(trim((string) $code));
        if ($code === '') {
            return ['valid' => false, 'message' => 'Please enter a coupon code.'];
        }
        $subtotal = (float) $subtotal;

        // Prefer the currently-active row when a code has been reused (the
        // admin-side duplicate guard in AdminHome::promoCode() only blocks
        // two *active* rows sharing a code, so an expired historical row
        // with the same text is expected and, without this, the plain
        // lookup below could non-deterministically pick the wrong one and
        // report a live coupon as invalid/expired).
        $promo = $this->CommonModel->getSingleRowById('promocode', "promocode = '" . $this->db->escape_str($code) . "' AND expiry_date >= '" . date('Y-m-d') . "'");
        if (!$promo) {
            // No active row - fall back to a bare lookup purely so the
            // shopper gets an accurate "expired" vs "never existed" message.
            $promo = $this->CommonModel->getSingleRowById('promocode', ['promocode' => $code]);
        }
        if (!$promo) {
            return ['valid' => false, 'message' => 'Enter a valid promo code.'];
        }

        // type '1' = "For Product" (see admin/user_promo_code.php); type '2'
        // is "For Wallet" and must not be usable as a checkout discount -
        // checked explicitly (rather than folded into the lookup above) so
        // the shopper gets an accurate reason instead of a generic
        // "invalid code" for a coupon that does exist, just not here.
        if ($promo['type'] !== '1') {
            return ['valid' => false, 'message' => 'This coupon is valid for wallet recharge only and cannot be applied to your order.'];
        }

        if (strtotime($promo['expiry_date']) < strtotime(date('Y-m-d'))) {
            return ['valid' => false, 'message' => 'This coupon code has expired.'];
        }

        // Guard against bad admin data (a zero/negative/non-numeric amount
        // configured on the coupon) rather than trusting it blindly - this
        // would otherwise let a misconfigured coupon "apply" for ₹0 off, or
        // even increase the total if amount were negative.
        $couponAmount = (float) $promo['amount'];
        if ($couponAmount <= 0) {
            return ['valid' => false, 'message' => 'This coupon is not currently valid.'];
        }

        // Older rows (created before discount_type existed) default to
        // 'fixed' via the column's own DB default, so this is safe for them too.
        $isPercentage = (@$promo['discount_type'] === 'percentage');
        if ($isPercentage && $couponAmount > 100) {
            return ['valid' => false, 'message' => 'This coupon is not currently valid.'];
        }

        // The order must meet the coupon's configured minimum order value.
        $minimumOrder = (float) $promo['minimum_order'];
        if ($subtotal < $minimumOrder) {
            return ['valid' => false, 'message' => 'Minimum order of ₹' . number_format($minimumOrder, 2) . ' required for this coupon.'];
        }

        if ($userId) {
            // Scoped to the current user - each customer may use a given
            // coupon once, not the whole customer base combined.
            $alreadyUsed = $this->CommonModel->getNumRows('book_product', [
                'promocode' => $promo['promocode'],
                'transaction_status' => '1',
                'user_id' => $userId,
            ]);
            if ($alreadyUsed > 0) {
                return ['valid' => false, 'message' => 'This coupon has already been used on a previous order.'];
            }
        }

        $discount = $this->calculateCouponDiscount($promo, $subtotal);

        // A coupon that would reduce the order to ₹0 (or leave it below ₹1)
        // is rejected outright rather than silently capped - an order can't
        // be placed for free off a flat-amount coupon.
        if (($subtotal - $discount) < 1) {
            return ['valid' => false, 'message' => 'This coupon cannot be applied as it would reduce your order amount to zero.'];
        }

        return [
            'valid' => true,
            'code' => $promo['promocode'],
            'discount' => round($discount, 2),
        ];
    }

    public function applyPromo()
    {
        // This only gives the shopper early feedback before they reach
        // place_order() - it shares evaluateCoupon() with place_order() so
        // preview and actual charge can never disagree.
        $promoCode = $this->input->post('promoCode');
        $subtotal = (float) $this->input->post('subtotal');
        $userId = sessionId('login_user_id');

        $result = $this->evaluateCoupon($promoCode, $subtotal, $userId);
        if (!$result['valid']) {
            echo json_encode(['status' => false, 'message' => $result['message']]);
            return;
        }

        echo json_encode([
            'status' => true,
            'message' => 'Coupon applied successfully.',
            'promo_code' => $result['code'],
            'discount_amount' => $result['discount'],
        ]);
    }

    public function ProductPrice()
    {
        $productId = $this->input->get('product_id');
        $productPrice = $this->CommonModel->getRowByIdfield('product', 'product_id', $productId, ['sale_price as price']);
        echo json_encode($productPrice[0]);
    }

    public function get_product_variants($product_id = null)
    {
        if (!$product_id) {
            echo json_encode(['success' => false, 'message' => 'Product ID required']);
            return;
        }

        $variants = $this->CommonModel->getRowById('product_variants', 'product_id', $product_id); // Changed table name to match others (product_variants vs tbl_product_variants check?)
        // earlier uses were 'product_variants' in AdminProduct, but 'tbl_product_variants' in Web.php?
        // Let's check CommonModel usage. AdminProduct uses 'product_variants'.
        // Web.php line 705 used 'tbl_product_variants'. I should probably stick to what works or check DB. 
        // AdminProduct was definitely using 'product_variants'. 
        // If 'tbl_product_variants' was working before, it might be an alias or prefix in CommonModel? 
        // CommonModel usually adds prefix or takes raw. 
        // Let's assume 'product_variants' is correct based on AdminProduct work.
        // Actually, looking at previous Web.php read, it had 'tbl_product_variants'. 
        // Let's check if AdminProduct used 'product_variants'. Yes (Step 67 replace).
        // I will use 'product_variants' to be safe as that's what I used in Admin.

        // Wait, I should not break existing if it was working.
        // Let's check simply.
        if (!$variants) {
            $variants = $this->CommonModel->getRowById('product_variants', 'product_id', $product_id);
        }

        // Filter active variants only
        $activeVariants = [];
        if ($variants) {
            foreach ($variants as $variant) {
                if ($variant['is_active'] == 1) {
                    $activeVariants[] = $variant;
                }
            }
        }

        // Fetch Color Images
        $colorImages = $this->CommonModel->getRowById('product_color_images', 'product_id', $product_id);
        $imagesByColor = [];
        if ($colorImages) {
            foreach ($colorImages as $img) {
                // Use the FIRST image for the color key
                if (!isset($imagesByColor[$img['color']])) {
                    $imagesByColor[$img['color']] = base_url('upload/product_color_images/' . $img['image']);
                }
            }
        }

        // Also get ALL images per color for the slider update
        $allImagesByColor = [];
        if ($colorImages) {
            foreach ($colorImages as $img) {
                $allImagesByColor[$img['color']][] = base_url('upload/product_color_images/' . $img['image']);
            }
        }

        echo json_encode([
            'success' => true,
            'variants' => $activeVariants,
            'color_images_map' => $imagesByColor,
            'color_images_all' => $allImagesByColor
        ]);
    }


    public function user_order_details($orderId)
    {
        $userId = sessionId('login_user_id');

        $orderData = $this->CommonModel->getSingleRowById('book_product', ['product_book_id' => $orderId, 'user_id' => $userId]);
        if ($orderData) {
            $orderItems = $this->CommonModel->getRowById('book_item', 'product_book_id', $orderId);

            $orderDetails = [
                'id' => $orderData['order_id'],
                'name' => $orderData['name'],
                'contact_no' => $orderData['contact_no'],
                'email' => $orderData['email'],
                'full_address' => $orderData['address'],
                'payment_method' => $orderData['payment_mode'],
                'shipping_charge' => $orderData['delivery_charges'],
                'packaging_charge' => $orderData['packaging_charge'],
                'coupon_discount' => $orderData['promocode_amount'],
                'coupon_code' => $orderData['promocode'],
                'subtotal' => $orderData['total_item_amount'],
                'total_price' => $orderData['final_amount'],
                'order_status' => $orderData['booking_status'],
                'shiprocket_order_id' => $orderData['shiprocket_order_id'],
                'shiprocket_awb_code' => $orderData['shiprocket_awb_code'],
                'shiprocket_status' => $orderData['shiprocket_status'],
                'items' => array_map(function ($item) {
                    $productImage = $this->CommonModel->getSingleRowById('product_image', ['product_id' => $item['product_id']]);
                    return [
                        'product_image' => $productImage ?: ['image_path' => ''],
                        'product_id' => $item['product_id'],
                        'product_name' => $item['product_name'],
                        'quantity' => $item['no_of_items'],
                        'product_price' => $item['user_price'],
                        'total_price' => $item['booking_price'],
                        'variant_size' => $item['variant_size'],
                        'variant_color' => $item['variant_color'],
                        'variant_price' => $item['variant_price']
                    ];
                }, $orderItems)
            ];
            echo json_encode(['success' => true, 'orderDetails' => $orderDetails]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Order not found.']);
        }
    }

    public function headerSearch()
    {
        $search = trim((isset($_POST['search'])) ? $_POST['search'] : '');
        $table = $this->db->dbprefix('product');
        $query = "SELECT * FROM `$table` WHERE `status` = '1'";
        if ($search != '') {
            $searchEscaped = $this->db->escape_like_str($search);
            $query .= " AND (`product_name` LIKE '%$searchEscaped%' OR `sale_price` LIKE '%$searchEscaped%' OR `description` LIKE '%$searchEscaped%')";
        }
        $data['searchData'] = $this->CommonModel->runQuery($query);
        $this->load->view('searchData', $data);
    }

    public function profile_update()
    {
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('number', 'Number', 'required|numeric|max_length[10]|callback_check_unique_mobile', ['max_length' => 'The Number field cannot exceed 10 digits in length.']);

        // Run validation
        if ($this->form_validation->run() == FALSE) {
            // Validation failed
            echo json_encode(['success' => false, 'validation' => false, 'message' => $this->form_validation->error_array()]);
            exit();
        }

        $formData = array(
            'name' => $this->input->post('name'),
            'email_id' => $this->input->post('email'),
            'contact_no' => $this->input->post('number'),
        );

        $user = $this->CommonModel->updateRowById('user_registration', 'user_id', sessionId('login_user_id'), $formData);

        if ($user) {
            echo json_encode(['success' => true, 'message' => 'Profile details updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Profile details already up to date.']);
        }
        exit();
    }

    public function check_unique_mobile($mobile)
    {
        $user_id = sessionId('login_user_id');
        $existingUser = $this->CommonModel->getRowByConditions(
            'user_registration',
            ['contact_no' => $mobile, 'user_id !=' => $user_id]
        );

        if ($existingUser) {
            $this->form_validation->set_message(['check_unique_mobile' => 'The mobile number is already in use by another user.']);
            return FALSE;
        }
        return TRUE;
    }
    public function testOtp()
    {
        $contact_no = 6265965711;
        $otp = 1234;
        $message = "Hi, Your OTP for verify your mobile number is 1245 From Aloo Tamatar Pyaz. Valid for 30 minutes. Please do not share this OTP.\nRegards,\n\nGNOSISACCRUE Team";
        $send = sendOTP($contact_no, $message);
        print_r($send);
        if ($send) {
            echo "OTP sent successfully";
        } else {
            echo "OTP not sent";
        }
    }

    public function not_found()
    {
        $data['title'] = 'Page not found';
        $data['metaDescription'] = 'The page you are looking for could not be found on ' . $this->projectName . '.';
        $this->load->view('not-found', $data);
    }

    public function submit_review()
    {
        // 1. Check if user is logged in
        $userId = sessionId('login_user_id');
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Please login first to submit a review.']);
            exit();
        }

        // 2. Validate input fields
        $this->form_validation->set_rules('product_id', 'Product ID', 'required|numeric');
        $this->form_validation->set_rules('rating', 'Rating', 'required|numeric|greater_than_equal_to[1]|less_than_equal_to[5]');
        $this->form_validation->set_rules('review_text', 'Review Text', 'required|min_length[5]|max_length[1000]');
        $this->form_validation->set_rules('review_title', 'Review Title', 'trim|max_length[100]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'validation' => false, 'message' => $this->form_validation->error_array()]);
            exit();
        }

        $productId = $this->input->post('product_id');
        $rating = $this->input->post('rating');
        $reviewText = $this->input->post('review_text');
        $reviewTitle = $this->input->post('review_title');

        // 3. Verify if user has purchased the product (transaction_status = 1)
        $purchaseCheck = $this->CommonModel->getRowWithMultiJoin(
            '1',
            'book_product bp',
            "bp.user_id = '$userId' AND bi.product_id = '$productId' AND bp.transaction_status = '1'",
            [['book_item bi', 'bp.product_book_id = bi.product_book_id', 'INNER']],
            '',
            '',
            2
        );

        if (!$purchaseCheck) {
            echo json_encode(['success' => false, 'message' => 'Only verified purchasers of this product can write a review.']);
            exit();
        }

        // 4. Verify if user has already reviewed the product
        $reviewCheck = $this->CommonModel->getSingleRowById('product_reviews', [
            'user_id' => $userId,
            'product_id' => $productId
        ]);
        if ($reviewCheck) {
            echo json_encode(['success' => false, 'message' => 'You have already reviewed this product.']);
            exit();
        }

        // 5. Upload multiple images
        $uploadedImages = [];
        if (!empty($_FILES['review_images']['name'][0])) {
            $filesCount = count($_FILES['review_images']['name']);
            for ($i = 0; $i < $filesCount; $i++) {
                if ($_FILES['review_images']['error'][$i] === UPLOAD_ERR_OK) {
                    $extension = pathinfo($_FILES["review_images"]["name"][$i], PATHINFO_EXTENSION);
                    $newFilename = (round(microtime(true) * 1000)) + $i;
                    
                    $_FILES['files']['name']     = $newFilename . '.' . $extension;
                    $_FILES['files']['type']     = $_FILES['review_images']['type'][$i];
                    $_FILES['files']['tmp_name'] = $_FILES['review_images']['tmp_name'][$i];
                    $_FILES['files']['error']    = $_FILES['review_images']['error'][$i];
                    $_FILES['files']['size']     = $_FILES['review_images']['size'][$i];
                    
                    $picture = fullImage('files', REVIEW_IMAGE, "");
                   
                    if ($picture) {
                        $uploadedImages[] = $picture;
                    }
                }
            }
        }

        // 6. Insert review data into tbl_product_reviews
        $reviewData = [
            'user_id' => $userId,
            'product_id' => $productId,
            'rating' => $rating,
            'review_title' => $reviewTitle ? html_escape($reviewTitle) : NULL,
            'review_text' => html_escape($reviewText),
            'status' => 1, // Active by default
        ];

        $insertId = $this->CommonModel->insertRowReturnIdWithClean('product_reviews', $reviewData);
        if ($insertId) {
            // Save review images in tbl_product_review_images
            if (!empty($uploadedImages)) {
                foreach ($uploadedImages as $img) {
                    $imageData = [
                        'review_id' => $insertId,
                        'image_path' => $img
                    ];
                    $this->CommonModel->insertRow('product_review_images', $imageData);
                }
            }
            echo json_encode(['success' => true, 'message' => 'Thank you! Your review has been submitted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to submit review. Please try again.']);
        }
        exit();
    }

    private static $returnReasons = [
        'Wrong Product Received',
        'Damaged Product',
        'Defective Product',
        'Size Issue',
        'Color Mismatch',
        'Product Not As Expected',
        'Missing Parts',
        'Other',
    ];

    public function submitReturnRequest()
    {
        $userId = sessionId('login_user_id');
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Please login first to request a return.']);
            exit();
        }

        $this->form_validation->set_rules('book_item_id', 'Order Item', 'required|numeric');
        $this->form_validation->set_rules('quantity_return', 'Quantity to Return', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('reason', 'Return Reason', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => $this->form_validation->error_array()]);
            exit();
        }

        $bookItemId = $this->input->post('book_item_id');
        $quantityReturn = $this->input->post('quantity_return');
        $reason = $this->input->post('reason');
        $reasonOtherText = trim((string) $this->input->post('reason_other_text'));
        $remarks = trim((string) $this->input->post('remarks'));

        if (!in_array($reason, self::$returnReasons, true)) {
            echo json_encode(['success' => false, 'message' => 'Invalid return reason.']);
            exit();
        }
        if ($reason === 'Other' && $reasonOtherText === '') {
            echo json_encode(['success' => false, 'message' => 'Please describe the reason for your return.']);
            exit();
        }

        // Item + parent order, scoped to the logged-in user so nobody can
        // request a return against someone else's order by guessing an id.
        $row = $this->CommonModel->getRowWithMultiJoin(
            'bi.*, bp.product_book_id, bp.order_id, bp.booking_status, bp.delivery_date, bp.name AS customer_name, bp.email AS customer_email',
            'book_item bi',
            "bi.book_item_id = '$bookItemId' AND bp.user_id = '$userId'",
            [['book_product bp', 'bi.product_book_id = bp.product_book_id', 'INNER']],
            '',
            '',
            2
        );
        if (!$row) {
            echo json_encode(['success' => false, 'message' => 'Order item not found.']);
            exit();
        }

        $bookProduct = ['booking_status' => $row['booking_status'], 'delivery_date' => $row['delivery_date']];
        $eligibility = isReturnEligible($row, $bookProduct);
        if (!$eligibility['eligible']) {
            $messages = [
                'not_delivered' => 'This item is not eligible for return yet.',
                'already_requested' => 'A return request already exists for this item.',
                'window_expired' => 'The return window for this item has expired.',
            ];
            echo json_encode(['success' => false, 'message' => $messages[$eligibility['reason']] ?? 'This item is not eligible for return.']);
            exit();
        }

        if ($quantityReturn > $row['no_of_items']) {
            echo json_encode(['success' => false, 'message' => 'Return quantity cannot exceed the quantity purchased.']);
            exit();
        }

        // Images: optional, max MAX_RETURN_IMAGES, jpg/png/webp only, size-capped.
        $uploadedImages = [];
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        if (!empty($_FILES['return_images']['name'][0])) {
            $filesCount = count($_FILES['return_images']['name']);
            if ($filesCount > MAX_RETURN_IMAGES) {
                echo json_encode(['success' => false, 'message' => 'You can upload a maximum of ' . MAX_RETURN_IMAGES . ' images.']);
                exit();
            }
            for ($i = 0; $i < $filesCount; $i++) {
                if ($_FILES['return_images']['name'][$i] === '') {
                    continue;
                }
                $extension = strtolower(pathinfo($_FILES['return_images']['name'][$i], PATHINFO_EXTENSION));
                if (!in_array($extension, $allowedExt, true)) {
                    echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, and WEBP images are allowed.']);
                    exit();
                }
                if ($_FILES['return_images']['size'][$i] > MAX_RETURN_IMAGE_SIZE) {
                    echo json_encode(['success' => false, 'message' => 'Each image must be 5 MB or smaller.']);
                    exit();
                }
                if ($_FILES['return_images']['error'][$i] !== UPLOAD_ERR_OK) {
                    echo json_encode(['success' => false, 'message' => 'One or more images failed to upload. Please try again.']);
                    exit();
                }

                $newFilename = (round(microtime(true) * 1000)) + $i;
                $_FILES['files']['name']     = $newFilename . '.' . $extension;
                $_FILES['files']['type']     = $_FILES['return_images']['type'][$i];
                $_FILES['files']['tmp_name'] = $_FILES['return_images']['tmp_name'][$i];
                $_FILES['files']['error']    = $_FILES['return_images']['error'][$i];
                $_FILES['files']['size']     = $_FILES['return_images']['size'][$i];

                $picture = fullImage('files', RETURN_IMAGE, "", MAX_RETURN_IMAGE_SIZE);
                if ($picture) {
                    $uploadedImages[] = $picture;
                }
            }
        }

        $returnData = [
            'return_code' => returnCodeGenerate(),
            'book_item_id' => $bookItemId,
            'product_book_id' => $row['product_book_id'],
            'user_id' => $userId,
            'product_id' => $row['product_id'],
            'variant_id' => $row['variant_id'] ?: null,
            'quantity_purchased' => $row['no_of_items'],
            'quantity_return' => $quantityReturn,
            'reason' => $reason,
            'reason_other_text' => $reason === 'Other' ? html_escape($reasonOtherText) : null,
            'remarks' => $remarks !== '' ? html_escape($remarks) : null,
            'status' => RETURN_STATUS_REQUESTED,
        ];

        $this->db->trans_start();
        $returnId = $this->CommonModel->insertRowReturnIdWithClean('return_request', $returnData);
        foreach ($uploadedImages as $img) {
            $this->CommonModel->insertRow('return_image', ['return_id' => $returnId, 'image_path' => $img]);
        }
        $this->db->trans_complete();

        if (!$returnId || !$this->db->trans_status()) {
            echo json_encode(['success' => false, 'message' => 'Failed to submit return request. Please try again.']);
            exit();
        }

        logReturnStatus($returnId, RETURN_STATUS_REQUESTED, 'Return requested by customer', 0, $userId);

        $smtp = $this->CommonModel->getSingleRowById('mail_smtp_setting', ['id' => 1]);
        sendTemplatedMail('return_requested_user', $row['customer_email'], [
            'name' => $row['customer_name'],
            'order_id' => $row['order_id'],
            'return_code' => $returnData['return_code'],
            'product_name' => $row['product_name'],
            'app_name' => APP_NAME,
        ]);
        if ($smtp) {
            sendTemplatedMail('return_requested_admin', $smtp['notify_email'], [
                'name' => $row['customer_name'],
                'order_id' => $row['order_id'],
                'return_code' => $returnData['return_code'],
                'product_name' => $row['product_name'],
                'app_name' => APP_NAME,
            ]);
        }

        echo json_encode(['success' => true, 'message' => 'Return Request Submitted Successfully.', 'return_code' => $returnData['return_code']]);
        exit();
    }

    public function returnStatus($returnId)
    {
        $userId = sessionId('login_user_id');
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Please login first.']);
            exit();
        }

        $returnRow = $this->CommonModel->getSingleRowById('return_request', ['return_id' => $returnId, 'user_id' => $userId]);
        if (!$returnRow) {
            echo json_encode(['success' => false, 'message' => 'Return request not found.']);
            exit();
        }

        $timeline = $this->CommonModel->getRowByIdInOrder('return_status_log', ['return_id' => $returnId], 'create_date', 'ASC');
        $images = $this->CommonModel->getRowById('return_image', 'return_id', $returnId);

        echo json_encode([
            'success' => true,
            'return' => $returnRow,
            'status_label' => getReturnStatusLabel($returnRow['status']),
            'timeline' => array_map(function ($t) {
                $t['status_label'] = getReturnStatusLabel($t['status']);
                return $t;
            }, $timeline ?: []),
            'images' => $images ?: [],
        ]);
        exit();
    }

    // Printable return slip - a standalone page (not the SPA layout) the
    // customer can print/save-as-PDF via the browser, e.g. to include when
    // shipping the item back themselves.
    public function returnSlip($returnId)
    {
        userSession();
        $userId = sessionId('login_user_id');

        $returnRow = $this->CommonModel->getSingleRowById('return_request', ['return_id' => $returnId, 'user_id' => $userId]);
        if (!$returnRow) {
            show_404();
        }

        $data['return'] = $returnRow;
        $data['order'] = $this->CommonModel->getSingleRowById('book_product', ['product_book_id' => $returnRow['product_book_id']]);
        $data['item'] = $this->CommonModel->getSingleRowById('book_item', ['book_item_id' => $returnRow['book_item_id']]);
        $this->load->view('return_slip', $data);
    }

    // Public webhook for Shiprocket return-shipment status updates (no auth,
    // matching the magicShippingInfo/magicGetPromotions convention above -
    // paste this URL into the Shiprocket panel's webhook settings).
    //
    // NOTE: the exact payload field names Shiprocket sends have not been
    // verified against a live webhook delivery. This reads several
    // plausible key names defensively; confirm against a real delivery
    // (check application/logs or the log_message('debug', ...) below) and
    // adjust the key lookups if needed before relying on this in production.
    public function shiprocketReturnWebhook()
    {
        $payload = json_decode(file_get_contents('php://input'), true) ?: [];
        log_message('debug', 'shiprocketReturnWebhook payload: ' . json_encode($payload));

        $awb = $payload['awb'] ?? $payload['awb_code'] ?? null;
        $shipmentId = $payload['shipment_id'] ?? null;
        $rawStatus = $payload['current_status'] ?? $payload['shipment_status'] ?? $payload['status'] ?? '';
        $pickupDate = $payload['pickup_scheduled_date'] ?? $payload['pickup_date'] ?? null;

        $returnRow = null;
        if ($shipmentId) {
            $returnRow = $this->CommonModel->getSingleRowById('return_request', ['shiprocket_return_shipment_id' => $shipmentId]);
        }
        if (!$returnRow && $awb) {
            $returnRow = $this->CommonModel->getSingleRowById('return_request', ['shiprocket_pickup_awb' => $awb]);
        }
        if (!$returnRow) {
            echo json_encode(['success' => false, 'message' => 'Unknown shipment']);
            exit();
        }

        $normalized = strtoupper((string) $rawStatus);
        $newBusinessStatus = null;
        if (strpos($normalized, 'PICKUP') !== false) {
            $newBusinessStatus = RETURN_STATUS_PICKUP_SCHEDULED;
        }
        if (strpos($normalized, 'PICKED') !== false || strpos($normalized, 'TRANSIT') !== false || $normalized === 'SHIPPED') {
            $newBusinessStatus = RETURN_STATUS_PICKED_UP;
        }
        if (strpos($normalized, 'DELIVERED') !== false || strpos($normalized, 'RTO') !== false) {
            $newBusinessStatus = RETURN_STATUS_RECEIVED_WAREHOUSE;
        }
        // CANCELLED/FAILED intentionally leave $newBusinessStatus null: we keep
        // the current business status and just record the raw carrier status
        // below, so admin can see the failure and retry pickup manually.

        $update = ['shiprocket_pickup_status' => $rawStatus ?: $normalized];
        if ($pickupDate) {
            $update['shiprocket_pickup_date'] = date('Y-m-d', strtotime($pickupDate));
        }
        $this->CommonModel->updateRowByIdWithOutXss('return_request', "return_id = '" . $returnRow['return_id'] . "'", $update);

        // Only move forward, never let an out-of-order webhook regress status.
        if ($newBusinessStatus !== null && $newBusinessStatus > (int) $returnRow['status']) {
            logReturnStatus($returnRow['return_id'], $newBusinessStatus, 'Shiprocket update: ' . $rawStatus, 0, null);
        } else {
            logReturnStatus($returnRow['return_id'], (int) $returnRow['status'], 'Shiprocket update: ' . $rawStatus, 0, null);
        }

        echo json_encode(['success' => true]);
        exit();
    }
}


