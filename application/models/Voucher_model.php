<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Voucher_model extends MY_Model {
	var $table = 'voucher';

	public function validate_for_checkout($code, $user_id, $cart_total)
	{
		$this->load->helper('loyalty');
		$code = loyalty_normalize_voucher_code($code);
		$user_id = (int) $user_id;
		$cart_total = (int) $cart_total;

		if ($code === '') {
			return array('ok' => false, 'message' => 'Vui lòng nhập mã voucher.');
		}
		if ($user_id <= 0) {
			return array('ok' => false, 'message' => 'Bạn cần đăng nhập để sử dụng mã giảm giá.');
		}
		if ($cart_total <= 0) {
			return array('ok' => false, 'message' => 'Giỏ hàng không hợp lệ.');
		}

		$row = $this->db->where('code', $code)->get($this->table)->row();
		if (!$row) {
			return array('ok' => false, 'message' => 'Mã voucher không tồn tại hoặc đã hết hiệu lực.');
		}
		if ((int) $row->is_active !== 1) {
			return array('ok' => false, 'message' => 'Mã voucher đang tạm khóa.');
		}

		$now = time();
		if ((int) $row->valid_from > 0 && $now < (int) $row->valid_from) {
			return array('ok' => false, 'message' => 'Mã voucher chưa đến thời gian áp dụng.');
		}
		if ((int) $row->valid_to > 0 && $now > (int) $row->valid_to) {
			return array('ok' => false, 'message' => 'Mã voucher đã hết hạn.');
		}

		if ((int) $row->user_id > 0 && (int) $row->user_id !== $user_id) {
			return array('ok' => false, 'message' => 'Mã voucher không dành cho tài khoản của bạn.');
		}

		if ((int) $row->min_order_amount > 0 && $cart_total < (int) $row->min_order_amount) {
			return array(
				'ok' => false,
				'message' => 'Đơn hàng tối thiểu ' . number_format((int) $row->min_order_amount, 0, ',', '.') . ' ₫ để dùng mã này.',
			);
		}

		$limit = (int) $row->usage_limit;
		if ($limit > 0 && (int) $row->used_count >= $limit) {
			return array('ok' => false, 'message' => 'Mã voucher đã hết lượt sử dụng.');
		}

		$user = $this->db->where('id', $user_id)->get('user')->row();
		$user_tier = ($user && isset($user->loyalty_tier)) ? $user->loyalty_tier : 'member';
		if (!loyalty_tier_meets($user_tier, $row->tier_min)) {
			return array(
				'ok' => false,
				'message' => 'Mã này dành cho hạng ' . loyalty_tier_label($row->tier_min) . ' trở lên.',
			);
		}

		$per_user = (int) $row->per_user_limit;
		if ($per_user > 0) {
			$used_by_user = (int) $this->db
				->where('voucher_id', (int) $row->id)
				->where('user_id', $user_id)
				->count_all_results('voucher_use');
			if ($used_by_user >= $per_user) {
				return array('ok' => false, 'message' => 'Bạn đã sử dụng hết lượt cho mã này.');
			}
		}

		$discount = loyalty_calc_voucher_discount($row, $cart_total);
		if ($discount <= 0) {
			return array('ok' => false, 'message' => 'Mã voucher không áp dụng được cho đơn này.');
		}

		return array(
			'ok' => true,
			'message' => '',
			'voucher' => $row,
			'discount' => $discount,
		);
	}

	public function record_use($voucher_id, $user_id, $transaction_id, $discount_amount)
	{
		$voucher_id = (int) $voucher_id;
		$data = array(
			'voucher_id' => $voucher_id,
			'user_id' => (int) $user_id,
			'transaction_id' => (int) $transaction_id,
			'discount_amount' => (int) $discount_amount,
			'created' => time(),
		);
		$this->db->insert('voucher_use', $data);
		$this->db->set('used_count', 'used_count + 1', false);
		$this->db->where('id', $voucher_id);
		$this->db->update($this->table);
	}

	public function release_for_transaction($transaction_id)
	{
		$transaction_id = (int) $transaction_id;
		if ($transaction_id <= 0) {
			return;
		}
		$uses = $this->db->where('transaction_id', $transaction_id)->get('voucher_use')->result();
		foreach ($uses as $use) {
			$vid = (int) $use->voucher_id;
			$voucher = $this->get_info($vid);
			if ($voucher) {
				$new_count = max(0, (int) $voucher->used_count - 1);
				$this->update($vid, array('used_count' => $new_count));
			}
		}
		$this->db->where('transaction_id', $transaction_id)->delete('voucher_use');
	}

	// Voucher còn hiệu lực cho khách (gợi ý trên tài khoản).
	public function list_available_for_user($user_id)
	{
		$user_id = (int) $user_id;
		if ($user_id <= 0) {
			return array();
		}
		$user = $this->db->where('id', $user_id)->get('user')->row();
		$user_tier = ($user && isset($user->loyalty_tier)) ? $user->loyalty_tier : 'member';
		$now = time();

		$this->db->where('is_active', 1);
		$this->db->group_start();
		$this->db->where('user_id', 0);
		$this->db->or_where('user_id', $user_id);
		$this->db->group_end();
		$rows = $this->db->order_by('id', 'DESC')->get($this->table)->result();
		$out = array();
		foreach ($rows as $row) {
			if ((int) $row->valid_from > 0 && $now < (int) $row->valid_from) {
				continue;
			}
			if ((int) $row->valid_to > 0 && $now > (int) $row->valid_to) {
				continue;
			}
			$limit = (int) $row->usage_limit;
			if ($limit > 0 && (int) $row->used_count >= $limit) {
				continue;
			}
			if (!loyalty_tier_meets($user_tier, $row->tier_min)) {
				continue;
			}
			$out[] = $row;
		}
		return $out;
	}
}
