<?php
/**
 * Cập nhật schema DB sau khi import webshop.sql — chạy lại an toàn.
 *
 *   php databaseaddphpmyadmin/run_migrations.php
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

function column_exists($mysqli, $table, $column)
{
	$table = $mysqli->real_escape_string($table);
	$column = $mysqli->real_escape_string($column);
	$r = $mysqli->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
	return $r && $r->num_rows > 0;
}

$schemaSql = <<<'SQL'
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token_hash` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `expires_at` int NOT NULL,
  `used_at` int NOT NULL DEFAULT '0',
  `created` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_password_resets_token` (`token_hash`),
  KEY `idx_password_resets_user` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_phone` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `phone_label` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `phone_number` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_user_phone_user` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_review` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `user_id` int NOT NULL DEFAULT '0',
  `user_name` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `stars` tinyint NOT NULL DEFAULT '5',
  `session_token` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `created` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_product_review_product` (`product_id`),
  KEY `idx_product_review_user` (`user_id`),
  KEY `idx_product_review_session` (`session_token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `contact_name` varchar(128) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `phone` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(128) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `address` varchar(512) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `note` text COLLATE utf8mb3_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '1',
  `created` int NOT NULL DEFAULT '0',
  `updated` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_suppliers_code` (`code`),
  KEY `idx_suppliers_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE IF NOT EXISTS `stock_receipts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `receipt_code` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `supplier_id` int NOT NULL DEFAULT '0',
  `status` enum('draft','confirmed','cancelled') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'draft',
  `note` text COLLATE utf8mb3_unicode_ci,
  `total_qty` int NOT NULL DEFAULT '0',
  `created_by` int NOT NULL DEFAULT '0',
  `confirmed_by` int NOT NULL DEFAULT '0',
  `confirmed_at` int NOT NULL DEFAULT '0',
  `cancelled_at` int NOT NULL DEFAULT '0',
  `created` int NOT NULL DEFAULT '0',
  `updated` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_stock_receipts_code` (`receipt_code`),
  KEY `idx_stock_receipts_status` (`status`),
  KEY `idx_stock_receipts_supplier` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE IF NOT EXISTS `stock_receipt_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `receipt_id` int NOT NULL,
  `product_id` int NOT NULL,
  `size` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `color` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `qty` int NOT NULL DEFAULT '0',
  `unit_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `note` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_sri_receipt` (`receipt_id`),
  KEY `idx_sri_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `size` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `color` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `quantity` int NOT NULL DEFAULT '0',
  `updated` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_product_inventory_variant` (`product_id`,`size`,`color`),
  KEY `idx_pi_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `size` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `color` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `movement_type` varchar(16) COLLATE utf8mb3_unicode_ci NOT NULL,
  `qty_change` int NOT NULL,
  `before_qty` int NOT NULL DEFAULT '0',
  `after_qty` int NOT NULL DEFAULT '0',
  `reference_type` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `reference_id` int NOT NULL DEFAULT '0',
  `note` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `created_by` int NOT NULL DEFAULT '0',
  `created` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_sm_product` (`product_id`),
  KEY `idx_sm_reference` (`reference_type`,`reference_id`),
  KEY `idx_sm_created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL;

if (!$m->multi_query($schemaSql)) {
	fwrite(STDERR, 'Schema error: ' . $m->error . PHP_EOL);
	exit(1);
}
do {
	if ($result = $m->store_result()) {
		$result->free();
	}
} while ($m->more_results() && $m->next_result());

foreach (array('password_resets', 'user_phone', 'product_review', 'suppliers', 'stock_receipts', 'stock_receipt_items', 'product_inventory', 'stock_movements') as $t) {
	echo (table_exists($m, $t) ? 'OK' : 'FAIL') . ": table `{$t}`" . PHP_EOL;
}

// Dữ liệu mẫu NCC (chỉ khi bảng trống)
if (table_exists($m, 'suppliers')) {
	$r = $m->query('SELECT COUNT(*) AS c FROM `suppliers`');
	$row = $r ? $r->fetch_assoc() : null;
	if ($row && (int) $row['c'] === 0) {
		$now = time();
		$m->query("INSERT INTO `suppliers` (`code`,`name`,`contact_name`,`phone`,`email`,`address`,`note`,`status`,`created`,`updated`) VALUES
			('NCC001','Nhà cung cấp mẫu qD Design','Nguyễn Văn A','0901234567','ncc@example.com','TP.HCM','Dữ liệu demo nhập kho',1,{$now},{$now})");
		echo "OK: sample supplier NCC001\n";
	} else {
		echo "SKIP: suppliers already has data\n";
	}
}

if (table_exists($m, 'product')) {
	if (!column_exists($m, 'product', 'discount_type')) {
		if (!$m->query("ALTER TABLE `product` ADD COLUMN `discount_type` varchar(10) NOT NULL DEFAULT 'percent' AFTER `discount`")) {
			fwrite(STDERR, 'ALTER discount_type: ' . $m->error . PHP_EOL);
			exit(1);
		}
		echo "OK: product.discount_type added\n";
	} else {
		echo "SKIP: product.discount_type exists\n";
	}

	if (!column_exists($m, 'product', 'discount_percent')) {
		if (!$m->query("ALTER TABLE `product` ADD COLUMN `discount_percent` int NOT NULL DEFAULT 0 AFTER `discount_type`")) {
			fwrite(STDERR, 'ALTER discount_percent: ' . $m->error . PHP_EOL);
			exit(1);
		}
		echo "OK: product.discount_percent added\n";
	} else {
		echo "SKIP: product.discount_percent exists\n";
	}
} else {
	echo "WARN: table `product` not found — import webshop.sql first\n";
}

// --- Module kho v2: product_variants + variant_id ---
$variantSql = <<<'SQL'
CREATE TABLE IF NOT EXISTS `product_variants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `sku` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `color` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `size` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `cost_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `stock` int NOT NULL DEFAULT '0',
  `min_stock` int NOT NULL DEFAULT '5',
  `image` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `status` tinyint NOT NULL DEFAULT '1',
  `created` int NOT NULL DEFAULT '0',
  `updated` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_product_variants_sku` (`sku`),
  UNIQUE KEY `uk_product_variants_combo` (`product_id`,`color`,`size`),
  KEY `idx_pv_product` (`product_id`),
  KEY `idx_pv_stock` (`stock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL;

if (!$m->query($variantSql)) {
	fwrite(STDERR, 'product_variants: ' . $m->error . PHP_EOL);
	exit(1);
}
echo (table_exists($m, 'product_variants') ? 'OK' : 'FAIL') . ": table `product_variants`" . PHP_EOL;

$alterSpecs = array(
	'product' => array(
		'code' => "varchar(64) NOT NULL DEFAULT ''",
		'status' => "tinyint NOT NULL DEFAULT '1'",
	),
	'stock_receipts' => array(
		'receipt_date' => "date DEFAULT NULL",
		'total_amount' => "decimal(15,2) NOT NULL DEFAULT '0.00'",
	),
	'stock_receipt_items' => array(
		'variant_id' => "int NOT NULL DEFAULT '0'",
		'subtotal' => "decimal(15,2) NOT NULL DEFAULT '0.00'",
	),
	'stock_movements' => array(
		'variant_id' => "int NOT NULL DEFAULT '0'",
	),
	'order' => array(
		'variant_id' => "int NOT NULL DEFAULT '0'",
		'cost_price' => "decimal(15,2) NOT NULL DEFAULT '0.00'",
	),
);

foreach ($alterSpecs as $table => $cols) {
	if (!table_exists($m, $table)) {
		echo "SKIP: alter `{$table}` — table missing\n";
		continue;
	}
	foreach ($cols as $col => $def) {
		if (!column_exists($m, $table, $col)) {
			if (!$m->query("ALTER TABLE `{$table}` ADD COLUMN `{$col}` {$def}")) {
				fwrite(STDERR, "ALTER {$table}.{$col}: " . $m->error . PHP_EOL);
				exit(1);
			}
			echo "OK: {$table}.{$col} added\n";
		} else {
			echo "SKIP: {$table}.{$col} exists\n";
		}
	}
}

// Backfill product.code
if (table_exists($m, 'product') && table_exists($m, 'product_variants')) {
	$r = $m->query("SELECT id, name, color, size, price, quantity FROM product ORDER BY id ASC");
	if ($r) {
		$now = time();
		while ($p = $r->fetch_assoc()) {
			$pid = (int) $p['id'];
			if (column_exists($m, 'product', 'code')) {
				$code = 'SP' . str_pad((string) $pid, 5, '0', STR_PAD_LEFT);
				$m->query("UPDATE product SET code = '{$m->real_escape_string($code)}' WHERE id = {$pid} AND (code IS NULL OR code = '')");
			}

			$colors = array('');
			if (!empty($p['color'])) {
				$colors = array_values(array_filter(array_map('trim', explode(',', $p['color'])), function ($v) { return $v !== ''; }));
			}
			if (empty($colors)) {
				$colors = array('');
			}
			$sizes = array('');
			if (!empty($p['size'])) {
				$sizes = array_values(array_filter(array_map('trim', explode(',', $p['size'])), function ($v) { return $v !== ''; }));
			}
			if (empty($sizes)) {
				$sizes = array('');
			}

			foreach ($colors as $color) {
				foreach ($sizes as $size) {
					$cEsc = $m->real_escape_string($color);
					$sEsc = $m->real_escape_string($size);
					$chk = $m->query("SELECT id FROM product_variants WHERE product_id = {$pid} AND color = '{$cEsc}' AND size = '{$sEsc}' LIMIT 1");
					if ($chk && $chk->num_rows > 0) {
						continue;
					}

					$slug = function ($text) {
						$text = trim((string) $text);
						if ($text === '') return 'DF';
						$text = preg_replace('/[^A-Za-z0-9]+/', '-', $text);
						$text = strtoupper(trim($text, '-'));
						return $text !== '' ? substr($text, 0, 12) : 'DF';
					};
					$sku = 'SP' . str_pad((string) $pid, 5, '0', STR_PAD_LEFT) . '-' . $slug($color) . '-' . $slug($size);
					$n = 1;
					$baseSku = $sku;
					while (true) {
						$skuEsc = $m->real_escape_string($sku);
						$dupe = $m->query("SELECT id FROM product_variants WHERE sku = '{$skuEsc}' LIMIT 1");
						if (!$dupe || $dupe->num_rows === 0) break;
						$n++;
						$sku = $baseSku . '-' . $n;
					}

					$stock = 0;
					if (table_exists($m, 'product_inventory')) {
						$inv = $m->query("SELECT quantity FROM product_inventory WHERE product_id = {$pid} AND color = '{$cEsc}' AND size = '{$sEsc}' LIMIT 1");
						if ($inv && ($invRow = $inv->fetch_assoc())) {
							$stock = (int) $invRow['quantity'];
						}
					}

					$price = (float) $p['price'];
					$skuEsc = $m->real_escape_string($sku);
					$m->query("INSERT INTO product_variants (product_id, sku, color, size, price, cost_price, stock, min_stock, image, status, created, updated)
						VALUES ({$pid}, '{$skuEsc}', '{$cEsc}', '{$sEsc}', {$price}, 0, {$stock}, 5, '', 1, {$now}, {$now})");
				}
			}

			// Sync product.quantity from variants
			$sum = $m->query("SELECT COALESCE(SUM(stock),0) AS s FROM product_variants WHERE product_id = {$pid}");
			if ($sum && ($sumRow = $sum->fetch_assoc())) {
				$m->query('UPDATE product SET quantity = ' . (int) $sumRow['s'] . " WHERE id = {$pid}");
			}
		}
		echo "OK: product_variants backfill from product attributes\n";
	}

	// Map stock_receipt_items.variant_id
	if (table_exists($m, 'stock_receipt_items')) {
		$m->query("UPDATE stock_receipt_items sri
			INNER JOIN product_variants pv ON pv.product_id = sri.product_id AND pv.color = sri.color AND pv.size = sri.size
			SET sri.variant_id = pv.id
			WHERE sri.variant_id = 0");
		$m->query("UPDATE stock_receipt_items SET subtotal = qty * unit_cost WHERE subtotal = 0");
		echo "OK: stock_receipt_items variant_id mapped\n";
	}

	if (table_exists($m, 'stock_movements')) {
		$m->query("UPDATE stock_movements sm
			INNER JOIN product_variants pv ON pv.product_id = sm.product_id AND pv.color = sm.color AND pv.size = sm.size
			SET sm.variant_id = pv.id
			WHERE sm.variant_id = 0");
		echo "OK: stock_movements variant_id mapped\n";
	}
}

echo "Done.\n";
