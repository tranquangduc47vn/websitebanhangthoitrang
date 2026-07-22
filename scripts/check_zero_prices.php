<?php
define('BASEPATH', 'x');
define('ENVIRONMENT', 'development');
$db = array();
$active_group = 'default';
require dirname(__DIR__) . '/application/config/database.php';

$m = new mysqli(
	$db['default']['hostname'],
	$db['default']['username'],
	$db['default']['password'],
	$db['default']['database']
);
if ($m->connect_error) {
	fwrite(STDERR, $m->connect_error . PHP_EOL);
	exit(1);
}

$r = $m->query('SELECT COUNT(*) AS c FROM product WHERE price <= 0 OR price IS NULL');
echo 'zero_price: ' . $r->fetch_assoc()['c'] . PHP_EOL;

$r = $m->query('SELECT id, catalog_id, name, price, discount FROM product WHERE price <= 0 OR price IS NULL ORDER BY id');
while ($row = $r->fetch_assoc()) {
	echo $row['id'] . ' | cat ' . $row['catalog_id'] . ' | ' . $row['name'] . PHP_EOL;
}
