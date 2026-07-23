<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_movements extends MY_Admin_Controller {

	protected $currentUser;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('stock_movement_model');
		$this->currentUser = $this->session->userdata('login');

		if (!admin_can('stock.view', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có quyền xem lịch sử biến động.');
			redirect(admin_url('home'));
		}
	}

	public function index()
	{
		$filter_sku = trim((string) $this->input->get('sku'));
		$filter_type = trim((string) $this->input->get('type'));

		$this->db->from('stock_movements');
		$this->db->join('product_variants', 'product_variants.id = stock_movements.variant_id', 'left');
		$this->db->join('product', 'product.id = stock_movements.product_id', 'left');
		if ($filter_sku !== '') {
			$this->db->like('product_variants.sku', $filter_sku);
		}
		if ($filter_type !== '' && in_array($filter_type, array('in', 'out', 'adjust'), true)) {
			$this->db->where('stock_movements.movement_type', $filter_type);
		}
		$total = $this->db->count_all_results();

		$this->load->library('pagination');
		$config = pagination(admin_url('stock-movements/index'), $total, 25, 4);
		$config['reuse_query_string'] = true;
		$this->pagination->initialize($config);

		$segment = isset($this->uri->segments['4']) ? (int) $this->uri->segments['4'] : 0;

		$this->db->select('stock_movements.*, product_variants.sku, product.name AS product_name');
		$this->db->from('stock_movements');
		$this->db->join('product_variants', 'product_variants.id = stock_movements.variant_id', 'left');
		$this->db->join('product', 'product.id = stock_movements.product_id', 'left');
		if ($filter_sku !== '') {
			$this->db->like('product_variants.sku', $filter_sku);
		}
		if ($filter_type !== '' && in_array($filter_type, array('in', 'out', 'adjust'), true)) {
			$this->db->where('stock_movements.movement_type', $filter_type);
		}
		$this->db->order_by('stock_movements.id', 'DESC');
		$this->db->limit($config['per_page'], $segment);
		$list = $this->db->get()->result();

		$this->data['list'] = $list;
		$this->data['filter_sku'] = $filter_sku;
		$this->data['filter_type'] = $filter_type;

		$this->render_admin('admin/stock_movements/index');
	}
}
