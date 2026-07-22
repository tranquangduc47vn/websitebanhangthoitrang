<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ConversationLogger {

	public function resolveConversation($userId, $guestToken, $conversationId)
	{
		$CI = get_instance();
		$CI->load->model('ai_conversation_model');
		return $CI->ai_conversation_model->find_or_create($userId, $guestToken, $conversationId);
	}

	public function logUserMessage($conversationId, $content)
	{
		return $this->logCustomerMessage($conversationId, $content);
	}

	public function logCustomerMessage($conversationId, $content)
	{
		$CI = get_instance();
		$CI->load->model('ai_message_model');
		return $CI->ai_message_model->log($conversationId, 'customer', $content);
	}

	public function logAiMessage($conversationId, $content, array $meta = array())
	{
		$CI = get_instance();
		$CI->load->model('ai_message_model');
		return $CI->ai_message_model->log($conversationId, 'ai', $content, $meta);
	}

	public function logStaffMessage($conversationId, $content, $staffName = '')
	{
		$CI = get_instance();
		$CI->load->model('ai_message_model');
		return $CI->ai_message_model->log($conversationId, 'staff', $content, array(
			'staff_name' => (string) $staffName,
		));
	}

	public function logSystemMessage($conversationId, $content, array $meta = array())
	{
		$CI = get_instance();
		$CI->load->model('ai_message_model');
		return $CI->ai_message_model->log($conversationId, 'system', $content, $meta);
	}

	public function touch($conversationId, $status = null)
	{
		$CI = get_instance();
		$CI->load->model('ai_conversation_model');
		return $CI->ai_conversation_model->touch($conversationId, $status);
	}

	public function history($conversationId, $limit)
	{
		$CI = get_instance();
		$CI->load->model('ai_message_model');
		return $CI->ai_message_model->for_conversation($conversationId, $limit);
	}
}
