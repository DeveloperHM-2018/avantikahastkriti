<?php

// Storefront (session) authentication. An account is identified by a mobile
// number OR an email address, and OTP is the primary way in; a password is
// optional and only ever an alternative.
//
// Every OTP endpoint is two-phase on one URL: posted without `otp` it sends a
// code to the identifier, posted again with `otp` it verifies and completes.
// A mobile/email is only ever written to an account after an OTP proved the
// customer owns it, because either one can be used to log in.
class Auth extends CI_Controller
{
    const RESET_WINDOW_SECONDS = 600;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('OtpModel');
    }

    public function index() {}

    public function otpLogin()
    {
        $identifier = $this->postedIdentifier();
        $user = $this->activeUserFor($identifier);

        if ($this->postedOtp() === '') {
            $this->json($this->OtpModel->send($identifier, 'login', $user['name']));
        }
        $this->verifyOtpOrFail($identifier, 'login');

        $this->startSession($user['user_id']);
        $this->json(['success' => true, 'message' => 'Login successful.']);
    }

    public function login()
    {
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->validateOrFail();
        $identifier = $this->postedIdentifier();
        $user = $this->activeUserFor($identifier);

        if (!userHasPassword($user)) {
            $this->json(['success' => false, 'message' => 'This account has no password yet. Please login with OTP instead.']);
        }
        if (!password_verify((string) $this->input->post('password'), $user['password'])) {
            $this->json(['success' => false, 'message' => 'Incorrect password.']);
        }

        $this->startSession($user['user_id']);
        $this->json(['success' => true, 'message' => 'Login successful.']);
    }

    public function registration()
    {
        $password = (string) $this->input->post('password');
        $this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[100]');
        if ($password !== '') {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]|callback_password_check');
        }
        $this->form_validation->set_rules('terms_condition', 'Terms and condition', 'required', ['required' => 'Check to agree terms and condition.']);
        $this->validateOrFail();

        $identifier = $this->postedIdentifier();
        if ($this->CommonModel->getSingleRowById('user_registration', [$identifier['column'] => $identifier['value']])) {
            $this->json(['success' => false, 'validation' => false, 'message' => ['identifier' => 'This ' . $identifier['label'] . ' is already registered. Please login instead.']]);
        }

        $name = trim($this->input->post('name'));
        if ($this->postedOtp() === '') {
            $this->json($this->OtpModel->send($identifier, 'register', $name));
        }
        $this->verifyOtpOrFail($identifier, 'register');

        $formData = [
            'name' => $name,
            $identifier['column'] => $identifier['value'],
        ];
        if ($password !== '') {
            $formData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        $userId = $this->CommonModel->insertRowReturnId('user_registration', $formData);
        if (!$userId) {
            $this->json(['success' => false, 'message' => 'Something went wrong. Please try again.']);
        }
        $this->startSession($userId);

        $smtp = $this->CommonModel->getSingleRowById('mail_smtp_setting', ['id' => 1]);
        if ($identifier['type'] == 'email') {
            sendTemplatedMail('user_registered_user', $identifier['value'], ['name' => $name, 'app_name' => APP_NAME]);
        } else {
            sendTemplatedWhatsapp('user_registered_user', $identifier['value'], ['name' => $name]);
        }
        sendTemplatedMail('user_registered_admin', @$smtp['notify_email'], ['name' => $name, 'email' => $identifier['value'], 'app_name' => APP_NAME]);
        sendTemplatedWhatsapp('user_registered_admin', WHATSAPP_ADMIN_NUMBER, ['name' => $name, 'email' => $identifier['value']]);

        $this->json(['success' => true, 'message' => 'Registered Successfully']);
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
        $this->session->unset_userdata(['login_user_id', 'reset_user_id', 'reset_expires']);
        // Only a plain internal path - never a full URL handed in by a link.
        $getSlug = (string) $this->input->get('redirect');
        redirect(preg_match('#^[A-Za-z0-9/_-]+$#', $getSlug) ? $getSlug : base_url());
    }

    // Step 1 of a password reset: prove ownership of the account's mobile or
    // email by OTP. Only that proof (held in the session for a few minutes)
    // lets resetPassword() below change anything.
    public function forgotPassword()
    {
        $identifier = $this->postedIdentifier();
        $user = $this->activeUserFor($identifier);

        if ($this->postedOtp() === '') {
            $this->json($this->OtpModel->send($identifier, 'reset', $user['name']));
        }
        $this->verifyOtpOrFail($identifier, 'reset');

        $this->session->set_userdata([
            'reset_user_id' => $user['user_id'],
            'reset_expires' => time() + self::RESET_WINDOW_SECONDS,
        ]);
        $this->json(['success' => true, 'message' => 'OTP verified successfully. Proceed to reset password.']);
    }

    public function resetPassword()
    {
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|callback_password_check');
        $this->form_validation->set_rules('confirm-password', 'Confirm Password', 'required|matches[password]');
        $this->validateOrFail();

        $userId = passwordResetUserId();
        $user = $userId ? $this->CommonModel->getSingleRowById('user_registration', ['user_id' => $userId]) : false;
        if (!$user || $user['user_status'] != '1') {
            $this->json(['success' => false, 'message' => 'Your verification has expired. Please verify the OTP again to reset your password.']);
        }

        $this->CommonModel->updateRowById('user_registration', 'user_id', $userId, ['password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)]);
        // The OTP already proved who this is, so there is no reason to send
        // them back to a login form to type the password they just chose.
        $this->startSession($userId);
        $this->json(['success' => true, 'message' => 'Password reset successfully.']);
    }

    // Doubles as "set a password" for an account that has none yet (OTP
    // sign-up, mobile app, guest checkout) - there is no current one to ask for.
    public function changePassword()
    {
        $user = $this->sessionUserOrFail('Please login to change your password.');
        $hasPassword = userHasPassword($user);

        if ($hasPassword) {
            $this->form_validation->set_rules('current_password', 'Current Password', 'required');
        }
        $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[6]|callback_password_check');
        $this->form_validation->set_rules('confirm_password', 'Confirm New Password', 'required|matches[new_password]');
        $this->validateOrFail();

        if ($hasPassword && !password_verify((string) $this->input->post('current_password'), $user['password'])) {
            $this->json(['success' => false, 'message' => 'Current password is incorrect.']);
        }

        $update = $this->CommonModel->updateRowById('user_registration', 'user_id', $user['user_id'], [
            'password' => password_hash($this->input->post('new_password'), PASSWORD_DEFAULT),
        ]);

        if ($update) {
            $this->json(['success' => true, 'message' => $hasPassword ? 'Password changed successfully.' : 'Password set successfully.']);
        }
        $this->json(['success' => false, 'message' => 'Something went wrong. Please try again.']);
    }

    // Add or change the mobile number / email on the logged-in account. The
    // OTP goes to the NEW value: it becomes a way to log in to this account,
    // so the customer has to show it is theirs first.
    public function updateContact()
    {
        $user = $this->sessionUserOrFail('Please login to update your details.');
        $identifier = $this->postedIdentifier();

        if (strcasecmp((string) $user[$identifier['column']], $identifier['value']) === 0) {
            $this->json(['success' => false, 'validation' => false, 'message' => ['identifier' => 'This is already your ' . $identifier['label'] . '.']]);
        }
        if ($this->CommonModel->getSingleRowById('user_registration', [$identifier['column'] => $identifier['value']])) {
            $this->json(['success' => false, 'validation' => false, 'message' => ['identifier' => 'This ' . $identifier['label'] . ' is already in use by another account.']]);
        }

        if ($this->postedOtp() === '') {
            $this->json($this->OtpModel->send($identifier, 'contact', $user['name']));
        }
        $this->verifyOtpOrFail($identifier, 'contact');

        $this->CommonModel->updateRowById('user_registration', 'user_id', $user['user_id'], [$identifier['column'] => $identifier['value']]);
        $this->json(['success' => true, 'message' => ucfirst($identifier['label']) . ' updated successfully.', 'type' => $identifier['type'], 'value' => $identifier['value']]);
    }

    public function deleteUser()
    {
        $id = $this->input->get('user_id');
        $result = ['status' => false, 'message' => 'Enter valid user id'];
        echo json_encode($result);
    }

    private function json($data)
    {
        echo json_encode($data);
        exit();
    }

    private function validateOrFail()
    {
        if ($this->form_validation->run() == FALSE) {
            $this->json(['success' => false, 'validation' => false, 'message' => $this->form_validation->error_array()]);
        }
    }

    private function postedIdentifier()
    {
        $identifier = parseLoginIdentifier($this->input->post('identifier'));
        if (!$identifier) {
            $this->json(['success' => false, 'validation' => false, 'message' => ['identifier' => 'Enter a valid 10-digit mobile number or email address.']]);
        }
        return $identifier;
    }

    private function postedOtp()
    {
        return trim((string) $this->input->post('otp'));
    }

    private function verifyOtpOrFail($identifier, $purpose)
    {
        $result = $this->OtpModel->verify($identifier, $purpose, $this->postedOtp());
        if (!$result['success']) {
            $this->json($result);
        }
    }

    // The account behind a login/reset identifier - or the request ends here.
    // `register` tells the page to offer sign-up with what was typed.
    private function activeUserFor($identifier)
    {
        $user = $this->CommonModel->getSingleRowById('user_registration', [$identifier['column'] => $identifier['value']]);
        if (!$user) {
            $this->json(['success' => false, 'register' => true, 'message' => 'No account found with this ' . $identifier['label'] . '.']);
        }
        if ($user['user_status'] != '1') {
            $this->json(['success' => false, 'message' => 'Your account has been blocked. Please contact support.']);
        }
        return $user;
    }

    private function sessionUserOrFail($message)
    {
        $userId = sessionId('login_user_id');
        $user = $userId ? $this->CommonModel->getSingleRowById('user_registration', ['user_id' => $userId]) : false;
        if (!$user) {
            $this->json(['success' => false, 'message' => $message]);
        }
        return $user;
    }

    // New session id on every login, so an id planted in the browser
    // beforehand is worthless once the customer signs in.
    private function startSession($userId)
    {
        $this->session->sess_regenerate(TRUE);
        $this->session->unset_userdata(['reset_user_id', 'reset_expires']);
        $this->session->set_userdata('login_user_id', $userId);
    }
}
