<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Admin chat khách hàng (quyền order.chat)
class SupportChat extends MY_Admin_Controller {

	protected $currentUser;

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('permission');
		$this->currentUser = $this->session->userdata('login');
		if (!admin_can('order.chat', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có quyền hỗ trợ khách hàng.');
			redirect(admin_url('home'));
		}
		$this->load->model('ai_conversation_model');
		$this->load->model('ai_message_model');
	}

	public function index()
	{
		require_once APPPATH . 'services/support/SupportChatService.php';
		$service = new SupportChatService();

		$status = trim((string) $this->input->get('status'));
		if ($status === '') {
			$status = 'all';
		}

		$this->data['stats'] = $service->getDashboardStats();
		$this->data['list'] = $service->listConversations(array(
			'status' => $status,
			'q' => trim((string) $this->input->get('q')),
			'unread_only' => $this->input->get('unread') ? true : false,
			'limit' => 100,
		));
		$this->data['filter_status'] = $status;
		$this->data['keyword'] = trim((string) $this->input->get('q'));
		$this->data['unread_only'] = $this->input->get('unread') ? true : false;
		$this->data['waiting_count'] = $this->ai_conversation_model->count_waiting();
		$this->data['schema_ready'] = $this->ai_conversation_model->has_support_columns();

		$this->render_admin('admin/support_chat/index');
	}

	public function chat($id = 0)
	{
		$id = (int) $id;
		require_once APPPATH . 'services/support/SupportChatService.php';
		$service = new SupportChatService();
		$conversation = $service->getConversationForStaff($id);

		if (!$conversation) {
			$this->session->set_flashdata('message_fail', 'Hội thoại không tồn tại.');
			redirect(admin_url('support-chat'));
		}

		$this->data['conversation'] = $conversation;
		$this->data['messages'] = $conversation->messages;
		$this->data['staff_id'] = (int) $this->currentUser->id;
		$this->data['staff_name'] = isset($this->currentUser->name) ? (string) $this->currentUser->name : 'Nhân viên';

		$this->data['customer'] = null;
		if ((int) $conversation->user_id > 0) {
			$this->load->model('user_model');
			$this->data['customer'] = $this->user_model->get_info((int) $conversation->user_id);
		}

		$this->render_admin('admin/support_chat/chat');
	}

	public function take($id = 0)
	{
		$this->output->set_content_type('application/json', 'utf-8');
		require_once APPPATH . 'services/support/SupportChatService.php';
		$service = new SupportChatService();
		$result = $service->takeConversation(
			(int) $id,
			(int) $this->currentUser->id,
			isset($this->currentUser->name) ? (string) $this->currentUser->name : 'Nhân viên'
		);
		$this->output->set_output(json_encode($result, JSON_UNESCAPED_UNICODE));
	}

	public function send()
	{
		$this->output->set_content_type('application/json', 'utf-8');
		if (!$this->input->post()) {
			$this->output->set_output(json_encode(array('ok' => false, 'message' => 'Yêu cầu không hợp lệ.')));
			return;
		}

		require_once APPPATH . 'services/support/SupportChatService.php';
		$service = new SupportChatService();
		$result = $service->sendStaffMessage(
			(int) $this->input->post('conversation_id'),
			(int) $this->currentUser->id,
			isset($this->currentUser->name) ? (string) $this->currentUser->name : 'Nhân viên',
			(string) $this->input->post('message')
		);
		$this->output->set_output(json_encode($result, JSON_UNESCAPED_UNICODE));
	}

	public function end($id = 0)
	{
		$this->output->set_content_type('application/json', 'utf-8');
		require_once APPPATH . 'services/support/SupportChatService.php';
		$service = new SupportChatService();
		$result = $service->endConversation((int) $id, (int) $this->currentUser->id);
		$this->output->set_output(json_encode($result, JSON_UNESCAPED_UNICODE));
	}

	public function return_ai($id = 0)
	{
		$this->output->set_content_type('application/json', 'utf-8');
		require_once APPPATH . 'services/support/SupportChatService.php';
		$service = new SupportChatService();
		$result = $service->returnToAi((int) $id, (int) $this->currentUser->id);
		$this->output->set_output(json_encode($result, JSON_UNESCAPED_UNICODE));
	}

	public function poll()
	{
		$this->output->set_content_type('application/json', 'utf-8');
		require_once APPPATH . 'services/support/PollingChatSyncService.php';
		$sync = new PollingChatSyncService();
		$result = $sync->pollStaff(
			(int) $this->input->get('conversation_id'),
			(int) $this->input->get('after_id'),
			(int) $this->currentUser->id
		);
		$this->output->set_output(json_encode($result, JSON_UNESCAPED_UNICODE));
	}

	public function waiting_count()
	{
		$this->output->set_content_type('application/json', 'utf-8');
		$this->output->set_output(json_encode(array(
			'ok' => true,
			'count' => $this->ai_conversation_model->count_waiting(),
		), JSON_UNESCAPED_UNICODE));
	}
}
