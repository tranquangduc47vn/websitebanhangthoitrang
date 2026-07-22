<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DashboardReportPayload
{
	public $periodLabel;
	public $storeName;
	public $kpis;
	public $revenueChartPath;
	public $statusChartPath;
	public $topProductsChartPath;
	public $topProducts;
	public $lowStock;
	public $systemNotes;
	public $groupedSystemNotes;

	public function __construct()
	{
		$this->periodLabel = '';
		$this->storeName = '';
		$this->kpis = array();
		$this->revenueChartPath = '';
		$this->statusChartPath = '';
		$this->topProductsChartPath = '';
		$this->topProducts = array();
		$this->lowStock = array();
		$this->systemNotes = array();
		$this->groupedSystemNotes = array();
	}
}
