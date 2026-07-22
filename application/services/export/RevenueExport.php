<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/export/AbstractModuleExport.php';
require_once APPPATH . 'services/export/ExportReportTime.php';

class RevenueExport extends AbstractModuleExport
{
	public function build()
	{
		$CI = $this->ci();
		$range = ExportReportTime::resolve($this->filter);
		$where = ExportReportTime::sqlTransactionWhere(
			$range ? $range[0] : 0,
			$range ? $range[1] : 0
		);

		$row = $CI->db->query("SELECT COUNT(id) AS c, COALESCE(SUM(amount),0) AS s FROM transaction WHERE $where")->row();
		$orders = $row ? (int) $row->c : 0;
		$revenue = $row ? (int) $row->s : 0;

		$sql = "
			SELECT p.id, p.name, p.quantity,
			(SELECT COALESCE(SUM(o.qty),0) FROM `order` o JOIN transaction t ON o.transaction_id=t.id WHERE o.product_id=p.id AND " . str_replace(array('status', 'created'), array('t.status', 't.created'), $where) . ") AS sold
			FROM product p ORDER BY sold DESC
		";
		$products = $CI->db->query($sql)->result();

		$def = new ExportReportDefinition('Báo cáo doanh thu');
		$def->headers = array('Chỉ tiêu', 'Giá trị');
		$def->rows[] = array('Đơn hoàn thành', $orders);
		$def->rows[] = array('Doanh thu (₫)', $revenue);
		$def->moneyColumns = array(1);

		$def->totals = array(
			'Tổng doanh thu' => $revenue,
			'Tổng đơn thành công' => $orders,
		);

		foreach ($products as $p) {
			if ((int) $p->sold <= 0) {
				continue;
			}
			$def->rows[] = array('SP: ' . $p->name . ' — đã bán', (int) $p->sold);
		}
		return $def;
	}
}
