<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExportChartRenderer
{
	protected $fontRegular;
	protected $fontBold;

	public function __construct()
	{
		$this->fontRegular = $this->resolveFont(array(
			APPPATH . 'services/export/fonts/DejaVuSans.ttf',
			FCPATH . 'vendor/tecnickcom/tcpdf/fonts/dejavusans.ttf',
			'C:\\Windows\\Fonts\\arial.ttf',
			'/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
		));
		$this->fontBold = $this->resolveFont(array(
			APPPATH . 'services/export/fonts/DejaVuSans-Bold.ttf',
			FCPATH . 'vendor/tecnickcom/tcpdf/fonts/dejavusansb.ttf',
			'C:\\Windows\\Fonts\\arialbd.ttf',
			'/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
		));
	}

	protected function resolveFont(array $paths)
	{
		foreach ($paths as $p) {
			if (is_string($p) && $p !== '' && is_file($p)) {
				return $p;
			}
		}
		return '';
	}

	public function revenueChart($title, $periodLabel, array $labels, array $values)
	{
		if (!function_exists('imagecreatetruecolor')) {
			return '';
		}

		$w = 1000;
		$h = 400;
		$im = $this->canvas($w, $h);
		$grid = imagecolorallocate($im, 226, 232, 240);
		$gridMinor = imagecolorallocate($im, 241, 245, 249);
		$text = imagecolorallocate($im, 51, 65, 85);
		$muted = imagecolorallocate($im, 100, 116, 139);
		$line = imagecolorallocate($im, 31, 41, 55);
		$fill = imagecolorallocatealpha($im, 148, 163, 184, 108);

		$padL = 78;
		$padR = 28;
		$padT = 58;
		$padB = 62;
		$chartW = $w - $padL - $padR;
		$chartH = $h - $padT - $padB;

		$this->drawHeading($im, $title, $periodLabel, 20, 16, $text, $muted);

		$n = max(1, count($values));
		$max = 1.0;
		foreach ($values as $v) {
			$max = max($max, (float) $v);
		}

		$useMillion = ($max >= 2000000);
		$scale = $useMillion ? 1000000.0 : 1.0;
		$yUnit = $useMillion ? 'triệu đồng' : 'VNĐ';
		$niceMax = $this->niceCeil($max / $scale);
		if ($niceMax <= 0) {
			$niceMax = 1;
		}

		$this->drawText($im, 8, 90, 18, $padT + (int) ($chartH / 2), $muted, '(' . $yUnit . ')');

		$gridLines = 5;
		for ($g = 0; $g <= $gridLines; $g++) {
			$y = $padT + (int) ($chartH * $g / $gridLines);
			$col = ($g === $gridLines) ? $grid : $gridMinor;
			imageline($im, $padL, $y, $w - $padR, $y, $col);
			$val = $niceMax * (1 - $g / $gridLines) * $scale;
			$label = $this->formatAxisMoney($val, $useMillion);
			$this->drawText($im, 8, 0, $padL - 10, $y + 4, $muted, $label, false, 'right');
		}

		$stepX = $chartW / $n;
		if ($n <= 31) {
			for ($i = 0; $i < $n; $i++) {
				$x = $padL + (int) ($i * $stepX + $stepX / 2);
				imageline($im, $x, $padT, $x, $padT + $chartH, $gridMinor);
			}
		}

		$points = array();
		for ($i = 0; $i < $n; $i++) {
			$val = isset($values[$i]) ? (float) $values[$i] : 0.0;
			$x = $padL + (int) ($i * $stepX + $stepX / 2);
			$ratio = ($niceMax > 0) ? ($val / $scale) / $niceMax : 0;
			$y = $padT + $chartH - (int) ($ratio * ($chartH - 8));
			$points[] = array($x, $y, $val);
		}

		if (count($points) >= 2) {
			$poly = array();
			$poly[] = $points[0][0];
			$poly[] = $padT + $chartH;
			foreach ($points as $p) {
				$poly[] = $p[0];
				$poly[] = $p[1];
			}
			$poly[] = $points[count($points) - 1][0];
			$poly[] = $padT + $chartH;
			imagefilledpolygon($im, $poly, count($poly) / 2, $fill);
		}

		for ($i = 1; $i < count($points); $i++) {
			imageline($im, $points[$i - 1][0], $points[$i - 1][1], $points[$i][0], $points[$i][1], $line);
		}
		foreach ($points as $p) {
			imagefilledellipse($im, $p[0], $p[1], 8, 8, $line);
			imagefilledellipse($im, $p[0], $p[1], 4, 4, imagecolorallocate($im, 255, 255, 255));
		}

		if (!empty($points)) {
			$peak = $points[0];
			foreach ($points as $p) {
				if ($p[2] > $peak[2]) {
					$peak = $p;
				}
			}
			$lbl = 'Cao nhất: ' . $this->formatPointMoney($peak[2], $useMillion);
			$this->drawText($im, 8, 0, $peak[0], max($padT + 14, $peak[1] - 12), $text, $lbl, true, 'center');
			imageline($im, $peak[0], $peak[1] - 8, $peak[0], $peak[1] - 2, $text);
		}

		$maxXLabels = 12;
		$labelStep = max(1, (int) ceil($n / $maxXLabels));
		for ($i = 0; $i < $n; $i++) {
			if ($i % $labelStep !== 0 && $i !== $n - 1) {
				continue;
			}
			$x = $padL + (int) ($i * $stepX + $stepX / 2);
			$lbl = isset($labels[$i]) ? (string) $labels[$i] : '';
			if ($n > 20) {
				$this->drawText($im, 7, 45, $x, $padT + $chartH + 8, $muted, $lbl, false, 'center');
			} else {
				$this->drawText($im, 8, 0, $x, $padT + $chartH + 14, $muted, $lbl, false, 'center');
			}
		}

		imageline($im, $padL, $padT + $chartH, $w - $padR, $padT + $chartH, $grid);

		return $this->savePng($im, 'export_rev_');
	}

	public function statusDistributionChart($title, $periodLabel, array $labels, array $values)
	{
		if (!function_exists('imagecreatetruecolor')) {
			return '';
		}

		$w = 1000;
		$h = 330;
		$im = $this->canvas($w, $h);
		$text = imagecolorallocate($im, 51, 65, 85);
		$muted = imagecolorallocate($im, 100, 116, 139);
		$track = imagecolorallocate($im, 241, 245, 249);
		$border = imagecolorallocate($im, 226, 232, 240);

		$this->drawHeading($im, $title, $periodLabel, 20, 16, $text, $muted);

		$colors = $this->grayScaleColors();
		$total = 0;
		foreach ($values as $v) {
			$total += max(0, (int) $v);
		}
		$n = count($labels);
		if ($total <= 0) {
			$this->drawText($im, 10, 0, $w / 2, $h / 2, $muted, 'Không có đơn hàng trong kỳ', false, 'center');
			return $this->savePng($im, 'export_status_');
		}

		$labelX = 24;
		$barX = 205;
		$barW = 560;
		$valueX = 785;
		$ly = 78;
		$rowH = 42;
		$maxVal = max(1, max(array_map('intval', $values)));

		for ($i = 0; $i < $n; $i++) {
			$val = max(0, (int) (isset($values[$i]) ? $values[$i] : 0));
			$rgb = $colors[$i % count($colors)];
			$col = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
			$label = isset($labels[$i]) ? (string) $labels[$i] : '';
			$pct = $total > 0 ? round(($val / $total) * 100, 1) : 0;

			$barY = $ly + 14;
			$this->drawText($im, 9, 0, $labelX, $barY + 12, $text, $label, true);
			imagefilledrectangle($im, $barX, $barY, $barX + $barW, $barY + 16, $track);
			imagerectangle($im, $barX, $barY, $barX + $barW, $barY + 16, $border);
			if ($val > 0) {
				$bw = max(3, (int) ($barW * ($val / $maxVal)));
				imagefilledrectangle($im, $barX, $barY, $barX + $bw, $barY + 16, $col);
			}
			$stat = number_format($val, 0, ',', '.') . ' (' . number_format($pct, ($pct == (int) $pct ? 0 : 1), ',', '.') . '%)';
			$this->drawText($im, 9, 0, $valueX, $barY + 13, $text, $stat, true);
			$ly += $rowH;
		}

		return $this->savePng($im, 'export_status_');
	}

	public function topProductsBarChart($title, $periodLabel, array $rows)
	{
		if (!function_exists('imagecreatetruecolor')) {
			return '';
		}
		$w = 1000;
		$h = 360;
		$im = $this->canvas($w, $h);
		$text = imagecolorallocate($im, 51, 65, 85);
		$muted = imagecolorallocate($im, 100, 116, 139);
		$bar = imagecolorallocate($im, 71, 85, 105);
		$barTop = imagecolorallocate($im, 30, 41, 59);
		$track = imagecolorallocate($im, 241, 245, 249);
		$border = imagecolorallocate($im, 226, 232, 240);

		$this->drawHeading($im, $title, $periodLabel, 20, 16, $text, $muted);

		if (empty($rows)) {
			$this->drawText($im, 10, 0, $w / 2, $h / 2, $muted, 'Không có dữ liệu sản phẩm trong kỳ', false, 'center');
			return $this->savePng($im, 'export_top_');
		}

		$max = 1;
		foreach ($rows as $r) {
			$max = max($max, (int) $r['sold']);
		}
		$labelX = 24;
		$barX = 330;
		$barW = 470;
		$valueX = 820;
		$y = 72;
		$rowH = 26;
		$limit = min(10, count($rows));

		for ($i = 0; $i < $limit; $i++) {
			$r = $rows[$i];
			$name = $this->truncateText(isset($r['name']) ? $r['name'] : '', 34);
			$sold = (int) $r['sold'];
			$bw = $sold > 0 ? max(3, (int) ($barW * ($sold / $max))) : 0;
			$col = ($i === 0) ? $barTop : $bar;

			$this->drawText($im, 8, 0, $labelX, $y + 14, $text, ($i + 1) . '. ' . $name, $i === 0);
			imagefilledrectangle($im, $barX, $y + 4, $barX + $barW, $y + 18, $track);
			imagerectangle($im, $barX, $y + 4, $barX + $barW, $y + 18, $border);
			if ($bw > 0) {
				imagefilledrectangle($im, $barX, $y + 4, $barX + $bw, $y + 18, $col);
			}
			$this->drawText($im, 8, 0, $valueX, $y + 16, $text, number_format($sold, 0, ',', '.') . ' bán', true);
			$y += $rowH;
		}

		return $this->savePng($im, 'export_top_');
	}

	/** @deprecated Dashboard dùng revenueChart */
	public function lineChart($title, array $labels, array $values, $colorHex = '#2563eb')
	{
		return $this->revenueChart($title, '', $labels, $values);
	}

	/** @deprecated Dashboard dùng statusDistributionChart */
	public function pieChart($title, array $labels, array $values)
	{
		return $this->statusDistributionChart($title, '', $labels, $values);
	}

	public function barChart($title, array $labels, array $values, $colorHex = '#6366f1')
	{
		return $this->revenueChart($title, '', $labels, $values);
	}

	protected function statusColors()
	{
		return array(
			array(239, 68, 68),
			array(245, 158, 11),
			array(99, 102, 241),
			array(34, 197, 94),
			array(148, 163, 184),
		);
	}

	protected function grayScaleColors()
	{
		return array(
			array(30, 41, 59),
			array(71, 85, 105),
			array(100, 116, 139),
			array(148, 163, 184),
			array(203, 213, 225),
		);
	}

	protected function canvas($w, $h)
	{
		$im = imagecreatetruecolor($w, $h);
		$white = imagecolorallocate($im, 255, 255, 255);
		imagefilledrectangle($im, 0, 0, $w, $h, $white);
		return $im;
	}

	protected function drawHeading($im, $title, $subtitle, $x, $y, $titleColor, $subColor)
	{
		$this->drawText($im, 12, 0, $x, $y + 14, $titleColor, $title, true);
		if ($subtitle !== '') {
			$this->drawText($im, 9, 0, $x, $y + 32, $subColor, $subtitle, false);
		}
	}

	protected function drawText($im, $size, $angle, $x, $y, $color, $text, $bold = false, $align = 'left')
	{
		$text = (string) $text;
		$font = ($bold && $this->fontBold !== '') ? $this->fontBold : $this->fontRegular;
		if ($font === '' || !function_exists('imagettfbbox')) {
			imagestring($im, 2, (int) $x, (int) $y, $this->asciiFallback($text), $color);
			return;
		}
		$box = imagettfbbox($size, $angle, $font, $text);
		$tw = abs($box[4] - $box[0]);
		if ($align === 'center') {
			$x = $x - (int) ($tw / 2);
		} elseif ($align === 'right') {
			$x = $x - $tw;
		}
		imagettftext($im, $size, $angle, (int) $x, (int) $y, $color, $font, $text);
	}

	protected function asciiFallback($text)
	{
		if (function_exists('mb_substr')) {
			return mb_substr($text, 0, 40, 'UTF-8');
		}
		return substr($text, 0, 40);
	}

	protected function truncateText($text, $max)
	{
		$text = trim((string) $text);
		if (function_exists('mb_strlen') && function_exists('mb_substr')) {
			return mb_strlen($text, 'UTF-8') > $max ? mb_substr($text, 0, $max - 3, 'UTF-8') . '...' : $text;
		}
		return strlen($text) > $max ? substr($text, 0, $max - 3) . '...' : $text;
	}

	protected function niceCeil($value)
	{
		if ($value <= 0) {
			return 1;
		}
		$exp = pow(10, floor(log10($value)));
		$norm = $value / $exp;
		if ($norm <= 1) {
			$nice = 1;
		} elseif ($norm <= 2) {
			$nice = 2;
		} elseif ($norm <= 5) {
			$nice = 5;
		} else {
			$nice = 10;
		}
		return $nice * $exp;
	}

	protected function formatAxisMoney($amount, $useMillion)
	{
		$amount = (float) $amount;
		if ($useMillion) {
			$v = $amount / 1000000;
			if ($v >= 10) {
				return number_format($v, 0, ',', '.');
			}
			return number_format($v, 1, ',', '.');
		}
		if ($amount >= 1000000) {
			return number_format($amount / 1000000, 1, ',', '.') . ' tr';
		}
		if ($amount >= 1000) {
			return number_format($amount / 1000, 0, ',', '.') . 'k';
		}
		return number_format($amount, 0, ',', '.');
	}

	protected function formatPointMoney($amount, $useMillion)
	{
		$amount = (float) $amount;
		if ($useMillion) {
			return number_format($amount / 1000000, 1, ',', '.') . ' tr';
		}
		if ($amount >= 1000000000) {
			return number_format($amount / 1000000000, 1, ',', '.') . ' tỷ';
		}
		if ($amount >= 1000000) {
			return number_format($amount / 1000000, 1, ',', '.') . ' tr';
		}
		return number_format($amount, 0, ',', '.');
	}

	protected function savePng($im, $prefix)
	{
		$file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $prefix . uniqid('', true) . '.png';
		imagepng($im, $file);
		imagedestroy($im);
		return $file;
	}
}
