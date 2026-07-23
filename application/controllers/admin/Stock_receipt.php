<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_receipt extends MY_Admin_Controller {

	protected $currentUser;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('stock_receipt_model');
		$this->load->model('stock_receipt_item_model');
		$this->load->model('supplier_model');
		$this->load->model('product_model');
		$this->load->model('inventory_model');
		$this->load->library('stock_service');
		$this->currentUser = $this->session->userdata('login');

		if (!admin_can('stock.view', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có quyền xem phiếu nhập kho.');
			redirect(admin_url('home'));
		}
	}

	public function index()
	{
		$status = trim((string) $this->input->get('status'));
		$code = trim((string) $this->input->get('code'));

		$this->db->select('stock_receipts.*, suppliers.name AS supplier_name', false);
		$this->db->select(
			'(SELECT COALESCE(SUM(IF(sri.subtotal > 0, sri.subtotal, sri.qty * sri.unit_cost)), 0)
			FROM stock_receipt_items sri WHERE sri.receipt_id = stock_receipts.id) AS lines_total',
			false
		);
		$this->db->from('stock_receipts');
		$this->db->join('suppliers', 'suppliers.id = stock_receipts.supplier_id', 'left');
		if ($status !== '' && in_array($status, array('draft', 'confirmed', 'cancelled'), true)) {
			$this->db->where('stock_receipts.status', $status);
		}
		if ($code !== '') {
			$this->db->like('stock_receipts.receipt_code', $code);
		}
		$this->db->order_by('stock_receipts.id', 'DESC');

		$list = $this->db->get()->result();

		$this->data['list'] = $list;
		$this->data['filter_status'] = $status;
		$this->data['filter_code'] = $code;
		$this->data['can_manage'] = admin_can('stock.manage', $this->currentUser);
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['message_fail'] = $this->session->flashdata('message_fail');

		$this->render_admin('admin/stock_receipt/index');
	}

	public function add()
	{
		if (!admin_can('stock.manage', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có quyền tạo phiếu nhập.');
			redirect(admin_url('stock-receipts'));
		}

		if ($this->input->post('submit')) {
			$result = $this->stock_service->create_draft_receipt(
				(int) $this->input->post('supplier_id'),
				$this->input->post('note'),
				$this->_lines_from_post(),
				(int) $this->currentUser->id
			);
			if ($result['ok']) {
				$this->session->set_flashdata('message', 'Đã tạo phiếu nháp ' . $result['receipt_code'] . '.');
				redirect(admin_url('stock-receipts/view/' . $result['receipt_id']));
			}
			$this->session->set_flashdata('message_fail', $result['message']);
			redirect(admin_url('stock-receipts/add'));
		}

		$this->_form_data(null, array());
		$this->render_admin('admin/stock_receipt/form');
	}

	public function edit()
	{
		if (!admin_can('stock.manage', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có quyền sửa phiếu nhập.');
			redirect(admin_url('stock-receipts'));
		}

		$id = (int) $this->uri->rsegment(3);
		$receipt = $this->stock_receipt_model->get_detail($id);
		if (!$receipt) {
			$this->session->set_flashdata('message_fail', 'Phiếu nhập không tồn tại.');
			redirect(admin_url('stock-receipts'));
		}
		if ($receipt->status !== 'draft') {
			$this->session->set_flashdata('message_fail', 'Phiếu đã khóa, không thể sửa.');
			redirect(admin_url('stock-receipts/view/' . $id));
		}

		if ($this->input->post('submit')) {
			$result = $this->stock_service->update_draft_receipt(
				$id,
				(int) $this->input->post('supplier_id'),
				$this->input->post('note'),
				$this->_lines_from_post()
			);
			if ($result['ok']) {
				$this->session->set_flashdata('message', 'Đã cập nhật phiếu nháp.');
				redirect(admin_url('stock-receipts/view/' . $id));
			}
			$this->session->set_flashdata('message_fail', $result['message']);
			redirect(admin_url('stock-receipts/edit/' . $id));
		}

		$items = $this->stock_receipt_item_model->get_by_receipt($id);
		$this->_form_data($receipt, $items);
		$this->render_admin('admin/stock_receipt/form');
	}

	public function view()
	{
		$id = (int) $this->uri->rsegment(3);
		$receipt = $this->stock_receipt_model->get_detail($id);
		if (!$receipt) {
			$this->session->set_flashdata('message_fail', 'Phiếu nhập không tồn tại.');
			redirect(admin_url('stock-receipts'));
		}

		$this->data['receipt'] = $receipt;
		$this->data['items'] = $this->stock_receipt_item_model->get_by_receipt($id);
		$this->data['can_manage'] = admin_can('stock.manage', $this->currentUser);
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['message_fail'] = $this->session->flashdata('message_fail');

		$this->render_admin('admin/stock_receipt/view');
	}

	public function confirm()
	{
		if (!$this->input->post()) {
			redirect(admin_url('stock-receipts'));
		}
		if (!admin_can('stock.manage', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có quyền xác nhận phiếu.');
			redirect(admin_url('stock-receipts'));
		}

		$id = (int) $this->input->post('receipt_id');
		$result = $this->stock_service->confirm_receipt($id, (int) $this->currentUser->id);
		if ($result['ok']) {
			$this->session->set_flashdata('message', $result['message']);
		} else {
			$this->session->set_flashdata('message_fail', $result['message']);
		}
		redirect(admin_url('stock-receipts/view/' . $id));
	}

	public function cancel()
	{
		if (!$this->input->post()) {
			redirect(admin_url('stock-receipts'));
		}
		if (!admin_can('stock.manage', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có quyền hủy phiếu.');
			redirect(admin_url('stock-receipts'));
		}

		$id = (int) $this->input->post('receipt_id');
		$result = $this->stock_service->cancel_receipt($id, (int) $this->currentUser->id);
		if ($result['ok']) {
			$this->session->set_flashdata('message', $result['message']);
		} else {
			$this->session->set_flashdata('message_fail', $result['message']);
		}
		redirect(admin_url('stock-receipts/view/' . $id));
	}

	public function search_variants()
	{
		if (!$this->_receipt_picker_allowed()) {
			return $this->_json_response(array('ok' => false, 'message' => 'Forbidden'), 403);
		}

		$filters = $this->_receipt_picker_filters_from_request();
		$page = max(1, (int) $this->input->get('page'));
		$per_page = max(10, min(50, (int) $this->input->get('per_page') ?: 25));
		$offset = ($page - 1) * $per_page;
		$total = $this->inventory_model->count_receipt_variants($filters);
		$rows = $this->inventory_model->search_receipt_variants($filters, $per_page, $offset);

		$this->_json_response(array(
			'ok' => true,
			'items' => $this->_format_receipt_variant_rows($rows),
			'total' => $total,
			'page' => $page,
			'per_page' => $per_page,
			'pages' => $total > 0 ? (int) ceil($total / $per_page) : 1,
		));
	}

	public function filter_products()
	{
		if (!$this->_receipt_picker_allowed()) {
			return $this->_json_response(array('ok' => false, 'message' => 'Forbidden'), 403);
		}

		$q = trim((string) $this->input->get('q'));
		$catalog_id = (int) $this->input->get('catalog_id');
		$page = max(1, (int) $this->input->get('page'));
		$per_page = 20;
		$offset = ($page - 1) * $per_page;

		$this->db->select('product.id, product.name, product.code');
		$this->db->from('product');
		$this->db->where('product.status', 1);
		if ($catalog_id > 0) {
			$this->db->where('product.catalog_id', $catalog_id);
		}
		if ($q !== '') {
			$this->db->group_start();
			$this->db->like('product.name', $q);
			$this->db->or_like('product.code', $q);
			$this->db->group_end();
		}
		$this->db->order_by('product.name', 'ASC');
		$this->db->limit($per_page + 1, $offset);
		$rows = $this->db->get()->result();

		$has_more = count($rows) > $per_page;
		if ($has_more) {
			array_pop($rows);
		}

		$results = array();
		foreach ($rows as $row) {
			$label = $row->name;
			if (!empty($row->code)) {
				$label = $row->code . ' — ' . $label;
			}
			$results[] = array(
				'id' => (int) $row->id,
				'text' => $label,
			);
		}

		$this->_json_response(array(
			'ok' => true,
			'results' => $results,
			'pagination' => array('more' => $has_more),
		));
	}

	public function filter_options()
	{
		if (!$this->_receipt_picker_allowed()) {
			return $this->_json_response(array('ok' => false, 'message' => 'Forbidden'), 403);
		}

		$scope = array(
			'catalog_id' => (int) $this->input->get('catalog_id'),
			'product_id' => (int) $this->input->get('product_id'),
		);
		if ($scope['catalog_id'] <= 0) {
			unset($scope['catalog_id']);
		}
		if ($scope['product_id'] <= 0) {
			unset($scope['product_id']);
		}

		$this->_json_response(array(
			'ok' => true,
			'colors' => $this->inventory_model->get_receipt_filter_colors($scope),
			'sizes' => $this->inventory_model->get_receipt_filter_sizes($scope),
		));
	}

	public function recent_variants()
	{
		if (!$this->_receipt_picker_allowed()) {
			return $this->_json_response(array('ok' => false, 'message' => 'Forbidden'), 403);
		}

		$rows = $this->inventory_model->get_recent_receipt_variants((int) $this->currentUser->id, 10);
		$this->_json_response(array(
			'ok' => true,
			'items' => $this->_format_receipt_variant_rows($rows),
		));
	}

	private function _receipt_picker_allowed()
	{
		return admin_can('stock.manage', $this->currentUser) || admin_can('stock.view', $this->currentUser);
	}

	private function _receipt_picker_filters_from_request()
	{
		$filters = array(
			'q' => trim((string) $this->input->get('q')),
		);
		$catalog_id = (int) $this->input->get('catalog_id');
		$product_id = (int) $this->input->get('product_id');
		$color = trim((string) $this->input->get('color'));
		$size = trim((string) $this->input->get('size'));

		if ($catalog_id > 0) {
			$filters['catalog_id'] = $catalog_id;
		}
		if ($product_id > 0) {
			$filters['product_id'] = $product_id;
		}
		if ($color !== '') {
			$filters['color'] = $color;
		}
		if ($size !== '') {
			$filters['size'] = $size;
		}

		return $filters;
	}

	private function _format_receipt_variant_rows($rows)
	{
		$items = array();
		foreach ($rows as $row) {
			$stock = isset($row->stock) ? (int) $row->stock : 0;
			$min_stock = isset($row->min_stock) ? (int) $row->min_stock : 0;
			$status = $this->inventory_model->stock_label($stock, $min_stock);
			$items[] = array(
				'id' => (int) $row->id,
				'product_id' => (int) $row->product_id,
				'sku' => (string) $row->sku,
				'product_name' => (string) $row->product_name,
				'color' => (string) $row->color,
				'size' => (string) $row->size,
				'stock' => $stock,
				'min_stock' => $min_stock,
				'cost_price' => isset($row->cost_price) ? (float) $row->cost_price : 0,
				'stock_status' => $status['key'],
				'stock_label' => $status['label'],
				'stock_class' => $status['class'],
			);
		}
		return $items;
	}

	private function _json_response($payload, $status = 200)
	{
		$this->output
			->set_status_header((int) $status)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($payload, JSON_UNESCAPED_UNICODE));
	}

	private function _form_data($receipt, $items)
	{
		$this->data['receipt'] = $receipt;
		$this->data['items'] = $items;
		$this->data['suppliers'] = $this->supplier_model->get_list(array(
			'where' => array('status' => 1),
			'order' => array('name', 'ASC'),
		));

		$this->load->model('catalog_model');
		$this->data['catalogs'] = $this->catalog_model->get_list(array(
			'order' => array('name', 'ASC'),
		));

		$variant_ids = array();
		foreach ((array) $items as $item) {
			if (!empty($item->variant_id)) {
				$variant_ids[] = (int) $item->variant_id;
			}
		}
		$this->data['line_variants'] = $this->inventory_model->get_variants_by_ids($variant_ids);

		$this->data['receipt_form_urls'] = array(
			'search_variants' => admin_url('receipts/search_variants'),
			'filter_products' => admin_url('receipts/filter_products'),
			'filter_options' => admin_url('receipts/filter_options'),
			'recent_variants' => admin_url('receipts/recent_variants'),
		);
	}

	private function _lines_from_post()
	{
		$variant_ids = $this->input->post('variant_id');
		$product_ids = $this->input->post('product_id');
		$sizes = $this->input->post('size');
		$colors = $this->input->post('color');
		$qtys = $this->input->post('qty');
		$costs = $this->input->post('unit_cost');

		$lines = array();
		$rows = is_array($variant_ids) ? $variant_ids : (is_array($product_ids) ? $product_ids : array());

		foreach ($rows as $i => $_) {
			$line = array(
				'qty' => is_array($qtys) && isset($qtys[$i]) ? $qtys[$i] : 0,
				'unit_cost' => is_array($costs) && isset($costs[$i]) ? $costs[$i] : 0,
			);
			if (is_array($variant_ids) && !empty($variant_ids[$i])) {
				$line['variant_id'] = (int) $variant_ids[$i];
			} else {
				$line['product_id'] = is_array($product_ids) && isset($product_ids[$i]) ? (int) $product_ids[$i] : 0;
				$line['size'] = is_array($sizes) && isset($sizes[$i]) ? $sizes[$i] : '';
				$line['color'] = is_array($colors) && isset($colors[$i]) ? $colors[$i] : '';
			}
			if (!empty($line['variant_id']) || !empty($line['product_id'])) {
				$lines[] = $line;
			}
		}

		return $lines;
	}
}
