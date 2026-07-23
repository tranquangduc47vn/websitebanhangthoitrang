<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_variant_model extends MY_Model {

	var $table = 'product_variants';
	var $order = array('product_id', 'ASC');

	public function get_by_product($product_id)
	{
		return $this->get_list(array(
			'where' => array('product_id' => (int) $product_id),
			'order' => array('color', 'ASC'),
		));
	}

	public function get_by_sku($sku)
	{
		return $this->get_info_rule(array('sku' => (string) $sku));
	}

	public function find_variant($product_id, $color, $size)
	{
		return $this->get_info_rule(array(
			'product_id' => (int) $product_id,
			'color' => (string) $color,
			'size' => (string) $size,
		));
	}

	public function sku_exists($sku, $exclude_id = 0)
	{
		$this->db->where('sku', (string) $sku);
		if ((int) $exclude_id > 0) {
			$this->db->where('id !=', (int) $exclude_id);
		}
		return $this->db->count_all_results($this->table) > 0;
	}

	public function get_for_update($variant_id)
	{
		return $this->db->query(
			'SELECT * FROM `product_variants` WHERE `id` = ? FOR UPDATE',
			array((int) $variant_id)
		)->row();
	}

	public function set_stock($variant_id, $new_stock, $now = null)
	{
		$now = $now !== null ? (int) $now : time();
		return $this->update((int) $variant_id, array(
			'stock' => (int) $new_stock,
			'updated' => $now,
		));
	}

	public function change_stock($variant_id, $delta)
	{
		$variant_id = (int) $variant_id;
		$delta = (int) $delta;
		if ($variant_id <= 0 || $delta === 0) {
			return false;
		}
		$now = time();
		$this->db->set('stock', 'stock + ' . $delta, false);
		$this->db->set('updated', $now);
		$this->db->where('id', $variant_id);
		$this->db->update($this->table);
		return true;
	}

	public function sum_stock_by_product($product_id)
	{
		$row = $this->db->select_sum('stock')
			->where('product_id', (int) $product_id)
			->where('status', 1)
			->get($this->table)
			->row();
		return $row && $row->stock !== null ? (int) $row->stock : 0;
	}
}
