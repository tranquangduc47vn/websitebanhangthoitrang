<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('ai_widget_settings')) {
	function ai_widget_settings()
	{
		$CI =& get_instance();
		$defaults = array(
			'ai_enabled' => false,
			'staff_support_enabled' => true,
			'welcome_message' => 'Xin chào 👋',
		);

		if (!$CI->db->table_exists('ai_setting')) {
			return $defaults;
		}

		$CI->load->model('ai_setting_model');
		return array(
			'ai_enabled' => $CI->ai_setting_model->get_bool('ai_enabled', false),
			'staff_support_enabled' => $CI->ai_setting_model->get_bool('staff_support_enabled', true),
			'welcome_message' => $CI->ai_setting_model->get('welcome_message', $defaults['welcome_message']),
		);
	}
}

if (!function_exists('ai_render_widget')) {
	function ai_render_widget()
	{
		$CI =& get_instance();
		$settings = ai_widget_settings();

		if (!$settings['ai_enabled'] && !$settings['staff_support_enabled']) {
			return;
		}

		$CI->config->load('ai', true);

		$data = array(
			'ai_enabled' => $settings['ai_enabled'],
			'staff_support_enabled' => $settings['staff_support_enabled'],
			'welcome_message' => $settings['welcome_message'],
			'welcome_intro' => (string) $CI->config->item('ai_welcome_intro', 'ai'),
			'quick_replies' => (array) $CI->config->item('ai_quick_replies', 'ai'),
			'poll_interval_ms' => (int) $CI->config->item('support_poll_interval_ms', 'ai'),
		);

		$CI->load->view('site/chat/widget', $data);
	}
}

if (!function_exists('support_waiting_count')) {
	function support_waiting_count()
	{
		$CI =& get_instance();
		if (!$CI->db->table_exists('ai_conversation')) {
			return 0;
		}
		$CI->load->model('ai_conversation_model');
		return $CI->ai_conversation_model->count_waiting();
	}
}
