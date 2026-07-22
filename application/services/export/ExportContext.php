<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExportContext
{
	public $module;
	public $format;
	public $storeName;
	public $logoPath;
	public $exporterName;
	public $exportedAt;
	public $filterText;

	public function __construct($module, $format)
	{
		$this->module = $module;
		$this->format = $format;
		$this->exportedAt = time();
		$this->storeName = 'Webshop';
		$this->logoPath = '';
		$this->exporterName = '';
		$this->filterText = '';

		$logo = FCPATH . 'upload/logo.png';
		if (is_file($logo)) {
			$this->logoPath = $logo;
		}
	}
}
