<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/ai/AiAssistantService.php';
require_once APPPATH . 'services/ai/ConversationLogger.php';

class SupportChatService {

	const STATUS_AI_ACTIVE = 'ai_active';
	const STATUS_WAITING_STAFF = 'waiting_staff';
	const STATUS_STAFF_JOINED = 'staff_joined';
	const STATUS_CLOSED = 'closed';

	public function handleCustomerMessage($message, $conversationId, $userId, $guestToken)
	{
		$logger = new ConversationLogger();
		$conversation = $logger->resolveConversation($userId, $guestToken, $conversationId);
		$status = $this->normalizeStatus($conversation->status);

		if ($status === self::STATUS_CLOSED) {
			$logger->touch($conversation->id, self::STATUS_AI_ACTIVE);
			$conversation->status = self::STATUS_AI_ACTIVE;
			$status = self::STATUS_AI_ACTIVE;
		}

		if ($status === self::STATUS_STAFF_JOINED) {
			return $this->customerMessageToStaff($conversation, $message, $logger);
		}

		if ($status === self::STATUS_WAITING_STAFF) {
			return $this->customerMessageWhileWaiting($conversation, $message, $logger);
		}

		$ai = new AiAssistantService();
		$result = $ai->handle($message, (int) $conversation->id, $userId, $guestToken);

		if (!empty($result['handoff'])) {
			$result['status'] = self::STATUS_WAITING_STAFF;
			$result['connecting'] = true;
			$this->markUnreadStaff((int) $result['conversation_id']);
		} else {
			$result['status'] = self::STATUS_AI_ACTIVE;
		}

		return $result;
	}

	protected function customerMessageToStaff($conversation, $message, ConversationLogger $logger)
	{
		$message = trim((string) $message);
		if ($message === '') {
			return $this->respondCustomer($conversation->id, '', self::STATUS_STAFF_JOINED, false, array(), $conversation);
		}

		$logger->logCustomerMessage($conversation->id, $message);
		$this->markUnreadStaff($conversation->id);
		$logger->touch($conversation->id);

		return $this->respondCustomer(
			$conversation->id,
			'',
			self::STATUS_STAFF_JOINED,
			false,
			array(),
			$conversation
		);
	}

	protected function customerMessageWhileWaiting($conversation, $message, ConversationLogger $logger)
	{
		$message = trim((string) $message);
		if ($message !== '') {
			$logger->logCustomerMessage($conversation->id, $message);
			$this->markUnreadStaff($conversation->id);
			$logger->touch($conversation->id);
		}

		return $this->respondCustomer(
			$conversation->id,
			'',
			self::STATUS_WAITING_STAFF,
			true,
			array(),
			$conversation
		);
	}

	public function pollForCustomer($conversationId, $afterMessageId, $userId, $guestToken)
	{
		$conversation = $this->authorizeCustomerConversation($conversationId, $userId, $guestToken);
		if (!$conversation) {
			return array('ok' => true, 'messages' => array(), 'status' => self::STATUS_AI_ACTIVE);
		}

		$this->clearUnreadCustomer($conversationId);
		$messages = $this->fetchMessagesSince($conversationId, $afterMessageId);

		$CI =& get_instance();
		$CI->load->model('ai_conversation_model');

		return array(
			'ok' => true,
			'conversation_id' => (int) $conversation->id,
			'status' => $this->normalizeStatus($conversation->status),
			'staff_name' => (string) $CI->ai_conversation_model->field_value($conversation, 'staff_name', ''),
			'messages' => $messages,
		);
	}

	public function pollForStaff($conversationId, $afterMessageId, $staffId)
	{
		$CI =& get_instance();
		$CI->load->model('ai_conversation_model');
		$conversation = $CI->ai_conversation_model->get_info($conversationId);
		if (!$conversation) {
			return array('ok' => false, 'message' => 'Hội thoại không tồn tại.');
		}

		$messages = $this->fetchMessagesSince($conversationId, $afterMessageId);

		return array(
			'ok' => true,
			'conversation' => $this->formatConversationRow($conversation),
			'messages' => $messages,
		);
	}

	public function getDashboardStats()
	{
		$CI =& get_instance();
		$CI->load->model('ai_conversation_model');

		return array(
			'waiting' => $CI->ai_conversation_model->count_by_status(self::STATUS_WAITING_STAFF),
			'active_staff' => $CI->ai_conversation_model->count_by_status(self::STATUS_STAFF_JOINED),
			'ai_active' => $CI->ai_conversation_model->count_by_status(self::STATUS_AI_ACTIVE),
			'closed' => $CI->ai_conversation_model->count_by_status(self::STATUS_CLOSED),
		);
	}

	public function listConversations($filters = array())
	{
		$CI =& get_instance();
		$CI->load->model('ai_conversation_model');
		return $CI->ai_conversation_model->list_inbox($filters);
	}

	public function takeConversation($conversationId, $staffId, $staffName)
	{
		$CI =& get_instance();
		$CI->load->model('ai_conversation_model');
		$conversation = $CI->ai_conversation_model->get_info($conversationId);
		if (!$conversation) {
			return array('ok' => false, 'message' => 'Hội thoại không tồn tại.');
		}

		$status = $this->normalizeStatus($conversation->status);
		if ($status === self::STATUS_CLOSED) {
			return array('ok' => false, 'message' => 'Hội thoại đã kết thúc.');
		}
		if ($status === self::STATUS_STAFF_JOINED) {
			$ownerId = (int) $CI->ai_conversation_model->field_value($conversation, 'staff_id', 0);
			if ($ownerId > 0 && $ownerId !== (int) $staffId) {
				return array('ok' => false, 'message' => 'Hội thoại đang được nhân viên khác xử lý.');
			}
		}

		$CI->ai_conversation_model->assign_staff($conversationId, (int) $staffId, (string) $staffName);
		$logger = new ConversationLogger();
		$logger->logSystemMessage($conversationId, 'Nhân viên ' . $staffName . ' đã tham gia hỗ trợ.');
		$logger->touch($conversationId, self::STATUS_STAFF_JOINED);

		return array(
			'ok' => true,
			'status' => self::STATUS_STAFF_JOINED,
			'staff_name' => $staffName,
		);
	}

	public function sendStaffMessage($conversationId, $staffId, $staffName, $message)
	{
		$message = trim((string) $message);
		if ($message === '') {
			return array('ok' => false, 'message' => 'Nội dung không được để trống.');
		}

		$CI =& get_instance();
		$CI->load->model('ai_conversation_model');
		$conversation = $CI->ai_conversation_model->get_info($conversationId);
		if (!$conversation) {
			return array('ok' => false, 'message' => 'Hội thoại không tồn tại.');
		}

		$status = $this->normalizeStatus($conversation->status);
		if ($status === self::STATUS_CLOSED) {
			return array('ok' => false, 'message' => 'Hội thoại đã kết thúc.');
		}

		// Tự nhận cuộc trò chuyện khi nhân viên gửi tin (waiting_staff / ai_active).
		if ($status === self::STATUS_WAITING_STAFF || $status === self::STATUS_AI_ACTIVE) {
			$take = $this->takeConversation($conversationId, $staffId, $staffName);
			if (!$take['ok']) {
				return $take;
			}
			$conversation = $CI->ai_conversation_model->get_info($conversationId);
			$status = self::STATUS_STAFF_JOINED;
		}

		if ($status !== self::STATUS_STAFF_JOINED) {
			return array('ok' => false, 'message' => 'Chưa nhận cuộc trò chuyện.');
		}
		$ownerId = (int) $CI->ai_conversation_model->field_value($conversation, 'staff_id', 0);
		if ($CI->ai_conversation_model->has_support_columns() && $ownerId > 0 && $ownerId !== (int) $staffId) {
			return array('ok' => false, 'message' => 'Bạn không phải nhân viên phụ trách.');
		}

		$logger = new ConversationLogger();
		$msgId = $logger->logStaffMessage($conversationId, $message, $staffName);
		$this->markUnreadCustomer($conversationId);
		$logger->touch($conversationId);

		return array(
			'ok' => true,
			'message_id' => (int) $msgId,
			'content' => $message,
			'created' => time(),
		);
	}

	public function endConversation($conversationId, $staffId)
	{
		return $this->closeByStaff($conversationId, $staffId, 'Cuộc trò chuyện đã kết thúc. Cảm ơn bạn đã liên hệ!');
	}

	public function returnToAi($conversationId, $staffId)
	{
		$CI =& get_instance();
		$CI->load->model('ai_conversation_model');
		$conversation = $CI->ai_conversation_model->get_info($conversationId);
		if (!$conversation) {
			return array('ok' => false, 'message' => 'Hội thoại không tồn tại.');
		}
		$ownerId = (int) $CI->ai_conversation_model->field_value($conversation, 'staff_id', 0);
		if ($CI->ai_conversation_model->has_support_columns() && $ownerId > 0 && $ownerId !== (int) $staffId) {
			return array('ok' => false, 'message' => 'Bạn không phải nhân viên phụ trách.');
		}

		$CI->ai_conversation_model->release_staff($conversationId);
		$logger = new ConversationLogger();
		$logger->logSystemMessage($conversationId, 'Trợ lý AI tiếp tục hỗ trợ bạn.');
		$logger->touch($conversationId, self::STATUS_AI_ACTIVE);

		return array('ok' => true, 'status' => self::STATUS_AI_ACTIVE);
	}

	protected function closeByStaff($conversationId, $staffId, $farewell)
	{
		$CI =& get_instance();
		$CI->load->model('ai_conversation_model');
		$conversation = $CI->ai_conversation_model->get_info($conversationId);
		if (!$conversation) {
			return array('ok' => false, 'message' => 'Hội thoại không tồn tại.');
		}
		$ownerId = (int) $CI->ai_conversation_model->field_value($conversation, 'staff_id', 0);
		if ($CI->ai_conversation_model->has_support_columns() && $ownerId > 0 && $ownerId !== (int) $staffId) {
			return array('ok' => false, 'message' => 'Bạn không phải nhân viên phụ trách.');
		}

		$logger = new ConversationLogger();
		$logger->logSystemMessage($conversationId, $farewell);
		$CI->ai_conversation_model->release_staff($conversationId);
		$logger->touch($conversationId, self::STATUS_CLOSED);

		return array('ok' => true, 'status' => self::STATUS_CLOSED);
	}

	public function getConversationForStaff($conversationId)
	{
		$CI =& get_instance();
		$CI->load->model('ai_conversation_model');
		$CI->load->model('ai_message_model');
		$conversation = $CI->ai_conversation_model->get_info($conversationId);
		if (!$conversation) {
			return null;
		}
		$conversation->messages = $CI->ai_message_model->for_conversation($conversationId);
		return $conversation;
	}

	public function normalizeStatus($status)
	{
		$status = (string) $status;
		if ($status === 'open') {
			return self::STATUS_AI_ACTIVE;
		}
		if ($status === 'handed_off') {
			return self::STATUS_WAITING_STAFF;
		}
		return $status;
	}

	public function normalizeSender($sender)
	{
		$sender = (string) $sender;
		if ($sender === 'user') {
			return 'customer';
		}
		return $sender;
	}

	protected function fetchMessagesSince($conversationId, $afterMessageId)
	{
		$CI =& get_instance();
		$CI->load->model('ai_message_model');
		$rows = $CI->ai_message_model->since_id($conversationId, $afterMessageId);
		$messages = array();
		foreach ($rows as $row) {
			$sender = $this->normalizeSender($row->sender);
			if ($sender === 'system') {
				$sender = 'system';
			}
			$meta = json_decode((string) $row->meta, true);
			if (!is_array($meta)) {
				$meta = array();
			}
			$messages[] = array(
				'id' => (int) $row->id,
				'sender_type' => $sender,
				'content' => (string) $row->content,
				'created' => (int) $row->created,
				'staff_name' => isset($meta['staff_name']) ? (string) $meta['staff_name'] : '',
			);
		}
		return $messages;
	}

	protected function authorizeCustomerConversation($conversationId, $userId, $guestToken)
	{
		$CI =& get_instance();
		$CI->load->model('ai_conversation_model');
		$conversation = $CI->ai_conversation_model->get_info($conversationId);
		if (!$conversation) {
			return null;
		}
		$userId = (int) $userId;
		if ($userId > 0 && (int) $conversation->user_id === $userId) {
			return $conversation;
		}
		if ($userId === 0 && $guestToken !== '' && $conversation->guest_token === $guestToken) {
			return $conversation;
		}
		return null;
	}

	protected function formatConversationRow($conversation)
	{
		$CI =& get_instance();
		$CI->load->model('ai_conversation_model');
		$CI->load->model('ai_message_model');
		$preview = $CI->ai_message_model->last_message_preview((int) $conversation->id);
		$m = $CI->ai_conversation_model;

		return array(
			'id' => (int) $conversation->id,
			'status' => $this->normalizeStatus($conversation->status),
			'user_id' => (int) $conversation->user_id,
			'staff_id' => (int) $m->field_value($conversation, 'staff_id', 0),
			'staff_name' => (string) $m->field_value($conversation, 'staff_name', ''),
			'started' => (int) $conversation->started,
			'last_message' => (int) $conversation->last_message,
			'unread_staff' => (int) $m->field_value($conversation, 'unread_staff', 0),
			'preview' => $preview,
		);
	}

	protected function markUnreadStaff($conversationId)
	{
		$CI =& get_instance();
		if (!$CI->db->field_exists('unread_staff', 'ai_conversation')) {
			return;
		}
		$CI->load->model('ai_conversation_model');
		$CI->ai_conversation_model->update((int) $conversationId, array('unread_staff' => 1));
	}

	protected function markUnreadCustomer($conversationId)
	{
		$CI =& get_instance();
		if (!$CI->db->field_exists('unread_customer', 'ai_conversation')) {
			return;
		}
		$CI->load->model('ai_conversation_model');
		$CI->ai_conversation_model->update((int) $conversationId, array('unread_customer' => 1));
	}

	protected function clearUnreadCustomer($conversationId)
	{
		$CI =& get_instance();
		if (!$CI->db->field_exists('unread_customer', 'ai_conversation')) {
			return;
		}
		$CI->load->model('ai_conversation_model');
		$conv = $CI->ai_conversation_model->get_info($conversationId);
		if ($conv && (int) $conv->unread_customer === 1) {
			$CI->ai_conversation_model->update((int) $conversationId, array('unread_customer' => 0));
		}
	}

	protected function respondCustomer($conversationId, $content, $status, $connecting, array $products, $conversation = null)
	{
		$staffName = '';
		if ($conversation && isset($conversation->staff_name)) {
			$staffName = (string) $conversation->staff_name;
		}
		return array(
			'ok' => true,
			'conversation_id' => (int) $conversationId,
			'content' => $content,
			'handoff' => ($status === self::STATUS_WAITING_STAFF),
			'connecting' => (bool) $connecting,
			'status' => $status,
			'staff_name' => $staffName,
			'products' => $products,
		);
	}

	public function historyForCustomer($conversationId, $userId, $guestToken)
	{
		$conversation = $this->authorizeCustomerConversation($conversationId, $userId, $guestToken);
		if (!$conversation) {
			return array('ok' => true, 'messages' => array(), 'status' => self::STATUS_AI_ACTIVE);
		}

		$CI =& get_instance();
		$CI->load->model('ai_message_model');
		$rows = $CI->ai_message_model->for_conversation($conversationId);
		$messages = array();
		foreach ($rows as $row) {
			$sender = $this->normalizeSender($row->sender);
			$meta = json_decode((string) $row->meta, true);
			if (!is_array($meta)) {
				$meta = array();
			}
			$messages[] = array(
				'id' => (int) $row->id,
				'sender_type' => $sender,
				'content' => (string) $row->content,
				'created' => (int) $row->created,
				'staff_name' => isset($meta['staff_name']) ? (string) $meta['staff_name'] : '',
			);
		}

		return array(
			'ok' => true,
			'conversation_id' => (int) $conversation->id,
			'status' => $this->normalizeStatus($conversation->status),
			'staff_name' => (string) $CI->ai_conversation_model->field_value($conversation, 'staff_name', ''),
			'messages' => $messages,
		);
	}
}
