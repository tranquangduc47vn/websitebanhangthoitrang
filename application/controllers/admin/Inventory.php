<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory extends MY_Admin_Controller {

	protected $currentUser;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('catalog_model');
		$this->load->model('product_variant_model');
		$this->load->model('inventory_model');
		$this->load->library('inventory_service');
		$this->currentUser = $this->session->userdata('login');

		if (!admin_can('inventory.view', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có quyền xem tồn kho.');
			redirect(admin_url('home'));
		}
	}

	public function index()
	{
		$filters = array(
			'name' => trim((string) $this->input->get('name')),
			'catalog_id' => (int) $this->input->get('catalog_id'),
			'sku' => trim((string) $this->input->get('sku')),
			'stock' => $this->input->get('stock'),
		);

		$total = $this->inventory_model->count_list($filters);
		$this->load->library('pagination');
		$config = pagination(admin_url('inventory/index'), $total, 20, 4);
		$config['reuse_query_string'] = true;
		$this->pagination->initialize($config);

		$segment = isset($this->uri->segments['4']) ? (int) $this->uri->segments['4'] : 0;
		$list = $this->inventory_model->get_list($filters, $config['per_page'], $segment);

		$this->data['list'] = $list;
		$this->data['filters'] = $filters;
		$this->data['stats'] = $this->inventory_model->get_stats();
		$this->data['catalog'] = $this->_list_catalog();
		$this->data['can_adjust'] = admin_can('inventory.adjust', $this->currentUser);
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['message_fail'] = $this->session->flashdata('message_fail');

		$this->render_admin('admin/inventory/index');
	}

	public function low_stock()
	{
		$_GET['stock'] = 'low';
		$_REQUEST['stock'] = 'low';
		$this->index();
	}

	public function adjust($variant_id = 0)
	{
		$variant_id = (int) $variant_id;
		if (!$this->input->post()) {
			if ($variant_id <= 0) {
				redirect(admin_url('inventory'));
			}
			$variant = $this->product_variant_model->get_info($variant_id);
			if (!$variant) {
				$this->session->set_flashdata('message_fail', 'Biến thể không tồn tại.');
				redirect(admin_url('inventory'));
			}
			$this->db->select('product.name AS product_name');
			$this->db->join('product', 'product.id = product_variants.product_id', 'inner');
			$this->db->where('product_variants.id', $variant_id);
			$row = $this->db->get('product_variants')->row();

			$this->data['variant'] = $variant;
			$this->data['product_name'] = $row ? $row->product_name : '';
			$this->render_admin('admin/inventory/adjust');
			return;
		}

		if (!admin_can('inventory.adjust', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có quyền kiểm kê tồn kho.');
			redirect(admin_url('inventory'));
		}

		$variant_id = (int) $this->input->post('variant_id');
		$new_qty = (int) $this->input->post('new_qty');
		$note = trim((string) $this->input->post('note'));

		$result = $this->inventory_service->adjust_stock(
			$variant_id,
			$new_qty,
			$note,
			(int) $this->currentUser->id
		);

		if ($result['ok']) {
			$this->session->set_flashdata('message', $result['message']);
		} else {
			$this->session->set_flashdata('message_fail', $result['message']);
		}
		redirect(admin_url('inventory'));
	}

	private function _list_catalog()
	{
		$input = array('where' => array('parent_id' => '1'), 'order' => array('sort_order', 'asc'));
		$catalog = $this->catalog_model->get_list($input);
		foreach ($catalog as $value) {
			$input['where'] = array('parent_id' => $value->id);
			$value->sub = $this->catalog_model->get_list($input);
		}
		return $catalog;
	}
}
