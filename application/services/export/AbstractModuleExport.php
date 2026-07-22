<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/export/ExportReportDefinition.php';
require_once APPPATH . 'services/export/ExportFilter.php';

abstract class AbstractModuleExport
{
	protected $filter;

	public function __construct(ExportFilter $filter)
	{
		$this->filter = $filter;
	}

	abstract public function build();

	protected function ci()
	{
		return get_instance();
	}

	protected function fmtMoney($n)
	{
		return (int) round((float) $n);
	}

	protected function fmtDateTime($ts)
	{
		$ts = (int) $ts;
		if ($ts <= 0) {
			return '';
		}
		return date('d/m/Y H:i', $ts);
	}

	protected function fmtDateFromMixed($value)
	{
		if (is_numeric($value)) {
			return $this->fmtDateTime((int) $value);
		}
		$ts = strtotime((string) $value);
		return $ts ? date('d/m/Y H:i', $ts) : (string) $value;
	}
}
