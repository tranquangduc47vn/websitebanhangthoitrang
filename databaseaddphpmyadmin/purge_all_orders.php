<?php
/**
 * Xóa TOÀN BỘ lịch sử đơn hàng (transaction + order + liên quan).
 *
 *   php databaseaddphpmyadmin/purge_all_orders.php
 *
 * Lưu ý: không hoàn tồn kho đã trừ khi đặt hàng.
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

function count_rows($mysqli, $table)
{
	if (!table_exists($mysqli, $table)) {
		return 0;
	}
	$r = $mysqli->query("SELECT COUNT(*) AS c FROM `{$table}`");
	$row = $r ? $r->fetch_assoc() : null;
	return $row ? (int) $row['c'] : 0;
}

$tx_before = count_rows($m, 'transaction');
$order_before = count_rows($m, 'order');

echo "Transactions before: {$tx_before}\n";
echo "Order lines before: {$order_before}\n";

if ($tx_before === 0 && $order_before === 0) {
	echo "Nothing to delete.\n";
	exit(0);
}

$m->query('SET FOREIGN_KEY_CHECKS = 0');

$steps = array();

if (table_exists($m, 'voucher_use')) {
	$steps['voucher_use'] = 'DELETE FROM `voucher_use`';
}
if (table_exists($m, 'user_point_log')) {
	$steps['user_point_log (orders)'] = "DELETE FROM `user_point_log` WHERE transaction_id > 0";
}
if (table_exists($m, 'stock_movements')) {
	$steps['stock_movements (orders)'] = "DELETE FROM `stock_movements` WHERE reference_type = 'order'";
}
$steps['order'] = 'DELETE FROM `order`';
$steps['transaction'] = 'DELETE FROM `transaction`';

foreach ($steps as $label => $sql) {
	if (!$m->query($sql)) {
		$m->query('SET FOREIGN_KEY_CHECKS = 1');
		fwrite(STDERR, "FAIL {$label}: " . $m->error . PHP_EOL);
		exit(1);
	}
	echo "OK: {$label} ({$m->affected_rows} rows)\n";
}

$m->query('SET FOREIGN_KEY_CHECKS = 1');

if (table_exists($m, 'transaction')) {
	$m->query('ALTER TABLE `transaction` AUTO_INCREMENT = 1');
}
if (table_exists($m, 'order')) {
	$m->query('ALTER TABLE `order` AUTO_INCREMENT = 1');
}

echo "Transactions after: " . count_rows($m, 'transaction') . "\n";
echo "Order lines after: " . count_rows($m, 'order') . "\n";
echo "Done.\n";
