<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Realtime sync contract; polling now, WebSocket later without changing business logic.
interface ChatSyncInterface {

	public function pollCustomer($conversationId, $afterMessageId, $userId, $guestToken);

	public function pollStaff($conversationId, $afterMessageId, $staffId);
}
