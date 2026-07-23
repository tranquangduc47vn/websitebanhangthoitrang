<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_phone_model extends MY_Model {
	var $table = 'user_phone';
	var $order = array('is_default', 'DESC');

	const MAX_PER_USER = 3;

	public function count_for_user($user_id)
	{
		return (int) $this->get_total(array('where' => array('user_id' => (int) $user_id)));
	}

	public function list_for_user($user_id)
	{
		return $this->get_list(array(
			'where' => array('user_id' => (int) $user_id),
			'order_raw' => 'is_default DESC, id ASC',
		));
	}

	public function get_owned($phone_id, $user_id)
	{
		return $this->get_info_rule(array(
			'id' => (int) $phone_id,
			'user_id' => (int) $user_id,
		));
	}

	public function find_by_number($user_id, $phone_number)
	{
		return $this->get_info_rule(array(
			'user_id' => (int) $user_id,
			'phone_number' => (string) $phone_number,
		));
	}

	public function ensure_legacy_from_user_row($user_id, $legacy_phone)
	{
		if ($this->count_for_user($user_id) > 0) {
			return;
		}
		$phone = trim((string) $legacy_phone);
		if ($phone === '') {
			return;
		}
		$this->create(array(
			'user_id' => (int) $user_id,
			'phone_label' => '',
			'phone_number' => $phone,
			'is_default' => 1,
			'created' => time(),
		));
	}

	public function sync_user_primary_phone($user_id)
	{
		$user_id = (int) $user_id;
		$list = $this->list_for_user($user_id);
		$phone = '';
		if (!empty($list)) {
			foreach ($list as $row) {
				if ((int) $row->is_default === 1) {
					$phone = $row->phone_number;
					break;
				}
			}
			if ($phone === '') {
				$phone = $list[0]->phone_number;
				$this->update_rule(array('user_id' => $user_id), array('is_default' => 0));
				$this->update($list[0]->id, array('is_default' => 1));
			}
		}
		$this->load->model('user_model');
		$this->user_model->update($user_id, array('phone' => $phone));
		return $phone;
	}

	public function set_default($user_id, $phone_id)
	{
		$user_id = (int) $user_id;
		$phone_id = (int) $phone_id;
		if (!$this->get_owned($phone_id, $user_id)) {
			return false;
		}
		$this->update_rule(array('user_id' => $user_id), array('is_default' => 0));
		$this->update($phone_id, array('is_default' => 1));
		return true;
	}
}
