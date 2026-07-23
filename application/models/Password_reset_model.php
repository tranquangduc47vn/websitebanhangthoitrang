<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Password_reset_model extends MY_Model {
	var $table = 'password_resets';

	const EXPIRE_SECONDS = 900;

	public function invalidate_user_tokens($user_id)
	{
		$user_id = (int) $user_id;
		if ($user_id <= 0) {
			return;
		}
		$this->update_rule(
			array('user_id' => $user_id, 'used_at' => 0),
			array('used_at' => time())
		);
	}

	public function create_token($user_id, $token_plain)
	{
		$user_id = (int) $user_id;
		$this->invalidate_user_tokens($user_id);

		$data = array(
			'user_id' => $user_id,
			'token_hash' => hash_reset_token($token_plain),
			'expires_at' => time() + self::EXPIRE_SECONDS,
			'used_at' => 0,
			'created' => time(),
		);

		if ($this->create($data)) {
			return (int) $this->db->insert_id();
		}

		return 0;
	}

	public function find_valid_by_token($token_plain)
	{
		$token_plain = trim((string) $token_plain);
		if ($token_plain === '') {
			return false;
		}

		$row = $this->get_info_rule(array(
			'token_hash' => hash_reset_token($token_plain),
			'used_at' => 0,
		));

		if (!$row) {
			return false;
		}

		if ((int) $row->expires_at < time()) {
			return false;
		}

		return $row;
	}

	public function mark_used($id)
	{
		return $this->update((int) $id, array('used_at' => time()));
	}
}
