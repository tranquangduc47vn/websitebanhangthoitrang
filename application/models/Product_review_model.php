<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_review_model extends MY_Model {
	var $table = 'product_review';
	var $order = array('created', 'DESC');

	public function get_by_product($product_id, $limit = 50)
	{
		$product_id = (int) $product_id;
		if ($product_id <= 0) {
			return array();
		}

		return $this->get_list(array(
			'where' => array('product_id' => $product_id),
			'limit' => array((int) $limit, 0),
		));
	}

	public function has_reviewed($product_id, $user_id = 0, $session_token = '')
	{
		$product_id = (int) $product_id;
		if ($product_id <= 0) {
			return FALSE;
		}

		if ($user_id > 0) {
			return $this->check_exists(array(
				'product_id' => $product_id,
				'user_id' => (int) $user_id,
			));
		}

		if ($session_token !== '') {
			return $this->check_exists(array(
				'product_id' => $product_id,
				'session_token' => $session_token,
			));
		}

		return FALSE;
	}

	public function get_star_breakdown($product_id)
	{
		$product_id = (int) $product_id;
		$breakdown = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);

		if ($product_id <= 0) {
			return $breakdown;
		}

		$this->db->select('stars, COUNT(*) AS total');
		$this->db->where('product_id', $product_id);
		$this->db->group_by('stars');
		$rows = $this->db->get($this->table)->result();

		foreach ($rows as $row) {
			$star = (int) $row->stars;
			if ($star >= 1 && $star <= 5) {
				$breakdown[$star] = (int) $row->total;
			}
		}

		return $breakdown;
	}

	public function get_user_review($product_id, $user_id = 0, $session_token = '')
	{
		$product_id = (int) $product_id;
		if ($product_id <= 0) {
			return FALSE;
		}

		if ($user_id > 0) {
			return $this->get_info_rule(array(
				'product_id' => $product_id,
				'user_id' => (int) $user_id,
			));
		}

		if ($session_token !== '') {
			return $this->get_info_rule(array(
				'product_id' => $product_id,
				'session_token' => $session_token,
			));
		}

		return FALSE;
	}

	public function delete_user_review($product_id, $user_id = 0, $session_token = '')
	{
		$review = $this->get_user_review($product_id, $user_id, $session_token);
		if (!$review) {
			return FALSE;
		}

		return $this->delete($review->id);
	}

	public function get_recent_with_product($limit = 10, $range_start = 0, $range_end = 0)
	{
		$limit = (int) $limit;
		if ($limit <= 0) {
			$limit = 10;
		}

		$this->db->select('product_review.*, product.name AS product_name');
		$this->db->from($this->table);
		$this->db->join('product', 'product.id = product_review.product_id', 'left');
		if ($range_start > 0) {
			$this->db->where('product_review.created >=', (int) $range_start);
		}
		if ($range_end > 0) {
			$this->db->where('product_review.created <=', (int) $range_end);
		}
		$this->db->order_by('product_review.created', 'DESC');
		$this->db->limit($limit);

		return $this->db->get()->result();
	}

	public function count_in_range($range_start = 0, $range_end = 0)
	{
		if ($range_start > 0) {
			$this->db->where('created >=', (int) $range_start);
		}
		if ($range_end > 0) {
			$this->db->where('created <=', (int) $range_end);
		}
		return (int) $this->db->count_all_results($this->table);
	}

	public function sync_product_stats($product_id)
	{
		$product_id = (int) $product_id;
		if ($product_id <= 0) {
			return FALSE;
		}

		$this->db->select('COUNT(*) AS rate_count, COALESCE(SUM(stars), 0) AS rate_total', FALSE);
		$this->db->where('product_id', $product_id);
		$row = $this->db->get($this->table)->row();

		$rate_count = $row ? (int) $row->rate_count : 0;
		$rate_total = $row ? (int) $row->rate_total : 0;

		$this->load->model('product_model');
		$this->product_model->update($product_id, array(
			'rate_count' => $rate_count,
			'rate_total' => $rate_total,
		));

		return array(
			'rate_count' => $rate_count,
			'rate_total' => $rate_total,
		);
	}
}
