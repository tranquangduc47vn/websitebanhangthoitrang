<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Order lookup scoped to logged-in user_id only — no email/phone lookup.
class OrderLookupService {

	public function recentOrdersForUser($userId, $limit = 5)
	{
		$userId = (int) $userId;
		if ($userId <= 0) {
			return array();
		}

		$CI = get_instance();
		$CI->load->model('transaction_model');
		$CI->load->helper('admin');

		$rows = $CI->transaction_model->get_list(array(
			'where' => array('user_id' => $userId),
			'order' => array('id', 'DESC'),
			'limit' => array(max(1, (int) $limit), 0),
		));

		$out = array();
		foreach ($rows as $row) {
			$out[] = array(
				'id' => (int) $row->id,
				'status_text' => admin_order_status_text($row->status),
				'amount' => (float) $row->amount,
				'amount_fmt' => number_format((float) $row->amount, 0, ',', '.') . ' đ',
				'created' => (int) $row->created,
				'created_fmt' => date('d/m/Y', (int) $row->created),
			);
		}
		return $out;
	}
}
