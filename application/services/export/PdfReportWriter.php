<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PdfReportWriter
{
	public function send(ExportContext $ctx, ExportReportDefinition $def, $filename, $inline = false)
	{
		require_once APPPATH . 'services/export/ExportReportPdf.php';

		$pdf = new ExportReportPdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetCreator('Hệ thống quản trị');
		$pdf->SetAuthor($ctx->exporterName);
		$pdf->SetTitle($def->title);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(true);
		$storeName = $ctx->storeName !== '' ? $ctx->storeName : 'Webshop';
		$pdf->footerLeftText = $def->title . ' - ' . $storeName;
		$pdf->SetMargins(15, 14, 15);
		$pdf->SetAutoPageBreak(true, 20);
		$pdf->AddPage();
		$pdf->SetFont('dejavusans', '', 10);

		$this->renderHeader($pdf, $ctx, $def, $storeName);

		$html = $this->buildTableHtml($def);
		$pdf->writeHTML($html, true, false, true, false, '');

		if (!empty($def->totals)) {
			$pdf->Ln(2);
			$pdf->writeHTML($this->buildTotalsHtml($def), true, false, true, false, '');
		}

		if (!empty($def->chartImages)) {
			foreach ($def->chartImages as $img) {
				if ($img !== '' && is_file($img)) {
					$pdf->AddPage();
					$pdf->Image($img, 15, 20, 180, 0, 'PNG');
					@unlink($img);
				}
			}
		}

		$pdf->Ln(10);
		$pdf->SetFont('dejavusans', '', 9);
		$pdf->SetTextColor(60, 60, 60);
		$pdf->Cell(90, 6, 'Người lập báo cáo', 0, 0, 'C');
		$pdf->Cell(90, 6, 'Phê duyệt', 0, 1, 'C');
		$pdf->Ln(14);
		$pdf->Cell(90, 6, $ctx->exporterName, 0, 0, 'C');
		$pdf->Cell(90, 6, '........................', 0, 1, 'C');

		$disposition = $inline ? 'inline' : 'attachment';
		$pdf->Output($filename, $disposition === 'inline' ? 'I' : 'D');
		exit;
	}

	protected function renderHeader($pdf, ExportContext $ctx, ExportReportDefinition $def, $storeName)
	{
		$pdf->SetTextColor(20, 20, 20);
		$pdf->SetFont('dejavusans', 'B', 18);
		$pdf->Cell(0, 9, $def->title, 0, 1, 'L');

		$pdf->SetDrawColor(150, 150, 150);
		$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
		$pdf->Ln(4);

		$html = '<table cellpadding="3" cellspacing="0" style="width:100%;font-size:9.5pt;color:#333333;">'
			. '<tr>'
			. '<td style="width:18%;font-weight:bold;">Cửa hàng</td><td style="width:32%;">' . $this->esc($storeName) . '</td>'
			. '<td style="width:20%;font-weight:bold;">Thời gian xuất</td><td style="width:30%;text-align:right;">' . $this->esc(date('d/m/Y H:i', $ctx->exportedAt)) . '</td>'
			. '</tr><tr>'
			. '<td style="font-weight:bold;">Người xuất</td><td>' . $this->esc($ctx->exporterName) . '</td>'
			. '<td style="font-weight:bold;">Bộ lọc</td><td style="text-align:right;">' . $this->esc($ctx->filterText) . '</td>'
			. '</tr></table>';
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->Ln(4);
		$pdf->SetTextColor(0, 0, 0);
	}

	protected function buildTableHtml(ExportReportDefinition $def)
	{
		$colCount = count($def->headers);

		$out = '<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;font-size:9.5pt;">';
		$out .= '<thead><tr style="background-color:#f3f4f6;font-weight:bold;color:#111827;">';
		foreach ($def->headers as $i => $h) {
			$w = isset($def->columnWidths[$i]) ? $def->columnWidths[$i] : '';
			$style = 'border:0.4px solid #d1d5db;text-align:center;vertical-align:middle;padding:3mm 3mm;';
			if ($w !== '') {
				$style .= 'width:' . $w . ';';
			}
			$out .= '<th style="' . $style . '">' . $this->esc($h) . '</th>';
		}
		$out .= '</tr></thead><tbody>';

		foreach ($def->rows as $rIdx => $r) {
			$bg = ($rIdx % 2 === 0) ? '#ffffff' : '#f9fafb';
			$out .= '<tr style="background-color:' . $bg . ';">';
			foreach ($r as $i => $cell) {
				$align = 'left';
				$bold = '';
				$nowrap = '';
				if (in_array($i, $def->moneyColumns, true) && is_numeric($cell)) {
					$cell = $this->formatMoney((int) $cell);
					$align = 'right';
					$nowrap = 'white-space:nowrap;';
				} elseif (in_array($i, $def->dateColumns, true) && is_numeric($cell)) {
					$cell = date('d/m/Y H:i', (int) $cell);
					$align = 'center';
					$nowrap = 'white-space:nowrap;';
				} elseif ($i === 0) {
					$align = 'center';
					$nowrap = 'white-space:nowrap;';
				} elseif (isset($def->columnWidths[$i]) && !is_numeric($cell)) {
					$maxChars = max(12, (int) round(((float) rtrim($def->columnWidths[$i], '%')) * 1.1));
					$cell = $this->truncate((string) $cell, $maxChars);
					$nowrap = 'white-space:nowrap;';
				}
				if (in_array($i, $def->centerColumns, true)) {
					$align = 'center';
					$bold = 'font-weight:bold;';
					$nowrap = 'white-space:nowrap;';
				} elseif (in_array($i, $def->centerAlignColumns, true)) {
					$align = 'center';
					$nowrap = 'white-space:nowrap;';
				}
				$style = 'border:0.3px solid #e5e7eb;vertical-align:middle;padding:2.8mm 3mm;text-align:' . $align . ';' . $nowrap . $bold;
				$out .= '<td style="' . $style . '">' . $this->esc($cell) . '</td>';
			}
			$out .= '</tr>';
		}
		if (empty($def->rows)) {
			$out .= '<tr><td colspan="' . max(1, $colCount) . '" style="border:0.3px solid #e5e7eb;text-align:center;padding:6mm;color:#6b7280;">Không có dữ liệu phù hợp với bộ lọc hiện tại.</td></tr>';
		}
		$out .= '</tbody></table>';
		return $out;
	}

	protected function buildTotalsHtml(ExportReportDefinition $def)
	{
		$out = '<table cellpadding="0" cellspacing="0" style="width:60%;border-collapse:collapse;font-size:9.5pt;">';
		$out .= '<tr><td colspan="2" style="border:0.4px solid #d1d5db;background-color:#f3f4f6;font-weight:bold;padding:2.5mm 3mm;">Tổng kết</td></tr>';
		foreach ($def->totals as $k => $v) {
			$isMoney = is_numeric($v) && strpos($k, 'Tổng') !== false
				&& stripos($k, 'đơn') === false && stripos($k, 'KPI') === false && stripos($k, 'lượng') === false && stripos($k, 'sản phẩm') === false;
			$val = is_numeric($v) ? ($isMoney ? $this->formatMoney((int) $v) : number_format((int) $v, 0, ',', '.')) : $this->esc($v);
			$out .= '<tr>'
				. '<td style="border:0.3px solid #e5e7eb;padding:2.5mm 3mm;font-weight:bold;width:60%;">' . $this->esc($k) . '</td>'
				. '<td style="border:0.3px solid #e5e7eb;padding:2.5mm 3mm;text-align:right;font-weight:bold;">' . $val . '</td>'
				. '</tr>';
		}
		$out .= '</table>';
		return $out;
	}

	protected function formatMoney($amount)
	{
		return number_format((int) $amount, 0, ',', '.') . ' đ';
	}

	protected function truncate($text, $max)
	{
		$text = trim((string) $text);
		if (function_exists('mb_strlen') && function_exists('mb_substr')) {
			return mb_strlen($text, 'UTF-8') > $max ? mb_substr($text, 0, $max - 3, 'UTF-8') . '...' : $text;
		}
		return strlen($text) > $max ? substr($text, 0, $max - 3) . '...' : $text;
	}

	protected function esc($s)
	{
		return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
	}
}
