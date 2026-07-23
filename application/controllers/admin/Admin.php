<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Admin_Controller {

	protected $currentUser;

	function __construct()
	{
		parent::__construct();
		$this->load->model('admin_model');
		$this->load->library('form_validation');
		$this->load->helper('form');
		
		$this->currentUser = $this->session->userdata('login');

		if (!$this->currentUser) {
			redirect(admin_url('login'));
		}
	}

	public function validate_password_strength($password)
	{
		$message = password_strength_message($password);
		if ($message !== '') {
			$this->form_validation->set_message(__FUNCTION__, $message);
			return false;
		}
		return true;
	}

	public function index()
	{
		if (!admin_can('staff.manage', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có quyền truy cập khu vực này!');
			redirect(admin_url('home'));
		}

		$this->data['message_success'] = $this->session->flashdata('message_success');
		$this->data['message_fail'] = $this->session->flashdata('message_fail');

		$admin = $this->admin_model->get_list();
		$this->data['admin']= $admin;

		$this->render_admin('admin/admin/index');
	}

	public function add()
	{
		if (!admin_can('staff.manage', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có quyền thêm mới thành viên!');
			redirect(admin_url('admin'));
		}

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger" role="alert" style="padding:5px;border-bottom:0px;">', '</div>');

		if ($this->input->post()) {
			$this->form_validation->set_rules('name','Họ tên','required');
			$this->form_validation->set_rules('email','Tên đăng nhập','valid_email|required');
			$this->form_validation->set_rules('password','Mật khẩu','required|callback_validate_password_strength');
			$this->form_validation->set_rules('re_password','Mật khẩu nhập lại','matches[password]');
			$this->form_validation->set_rules('level','Phân quyền','required');
			
			if ($this->form_validation->run()) {
				$level_input = $this->input->post('level');
				
				// MOD không được tạo tài khoản ADMIN
				if (!admin_can('staff.grant_admin', $this->currentUser) && (int) $level_input === ROLE_ADMIN) {
					$this->session->set_flashdata('message_fail', 'Bạn không thể cấp quyền ADMIN cho người khác!');
					redirect(admin_url('admin'));
				}

				$password = $this->input->post('password');
				$data = array(
					'name' => $this->input->post('name'),
					'email' => $this->input->post('email'),
					'password' => hash_user_password($password),
					'level' => $level_input,
					'created' => now()
				);
				if ($this->admin_model->create($data)) {
					$this->session->set_flashdata('message_success', 'Thêm thành viên thành công');
				}else{
					$this->session->set_flashdata('message_fail', 'Thêm thành viên thất bại');
				}
				redirect(admin_url('admin'));
			}
		}

		$this->render_admin('admin/admin/add');
	}

	public function edit()
	{
		if (!admin_can('staff.manage', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có quyền chỉnh sửa thành viên!');
			redirect(admin_url('admin'));
		}

		$id = $this->uri->segment(4);
		$admin = $this->admin_model->get_info($id);

		if (empty($admin)) {
			$this->session->set_flashdata('message_fail', 'Thành viên không tồn tại');
			redirect(admin_url('admin'));
		}

		// MOD không sửa được tài khoản ADMIN
		if ((int) $admin->level === ROLE_ADMIN && !admin_can('staff.edit_admin', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có quyền chỉnh sửa tài khoản của Quản trị tối cao (ADMIN)!');
			redirect(admin_url('admin'));
		}

		if ($this->input->post()) {
			$this->form_validation->set_rules('name','Họ tên','required');
			$this->form_validation->set_rules('email','Tên đăng nhập','valid_email|required');
			$this->form_validation->set_rules('level','Phân quyền','required');
			$password = $this->input->post('password');
			if ($password!='') {
				$this->form_validation->set_rules('password','Mật khẩu','required|callback_validate_password_strength');
				$this->form_validation->set_rules('re_password','Mật khẩu nhập lại','matches[password]');
			}			
			if ($this->form_validation->run()) {
				$level_input = $this->input->post('level');

				// MOD không nâng quyền lên ADMIN
				if (!admin_can('staff.grant_admin', $this->currentUser) && (int) $level_input === ROLE_ADMIN) {
					$this->session->set_flashdata('message_fail', 'Bạn không được phép nâng quyền lên ADMIN!');
					redirect(admin_url('admin'));
				}

				$data = array(
					'name' => $this->input->post('name'),
					'email' => $this->input->post('email'),
					'level' => $level_input
				);
				if ($password!='') {
					$data['password'] = hash_user_password($password);
				}
				if ($this->admin_model->update($id,$data)) {
					$this->session->set_flashdata('message_success', 'Thay đổi thông tin thành viên thành công');
				}else{
					$this->session->set_flashdata('message_fail', 'Thay đổi thông tin thành viên thất bại');
				}
				redirect(admin_url('admin'));
			}
		}

		$this->data['admin']= $admin;
		
		$this->render_admin('admin/admin/edit');
	}

	public function del()
	{
		if (!admin_can('staff.delete', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có đủ quyền hạn để xóa thành viên!');
			redirect(admin_url('admin'));
		}

		$id = $this->uri->segment(4);
		$admin_to_delete = $this->admin_model->get_info($id);

		if (!$admin_to_delete) {
			$this->session->set_flashdata('message_fail', 'Admin không tồn tại');
			redirect(admin_url('admin'));
		}

		// Không xóa tài khoản ADMIN
		if ($admin_to_delete->level == ROLE_ADMIN) {
			$this->session->set_flashdata('message_fail', 'Hệ thống bảo vệ: Không thể xóa tài khoản Quản trị tối cao (ADMIN)!');
			redirect(admin_url('admin'));
		}

		if ($this->admin_model->delete($id)) {
			$this->session->set_flashdata('message_success', 'Xóa admin thành công');
		}else{
			$this->session->set_flashdata('message_fail', 'Xóa admin thất bại');
		}
		redirect(admin_url('admin'));
	}

	public function logout()
	{
		if ($this->session->userdata('login')) {
			$this->session->unset_userdata('login');
		}
		$this->session->set_flashdata('message_success', 'Bạn đã đăng xuất.');
		admin_no_cache_headers();
		redirect(admin_url('login'));
	}
}