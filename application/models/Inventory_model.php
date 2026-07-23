<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_model extends CI_Model {

	public function stock_label($stock, $min_stock)
	{
		$stock = (int) $stock;
		$min_stock = (int) $min_stock;
		if ($stock <= 0) {
			return array('key' => 'out', 'label' => 'Hết hàng', 'class' => 'danger');
		}
		if ($stock <= $min_stock) {
			return array('key' => 'low', 'label' => 'Sắp hết', 'class' => 'warning');
		}
		return array('key' => 'ok', 'label' => 'Còn hàng', 'class' => 'success');
	}

	public function count_list($filters = array())
	{
		$this->db->reset_query();
		$this->db->select('COUNT(DISTINCT product_variants.id) AS cnt', false);
		$this->db->from('product_variants');
		$this->db->join('product', 'product.id = product_variants.product_id', 'inner');
		$this->db->join('catalog', 'catalog.id = product.catalog_id', 'left');
		$this->_apply_list_filters($filters);
		$row = $this->db->get()->row();
		return $row ? (int) $row->cnt : 0;
	}

	public function get_list($filters = array(), $limit = 20, $offset = 0)
	{
		$this->db->select('
			product_variants.*,
			product.name AS product_name,
			product.image_link AS product_image,
			catalog.name AS catalog_name
		');
		$this->db->from('product_variants');
		$this->db->join('product', 'product.id = product_variants.product_id', 'inner');
		$this->db->join('catalog', 'catalog.id = product.catalog_id', 'left');
		$this->_apply_list_filters($filters);
		$this->db->order_by('product_variants.stock', 'ASC');
		$this->db->order_by('product_variants.id', 'DESC');
		if ($limit > 0) {
			$this->db->limit((int) $limit, (int) $offset);
		}
		return $this->db->get()->result();
	}

	public function get_stats()
	{
		$row = $this->db->query('
			SELECT
				COUNT(*) AS total,
				SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END) AS out_of_stock,
				SUM(CASE WHEN stock > 0 AND stock <= min_stock THEN 1 ELSE 0 END) AS low_stock,
				SUM(stock * cost_price) AS stock_value
			FROM product_variants
			WHERE status = 1
		')->row();

		return array(
			'total' => $row ? (int) $row->total : 0,
			'out_of_stock' => $row ? (int) $row->out_of_stock : 0,
			'low_stock' => $row ? (int) $row->low_stock : 0,
			'stock_value' => $row && $row->stock_value !== null ? (float) $row->stock_value : 0,
		);
	}

	public function search_variants($keyword, $limit = 30)
	{
		return $this->search_receipt_variants(array('q' => $keyword), (int) $limit, 0);
	}

	public function search_receipt_variants($filters = array(), $limit = 25, $offset = 0)
	{
		$this->db->select('
			product_variants.id,
			product_variants.product_id,
			product_variants.sku,
			product_variants.color,
			product_variants.size,
			product_variants.stock,
			product_variants.min_stock,
			product_variants.cost_price,
			product.name AS product_name,
			catalog.name AS catalog_name
		');
		$this->db->from('product_variants');
		$this->db->join('product', 'product.id = product_variants.product_id', 'inner');
		$this->db->join('catalog', 'catalog.id = product.catalog_id', 'left');
		$this->_apply_receipt_picker_filters($filters);
		$this->db->order_by('product.name', 'ASC');
		$this->db->order_by('product_variants.color', 'ASC');
		$this->db->order_by('product_variants.size', 'ASC');
		$this->db->order_by('product_variants.id', 'ASC');
		if ($limit > 0) {
			$this->db->limit((int) $limit, (int) $offset);
		}
		return $this->db->get()->result();
	}

	public function count_receipt_variants($filters = array())
	{
		$this->db->reset_query();
		$this->db->select('COUNT(DISTINCT product_variants.id) AS cnt', false);
		$this->db->from('product_variants');
		$this->db->join('product', 'product.id = product_variants.product_id', 'inner');
		$this->db->join('catalog', 'catalog.id = product.catalog_id', 'left');
		$this->_apply_receipt_picker_filters($filters);
		$row = $this->db->get()->row();
		return $row ? (int) $row->cnt : 0;
	}

	public function get_receipt_filter_colors($filters = array())
	{
		$this->db->select('DISTINCT product_variants.color', false);
		$this->db->from('product_variants');
		$this->db->join('product', 'product.id = product_variants.product_id', 'inner');
		$this->_apply_receipt_picker_scope($filters);
		$this->db->where('product_variants.color !=', '');
		$this->db->order_by('product_variants.color', 'ASC');
		$rows = $this->db->get()->result();
		$out = array();
		foreach ($rows as $row) {
			$out[] = $row->color;
		}
		return $out;
	}

	public function get_receipt_filter_sizes($filters = array())
	{
		$this->db->select('DISTINCT product_variants.size', false);
		$this->db->from('product_variants');
		$this->db->join('product', 'product.id = product_variants.product_id', 'inner');
		$this->_apply_receipt_picker_scope($filters);
		$this->db->where('product_variants.size !=', '');
		$this->db->order_by('product_variants.size', 'ASC');
		$rows = $this->db->get()->result();
		$out = array();
		foreach ($rows as $row) {
			$out[] = $row->size;
		}
		return $out;
	}

	public function get_variants_by_ids($ids)
	{
		$ids = array_values(array_unique(array_filter(array_map('intval', (array) $ids))));
		if (empty($ids)) {
			return array();
		}
		$this->db->select('
			product_variants.*,
			product.name AS product_name,
			catalog.name AS catalog_name
		');
		$this->db->from('product_variants');
		$this->db->join('product', 'product.id = product_variants.product_id', 'inner');
		$this->db->join('catalog', 'catalog.id = product.catalog_id', 'left');
		$this->db->where_in('product_variants.id', $ids);
		$rows = $this->db->get()->result();
		$map = array();
		foreach ($rows as $row) {
			$map[(int) $row->id] = $row;
		}
		return $map;
	}

	public function get_recent_receipt_variants($admin_id, $limit = 10)
	{
		$admin_id = (int) $admin_id;
		$limit = max(1, min(20, (int) $limit));
		if ($admin_id <= 0) {
			return array();
		}

		$sql = '
			SELECT
				pv.id,
				pv.product_id,
				pv.sku,
				pv.color,
				pv.size,
				pv.stock,
				pv.min_stock,
				pv.cost_price,
				p.name AS product_name,
				MAX(sri.id) AS last_used_at
			FROM stock_receipt_items sri
			INNER JOIN stock_receipts sr ON sr.id = sri.receipt_id
			INNER JOIN product_variants pv ON pv.id = sri.variant_id
			INNER JOIN product p ON p.id = pv.product_id
			WHERE sr.created_by = ?
				AND sri.variant_id > 0
				AND pv.status = 1
			GROUP BY pv.id, pv.product_id, pv.sku, pv.color, pv.size, pv.stock, pv.min_stock, pv.cost_price, p.name
			ORDER BY last_used_at DESC
			LIMIT ?
		';
		return $this->db->query($sql, array($admin_id, $limit))->result();
	}

	protected function _apply_receipt_picker_scope($filters)
	{
		$this->db->where('product_variants.status', 1);
		if (!empty($filters['catalog_id'])) {
			$this->db->where('product.catalog_id', (int) $filters['catalog_id']);
		}
		if (!empty($filters['product_id'])) {
			$this->db->where('product_variants.product_id', (int) $filters['product_id']);
		}
	}

	protected function _apply_receipt_picker_filters($filters)
	{
		$this->_apply_receipt_picker_scope($filters);
		if (!empty($filters['color'])) {
			$this->db->where('product_variants.color', (string) $filters['color']);
		}
		if (!empty($filters['size'])) {
			$this->db->where('product_variants.size', (string) $filters['size']);
		}
		if (!empty($filters['q'])) {
			$q = trim((string) $filters['q']);
			if ($q !== '') {
				$this->db->group_start();
				$this->db->like('product_variants.sku', $q);
				$this->db->or_like('product.name', $q);
				$this->db->or_like('product_variants.color', $q);
				$this->db->or_like('product_variants.size', $q);
				$this->db->group_end();
			}
		}
	}

	protected function _apply_list_filters($filters)
	{
		if (!empty($filters['name'])) {
			$this->db->like('product.name', $filters['name']);
		}
		if (!empty($filters['catalog_id'])) {
			$this->db->where('product.catalog_id', (int) $filters['catalog_id']);
		}
		if (!empty($filters['sku'])) {
			$this->db->like('product_variants.sku', $filters['sku']);
		}
		if (isset($filters['stock'])) {
			if ($filters['stock'] === 'out') {
				$this->db->where('product_variants.stock <=', 0);
			} elseif ($filters['stock'] === 'low') {
				$this->db->where('product_variants.stock >', 0);
				$this->db->where('product_variants.stock <= product_variants.min_stock', null, false);
			} elseif ($filters['stock'] === 'ok') {
				$this->db->where('product_variants.stock > product_variants.min_stock', null, false);
			}
		}
	}
}
