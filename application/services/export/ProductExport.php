<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/export/AbstractModuleExport.php';

class ProductExport extends AbstractModuleExport
{
	public function build()
	{
		$CI = $this->ci();
		$CI->load->model('product_model');

		$name = trim((string) $this->filter->get('name'));
		$catalog_id = (int) $this->filter->get('catalog_id');

		$input = array('order' => array('product.id', 'DESC'));
		if ($name !== '') {
			$input['like'] = array('product.name', $name);
		}
		if ($catalog_id > 0) {
			$input['where'] = array('product.catalog_id' => $catalog_id);
		}

		$limit = $this->filter->paginationLimit(10);
		if ($limit !== null) {
			$input['limit'] = array($limit[0], $limit[1]);
		}

		$CI->db->select('product.id, product.name, product.price, product.quantity, product.discount, catalog.name as catalog_name');
		$CI->db->join('catalog', 'catalog.id = product.catalog_id', 'left');
		$list = $CI->product_model->get_list($input);

		$def = new ExportReportDefinition('Báo cáo sản phẩm');
		$def->headers = array('Mã', 'Tên sản phẩm', 'Danh mục', 'Giá bán (₫)', 'Tồn kho');
		$def->moneyColumns = array(3);
		$totalStockValue = 0;
		foreach ($list as $p) {
			$qty = (int) $p->quantity;
			$price = (int) $p->price;
			$totalStockValue += $qty * $price;
			$def->rows[] = array(
				(int) $p->id,
				$p->name,
				isset($p->catalog_name) ? $p->catalog_name : '',
				$price,
				$qty,
			);
		}
		$def->totals = array(
			'Tổng sản phẩm' => count($def->rows),
			'Tổng giá trị tồn (ước tính)' => $totalStockValue,
		);
		return $def;
	}
}
