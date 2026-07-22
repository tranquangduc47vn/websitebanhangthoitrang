<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Voucher extends MY_Admin_Controller {

	protected $currentUser;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('voucher_model');
		$this->currentUser = $this->session->userdata('login');
		if (!$this->currentUser) {
			redirect(admin_url('login'));
		}
		if (!admin_can('voucher.manage', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có quyền quản lý voucher!');
			redirect(admin_url('home'));
		}
		$this->load->helper('loyalty');
		$this->load->helper('form');
	}

	public function index()
	{
		$input = array('order' => array('id', 'DESC'));
		$this->data['list'] = $this->voucher_model->get_list($input);
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['message_fail'] = $this->session->flashdata('message_fail');
		$this->render_admin('admin/voucher/index');
	}

	public function add()
	{
		if ($this->input->post('submit')) {
			$data = $this->_form_data_from_post();
			if ($data === false) {
				redirect(admin_url('voucher/add'));
			}
			$data['created'] = time();
			$data['used_count'] = 0;
			if ($this->voucher_model->create($data)) {
				$this->session->set_flashdata('message', 'Tạo voucher thành công.');
			}
			redirect(admin_url('voucher'));
		}
		$this->data['voucher'] = null;
		$this->render_admin('admin/voucher/form');
	}

	public function edit()
	{
		$id = (int) $this->uri->rsegment(3);
		$voucher = $this->voucher_model->get_info($id);
		if (!$voucher) {
			$this->session->set_flashdata('message_fail', 'Voucher không tồn tại.');
			redirect(admin_url('voucher'));
		}
		if ($this->input->post('submit')) {
			$data = $this->_form_data_from_post($id);
			if ($data === false) {
				redirect(admin_url('voucher/edit/' . $id));
			}
			$this->voucher_model->update($id, $data);
			$this->session->set_flashdata('message', 'Cập nhật voucher thành công.');
			redirect(admin_url('voucher'));
		}
		$this->data['voucher'] = $voucher;
		$this->render_admin('admin/voucher/form');
	}

	public function delete()
	{
		$id = (int) $this->uri->rsegment(3);
		if ($this->voucher_model->delete($id)) {
			$this->session->set_flashdata('message', 'Đã xóa voucher.');
		}
		redirect(admin_url('voucher'));
	}

	public function toggle_active()
	{
		$id = (int) $this->uri->rsegment(3);
		$voucher = $this->voucher_model->get_info($id);
		if (!$voucher) {
			if ($this->input->is_ajax_request()) {
				$this->output->set_content_type('application/json', 'utf-8')
					->set_output(json_encode(array('ok' => false, 'message' => 'Voucher không tồn tại.')));
				return;
			}
			$this->session->set_flashdata('message_fail', 'Voucher không tồn tại.');
			redirect(admin_url('voucher'));
		}

		$new_active = ((int) $voucher->is_active === 1) ? 0 : 1;
		$this->voucher_model->update($id, array('is_active' => $new_active));

		if ($this->input->is_ajax_request()) {
			$this->output->set_content_type('application/json', 'utf-8')
				->set_output(json_encode(array(
					'ok' => true,
					'is_active' => $new_active,
					'label' => $new_active ? 'Đang bật' : 'Tắt',
				)));
			return;
		}

		$this->session->set_flashdata('message', $new_active ? 'Đã bật voucher.' : 'Đã tắt voucher.');
		redirect(admin_url('voucher'));
	}

	protected function _form_data_from_post($exclude_id = 0)
	{
		$code = loyalty_normalize_voucher_code($this->input->post('code'));
		if ($code === '') {
			$this->session->set_flashdata('message_fail', 'Mã voucher không hợp lệ.');
			return false;
		}
		$exists = $this->db->where('code', $code)->get('voucher')->row();
		if ($exists && (int) $exists->id !== (int) $exclude_id) {
			$this->session->set_flashdata('message_fail', 'Mã voucher đã tồn tại.');
			return false;
		}

		$type = $this->input->post('discount_type');
		if (!in_array($type, array('percent', 'fixed'), true)) {
			$type = 'fixed';
		}

		$valid_from = trim((string) $this->input->post('valid_from'));
		$valid_to = trim((string) $this->input->post('valid_to'));

		return array(
			'code' => $code,
			'name' => trim((string) $this->input->post('name')),
			'description' => trim((string) $this->input->post('description')),
			'discount_type' => $type,
			'discount_value' => (int) $this->input->post('discount_value'),
			'min_order_amount' => (int) $this->input->post('min_order_amount'),
			'max_discount' => (int) $this->input->post('max_discount'),
			'tier_min' => strtolower(trim((string) $this->input->post('tier_min'))),
			'user_id' => (int) $this->input->post('user_id'),
			'usage_limit' => (int) $this->input->post('usage_limit'),
			'per_user_limit' => max(1, (int) $this->input->post('per_user_limit')),
			'valid_from' => $valid_from !== '' ? strtotime($valid_from) : 0,
			'valid_to' => $valid_to !== '' ? strtotime($valid_to . ' 23:59:59') : 0,
			'is_active' => $this->input->post('is_active') ? 1 : 0,
		);
	}
}
