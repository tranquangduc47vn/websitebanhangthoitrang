<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_address_model extends MY_Model {
	var $table = 'user_address';
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

	public function get_owned($address_id, $user_id)
	{
		return $this->get_info_rule(array(
			'id' => (int) $address_id,
			'user_id' => (int) $user_id,
		));
	}

	public function ensure_legacy_from_user_row($user_id, $legacy_address_line)
	{
		if ($this->count_for_user($user_id) > 0) {
			return;
		}
		$line = trim((string) $legacy_address_line);
		if ($line === '') {
			return;
		}
		$this->create(array(
			'user_id' => (int) $user_id,
			'address_note' => '',
			'province_id' => '',
			'district_id' => '',
			'ward_id' => '',
			'address_line' => $line,
			'is_default' => 1,
			'created' => time(),
		));
	}

	public function sync_user_primary_address($user_id)
	{
		$user_id = (int) $user_id;
		$list = $this->list_for_user($user_id);
		$line = '';
		if (!empty($list)) {
			foreach ($list as $row) {
				if ((int) $row->is_default === 1) {
					$line = $row->address_line;
					break;
				}
			}
			if ($line === '') {
				$line = $list[0]->address_line;
				$this->update_rule(
					array('user_id' => $user_id),
					array('is_default' => 0)
				);
				$this->update($list[0]->id, array('is_default' => 1));
			}
		}
		$this->load->model('user_model');
		$this->user_model->update($user_id, array('address' => $line));
		return $line;
	}

	public function set_default($user_id, $address_id)
	{
		$user_id = (int) $user_id;
		$address_id = (int) $address_id;
		if (!$this->get_owned($address_id, $user_id)) {
			return false;
		}
		$this->update_rule(array('user_id' => $user_id), array('is_default' => 0));
		$this->update($address_id, array('is_default' => 1));
		return true;
	}
}
