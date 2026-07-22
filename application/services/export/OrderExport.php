<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/export/AbstractModuleExport.php';
require_once APPPATH . 'helpers/admin_helper.php';

class OrderExport extends AbstractModuleExport
{
	public function build()
	{
		$CI = $this->ci();
		$CI->load->model('transaction_model');

		$input = array('order' => array('id', 'DESC'));
		$limit = $this->filter->paginationLimit(10);
		if ($limit !== null) {
			$input['limit'] = array($limit[0], $limit[1]);
		}

		$list = $CI->transaction_model->get_list($input);
		$def = new ExportReportDefinition('Báo cáo đơn hàng');
		$def->headers = array('Mã', 'Khách hàng', 'Email', 'SĐT', 'Trạng thái', 'Thành tiền (₫)', 'Ngày đặt');
		$def->moneyColumns = array(5);
		$def->dateColumns = array(6);

		$sum = 0;
		foreach ($list as $t) {
			$amount = (int) $t->amount;
			$sum += $amount;
			$def->rows[] = array(
				(int) $t->id,
				$t->user_name,
				$t->user_email,
				$t->user_phone,
				admin_order_status_text($t->status),
				$amount,
				(int) $t->created,
			);
		}
		$def->totals = array(
			'Tổng đơn' => count($def->rows),
			'Tổng giá trị đơn' => $sum,
		);
		return $def;
	}
}
