<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Export extends MY_Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('export');
	}

	public function excel($module = '')
	{
		$this->_dispatch('excel', $module);
	}

	public function pdf($module = '')
	{
		$this->_dispatch('pdf', $module);
	}

	public function print_report($module = '')
	{
		$this->_dispatch('print', $module);
	}

	protected function _dispatch($format, $module)
	{
		require_once APPPATH . 'services/export/ExportService.php';
		$params = $this->input->get();
		$login = $this->session->userdata('login');
		$service = new ExportService();
		$service->run($format, $module, is_array($params) ? $params : array(), $login);
	}
}
