<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/export/AbstractModuleExport.php';
require_once APPPATH . 'services/export/ExportChartRenderer.php';
require_once APPPATH . 'services/export/DashboardReportPayload.php';

class DashboardExport extends AbstractModuleExport
{
	public function build()
	{
		$CI = $this->ci();
		$CI->load->helper(array('admin_dashboard', 'permission'));

		$period = $this->filter->get('period', '30d');
		$from = $this->filter->get('from', '');
		$to = $this->filter->get('to', '');

		$dash = admin_dashboard_get_data(array(
			'period' => $period,
			'from' => $from,
			'to' => $to,
		));

		$payload = new DashboardReportPayload();
		$range = $dash['range'];
		$payload->periodLabel = $this->formatReportPeriod($range['start'], $range['end']);
		$payload->storeName = $this->resolveStoreName($CI);

		$kpiKeys = array(
			'orders_today' => 'Tổng đơn hàng',
			'revenue_today' => 'Doanh thu',
			'pending' => 'Đơn chờ xử lý',
			'shipping' => 'Đơn đang giao',
			'completed' => 'Đơn hoàn thành',
			'cancelled' => 'Đơn đã hủy',
			'new_users' => 'Khách hàng mới',
		);
		$byKey = array();
		foreach ($dash['kpis'] as $kpi) {
			$byKey[$kpi['key']] = $kpi;
		}
		foreach ($kpiKeys as $key => $label) {
			if (!isset($byKey[$key])) {
				continue;
			}
			$k = $byKey[$key];
			$payload->kpis[$label] = admin_dashboard_format_kpi($k['value'], $k['format']);
		}

		$charts = $dash['charts'];
		$renderer = new ExportChartRenderer();
		$daySpan = (int) floor(($range['end'] - $range['start']) / 86400) + 1;
		$revenueTitle = $this->revenueChartTitle($daySpan);
		if (!empty($charts['labels']) && !empty($charts['revenue'])) {
			$payload->revenueChartPath = $renderer->revenueChart(
				$revenueTitle,
				$payload->periodLabel,
				$charts['labels'],
				$charts['revenue']
			);
		}
		if (!empty($charts['status_pie']['labels']) && !empty($charts['status_pie']['values'])) {
			$payload->statusChartPath = $renderer->statusDistributionChart(
				'Trạng thái đơn hàng',
				$payload->periodLabel,
				$charts['status_pie']['labels'],
				$charts['status_pie']['values']
			);
		}

		$top = admin_dashboard_top_products($dash['range'], 10);
		$stt = 0;
		foreach ($top as $p) {
			$stt++;
			$payload->topProducts[] = array(
				'stt' => $stt,
				'id' => (int) $p->id,
				'name' => $p->name,
				'sold' => (int) $p->total_sold,
				'revenue' => (int) $p->total_amount,
			);
		}
		$payload->topProductsChartPath = $renderer->topProductsBarChart(
			'Top 10 sản phẩm bán chạy',
			$payload->periodLabel,
			$payload->topProducts
		);

		$threshold = isset($dash['low_stock_threshold']) ? (int) $dash['low_stock_threshold'] : 10;
		$low = admin_dashboard_low_stock($threshold, 15);
		foreach ($low as $p) {
			$qty = (int) $p->quantity;
			$payload->lowStock[] = array(
				'id' => (int) $p->id,
				'name' => $p->name,
				'qty' => $qty,
				'price' => (int) $p->price,
				'status' => $this->stockStatusLabel($qty),
			);
		}

		$perm = array(
			'orders' => true,
			'inventory' => true,
			'products' => true,
			'users' => true,
			'voucher' => true,
			'posts' => true,
			'slider' => true,
			'staff' => true,
			'revenue' => true,
			'catalog' => true,
		);
		$groups = admin_dashboard_notification_groups($dash, $perm, null);
		$noteGroups = array(
			'orders' => 'Đơn hàng',
			'inventory' => 'Kho hàng',
			'customers' => 'Khách hàng',
			'promotions' => 'Khuyến mãi',
			'system' => 'Hệ thống',
		);
		foreach ($noteGroups as $name) {
			$payload->groupedSystemNotes[$name] = array();
		}
		foreach ($groups as $group) {
			$id = isset($group['id']) ? (string) $group['id'] : '';
			$title = isset($noteGroups[$id]) ? $noteGroups[$id] : (isset($group['title']) ? $group['title'] : '');
			if (!empty($group['lines']) && is_array($group['lines'])) {
				foreach ($group['lines'] as $line) {
					$text = is_string($line) ? $line : '';
					if ($text !== '') {
						$payload->systemNotes[] = ($title !== '' ? $title . ': ' : '') . $text;
						if ($title !== '' && !isset($payload->groupedSystemNotes[$title])) {
							$payload->groupedSystemNotes[$title] = array();
						}
						if ($title !== '' && count($payload->groupedSystemNotes[$title]) < 3) {
							$payload->groupedSystemNotes[$title][] = $text;
						}
					}
				}
			}
		}
		if (empty($payload->systemNotes)) {
			$payload->systemNotes[] = 'Không có ghi chú hệ thống cần xử lý trong kỳ.';
			$payload->groupedSystemNotes['Hệ thống'][] = 'Không có ghi chú hệ thống cần xử lý trong kỳ.';
		}

		$def = new ExportReportDefinition('BÁO CÁO TỔNG QUAN');
		$def->reportType = 'dashboard';
		$def->dashboardPayload = $payload;
		return $def;
	}

	protected function formatReportPeriod($start, $end)
	{
		$start = (int) $start;
		$end = (int) $end;
		return date('j/n/Y', $start) . ' - ' . date('j/n/Y', $end);
	}

	protected function revenueChartTitle($daySpan)
	{
		if ($daySpan <= 45) {
			return 'Doanh thu theo ngày';
		}
		if ($daySpan <= 120) {
			return 'Doanh thu theo tuần';
		}
		return 'Doanh thu theo kỳ';
	}

	protected function stockStatusLabel($qty)
	{
		$qty = (int) $qty;
		if ($qty <= 5) {
			return 'Rất thấp';
		}
		if ($qty <= 10) {
			return 'Sắp hết';
		}
		return 'Bình thường';
	}

	protected function resolveStoreName($CI)
	{
		if (!$CI->db->table_exists('stores')) {
			return '';
		}
		$row = $CI->db->select('name')->from('stores')->order_by('id', 'ASC')->limit(1)->get()->row();
		if (!$row || trim((string) $row->name) === '') {
			return '';
		}
		$name = trim((string) $row->name);
		if (strcasecmp($name, 'webshop') === 0) {
			return '';
		}
		return $name;
	}
}
