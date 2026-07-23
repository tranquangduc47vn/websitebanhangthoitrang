<?php
/**
 * Kiểm tra tồn kho theo variant khi thêm giỏ hàng.
 * Run: php tests/manual_variant_stock_cart.php
 */
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

$root = dirname(__DIR__);
chdir($root);
ob_start();
require $root . '/index.php';
ob_end_clean();

$CI =& get_instance();
$CI->load->library('product_service');

$passed = 0;
$failed = 0;

function assert_case($label, $cond)
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

function find_test_product($CI)
{
	$sql = "
		SELECT pv.product_id
		FROM product_variants pv
		INNER JOIN product p ON p.id = pv.product_id
		WHERE pv.status = 1
			AND pv.color != ''
			AND pv.size != ''
		GROUP BY pv.product_id
		HAVING COUNT(DISTINCT pv.id) >= 3
			AND SUM(CASE WHEN pv.stock = 2 THEN 1 ELSE 0 END) >= 3
		ORDER BY pv.product_id DESC
		LIMIT 1
	";
	$row = $CI->db->query($sql)->row();
	return $row ? (int) $row->product_id : 0;
}

$product_id = find_test_product($CI);
if ($product_id <= 0) {
	echo "SKIP: Không tìm thấy sản phẩm có ≥3 biến thể stock=2 để test.\n";
	echo "Tạo SP màu Hồng size S/M/L, mỗi variant tồn 2, rồi chạy lại.\n";
	exit(0);
}

$map = $CI->product_service->get_variant_stock_map($product_id);
assert_case('variant stock map loaded', !empty($map));

$size_l = '';
$size_m = '';
$color = '';
foreach ($map as $entry) {
	if ((int) $entry['stock'] !== 2) {
		continue;
	}
	$color = $entry['color'];
	if ($entry['size'] === 'L') {
		$size_l = 'L';
	}
	if ($entry['size'] === 'M') {
		$size_m = 'M';
	}
}

if ($color === '' || $size_l === '' || $size_m === '') {
	echo "SKIP: Sản phẩm #{$product_id} chưa có cặp Hồng/M tương ứng size L và M với stock=2.\n";
	exit(0);
}

$svc = $CI->product_service;

$c1 = $svc->validate_cart_quantity($product_id, $color, $size_l, 3, 0, false);
assert_case('L + qty 3 => FAIL', !$c1['ok']);
assert_case('L + qty 3 message has product name', strpos($c1['message'], 'size ' . $size_l) !== false);
assert_case('L + qty 3 message has color', strpos($c1['message'], 'màu ' . $color) !== false);
assert_case('L + qty 3 stock=2 in message', strpos($c1['message'], 'chỉ còn 2 sản phẩm') !== false);

$c2 = $svc->validate_cart_quantity($product_id, $color, $size_l, 2, 0, false);
assert_case('L + qty 2 => PASS', $c2['ok']);
assert_case('L + qty 2 variant_id > 0', !empty($c2['variant_id']));

$c3 = $svc->validate_cart_quantity($product_id, $color, $size_m, 2, 0, false);
assert_case('M + qty 2 => PASS', $c3['ok']);

$c4 = $svc->validate_cart_quantity($product_id, $color, $size_m, 3, 0, false);
assert_case('M + qty 3 => FAIL', !$c4['ok']);

$row_l = $svc->resolve_variant_row($product_id, $color, $size_l, false);
$row_m = $svc->resolve_variant_row($product_id, $color, $size_m, false);
assert_case('resolve L stock = 2', $row_l && (int) $row_l['stock'] === 2);
assert_case('resolve M stock = 2', $row_m && (int) $row_m['stock'] === 2);
assert_case('L and M are different variants', $row_l && $row_m && $row_l['variant_id'] !== $row_m['variant_id']);

echo "\nProduct tested: #{$product_id} ({$color} / L,M)\n";
echo "---\nTotal: {$passed} PASS, {$failed} FAIL\n";
exit($failed > 0 ? 1 : 0);
