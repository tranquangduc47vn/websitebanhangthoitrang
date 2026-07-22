<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/export/ExportReportPdf.php';

class DashboardPdfWriter
{
	const LEFT = 15;
	const RIGHT = 195;
	const CONTENT_W = 180;

	public function send(ExportContext $ctx, ExportReportDefinition $def, $filename, $inline = false)
	{
		require_once APPPATH . 'services/export/DashboardReportPayload.php';
		$payload = $def->dashboardPayload;
		if (!$payload instanceof DashboardReportPayload) {
			show_error('Dữ liệu báo cáo Dashboard không hợp lệ.', 500);
		}

		$pdf = new ExportReportPdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetCreator('Hệ thống quản trị');
		$pdf->SetAuthor($ctx->exporterName);
		$pdf->SetTitle($def->title);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(true);
		$pdf->SetMargins(self::LEFT, 14, 15);
		$pdf->SetAutoPageBreak(true, 19);
		$pdf->AddPage();
		$pdf->SetFont('dejavusans', '', 10);

		$this->renderHeader($pdf, $ctx, $def, $payload);
		$this->sectionTitle($pdf, '1. Tổng quan KPI');
		$pdf->writeHTML($this->buildKpiCardsHtml($payload), true, false, true, false, '');
		$pdf->Ln(2);

		$this->renderImageSection($pdf, '2. Biểu đồ doanh thu', $payload->revenueChartPath, 180, 68);
		$this->renderImageSection($pdf, '3. Trạng thái đơn hàng', $payload->statusChartPath, 180, 60);

		$this->sectionTitle($pdf, '4. Top 10 sản phẩm bán chạy');
		$pdf->writeHTML($this->buildTopProductsHtml($payload), true, false, true, false, '');
		$pdf->Ln(2);
		$this->renderPlainImage($pdf, $payload->topProductsChartPath, 180, 62);

		$this->sectionTitle($pdf, '5. Sản phẩm sắp hết hàng');
		$pdf->writeHTML($this->buildLowStockHtml($payload), true, false, true, false, '');
		$pdf->Ln(2);

		$this->sectionTitle($pdf, '6. Ghi chú hệ thống');
		$pdf->writeHTML($this->buildGroupedNotesHtml($payload), true, false, true, false, '');

		$this->cleanupImages($payload);
		$pdf->Output($filename, $inline ? 'I' : 'D');
		exit;
	}

	protected function renderHeader($pdf, ExportContext $ctx, ExportReportDefinition $def, DashboardReportPayload $payload)
	{
		$pdf->SetTextColor(20, 20, 20);
		$pdf->SetFont('dejavusans', 'B', 19);
		$pdf->Cell(0, 10, $def->title, 0, 1, 'L');

		$pdf->SetDrawColor(150, 150, 150);
		$pdf->Line(self::LEFT, $pdf->GetY(), self::RIGHT, $pdf->GetY());
		$pdf->Ln(4);

		$store = $payload->storeName !== '' ? $payload->storeName : 'N/A';
		$html = '<table cellpadding="3" cellspacing="0" style="width:100%;font-size:10pt;color:#333333;">'
			. '<tr>'
			. '<td style="width:18%;font-weight:bold;">Cửa hàng</td><td style="width:34%;">' . $this->esc($store) . '</td>'
			. '<td style="width:22%;font-weight:bold;">Khoảng thời gian</td><td style="width:26%;text-align:right;">' . $this->esc($payload->periodLabel) . '</td>'
			. '</tr><tr>'
			. '<td style="font-weight:bold;">Người xuất</td><td>' . $this->esc($ctx->exporterName) . '</td>'
			. '<td style="font-weight:bold;">Thời gian xuất</td><td style="text-align:right;">' . $this->esc(date('d/m/Y H:i', $ctx->exportedAt)) . '</td>'
			. '</tr></table>';
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->Ln(3);
	}

	protected function sectionTitle($pdf, $title)
	{
		if ($pdf->GetY() > 248) {
			$pdf->AddPage();
		}
		$pdf->SetFont('dejavusans', 'B', 13);
		$pdf->SetTextColor(25, 25, 25);
		$pdf->Cell(0, 7, $title, 0, 1, 'L');
		$pdf->SetDrawColor(210, 210, 210);
		$pdf->Line(self::LEFT, $pdf->GetY(), self::RIGHT, $pdf->GetY());
		$pdf->Ln(3);
		$pdf->SetFont('dejavusans', '', 10);
		$pdf->SetTextColor(0, 0, 0);
	}

	protected function renderImageSection($pdf, $title, $path, $width, $height)
	{
		if ($path === '' || !is_file($path)) {
			return;
		}
		$this->sectionTitle($pdf, $title);
		$this->renderPlainImage($pdf, $path, $width, $height);
	}

	protected function renderPlainImage($pdf, $path, $width, $height)
	{
		if ($path === '' || !is_file($path)) {
			return;
		}
		if ($pdf->GetY() > (260 - $height)) {
			$pdf->AddPage();
		}
		$y = $pdf->GetY();
		$pdf->Image($path, self::LEFT, $y, $width, 0, 'PNG');
		$pdf->SetY($y + $height);
		$pdf->Ln(2);
	}

	protected function buildKpiCardsHtml(DashboardReportPayload $payload)
	{
		$items = array();
		foreach ($payload->kpis as $label => $value) {
			$unit = $this->inferUnit($label, $value);
			$items[] = array('label' => $label, 'value' => $this->stripUnit($value), 'unit' => $unit);
		}
		$out = '<table cellpadding="5" cellspacing="5" style="width:100%;font-size:9.5pt;border-collapse:separate;">';
		for ($i = 0; $i < count($items); $i += 4) {
			$out .= '<tr>';
			for ($j = 0; $j < 4; $j++) {
				$item = isset($items[$i + $j]) ? $items[$i + $j] : null;
				if ($item === null) {
					$out .= '<td style="width:25%;"></td>';
					continue;
				}
				$out .= '<td style="width:25%;border:1px solid #d9d9d9;background-color:#f7f7f7;">'
					. '<div style="font-size:8.5pt;color:#555555;">' . $this->esc($item['label']) . '</div>'
					. '<div style="font-size:15pt;font-weight:bold;color:#222222;line-height:20px;">' . $this->esc($item['value']) . '</div>'
					. '<div style="font-size:8pt;color:#666666;">' . $this->esc($item['unit']) . '</div>'
					. '</td>';
			}
			$out .= '</tr>';
		}
		$out .= '</table>';
		return $out;
	}

	protected function buildTopProductsHtml(DashboardReportPayload $payload)
	{
		if (empty($payload->topProducts)) {
			return '<p style="font-size:10pt;color:#666666;">Không có dữ liệu bán hàng trong kỳ.</p>';
		}
		$totalSold = 0;
		$totalRevenue = 0;
		$out = '<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;font-size:9.5pt;">';
		$out .= '<thead><tr style="background-color:#f3f4f6;font-weight:bold;color:#111827;">'
			. '<th style="border:0.4px solid #d1d5db;width:8%;text-align:center;vertical-align:middle;padding:3mm;">STT</th>'
			. '<th style="border:0.4px solid #d1d5db;width:54%;text-align:left;vertical-align:middle;padding:3mm;">Sản phẩm</th>'
			. '<th style="border:0.4px solid #d1d5db;width:15%;text-align:center;vertical-align:middle;padding:3mm;">Số lượng</th>'
			. '<th style="border:0.4px solid #d1d5db;width:23%;text-align:right;vertical-align:middle;padding:3mm;">Doanh thu</th>'
			. '</tr></thead><tbody>';
		foreach ($payload->topProducts as $idx => $row) {
			$totalSold += (int) $row['sold'];
			$totalRevenue += (int) $row['revenue'];
			$bg = $idx === 0 ? '#f3f4f6' : (($idx % 2 === 0) ? '#ffffff' : '#f9fafb');
			$name = $this->truncate($row['name'], 36);
			$out .= '<tr style="background-color:' . $bg . ';">'
				. '<td style="border:0.3px solid #e5e7eb;text-align:center;vertical-align:middle;padding:2.8mm 3mm;">' . (int) $row['stt'] . '</td>'
				. '<td style="border:0.3px solid #e5e7eb;text-align:left;vertical-align:middle;padding:2.8mm 3mm;white-space:nowrap;">' . $this->esc($name) . '</td>'
				. '<td style="border:0.3px solid #e5e7eb;text-align:center;vertical-align:middle;padding:2.8mm 3mm;font-weight:bold;">' . number_format((int) $row['sold'], 0, ',', '.') . '</td>'
				. '<td style="border:0.3px solid #e5e7eb;text-align:right;vertical-align:middle;padding:2.8mm 3mm;">' . number_format((int) $row['revenue'], 0, ',', '.') . ' đ</td>'
				. '</tr>';
		}
		$out .= '<tr style="background-color:#f3f4f6;font-weight:bold;">'
			. '<td style="border:0.4px solid #d1d5db;text-align:center;vertical-align:middle;padding:2.8mm 3mm;" colspan="2">Tổng</td>'
			. '<td style="border:0.4px solid #d1d5db;text-align:center;vertical-align:middle;padding:2.8mm 3mm;">' . number_format($totalSold, 0, ',', '.') . '</td>'
			. '<td style="border:0.4px solid #d1d5db;text-align:right;vertical-align:middle;padding:2.8mm 3mm;">' . number_format($totalRevenue, 0, ',', '.') . ' đ</td>'
			. '</tr>';
		$out .= '</tbody></table>';
		return $out;
	}

	protected function buildLowStockHtml(DashboardReportPayload $payload)
	{
		if (empty($payload->lowStock)) {
			return '<p style="font-size:10pt;color:#666666;">Không có sản phẩm sắp hết hàng.</p>';
		}
		$out = '<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;font-size:9.5pt;">';
		$out .= '<thead><tr style="background-color:#f3f4f6;font-weight:bold;color:#111827;">'
			. '<th style="border:0.4px solid #d1d5db;width:6%;text-align:center;vertical-align:middle;padding:3mm;">STT</th>'
			. '<th style="border:0.4px solid #d1d5db;width:12%;text-align:center;vertical-align:middle;padding:3mm;">Mã SP</th>'
			. '<th style="border:0.4px solid #d1d5db;width:38%;text-align:left;vertical-align:middle;padding:3mm;">Sản phẩm</th>'
			. '<th style="border:0.4px solid #d1d5db;width:12%;text-align:center;vertical-align:middle;padding:3mm;">Tồn kho</th>'
			. '<th style="border:0.4px solid #d1d5db;width:17%;text-align:right;vertical-align:middle;padding:3mm;">Giá bán</th>'
			. '<th style="border:0.4px solid #d1d5db;width:15%;text-align:center;vertical-align:middle;padding:3mm;">Trạng thái</th>'
			. '</tr></thead><tbody>';
		foreach ($payload->lowStock as $idx => $row) {
			$qty = (int) $row['qty'];
			$status = isset($row['status']) ? $row['status'] : '';
			$bg = ($qty <= 5) ? '#f3f4f6' : (($idx % 2 === 0) ? '#ffffff' : '#f9fafb');
			$out .= '<tr style="background-color:' . $bg . ';">'
				. '<td style="border:0.3px solid #e5e7eb;text-align:center;vertical-align:middle;padding:2.8mm 3mm;">' . ($idx + 1) . '</td>'
				. '<td style="border:0.3px solid #e5e7eb;text-align:center;vertical-align:middle;padding:2.8mm 3mm;">#' . (int) $row['id'] . '</td>'
				. '<td style="border:0.3px solid #e5e7eb;text-align:left;vertical-align:middle;padding:2.8mm 3mm;white-space:nowrap;">' . $this->esc($this->truncate($row['name'], 40)) . '</td>'
				. '<td style="border:0.3px solid #e5e7eb;text-align:center;vertical-align:middle;padding:2.8mm 3mm;font-weight:bold;">' . number_format($qty, 0, ',', '.') . '</td>'
				. '<td style="border:0.3px solid #e5e7eb;text-align:right;vertical-align:middle;padding:2.8mm 3mm;">' . number_format((int) $row['price'], 0, ',', '.') . ' đ</td>'
				. '<td style="border:0.3px solid #e5e7eb;text-align:center;vertical-align:middle;padding:2.8mm 3mm;">' . $this->esc($status) . '</td>'
				. '</tr>';
		}
		$out .= '</tbody></table>';
		return $out;
	}

	protected function buildGroupedNotesHtml(DashboardReportPayload $payload)
	{
		$groups = !empty($payload->groupedSystemNotes) ? $payload->groupedSystemNotes : array('Hệ thống' => $payload->systemNotes);
		$out = '<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;font-size:9.5pt;">';
		$out .= '<thead><tr style="background-color:#f3f4f6;font-weight:bold;color:#111827;">'
			. '<th style="border:0.4px solid #d1d5db;width:20%;text-align:left;vertical-align:middle;padding:3mm;">Nhóm</th>'
			. '<th style="border:0.4px solid #d1d5db;width:80%;text-align:left;vertical-align:middle;padding:3mm;">Ghi chú quan trọng</th>'
			. '</tr></thead><tbody>';
		$i = 0;
		foreach ($groups as $group => $notes) {
			if (empty($notes)) {
				$notes = array('Không có ghi chú nổi bật.');
			}
			$notes = array_slice($notes, 0, 3);
			$bg = ($i % 2 === 0) ? '#ffffff' : '#f9fafb';
			$text = array();
			foreach ($notes as $note) {
				$text[] = '• ' . $this->esc($note);
			}
			$out .= '<tr style="background-color:' . $bg . ';">'
				. '<td style="border:0.3px solid #e5e7eb;font-weight:bold;vertical-align:middle;padding:2.8mm 3mm;">' . $this->esc($group) . '</td>'
				. '<td style="border:0.3px solid #e5e7eb;vertical-align:middle;padding:2.8mm 3mm;">' . implode('<br>', $text) . '</td>'
				. '</tr>';
			$i++;
		}
		$out .= '</tbody></table>';
		return $out;
	}

	protected function inferUnit($label, $value)
	{
		return (strpos((string) $value, '₫') !== false || stripos($label, 'doanh thu') !== false) ? 'VNĐ' : 'đơn vị';
	}

	protected function stripUnit($value)
	{
		return trim(str_replace(array('₫', 'VNĐ'), '', (string) $value));
	}

	protected function truncate($text, $max)
	{
		$text = trim((string) $text);
		if (function_exists('mb_strlen') && function_exists('mb_substr')) {
			return mb_strlen($text, 'UTF-8') > $max ? mb_substr($text, 0, $max - 3, 'UTF-8') . '...' : $text;
		}
		return strlen($text) > $max ? substr($text, 0, $max - 3) . '...' : $text;
	}

	protected function cleanupImages(DashboardReportPayload $payload)
	{
		foreach (array($payload->revenueChartPath, $payload->statusChartPath, $payload->topProductsChartPath) as $path) {
			if ($path !== '' && is_file($path)) {
				@unlink($path);
			}
		}
	}

	protected function esc($s)
	{
		return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
	}
}
