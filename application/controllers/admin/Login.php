<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('admin', 'permission'));
		admin_no_cache_headers();
		$login = $this->session->userdata('login');
		if ($login && admin_can_access_panel($login)) {
			redirect(admin_url('home'));
		}
		$this->load->model('admin_model');
		$this->load->library('form_validation');
		$this->load->helper('form');
	}

	public function index()
	{
		$message_success = $this->session->flashdata('message_success');
		$this->data['message_success'] = $message_success;

		$message_fail = $this->session->flashdata('message_fail');
		$this->data['message_fail'] = $message_fail;

		if ($this->input->post()) {
			$this->form_validation->set_rules('email', 'Email đăng nhập', 'required|valid_email');
			$this->form_validation->set_rules('password', 'Mật khẩu', 'required');
			$this->form_validation->set_rules('login', 'login', 'callback_check_login');
			if ($this->form_validation->run()) {
				$user = $this->get_info_login();
				$this->session->set_userdata('login', $user);
				$this->session->set_flashdata('message_success', 'Đăng nhập thành công');
				redirect(admin_url('home'));
			}
		}
		$this->load->view('admin/login/index.php');
	}

	public function check_login()
	{
		$user = $this->get_info_login();
		if (!$user) {
			$this->form_validation->set_message(__FUNCTION__, 'Đăng nhập thất bại');
			return false;
		}
		if (!admin_can_access_panel($user)) {
			$this->form_validation->set_message(__FUNCTION__, 'Tài khoản không có quyền truy cập khu vực quản trị.');
			return false;
		}
		return true;
	}

	function get_info_login()
	{
		$email = trim((string) $this->input->post('email'));
		$password = $this->input->post('password');
		$user = $this->admin_model->get_info_rule(array('email' => $email));
		if (!$user) {
			return false;
		}

		$verified = verify_user_password($password, $user->password);
		if ($verified === true) {
			unset($user->password);
			return $user;
		}
		if ($verified === 'rehash') {
			$this->admin_model->update($user->id, array('password' => hash_user_password($password)));
			unset($user->password);
			return $user;
		}

		return false;
	}
}
