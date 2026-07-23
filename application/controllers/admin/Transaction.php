<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction extends MY_Admin_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('transaction_model');
		$this->load->model('order_model');
		$this->load->model('product_model');
		$this->load->helper('permission');

		$login = $this->session->userdata('login');
		$method = $this->router->fetch_method();
		if ($method === 'chat') {
			if (!admin_can('order.chat', $login)) {
				admin_flash_fail('Bạn không có quyền truy cập chat khách hàng.');
				redirect(admin_url('home'));
			}
		} elseif (!admin_can('order.manage', $login)) {
			admin_flash_fail('Bạn không có quyền quản lý đơn hàng.');
			redirect(admin_url('home'));
		}
	}

	public function index()
	{
		$filter_q = trim((string) $this->input->get('q'));
		$filter_status = $this->input->get('status');
		$filter_payment = $this->input->get('payment');
		$filter_date_from = trim((string) $this->input->get('date_from'));
		$filter_date_to = trim((string) $this->input->get('date_to'));

		$this->db->reset_query();
		$this->_apply_order_filters($filter_q, $filter_status, $filter_payment, $filter_date_from, $filter_date_to);
		$total = $this->transaction_model->get_total();
		$this->data['total'] = $total;

		$this->load->library('pagination');
		$per = 10;
		$config = pagination(admin_url('orders'), $total, $per, 3);
		$config['reuse_query_string'] = true;
		$this->pagination->initialize($config);

		$segment = 0;
		if ($this->uri->segment(2) === 'orders' && is_numeric($this->uri->segment(3))) {
			$segment = (int) $this->uri->segment(3);
		} elseif (is_numeric($this->uri->segment(4))) {
			$segment = (int) $this->uri->segment(4);
		}
		$this->data['pagination_offset'] = $segment;

		$this->db->reset_query();
		$this->_apply_order_filters($filter_q, $filter_status, $filter_payment, $filter_date_from, $filter_date_to);

		$input = array();
		$input['limit'] = array($config['per_page'], $segment);
		$input['order'] = array('id', 'DESC');
		$transaction = $this->transaction_model->get_list($input);
		$this->data['transaction'] = $transaction;

		$this->data['filter_q'] = $filter_q;
		$this->data['filter_status'] = $filter_status;
		$this->data['filter_payment'] = $filter_payment;
		$this->data['filter_date_from'] = $filter_date_from;
		$this->data['filter_date_to'] = $filter_date_to;

		$this->render_admin('admin/orders/index');
	}

	private function _apply_order_filters($keyword, $status, $payment, $date_from, $date_to)
	{
		if ($keyword !== '') {
			$this->db->group_start();
			$this->db->like('user_name', $keyword);
			$this->db->or_like('user_phone', $keyword);
			$this->db->group_end();
		}

		if ($status !== null && $status !== '' && in_array((string) $status, array('0', '1', '2', '3', '4'), true)) {
			$this->db->where('status', (string) $status);
		}

		if ($payment === 'transfer') {
			$this->db->where('payment', 'Chuyển khoản');
		} elseif ($payment === 'cod') {
			$this->db->where("(payment IS NULL OR payment != 'Chuyển khoản')", null, false);
		}

		if ($date_from !== '') {
			$ts_from = strtotime($date_from . ' 00:00:00');
			if ($ts_from) {
				$this->db->where('created >=', $ts_from);
			}
		}

		if ($date_to !== '') {
			$ts_to = strtotime($date_to . ' 23:59:59');
			if ($ts_to) {
				$this->db->where('created <=', $ts_to);
			}
		}
	}

	public function del()
	{
		// URI segment 3 hoặc 4 tùy route
		$id = $this->uri->segment(4);
		if (empty($id)) {
			$id = $this->uri->segment(3); 
		}

		$transaction = $this->transaction_model->get_info($id);
		
		if (empty($transaction)) {
			admin_flash_fail('Đơn đặt hàng không tồn tại!');
			redirect(admin_url('transaction'));
		}

		if ((string) $transaction->status !== '4') {
			$reverse_buyed = ((string) $transaction->status === '3');
			$this->transaction_model->release_stock_for_transaction($id, $reverse_buyed);
			$this->load->model('loyalty_model');
			$this->loyalty_model->on_order_cancelled($transaction, $reverse_buyed);
		}

		$this->db->where('transaction_id', $id);
		$this->db->delete('order');

		// Xóa transaction sau khi đã xóa order con
		if ($this->transaction_model->delete($id)) {
			admin_flash_success('Xóa đơn đặt hàng và chi tiết sản phẩm thành công.');
		} else {
			admin_flash_fail('Lỗi hệ thống! Không thể xóa đơn đặt hàng này.');
		}
		
		redirect(admin_url('transaction'));
	}

	public function detail()
	{
		$id = $this->uri->segment(4);
		$transaction = $this->transaction_model->get_info($id);
		if (empty($transaction)) {
			admin_flash_fail('Đơn đặt hàng không tồn tại!');
			redirect(admin_url('transaction'));
		}
		$this->data['transaction'] = $transaction;
		
		$input = array();
		$input['where'] = array('transaction_id' => $transaction->id);
		$info = $this->order_model->get_list($input);
		
		$list_product = array();
		foreach ($info as $key => $value) {
			$this->db->select('product.id as id, product.name as name, image_link, order.qty as qty, order.amount as price, order.id as order_id, order.size as size, order.color as color');
			$this->db->join('order','order.product_id = product.id');
			$where = array();
			$where = array('product.id' => $value->product_id, 'order.id' => $value->id);
			$list_product[] = $this->product_model->get_info_rule($where);
		}
		$this->data['list_product'] = $list_product;
		$this->render_admin('admin/orders/detail');
	}

	public function deldetail()
	{
		$id = $this->uri->segment(4);
		$where = array();
		$where = array('id' => $id);
		if (!$this->order_model->check_exists($where)) {
			admin_flash_fail('Dòng sản phẩm trong đơn không tồn tại');
			redirect(admin_url('transaction'));
		}
		$order = $this->order_model->get_info($id);
		$transaction_id = $order->transaction_id;
		if ($this->order_model->delete($id)) {
			$transaction = $this->transaction_model->get_info($order->transaction_id);
			if (!empty($transaction) && (string) $transaction->status === '0') {
				$product = $this->product_model->get_info($order->product_id);
				if (!empty($product)) {
					$this->product_model->update($product->id, array(
						'quantity' => (int) $product->quantity + (int) $order->qty,
					));
				}
			}
			if (!empty($transaction)) {
				$data = array();
				$data['amount'] = $transaction->amount - $order->amount;
				$this->transaction_model->update($transaction->id, $data);
			}
			admin_flash_success('Xóa thành công');
		} else {
			admin_flash_fail('Xóa thất bại');
		}
		redirect(admin_url('transaction/detail/' . $transaction_id));
	}

	public function accept()
	{
		$id = $this->uri->segment(4);
		$transaction = $this->transaction_model->get_info($id);
		if (empty($transaction)) {
			admin_flash_fail('Đơn đặt hàng không tồn tại!');
			redirect(admin_url('transaction'));
		}
		if ((string) $transaction->status !== '0') {
			admin_flash_fail('Chỉ có thể xác nhận đơn đang ở trạng thái chờ xử lý.');
			redirect(admin_url('transaction/detail/' . $id));
		}
		$this->transaction_model->update($id, array('status' => '1'));
		admin_flash_success('Xác nhận đơn đặt hàng thành công');
		redirect(admin_url('transaction/detail/' . $id));
	}

	public function ship()
	{
		$id = $this->uri->segment(4);
		$transaction = $this->transaction_model->get_info($id);
		if (empty($transaction)) {
			admin_flash_fail('Đơn đặt hàng không tồn tại!');
			redirect(admin_url('transaction'));
		}
		if ((string) $transaction->status !== '1') {
			admin_flash_fail('Chỉ có thể giao hàng khi đơn đã được xác nhận.');
			redirect(admin_url('transaction/detail/' . $id));
		}
		$this->transaction_model->update($id, array('status' => '2'));
		admin_flash_success('Cập nhật trạng thái đơn hàng: Đang giao hàng');
		redirect(admin_url('transaction/detail/' . $id));
	}
	public function complete()
	{
		$id = $this->uri->segment(4);
		$transaction = $this->transaction_model->get_info($id);

		if (empty($transaction)) {
			admin_flash_fail('Đơn đặt hàng không tồn tại!');
			redirect(admin_url('transaction'));
		}

		if ((string) $transaction->status === '3') {
			admin_flash_fail('Đơn hàng đã hoàn thành trước đó.');
			redirect(admin_url('transaction/detail/' . $id));
		}

		if ((string) $transaction->status === '4') {
			admin_flash_fail('Không thể hoàn thành đơn đã hủy.');
			redirect(admin_url('transaction/detail/' . $id));
		}

		if (!in_array((string) $transaction->status, array('1', '2'), true)) {
			admin_flash_fail('Đơn cần được xác nhận và chuyển giao trước khi hoàn thành.');
			redirect(admin_url('transaction/detail/' . $id));
		}

		$this->transaction_model->apply_buyed_for_transaction($id);
		$this->transaction_model->update($id, array('status' => '3'));

		$this->load->model('loyalty_model');
		$transaction->status = '3';
		$this->loyalty_model->credit_order_completed($transaction);

		admin_flash_success('Đơn đặt hàng đã hoàn thành xuất sắc!');
		redirect(admin_url('transaction/detail/' . $id));
	}

	public function cancel()
	{
		$id = $this->uri->segment(4);

		$transaction = $this->transaction_model->get_info($id);
		if (empty($transaction)) {
			admin_flash_fail('Đơn đặt hàng không tồn tại!');
			redirect(admin_url('transaction'));
		}

		if ((string) $transaction->status === '4') {
			admin_flash_fail('Đơn hàng đã được hủy trước đó.');
			redirect(admin_url('transaction/detail/' . $id));
		}

		$reason = $this->input->get('reason');
		if (empty($reason)) {
			$reason = 'Hủy do Admin hoặc lý do khách quan khác.';
		}

		$reverse_buyed = ((string) $transaction->status === '3');
		$this->transaction_model->release_stock_for_transaction($id, $reverse_buyed);

		$this->load->model('loyalty_model');
		$this->loyalty_model->on_order_cancelled($transaction, $reverse_buyed);

		$this->transaction_model->update($id, array(
			'status' => '4',
			'reason' => $reason,
		));

		admin_flash_success('Đã cập nhật trạng thái đơn hàng: Hủy/Hoàn trả.');
		redirect(admin_url('transaction/detail/' . $id));
	}

	public function chat()
	{
		redirect(admin_url('support-chat'));
	}
}