<?php
/**
 * Manual tests — module nhập kho (stock receipts).
 *
 *   php tests/manual_stock_receipt.php
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
$CI->load->library('stock_service');
$CI->load->model('product_model');
$CI->load->model('product_inventory_model');
$CI->load->model('stock_receipt_model');
$CI->load->model('stock_movement_model');

$results = array();

function test_assert(&$results, $name, $condition, $detail = '')
{
	$results[] = array(
		'name' => $name,
		'pass' => (bool) $condition,
		'detail' => $detail,
	);
}

// Tables exist
$tables = array('suppliers', 'stock_receipts', 'stock_receipt_items', 'product_inventory', 'stock_movements');
foreach ($tables as $t) {
	test_assert($results, 'table `' . $t . '` exists', $CI->db->table_exists($t));
}

$product = $CI->db->order_by('id', 'ASC')->limit(1)->get('product')->row();
test_assert($results, 'sample product available', !empty($product), $product ? 'id=' . $product->id : 'none');

if (!$product) {
	output($results);
	exit(1);
}

$pid = (int) $product->id;
$variants = $CI->stock_service->parse_product_variants($product);
$size = $variants['sizes'][0];
$color = $variants['colors'][0];

$row_before = $CI->product_inventory_model->get_variant($pid, $size, $color);
$qty_before = $row_before ? (int) $row_before->quantity : 0;

$create = $CI->stock_service->create_draft_receipt(0, 'Test manual', array(
	array('product_id' => $pid, 'size' => $size, 'color' => $color, 'qty' => 3, 'unit_cost' => 100000),
), 1);

test_assert($results, 'create draft receipt', !empty($create['ok']), isset($create['message']) ? $create['message'] : '');
$receipt_id = !empty($create['receipt_id']) ? (int) $create['receipt_id'] : 0;

$receipt = $receipt_id ? $CI->stock_receipt_model->get_info($receipt_id) : null;
test_assert($results, 'draft status after create', $receipt && $receipt->status === 'draft');

$row_after_draft = $CI->product_inventory_model->get_variant($pid, $size, $color);
$qty_after_draft = $row_after_draft ? (int) $row_after_draft->quantity : 0;
test_assert($results, 'draft does NOT add inventory', $qty_after_draft === $qty_before, $qty_before . ' vs ' . $qty_after_draft);

$code1 = $CI->stock_service->generate_receipt_code();
$code2 = $CI->stock_service->generate_receipt_code();
test_assert($results, 'receipt code format PNYYYYMMDDNNN', (bool) preg_match('/^PN\d{11}$/', $code1), $code1);

if ($receipt_id) {
	$confirm = $CI->stock_service->confirm_receipt($receipt_id, 1);
	test_assert($results, 'confirm receipt', !empty($confirm['ok']), isset($confirm['message']) ? $confirm['message'] : '');

	$receipt2 = $CI->stock_receipt_model->get_info($receipt_id);
	test_assert($results, 'receipt locked confirmed', $receipt2 && $receipt2->status === 'confirmed');

	$row_after = $CI->product_inventory_model->get_variant($pid, $size, $color);
	$qty_after = $row_after ? (int) $row_after->quantity : 0;
	test_assert($results, 'inventory increased by 3', $qty_after === $qty_before + 3, $qty_before . ' -> ' . $qty_after);

	$mov = $CI->db->where('reference_type', 'stock_receipt')
		->where('reference_id', $receipt_id)
		->where('movement_type', 'in')
		->get('stock_movements')->row();
	test_assert($results, 'stock_movement type=in logged', !empty($mov));
	test_assert($results, 'movement before/after qty', $mov && (int) $mov->before_qty === $qty_before && (int) $mov->after_qty === $qty_after);

	$confirm_again = $CI->stock_service->confirm_receipt($receipt_id, 1);
	test_assert($results, 'cannot confirm twice', empty($confirm_again['ok']));

	$product_row = $CI->product_model->get_info($pid);
	test_assert($results, 'product.quantity synced', $product_row && (int) $product_row->quantity >= $qty_after);
}

// Cancel draft flow
$create2 = $CI->stock_service->create_draft_receipt(0, 'Cancel test', array(
	array('product_id' => $pid, 'size' => $size, 'color' => $color, 'qty' => 1, 'unit_cost' => 0),
), 1);
$rid2 = !empty($create2['receipt_id']) ? (int) $create2['receipt_id'] : 0;
if ($rid2) {
	$cancel = $CI->stock_service->cancel_receipt($rid2, 1);
	test_assert($results, 'cancel draft', !empty($cancel['ok']));
	$r2 = $CI->stock_receipt_model->get_info($rid2);
	test_assert($results, 'cancelled status', $r2 && $r2->status === 'cancelled');
}

output($results);

function output($results)
{
	$pass = 0;
	$fail = 0;
	foreach ($results as $r) {
		$status = $r['pass'] ? 'PASS' : 'FAIL';
		if ($r['pass']) {
			$pass++;
		} else {
			$fail++;
		}
		echo $status . ' — ' . $r['name'];
		if ($r['detail'] !== '') {
			echo ' (' . $r['detail'] . ')';
		}
		echo PHP_EOL;
	}
	echo PHP_EOL . "Total: {$pass} PASS, {$fail} FAIL" . PHP_EOL;
	exit($fail > 0 ? 1 : 0);
}
