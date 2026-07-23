<?php
/**
 * Smoke test — receipt form variant picker (model layer).
 * Run: php tests/manual_stock_receipt_form.php
 */
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

$root = dirname(__DIR__);
chdir($root);
ob_start();
require $root . '/index.php';
ob_end_clean();

$CI =& get_instance();
$CI->load->model('inventory_model');

$passed = 0;
$failed = 0;

function assert_true($label, $cond)
{
	global $passed, $failed;
	if ($cond) {
		echo "PASS: {$label}\n";
		$passed++;
	} else {
		echo "FAIL: {$label}\n";
		$failed++;
	}
}

$total_variants = (int) $CI->db->where('status', 1)->count_all_results('product_variants');
assert_true('product_variants table readable', true);

$filters = array('q' => '');
$count_all = $CI->inventory_model->count_receipt_variants($filters);
assert_true('count_receipt_variants returns int', is_int($count_all));
assert_true('count matches table (active variants)', $count_all === $total_variants);

$page = $CI->inventory_model->search_receipt_variants($filters, 25, 0);
assert_true('search_receipt_variants returns array', is_array($page));
assert_true('search page size <= 25', count($page) <= 25);

if (!empty($page)) {
	$row = $page[0];
	assert_true('row has sku', isset($row->sku));
	assert_true('row has product_name', isset($row->product_name));
	assert_true('row has stock', isset($row->stock));

	$by_sku = $CI->inventory_model->search_receipt_variants(array('q' => $row->sku), 10, 0);
	assert_true('search by SKU finds row', !empty($by_sku));

	$colors = $CI->inventory_model->get_receipt_filter_colors(array('product_id' => (int) $row->product_id));
	assert_true('filter colors is array', is_array($colors));

	$sizes = $CI->inventory_model->get_receipt_filter_sizes(array('product_id' => (int) $row->product_id));
	assert_true('filter sizes is array', is_array($sizes));

	$map = $CI->inventory_model->get_variants_by_ids(array((int) $row->id));
	assert_true('get_variants_by_ids maps id', isset($map[(int) $row->id]));
}

$recent = $CI->inventory_model->get_recent_receipt_variants(1, 10);
assert_true('recent variants query runs', is_array($recent));
assert_true('recent variants <= 10', count($recent) <= 10);

$CI->inventory_model->get_receipt_filter_colors(array());
$CI->inventory_model->get_receipt_filter_sizes(array());
$count_after_filters = $CI->inventory_model->count_receipt_variants(array());
assert_true('count after filter_options sequence', is_int($count_after_filters));

$list_count = $CI->inventory_model->count_list(array());
assert_true('count_list runs without duplicate FROM', is_int($list_count));

$offset_page = $CI->inventory_model->search_receipt_variants($filters, 10, 10);
assert_true('pagination offset query runs', is_array($offset_page));

echo "\n---\nTotal: {$passed} PASS, {$failed} FAIL\n";
exit($failed > 0 ? 1 : 0);
