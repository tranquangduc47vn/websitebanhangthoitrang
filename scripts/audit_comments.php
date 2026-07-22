<?php
/**
 * Đếm comment theo pattern (baseline / sau cleanup). Chạy: php scripts/audit_comments.php
 */
$root = dirname(__DIR__);
$dirs = array(
	$root . '/application/controllers',
	$root . '/application/models',
	$root . '/application/core',
	$root . '/application/helpers',
	$root . '/application/services',
	$root . '/application/libraries',
	$root . '/application/config/routes.php',
	$root . '/assets/admin/js',
	$root . '/assets/site/js',
	$root . '/scripts',
);

$noise = 0;
$total = 0;
$patterns = array(
	'banner' => '/^\s*(?:\/\/|\*)\s*(?:={3,}|={5,}|-{3,}|\*{3,})/',
	'emoji_ai' => '/🔥|NÂNG CẤP|ĐÃ SỬA|BÍ QUYẾT|FIX QUAN|BỔ SUNG|đồ án/i',
	'verbose_en' => '/This function|Ensure proper|AI-generated|ChatGPT|Cursor/i',
	'step_label' => '/Bước \d+|NGHIỆP VỤ \d+|PHẦN LOGIC/i',
	'section_crud' => '/^\s*\/\/\s*(CREATE|UPDATE|DELETE|GET INFO|TOTAL|SUM|GET ROW|GET LIST|CHECK EXISTS|WHERE|LIKE|ORDER|LIMIT)\s*$/i',
);

function walk($path, &$files)
{
	if (is_file($path)) {
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		if (in_array($ext, array('php', 'js'), true)) {
			$files[] = $path;
		}
		return;
	}
	if (!is_dir($path)) {
		return;
	}
	foreach (scandir($path) as $f) {
		if ($f === '.' || $f === '..') {
			continue;
		}
		walk($path . DIRECTORY_SEPARATOR . $f, $files);
	}
}

$files = array();
foreach ($dirs as $d) {
	if (is_file($d)) {
		$files[] = $d;
	} elseif (is_dir($d)) {
		walk($d, $files);
	}
}

$byPattern = array();
foreach ($patterns as $k => $_) {
	$byPattern[$k] = 0;
}

foreach ($files as $file) {
	if (preg_match('/\.min\.(js|css)$/', $file)) {
		continue;
	}
	if (strpos($file, 'vendor') !== false || strpos($file, 'ckeditor') !== false) {
		continue;
	}
	$lines = file($file);
	foreach ($lines as $line) {
		if (!preg_match('/^\s*(?:\/\/|\/\*|\*)/', $line)) {
			continue;
		}
		$total++;
		foreach ($patterns as $k => $re) {
			if (preg_match($re, $line)) {
				$byPattern[$k]++;
				$noise++;
				break;
			}
		}
	}
}

echo "comment_lines={$total}\n";
echo "noise_hits={$noise}\n";
foreach ($byPattern as $k => $v) {
	echo "{$k}={$v}\n";
}
