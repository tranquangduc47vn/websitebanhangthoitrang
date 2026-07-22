<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Admin_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
		$this->load->model('order_model');
		$this->load->model('product_model');
		$this->load->model('transaction_model');
		$this->load->library('form_validation');
		$this->load->helper('form');
	}
	public function index()
	{
		$message_success = $this->session->flashdata('message_success');
		$this->data['message_success'] = $message_success;

		$message_fail = $this->session->flashdata('message_fail');
		$this->data['message_fail'] = $message_fail;

		$user = $this->user_model->get_list();
		$this->data['user']= $user;

		$this->render_admin('admin/users/index');
	}
	public function order()
	{
		$message_success = $this->session->flashdata('message_success');
		$this->data['message_success'] = $message_success;

		$message_fail = $this->session->flashdata('message_fail');
		$this->data['message_fail'] = $message_fail;

		

		$id = $this->uri->segment(4);
		$input['where'] = array('user_id' => $id);
		$order = $this->transaction_model->get_list($input);
		$this->data['order']= $order;

		$user = $this->user_model->get_info($id);
		$this->data['user']= $user;
		$this->render_admin('admin/users/order');
	}
	public function detail()
	{
		$id = $this->uri->segment(4);
		$transaction = $this->transaction_model->get_info($id);
		if (empty($transaction)) {
			$this->session->set_flashdata('message_fail', 'Đơn đặt hàng không tồn tại!');
			redirect(admin_url('user'));
		}
		$this->data['transaction'] = $transaction;
		
		$input =array();
		$input['where'] = array('transaction_id' => $transaction->id);
		$info = $this->order_model->get_list($input);
		
		$list_product = array();
		foreach ($info as $key => $value) {
			$this->db->select('product.id as id,product.name as name,image_link, order.qty as qty, order.amount as price, order.id as order_id');
			$this->db->join('order','order.product_id = product.id');
			$where = array();
			$where = array('product.id' => $value->product_id, 'order.id' => $value->id);
			$list_product[] = $this->product_model->get_info_rule($where);
		}
		$this->data['list_product'] = $list_product;
		$this->render_admin('admin/users/detail');
	}
	public function accept()
	{
		$id = $this->uri->segment(4);
		$transaction = $this->transaction_model->get_info($id);
		if (empty($transaction)) {
			$this->session->set_flashdata('message_fail', 'Đơn đặt hàng không tồn tại!');
			redirect(admin_url('user'));
		}
		if ((string) $transaction->status !== '0') {
			$this->session->set_flashdata('message_fail', 'Chỉ có thể xác nhận đơn đang ở trạng thái chờ xử lý.');
			redirect(admin_url('user/detail/' . $id));
		}
		$this->transaction_model->update($id, array('status' => '1'));
		$this->session->set_flashdata('message_success', 'Xác nhận đơn đặt hàng thành công');
		redirect(admin_url('user/detail/' . $id));
	}
	public function del()
	{
		$id = $this->uri->segment(4);
		$where = array();
		$where = array('id' => $id);
		if (!$this->user_model->check_exists($where)) {
			$this->session->set_flashdata('message_fail', 'user không tồn tại');
			redirect(admin_url('user'));
		}
		if ($this->user_model->delete($id)) {
			$this->session->set_flashdata('message_success', 'Xóa user thành công');
		}else{
			$this->session->set_flashdata('message_fail', 'Xóa user thất bại');
		}
		redirect(admin_url('user'));
	}
	public function deldetail()
	{
		$id = $this->uri->segment(4);
		$where = array();
		$where = array('id' => $id);
		if (!$this->order_model->check_exists($where)) {
			$this->session->set_flashdata('message_fail', 'Danh mục không tồn tại');
			redirect(admin_url('transaction'));
		}
		if ($this->order_model->delete($id)) {
			$this->session->set_flashdata('message_success', 'Xóa thành công');
		}else{
			$this->session->set_flashdata('message_fail', 'Xóa thất bại');
		}
		redirect(admin_url('user'));
		
	}
}
