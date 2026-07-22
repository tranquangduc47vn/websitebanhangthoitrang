<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory extends MY_Admin_Controller {

	protected $currentUser;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('product_model');
		$this->load->model('catalog_model');
		$this->currentUser = $this->session->userdata('login');

		if (!admin_can('inventory.view', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có quyền xem tồn kho.');
			redirect(admin_url('home'));
		}
	}

	public function index()
	{
		$this->data['message_success'] = $this->session->flashdata('message_success');
		$this->data['message_fail'] = $this->session->flashdata('message_fail');

		$search_name = trim((string) $this->input->get('name'));
		$search_catalog = $this->input->get('catalog_id');
		$stock_filter = $this->input->get('stock');

		$input_total = array();
		if ($search_name !== '') {
			$input_total['like'] = array('product.name', $search_name);
		}
		if (!empty($search_catalog)) {
			$input_total['where'] = array('product.catalog_id' => (int) $search_catalog);
		}

		$this->db->reset_query();
		$this->_apply_stock_filter($stock_filter);
		$total = $this->product_model->get_total($input_total);
		$this->data['total'] = $total;

		$this->load->library('pagination');
		$config = pagination(admin_url('inventory/index'), $total, 15, 4);
		$config['reuse_query_string'] = true;
		$this->pagination->initialize($config);

		$segment = isset($this->uri->segments['4']) ? (int) $this->uri->segments['4'] : 0;

		$input = array();
		$input['limit'] = array($config['per_page'], $segment);
		if ($search_name !== '') {
			$input['like'] = array('product.name', $search_name);
		}
		if (!empty($search_catalog)) {
			$input['where'] = array('product.catalog_id' => (int) $search_catalog);
		}

		$this->db->reset_query();
		$this->_apply_stock_filter($stock_filter);

		$this->db->select('
			product.id,
			product.name,
			product.image_link,
			product.quantity,
			product.buyed,
			catalog.name as namecatalog
		');
		$this->db->join('catalog', 'catalog.id = product.catalog_id', 'left');
		$this->db->order_by('product.quantity', 'ASC');
		$this->db->order_by('product.id', 'DESC');

		$products = $this->product_model->get_list($input);
		$this->data['products'] = $products;
		$this->data['catalog'] = $this->_list_catalog();
		$this->data['search_name'] = $search_name;
		$this->data['search_catalog'] = $search_catalog;
		$this->data['stock_filter'] = $stock_filter;
		$this->data['stats'] = $this->_stock_stats();
		$this->data['can_adjust_inventory'] = admin_can('inventory.adjust', $this->currentUser);

		$this->render_admin('admin/inventory/index');
	}

	// Nhập thêm tồn kho (cộng dồn)
	public function adjust()
	{
		if (!$this->input->post()) {
			redirect(admin_url('inventory'));
		}

		if (!admin_can('inventory.adjust', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Role User chỉ được xem tồn kho, không nhập kho.');
			redirect(admin_url('inventory'));
		}

		$product_id = (int) $this->input->post('product_id');
		$add_qty = (int) $this->input->post('add_qty');

		if ($product_id <= 0) {
			$this->session->set_flashdata('message_fail', 'Sản phẩm không hợp lệ.');
			redirect(admin_url('inventory'));
		}
		if ($add_qty <= 0 || $add_qty > 999999) {
			$this->session->set_flashdata('message_fail', 'Số lượng nhập phải từ 1 đến 999.999.');
			redirect(admin_url('inventory'));
		}

		$product = $this->product_model->get_info($product_id);
		if (empty($product)) {
			$this->session->set_flashdata('message_fail', 'Không tìm thấy sản phẩm.');
			redirect(admin_url('inventory'));
		}

		$this->db->set('quantity', 'quantity + ' . (int) $add_qty, false);
		$this->db->where('id', $product_id);
		$this->db->update('product');

		if ($this->db->affected_rows() >= 0) {
			$updated = $this->product_model->get_info($product_id);
			$new_qty = isset($updated->quantity) ? (int) $updated->quantity : 0;
			$this->session->set_flashdata(
				'message_success',
				'Đã nhập +' . number_format($add_qty) . ' cho "' . $product->name . '". Tồn hiện tại: ' . number_format($new_qty) . '.'
			);
		} else {
			$this->session->set_flashdata('message_fail', 'Cập nhật tồn kho thất bại.');
		}

		$back = $this->input->post('redirect_query');
		if (!empty($back)) {
			redirect(admin_url('inventory/index?' . ltrim($back, '?')));
		}
		redirect(admin_url('inventory'));
	}

	private function _apply_stock_filter($stock_filter)
	{
		if ($stock_filter === 'out') {
			$this->db->where('product.quantity <=', 0);
		} elseif ($stock_filter === 'low') {
			$this->db->where('product.quantity >', 0);
			$this->db->where('product.quantity <=', 10);
		} elseif ($stock_filter === 'ok') {
			$this->db->where('product.quantity >', 10);
		}
	}

	private function _stock_stats()
	{
		$row = $this->db->query('
			SELECT
				COUNT(*) AS total,
				SUM(CASE WHEN quantity <= 0 THEN 1 ELSE 0 END) AS out_of_stock,
				SUM(CASE WHEN quantity > 0 AND quantity <= 10 THEN 1 ELSE 0 END) AS low_stock
			FROM product
		')->row();

		return array(
			'total' => $row ? (int) $row->total : 0,
			'out_of_stock' => $row ? (int) $row->out_of_stock : 0,
			'low_stock' => $row ? (int) $row->low_stock : 0,
		);
	}

	private function _list_catalog()
	{
		$input = array();
		$input['where'] = array('parent_id' => '1');
		$input['order'] = array('sort_order', 'asc');
		$catalog = $this->catalog_model->get_list($input);
		foreach ($catalog as $value) {
			$input['where'] = array('parent_id' => $value->id);
			$value->sub = $this->catalog_model->get_list($input);
		}
		return $catalog;
	}
}
