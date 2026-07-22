<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/export/AbstractModuleExport.php';

class VoucherExport extends AbstractModuleExport
{
	public function build()
	{
		$CI = $this->ci();
		if (!$CI->db->table_exists('voucher')) {
			$def = new ExportReportDefinition('Báo cáo voucher');
			$def->headers = array('Thông báo');
			$def->rows[] = array('Chưa cài bảng voucher.');
			return $def;
		}

		$now = time();
		$list = $CI->db->order_by('id', 'DESC')->get('voucher')->result();

		$def = new ExportReportDefinition('Báo cáo voucher');
		$def->headers = array('Mã', 'Tên', 'Code', 'Giảm', 'Hết hạn', 'Đã dùng', 'Trạng thái');
		$def->dateColumns = array(4);

		$active = 0;
		foreach ($list as $v) {
			if ((int) $v->is_active === 1) {
				$active++;
			}
			$disc = ((string) $v->discount_type === 'percent')
				? (int) $v->discount_value . '%'
				: number_format((int) $v->discount_value, 0, ',', '.') . ' ₫';
			$exp = (int) $v->valid_to;
			$def->rows[] = array(
				(int) $v->id,
				$v->name,
				$v->code,
				$disc,
				$exp > 0 ? $exp : '',
				(int) $v->used_count . ' / ' . (int) $v->usage_limit,
				(int) $v->is_active === 1 ? 'Đang bật' : 'Tắt',
			);
		}
		$def->totals = array(
			'Tổng voucher' => count($def->rows),
			'Đang active' => $active,
		);
		return $def;
	}
}
