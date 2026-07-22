<?php
/**
 * Import sản phẩm demo từ jm.com.vn → DB webshop local.
 * Chạy: php scripts/import_jm_products.php
 * Tuỳ chọn: php scripts/import_jm_products.php --limit=10 --dry-run
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

function jm_import_cli_main(array $argv)
{
$root = dirname(__DIR__);
$limitPerCategory = 10;
$dryRun = false;
$debug = false;

foreach (array_slice($argv, 1) as $arg) {
	if ($arg === '--dry-run') {
		$dryRun = true;
	} elseif ($arg === '--debug') {
		$debug = true;
	} elseif (strpos($arg, '--limit=') === 0) {
		$limitPerCategory = max(1, (int) substr($arg, 8));
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
ensureCatalogs($mysqli);

$categories = array(
	array('url' => 'https://jm.com.vn/dam-pc1.html', 'catalog_id' => 15, 'label' => 'Đầm'),
	array('url' => 'https://jm.com.vn/ao-pc7.html', 'catalog_id' => 16, 'label' => 'Áo'),
	array('url' => 'https://jm.com.vn/quan-pc14.html', 'catalog_id' => 18, 'label' => 'Quần'),
	array('url' => 'https://jm.com.vn/chan-vay-pc20.html', 'catalog_id' => 17, 'label' => 'Chân váy'),
	array('url' => 'https://jm.com.vn/ao-khoac-pc25.html', 'catalog_id' => 25, 'label' => 'Áo khoác'),
);

$uploadDir = $root . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR;
if (!$dryRun && !is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
	fwrite(STDERR, "Cannot create upload dir: {$uploadDir}\n");
	exit(1);
}

$imported = 0;
$skipped = 0;
$errors = 0;

foreach ($categories as $cat) {
	echo "\n=== {$cat['label']} (catalog {$cat['catalog_id']}) ===\n";
	$html = httpGet($cat['url']);
	if ($html === '') {
		echo "  ! Không tải được trang danh mục\n";
		$errors++;
		continue;
	}
	if ($debug) {
		echo '  HTML bytes: ' . strlen($html) . "\n";
		file_put_contents($root . '/storage/jm-debug-' . $cat['catalog_id'] . '.html', $html);
	}

	$productUrls = extractProductUrls($html);
	$productUrls = array_slice($productUrls, 0, $limitPerCategory);
	echo '  Tìm thấy ' . count($productUrls) . " sản phẩm\n";

	foreach ($productUrls as $url) {
		$result = importProduct($mysqli, $uploadDir, $url, (int) $cat['catalog_id'], $dryRun);
		if ($result === 'imported') {
			$imported++;
		} elseif ($result === 'skipped') {
			$skipped++;
		} else {
			$errors++;
		}
		usleep(250000);
	}
}

echo "\nDone. imported={$imported}, skipped={$skipped}, errors={$errors}, dryRun=" . ($dryRun ? 'yes' : 'no') . "\n";
}

function jm_import_db_connect()
{
	$root = dirname(__DIR__);
	if (!defined('BASEPATH')) {
		define('BASEPATH', $root . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR);
	}
	if (!defined('ENVIRONMENT')) {
		define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');
	}
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
	return $mysqli;
}

function ensureCatalogs(mysqli $db)
{
	$rows = array(
		array(25, 'Áo Khoác', 8, 5),
	);
	foreach ($rows as $row) {
		list($id, $name, $parentId, $sort) = $row;
		$stmt = $db->prepare('SELECT id FROM catalog WHERE id = ? LIMIT 1');
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$res = $stmt->get_result();
		$exists = $res && $res->fetch_assoc();
		$stmt->close();
		if ($exists) {
			continue;
		}
		$created = date('Y-m-d H:i:s');
		$stmt = $db->prepare('INSERT INTO catalog (id, name, description, parent_id, sort_order, created) VALUES (?, ?, "", ?, ?, ?)');
		$stmt->bind_param('isiis', $id, $name, $parentId, $sort, $created);
		$stmt->execute();
		$stmt->close();
		echo "Created catalog #{$id}: {$name}\n";
	}
}

function httpGet($url)
{
	$ch = curl_init($url);
	curl_setopt_array($ch, array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_TIMEOUT => 45,
		CURLOPT_SSL_VERIFYPEER => true,
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
		CURLOPT_HTTPHEADER => array('Accept-Language: vi-VN,vi;q=0.9'),
	));
	$body = curl_exec($ch);
	$code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($body === false || $code >= 400) {
		return '';
	}
	return (string) $body;
}

function extractProductUrls($html)
{
	$urls = array();
	// Absolute URLs
	if (preg_match_all('#https://jm\.com\.vn/[a-z0-9\-()]+-p\d+\.html#i', $html, $m)) {
		foreach ($m[0] as $u) {
			$urls[$u] = true;
		}
	}
	// Relative URLs in HTML attributes
	if (preg_match_all('#href="(/[^"]+-p\d+\.html)"#i', $html, $m2)) {
		foreach ($m2[1] as $path) {
			$urls['https://jm.com.vn' . $path] = true;
		}
	}
	return array_keys($urls);
}

function importProduct(mysqli $db, $uploadDir, $url, $catalogId, $dryRun)
{
	$html = httpGet($url);
	if ($html === '') {
		echo "  ! Fetch fail: {$url}\n";
		return 'error';
	}

	$name = parseTitle($html);
	if ($name === '') {
		echo "  ! No title: {$url}\n";
		return 'error';
	}

	if (productExists($db, $name, $catalogId)) {
		echo "  - Skip (exists): {$name}\n";
		return 'skipped';
	}

	$prices = parsePrices($html);
	$images = parseProductImages($html);
	if (empty($images)) {
		echo "  ! No images: {$name}\n";
		return 'error';
	}

	$colors = parseColors($html);
	$sizes = parseSizes($html);
	$content = parseContent($html);

	$mainImage = $images[0];
	$gallery = array_slice($images, 1, 5);

	if ($dryRun) {
		echo "  ~ [dry-run] {$name} | price={$prices['price']} discount={$prices['discount']} imgs=" . count($images) . "\n";
		return 'imported';
	}

	$mainFile = downloadImage($mainImage, $uploadDir, slugify($name) . '-main');
	if ($mainFile === '') {
		echo "  ! Download main image fail: {$name}\n";
		return 'error';
	}

	$galleryFiles = array();
	foreach ($gallery as $idx => $imgUrl) {
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

	$colorStr = empty($colors) ? null : implode(',', $colors);
	$sizeStr = empty($sizes) ? 'S,M,L,XL' : implode(',', $sizes);
	$imageListJson = json_encode($galleryFiles, JSON_UNESCAPED_UNICODE);
	$price = (float) $prices['price'];
	$discount = (int) $prices['discount'];

	$stmt->bind_param('issssdissi', $catalogId, $name, $content, $colorStr, $sizeStr, $price, $discount, $mainFile, $imageListJson, $now);
	$ok = $stmt->execute();
	if (!$ok) {
		echo "  ! Insert fail: {$name} — " . $stmt->error . ' / ' . $db->error . "\n";
		$stmt->close();
		return 'error';
	}
	$stmt->close();

	echo "  + Imported: {$name}\n";
	return 'imported';
}

function productExists(mysqli $db, $name, $catalogId)
{
	$stmt = $db->prepare('SELECT id FROM product WHERE name = ? LIMIT 1');
	$stmt->bind_param('s', $name);
	$stmt->execute();
	$res = $stmt->get_result();
	$row = $res ? $res->fetch_assoc() : null;
	$stmt->close();
	return !empty($row);
}

function parseTitle($html)
{
	if (preg_match('#<h1[^>]*>(.*?)</h1>#is', $html, $m)) {
		return trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES, 'UTF-8'));
	}
	if (preg_match('#<title>(.*?)</title>#is', $html, $m)) {
		return trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES, 'UTF-8'));
	}
	return '';
}

function parsePrices($html)
{
	$price = 0;
	$discount = 0;
	$nums = array();

	if (preg_match('#<div[^>]*class="[^"]*product-price[^"]*"[^>]*>(.*?)</div>#is', $html, $m)) {
		$nums = extractMoneyValues($m[1]);
	} elseif (preg_match('#class="[^"]*price[^"]*"[^>]*>(.*?)</(?:span|div|p)>#is', $html, $m)) {
		$nums = extractMoneyValues($m[1]);
	} elseif (preg_match('#itemprop="price"[^>]*content="(\d+)"#i', $html, $m)) {
		$nums = array((int) $m[1]);
	} elseif (preg_match('#"price"\s*:\s*"?(\d+)"?#i', $html, $m)) {
		$nums = array((int) $m[1]);
	}

	if (empty($nums)) {
		$nums = extractMoneyValues($html);
	}

	$nums = array_values(array_filter($nums, function ($n) {
		return $n >= 50000 && $n <= 10000000;
	}));

	if (count($nums) >= 2) {
		$sale = min($nums[0], $nums[1]);
		$original = max($nums[0], $nums[1]);
		$price = $original;
		$discount = $original - $sale;
	} elseif (count($nums) === 1) {
		$price = $nums[0];
		$discount = 0;
	}

	return array('price' => $price, 'discount' => (int) $discount);
}

function extractMoneyValues($text)
{
	$values = array();
	if (preg_match_all('#(\d[\d\.,]{3,})#u', strip_tags($text), $m)) {
		foreach ($m[1] as $raw) {
			$clean = preg_replace('#[^\d]#', '', $raw);
			if ($clean !== '') {
				$values[] = (int) $clean;
			}
		}
	}
	return $values;
}

function parseProductImages($html)
{
	$images = array();
	if (preg_match_all('#https://pos\.nvncdn\.com/[^"\']+/ps/[^"\']+#i', $html, $m)) {
		foreach ($m[0] as $u) {
			$u = preg_replace('#\?.*$#', '', $u);
			$images[$u] = true;
		}
	}
	return array_keys($images);
}

function parseColors($html)
{
	$colors = array();
	$allowed = array('Đen', 'Trắng', 'Đỏ', 'Vàng', 'Xanh dương', 'Xanh lá', 'Hồng', 'Xám', 'Nâu', 'Kem', 'Cam', 'Tím', 'Xanh đen', 'Be');
	if (preg_match('#Màu sắc:#i', $html)) {
		foreach ($allowed as $c) {
			if (preg_match('#>' . preg_quote($c, '#') . '<#u', $html)) {
				$colors[] = $c;
			}
		}
	}
	return array_values(array_unique($colors));
}

function parseSizes($html)
{
	$sizes = array();
	foreach (array('S', 'M', 'L', 'XL', 'XXL') as $s) {
		if (preg_match('#>\s*' . preg_quote($s, '#') . '\s*<#', $html)) {
			$sizes[] = $s;
		}
	}
	return $sizes;
}

function parseContent($html)
{
	if (preg_match('#id="tab-info"[^>]*>(.*?)</div>#is', $html, $m)) {
		return trim($m[1]);
	}
	if (preg_match('#class="product-description"[^>]*>(.*?)</div>#is', $html, $m)) {
		return trim($m[1]);
	}
	return '<p>Sản phẩm thời trang JM — demo import local.</p>';
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

if (!defined('JM_IMPORT_LIB_ONLY')) {
	jm_import_cli_main($argv ?? array());
}
