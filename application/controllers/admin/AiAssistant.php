<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Admin cấu hình trợ lý, FAQ, lịch sử hội thoại (quyền ai.manage)
class AiAssistant extends MY_Admin_Controller {

	protected $currentUser;

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('permission');
		$this->currentUser = $this->session->userdata('login');
		if (!admin_can('ai.manage', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có quyền quản lý Trợ lý AI.');
			redirect(admin_url('home'));
		}
		$this->load->model('ai_setting_model');
		$this->load->helper('form');
	}

	public function settings()
	{
		if ($this->input->post('submit')) {
			$this->ai_setting_model->set_many(array(
				'ai_enabled' => $this->input->post('ai_enabled') ? '1' : '0',
				'staff_support_enabled' => $this->input->post('staff_support_enabled') ? '1' : '0',
				'fallback_to_staff' => $this->input->post('fallback_to_staff') ? '1' : '0',
				'welcome_message' => trim((string) $this->input->post('welcome_message')),
				'working_hours_text' => trim((string) $this->input->post('working_hours_text')),
			));
			$this->session->set_flashdata('message', 'Đã lưu cấu hình Trợ lý AI.');
			redirect(admin_url('ai-assistant/settings'));
		}

		$this->data['settings'] = array(
			'ai_enabled' => $this->ai_setting_model->get_bool('ai_enabled', true),
			'staff_support_enabled' => $this->ai_setting_model->get_bool('staff_support_enabled', true),
			'fallback_to_staff' => $this->ai_setting_model->get_bool('fallback_to_staff', true),
			'welcome_message' => $this->ai_setting_model->get('welcome_message', ''),
			'working_hours_text' => $this->ai_setting_model->get('working_hours_text', ''),
		);
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['message_fail'] = $this->session->flashdata('message_fail');
		$this->render_admin('admin/ai_assistant/settings');
	}

	public function faq($action = 'index', $id = 0)
	{
		$this->load->model('ai_faq_model');
		switch ($action) {
			case 'add':
				return $this->_faq_form(0);
			case 'edit':
				return $this->_faq_form((int) $id);
			case 'delete':
				return $this->_faq_delete((int) $id);
			case 'toggle_active':
				return $this->_faq_toggle((int) $id);
			default:
				return $this->_faq_index();
		}
	}

	public function conversations($action = 'index', $id = 0)
	{
		if ($action === 'detail') {
			return $this->_conversation_detail((int) $id);
		}
		return $this->_conversations_index();
	}

	protected function _faq_index()
	{
		$this->data['list'] = $this->ai_faq_model->get_list(array('order' => array('sort_order', 'ASC')));
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['message_fail'] = $this->session->flashdata('message_fail');
		$this->render_admin('admin/ai_assistant/faq_index');
	}

	protected function _faq_form($id)
	{
		$faq = $id > 0 ? $this->ai_faq_model->get_info($id) : null;
		if ($id > 0 && !$faq) {
			$this->session->set_flashdata('message_fail', 'FAQ không tồn tại.');
			redirect(admin_url('ai-assistant/faq'));
		}

		if ($this->input->post('submit')) {
			$data = array(
				'question' => trim((string) $this->input->post('question')),
				'answer' => trim((string) $this->input->post('answer')),
				'keywords' => trim((string) $this->input->post('keywords')),
				'category' => trim((string) $this->input->post('category')),
				'sort_order' => (int) $this->input->post('sort_order'),
				'is_active' => $this->input->post('is_active') ? 1 : 0,
			);

			if ($data['question'] === '' || $data['answer'] === '') {
				$this->session->set_flashdata('message_fail', 'Vui lòng nhập đủ câu hỏi và câu trả lời.');
			} else {
				if ($id > 0) {
					$this->ai_faq_model->update($id, $data);
					$this->session->set_flashdata('message', 'Đã cập nhật FAQ.');
				} else {
					$this->ai_faq_model->create($data);
					$this->session->set_flashdata('message', 'Đã thêm FAQ.');
				}
				redirect(admin_url('ai-assistant/faq'));
			}
		}

		$this->data['faq'] = $faq;
		$this->data['message_fail'] = $this->session->flashdata('message_fail');
		$this->render_admin('admin/ai_assistant/faq_form');
	}

	protected function _faq_delete($id)
	{
		if ($id > 0 && $this->ai_faq_model->delete($id)) {
			$this->session->set_flashdata('message', 'Đã xóa FAQ.');
		}
		redirect(admin_url('ai-assistant/faq'));
	}

	protected function _faq_toggle($id)
	{
		$faq = $this->ai_faq_model->get_info($id);
		if (!$faq) {
			if ($this->input->is_ajax_request()) {
				$this->output->set_content_type('application/json', 'utf-8')
					->set_output(json_encode(array('ok' => false, 'message' => 'FAQ không tồn tại.')));
				return;
			}
			redirect(admin_url('ai-assistant/faq'));
		}

		$newActive = ((int) $faq->is_active === 1) ? 0 : 1;
		$this->ai_faq_model->update($id, array('is_active' => $newActive));

		if ($this->input->is_ajax_request()) {
			$this->output->set_content_type('application/json', 'utf-8')
				->set_output(json_encode(array(
					'ok' => true,
					'is_active' => $newActive,
					'label' => $newActive ? 'Đang bật' : 'Tắt',
				)));
			return;
		}

		redirect(admin_url('ai-assistant/faq'));
	}

	protected function _conversations_index()
	{
		$this->load->model('ai_conversation_model');
		$keyword = trim((string) $this->input->get('q'));
		$this->data['list'] = $this->ai_conversation_model->search($keyword, array('limit' => array(100, 0)));
		$this->data['keyword'] = $keyword;
		$this->render_admin('admin/ai_assistant/conversations_index');
	}

	protected function _conversation_detail($id)
	{
		$this->load->model('ai_conversation_model');
		$this->load->model('ai_message_model');

		$conversation = $this->ai_conversation_model->get_info($id);
		if (!$conversation) {
			$this->session->set_flashdata('message_fail', 'Hội thoại không tồn tại.');
			redirect(admin_url('ai-assistant/conversations'));
		}

		$this->data['conversation'] = $conversation;
		$this->data['messages'] = $this->ai_message_model->for_conversation($id);

		$this->data['customer'] = null;
		if ((int) $conversation->user_id > 0) {
			$this->load->model('user_model');
			$this->data['customer'] = $this->user_model->get_info((int) $conversation->user_id);
		}

		$this->render_admin('admin/ai_assistant/conversation_detail');
	}
}
