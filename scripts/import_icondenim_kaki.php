<?php
/**
 * Import quần kaki từ icondenim.com → danh mục Quần Kali (catalog #13).
 * Chạy: php scripts/import_icondenim_kaki.php
 * Tuỳ chọn: php scripts/import_icondenim_kaki.php --limit=15 --dry-run
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

$root = dirname(__DIR__);
$limit = 15;
$dryRun = false;
$catalogId = 13;
$collectionUrl = 'https://icondenim.com/collections/quan-kaki/products.json?limit=250';

foreach (array_slice($argv, 1) as $arg) {
	if ($arg === '--dry-run') {
		$dryRun = true;
	} elseif (strpos($arg, '--limit=') === 0) {
		$limit = max(1, (int) substr($arg, 8));
	} elseif (strpos($arg, '--catalog=') === 0) {
		$catalogId = max(1, (int) substr($arg, 10));
	}
}

define('BASEPATH', $root . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR);
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');
$db = array();
$active_group = 'default';
require $root . '/application/config/database.php';
$cfg = $db[$active_group];

$mysqli = @new mysqli($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database']);
if ($mysqli->connect_error) {
	fwrite(STDERR, "DB connect failed: {$mysqli->connect_error}\n");
	exit(1);
}
$mysqli->set_charset($cfg['char_set'] ?: 'utf8');

$uploadDir = $root . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR;
if (!$dryRun && !is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
	fwrite(STDERR, "Cannot create upload dir: {$uploadDir}\n");
	exit(1);
}

$json = httpGet($collectionUrl);
if ($json === '') {
	fwrite(STDERR, "Cannot fetch collection JSON\n");
	exit(1);
}

$data = json_decode($json, true);
if (empty($data['products']) || !is_array($data['products'])) {
	fwrite(STDERR, "Invalid products JSON\n");
	exit(1);
}

$products = array_slice($data['products'], 0, $limit);
echo 'Catalog #' . $catalogId . " — import tối đa {$limit} sản phẩm (" . count($products) . " trong batch)\n";

$imported = 0;
$skipped = 0;
$errors = 0;

foreach ($products as $product) {
	$result = importShopifyProduct($mysqli, $uploadDir, $product, $catalogId, $dryRun);
	if ($result === 'imported') {
		$imported++;
	} elseif ($result === 'skipped') {
		$skipped++;
	} else {
		$errors++;
	}
	usleep(200000);
}

echo "\nDone. imported={$imported}, skipped={$skipped}, errors={$errors}, dryRun=" . ($dryRun ? 'yes' : 'no') . "\n";

function httpGet($url)
{
	$ch = curl_init($url);
	curl_setopt_array($ch, array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_TIMEOUT => 60,
		CURLOPT_SSL_VERIFYPEER => true,
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
		CURLOPT_HTTPHEADER => array('Accept: application/json'),
	));
	$body = curl_exec($ch);
	$code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($body === false || $code >= 400) {
		return '';
	}
	return (string) $body;
}

function importShopifyProduct(mysqli $db, $uploadDir, array $product, $catalogId, $dryRun)
{
	$name = trim((string) ($product['title'] ?? ''));
	if ($name === '') {
		echo "  ! Thiếu tên sản phẩm\n";
		return 'error';
	}

	if (productExists($db, $name)) {
		echo "  - Skip (exists): {$name}\n";
		return 'skipped';
	}

	$prices = parseShopifyPrices($product);
	$images = parseShopifyImages($product);
	if (empty($images)) {
		echo "  ! No images: {$name}\n";
		return 'error';
	}

	$content = parseShopifyContent($product);
	$colors = parseShopifyOptions($product, 'color');
	$sizes = parseShopifyOptions($product, 'size');
	if (empty($sizes)) {
		$sizes = array('28', '29', '30', '31', '32');
	}

	if ($dryRun) {
		echo "  ~ [dry-run] {$name} | price={$prices['price']} discount={$prices['discount']} imgs=" . count($images) . "\n";
		return 'imported';
	}

	$mainFile = downloadImage($images[0], $uploadDir, slugify($name) . '-main');
	if ($mainFile === '') {
		echo "  ! Download main image fail: {$name}\n";
		return 'error';
	}

	$galleryFiles = array();
	foreach (array_slice($images, 1, 5) as $idx => $imgUrl) {
		$file = downloadImage($imgUrl, $uploadDir, slugify($name) . '-g' . ($idx + 1));
		if ($file !== '') {
			$galleryFiles[] = $file;
		}
	}

	$now = time();
	$stmt = $db->prepare('INSERT INTO product (catalog_id, name, content, color, size, price, discount, image_link, image_list, view, buyed, quantity, rate_total, rate_count, created) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 10, 4, 1, ?)');
	if (!$stmt) {
		echo "  ! SQL prepare fail: {$name}\n";
		return 'error';
	}

	$colorStr = empty($colors) ? 'Đen,Be,Xanh đen' : implode(',', $colors);
	$sizeStr = implode(',', $sizes);
	$imageListJson = json_encode($galleryFiles, JSON_UNESCAPED_UNICODE);
	$price = (float) $prices['price'];
	$discount = (int) $prices['discount'];

	$stmt->bind_param('issssdissi', $catalogId, $name, $content, $colorStr, $sizeStr, $price, $discount, $mainFile, $imageListJson, $now);
	$ok = $stmt->execute();
	if (!$ok) {
		echo "  ! Insert fail: {$name} — " . $stmt->error . "\n";
		$stmt->close();
		return 'error';
	}
	$stmt->close();

	echo "  + Imported: {$name} ({$price}đ)\n";
	return 'imported';
}

function productExists(mysqli $db, $name)
{
	$stmt = $db->prepare('SELECT id FROM product WHERE name = ? LIMIT 1');
	$stmt->bind_param('s', $name);
	$stmt->execute();
	$res = $stmt->get_result();
	$row = $res ? $res->fetch_assoc() : null;
	$stmt->close();
	return !empty($row);
}

function parseShopifyPrices(array $product)
{
	$variants = isset($product['variants']) && is_array($product['variants']) ? $product['variants'] : array();
	$sale = null;
	$compare = null;

	foreach ($variants as $variant) {
		$p = isset($variant['price']) ? (float) $variant['price'] : 0;
		$c = isset($variant['compare_at_price']) ? (float) $variant['compare_at_price'] : 0;
		if ($p <= 0) {
			continue;
		}
		if ($sale === null || $p < $sale) {
			$sale = $p;
		}
		if ($c > $p && ($compare === null || $c > $compare)) {
			$compare = $c;
		}
	}

	if ($sale === null) {
		return array('price' => 0, 'discount' => 0);
	}

	if ($compare !== null && $compare > $sale) {
		return array(
			'price' => $compare,
			'discount' => (int) round($compare - $sale),
		);
	}

	return array('price' => $sale, 'discount' => 0);
}

function parseShopifyImages(array $product)
{
	$urls = array();
	if (!empty($product['images']) && is_array($product['images'])) {
		foreach ($product['images'] as $img) {
			$src = isset($img['src']) ? trim((string) $img['src']) : '';
			if ($src !== '') {
				$src = preg_replace('#\?.*$#', '', $src);
				$urls[$src] = true;
			}
		}
	}
	if (empty($urls) && !empty($product['image']['src'])) {
		$src = preg_replace('#\?.*$#', '', (string) $product['image']['src']);
		$urls[$src] = true;
	}
	return array_keys($urls);
}

function parseShopifyContent(array $product)
{
	$parts = array();
	$body = trim(strip_tags((string) ($product['body_html'] ?? '')));
	if ($body !== '') {
		$parts[] = '<p>' . htmlspecialchars($body, ENT_QUOTES, 'UTF-8') . '</p>';
	}
	$vendor = trim((string) ($product['vendor'] ?? ''));
	if ($vendor !== '') {
		$parts[] = '<p><strong>Thương hiệu:</strong> ' . htmlspecialchars($vendor, ENT_QUOTES, 'UTF-8') . '</p>';
	}
	$type = trim((string) ($product['product_type'] ?? ''));
	if ($type !== '') {
		$parts[] = '<p><strong>Loại:</strong> ' . htmlspecialchars($type, ENT_QUOTES, 'UTF-8') . '</p>';
	}
	if (empty($parts)) {
		return '<p>Quần kaki nam cao cấp — tham khảo từ ICONDENIM.</p>';
	}
	return implode("\n", $parts);
}

function parseShopifyOptions(array $product, $kind)
{
	$options = isset($product['options']) && is_array($product['options']) ? $product['options'] : array();
	$values = array();

	foreach ($options as $option) {
		$name = mb_strtolower(trim((string) ($option['name'] ?? '')), 'UTF-8');
		$isMatch = ($kind === 'color' && (strpos($name, 'màu') !== false || $name === 'color'))
			|| ($kind === 'size' && (strpos($name, 'size') !== false || strpos($name, 'kích') !== false));
		if (!$isMatch || empty($option['values']) || !is_array($option['values'])) {
			continue;
		}
		foreach ($option['values'] as $val) {
			$val = trim((string) $val);
			if ($val !== '') {
				$values[] = $val;
			}
		}
	}

	return array_values(array_unique($values));
}

function downloadImage($url, $uploadDir, $baseName)
{
	$data = httpGet($url);
	if ($data === '') {
		return '';
	}

	$ext = 'jpg';
	if (preg_match('#\.(jpe?g|png|webp|gif)(?:\?|$)#i', $url, $m)) {
		$ext = strtolower($m[1]);
		if ($ext === 'jpeg') {
			$ext = 'jpg';
		}
	}

	$filename = $baseName . '-' . substr(md5($url), 0, 8) . '.' . $ext;
	$path = $uploadDir . $filename;
	if (file_put_contents($path, $data) === false) {
		return '';
	}
	return $filename;
}

function slugify($text)
{
	$text = mb_strtolower(trim($text), 'UTF-8');
	$text = preg_replace('#[^\pL\pN]+#u', '-', $text);
	$text = trim($text, '-');
	if ($text === '') {
		$text = 'sp';
	}
	return mb_substr($text, 0, 60, 'UTF-8');
}
