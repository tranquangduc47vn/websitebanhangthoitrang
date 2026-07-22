<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/support/ChatSyncInterface.php';
require_once APPPATH . 'services/support/SupportChatService.php';

class PollingChatSyncService implements ChatSyncInterface {

	protected $support;

	public function __construct()
	{
		$this->support = new SupportChatService();
	}

	public function pollCustomer($conversationId, $afterMessageId, $userId, $guestToken)
	{
		return $this->support->pollForCustomer($conversationId, $afterMessageId, $userId, $guestToken);
	}

	public function pollStaff($conversationId, $afterMessageId, $staffId)
	{
		return $this->support->pollForStaff($conversationId, $afterMessageId, $staffId);
	}
}
