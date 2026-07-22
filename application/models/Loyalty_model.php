<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loyalty_model extends CI_Model {

	public function get_profile($user_id)
	{
		$user_id = (int) $user_id;
		if ($user_id <= 0) {
			return null;
		}
		$user = $this->db->where('id', $user_id)->get('user')->row();
		if (!$user) {
			return null;
		}
		$this->load->helper('loyalty');
		$tier = isset($user->loyalty_tier) ? $user->loyalty_tier : 'member';
		$completed = isset($user->loyalty_completed_orders) ? (int) $user->loyalty_completed_orders : 0;
		$spend = isset($user->loyalty_lifetime_spend) ? (int) $user->loyalty_lifetime_spend : 0;
		$computed = loyalty_compute_tier($completed, $spend);
		if ($computed !== $tier) {
			$this->db->where('id', $user_id)->update('user', array('loyalty_tier' => $computed));
			$tier = $computed;
		}
		return (object) array(
			'points' => isset($user->loyalty_points) ? (int) $user->loyalty_points : 0,
			'lifetime_spend' => $spend,
			'completed_orders' => $completed,
			'tier' => $tier,
			'tier_label' => loyalty_tier_label($tier),
		);
	}

	public function credit_order_completed($transaction)
	{
		if (empty($transaction) || (int) $transaction->user_id <= 0) {
			return;
		}
		$tx_id = (int) $transaction->id;
		if ($this->_log_exists($tx_id, 'earn')) {
			return;
		}

		$this->load->helper('loyalty');
		$user_id = (int) $transaction->user_id;
		$user = $this->db->where('id', $user_id)->get('user')->row();
		if (!$user) {
			return;
		}

		$final_amount = (int) $transaction->amount;
		$tier = isset($user->loyalty_tier) ? $user->loyalty_tier : 'member';
		$points = loyalty_calc_points_for_amount($final_amount, $tier);

		$completed = (int) $user->loyalty_completed_orders + 1;
		$spend = (int) $user->loyalty_lifetime_spend + $final_amount;
		$new_tier = loyalty_compute_tier($completed, $spend);
		$new_points = (int) $user->loyalty_points + $points;

		$this->db->where('id', $user_id)->update('user', array(
			'loyalty_completed_orders' => $completed,
			'loyalty_lifetime_spend' => $spend,
			'loyalty_points' => $new_points,
			'loyalty_tier' => $new_tier,
		));

		if ($points > 0) {
			$this->db->insert('user_point_log', array(
				'user_id' => $user_id,
				'points' => $points,
				'type' => 'earn',
				'transaction_id' => $tx_id,
				'note' => 'Tích điểm đơn hoàn thành #' . $tx_id,
				'created' => time(),
			));
		}
	}

	public function reverse_order_completed($transaction)
	{
		if (empty($transaction) || (int) $transaction->user_id <= 0) {
			return;
		}
		$tx_id = (int) $transaction->id;
		if (!$this->_log_exists($tx_id, 'earn')) {
			return;
		}
		if ($this->_log_exists($tx_id, 'reverse')) {
			return;
		}

		$this->load->helper('loyalty');
		$user_id = (int) $transaction->user_id;
		$user = $this->db->where('id', $user_id)->get('user')->row();
		if (!$user) {
			return;
		}

		$earn_row = $this->db
			->where('transaction_id', $tx_id)
			->where('type', 'earn')
			->get('user_point_log')
			->row();
		$points = $earn_row ? (int) $earn_row->points : 0;

		$final_amount = (int) $transaction->amount;
		$completed = max(0, (int) $user->loyalty_completed_orders - 1);
		$spend = max(0, (int) $user->loyalty_lifetime_spend - $final_amount);
		$new_tier = loyalty_compute_tier($completed, $spend);
		$new_points = max(0, (int) $user->loyalty_points - $points);

		$this->db->where('id', $user_id)->update('user', array(
			'loyalty_completed_orders' => $completed,
			'loyalty_lifetime_spend' => $spend,
			'loyalty_points' => $new_points,
			'loyalty_tier' => $new_tier,
		));

		$this->db->insert('user_point_log', array(
			'user_id' => $user_id,
			'points' => -$points,
			'type' => 'reverse',
			'transaction_id' => $tx_id,
			'note' => 'Hoàn tích điểm do hủy đơn #' . $tx_id,
			'created' => time(),
		));
	}

	protected function _log_exists($transaction_id, $type)
	{
		return (int) $this->db
			->where('transaction_id', (int) $transaction_id)
			->where('type', $type)
			->count_all_results('user_point_log') > 0;
	}

	// Hoàn voucher + điểm (nếu đơn đã hoàn thành) khi hủy / xóa đơn.
	public function on_order_cancelled($transaction, $was_completed)
	{
		if (empty($transaction)) {
			return;
		}
		if ($was_completed) {
			$this->reverse_order_completed($transaction);
		}
		$this->load->model('voucher_model');
		$this->voucher_model->release_for_transaction((int) $transaction->id);
	}
}
