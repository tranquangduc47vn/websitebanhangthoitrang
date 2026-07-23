<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_receipt_item_model extends MY_Model {
	var $table = 'stock_receipt_items';
	var $order = array('id', 'ASC');

	public function get_by_receipt($receipt_id)
	{
		$receipt_id = (int) $receipt_id;
		if ($receipt_id <= 0) {
			return array();
		}

		$this->db->select('stock_receipt_items.*, product.name AS product_name, product.image_link');
		$this->db->from($this->table);
		$this->db->join('product', 'product.id = stock_receipt_items.product_id', 'left');
		$this->db->where('stock_receipt_items.receipt_id', $receipt_id);
		$this->db->order_by('stock_receipt_items.id', 'ASC');

		return $this->db->get()->result();
	}

	public function delete_by_receipt($receipt_id)
	{
		$receipt_id = (int) $receipt_id;
		if ($receipt_id <= 0) {
			return false;
		}
		$this->db->where('receipt_id', $receipt_id);
		return $this->db->delete($this->table);
	}
}
