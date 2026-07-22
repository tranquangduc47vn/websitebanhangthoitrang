<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExportFilter
{
	public $params;
	public $scope;

	public function __construct(array $params)
	{
		$this->params = $params;
		$scope = isset($params['scope']) ? strtolower((string) $params['scope']) : 'all';
		$this->scope = ($scope === 'page') ? 'page' : 'all';
	}

	public function get($key, $default = null)
	{
		return isset($this->params[$key]) ? $this->params[$key] : $default;
	}

	public function int($key, $default = 0)
	{
		return (int) $this->get($key, $default);
	}

	public function describe($module)
	{
		$parts = array();
		switch ($module) {
			case 'products':
				if ($this->get('name')) {
					$parts[] = 'Tên: ' . $this->get('name');
				}
				if ($this->get('catalog_id')) {
					$parts[] = 'Danh mục ID: ' . (int) $this->get('catalog_id');
				}
				break;
			case 'inventory':
				if ($this->get('name')) {
					$parts[] = 'Tên: ' . $this->get('name');
				}
				if ($this->get('catalog_id')) {
					$parts[] = 'Danh mục ID: ' . (int) $this->get('catalog_id');
				}
				if ($this->get('stock')) {
					$parts[] = 'Tồn: ' . $this->get('stock');
				}
				break;
			case 'revenue':
			case 'top_products':
				if ($this->get('type')) {
					$parts[] = 'Kỳ: ' . $this->get('type');
				}
				if ($this->get('date_from')) {
					$parts[] = 'Từ ' . $this->get('date_from');
				}
				if ($this->get('date_to')) {
					$parts[] = 'Đến ' . $this->get('date_to');
				}
				break;
			case 'dashboard':
				$period = $this->get('period', '30d');
				$parts[] = 'Kỳ: ' . $period;
				if ($this->get('from')) {
					$parts[] = 'Từ ' . $this->get('from');
				}
				if ($this->get('to')) {
					$parts[] = 'Đến ' . $this->get('to');
				}
				break;
			default:
				break;
		}
		$parts[] = ($this->scope === 'page') ? 'Phạm vi: Trang hiện tại' : 'Phạm vi: Toàn bộ theo bộ lọc';
		return implode(' · ', $parts);
	}

	public function paginationLimit($defaultPer = 10)
	{
		if ($this->scope !== 'page') {
			return null;
		}
		$per = max(1, $this->int('per_page', $defaultPer));
		$offsetRaw = $this->get('offset');
		if ($offsetRaw === null || $offsetRaw === '') {
			$CI = get_instance();
			$offset = max(0, (int) $CI->uri->segment(4));
		} else {
			$offset = max(0, (int) $offsetRaw);
		}
		return array($per, $offset);
	}
}
