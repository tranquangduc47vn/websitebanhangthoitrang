<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Frontend_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
		$this->load->model('user_address_model');
		$this->load->model('user_phone_model');
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
			$this->user_phone_model->ensure_legacy_from_user_row($user_id, $user_row->phone);
		}

		$this->data['user_addresses'] = $this->user_address_model->list_for_user($user_id);
		$this->data['address_max'] = User_address_model::MAX_PER_USER;
		$this->data['address_count'] = count($this->data['user_addresses']);
		$this->data['user_phones'] = $this->user_phone_model->list_for_user($user_id);
		$this->data['phone_max'] = User_phone_model::MAX_PER_USER;
		$this->data['phone_count'] = count($this->data['user_phones']);
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

	public function validate_password_strength($password)
	{
		$message = password_strength_message($password);
		if ($message !== '') {
			$this->form_validation->set_message(__FUNCTION__, $message);
			return false;
		}
		return true;
	}

	public function password_change()
	{
		$user_login = $this->_require_login();
		$user_id = (int) $user_login->id;

		if (!$this->_is_post_request()) {
			redirect(base_url('user'));
		}

		$user_row = $this->user_model->get_info($user_id);
		if (!$user_row) {
			$this->session->set_flashdata('message_fail', 'Không tìm thấy tài khoản.');
			redirect(base_url('user'));
		}

		$this->form_validation->set_rules('old_password', 'Mật khẩu hiện tại', 'required');
		$this->form_validation->set_rules('password', 'Mật khẩu mới', 'required|callback_validate_password_strength');
		$this->form_validation->set_rules('re_password', 'Nhập lại mật khẩu mới', 'required|matches[password]');

		if (!$this->form_validation->run()) {
			$this->session->set_flashdata('message_fail', strip_tags(validation_errors(' ', ' ')));
			redirect(base_url('user#jm-account-profile'));
		}

		$verified = verify_user_password($this->input->post('old_password'), $user_row->password);
		if ($verified === false) {
			$this->session->set_flashdata('message_fail', 'Mật khẩu hiện tại không đúng.');
			redirect(base_url('user#jm-account-profile'));
		}

		$new_password = $this->input->post('password');
		$this->user_model->update($user_id, array('password' => hash_user_password($new_password)));

		$this->session->set_flashdata('message_success', 'Đã đổi mật khẩu thành công.');
		redirect(base_url('user#jm-account-profile'));
	}

	public function profile_update()
	{
		$user_login = $this->_require_login();
		$user_id = (int) $user_login->id;

		if (!$this->input->post()) {
			redirect(base_url('user'));
		}

		$this->form_validation->set_rules('name', 'Họ tên', 'required|trim|max_length[100]');
		if (!$this->form_validation->run()) {
			$this->session->set_flashdata('message_fail', strip_tags(validation_errors(' ', ' ')));
			redirect(base_url('user#jm-account-profile'));
		}

		$name = trim($this->input->post('name'));
		$this->user_model->update($user_id, array('name' => $name));
		$this->_refresh_session_user($user_id);

		$this->session->set_flashdata('message_success', 'Đã cập nhật họ tên.');
		redirect(base_url('user#jm-account-profile'));
	}

	public function phone_add()
	{
		$user_login = $this->_require_login();
		$user_id = (int) $user_login->id;

		if (!$this->input->post()) {
			redirect(base_url('user'));
		}

		if ($this->user_phone_model->count_for_user($user_id) >= User_phone_model::MAX_PER_USER) {
			$this->session->set_flashdata('message_fail', 'Bạn chỉ được lưu tối đa ' . User_phone_model::MAX_PER_USER . ' số điện thoại.');
			redirect(base_url('user#phones'));
		}

		$this->form_validation->set_rules('phone_number', 'Số điện thoại', 'required|trim|callback_validate_user_phone');
		$this->form_validation->set_rules('phone_label', 'Ghi chú', 'trim|max_length[50]');

		if (!$this->form_validation->run()) {
			$this->session->set_flashdata('message_fail', strip_tags(validation_errors(' ', ' ')));
			redirect(base_url('user#phones'));
		}

		$phone_number = $this->_normalize_phone($this->input->post('phone_number'));
		if ($this->user_phone_model->find_by_number($user_id, $phone_number)) {
			$this->session->set_flashdata('message_fail', 'Số điện thoại này đã có trong sổ của bạn.');
			redirect(base_url('user#phones'));
		}

		$make_default = $this->input->post('is_default') ? 1 : 0;
		if ($this->user_phone_model->count_for_user($user_id) === 0) {
			$make_default = 1;
		}
		if ($make_default) {
			$this->user_phone_model->update_rule(array('user_id' => $user_id), array('is_default' => 0));
		}

		$this->user_phone_model->create(array(
			'user_id' => $user_id,
			'phone_label' => trim((string) $this->input->post('phone_label')),
			'phone_number' => $phone_number,
			'is_default' => $make_default,
			'created' => time(),
		));

		$this->user_phone_model->sync_user_primary_phone($user_id);
		$this->_refresh_session_user($user_id);

		$this->session->set_flashdata('message_success', 'Đã thêm số điện thoại.');
		redirect(base_url('user#phones'));
	}

	public function phone_delete($phone_id = 0)
	{
		$user_login = $this->_require_login();
		$user_id = (int) $user_login->id;
		$phone_id = (int) $phone_id;

		if (!$this->_is_post_request() || $phone_id <= 0) {
			redirect(base_url('user'));
		}

		$row = $this->user_phone_model->get_owned($phone_id, $user_id);
		if (!$row) {
			$this->session->set_flashdata('message_fail', 'Không tìm thấy số điện thoại hoặc bạn không có quyền xóa.');
			redirect(base_url('user#phones'));
		}

		$was_default = (int) $row->is_default === 1;
		$this->user_phone_model->delete($phone_id);

		if ($was_default) {
			$remaining = $this->user_phone_model->list_for_user($user_id);
			if (!empty($remaining)) {
				$this->user_phone_model->set_default($user_id, $remaining[0]->id);
			}
		}

		$this->user_phone_model->sync_user_primary_phone($user_id);
		$this->_refresh_session_user($user_id);

		$this->session->set_flashdata('message_success', 'Đã xóa số điện thoại.');
		redirect(base_url('user#phones'));
	}

	public function phone_default($phone_id = 0)
	{
		$user_login = $this->_require_login();
		$user_id = (int) $user_login->id;
		$phone_id = (int) $phone_id;

		if (!$this->_is_post_request() || $phone_id <= 0) {
			redirect(base_url('user'));
		}

		if (!$this->user_phone_model->set_default($user_id, $phone_id)) {
			$this->session->set_flashdata('message_fail', 'Không thể đặt số mặc định.');
			redirect(base_url('user#phones'));
		}

		$this->user_phone_model->sync_user_primary_phone($user_id);
		$this->_refresh_session_user($user_id);

		$this->session->set_flashdata('message_success', 'Đã cập nhật số điện thoại mặc định.');
		redirect(base_url('user#phones'));
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
			redirect(base_url('user#addresses'));
		}

		$address_line = vn_address_format_line(
			$this->input->post('province_id'),
			$this->input->post('district_id'),
			$this->input->post('ward_id'),
			$this->input->post('address_note')
		);
		if ($address_line === '') {
			$this->session->set_flashdata('message_fail', 'Địa chỉ không hợp lệ. Vui lòng chọn lại tỉnh, quận, phường.');
			redirect(base_url('user#addresses'));
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
		redirect(base_url('user#addresses'));
	}

	public function address_delete($address_id = 0)
	{
		$user_login = $this->_require_login();
		$user_id = (int) $user_login->id;
		$address_id = (int) $address_id;

		if (!$this->_is_post_request() || $address_id <= 0) {
			redirect(base_url('user'));
		}

		$row = $this->user_address_model->get_owned($address_id, $user_id);
		if (!$row) {
			$this->session->set_flashdata('message_fail', 'Không tìm thấy địa chỉ hoặc bạn không có quyền xóa.');
			redirect(base_url('user#addresses'));
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
		redirect(base_url('user#addresses'));
	}

	public function address_default($address_id = 0)
	{
		$user_login = $this->_require_login();
		$user_id = (int) $user_login->id;
		$address_id = (int) $address_id;

		if (!$this->_is_post_request() || $address_id <= 0) {
			redirect(base_url('user'));
		}

		if (!$this->user_address_model->set_default($user_id, $address_id)) {
			$this->session->set_flashdata('message_fail', 'Không thể đặt địa chỉ mặc định.');
			redirect(base_url('user#addresses'));
		}

		$this->user_address_model->sync_user_primary_address($user_id);
		$this->_refresh_session_user($user_id);

		$this->session->set_flashdata('message_success', 'Đã cập nhật địa chỉ mặc định.');
		redirect(base_url('user#addresses'));
	}

	protected function _is_post_request()
	{
		return strtolower($this->input->method(TRUE)) === 'post';
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
			$this->form_validation->set_rules('password','Mật khẩu','required|callback_validate_password_strength');
			$this->form_validation->set_rules('re_password','Mật khẩu nhập lại','matches[password]');
			$this->form_validation->set_rules('province_id', 'Tỉnh / Thành phố', 'required|trim|callback_validate_vn_address');
			$this->form_validation->set_rules('district_id', 'Quận / Huyện', 'required|trim');
			$this->form_validation->set_rules('ward_id', 'Phường / Xã', 'required|trim');
			$this->form_validation->set_rules('address_note', 'Ghi chú địa chỉ', 'required|trim|max_length[255]');
			$this->form_validation->set_rules('phone','Điện thoại','required|trim|callback_validate_user_phone');
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
					'password' => hash_user_password($password),
					'address' => $address_line,
					'phone' => $this->input->post('phone'),
					'created' => time()
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
					$reg_phone = $this->_normalize_phone($this->input->post('phone'));
					if ($user_id > 0 && $reg_phone !== '') {
						$this->user_phone_model->create(array(
							'user_id' => $user_id,
							'phone_label' => '',
							'phone_number' => $reg_phone,
							'is_default' => 1,
							'created' => time(),
						));
					}

					$new_user = $this->user_model->get_info($user_id);
					if ($new_user) {
						$this->load->library('mail_service');
						if (!$this->mail_service->send_welcome($new_user)) {
							log_message('error', 'Welcome email failed for user_id=' . $user_id);
						}
					}

					$this->session->set_flashdata('message_success', 'Đăng ký tài khoản thành công! Vui lòng đăng nhập tại đây.');
					redirect(base_url('dang-nhap'));
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

	public function validate_user_phone($phone)
	{
		$phone = $this->_normalize_phone($phone);
		if ($phone === '') {
			$this->form_validation->set_message('validate_user_phone', 'Số điện thoại không được để trống.');
			return false;
		}
		$digits = preg_replace('/\D/', '', $phone);
		if (strlen($digits) < 9 || strlen($digits) > 15) {
			$this->form_validation->set_message('validate_user_phone', 'Số điện thoại không hợp lệ (9–15 chữ số).');
			return false;
		}
		return true;
	}

	protected function _normalize_phone($phone)
	{
		return preg_replace('/\s+/', '', trim((string) $phone));
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
		$email = trim((string) $this->input->post('email'));
		$password = $this->input->post('password');
		$user = $this->user_model->get_info_rule(array('email' => $email));
		if (!$user) {
			return false;
		}

		$verified = verify_user_password($password, $user->password);
		if ($verified === true) {
			unset($user->password);
			return $user;
		}
		if ($verified === 'rehash') {
			$this->user_model->update($user->id, array('password' => hash_user_password($password)));
			unset($user->password);
			return $user;
		}

		return false;
	}

	public function forgot_password()
	{
		$this->data['message_success'] = $this->session->flashdata('message_success');
		$this->data['message_fail'] = $this->session->flashdata('message_fail');
		$this->form_validation->set_error_delimiters('<span class="jm-auth-field-error">', '</span>');

		if ($this->input->post()) {
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
			if ($this->form_validation->run()) {
				$email = trim($this->input->post('email'));
				$user = $this->user_model->get_info_rule(array('email' => $email));

				if ($user) {
					$this->load->model('password_reset_model');
					$token = generate_reset_token();
					$this->password_reset_model->create_token($user->id, $token);

					$this->load->library('mail_service');
					if (!$this->mail_service->send_password_reset($user, $token)) {
						log_message('error', 'Password reset email failed for user_id=' . (int) $user->id);
					}
				}

				$this->session->set_flashdata(
					'message_success',
					'Nếu email tồn tại trong hệ thống, chúng tôi đã gửi hướng dẫn đặt lại mật khẩu. Vui lòng kiểm tra hộp thư (và cả thư rác).'
				);
				redirect(base_url('quen-mat-khau'));
			}
		}

		$this->render_frontend_standalone('site/user/forgot_password');
	}

	public function reset_password($token = '')
	{
		$this->load->model('password_reset_model');
		$this->data['message_success'] = $this->session->flashdata('message_success');
		$this->data['message_fail'] = $this->session->flashdata('message_fail');
		$this->form_validation->set_error_delimiters('<span class="jm-auth-field-error">', '</span>');

		$token = trim((string) $token);
		if ($token === '' && $this->input->post('token')) {
			$token = trim((string) $this->input->post('token'));
		}

		$reset_row = ($token !== '') ? $this->password_reset_model->find_valid_by_token($token) : false;

		if ($this->input->post()) {
			if (!$reset_row) {
				$this->session->set_flashdata('message_fail', 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.');
				redirect(base_url('quen-mat-khau'));
			}

			$this->form_validation->set_rules('password', 'Mật khẩu mới', 'required|callback_validate_password_strength');
			$this->form_validation->set_rules('re_password', 'Nhập lại mật khẩu', 'required|matches[password]');

			if ($this->form_validation->run()) {
				$reset_row = $this->password_reset_model->find_valid_by_token($token);
				if (!$reset_row) {
					$this->session->set_flashdata('message_fail', 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.');
					redirect(base_url('quen-mat-khau'));
				}

				$this->user_model->update(
					$reset_row->user_id,
					array('password' => hash_user_password($this->input->post('password')))
				);
				$this->password_reset_model->mark_used($reset_row->id);

				$this->session->set_flashdata('message_success', 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập.');
				redirect(base_url('dang-nhap'));
			}
		}

		if ($token === '' || !$reset_row) {
			$this->data['token_valid'] = false;
		} else {
			$this->data['token_valid'] = true;
			$this->data['reset_token'] = $token;
		}

		$this->render_frontend_standalone('site/user/reset_password');
	}

	public function logout()
	{
		if ($this->session->userdata('user')) {
			$this->session->unset_userdata('user');
		}
		$this->session->unset_userdata('custom_cart');
		$this->session->unset_userdata('checkout_voucher');
		$this->session->unset_userdata('redirect_back');
		redirect(base_url());
	}
}