<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ai_setting_model extends MY_Model {
	var $table = 'ai_setting';

	protected static $cache = null;

	public function all()
	{
		if (self::$cache === null) {
			self::$cache = array();
			if ($this->db->table_exists($this->table)) {
				$rows = $this->db->get($this->table)->result();
				foreach ($rows as $row) {
					self::$cache[$row->setting_key] = $row->setting_value;
				}
			}
		}
		return self::$cache;
	}

	public function get($key, $default = '')
	{
		$all = $this->all();
		return isset($all[$key]) ? $all[$key] : $default;
	}

	public function get_bool($key, $default = false)
	{
		$value = $this->get($key, $default ? '1' : '0');
		return $value === '1' || $value === 1 || $value === true;
	}

	public function set($key, $value)
	{
		$existing = $this->db->where('setting_key', $key)->get($this->table)->row();
		if ($existing) {
			$this->db->where('setting_key', $key)->update($this->table, array(
				'setting_value' => (string) $value,
				'updated' => time(),
			));
		} else {
			$this->db->insert($this->table, array(
				'setting_key' => $key,
				'setting_value' => (string) $value,
				'updated' => time(),
			));
		}
		self::$cache = null;
		return true;
	}

	public function set_many(array $pairs)
	{
		foreach ($pairs as $key => $value) {
			$this->set($key, $value);
		}
		return true;
	}
}
