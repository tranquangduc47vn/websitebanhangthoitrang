<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Frontend_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
		$this->load->model('user_address_model');
		$this->load->library('form_validation');
		$this->load->helper('form');
	}

public function index()
	{
		$user_login = $this->_require_login();
		$user_id = (int) $user_login->id;

		$this->load->helper('vn_address');

		$user_row = $this->user_model->get_info($user_id);
		if ($user_row) {
			$this->user_address_model->ensure_legacy_from_user_row($user_id, $user_row->address);
		}

		$this->data['user_addresses'] = $this->user_address_model->list_for_user($user_id);
		$this->data['address_max'] = User_address_model::MAX_PER_USER;
		$this->data['address_count'] = count($this->data['user_addresses']);
		$this->data['vn_address_json_url'] = vn_address_json_url();

		$this->data['message_success'] = $this->session->flashdata('message_success');
		$this->data['message_fail'] = $this->session->flashdata('message_fail');

		$this->load->model('transaction_model');

		$input = array();

		if (isset($user_login->id)) {
			$input['where'] = array('user_id' => $user_login->id);
		} else {
			$input['where'] = array('user_email' => $user_login->email);
		}

		$input['order'] = array('id', 'DESC');

		$list_transaction = $this->transaction_model->get_list($input);
		$this->data['list_transaction'] = $list_transaction;

		$this->load->helper('loyalty');
		$this->load->model('loyalty_model');
		$this->load->model('voucher_model');
		$this->data['loyalty'] = $this->loyalty_model->get_profile($user_id);
		$this->data['available_vouchers'] = $this->voucher_model->list_available_for_user($user_id);
		if ($user_row) {
			$this->data['user'] = $user_row;
		}

		$this->data['temp'] = 'site/user/index.php';
		$this->load->view('site/layoutsub', $this->data);
	}

	public function address_add()
	{
		$user_login = $this->_require_login();
		$user_id = (int) $user_login->id;
		$this->load->helper('vn_address');

		if (!$this->input->post()) {
			redirect(base_url('user'));
		}

		if ($this->user_address_model->count_for_user($user_id) >= User_address_model::MAX_PER_USER) {
			$this->session->set_flashdata('message_fail', 'Bạn chỉ được lưu tối đa ' . User_address_model::MAX_PER_USER . ' địa chỉ giao hàng.');
			redirect(base_url('user'));
		}

		$this->form_validation->set_rules('province_id', 'Tỉnh / Thành phố', 'required|trim|callback_validate_vn_address');
		$this->form_validation->set_rules('district_id', 'Quận / Huyện', 'required|trim');
		$this->form_validation->set_rules('ward_id', 'Phường / Xã', 'required|trim');
		$this->form_validation->set_rules('address_note', 'Ghi chú địa chỉ', 'required|trim|max_length[255]');

		if (!$this->form_validation->run()) {
			$this->session->set_flashdata('message_fail', strip_tags(validation_errors(' ', ' ')));
			redirect(base_url('user#jm-account-addresses'));
		}

		$address_line = vn_address_format_line(
			$this->input->post('province_id'),
			$this->input->post('district_id'),
			$this->input->post('ward_id'),
			$this->input->post('address_note')
		);
		if ($address_line === '') {
			$this->session->set_flashdata('message_fail', 'Địa chỉ không hợp lệ. Vui lòng chọn lại tỉnh, quận, phường.');
			redirect(base_url('user#jm-account-addresses'));
		}

		$make_default = $this->input->post('is_default') ? 1 : 0;
		if ($this->user_address_model->count_for_user($user_id) === 0) {
			$make_default = 1;
		}
		if ($make_default) {
			$this->user_address_model->update_rule(array('user_id' => $user_id), array('is_default' => 0));
		}

		$this->user_address_model->create(array(
			'user_id' => $user_id,
			'address_note' => trim($this->input->post('address_note')),
			'province_id' => trim($this->input->post('province_id')),
			'district_id' => trim($this->input->post('district_id')),
			'ward_id' => trim($this->input->post('ward_id')),
			'address_line' => $address_line,
			'is_default' => $make_default,
			'created' => time(),
		));

		$this->user_address_model->sync_user_primary_address($user_id);
		$this->_refresh_session_user($user_id);

		$this->session->set_flashdata('message_success', 'Đã thêm địa chỉ giao hàng.');
		redirect(base_url('user#jm-account-addresses'));
	}

	public function address_delete($address_id = 0)
	{
		$user_login = $this->_require_login();
		$user_id = (int) $user_login->id;
		$address_id = (int) $address_id;

		if (!$this->input->post() || $address_id <= 0) {
			redirect(base_url('user'));
		}

		$row = $this->user_address_model->get_owned($address_id, $user_id);
		if (!$row) {
			$this->session->set_flashdata('message_fail', 'Không tìm thấy địa chỉ hoặc bạn không có quyền xóa.');
			redirect(base_url('user#jm-account-addresses'));
		}

		$was_default = (int) $row->is_default === 1;
		$this->user_address_model->delete($address_id);

		if ($was_default) {
			$remaining = $this->user_address_model->list_for_user($user_id);
			if (!empty($remaining)) {
				$this->user_address_model->set_default($user_id, $remaining[0]->id);
			}
		}

		$this->user_address_model->sync_user_primary_address($user_id);
		$this->_refresh_session_user($user_id);

		$this->session->set_flashdata('message_success', 'Đã xóa địa chỉ.');
		redirect(base_url('user#jm-account-addresses'));
	}

	public function address_default($address_id = 0)
	{
		$user_login = $this->_require_login();
		$user_id = (int) $user_login->id;
		$address_id = (int) $address_id;

		if (!$this->input->post() || $address_id <= 0) {
			redirect(base_url('user'));
		}

		if (!$this->user_address_model->set_default($user_id, $address_id)) {
			$this->session->set_flashdata('message_fail', 'Không thể đặt địa chỉ mặc định.');
			redirect(base_url('user#jm-account-addresses'));
		}

		$this->user_address_model->sync_user_primary_address($user_id);
		$this->_refresh_session_user($user_id);

		$this->session->set_flashdata('message_success', 'Đã cập nhật địa chỉ mặc định.');
		redirect(base_url('user#jm-account-addresses'));
	}

	protected function _require_login()
	{
		$user_login = $this->session->userdata('user');
		if (!isset($user_login)) {
			redirect(base_url('user/login'));
		}
		if (empty($user_login->id)) {
			$row = $this->user_model->get_info_rule(array('email' => $user_login->email));
			if ($row) {
				$user_login->id = $row->id;
				$this->session->set_userdata('user', $user_login);
			}
		}
		return $user_login;
	}

	protected function _refresh_session_user($user_id)
	{
		$user_row = $this->user_model->get_info((int) $user_id);
		if (!$user_row) {
			return;
		}
		$session_user = $this->session->userdata('user');
		if (!$session_user) {
			return;
		}
		$session_user->address = $user_row->address;
		$session_user->name = $user_row->name;
		$session_user->phone = $user_row->phone;
		$this->session->set_userdata('user', $session_user);
		$this->data['user'] = $session_user;
	}

	public function register()
	{
		$this->load->helper('vn_address');

		$message_success = $this->session->flashdata('message_success');
		$this->data['message_success'] = $message_success;

		$message_fail = $this->session->flashdata('message_fail');
		$this->data['message_fail'] = $message_fail;

		$this->form_validation->set_error_delimiters('<span class="jm-auth-field-error">', '</span>');
		if ($this->input->post()) {
			$this->form_validation->set_rules('name','Họ tên','required');
			$this->form_validation->set_rules('email', 'Email đăng nhập', 'required|valid_email|callback_check_email');
			$this->form_validation->set_rules('password','Mật khẩu','required');
			$this->form_validation->set_rules('re_password','Mật khẩu nhập lại','matches[password]');
			$this->form_validation->set_rules('province_id', 'Tỉnh / Thành phố', 'required|trim|callback_validate_vn_address');
			$this->form_validation->set_rules('district_id', 'Quận / Huyện', 'required|trim');
			$this->form_validation->set_rules('ward_id', 'Phường / Xã', 'required|trim');
			$this->form_validation->set_rules('address_note', 'Ghi chú địa chỉ', 'required|trim|max_length[255]');
			$this->form_validation->set_rules('phone','Điện thoại','required|trim');
			if ($this->form_validation->run()) {
				$password = $this->input->post('password');
				$address_line = vn_address_format_line(
					$this->input->post('province_id'),
					$this->input->post('district_id'),
					$this->input->post('ward_id'),
					$this->input->post('address_note')
				);
				$data = array(
					'name' => $this->input->post('name'),
					'email' => $this->input->post('email'),
					'password' => md5($password),
					'address' => $address_line,
					'phone' => $this->input->post('phone'),
					'created' => date('Y-m-d H:i:s')
				);
				if ($this->user_model->create($data)) {
					$user_id = (int) $this->db->insert_id();
					if ($user_id > 0 && $address_line !== '') {
						$this->user_address_model->create(array(
							'user_id' => $user_id,
							'address_note' => trim($this->input->post('address_note')),
							'province_id' => trim($this->input->post('province_id')),
							'district_id' => trim($this->input->post('district_id')),
							'ward_id' => trim($this->input->post('ward_id')),
							'address_line' => $address_line,
							'is_default' => 1,
							'created' => time(),
						));
					}
					$this->session->set_flashdata('message_success', 'Đăng ký tài khoản thành công! Vui lòng đăng nhập tại đây.');
					redirect(base_url('user/login'));
				}else{
					$this->session->set_flashdata('message_fail', 'Đăng ký thất bại, vui lòng kiểm tra lại!');
					redirect(base_url('user/register'));
				}
			}
		}
		$this->render_frontend_standalone('site/user/register');
	}

	function check_email()
	{
		$email = $this->input->post('email');
		$where = array('email'=> $email);
		if ($this->user_model->check_exists($where))
		{
			$this->form_validation->set_message(__FUNCTION__,'Tên đăng nhập đã tồn tại');
			return FALSE;
		}
		return TRUE;
	}

	public function validate_vn_address()
	{
		$province_id = $this->input->post('province_id');
		$district_id = $this->input->post('district_id');
		$ward_id = $this->input->post('ward_id');

		if (!vn_address_lookup($province_id, $district_id, $ward_id)) {
			$this->form_validation->set_message('validate_vn_address', 'Vui lòng chọn đúng Tỉnh/Thành, Quận/Huyện và Phường/Xã trên danh sách Việt Nam.');
			return false;
		}
		return true;
	}

	public function login()
	{
		$this->form_validation->set_error_delimiters('<span class="jm-auth-field-error">', '</span>');
		
		$user = $this->session->userdata('user');
		if(isset($user)) {
			$redirect_url = $this->session->userdata('redirect_back') ? $this->session->userdata('redirect_back') : build_cart_url();
			$this->session->unset_userdata('redirect_back');
			redirect($redirect_url);
		}

		$message_success = $this->session->flashdata('message_success');
		$this->data['message_success'] = $message_success;

		$message_fail = $this->session->flashdata('message_fail');
		$this->data['message_fail'] = $message_fail;

		if ($this->input->post()) {
			$this->form_validation->set_rules('email', 'Email đăng nhập', 'required|valid_email');
			$this->form_validation->set_rules('password', 'Mật khẩu', 'required');
			
			if ($this->form_validation->run())
			{

				$user_data = $this->get_info_user();
				if($user_data) 
				{

					$this->session->set_userdata('user', $user_data);


					$redirect_url = $this->session->userdata('redirect_back') ? $this->session->userdata('redirect_back') : base_url();
					$this->session->unset_userdata('redirect_back');
					redirect($redirect_url);
				} 
				else 
				{

					$this->data['message_fail'] = 'Sai thông tin email hoặc mật khẩu, vui lòng thử lại!';
				}
			}
		}
		$this->render_frontend_standalone('site/user/login');
	}

	public function get_info_user()
	{
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$where = array ('email' => $email, 'password' => md5($password));
		$user = $this->user_model->get_info_rule($where);
		return $user;
	}

	public function logout()
	{
		if ($this->session->userdata('user')) {
			$this->session->unset_userdata('user');
		}
		redirect(base_url());
	}
}