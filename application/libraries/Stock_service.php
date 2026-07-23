<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Nghiệp vụ nhập kho: phiếu draft, xác nhận, hủy, biến động tồn theo variant.
 */
class Stock_service {

	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('stock_receipt_model');
		$this->CI->load->model('stock_receipt_item_model');
		$this->CI->load->model('product_variant_model');
		$this->CI->load->model('stock_movement_model');
		$this->CI->load->model('product_model');
		$this->CI->load->library('product_service');
	}

	public function parse_product_variants($product)
	{
		return $this->CI->product_service->parse_product_attributes($product);
	}

	public function generate_receipt_code($date = null)
	{
		$date = $date ?: date('Ymd');
		$prefix = 'PN' . $date;
		$this->CI->db->like('receipt_code', $prefix, 'after');
		$this->CI->db->order_by('receipt_code', 'DESC');
		$this->CI->db->limit(1);
		$row = $this->CI->db->get('stock_receipts')->row();

		$seq = 1;
		if ($row && !empty($row->receipt_code) && preg_match('/^PN' . preg_quote($date, '/') . '(\d{3})$/', $row->receipt_code, $m)) {
			$seq = (int) $m[1] + 1;
		}

		return $prefix . str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
	}

	public function validate_line_items(array $lines)
	{
		$errors = array();
		$normalized = array();

		if (empty($lines)) {
			return array('ok' => false, 'errors' => array('Phiếu nhập cần ít nhất một dòng sản phẩm.'), 'lines' => array());
		}

		foreach ($lines as $idx => $line) {
			$variant_id = isset($line['variant_id']) ? (int) $line['variant_id'] : 0;
			$qty = isset($line['qty']) ? (int) $line['qty'] : 0;
			$unit_cost = isset($line['unit_cost']) ? (float) $line['unit_cost'] : 0;

			if ($variant_id <= 0) {
				$product_id = isset($line['product_id']) ? (int) $line['product_id'] : 0;
				$size = trim(isset($line['size']) ? $line['size'] : '');
				$color = trim(isset($line['color']) ? $line['color'] : '');
				if ($product_id > 0) {
					$variant_id = $this->CI->product_service->resolve_variant_id($product_id, $color, $size, true);
				}
			}

			if ($variant_id <= 0) {
				$errors[] = 'Dòng ' . ($idx + 1) . ': chọn biến thể (SKU).';
				continue;
			}

			$variant = $this->CI->product_variant_model->get_info($variant_id);
			if (!$variant || (int) $variant->status !== 1) {
				$errors[] = 'Dòng ' . ($idx + 1) . ': biến thể không tồn tại hoặc đã ngưng.';
				continue;
			}

			if ($qty <= 0 || $qty > 999999) {
				$errors[] = 'Dòng ' . ($idx + 1) . ': số lượng phải từ 1 đến 999.999.';
				continue;
			}
			if ($unit_cost < 0) {
				$errors[] = 'Dòng ' . ($idx + 1) . ': giá nhập không được âm.';
				continue;
			}

			$key = (string) $variant_id;
			if (isset($normalized[$key])) {
				$normalized[$key]['qty'] += $qty;
			} else {
				$normalized[$key] = array(
					'variant_id' => $variant_id,
					'product_id' => (int) $variant->product_id,
					'size' => $variant->size,
					'color' => $variant->color,
					'qty' => $qty,
					'unit_cost' => max(0, $unit_cost),
					'subtotal' => max(0, $unit_cost) * $qty,
				);
			}
		}

		if (!empty($errors)) {
			return array('ok' => false, 'errors' => $errors, 'lines' => array());
		}

		foreach ($normalized as &$n) {
			$n['subtotal'] = $n['unit_cost'] * $n['qty'];
		}
		unset($n);

		return array('ok' => true, 'errors' => array(), 'lines' => array_values($normalized));
	}

	public function create_draft_receipt($supplier_id, $note, $lines, $admin_id, $receipt_date = null)
	{
		$validation = $this->validate_line_items($lines);
		if (!$validation['ok']) {
			return array('ok' => false, 'message' => implode(' ', $validation['errors']));
		}

		$now = time();
		$total_qty = 0;
		$total_amount = 0;
		foreach ($validation['lines'] as $line) {
			$total_qty += (int) $line['qty'];
			$total_amount += (float) $line['subtotal'];
		}

		$this->CI->db->trans_start();

		$receipt_code = $this->generate_receipt_code();
		$attempts = 0;
		while ($this->CI->stock_receipt_model->check_exists(array('receipt_code' => $receipt_code)) && $attempts < 5) {
			$attempts++;
			$receipt_code = $this->generate_receipt_code();
		}

		$this->CI->stock_receipt_model->create(array(
			'receipt_code' => $receipt_code,
			'supplier_id' => max(0, (int) $supplier_id),
			'status' => 'draft',
			'note' => trim((string) $note),
			'total_qty' => $total_qty,
			'total_amount' => $total_amount,
			'receipt_date' => $receipt_date ?: date('Y-m-d'),
			'created_by' => (int) $admin_id,
			'created' => $now,
			'updated' => $now,
		));

		$receipt_id = (int) $this->CI->db->insert_id();
		if ($receipt_id <= 0) {
			$this->CI->db->trans_complete();
			return array('ok' => false, 'message' => 'Không tạo được phiếu nhập.');
		}

		foreach ($validation['lines'] as $line) {
			$this->CI->stock_receipt_item_model->create(array(
				'receipt_id' => $receipt_id,
				'variant_id' => $line['variant_id'],
				'product_id' => $line['product_id'],
				'size' => $line['size'],
				'color' => $line['color'],
				'qty' => $line['qty'],
				'unit_cost' => $line['unit_cost'],
				'subtotal' => $line['subtotal'],
			));
		}

		$this->CI->db->trans_complete();

		if ($this->CI->db->trans_status() === false) {
			return array('ok' => false, 'message' => 'Lỗi khi lưu phiếu nhập.');
		}

		return array('ok' => true, 'receipt_id' => $receipt_id, 'receipt_code' => $receipt_code);
	}

	public function update_draft_receipt($receipt_id, $supplier_id, $note, $lines, $receipt_date = null)
	{
		$receipt_id = (int) $receipt_id;
		$receipt = $this->CI->stock_receipt_model->get_info($receipt_id);
		if (!$receipt) {
			return array('ok' => false, 'message' => 'Phiếu nhập không tồn tại.');
		}
		if ($receipt->status !== 'draft') {
			return array('ok' => false, 'message' => 'Chỉ được sửa phiếu ở trạng thái nháp.');
		}

		$validation = $this->validate_line_items($lines);
		if (!$validation['ok']) {
			return array('ok' => false, 'message' => implode(' ', $validation['errors']));
		}

		$now = time();
		$total_qty = 0;
		$total_amount = 0;
		foreach ($validation['lines'] as $line) {
			$total_qty += (int) $line['qty'];
			$total_amount += (float) $line['subtotal'];
		}

		$this->CI->db->trans_start();

		$this->CI->stock_receipt_model->update($receipt_id, array(
			'supplier_id' => max(0, (int) $supplier_id),
			'note' => trim((string) $note),
			'total_qty' => $total_qty,
			'total_amount' => $total_amount,
			'receipt_date' => $receipt_date ?: ($receipt->receipt_date ?? date('Y-m-d')),
			'updated' => $now,
		));

		$this->CI->stock_receipt_item_model->delete_by_receipt($receipt_id);
		foreach ($validation['lines'] as $line) {
			$this->CI->stock_receipt_item_model->create(array(
				'receipt_id' => $receipt_id,
				'variant_id' => $line['variant_id'],
				'product_id' => $line['product_id'],
				'size' => $line['size'],
				'color' => $line['color'],
				'qty' => $line['qty'],
				'unit_cost' => $line['unit_cost'],
				'subtotal' => $line['subtotal'],
			));
		}

		$this->CI->db->trans_complete();

		if ($this->CI->db->trans_status() === false) {
			return array('ok' => false, 'message' => 'Lỗi khi cập nhật phiếu nhập.');
		}

		return array('ok' => true, 'receipt_id' => $receipt_id);
	}

	public function confirm_receipt($receipt_id, $admin_id)
	{
		$receipt_id = (int) $receipt_id;
		$admin_id = (int) $admin_id;

		$this->CI->db->trans_start();

		$receipt = $this->CI->db->query(
			'SELECT * FROM `stock_receipts` WHERE `id` = ? FOR UPDATE',
			array($receipt_id)
		)->row();

		if (!$receipt) {
			$this->CI->db->trans_complete();
			return array('ok' => false, 'message' => 'Phiếu nhập không tồn tại.');
		}
		if ($receipt->status !== 'draft') {
			$this->CI->db->trans_complete();
			return array('ok' => false, 'message' => 'Phiếu đã xác nhận hoặc đã hủy, không thể xác nhận lại.');
		}

		$items = $this->CI->stock_receipt_item_model->get_by_receipt($receipt_id);
		if (empty($items)) {
			$this->CI->db->trans_complete();
			return array('ok' => false, 'message' => 'Phiếu không có dòng sản phẩm.');
		}

		$now = time();
		$product_ids = array();
		$total_amount = 0;

		foreach ($items as $item) {
			$variant_id = (int) $item->variant_id;
			if ($variant_id <= 0) {
				$variant_id = $this->CI->product_service->resolve_variant_id(
					(int) $item->product_id,
					$item->color,
					$item->size,
					true
				);
			}

			$variant = $this->CI->product_variant_model->get_for_update($variant_id);
			if (!$variant) {
				$this->CI->db->trans_complete();
				return array('ok' => false, 'message' => 'Biến thể không tồn tại trên phiếu.');
			}

			$qty = (int) $item->qty;
			$before_qty = (int) $variant->stock;
			$after_qty = $before_qty + $qty;

			$this->CI->product_variant_model->set_stock($variant_id, $after_qty, $now);

			if ((float) $item->unit_cost > 0) {
				$this->CI->product_variant_model->update($variant_id, array(
					'cost_price' => (float) $item->unit_cost,
					'updated' => $now,
				));
			}

			$this->CI->stock_movement_model->create(array(
				'variant_id' => $variant_id,
				'product_id' => (int) $variant->product_id,
				'size' => $variant->size,
				'color' => $variant->color,
				'movement_type' => 'in',
				'qty_change' => $qty,
				'before_qty' => $before_qty,
				'after_qty' => $after_qty,
				'reference_type' => 'receipt',
				'reference_id' => $receipt_id,
				'note' => 'Nhập kho ' . $receipt->receipt_code,
				'created_by' => $admin_id,
				'created' => $now,
			));

			$total_amount += (float) $item->subtotal;
			if ($total_amount <= 0) {
				$total_amount += (float) $item->unit_cost * $qty;
			}
			$product_ids[(int) $variant->product_id] = true;
		}

		$this->CI->stock_receipt_model->update($receipt_id, array(
			'status' => 'confirmed',
			'confirmed_by' => $admin_id,
			'confirmed_at' => $now,
			'total_amount' => $total_amount,
			'updated' => $now,
		));

		$this->CI->db->trans_complete();

		if ($this->CI->db->trans_status() === false) {
			return array('ok' => false, 'message' => 'Xác nhận phiếu thất bại (transaction rollback).');
		}

		foreach (array_keys($product_ids) as $pid) {
			$this->CI->product_service->sync_product_quantity_cache($pid);
		}

		return array('ok' => true, 'message' => 'Đã xác nhận phiếu ' . $receipt->receipt_code . ' và cộng tồn kho.');
	}

	public function cancel_receipt($receipt_id, $admin_id)
	{
		$receipt_id = (int) $receipt_id;
		$receipt = $this->CI->stock_receipt_model->get_info($receipt_id);
		if (!$receipt) {
			return array('ok' => false, 'message' => 'Phiếu nhập không tồn tại.');
		}
		if ($receipt->status !== 'draft') {
			return array('ok' => false, 'message' => 'Chỉ hủy được phiếu nháp.');
		}

		$now = time();
		$this->CI->stock_receipt_model->update($receipt_id, array(
			'status' => 'cancelled',
			'cancelled_at' => $now,
			'updated' => $now,
		));

		return array('ok' => true, 'message' => 'Đã hủy phiếu ' . $receipt->receipt_code . '.');
	}

	public function status_label($status)
	{
		switch ($status) {
			case 'draft':
				return 'Nháp';
			case 'confirmed':
				return 'Đã xác nhận';
			case 'cancelled':
				return 'Đã hủy';
			default:
				return $status;
		}
	}
}
