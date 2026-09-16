<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Self-service profile/password management for any logged-in admin (super
// admin or sub-admin) - deliberately gated only by being logged in, not by a
// privilege key, since every admin must always be able to manage their own
// account; no privilege-escalation fields (user_type, privileges, status)
// are ever editable here.
class AdminProfile extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (sessionId('admin_id') == "") {
			redirect("admin");
		}
	}

	public function profile()
	{
		$data['title'] = 'My Profile';
		$data['admin'] = $this->CommonModel->getSingleRowById('admin_login', ['admin_id' => sessionId('admin_id')]);
		$this->load->view('admin/profile', $data);
	}

	public function updateProfile()
	{
		$adminId = sessionId('admin_id');
		$this->form_validation->set_rules('name', 'Name', 'trim|required');
		$this->form_validation->set_rules('contact_no', 'Contact Number', 'trim|required');
		$this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');

		if (!$this->form_validation->run()) {
			flashData('errors', validation_errors());
			redirect('adminProfile');
			return;
		}

		$before = $this->CommonModel->getSingleRowById('admin_login', ['admin_id' => $adminId]);
		$update = [
			'name' => $this->input->post('name'),
			'contact_no' => $this->input->post('contact_no'),
			'email_id' => $this->input->post('email_id'),
		];
		$this->CommonModel->updateRowById('admin_login', 'admin_id', $adminId, $update);
		$this->CommonModel->logAdminActivity(ACTOR_TYPE_ADMIN, $adminId, 'admin_profile_update', 'admin', $adminId, [
			'name' => $before['name'],
			'contact_no' => $before['contact_no'],
			'email_id' => $before['email_id'],
		], $update);

		// Session copy of the admin's display name must stay in sync -
		// header.php reads sessionId('admin_name'), not the DB row.
		setSession(['admin_name' => $update['name']]);

		flashData('errors', 'Profile updated successfully.');
		redirect('adminProfile');
	}

	public function changePassword()
	{
		$adminId = sessionId('admin_id');
		$this->form_validation->set_rules('current_password', 'Current Password', 'required');
		$this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[6]');
		$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[new_password]');
		$this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');

		if (!$this->form_validation->run()) {
			flashData('errors', validation_errors());
			redirect('adminProfile');
			return;
		}

		$admin = $this->CommonModel->getSingleRowById('admin_login', ['admin_id' => $adminId]);
		$currentPassword = $this->input->post('current_password');
		// Same legacy-cipher fallback AdminAuth::admin() uses at login, so an
		// account that hasn't logged in since the bcrypt migration can still
		// change its password here.
		$passwordOk = password_verify($currentPassword, $admin['password'])
			|| encryptId($currentPassword) === $admin['password'];

		if (!$passwordOk) {
			flashData('errors', 'Current password is incorrect.');
			redirect('adminProfile');
			return;
		}

		$this->CommonModel->updateRowById('admin_login', 'admin_id', $adminId, [
			'password' => password_hash($this->input->post('new_password'), PASSWORD_DEFAULT),
		]);
		$this->CommonModel->logAdminActivity(ACTOR_TYPE_ADMIN, $adminId, 'admin_password_change', 'admin', $adminId);

		flashData('errors', 'Password changed successfully.');
		redirect('adminProfile');
	}
}
