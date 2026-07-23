<?php
/**
 * Xóa TOÀN BỘ sản phẩm khỏi DB (+ dữ liệu liên quan, ảnh upload).
 *
 *   php databaseaddphpmyadmin/purge_all_products.php
 */
define('BASEPATH', true);
define('ENVIRONMENT', 'development');

require __DIR__ . '/../application/config/database.php';

$c = $db['default'];
$m = new mysqli($c['hostname'], $c['username'], $c['password'], $c['database']);
if ($m->connect_error) {
	fwrite(STDERR, 'Connect error: ' . $m->connect_error . PHP_EOL);
	exit(1);
}
$m->set_charset('utf8mb4');

function table_exists($mysqli, $table)
{
	$table = $mysqli->real_escape_string($table);
	$r = $mysqli->query("SHOW TABLES LIKE '{$table}'");
	return $r && $r->num_rows > 0;
}

$r = $m->query('SELECT COUNT(*) AS c FROM `product`');
$row = $r ? $r->fetch_assoc() : null;
$count_before = $row ? (int) $row['c'] : 0;
echo "Products before: {$count_before}\n";

if ($count_before === 0) {
	echo "Nothing to delete.\n";
	exit(0);
}

// Thu thập file ảnh trước khi xóa row
$images = array();
$res = $m->query('SELECT image_link, image_list FROM product');
if ($res) {
	while ($p = $res->fetch_assoc()) {
		if (!empty($p['image_link'])) {
			$images[] = $p['image_link'];
		}
		$list = json_decode($p['image_list'], true);
		if (is_array($list)) {
			foreach ($list as $img) {
				if (is_string($img) && $img !== '') {
					$images[] = $img;
				}
			}
		}
	}
}

$m->query('SET FOREIGN_KEY_CHECKS = 0');

$steps = array(
	'order' => 'DELETE FROM `order`',
	'stock_movements' => table_exists($m, 'stock_movements') ? 'DELETE FROM `stock_movements`' : null,
	'stock_receipt_items' => table_exists($m, 'stock_receipt_items') ? 'DELETE FROM `stock_receipt_items`' : null,
	'product_inventory' => table_exists($m, 'product_inventory') ? 'DELETE FROM `product_inventory`' : null,
	'product_variants' => table_exists($m, 'product_variants') ? 'DELETE FROM `product_variants`' : null,
	'product_review' => table_exists($m, 'product_review') ? 'DELETE FROM `product_review`' : null,
	'product_colors' => table_exists($m, 'product_colors') ? 'DELETE FROM `product_colors`' : null,
	'product' => 'DELETE FROM `product`',
);

foreach ($steps as $label => $sql) {
	if ($sql === null) {
		echo "SKIP: {$label}\n";
		continue;
	}
	if (!$m->query($sql)) {
		$m->query('SET FOREIGN_KEY_CHECKS = 1');
		fwrite(STDERR, "FAIL {$label}: " . $m->error . PHP_EOL);
		exit(1);
	}
	echo "OK: cleared `{$label}` ({$m->affected_rows} rows)\n";
}

$m->query('ALTER TABLE `product` AUTO_INCREMENT = 1');
$m->query('SET FOREIGN_KEY_CHECKS = 1');

$uploadDir = realpath(__DIR__ . '/../upload/product');
$deleted_files = 0;
if ($uploadDir && is_dir($uploadDir)) {
	$images = array_unique($images);
	foreach ($images as $file) {
		$file = basename(str_replace('\\', '/', $file));
		if ($file === '' || strpos($file, '..') !== false) {
			continue;
		}
		$path = $uploadDir . DIRECTORY_SEPARATOR . $file;
		if (is_file($path) && @unlink($path)) {
			$deleted_files++;
		}
	}
}

$r2 = $m->query('SELECT COUNT(*) AS c FROM `product`');
$row2 = $r2 ? $r2->fetch_assoc() : null;
$count_after = $row2 ? (int) $row2['c'] : -1;

echo "Products after: {$count_after}\n";
echo "Image files removed: {$deleted_files}\n";
echo "Done.\n";
