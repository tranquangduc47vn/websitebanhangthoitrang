<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Thay đổi tồn kho qua kiểm kê / đơn hàng (luôn ghi stock_movements).
 */
class Inventory_service {

	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('product_variant_model');
		$this->CI->load->model('stock_movement_model');
		$this->CI->load->model('product_model');
		$this->CI->load->library('product_service');
		$this->CI->config->load('stock');
	}

	public function adjust_stock($variant_id, $new_qty, $note, $admin_id)
	{
		$variant_id = (int) $variant_id;
		$new_qty = (int) $new_qty;
		$admin_id = (int) $admin_id;
		$note = trim((string) $note);

		if ($variant_id <= 0) {
			return array('ok' => false, 'message' => 'Biến thể không hợp lệ.');
		}
		if ($note === '') {
			return array('ok' => false, 'message' => 'Vui lòng nhập lý do điều chỉnh.');
		}
		if ($new_qty < 0 && !$this->CI->config->item('allow_negative_stock')) {
			return array('ok' => false, 'message' => 'Không cho phép tồn kho âm.');
		}

		$this->CI->db->trans_start();

		$variant = $this->CI->product_variant_model->get_for_update($variant_id);
		if (!$variant) {
			$this->CI->db->trans_complete();
			return array('ok' => false, 'message' => 'Biến thể không tồn tại.');
		}

		$before = (int) $variant->stock;
		$delta = $new_qty - $before;
		if ($delta === 0) {
			$this->CI->db->trans_complete();
			return array('ok' => false, 'message' => 'Tồn kho không thay đổi.');
		}

		$now = time();
		$this->CI->product_variant_model->set_stock($variant_id, $new_qty, $now);

		$this->CI->stock_movement_model->create(array(
			'variant_id' => $variant_id,
			'product_id' => (int) $variant->product_id,
			'size' => $variant->size,
			'color' => $variant->color,
			'movement_type' => 'adjust',
			'qty_change' => $delta,
			'before_qty' => $before,
			'after_qty' => $new_qty,
			'reference_type' => 'adjustment',
			'reference_id' => 0,
			'note' => $note,
			'created_by' => $admin_id,
			'created' => $now,
		));

		$this->CI->db->trans_complete();
		if ($this->CI->db->trans_status() === false) {
			return array('ok' => false, 'message' => 'Điều chỉnh thất bại.');
		}

		$this->CI->product_service->sync_product_quantity_cache((int) $variant->product_id);

		return array('ok' => true, 'message' => 'Đã điều chỉnh tồn kho biến thể ' . $variant->sku . '.');
	}

	public function deduct_for_order($variant_id, $qty, $reference_id, $admin_id = 0, $note = '', $manage_transaction = true)
	{
		$variant_id = (int) $variant_id;
		$qty = (int) $qty;
		if ($variant_id <= 0 || $qty <= 0) {
			return array('ok' => false, 'message' => 'Dữ liệu xuất kho không hợp lệ.');
		}

		if ($manage_transaction) {
			$this->CI->db->trans_start();
		}

		$variant = $this->CI->product_variant_model->get_for_update($variant_id);
		if (!$variant) {
			if ($manage_transaction) {
				$this->CI->db->trans_complete();
			}
			return array('ok' => false, 'message' => 'Biến thể không tồn tại.');
		}

		$before = (int) $variant->stock;
		$after = $before - $qty;
		if ($after < 0 && !$this->CI->config->item('allow_negative_stock')) {
			if ($manage_transaction) {
				$this->CI->db->trans_complete();
			}
			return array('ok' => false, 'message' => 'Không đủ tồn kho cho SKU ' . $variant->sku . '.');
		}

		$now = time();
		$this->CI->product_variant_model->set_stock($variant_id, $after, $now);

		$this->CI->stock_movement_model->create(array(
			'variant_id' => $variant_id,
			'product_id' => (int) $variant->product_id,
			'size' => $variant->size,
			'color' => $variant->color,
			'movement_type' => 'out',
			'qty_change' => -$qty,
			'before_qty' => $before,
			'after_qty' => $after,
			'reference_type' => 'order',
			'reference_id' => (int) $reference_id,
			'note' => $note !== '' ? $note : 'Xuất kho đơn hàng #' . (int) $reference_id,
			'created_by' => (int) $admin_id,
			'created' => $now,
		));

		if ($manage_transaction) {
			$this->CI->db->trans_complete();
			if ($this->CI->db->trans_status() === false) {
				return array('ok' => false, 'message' => 'Trừ tồn kho thất bại.');
			}
		}

		$this->CI->product_service->sync_product_quantity_cache((int) $variant->product_id);

		return array('ok' => true);
	}

	public function get_variant_stock($variant_id)
	{
		$row = $this->CI->product_variant_model->get_info((int) $variant_id);
		return $row ? (int) $row->stock : 0;
	}
}
