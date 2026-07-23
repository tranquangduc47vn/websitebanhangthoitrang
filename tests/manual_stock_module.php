<?php
/**
 * Manual tests — module SP / tồn kho / phiếu nhập (v2 variants).
 *
 *   php tests/manual_stock_module.php
 */
$_SERVER['CI_ENV'] = 'development';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SCRIPT_NAME'] = '/index.php';
chdir(__DIR__ . '/../');
ob_start();
require __DIR__ . '/../index.php';
ob_end_clean();

$CI =& get_instance();
$CI->load->library('product_service');
$CI->load->library('stock_service');
$CI->load->library('inventory_service');
$CI->load->model('product_model');
$CI->load->model('product_variant_model');
$CI->load->model('stock_receipt_model');
$CI->load->model('stock_movement_model');

$results = array();

function test_assert(&$results, $name, $condition, $detail = '')
{
	$results[] = array('name' => $name, 'pass' => (bool) $condition, 'detail' => $detail);
}

foreach (array('product_variants', 'stock_receipts', 'stock_receipt_items', 'stock_movements', 'suppliers') as $t) {
	test_assert($results, 'table `' . $t . '` exists', $CI->db->table_exists($t));
}

$product = $CI->db->order_by('id', 'ASC')->limit(1)->get('product')->row();
test_assert($results, 'sample product exists', !empty($product));

if (!$product) {
	output($results);
	exit(1);
}

$pid = (int) $product->id;
$attrs = $CI->product_service->parse_product_attributes($product);
$color = $attrs['colors'][0];
$size = $attrs['sizes'][0];

$sync = $CI->product_service->sync_variants($pid, $attrs['colors'], $attrs['sizes'], (float) $product->price);
test_assert($results, 'sync variants', !empty($sync['ok']));

$variant = $CI->product_variant_model->find_variant($pid, $color, $size);
test_assert($results, 'variant resolved', !empty($variant), $variant ? $variant->sku : 'none');

$vid = $variant ? (int) $variant->id : 0;
$stock_before = $variant ? (int) $variant->stock : 0;

$create = $CI->stock_service->create_draft_receipt(0, 'Test module v2', array(
	array('variant_id' => $vid, 'qty' => 2, 'unit_cost' => 50000),
), 1);
test_assert($results, 'create draft receipt', !empty($create['ok']), isset($create['message']) ? $create['message'] : '');
$receipt_id = !empty($create['receipt_id']) ? (int) $create['receipt_id'] : 0;

$v2 = $CI->product_variant_model->get_info($vid);
test_assert($results, 'draft does NOT add stock', $v2 && (int) $v2->stock === $stock_before);

if ($receipt_id) {
	$confirm = $CI->stock_service->confirm_receipt($receipt_id, 1);
	test_assert($results, 'confirm receipt', !empty($confirm['ok']), isset($confirm['message']) ? $confirm['message'] : '');

	$v3 = $CI->product_variant_model->get_info($vid);
	test_assert($results, 'stock +2 after confirm', $v3 && (int) $v3->stock === $stock_before + 2);

	$mov = $CI->db->where('variant_id', $vid)->where('movement_type', 'in')
		->where('reference_id', $receipt_id)->get('stock_movements')->row();
	test_assert($results, 'movement in logged', !empty($mov));
	test_assert($results, 'movement before/after', $mov && (int) $mov->before_qty === $stock_before && (int) $mov->after_qty === $stock_before + 2);

	$confirm2 = $CI->stock_service->confirm_receipt($receipt_id, 1);
	test_assert($results, 'cannot confirm twice', empty($confirm2['ok']));
}

$adj = $CI->inventory_service->adjust_stock($vid, $stock_before + 5, 'Test kiểm kê manual', 1);
test_assert($results, 'inventory adjust', !empty($adj['ok']), isset($adj['message']) ? $adj['message'] : '');

$mov_adj = $CI->db->where('variant_id', $vid)->where('movement_type', 'adjust')->order_by('id', 'DESC')->limit(1)->get('stock_movements')->row();
test_assert($results, 'movement adjust logged', !empty($mov_adj));

$deduct = $CI->inventory_service->deduct_for_order($vid, 1, 99999, 1, 'Test order');
test_assert($results, 'deduct for order', !empty($deduct['ok']));

$mov_out = $CI->db->where('variant_id', $vid)->where('movement_type', 'out')->order_by('id', 'DESC')->limit(1)->get('stock_movements')->row();
test_assert($results, 'movement out logged', !empty($mov_out));

$p = $CI->product_model->get_info($pid);
$sum = $CI->product_variant_model->sum_stock_by_product($pid);
test_assert($results, 'product.quantity cache synced', $p && (int) $p->quantity === (int) $sum);

output($results);

function output($results)
{
	$pass = 0;
	$fail = 0;
	foreach ($results as $r) {
		echo ($r['pass'] ? 'PASS' : 'FAIL') . ' — ' . $r['name'];
		if ($r['detail'] !== '') {
			echo ' (' . $r['detail'] . ')';
		}
		echo PHP_EOL;
		if ($r['pass']) {
			$pass++;
		} else {
			$fail++;
		}
	}
	echo PHP_EOL . "Total: {$pass} PASS, {$fail} FAIL" . PHP_EOL;
	exit($fail > 0 ? 1 : 0);
}
