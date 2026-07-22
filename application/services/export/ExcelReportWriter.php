<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExcelReportWriter
{
	public function send(ExportContext $ctx, ExportReportDefinition $def, $filename)
	{
		if (!class_exists(Spreadsheet::class)) {
			show_error('Thư viện PhpSpreadsheet chưa được cài. Chạy composer install trong thư mục dự án.');
		}

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('BaoCao');

		$row = 1;
		if ($ctx->logoPath !== '' && is_file($ctx->logoPath)) {
			$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			$drawing->setPath($ctx->logoPath);
			$drawing->setHeight(48);
			$drawing->setCoordinates('A1');
			$drawing->setWorksheet($sheet);
			$row = 4;
		}

		$sheet->setCellValue('A' . $row, $def->title);
		$sheet->mergeCells('A' . $row . ':F' . $row);
		$sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
		$row++;

		$meta = array(
			'Cửa hàng: ' . $ctx->storeName,
			'Xuất lúc: ' . date('d/m/Y H:i', $ctx->exportedAt),
			'Người xuất: ' . $ctx->exporterName,
			'Bộ lọc: ' . $ctx->filterText,
		);
		foreach ($meta as $line) {
			$sheet->setCellValue('A' . $row, $line);
			$sheet->mergeCells('A' . $row . ':F' . $row);
			$row++;
		}
		$row++;

		$headerRow = $row;
		$col = 1;
		foreach ($def->headers as $h) {
			$sheet->setCellValueByColumnAndRow($col, $row, $h);
			$col++;
		}
		$lastCol = count($def->headers);
		$sheet->getStyleByColumnAndRow(1, $headerRow, $lastCol, $headerRow)->getFont()->setBold(true);
		$sheet->getStyleByColumnAndRow(1, $headerRow, $lastCol, $headerRow)->getFill()
			->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');
		$row++;

		$dataStart = $row;
		foreach ($def->rows as $r) {
			$c = 1;
			foreach ($r as $cell) {
				$sheet->setCellValueByColumnAndRow($c, $row, $cell);
				$c++;
			}
			$row++;
		}
		$dataEnd = $row - 1;

		if ($dataEnd >= $dataStart) {
			foreach ($def->moneyColumns as $mc) {
				$colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($mc + 1);
				$sheet->getStyle($colLetter . $dataStart . ':' . $colLetter . $dataEnd)
					->getNumberFormat()->setFormatCode('#,##0" ₫"');
			}
			foreach ($def->dateColumns as $dc) {
				$colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($dc + 1);
				$sheet->getStyle($colLetter . $dataStart . ':' . $colLetter . $dataEnd)
					->getNumberFormat()->setFormatCode('dd/mm/yyyy hh:mm');
			}
		}

		if (!empty($def->totals)) {
			$row++;
			foreach ($def->totals as $label => $value) {
				$sheet->setCellValue('A' . $row, $label);
				$sheet->setCellValue('B' . $row, $value);
				$sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
				if (is_numeric($value)) {
					$sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0" ₫"');
				}
				$row++;
			}
		}

		$sheet->freezePane('A' . ($headerRow + 1));
		for ($i = 1; $i <= $lastCol; $i++) {
			$sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		exit;
	}
}
