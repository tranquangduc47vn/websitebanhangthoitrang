<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_inventory extends MY_Admin_Controller {

	protected $currentUser;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('product_model');
		$this->load->model('catalog_model');
		$this->load->model('stock_movement_model');
		$this->load->library('stock_service');
		$this->currentUser = $this->session->userdata('login');

		if (!admin_can('stock.view', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có quyền xem tồn kho biến thể.');
			redirect(admin_url('home'));
		}
	}

	public function index()
	{
		$search_name = trim((string) $this->input->get('name'));
		$search_catalog = (int) $this->input->get('catalog_id');
		$stock_filter = $this->input->get('stock');

		$this->db->from('product_inventory');
		$this->db->join('product', 'product.id = product_inventory.product_id', 'inner');
		if ($search_name !== '') {
			$this->db->like('product.name', $search_name);
		}
		if ($search_catalog > 0) {
			$this->db->where('product.catalog_id', $search_catalog);
		}
		if ($stock_filter === 'out') {
			$this->db->where('product_inventory.quantity <=', 0);
		} elseif ($stock_filter === 'low') {
			$this->db->where('product_inventory.quantity >', 0);
			$this->db->where('product_inventory.quantity <=', 10);
		} elseif ($stock_filter === 'ok') {
			$this->db->where('product_inventory.quantity >', 10);
		}
		$total = $this->db->count_all_results();

		$this->load->library('pagination');
		$config = pagination(admin_url('stock-inventory/index'), $total, 20, 4);
		$config['reuse_query_string'] = true;
		$this->pagination->initialize($config);
		$segment = isset($this->uri->segments['4']) ? (int) $this->uri->segments['4'] : 0;

		$this->db->select('product_inventory.*, product.name AS product_name, product.image_link, catalog.name AS catalog_name');
		$this->db->from('product_inventory');
		$this->db->join('product', 'product.id = product_inventory.product_id', 'inner');
		$this->db->join('catalog', 'catalog.id = product.catalog_id', 'left');
		if ($search_name !== '') {
			$this->db->like('product.name', $search_name);
		}
		if ($search_catalog > 0) {
			$this->db->where('product.catalog_id', $search_catalog);
		}
		if ($stock_filter === 'out') {
			$this->db->where('product_inventory.quantity <=', 0);
		} elseif ($stock_filter === 'low') {
			$this->db->where('product_inventory.quantity >', 0);
			$this->db->where('product_inventory.quantity <=', 10);
		} elseif ($stock_filter === 'ok') {
			$this->db->where('product_inventory.quantity >', 10);
		}
		$this->db->order_by('product_inventory.quantity', 'ASC');
		$this->db->order_by('product_inventory.id', 'DESC');
		$this->db->limit($config['per_page'], $segment);
		$rows = $this->db->get()->result();

		$this->data['rows'] = $rows;
		$this->data['total'] = $total;
		$this->data['catalog'] = $this->_list_catalog();
		$this->data['search_name'] = $search_name;
		$this->data['search_catalog'] = $search_catalog;
		$this->data['stock_filter'] = $stock_filter;

		$this->render_admin('admin/stock_inventory/index');
	}

	public function movements()
	{
		$product_id = (int) $this->input->get('product_id');
		$type = trim((string) $this->input->get('type'));

		$this->db->from('stock_movements');
		if ($product_id > 0) {
			$this->db->where('product_id', $product_id);
		}
		if ($type !== '' && in_array($type, array('in', 'out', 'adjust'), true)) {
			$this->db->where('movement_type', $type);
		}
		$total = $this->db->count_all_results();

		$this->load->library('pagination');
		$config = pagination(admin_url('stock-inventory/movements'), $total, 25, 4);
		$config['reuse_query_string'] = true;
		$this->pagination->initialize($config);
		$segment = isset($this->uri->segments['4']) ? (int) $this->uri->segments['4'] : 0;

		$this->db->select('stock_movements.*, product.name AS product_name');
		$this->db->from('stock_movements');
		$this->db->join('product', 'product.id = stock_movements.product_id', 'left');
		if ($product_id > 0) {
			$this->db->where('stock_movements.product_id', $product_id);
		}
		if ($type !== '' && in_array($type, array('in', 'out', 'adjust'), true)) {
			$this->db->where('stock_movements.movement_type', $type);
		}
		$this->db->order_by('stock_movements.id', 'DESC');
		$this->db->limit($config['per_page'], $segment);
		$list = $this->db->get()->result();

		$this->data['list'] = $list;
		$this->data['total'] = $total;
		$this->data['filter_product_id'] = $product_id;
		$this->data['filter_type'] = $type;

		$this->render_admin('admin/stock_inventory/movements');
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
