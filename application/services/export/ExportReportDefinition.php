<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExportReportDefinition
{
	public $title;
	public $headers;
	public $rows;
	public $totals;
	public $moneyColumns;
	public $dateColumns;
	public $chartImages;
	public $reportType;
	public $dashboardPayload;
	public $columnWidths;
	public $centerColumns;
	public $centerAlignColumns;

	public function __construct($title = '')
	{
		$this->title = $title;
		$this->headers = array();
		$this->rows = array();
		$this->totals = array();
		$this->moneyColumns = array();
		$this->dateColumns = array();
		$this->chartImages = array();
		$this->reportType = 'standard';
		$this->dashboardPayload = null;
		$this->columnWidths = array();
		$this->centerColumns = array();
		$this->centerAlignColumns = array();
	}
}
