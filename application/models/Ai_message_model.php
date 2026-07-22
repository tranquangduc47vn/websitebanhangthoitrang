<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ai_message_model extends MY_Model {
	var $table = 'ai_message';
	var $order = array('id', 'ASC');

	public function log($conversation_id, $sender, $content, $meta = array())
	{
		return $this->create(array(
			'conversation_id' => (int) $conversation_id,
			'sender' => (string) $sender,
			'content' => (string) $content,
			'meta' => json_encode($meta, JSON_UNESCAPED_UNICODE),
			'created' => time(),
		));
	}

	public function for_conversation($conversation_id, $limit = 0)
	{
		$input = array(
			'where' => array('conversation_id' => (int) $conversation_id),
			'order' => array('id', 'ASC'),
		);
		if ($limit > 0) {
			$input['limit'] = array($limit, 0);
			$input['order'] = array('id', 'DESC');
			$rows = $this->get_list($input);
			return array_reverse($rows);
		}
		return $this->get_list($input);
	}

	public function since_id($conversation_id, $after_id)
	{
		$conversation_id = (int) $conversation_id;
		$after_id = (int) $after_id;
		$this->db->where('conversation_id', $conversation_id);
		if ($after_id > 0) {
			$this->db->where('id >', $after_id);
		}
		$this->db->order_by('id', 'ASC');
		return $this->db->get($this->table)->result();
	}

	public function last_message_preview($conversation_id)
	{
		$this->db->where('conversation_id', (int) $conversation_id);
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);
		$row = $this->db->get($this->table)->row();
		if (!$row) {
			return '';
		}
		$content = trim(strip_tags((string) $row->content));
		if (mb_strlen($content, 'UTF-8') > 80) {
			return mb_substr($content, 0, 80, 'UTF-8') . '…';
		}
		return $content;
	}
}
