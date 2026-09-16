<?php

class AdminAuth extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function admin()
	{
		if (sessionId('admin_id') != '') {
			redirect('dashboard');
		} else {
			if (count($_POST) > 0) {
				$this->form_validation->set_rules('contact_no', 'contact number', 'required');
				$this->form_validation->set_rules('password', 'password', 'required');
				$this->form_validation->set_error_delimiters('<div style="color: red;">', '</div>');
				if ($this->form_validation->run()) {
					$phone = $this->input->post('contact_no');
					$password = $this->input->post('password');
					$get = $this->CommonModel->getSingleRowById('admin_login', ['contact_no' => $phone]);
					if ($get) {
						$id = $get['admin_id'];
						$name = $get['name'];
						$f_password = $get['password'];
						$status = $get['status'];

						// Admin passwords used to be stored with the reversible encryptId()
						// cipher (the same helper used to obfuscate IDs in URLs, not a real
						// password hash). Verify against a real bcrypt hash first; fall back
						// to the legacy scheme for accounts that haven't logged in since the
						// migration, and transparently re-hash them on success so every
						// account ends up on password_hash() over time.
						$passwordOk = password_verify($password, $f_password);
						if (!$passwordOk && encryptId($password) === $f_password) {
							$passwordOk = true;
							$this->CommonModel->updateRowById('admin_login', 'admin_id', $id, [
								'password' => password_hash($password, PASSWORD_DEFAULT),
							]);
						}

						if (!$passwordOk) {
							$this->CommonModel->logAdminActivity(1, $id, 'admin_login_failed', 'admin', $id, null, ['reason' => 'bad_password']);
							flashData('login_error', 'Enter a valid Password.');
						} else if ($status == '0') {
							$this->CommonModel->logAdminActivity(1, $id, 'admin_login_failed', 'admin', $id, null, ['reason' => 'blocked']);
							flashData('login_error', 'You are blocked.');
						} else {
							// Regenerate the session id on privilege escalation so a
							// session id captured/fixated before login (e.g. from a
							// shared kiosk or an intercepted pre-auth request) can
							// never carry into an authenticated, privileged session.
							session_regenerate_id(true);
							setSession(array(
								'admin_id' => $id,
								'admin_name' => $name,
								'privileges' => $get['privileges'],
								'user_type' => $get['user_type'],
							));
							$this->CommonModel->logAdminActivity(1, $id, 'admin_login', 'admin', $id);
							redirect('dashboard');
						}
					} else {
						flashData('login_error', 'Enter a valid Contact Number');
					}
				}
			}
			$this->load->view('admin/login');
		}
	}

	public function adminLogout()
	{
		$adminId = sessionId('admin_id');
		if ($adminId != '') {
			$this->CommonModel->logAdminActivity(1, $adminId, 'admin_logout', 'admin', $adminId);
		}
		// Clear the full admin session, not just admin_id/admin_name - leaving
		// privileges/user_type behind was a stale-privilege residue risk on
		// shared machines even though setSession() on the next login always
		// overwrites both keys.
		$this->session->unset_userdata(['admin_id', 'admin_name', 'privileges', 'user_type']);
		session_regenerate_id(true);
		redirect('admin');
	}

	public function deleteUser($id)
	{
		echo json_encode(['status' => false, 'message' => 'enter valid user id']);
	}
}
