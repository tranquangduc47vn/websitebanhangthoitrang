<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Suppliers extends MY_Admin_Controller {

	protected $currentUser;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('supplier_model');
		$this->currentUser = $this->session->userdata('login');

		if (!admin_can('stock.view', $this->currentUser)) {
			redirect(admin_url('home'));
		}
	}

	public function index()
	{
		$list = $this->supplier_model->get_list(array('order' => array('name', 'ASC')));
		$this->data['list'] = $list;
		$this->data['can_manage'] = admin_can('stock.manage', $this->currentUser);
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['message_fail'] = $this->session->flashdata('message_fail');
		$this->render_admin('admin/suppliers/index');
	}

	public function add()
	{
		if (!admin_can('stock.manage', $this->currentUser)) {
			redirect(admin_url('suppliers'));
		}
		if ($this->input->post('submit')) {
			$now = time();
			$this->supplier_model->create(array(
				'code' => trim($this->input->post('code')),
				'name' => trim($this->input->post('name')),
				'contact_name' => trim($this->input->post('contact_name')),
				'phone' => trim($this->input->post('phone')),
				'email' => trim($this->input->post('email')),
				'address' => trim($this->input->post('address')),
				'note' => trim($this->input->post('note')),
				'status' => 1,
				'created' => $now,
				'updated' => $now,
			));
			$this->session->set_flashdata('message', 'Đã thêm nhà cung cấp.');
			redirect(admin_url('suppliers'));
		}
		$this->render_admin('admin/suppliers/form');
	}
}
