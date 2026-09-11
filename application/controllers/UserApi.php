<?php

use avantika\Libraries\REST_Controller;
use Razorpay\Api\Api;

require APPPATH . '/libraries/REST_Controller.php';
require_once APPPATH . '../vendor/autoload.php';

class UserApi extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel');
    }

    public function stateApi_GET()
    {
        $get = $this->CommonModel->getAllRowsInOrder('state', 'state_name', 'ASC');
        if ($get) {
            foreach ($get as $list) {
                $all[] = array(
                    'state_id' => $list['state_id'],
                    'state_name' => $list['state_name']
                );
            }
            $this->response(array('status' => 200, 'message' => 'Show all state', 'data' => $all));
        } else {
            $this->response(array('status' => 400, 'message' => 'No Data Found', 'data' => null));
        }
    }

    public function cityApi_GET($state_id)
    {
        $get = $this->CommonModel->getRowByIdInOrder('city', ['state_id' => $state_id], 'city_name', 'ASC');
        if ($get) {
            foreach ($get as $cityList) {
                $all[] = array(
                    'city_id' => $cityList['city_id'],
                    'city_name' => $cityList['city_name'],
                );
            }
            $this->response(array('status' => 200, 'message' => 'Show all city', 'data' => $all));
        } else {
            $this->response(array('status' => 400, 'message' => 'Something went wrong. Please try again', 'data' => null));
        }
    }

    public function appContent_GET($type)
    {
        $get = $this->CommonModel->getSingleRowById('add_on_data', ['id' => $type]);
        if ($get) {
            $this->response(array('status' => 200, 'message' => 'Show content', 'data' => $get));
        } else {
            $this->response(array('status' => 400, 'message' => 'Something went wrong. Please try again', 'data' => null));
        }
    }

    public function userSendOTP_POST()
    {
        extract($this->input->post());
        $this->form_validation->set_rules('contact_no', 'contact number', 'trim|required');
        $this->form_validation->set_rules('hash_key', 'hash Key', 'trim|required');
        if ($this->form_validation->run()) {
            $get = $this->CommonModel->getSingleRowById('user_registration', ['contact_no' => $contact_no]);
            // OTP_ENABLE is only false in local DEV (see config.php) so a real SMS
            // gateway isn't required to test the login flow on a dev machine.
            // There is no bypass of any kind once this runs in production.
            $otp = OTP_ENABLE ? random_int(100000, 999999) : 12345;
            $message_content = "Hi, Your OTP for verify your mobile number is " . $otp . " From " . APP_NAME . " . Valid for 10 minutes. Please do not share this OTP.\nRegards,\n\nGNOSISACCRUE Team";
            $this->CommonModel->insertRow('temp_otp', [
                'contact_no' => $contact_no,
                'otp' => $otp,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+10 minutes')),
            ]);
            if ($get && $get['user_status'] != '1') {
                $this->response(array('status' => 400, 'message' => 'Your account has been blocked. Please contact tech support.', 'data' => null));
                return;
            }
            if (OTP_ENABLE) {
                sendOTP($contact_no, $message_content);
            }
            $this->response(array('status' => 200, 'message' => 'OTP send successfully.', 'data' => null), REST_Controller::HTTP_OK);
        } else {
            $this->response(array('status' => 400, 'message' => $this->form_validation->error_array(), 'data' => null));
        }
    }

    public function userLogin_POST()
    {
        extract($this->input->post());
        $this->form_validation->set_rules('contact_no', 'contact number', 'trim|required');
        $this->form_validation->set_rules('otp', 'otp', 'trim|required');
        $this->form_validation->set_rules('fcm_token', 'fcm_token', 'trim');
        if ($this->form_validation->run()) {
            $getOtp = $this->CommonModel->getSingleRowByIdInOrder('temp_otp', ['contact_no' => $contact_no], 'id', 'DESC');
            $otpExpired = $getOtp && !empty($getOtp['expires_at']) && strtotime($getOtp['expires_at']) < time();
            if ($getOtp && ($getOtp['otp'] == $otp) && !$otpExpired) {
                $getUser = $this->CommonModel->getSingleRowById('user_registration', ['contact_no' => $contact_no]);
                $hash = date('dm') . round(microtime(true) * 1000);
                $this->CommonModel->deleteRowById('temp_otp', ['contact_no' => $contact_no]);
                if ($getUser) {
                    $this->CommonModel->updateRowById('user_registration', 'user_id', $getUser['user_id'], array('unique_hash' => $hash, 'fcm_token' => $fcm_token));
                    $token_data = array(
                        'id' => $getUser['user_id'],
                        'name' => $getUser['name'],
                        'contact_no' => $getUser['contact_no'],
                        'unique_hash' => $hash,
                        'time' => time()
                    );
                    $token = $this->authorization_token->generateToken($token_data);
                    $getLocation = $this->CommonModel->getSingleRowById('user_address', "user_id = '{$getUser['user_id']}' AND is_default = '1'");
                    $data = array(
                        'name' => $getUser['name'],
                        'contact_no' => $getUser['contact_no'],
                        'email_id' => $getUser['email_id'],
                        'profile_image' =>  $getUser['profile_image'] == "" ? null : $getUser['profile_image'],
                        'is_registered' => $getUser['is_profile_complete'],
                        'verify_status' => $getUser['verify_status'],
                        'user_code' => $getUser['user_code'],
                        'latitude' => $getLocation ? $getLocation['latitude'] : 0,
                        'longitude' => $getLocation ? $getLocation['longitude'] : 0,
                        'token' => $token
                    );
                    $this->response(array('status' => 200, 'message' => 'User login successfully.', 'data' => $data), REST_Controller::HTTP_OK);
                } else {
                    $post = array(
                        'contact_no' => $contact_no,
                        'unique_hash' => $hash,
                        'fcm_token' => isset($fcm_token) ? $fcm_token : null,
                        'create_date' => setDateTime(),
                        'user_code' => referIdGenerate(),
                    );
                    $insertId = $this->CommonModel->insertRowReturnId('user_registration', $post);

                    $smtp = $this->CommonModel->getSingleRowById('mail_smtp_setting', ['id' => 1]);
                    sendTemplatedMail('user_registered_admin', @$smtp['notify_email'], ['name' => $contact_no, 'email' => '-', 'app_name' => APP_NAME]);

                    $token_data = array(
                        'id' => $insertId,
                        'contact_no' => $contact_no,
                        'unique_hash' => $hash,
                        'time' => time()
                    );
                    $token = $this->authorization_token->generateToken($token_data);
                    $data = array(
                        'name' => null,
                        'email_id' => null,
                        'contact_no' => $contact_no,
                        'user_code' => $post['user_code'],
                        'profile_image' => null,
                        'latitude' => 0,
                        'longitude' => 0,
                        'is_registered' => 0,
                        'verify_status' => 1,
                        'token' => $token
                    );
                    $this->response(array('status' => 200, 'message' => 'User login successfully.', 'data' => $data));
                }
            } else {
                $message = $otpExpired ? 'OTP has expired. Please request a new one.' : 'Enter Valid OTP';
                $this->response(array('status' => 400, 'message' => $message, 'data' => null));
            }
        } else {
            $this->response(array('status' => 400, 'message' => str_replace("\n", '', validation_errors()), 'data' => null));
        }
    }

    public function userProfileCreate_POST()
    {
        $token = $this->authorization_token->validateToken();
        if (!empty($token) and $token['status'] != 0) {
            extract($this->input->post());
            if ($getUser = getUserId($token)) {
                $tokenId = $token['data']->id;
                $this->form_validation->set_rules('name', 'name', 'trim|required', ['required' => 'Name is required']);
                if ($email_id != $getUser['email_id']) {
                    $this->form_validation->set_rules('email_id', 'Email Id', 'trim|is_unique[user_registration.email_id]', ['is_unique' => 'Email Id already exist.']);
                }
                $this->form_validation->set_rules('address', 'Address', 'trim|required');
                $this->form_validation->set_rules('area', 'Area', 'trim|required');
                $this->form_validation->set_rules('postal_code', 'Postal Code', 'trim|required');
                $this->form_validation->set_rules('state', 'State', 'trim|required');
                $this->form_validation->set_rules('city', 'City', 'trim|required');
                $this->form_validation->set_rules('latitude', 'Latitude', 'trim|required');
                $this->form_validation->set_rules('longitude', 'Longitude', 'trim|required');
                $this->form_validation->set_error_delimiters('', ' ');
                if ($this->form_validation->run()) {
                    $post['name'] = $name;
                    $email_id == "" ? null : $post['email_id'] = $email_id;
                    $post['is_profile_complete'] = 1;

                    $post_address['name'] = $name;
                    $post_address['contact_no'] = $getUser['contact_no'];
                    $post_address['address'] = $address;
                    $post_address['area'] = $area;
                    $post_address['postal_code'] = $postal_code;
                    $post_address['state'] = $state;
                    $post_address['city'] = $city;
                    $post_address['latitude'] = $latitude;
                    $post_address['longitude'] = $longitude;
                    $post_address['is_default'] = 1;
                    $post_address['user_id'] = $tokenId;

                    $picture = "";
                    if (!empty($_FILES['profile_image']['name'])) {
                        $picture  = fullImage('profile_image', PROFILE_IMAGE, $getUser['profile_image']);
                        $post['profile_image'] = $picture;
                    }

                    $this->CommonModel->updateRowByMoreId('user_registration', ['user_id' => $token['data']->id], $post);
                    $this->CommonModel->insertRow('user_address', $post_address);
                    $data = array(
                        'name' => ucwords($name),
                        'contact_no' => $getUser['contact_no'],
                        'email_id' => $email_id,
                        'profile_image' => $picture == "" ? null : $picture,
                    );
                    $this->response(array('status' => 200, 'message' => 'Profile create successfully.', 'data' => $data), REST_Controller::HTTP_OK);
                } else {
                    $this->response(array('status' => 400, 'message' => str_replace("\n", '', validation_errors()), 'data' => null));
                }
            } else {
                $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(array('status' => 401, 'message' => $token['message'], 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function userProfileUpdate_POST()
    {
        $token = $this->authorization_token->validateToken();
        if (!empty($token) and $token['status'] != 0) {
            extract($this->input->post());
            if ($getUser = getUserId($token)) {
                $tokenId = $token['data']->id;
                $this->form_validation->set_rules('name', 'name', 'trim|required', ['required' => 'Name is required']);
                if ($email_id != $getUser['email_id']) {
                    $this->form_validation->set_rules('email_id', 'Email Id', 'trim|is_unique[user_registration.email_id]', ['is_unique' => 'Email Id already exist.']);
                }
                $this->form_validation->set_error_delimiters('', ' ');
                if ($this->form_validation->run()) {
                    $post['name'] = $name;
                    $email_id == "" ? null : $post['email_id'] = $email_id;

                    $picture = "";
                    if (!empty($_FILES['profile_image']['name'])) {
                        $picture  = fullImage('profile_image', PROFILE_IMAGE, $getUser['profile_image']);
                        $post['profile_image'] = $picture;
                    }

                    $update = $this->CommonModel->updateRowByMoreId('user_registration', ['user_id' => $token['data']->id], $post);
                    if ($update) {
                        $data = array(
                            'name' => ucwords($name),
                            'contact_no' => $getUser['contact_no'],
                            'email_id' => $email_id,
                            'profile_image' => $picture == "" ? null : $picture,
                        );
                        $this->response(array('status' => 200, 'message' => 'Profile update successfully.', 'data' => $data), REST_Controller::HTTP_OK);
                    } else {
                        $this->response(array('status' => 400, 'message' => 'Profile not update. Please try again', 'data' => null));
                    }
                } else {
                    $this->response(array('status' => 400, 'message' => str_replace("\n", '', validation_errors()), 'data' => null));
                }
            } else {
                $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(array('status' => 401, 'message' => $token['message'], 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function userViewProfile_GET()
    {
        $token = $this->authorization_token->validateToken();
        if (!empty($token) and $token['status'] != 0) {
            extract($this->input->post());
            if ($get = getUserId($token)) {
                $data = [
                    'name' => $get['name'],
                    'email_id' => $get['email_id'],
                    'contact_no' => $get['contact_no'],
                    'user_code' => $get['user_code'],
                    'profile_image' => $get['profile_image'],
                    'wallet_amount' => $get['wallet_amount']
                ];
                $this->response(array('status' => 200, 'message' => 'User view profile.', 'data' => $data), REST_Controller::HTTP_OK);
            } else {
                $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(array('status' => 401, 'message' => $token['message'], 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function userAddress_POST()
    {
        $token = $this->authorization_token->validateToken();
        if (!empty($token) and $token['status'] != 0) {
            extract($this->input->post());
            if ($getUser = getUserId($token)) {
                $tokenId = $token['data']->id;
                $this->form_validation->set_rules('name', 'name', 'trim|required', ['required' => 'Name is required']);
                $this->form_validation->set_rules('contact_no', 'Contact Number', 'trim|required');
                $this->form_validation->set_rules('address', 'Address', 'trim|required');
                $this->form_validation->set_rules('area', 'Area', 'trim|required');
                $this->form_validation->set_rules('postal_code', 'Postal Code', 'trim|required');
                $this->form_validation->set_rules('state', 'State', 'trim|required');
                $this->form_validation->set_rules('city', 'City', 'trim|required');
                $this->form_validation->set_rules('latitude', 'Latitude', 'trim|required');
                $this->form_validation->set_rules('longitude', 'Longitude', 'trim|required');
                $this->form_validation->set_rules('is_default', 'is_default', 'trim|required');
                $this->form_validation->set_error_delimiters('', ' ');
                if ($this->form_validation->run()) {

                    $post_address['name'] = $name;
                    $post_address['contact_no'] = $contact_no;
                    $post_address['address'] = $address;
                    $post_address['area'] = $area;
                    $post_address['postal_code'] = $postal_code;
                    $post_address['state'] = $state;
                    $post_address['city'] = $city;
                    $post_address['latitude'] = $latitude;
                    $post_address['longitude'] = $longitude;
                    $post_address['user_id'] = $tokenId;

                    if ($is_default == 1) {
                        $this->CommonModel->updateRowByMoreId('user_address', ['user_id' => $tokenId], ['is_default' => 0]);
                        $post_address['is_default'] = $is_default;
                    }

                    if (isset($id) && $id != 0) {
                        // Scoped to the caller's own address - without user_id
                        // here, any logged-in user could edit (and reassign to
                        // themselves) any other user's saved address by id.
                        $this->CommonModel->updateRowByMoreId('user_address', ['id' => $id, 'user_id' => $tokenId], $post_address);
                        $this->response(array('status' => 200, 'message' => 'Address update successfully.', 'data' => null));
                    } else {
                        $this->CommonModel->insertRow('user_address', $post_address);
                        $this->response(array('status' => 200, 'message' => 'Address add successfully.', 'data' => null));
                    }
                } else {
                    $this->response(array('status' => 400, 'message' => str_replace("\n", '', validation_errors()), 'data' => null));
                }
            } else {
                $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(array('status' => 401, 'message' => $token['message'], 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function userAddress_GET()
    {
        $token = $this->authorization_token->validateToken();
        if (!empty($token) and $token['status'] != 0) {
            extract($this->input->post());
            if (getUserId($token)) {
                $tokenId = $token['data']->id;
                $get = $this->CommonModel->getRowByIdInOrder('user_address', ['user_id' => $tokenId], 'is_default', 'DESC');
                if ($get) {
                    $this->response(array('status' => 200, 'message' => 'Show all address', 'data' => $get));
                } else {
                    $this->response(array('status' => 400, 'message' => 'No address found', 'data' => null));
                }
            } else {
                $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(array('status' => 401, 'message' => $token['message'], 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function dashboardApi_POST()
    {
        $this->form_validation->set_rules('latitude', 'Latitude', 'trim|required|xss_clean');
        $this->form_validation->set_rules('longitude', 'longitude', 'trim|required|xss_clean');
        $this->form_validation->set_error_delimiters('', '');
        if ($this->form_validation->run()) {
            $token = $this->authorization_token->validateToken();
            if (!empty($token) and $token['status'] != 0) {
                extract($this->input->post());
                if ($getUser = getUserId($token)) {
                    $tokenId = $token['data']->id;
                    $getBanner = $this->CommonModel->getAllRows('banner');
                    $allBanner = [];
                    if ($getBanner) {
                        foreach ($getBanner as $bannerList) {
                            $allBanner[] = $bannerList['image_path'];
                        }
                    } else {
                        $allBanner = null;
                    }

                    $getCategory = $this->CommonModel->getRowByIdInOrder('category', "is_delete = '1'", "category_name", "ASC");

                    $all_product = [];
                    if ($getCategory) {
                        foreach ($getCategory as $c_list) {
                            $all_data = [];

                            $getProduct = $this->CommonModel->getRowByOrderWithLimit('product', "is_delete = '1' AND status = '1' AND product_type = '1'", "update_date", "ASC", 15);
                            if ($getProduct) {
                                foreach ($getProduct as $p_list) {
                                    $getProductImage = $this->CommonModel->getSingleRowById('product_image', "product_id = '{$p_list['product_id']}'");

                                    $p_list['image_thumb'] = $getProductImage ? $getProductImage['image_path'] : '';
                                    $all_data[] = $p_list;
                                }
                            }

                            $c_list['product_list'] = $all_data;
                            $all_product[] = $c_list;
                        }
                    }

                    $getAddress = $this->CommonModel->getSingleRowById('user_address', ['user_id' => $tokenId, 'is_default' => '1']);
                    $getSetting = $this->CommonModel->getSingleRowById('setting', ['id' => '1']);

                    $getDeliveryLocation = $this->CommonModel->getSingleRowById('delivery_charge', "delivery_charge_id = '1'");
                    $is_delivery_available = explode(",", $getDeliveryLocation['is_delivery_available']);
                    $data['is_delivery_available'] = $getDeliveryLocation['is_delivery_available'] != "" ? $is_delivery_available : [];

                    $data = [
                        'banner' => $allBanner,
                        'category' => $getCategory ? $getCategory : [],
                        'sub_category' => [],
                        'brand' => [],
                        'featured_product_list' => $all_product,
                        'verify_status' => $getUser['verify_status'],
                        'wallet_amount' => $getUser['wallet_amount'],
                        'delivery_timing' => $getSetting['delivery_timing'],
                        'is_no_order' => 0,
                        'is_delivery_available' => $is_delivery_available,
                        'city' => $getAddress ? $getAddress['city'] : '',
                        'postal_code' => $getAddress ? $getAddress['postal_code'] : '',
                        'category_image_path' => BASE_URL . CATEGORY_IMAGE,
                        'product_image_path' => BASE_URL . PRODUCT_IMAGE
                    ];

                    $this->response(array('status' => 200, 'message' => 'Show Dashboard Data', 'data' => $data));
                } else {
                    $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
                }
            } else {
                $this->response(array('status' => 401, 'message' => $token['message'], 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(array('status' => 400, 'message' => str_replace("\n", '', validation_errors()), 'data' => null));
        }
    }

    public function getProduct_GET($sub_category_id)
    {
        $token = $this->authorization_token->validateToken();
        if (!empty($token) and $token['status'] != 0) {
            extract($this->input->post());
            if (getUserId($token)) {

                $tokenId = $token['data']->id;
                $select = "product.*, category.category_name as sub_category_name";
                $join = [['category', 'category.category_id = product.category_id', 'LEFT']];
                $get = $this->CommonModel->getRowWithMultiJoin($select, 'product', ['product.category_id' => $sub_category_id, 'product.is_delete' => '1', 'product.status' => '1'], $join, 'product_name', 'ASC', 1);
                if ($get) {
                    foreach ($get as $p_list) {
                        $getProductImage = $this->CommonModel->getSingleRowById('product_image', ['product_id' => $p_list['product_id']]);
                        $p_list['image_thumb'] = $getProductImage ? $getProductImage['image_path'] : '';
                        $all_data[] = $p_list;
                    }
                    $this->response(array('status' => 200, 'message' => 'Show all product', 'data' => $all_data));
                } else {
                    $this->response(array('status' => 400, 'message' => 'No Product Found', 'data' => null));
                }
            } else {
                $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(array('status' => 401, 'message' => $token['message'], 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function getSubCategoryWiseProduct_GET()
    {
        $token = $this->authorization_token->validateToken();
        if (!empty($token) and $token['status'] != 0) {
            extract($this->input->post());
            if (getUserId($token)) {

                $tokenId = $token['data']->id;
                $select = "product.*, category.category_name as sub_category_name";
                $join = [['category', 'category.category_id = product.category_id', 'LEFT']];

                $getSubCategory = $this->CommonModel->getRowByIdInOrder('category', ['is_delete' => '1'], "category_name", "ASC");
                if ($getSubCategory) {
                    foreach ($getSubCategory as $sub_cate_list) {
                        $getProduct = $this->CommonModel->getRowWithMultiJoin($select, 'product', ['product.category_id' => $sub_cate_list['category_id'], 'product.is_delete' => '1', 'product.status' => '1'], $join, 'product_name', 'ASC', 1);

                        $allProduct = [];
                        if ($getProduct) {
                            foreach ($getProduct as $p_list) {
                                $getProductImage = $this->CommonModel->getSingleRowById('product_image', ['product_id' => $p_list['product_id']]);
                                $p_list['image_thumb'] = $getProductImage ? $getProductImage['image_path'] : '';
                                $allProduct[] = $p_list;
                            }
                        }

                        $allData[] = array(
                            'sub_category_id' => $sub_cate_list['category_id'],
                            'sub_category_name' => ucwords($sub_cate_list['category_name']),
                            'sub_category_image' => $sub_cate_list['image'],
                            'sub_category_product' => $allProduct,
                        );
                    }
                    $this->response(array('status' => 200, 'message' => 'Show all product', 'data' => $allData));
                } else {
                    $this->response(array('status' => 400, 'message' => 'No Product Found', 'data' => null));
                }
            } else {
                $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(array('status' => 401, 'message' => $token['message'], 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function searchProduct_POST()
    {
        $this->form_validation->set_rules('search', 'Search', 'trim|required');
        $this->form_validation->set_error_delimiters('', '');
        if ($this->form_validation->run()) {
            $token = $this->authorization_token->validateToken();
            if (!empty($token) and $token['status'] != 0) {
                extract($this->input->post());
                if (getUserId($token)) {
                    $tokenId = $token['data']->id;
                    $select = "product.*, category.category_name as sub_category_name";
                    $join = [['category', 'category.category_id = product.category_id', 'LEFT']];
                    $safeSearch = $this->db->escape_like_str($search);
                    $get = $this->CommonModel->getRowWithMultiJoin($select, 'product', "product.is_delete = '1' AND product.status = '1' AND product_name LIKE '%{$safeSearch}%' ESCAPE '!'", $join, 'product_name', 'ASC', 1);
                    if ($get) {
                        foreach ($get as $p_list) {
                            $getProductImage = $this->CommonModel->getSingleRowById('product_image', ['product_id' => $p_list['product_id']]);
                            $p_list['image_thumb'] = $getProductImage ? $getProductImage['image_path'] : '';
                            $all[] = $p_list;
                        }
                        $this->response(array('status' => 200, 'message' => 'Show all product', 'data' => $all));
                    } else {
                        $this->response(array('status' => 400, 'message' => 'No Product Found', 'data' => null));
                    }
                } else {
                    $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
                }
            } else {
                $this->response(array('status' => 401, 'message' => $token['message'], 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(array('status' => 400, 'message' => str_replace('\n', '', validation_errors()), 'data' => null));
        }
    }

    public function getSingleProduct_GET($product_id)
    {
        $token = $this->authorization_token->validateToken();
        if (!empty($token) and $token['status'] != 0) {
            extract($this->input->post());
            if (getUserId($token)) {
                $tokenId = $token['data']->id;

                $get = $this->CommonModel->getSingleRowById('product', ['product_id' => $product_id]);
                if ($get) {
                    $getProductImage = $this->CommonModel->getRowByMoreId('product_image', ['product_id' => $product_id]);
                    $get['image_thumb'] = $getProductImage ? $getProductImage[0]['image_path'] : '';
                    $get['all_images'] = $getProductImage ? $getProductImage : [];

                    $getProductVariants = $this->CommonModel->getRowByMoreId('product_variants', ['product_id' => $product_id]);
                    $get['product_variants'] = $getProductVariants ? $getProductVariants : [];

                    $this->response(array('status' => 200, 'message' => 'Show Product', 'data' => $get), REST_Controller::HTTP_OK);
                } else {
                    $this->response(array('status' => 400, 'message' => 'Something went wrong. Please try again', 'data' => null), REST_Controller::HTTP_BAD_REQUEST);
                }
            } else {
                $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(array('status' => 401, 'message' => $token['message'], 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function getDeliveryCharge_GET()
    {
        $token = $this->authorization_token->validateToken();
        if (!empty($token) and $token['status'] != 0) {
            extract($this->input->post());
            if (getUserId($token)) {
                $tokenId = $token['data']->id;

                $get = $this->CommonModel->getSingleRowById('delivery_charge', "delivery_charge_id = '1'");
                if ($get) {
                    $data['min_amount'] = $get['min_amount'];
                    $data['amount'] = $get['amount'];
                    $data['packaging_charge'] = $get['packaging_charge'];
                    $data['min_cod_available'] = $get['min_cod_available'];
                } else {
                    $data['min_amount'] = 0;
                    $data['amount'] = 0;
                }

                $data['packaging_charge'] = $get['packaging_charge'];
                $data['min_cod_available'] = $get['min_cod_available'];
                $this->response(array('status' => 200, 'message' => 'Show delivery charges', 'data' => $data));
            } else {
                $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(array('status' => 401, 'message' => $token['message'], 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    // Mirrors Web::evaluateCoupon()'s discount math so the mobile API and the
    // storefront never compute a different rupee amount for the same coupon.
    // $promo is a tbl_promocode row; the minimum-order/expiry/already-used
    // checks are done by the callers before this is invoked.
    private function calculatePromoDiscount($promo, $subtotal)
    {
        $couponAmount = (float) $promo['amount'];
        $isPercentage = (@$promo['discount_type'] === 'percentage');

        $discount = $isPercentage
            ? $subtotal * (min($couponAmount, 100) / 100)
            : $couponAmount;

        return round(min($discount, $subtotal), 2);
    }

    public function promoCodeApply_POST()
    {
        $this->form_validation->set_rules('promocode', 'Promo Code', 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|xss_clean');
        $this->form_validation->set_error_delimiters('', '');
        if ($this->form_validation->run()) {
            $token = $this->authorization_token->validateToken();
            if (!empty($token) and $token['status'] != 0) {
                extract($this->input->post());
                if (getUserId($token)) {
                    $tokenId = $token['data']->id;

                    $getPromoCode = $this->CommonModel->getSingleRowById('promocode', [
                        'expiry_date >=' => setDateOnly(),
                        'promocode' => $promocode,
                    ]);
                    if ($getPromoCode) {
                        if ((float) $amount < (float) $getPromoCode['minimum_order']) {
                            $this->response(array('status' => 400, 'message' => 'Minimum order amount of ' . $getPromoCode['minimum_order'] . ' required for this promo code.', 'data' => null));
                            return;
                        }
                        // Scoped to the current user - each customer may use a given
                        // promo code once, not the whole customer base combined.
                        $checkPromoCde = $this->CommonModel->getNumRows('book_product', [
                            'promocode' => $promocode,
                            'transaction_status' => '1',
                            'user_id' => $tokenId,
                        ]);
                        if ($checkPromoCde < 1) {
                            // $amount here is the caller's cart subtotal (validated
                            // against minimum_order above) - the discount computed
                            // from it, not the coupon's raw configured value, is
                            // what the client should actually deduct.
                            $data['amount'] = $this->calculatePromoDiscount($getPromoCode, (float) $amount);
                            $this->response(array('status' => 200, 'message' => 'Promo Code Applied Successfully', 'data' => $data));
                        } else {
                            $this->response(array('status' => 400, 'message' => 'Promo Code applied only once per user.', 'data' => null));
                        }
                    } else {
                        $this->response(array('status' => 400, 'message' => 'Enter a valid promo code.', 'data' => null));
                    }
                } else {
                    $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
                }
            } else {
                $this->response(array('status' => 401, 'message' => $token['message'], 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(array('status' => 400, 'message' => str_replace("\n", '', validation_errors()), 'data' => null));
        }
    }

    public function createOrder_POST()
    {
        $token = $this->authorization_token->validateToken();
        if (!empty($token) and $token['status'] != 0) {
            extract($this->input->post());
            if ($getUser = getUserId($token)) {
                $tokenId = $token['data']->id;
                $this->form_validation->set_rules('name', 'user name', 'trim|required');
                $this->form_validation->set_rules('contact_no', 'user contact number', 'trim|required');
                $this->form_validation->set_rules('address', 'user address', 'trim|required');
                $this->form_validation->set_rules('area', 'user area', 'trim|required');
                $this->form_validation->set_rules('postal_code', 'Postal Code', 'trim|required');
                $this->form_validation->set_rules('state', 'state', 'trim|required');
                $this->form_validation->set_rules('city', 'city', 'trim|required');
                $this->form_validation->set_rules('latitude', 'latitude', 'trim|required');
                $this->form_validation->set_rules('longitude', 'longitude', 'trim|required');
                $this->form_validation->set_rules('delivery_charges', 'delivery charges', 'trim|required');
                $this->form_validation->set_rules('packaging_charge', 'Packaging Charge', 'trim|required');
                $this->form_validation->set_rules('total_item_amount', 'Total Item Amount', 'trim|required');
                $this->form_validation->set_rules('wallet_amount', 'Wallet Amount', 'trim');
                $this->form_validation->set_rules('final_amount', 'Final Amount', 'trim|required');
                $this->form_validation->set_rules('payment_mode', 'Payment Mode', 'trim|required');
                $this->form_validation->set_rules('promocode_status', 'Promo Code Status', 'trim|required');
                $this->form_validation->set_rules('order_item_list', 'Order Item List', 'trim|required');
                $this->form_validation->set_error_delimiters('', '');
                if ($this->form_validation->run()) {

                    $productList = json_decode($order_item_list);

                    // Price/stock/delivery are always re-derived from the DB here -
                    // never trust what the client sends for these, it's only used
                    // as a "did the client's cart go stale" cross-check below.
                    $items = [];
                    $totalItemAmount = 0;
                    $stockError = null;
                    foreach ($productList as $p) {
                        $product = $this->CommonModel->getSingleRowById('product', ['product_id' => $p->product_id]);
                        if (!$product || $product['is_delete'] != '1' || $product['status'] != '1') {
                            $stockError = 'One or more products in your cart are no longer available.';
                            break;
                        }
                        if ($product['is_out_of_stock'] == 1) {
                            $stockError = $product['product_name'] . ' is currently out of stock.';
                            break;
                        }
                        $qty = (int) $p->no_of_items;
                        if ($qty < 1) {
                            $stockError = 'Invalid quantity for ' . $product['product_name'] . '.';
                            break;
                        }
                        if ($product['max_quantity'] < $qty) {
                            $stockError = 'Insufficient stock for ' . $product['product_name'] . '.';
                            break;
                        }
                        $lineTotal = $product['sale_price'] * $qty;
                        $totalItemAmount += $lineTotal;
                        $items[] = array(
                            'create_date' => setDateTime(),
                            'no_of_items' => $qty,
                            'base_price' => $product['sale_price'],
                            'user_price' => $product['sale_price'],
                            'booking_price' => $lineTotal,
                            'product_id' => $p->product_id,
                        );
                    }
                    if ($stockError) {
                        $this->response(array('status' => 400, 'message' => $stockError, 'data' => null));
                        return;
                    }

                    $charges = $this->CommonModel->getSingleRowById('delivery_charge', ['delivery_charge_id' => '1']);
                    $freeShippingThreshold = $charges ? (float) $charges['min_amount'] : 0;
                    $deliveryCharges = $totalItemAmount >= $freeShippingThreshold ? 0 : ($charges ? (float) $charges['amount'] : 0);
                    $packagingCharge = $charges ? (float) $charges['packaging_charge'] : 0;

                    $promocodeAmount = 0;
                    if ($promocode_status == '1' && !empty($promocode)) {
                        $getPromoCode = $this->CommonModel->getSingleRowById('promocode', [
                            'expiry_date >=' => setDateOnly(),
                            'promocode' => $promocode,
                        ]);
                        $alreadyUsed = $getPromoCode ? $this->CommonModel->getNumRows('book_product', [
                            'promocode' => $promocode,
                            'transaction_status' => '1',
                            'user_id' => $tokenId,
                        ]) : 0;
                        if (!$getPromoCode || $totalItemAmount < (float) $getPromoCode['minimum_order'] || $alreadyUsed > 0) {
                            $this->response(array('status' => 400, 'message' => 'Promo code is no longer valid. Please remove it and try again.', 'data' => null));
                            return;
                        }
                        $promocodeAmount = $this->calculatePromoDiscount($getPromoCode, $totalItemAmount);
                    }

                    // Never spend more wallet balance than the account actually
                    // has, and never more than is left after other discounts -
                    // wallet_amount here was previously an unchecked client value.
                    $availableWallet = isset($getUser['wallet_amount']) ? (float) $getUser['wallet_amount'] : 0;
                    $preWalletTotal = max(0, $totalItemAmount + $deliveryCharges + $packagingCharge - $promocodeAmount);
                    $walletAmount = min((float) $wallet_amount, $availableWallet, $preWalletTotal);
                    $finalAmount = max(0, $preWalletTotal - $walletAmount);

                    // The client's own totals are only used to catch a stale cart on
                    // their end (price/stock changed since they loaded the page) -
                    // the amount that is actually stored/charged is always ours.
                    if (abs((float) $final_amount - $finalAmount) > 1) {
                        $this->response(array('status' => 400, 'message' => 'Your cart total has changed. Please refresh your cart and try again.', 'data' => null));
                        return;
                    }

                    $orderId = orderIdGenerateUser('book_product', 'order_id');
                    $post['user_id'] = $tokenId;
                    $post['order_id'] = $orderId;
                    $post['name'] = $name;
                    $post['contact_no'] = $contact_no;
                    $post['address'] = $address;
                    $post['area'] = $area;
                    $post['postal_code'] = $postal_code;
                    $post['state'] = $state;
                    $post['city'] = $city;
                    $post['latitude'] = $latitude;
                    $post['longitude'] = $longitude;
                    $post['transaction_status'] = 0;

                    $post['delivery_charges'] = $deliveryCharges;
                    $post['packaging_charge'] = $packagingCharge;
                    $post['payment_mode'] = $payment_mode;
                    $post['total_item_amount'] = $totalItemAmount;
                    $post['wallet_amount'] = $walletAmount;
                    $post['final_amount'] = $finalAmount;
                    $post['delivery_date'] = date('Y-m-d', strtotime(setDateOnly() . '+ 1 days'));

                    $post['promocode_status'] = $promocode_status;
                    if ($promocode_status == '1') {
                        $post['promocode_amount'] = $promocodeAmount;
                        $post['promocode'] = $promocode;
                    }
                    $post['booking_date'] = setDateOnly();
                    $product_book_id = $this->CommonModel->insertRowReturnId('book_product', $post);
                    if ($product_book_id) {
                        foreach ($items as &$item) {
                            $item['product_book_id'] = $product_book_id;
                        }
                        unset($item);
                        $this->CommonModel->insertRowInBatch('book_item', $items);

                        if ($walletAmount > 0) {
                            debitWallet($tokenId, $walletAmount, 'Used on order ' . $orderId);
                        }

                        $smtp = $this->CommonModel->getSingleRowById('mail_smtp_setting', ['id' => 1]);
                        sendTemplatedMail('order_placed_user', @$getUser['email_id'], ['name' => $name, 'order_id' => $orderId, 'final_amount' => $finalAmount, 'app_name' => APP_NAME]);
                        sendTemplatedMail('order_placed_admin', @$smtp['notify_email'], ['name' => $name, 'order_id' => $orderId, 'final_amount' => $finalAmount, 'app_name' => APP_NAME]);

                        $this->response(array('status' => 200, 'message' => 'Order book successfully.', 'data' => array('order_id' => $orderId)));
                    } else {
                        $this->response(array('status' => 400, 'message' => 'Something went wrong. Please try again', 'data' => null));
                    }
                } else {
                    $this->response(array('status' => 400, "message" => str_replace("\n", " ", validation_errors()), 'data' => null));
                }
            } else {
                $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(array('status' => 401, 'message' => $token['message'], 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function orderTransactionStatus_POST()
    {
        $token = $this->authorization_token->validateToken();
        if (!empty($token) and $token['status'] != 0) {
            extract($this->input->post());
            if (getUserId($token)) {
                $tokenId = $token['data']->id;
                $user_type = $token['data']->user_type;
                $this->form_validation->set_rules('order_id', 'order_id', 'trim|required');
                $this->form_validation->set_rules('status', 'status', 'trim|required');
                if ($status == '1') {
                    $this->form_validation->set_rules('payment_id', 'payment_id', 'trim|required');
                    $this->form_validation->set_rules('mode', 'mode', 'trim|required');
                    $this->form_validation->set_rules('hash', 'hash', 'trim|required');
                }
                $this->form_validation->set_error_delimiters('', '');
                if ($this->form_validation->run()) {
                    // Always scoped to the caller's own order - without this any
                    // logged-in user could flip the payment status of someone
                    // else's order by guessing/enumerating order_id.
                    $order = $this->CommonModel->getSingleRowById('book_product', [
                        'order_id' => $order_id,
                        'user_id' => $tokenId,
                    ]);
                    if (!$order) {
                        $this->response(array('status' => 400, 'message' => 'Order not found', 'data' => null));
                        return;
                    }
                    if ($order['transaction_status'] != '0') {
                        // Already resolved (paid or failed) - report the existing
                        // state instead of letting the client re-trigger side
                        // effects (emails, double refunds, etc.) by replaying this.
                        $this->response(array('status' => 200, 'message' => 'Order status already recorded.', 'data' => null));
                        return;
                    }

                    if ($status == '1') {
                        // Independently confirm with Razorpay that this payment_id
                        // really was captured and for the right amount, instead of
                        // trusting whatever the client claims here.
                        $api = new Api(RAZOR_PYA_KEY, RAZOR_PYA_SECRET);
                        try {
                            $payment = $api->payment->fetch($payment_id);
                        } catch (Exception $e) {
                            $this->response(array('status' => 400, 'message' => 'Unable to verify payment. Please try again.', 'data' => null));
                            return;
                        }

                        $paidAmountRupees = ((float) $payment->amount) / 100;
                        $amountMatches = abs($paidAmountRupees - (float) $order['final_amount']) < 1;
                        $statusOk = in_array($payment->status, ['captured', 'authorized'], true);

                        if (!$statusOk || !$amountMatches) {
                            $this->response(array('status' => 400, 'message' => 'Payment verification failed.', 'data' => null));
                            return;
                        }

                        // A payment_id can only ever settle one order - block reuse
                        // of the same successful payment across multiple orders.
                        $reused = $this->CommonModel->getNumRows('book_product', [
                            'payment_id' => $payment_id,
                            'transaction_status' => '1',
                        ]);
                        if ($reused > 0) {
                            $this->response(array('status' => 400, 'message' => 'This payment has already been used for another order.', 'data' => null));
                            return;
                        }

                        $update = $this->CommonModel->updateRowByMoreId('book_product', [
                            'order_id' => $order_id,
                            'user_id' => $tokenId,
                            'transaction_status' => '0',
                        ], [
                            'transaction_status' => '1',
                            'payment_id' => $payment_id,
                            'mode' => $mode,
                            'hash' => $hash,
                        ]);
                        if ($update) {
                            $this->response(array('status' => 200, 'message' => 'status update successfully.', 'data' => null));
                        } else {
                            $this->response(array('status' => 400, 'message' => 'Something went wrong. Please try again', 'data' => null));
                        }
                    } else {
                        $this->CommonModel->updateRowByMoreId('book_product', [
                            'order_id' => $order_id,
                            'user_id' => $tokenId,
                            'transaction_status' => '0',
                        ], ['transaction_status' => '2']);
                        $this->response(array('status' => 400, 'message' => 'payment fail. Please try again', 'data' => null));
                    }
                } else {
                    $this->response(array('status' => 400, 'message' => str_replace("\n", " ", validation_errors()), 'data' => null));
                }
            } else {
                $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(array('status' => 401, 'message' => $token['message'], 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function orderHistory_GET()
    {
        $token = $this->authorization_token->validateToken();
        if (!empty($token) and $token['status'] != 0) {
            extract($this->input->post());
            if (getUserId($token)) {
                $tokenId = $token['data']->id;
                $getOrder = $this->CommonModel->getRowByIdInOrder('book_product', ['user_id' => $tokenId], 'create_date', 'DESC');
                if ($getOrder) {
                    $allOrders = [];
                    foreach ($getOrder as $data) {
                        $prodData = $this->CommonModel->getRowById('book_item', 'product_book_id', $data['product_book_id']);
                        if ($prodData) {
                            $allProd = [];
                            foreach ($prodData as $prod) {

                                $select = "product.*, sub_category.sub_category_name";
                                $join = [
                                    ['sub_category', 'sub_category.sub_category_id = product.sub_category_id', 'LEFT'],
                                ];
                                $getProduct = $this->CommonModel->getRowWithMultiJoin($select, 'product', "product.product_id = '{$prod['product_id']}'", $join, 'product_name', 'ASC', 2);
                                $getImages = $this->CommonModel->getRowById('product_image', 'product_id', $prod['product_id']);
                                $returnEligibility = isReturnEligible($prod, $data);

                                $allProd[] = array(
                                    'book_item_id' => $prod['book_item_id'],
                                    'product_id' => $prod['product_id'],
                                    'product_name' => ucwords($getProduct['product_name']),
                                    'category_name' => "",
                                    'sub_category_name' => ucwords($getProduct['sub_category_name']),
                                    'company_name' => "",
                                    "max_quantity" => $getProduct['max_quantity'],
                                    "quantity" => $getProduct['quantity'] . ' ' . $getProduct['quantity_type'],
                                    'no_of_items' => $prod['no_of_items'],
                                    'base_price' => $prod['base_price'],
                                    'price' => $prod['user_price'],
                                    'booking_price' => $prod['booking_price'],
                                    'product_image' => $getImages ? $getImages[0]['image_path'] : '',
                                    'return_eligible' => $returnEligibility['eligible'],
                                    'return_days_left' => $returnEligibility['days_left'],
                                    'return_status' => isset($returnEligibility['return_status']) ? getReturnStatusLabel($returnEligibility['return_status']) : null,
                                );
                            }
                        } else {
                            $allProd = null;
                        }


                        $allOrders[] = array(
                            'book_product_id' => $data['product_book_id'],
                            'create_date' => date('d-M-Y h:i A', strtotime($data['create_date'])),
                            'order_id' => $data['order_id'],
                            'name' => ucwords($data['name']),
                            'contact_no' => $data['contact_no'],
                            'address' => $data['address'],
                            'area' => $data['area'],
                            'postal_code' => $data['postal_code'],
                            'state' => $data['state'],
                            'city' => $data['city'],
                            'booking_status' => $data['booking_status'],
                            'total_amount' => $data['total_item_amount'],
                            'final_amount' => $data['final_amount'],
                            'delivery_charges' => $data['delivery_charges'],
                            'estimated_time' => $data['estimated_time'],
                            'cancel_msg' => $data['cancel_message'],
                            'promocode_status' => $data['promocode_status'],
                            'promocode' => $data['promocode'],
                            'promocode_amount' => $data['promocode_amount'],
                            'payment_mode' => $data['payment_mode'],
                            'transaction_status' => $data['transaction_status'],
                            'payment_id' => $data['payment_id'],
                            'transaction_mode' => $data['transaction_mode'],
                            'payment_hash' => $data['payment_hash'],
                            'invoice_url' => base_url() . 'invoice/' . $data['product_book_id'],
                            'items' => $allProd
                        );
                    }
                    $this->response(array('status' => 200, 'message' => 'Show all order', 'data' => $allOrders));
                } else {
                    $this->response(array('status' => 400, 'message' => 'No Order Available.', 'data' => null));
                }
            } else {
                $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(array('status' => 401, 'message' => $token['message'], 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
        }
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

    public function submitReturnRequest_POST()
    {
        $token = $this->authorization_token->validateToken();
        if (empty($token) || $token['status'] == 0) {
            $this->response(array('status' => 401, 'message' => $token['message'] ?? 'Unauthorized', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }
        if (!getUserId($token)) {
            $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }
        $userId = $token['data']->id;

        $bookItemId = $this->input->post('book_item_id');
        $quantityReturn = $this->input->post('quantity_return');
        $reason = $this->input->post('reason');
        $reasonOtherText = trim((string) $this->input->post('reason_other_text'));
        $remarks = trim((string) $this->input->post('remarks'));

        if (!$bookItemId || !$quantityReturn || !$reason) {
            $this->response(array('status' => 400, 'message' => 'book_item_id, quantity_return and reason are required.', 'data' => null));
            return;
        }
        if (!in_array($reason, self::$returnReasons, true)) {
            $this->response(array('status' => 400, 'message' => 'Invalid return reason.', 'data' => null));
            return;
        }
        if ($reason === 'Other' && $reasonOtherText === '') {
            $this->response(array('status' => 400, 'message' => 'Please describe the reason for your return.', 'data' => null));
            return;
        }

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
            $this->response(array('status' => 404, 'message' => 'Order item not found.', 'data' => null));
            return;
        }

        $bookProduct = ['booking_status' => $row['booking_status'], 'delivery_date' => $row['delivery_date']];
        $eligibility = isReturnEligible($row, $bookProduct);
        if (!$eligibility['eligible']) {
            $messages = [
                'not_delivered' => 'This item is not eligible for return yet.',
                'already_requested' => 'A return request already exists for this item.',
                'window_expired' => 'The return window for this item has expired.',
            ];
            $this->response(array('status' => 400, 'message' => $messages[$eligibility['reason']] ?? 'This item is not eligible for return.', 'data' => null));
            return;
        }
        if ($quantityReturn > $row['no_of_items']) {
            $this->response(array('status' => 400, 'message' => 'Return quantity cannot exceed the quantity purchased.', 'data' => null));
            return;
        }

        $uploadedImages = [];
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        if (!empty($_FILES['return_images']['name'][0])) {
            $filesCount = count($_FILES['return_images']['name']);
            if ($filesCount > MAX_RETURN_IMAGES) {
                $this->response(array('status' => 400, 'message' => 'You can upload a maximum of ' . MAX_RETURN_IMAGES . ' images.', 'data' => null));
                return;
            }
            for ($i = 0; $i < $filesCount; $i++) {
                if ($_FILES['return_images']['name'][$i] === '') {
                    continue;
                }
                $extension = strtolower(pathinfo($_FILES['return_images']['name'][$i], PATHINFO_EXTENSION));
                if (!in_array($extension, $allowedExt, true)) {
                    $this->response(array('status' => 400, 'message' => 'Only JPG, PNG, and WEBP images are allowed.', 'data' => null));
                    return;
                }
                if ($_FILES['return_images']['size'][$i] > MAX_RETURN_IMAGE_SIZE) {
                    $this->response(array('status' => 400, 'message' => 'Each image must be 5 MB or smaller.', 'data' => null));
                    return;
                }
                if ($_FILES['return_images']['error'][$i] !== UPLOAD_ERR_OK) {
                    $this->response(array('status' => 400, 'message' => 'One or more images failed to upload. Please try again.', 'data' => null));
                    return;
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
            $this->response(array('status' => 500, 'message' => 'Failed to submit return request. Please try again.', 'data' => null));
            return;
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

        $this->response(array('status' => 200, 'message' => 'Return Request Submitted Successfully.', 'data' => ['return_code' => $returnData['return_code']]));
    }

    public function returnStatus_GET($id = null)
    {
        $token = $this->authorization_token->validateToken();
        if (empty($token) || $token['status'] == 0) {
            $this->response(array('status' => 401, 'message' => $token['message'] ?? 'Unauthorized', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }
        if (!getUserId($token)) {
            $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }
        $userId = $token['data']->id;

        $returnRow = $this->CommonModel->getSingleRowById('return_request', ['return_id' => $id, 'user_id' => $userId]);
        if (!$returnRow) {
            $this->response(array('status' => 404, 'message' => 'Return request not found.', 'data' => null));
            return;
        }

        $timeline = $this->CommonModel->getRowByIdInOrder('return_status_log', ['return_id' => $id], 'create_date', 'ASC');
        $images = $this->CommonModel->getRowById('return_image', 'return_id', $id);

        $returnRow['status_label'] = getReturnStatusLabel($returnRow['status']);
        $this->response(array('status' => 200, 'message' => 'Return status fetched', 'data' => [
            'return' => $returnRow,
            'timeline' => array_map(function ($t) {
                $t['status_label'] = getReturnStatusLabel($t['status']);
                return $t;
            }, $timeline ?: []),
            'images' => $images ?: [],
        ]));
    }

    public function invoice_GET($id)
    {
        // This previously had no authentication at all and looked up the
        // order by order_id alone - anyone could download any customer's
        // invoice (name, address, phone, order contents) by guessing an id.
        $token = $this->authorization_token->validateToken();
        if (empty($token) || $token['status'] == 0) {
            $this->response(array('status' => 401, 'message' => $token['message'] ?? 'Unauthorized', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }
        if (!getUserId($token)) {
            $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }
        $userId = $token['data']->id;

        $data['all_data'] = $this->CommonModel->getSingleRowById('book_product', ['order_id' => $id, 'user_id' => $userId]);
        if (!$data['all_data']) {
            $this->response(array('status' => 404, 'message' => 'Order not found.', 'data' => null));
            return;
        }
        $data['all_data_items'] = $this->CommonModel->getRowByMoreId('book_item', ['product_book_id' => $data['all_data']['product_book_id']]);


        $this->load->view('admin/invoice_view', $data);
        $html = $this->output->get_output();
        $this->load->library('pdf');
        $this->dompdf->loadHtml($html);
        $this->dompdf->set_option('isRemoteEnabled', true);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream(date('dmYhis'), array("Attachment" => 0));

        // 'compress' => 1 or 0 – enable content stream compression.
        // 'Attachment' => 1 = download or 0 = preview

        // $filePath = "./upload/122.pdf";
        // $output = $this->dompdf->output();
        // file_put_contents($filePath, $output);
    }

    // Check Delivery Location

    public function checkDeliveryLocation_GET()
    {
        $token = $this->authorization_token->validateToken();
        if (!empty($token) and $token['status'] != 0) {
            extract($this->input->post());
            if (getUserId($token)) {
                $tokenId = $token['data']->id;

                $get = $this->CommonModel->getSingleRowById('delivery_charge', "delivery_charge_id = '1'");

                $is_delivery_available = explode(",", $get['is_delivery_available']);
                $data['is_delivery_available'] = $get['is_delivery_available'] != "" ? $is_delivery_available : [];

                $this->response(array('status' => 200, 'message' => 'Show delivery location', 'data' => $data));
            } else {
                $this->response(array('status' => 401, 'message' => 'Unauthorized user', 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(array('status' => 401, 'message' => $token['message'], 'data' => null), REST_Controller::HTTP_UNAUTHORIZED);
        }
    }
}
