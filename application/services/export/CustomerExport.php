<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/export/AbstractModuleExport.php';

class CustomerExport extends AbstractModuleExport
{
	public function build()
	{
		$CI = $this->ci();
		$CI->load->model('user_model');

		$input = array('order' => array('id', 'DESC'));
		$limit = $this->filter->paginationLimit(10);
		if ($limit !== null) {
			$input['limit'] = array($limit[0], $limit[1]);
		}

		$list = $CI->user_model->get_list($input);
		$def = new ExportReportDefinition('Báo cáo khách hàng');
		$def->headers = array('Mã', 'Họ tên', 'Email', 'Điện thoại', 'Ngày đăng ký');
		$def->dateColumns = array(4);

		foreach ($list as $u) {
			$def->rows[] = array(
				(int) $u->id,
				$u->name,
				$u->email,
				$u->phone,
				$u->created,
			);
		}
		$def->totals = array('Tổng khách hàng' => count($def->rows));
		return $def;
	}
}
