<?php

class Auth extends CI_Controller
{

    public function index() {}


    public function registration()
    {
        // Set validation rules
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[user_registration.email_id]', ['is_unique' => 'This %s already exists.']);
        $number = $this->input->post('number');
        if (!empty($number)) {
            $this->form_validation->set_rules('number', 'Number', 'numeric|max_length[10]|is_unique[user_registration.contact_no]', ['is_unique' => 'This %s already exists.', 'max_length' => 'The Number field cannot exceed 10 digits in length.']);
        }
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|callback_password_check');
        $this->form_validation->set_rules('confirm-password', 'Confirm Password', 'required|matches[password]');
        $this->form_validation->set_rules('terms_condition', 'Terms and condition', 'required', ['required' => 'Check to agree terms and condition.']);

        // Run validation
        if ($this->form_validation->run() == FALSE) {
            // Validation failed
            echo json_encode(['success' => false, 'validation' => false,  'message' => $this->form_validation->error_array()]);
            exit();
        }


        // If validation passes, collect form data
        $formData = array(
            'name' => $this->input->post('name'),
            'email_id' => $this->input->post('email'),
            'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
        );
        if (!empty($number)) {
            $formData['contact_no'] = $number;
        }

        // Simulating an update operation
        $insertData = $this->CommonModel->insertRowReturnId('user_registration', $formData); // Assume update operation is successful
        if ($insertData) {
            $this->session->set_userdata('login_user_id', $insertData);

            $smtp = $this->CommonModel->getSingleRowById('mail_smtp_setting', ['id' => 1]);
            sendTemplatedMail('user_registered_user', $formData['email_id'], ['name' => $formData['name'], 'app_name' => APP_NAME]);
            sendTemplatedMail('user_registered_admin', @$smtp['notify_email'], ['name' => $formData['name'], 'email' => $formData['email_id'], 'app_name' => APP_NAME]);

            echo json_encode(['success' => true, 'message' => 'Registered Successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Already Up to date']);
        }
        exit();
    }

    public function login()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        // Run validation
        if ($this->form_validation->run() == FALSE) {
            // Validation failed
            echo json_encode(['success' => false, 'validation' => false, 'message' => $this->form_validation->error_array()]);
            exit();
        }


        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->CommonModel->getSingleRowById('user_registration', ['email_id' => $email]);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                if ($user['user_status'] != '1') {
                    echo json_encode(['success' => false, 'message' => 'Your account has been blocked. Please contact support.']);
                    exit();
                }
                $this->session->set_userdata('login_user_id', $user['user_id']);
                echo json_encode(['success' => true, 'message' => 'Login successful.']);
            } else {
                // Password is incorrect
                echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
            }
        } else {
            // User not found
            echo json_encode(['success' => false, 'message' => 'User not found with this email.']);
        }

        exit();
    }


    public function password_check($password)
    {
        if (preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/', $password)) {
            return TRUE; // Password is valid
        } else {
            // Set custom error message
            $this->form_validation->set_message(['password_check' => 'The {field} must be at least 6 characters long, with at least one uppercase letter, one lowercase letter, one digit, and one symbol.']);
            return FALSE; // Password is invalid
        }
    }
    public function logout()
    {
        $getSlug = $this->input->get('redirect');
        if ($this->session->has_userdata('login_user_id')) {
            $this->session->unset_userdata('login_user_id');
            if ($getSlug) {
                redirect($getSlug);
            } else {
                redirect(base_url());
            }
        } else {
            redirect(base_url());
        }
    }

    public function resetPassword()
    {
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|callback_password_check');
        $this->form_validation->set_rules('confirm-password', 'Confirm Password', 'required|matches[password]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

        // Run validation
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'validation' => false, 'message' => $this->form_validation->error_array()]);
            exit();
        }

        $email = $this->input->post('email');

        // The OTP step (forgotPassword()) is what actually proves ownership
        // of the email - it must have been completed for this exact email
        // in this session before we allow the password to be changed here.
        if ($this->session->userdata('forgotContact') !== $email) {
            echo json_encode(['success' => false, 'message' => 'Please verify the OTP sent to your email before resetting your password.']);
            exit();
        }

        $update = $this->CommonModel->updateRowById('user_registration', 'email_id', $email, ['password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)]);

        if ($update) {
            $this->session->unset_userdata('forgotContact');
            echo json_encode(['success' => true, 'message' => 'Password reset successfully']);
        } else {
            // User not found
            echo json_encode(['success' => false, 'message' => 'Something went wrong.']);
        }

        exit();
    }

    public function changePassword()
    {
        $userId = sessionId('login_user_id');
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Please login to change your password.']);
            exit();
        }

        $this->form_validation->set_rules('current_password', 'Current Password', 'required');
        $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[6]|callback_password_check');
        $this->form_validation->set_rules('confirm_password', 'Confirm New Password', 'required|matches[new_password]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'validation' => false, 'message' => $this->form_validation->error_array()]);
            exit();
        }

        $user = $this->CommonModel->getSingleRowById('user_registration', ['user_id' => $userId]);
        if (!$user || !password_verify($this->input->post('current_password'), $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
            exit();
        }

        $update = $this->CommonModel->updateRowById('user_registration', 'user_id', $userId, [
            'password' => password_hash($this->input->post('new_password'), PASSWORD_DEFAULT),
        ]);

        if ($update) {
            echo json_encode(['success' => true, 'message' => 'Password changed successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again.']);
        }
        exit();
    }

    public function forgotPassword()
    {
        $email = $this->input->post('email');
        $otp = $this->input->post('otp');

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

        if (!empty($otp)) {
            $this->form_validation->set_rules('otp', 'OTP', 'required|numeric|min_length[6]|max_length[6]', [
                'min_length' => 'The OTP must be exactly 6 digits long.',
                'max_length' => 'The OTP must be exactly 6 digits long.'
            ]);
        }

        // Run validation
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'validation' => false, 'message' => $this->form_validation->error_array()]);
            exit();
        }

        // Check if user exists
        $user = $this->CommonModel->getSingleRowById('user_registration', ['email_id' => $email]);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found with this email.']);
            exit();
        }

        if (empty($otp)) {
            // Generate a 6-digit OTP
            $generatedOtp = rand(100000, 999999);
            $expiresAt = date("Y-m-d H:i:s", strtotime("+5 minutes")); // Set OTP expiry time

            // Store OTP in the database
            $otpData = [
                'email_id' => $email,
                'otp' => $generatedOtp,
                'expires_at' => $expiresAt
            ];

            $existingOtp = $this->CommonModel->getSingleRowById('temp_otp', ['email_id' => $email]);

            if ($existingOtp) {
                $this->CommonModel->updateRowById('temp_otp', 'email_id', $email, $otpData);
            } else {
                $this->CommonModel->insertRow('temp_otp', $otpData);
            }

            $sent = sendTemplatedMail('forgot_password_otp', $email, ['name' => $user['name'], 'otp' => $generatedOtp, 'app_name' => APP_NAME]);

            if (!$sent) {
                echo json_encode(['success' => false, 'message' => 'Unable to send OTP email. Please try again later.']);
                exit();
            }

            echo json_encode(['success' => true, 'message' => 'OTP has been sent to ' . $email, 'contact' => $email]);
        } else {
            // Verify OTP
            $otpRecord = $this->CommonModel->getSingleRowById('temp_otp', ['email_id' => $email]);

            if ($otpRecord) {
                $storedOtp = $otpRecord['otp'];
                $otpExpiry = strtotime($otpRecord['expires_at']);
                $currentTimestamp = time();

                if ($storedOtp == $otp) {
                    if ($currentTimestamp > $otpExpiry) {
                        echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new one.']);
                    } else {
                        // Consume the OTP so it can't be replayed to verify again.
                        $this->CommonModel->deleteRowById('temp_otp', ['email_id' => $email]);
                        $this->session->set_userdata('forgotContact', $email);
                        echo json_encode(['success' => true, 'message' => 'OTP verified successfully. Proceed to reset password.', 'contact' => $email]);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid OTP. Please try again.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'No OTP request found for this email.']);
            }
        }
    }
    
    public function deleteUser()
    {
        $id = $this->input->get('user_id');
        $result = ['status' => false, 'message' => 'Enter valid user id'];
        echo json_encode($result);
    }
}
