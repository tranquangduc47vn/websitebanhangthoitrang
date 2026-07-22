<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/export/AbstractModuleExport.php';

class InventoryExport extends AbstractModuleExport
{
	public function build()
	{
		$CI = $this->ci();
		$CI->load->model('product_model');

		$name = trim((string) $this->filter->get('name'));
		$catalog_id = (int) $this->filter->get('catalog_id');
		$stock = $this->filter->get('stock');

		$input = array('order' => array('product.quantity', 'ASC'));
		if ($name !== '') {
			$input['like'] = array('product.name', $name);
		}
		if ($catalog_id > 0) {
			$input['where'] = array('product.catalog_id' => $catalog_id);
		}

		self::applyStockFilter($CI, $stock);

		$limit = $this->filter->paginationLimit(15);
		if ($limit !== null) {
			$input['limit'] = array($limit[0], $limit[1]);
		}

		$CI->db->select('product.id, product.name, product.quantity, product.price, catalog.name as catalog_name');
		$CI->db->join('catalog', 'catalog.id = product.catalog_id', 'left');
		$list = $CI->product_model->get_list($input);

		$def = new ExportReportDefinition('Báo cáo tồn kho');
		$def->headers = array('STT', 'Mã SP', 'Tên sản phẩm', 'Tồn kho', 'Giá bán', 'Trạng thái');
		$def->columnWidths = array(
			0 => '6%',
			1 => '12%',
			2 => '42%',
			3 => '12%',
			4 => '15%',
			5 => '13%',
		);
		$def->moneyColumns = array(4);
		$def->centerColumns = array(3);
		$def->centerAlignColumns = array(5);

		$totalQty = 0;
		$totalValue = 0;
		$stt = 0;
		foreach ($list as $p) {
			$stt++;
			$qty = (int) $p->quantity;
			$price = (int) $p->price;
			$totalQty += $qty;
			$totalValue += $qty * $price;
			$def->rows[] = array(
				$stt,
				(int) $p->id,
				$p->name,
				$qty,
				$price,
				self::stockStatusLabel($qty),
			);
		}
		$def->totals = array(
			'Tổng số sản phẩm' => count($def->rows),
			'Tổng giá trị tồn kho' => $totalValue,
		);
		return $def;
	}

	public static function stockStatusLabel($qty)
	{
		$qty = (int) $qty;
		if ($qty <= 5) {
			return 'Rất thấp';
		}
		if ($qty <= 10) {
			return 'Sắp hết';
		}
		return 'Bình thường';
	}

	public static function applyStockFilter($CI, $stock)
	{
		$stock = (string) $stock;
		if ($stock === 'out') {
			$CI->db->where('product.quantity', 0);
		} elseif ($stock === 'low') {
			$CI->db->where('product.quantity >', 0);
			$CI->db->where('product.quantity <=', 10);
		} elseif ($stock === 'ok') {
			$CI->db->where('product.quantity >', 10);
		}
	}
}
