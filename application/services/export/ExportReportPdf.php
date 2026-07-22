<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!class_exists('TCPDF', false)) {
	require_once FCPATH . 'vendor/tecnickcom/tcpdf/tcpdf.php';
}

class ExportReportPdf extends TCPDF
{
	public $footerLeftText = 'Báo cáo được tạo tự động bởi Webshop Management System.';

	public function Footer()
	{
		$this->SetY(-14);
		$this->SetDrawColor(210, 210, 210);
		$this->Line(15, $this->GetY(), 195, $this->GetY());
		$this->Ln(1.5);
		$this->SetFont('dejavusans', '', 8);
		$this->SetTextColor(90, 90, 90);
		$this->Cell(90, 8, $this->footerLeftText, 0, 0, 'L');
		$this->Cell(0, 8, 'Trang ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'R');
	}
}
