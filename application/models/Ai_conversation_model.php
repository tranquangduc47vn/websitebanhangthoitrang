<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ai_conversation_model extends MY_Model {
	var $table = 'ai_conversation';
	var $order = array('id', 'DESC');

	public function find_or_create($user_id, $guest_token, $conversation_id = 0)
	{
		$conversation_id = (int) $conversation_id;
		if ($conversation_id > 0) {
			$existing = $this->get_info($conversation_id);
			if ($existing) {
				return $existing;
			}
		}

		$data = array(
			'user_id' => (int) $user_id,
			'guest_token' => (string) $guest_token,
			'status' => 'ai_active',
			'started' => time(),
			'last_message' => time(),
		);
		if ($this->db->field_exists('staff_id', $this->table)) {
			$data['staff_id'] = 0;
			$data['staff_name'] = '';
			$data['unread_staff'] = 0;
			$data['unread_customer'] = 0;
		}
		$this->create($data);
		$new_id = $this->db->insert_id();
		return $this->get_info($new_id);
	}

	public function touch($id, $status = null)
	{
		$data = array('last_message' => time());
		if ($status !== null) {
			$data['status'] = $status;
		}
		return $this->update($id, $data);
	}

	public function search($keyword = '', $input = array())
	{
		if ($keyword !== '') {
			$this->db->group_start();
			$this->db->like('guest_token', $keyword);
			$this->db->or_where('id', (int) $keyword);
			if ($this->db->field_exists('staff_name', $this->table)) {
				$this->db->or_like('staff_name', $keyword);
			}
			$this->db->group_end();
		}
		if (!empty($input['limit'])) {
			$this->db->limit($input['limit'][0], $input['limit'][1]);
		}
		$this->db->order_by('last_message', 'DESC');
		return $this->db->get($this->table)->result();
	}

	public function count_by_status($status)
	{
		$this->db->where('status', (string) $status);
		return (int) $this->db->count_all_results($this->table);
	}

	public function count_waiting()
	{
		if (!$this->db->field_exists('unread_staff', $this->table)) {
			$this->db->where_in('status', array('waiting_staff', 'handed_off'));
			return (int) $this->db->count_all_results($this->table);
		}
		$this->db->where_in('status', array('waiting_staff', 'handed_off'));
		return (int) $this->db->count_all_results($this->table);
	}

	public function list_inbox($filters = array())
	{
		if (!empty($filters['status']) && $filters['status'] !== 'all') {
			$this->db->where('status', (string) $filters['status']);
		}
		if (!empty($filters['q'])) {
			$q = trim((string) $filters['q']);
			$this->db->group_start();
			$this->db->like('guest_token', $q);
			$this->db->or_where('id', (int) $q);
			if ($this->db->field_exists('staff_name', $this->table)) {
				$this->db->or_like('staff_name', $q);
			}
			$this->db->group_end();
		}
		if (!empty($filters['unread_only']) && $this->db->field_exists('unread_staff', $this->table)) {
			$this->db->where('unread_staff', 1);
		}
		$limit = isset($filters['limit']) ? (int) $filters['limit'] : 100;
		$this->db->order_by('last_message', 'DESC');
		$this->db->limit($limit);
		return $this->db->get($this->table)->result();
	}

	public function assign_staff($id, $staff_id, $staff_name)
	{
		if (!$this->db->field_exists('staff_id', $this->table)) {
			return $this->update((int) $id, array('status' => 'staff_joined'));
		}
		$data = array(
			'staff_id' => (int) $staff_id,
			'staff_name' => (string) $staff_name,
			'status' => 'staff_joined',
		);
		if ($this->db->field_exists('unread_staff', $this->table)) {
			$data['unread_staff'] = 0;
		}
		return $this->update((int) $id, $data);
	}

	public function release_staff($id)
	{
		if (!$this->db->field_exists('staff_id', $this->table)) {
			return $this->update((int) $id, array('status' => 'ai_active'));
		}
		$data = array(
			'staff_id' => 0,
			'staff_name' => '',
			'status' => 'ai_active',
		);
		if ($this->db->field_exists('unread_staff', $this->table)) {
			$data['unread_staff'] = 0;
		}
		return $this->update((int) $id, $data);
	}

	// Giá trị cột an toàn khi DB chưa chạy upgrade migration.
	public function field_value($row, $field, $default = '')
	{
		if (!$row || !is_object($row) || !isset($row->$field)) {
			return $default;
		}
		return $row->$field;
	}

	public function has_support_columns()
	{
		return $this->db->field_exists('staff_id', $this->table);
	}
}
