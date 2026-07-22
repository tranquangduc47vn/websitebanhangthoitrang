<?php
/**
 * Cập nhật giá cho sản phẩm price = 0 — lấy từ jm.com.vn theo tên SP.
 *
 * Chạy:
 *   php scripts/fix_zero_prices.php --dry-run
 *   php scripts/fix_zero_prices.php
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('JM_IMPORT_LIB_ONLY', true);
require dirname(__DIR__) . '/scripts/import_jm_products.php';

$dryRun = in_array('--dry-run', array_slice($argv, 1), true);
$mysqli = jm_import_db_connect();

$categories = array(
	array('url' => 'https://jm.com.vn/dam-pc1.html', 'catalog_id' => 15),
	array('url' => 'https://jm.com.vn/ao-pc7.html', 'catalog_id' => 16),
	array('url' => 'https://jm.com.vn/quan-pc14.html', 'catalog_id' => 18),
	array('url' => 'https://jm.com.vn/chan-vay-pc20.html', 'catalog_id' => 17),
	array('url' => 'https://jm.com.vn/ao-khoac-pc25.html', 'catalog_id' => 25),
);

$defaults = array(
	15 => array('price' => 890000, 'discount' => 0),
	16 => array('price' => 450000, 'discount' => 0),
	17 => array('price' => 520000, 'discount' => 0),
	18 => array('price' => 480000, 'discount' => 0),
	25 => array('price' => 1290000, 'discount' => 0),
);

$res = $mysqli->query('SELECT id, catalog_id, name FROM product WHERE price <= 0 OR price IS NULL ORDER BY id');
$zeroProducts = array();
while ($row = $res->fetch_assoc()) {
	$key = jm_normalize_product_name($row['name']);
	$zeroProducts[$key] = $row;
}
echo 'Sản phẩm giá 0: ' . count($zeroProducts) . "\n";
if (empty($zeroProducts)) {
	exit(0);
}

$nameToUrl = array();
foreach ($categories as $cat) {
	echo "Quét danh mục {$cat['catalog_id']}...\n";
	$html = httpGet($cat['url']);
	if ($html === '') {
		echo "  ! Không tải được {$cat['url']}\n";
		continue;
	}
	foreach (extractProductUrls($html) as $url) {
		$page = httpGet($url);
		if ($page === '') {
			usleep(150000);
			continue;
		}
		$title = jm_normalize_product_name(parseTitle($page));
		if ($title !== '') {
			$nameToUrl[$title] = $url;
		}
		usleep(150000);
	}
}

$updated = 0;
$fallback = 0;
$failed = 0;

$updateStmt = $mysqli->prepare('UPDATE product SET price = ?, discount = ? WHERE id = ?');

foreach ($zeroProducts as $key => $product) {
	$price = 0;
	$discount = 0;
	$source = '';

	if (isset($nameToUrl[$key])) {
		$html = httpGet($nameToUrl[$key]);
		if ($html !== '') {
			$parsed = parsePrices($html);
			$price = (float) $parsed['price'];
			$discount = (int) $parsed['discount'];
			$source = 'jm';
		}
		usleep(150000);
	}

	if ($price <= 0) {
		$catId = (int) $product['catalog_id'];
		if (isset($defaults[$catId])) {
			$price = (float) $defaults[$catId]['price'];
			$discount = (int) $defaults[$catId]['discount'];
			$source = 'default';
			$fallback++;
		} else {
			echo "  ! Không có giá: #{$product['id']} {$product['name']}\n";
			$failed++;
			continue;
		}
	}

	if ($dryRun) {
		echo "  ~ [dry-run] #{$product['id']} {$product['name']} => price={$price} discount={$discount} ({$source})\n";
		$updated++;
		continue;
	}

	$id = (int) $product['id'];
	$updateStmt->bind_param('dii', $price, $discount, $id);
	if ($updateStmt->execute()) {
		echo "  + #{$id} {$product['name']} => " . number_format($price) . "đ ({$source})\n";
		$updated++;
	} else {
		echo "  ! Update fail #{$id}: {$updateStmt->error}\n";
		$failed++;
	}
}

$updateStmt->close();

echo "\nDone. updated={$updated}, fallback={$fallback}, failed={$failed}, dryRun=" . ($dryRun ? 'yes' : 'no') . "\n";

function jm_normalize_product_name($name)
{
	$name = html_entity_decode(trim((string) $name), ENT_QUOTES, 'UTF-8');
	$name = preg_replace('/\s+/u', ' ', $name);
	return mb_strtolower($name, 'UTF-8');
}
