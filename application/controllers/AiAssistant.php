<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// AJAX widget hỗ trợ khách
class AiAssistant extends MY_Frontend_Controller {

	public function send()
	{
		$this->output->set_content_type('application/json', 'utf-8');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(array('ok' => false, 'message' => 'Yêu cầu không hợp lệ.')));
			return;
		}

		require_once APPPATH . 'services/support/SupportChatService.php';

		$message = (string) $this->input->post('message');
		$conversationId = (int) $this->input->post('conversation_id');

		$userLogin = $this->session->userdata('user');
		$userId = ($userLogin && isset($userLogin->id)) ? (int) $userLogin->id : 0;
		$guestToken = $userId > 0 ? '' : $this->_guest_token();

		$service = new SupportChatService();
		$result = $service->handleCustomerMessage($message, $conversationId, $userId, $guestToken);

		$this->output->set_output(json_encode($result, JSON_UNESCAPED_UNICODE));
	}

	public function history()
	{
		$this->output->set_content_type('application/json', 'utf-8');

		$conversationId = (int) $this->input->get('conversation_id');
		if ($conversationId <= 0) {
			$this->output->set_output(json_encode(array('ok' => true, 'messages' => array())));
			return;
		}

		require_once APPPATH . 'services/support/SupportChatService.php';

		$userLogin = $this->session->userdata('user');
		$userId = ($userLogin && isset($userLogin->id)) ? (int) $userLogin->id : 0;
		$guestToken = $this->session->userdata('ai_guest_token');

		$service = new SupportChatService();
		$result = $service->historyForCustomer($conversationId, $userId, (string) $guestToken);

		$this->output->set_output(json_encode($result, JSON_UNESCAPED_UNICODE));
	}

	// Polling tin mới (ChatSyncInterface)
	public function poll()
	{
		$this->output->set_content_type('application/json', 'utf-8');

		require_once APPPATH . 'services/support/PollingChatSyncService.php';

		$conversationId = (int) $this->input->get('conversation_id');
		$afterId = (int) $this->input->get('after_id');

		$userLogin = $this->session->userdata('user');
		$userId = ($userLogin && isset($userLogin->id)) ? (int) $userLogin->id : 0;
		$guestToken = $this->session->userdata('ai_guest_token');

		$sync = new PollingChatSyncService();
		$result = $sync->pollCustomer($conversationId, $afterId, $userId, (string) $guestToken);

		$this->output->set_output(json_encode($result, JSON_UNESCAPED_UNICODE));
	}

	protected function _guest_token()
	{
		$token = $this->session->userdata('ai_guest_token');
		if (!$token) {
			$token = function_exists('random_bytes') ? bin2hex(random_bytes(16)) : md5(uniqid('ai', true));
			$this->session->set_userdata('ai_guest_token', $token);
		}
		return $token;
	}
}
