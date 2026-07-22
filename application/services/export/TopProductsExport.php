<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/export/AbstractModuleExport.php';
require_once APPPATH . 'services/export/ExportReportTime.php';

class TopProductsExport extends AbstractModuleExport
{
	public function build()
	{
		$CI = $this->ci();
		$range = ExportReportTime::resolve($this->filter);
		$where = ExportReportTime::sqlTransactionWhere(
			$range ? $range[0] : 0,
			$range ? $range[1] : 0,
			't'
		);

		$sql = "
			SELECT p.id, p.name,
				COALESCE(SUM(o.qty),0) AS sold,
				COALESCE(SUM(o.amount),0) AS revenue
			FROM product p
			INNER JOIN `order` o ON o.product_id = p.id
			INNER JOIN transaction t ON t.id = o.transaction_id AND $where
			GROUP BY p.id, p.name
			HAVING sold > 0
			ORDER BY sold DESC
			LIMIT 50
		";
		$list = $CI->db->query($sql)->result();

		$def = new ExportReportDefinition('Top sản phẩm bán chạy');
		$def->headers = array('Mã', 'Sản phẩm', 'Số lượng bán', 'Doanh thu (₫)');
		$def->moneyColumns = array(3);

		$sumQty = 0;
		$sumRev = 0;
		foreach ($list as $p) {
			$sold = (int) $p->sold;
			$rev = (int) $p->revenue;
			$sumQty += $sold;
			$sumRev += $rev;
			$def->rows[] = array((int) $p->id, $p->name, $sold, $rev);
		}
		$def->totals = array(
			'Tổng lượng bán' => $sumQty,
			'Tổng doanh thu' => $sumRev,
		);
		return $def;
	}
}
