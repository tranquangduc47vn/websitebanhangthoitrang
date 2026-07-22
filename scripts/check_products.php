<?php
define('BASEPATH', 'x');
define('ENVIRONMENT', 'development');
$db = array();
$active_group = 'default';
require dirname(__DIR__) . '/application/config/database.php';
$m = new mysqli($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);
$r = $m->query('SELECT catalog_id, COUNT(*) c FROM product WHERE catalog_id IN (15,16,17,18,25) GROUP BY catalog_id ORDER BY catalog_id');
while ($row = $r->fetch_assoc()) {
	echo $row['catalog_id'] . ': ' . $row['c'] . PHP_EOL;
}
echo "--- catalogs ---\n";
$r2 = $m->query('SELECT id, name, parent_id FROM catalog ORDER BY id');
while ($row = $r2->fetch_assoc()) {
	echo $row['id'] . ' | ' . $row['parent_id'] . ' | ' . $row['name'] . PHP_EOL;
}
