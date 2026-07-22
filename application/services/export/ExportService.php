<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/export/ExportContext.php';
require_once APPPATH . 'services/export/ExportFilter.php';
require_once APPPATH . 'services/export/ExcelReportWriter.php';
require_once APPPATH . 'services/export/PdfReportWriter.php';
require_once APPPATH . 'services/export/DashboardPdfWriter.php';

class ExportService
{
	protected static $map = array(
		'products' => 'ProductExport',
		'orders' => 'OrderExport',
		'customers' => 'CustomerExport',
		'revenue' => 'RevenueExport',
		'inventory' => 'InventoryExport',
		'top_products' => 'TopProductsExport',
		'voucher' => 'VoucherExport',
		'dashboard' => 'DashboardExport',
	);

	public function run($format, $module, array $params, $login)
	{
		$this->ensureComposerAutoload();

		$module = strtolower(trim((string) $module));
		$format = strtolower(trim((string) $format));

		if (!isset(self::$map[$module])) {
			show_error('Module export không hợp lệ.', 400);
		}
		if (!in_array($format, array('excel', 'pdf', 'print'), true)) {
			show_error('Định dạng export không hợp lệ.', 400);
		}

		$this->assertPermission($module, $login);

		$filter = new ExportFilter($params);
		$class = self::$map[$module];
		require_once APPPATH . 'services/export/' . $class . '.php';
		require_once APPPATH . 'services/export/ExportReportDefinition.php';
		require_once APPPATH . 'services/export/DashboardReportPayload.php';
		$exporter = new $class($filter);
		$def = $exporter->build();

		$ctx = new ExportContext($module, $format);
		$ctx->exporterName = isset($login->name) ? $login->name : 'Admin';
		$ctx->filterText = $filter->describe($module);
		if ($module === 'dashboard' && $def->dashboardPayload instanceof DashboardReportPayload) {
			$ctx->filterText = $def->dashboardPayload->periodLabel;
		}

		$this->logExport($login, $module, $format, $ctx->filterText);

		$slug = $module . '_' . date('Ymd_His');
		if ($module === 'dashboard') {
			if ($format === 'excel') {
				show_error('Báo cáo Dashboard chỉ hỗ trợ xuất PDF.', 400);
			}
			$writer = new DashboardPdfWriter();
			$inline = ($format === 'print');
			$writer->send($ctx, $def, $slug . '.pdf', $inline);
			return;
		}
		if ($format === 'excel') {
			$writer = new ExcelReportWriter();
			$writer->send($ctx, $def, $slug . '.xlsx');
		}
		if ($format === 'pdf') {
			$writer = new PdfReportWriter();
			$writer->send($ctx, $def, $slug . '.pdf', false);
		}
		$writer = new PdfReportWriter();
		$writer->send($ctx, $def, $slug . '.pdf', true);
	}

	protected function ensureComposerAutoload()
	{
		static $loaded = false;
		if ($loaded) {
			return;
		}
		$file = FCPATH . 'vendor/autoload.php';
		if (!is_file($file)) {
			show_error('Chưa cài thư viện export. Chạy: composer install --no-dev trong thư mục dự án.', 500);
		}
		require_once $file;
		$loaded = true;
	}

	protected function assertPermission($module, $login)
	{
		$CI = get_instance();
		$CI->load->helper('export');
		if (!admin_export_can($login)) {
			show_error('Chỉ tài khoản Admin mới được xuất báo cáo.', 403);
		}
		if (!admin_export_module_allowed($module, $login)) {
			show_error('Bạn không có quyền xuất module này.', 403);
		}
	}

	protected function logExport($login, $module, $format, $filterText)
	{
		$line = sprintf(
			"[%s] user_id=%s module=%s format=%s filter=%s\n",
			date('Y-m-d H:i:s'),
			isset($login->id) ? $login->id : '0',
			$module,
			$format,
			str_replace(array("\n", "\r"), ' ', $filterText)
		);
		$file = APPPATH . 'logs/export-' . date('Y-m-d') . '.log';
		@file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
	}
}
