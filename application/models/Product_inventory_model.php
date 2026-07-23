<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_inventory_model extends MY_Model {
	var $table = 'product_inventory';
	var $order = array('product_id', 'ASC');

	public function get_variant($product_id, $size, $color, $for_update = false)
	{
		$product_id = (int) $product_id;
		if ($for_update) {
			return $this->db->query(
				'SELECT * FROM `product_inventory` WHERE `product_id` = ? AND `size` = ? AND `color` = ? FOR UPDATE',
				array($product_id, $size, $color)
			)->row();
		}
		return $this->get_info_rule(array(
			'product_id' => $product_id,
			'size' => $size,
			'color' => $color,
		));
	}

	public function increment_qty($product_id, $size, $color, $qty, $now = null)
	{
		$product_id = (int) $product_id;
		$qty = (int) $qty;
		if ($product_id <= 0 || $qty <= 0) {
			return false;
		}
		$now = $now !== null ? (int) $now : time();

		$row = $this->get_variant($product_id, $size, $color, true);
		if ($row) {
			$this->db->set('quantity', 'quantity + ' . $qty, false);
			$this->db->set('updated', $now);
			$this->db->where('id', (int) $row->id);
			$this->db->update($this->table);
			return (int) $row->quantity + $qty;
		}

		$this->db->insert($this->table, array(
			'product_id' => $product_id,
			'size' => $size,
			'color' => $color,
			'quantity' => $qty,
			'updated' => $now,
		));

		return $qty;
	}

	public function sum_by_product($product_id)
	{
		$product_id = (int) $product_id;
		$row = $this->db->select_sum('quantity')
			->where('product_id', $product_id)
			->get($this->table)
			->row();
		return $row && $row->quantity !== null ? (int) $row->quantity : 0;
	}
}
