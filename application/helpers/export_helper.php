<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('admin_export_can')) {
	// Chỉ ROLE_ADMIN được export.
	function admin_export_can($login = null)
	{
		if ($login === null) {
			$login = get_instance()->session->userdata('login');
		}
		if (!$login || !isset($login->level)) {
			return false;
		}
		return (int) $login->level === ROLE_ADMIN;
	}
}

if (!function_exists('admin_export_module_allowed')) {
	function admin_export_module_allowed($module, $login = null)
	{
		$CI = get_instance();
		$CI->load->helper('permission');
		$module = strtolower((string) $module);
		switch ($module) {
			case 'products':
				return admin_can('product.manage', $login);
			case 'orders':
				return admin_can('order.manage', $login);
			case 'customers':
				return admin_sidebar_allowed('users', $login);
			case 'revenue':
			case 'top_products':
				return admin_can('order.manage', $login);
			case 'inventory':
				return admin_can('inventory.view', $login);
			case 'voucher':
				return admin_sidebar_allowed('voucher', $login);
			case 'dashboard':
				return admin_can('panel.home', $login);
			default:
				return false;
		}
	}
}

if (!function_exists('admin_export_query_string')) {
	function admin_export_query_string(array $extra = array())
	{
		$CI = get_instance();
		$get = $CI->input->get();
		unset($get['export'], $get['scope']);
		$get = array_merge($get, $extra);
		return http_build_query($get);
	}
}

if (!function_exists('admin_export_pagination_params')) {
	function admin_export_pagination_params($module)
	{
		$CI = get_instance();
		$perMap = array(
			'products' => 10,
			'orders' => 10,
			'inventory' => 15,
		);
		$module = strtolower((string) $module);
		$extra = array();
		if (isset($perMap[$module])) {
			$extra['per_page'] = $perMap[$module];
			$extra['offset'] = max(0, (int) $CI->uri->segment(4));
		}
		return $extra;
	}
}

if (!function_exists('admin_export_toolbar')) {
	function admin_export_toolbar($module, array $extra = array())
	{
		$CI = get_instance();
		$login = $CI->session->userdata('login');
		if (!admin_export_can($login) || !admin_export_module_allowed($module, $login)) {
			return;
		}
		$extra = array_merge(admin_export_pagination_params($module), $extra);
		$CI->load->view('admin/partials/export_toolbar', array(
			'export_module' => $module,
			'export_query' => admin_export_query_string($extra),
		));
	}
}
