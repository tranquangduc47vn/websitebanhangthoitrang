<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class IntentParser {

	protected $handoffKeywords = array(
		'gặp nhân viên', 'gặp người thật', 'nói chuyện với nhân viên', 'chuyển nhân viên',
		'tư vấn viên', 'nhân viên hỗ trợ', 'gặp admin', 'live chat', 'chat với nhân viên',
		'khiếu nại', 'phàn nàn', 'hỗ trợ trực tiếp',
	);

	protected $orderKeywords = array(
		'đơn hàng', 'đơn của tôi', 'tra cứu đơn', 'trạng thái đơn', 'hủy đơn',
		'theo dõi đơn', 'đơn đặt', 'kiểm tra đơn',
	);

	protected $productKeywords = array(
		'sản phẩm', 'áo', 'quần', 'đầm', 'váy', 'chân váy', 'giày', 'giá', 'tiền',
		'mẫu mới', 'bán chạy', 'giảm giá', 'khuyến mãi', 'mặc', 'bộ', 'set', 'còn hàng', 'tìm',
	);

	protected $policyKeywords = array(
		'chính sách', 'đổi trả', 'vận chuyển', 'giao hàng', 'thanh toán', 'giờ làm',
		'giờ mở', 'giờ đóng', 'liên hệ', 'giới thiệu', 'cửa hàng ở đâu', 'địa chỉ',
		'hotline', 'số điện thoại',
	);

	public function parse($message)
	{
		$normalized = $this->normalize($message);

		if ($this->matchesAny($normalized, $this->handoffKeywords)) {
			return array('intent' => 'handoff', 'filters' => array());
		}
		if ($this->isExchangeQuery($normalized)) {
			return array('intent' => 'exchange', 'filters' => array());
		}
		if ($this->isSizeAdviceQuery($normalized)) {
			return array('intent' => 'size', 'filters' => $this->extractBodyMetrics($normalized));
		}
		if ($this->matchesAny($normalized, $this->orderKeywords)) {
			return array('intent' => 'order', 'filters' => array());
		}
		$productFilters = $this->extractProductFilters($normalized);
		if ($this->matchesAny($normalized, $this->productKeywords)
			|| $this->hasPriceMention($normalized)
			|| !empty($productFilters['color'])
			|| !empty($productFilters['name_keyword'])) {
			return array('intent' => 'product', 'filters' => $productFilters);
		}
		if ($this->matchesAny($normalized, $this->policyKeywords)) {
			return array('intent' => 'policy', 'filters' => array());
		}

		return array('intent' => 'general', 'filters' => array());
	}

	protected function normalize($text)
	{
		return mb_strtolower(trim((string) $text), 'UTF-8');
	}

	protected function matchesAny($haystack, array $needles)
	{
		foreach ($needles as $needle) {
			if (mb_strpos($haystack, $needle) !== false) {
				return true;
			}
		}
		return false;
	}

	protected function hasPriceMention($normalized)
	{
		return (bool) preg_match('/\d+\s*(k|nghìn|ngàn|tr|triệu|đ|vnd)\b/u', $normalized);
	}

	protected function extractProductFilters($normalized)
	{
		$filters = array();

		if (preg_match('/\bnam\b/u', $normalized) && mb_strpos($normalized, 'nữ') === false) {
			$filters['gender'] = 'nam';
		} elseif (mb_strpos($normalized, 'nữ') !== false) {
			$filters['gender'] = 'nu';
		}

		if (preg_match('/(\d[\d.,]*)\s*(k|nghìn|ngàn)\b/u', $normalized, $m)) {
			$num = (float) str_replace(array('.', ','), '', $m[1]);
			$filters['max_price'] = $num * 1000;
		} elseif (preg_match('/(\d+(?:[.,]\d+)?)\s*(tr|triệu)\b/u', $normalized, $m)) {
			$num = (float) str_replace(',', '.', $m[1]);
			$filters['max_price'] = $num * 1000000;
		} elseif (preg_match('/(\d{4,})\s*(đ|vnd)?\b/u', $normalized, $m)) {
			$filters['max_price'] = (float) $m[1];
		}

		if (mb_strpos($normalized, 'bán chạy') !== false || mb_strpos($normalized, 'hot') !== false) {
			$filters['sort'] = 'bestseller';
		} elseif (mb_strpos($normalized, 'mới') !== false) {
			$filters['sort'] = 'newest';
		} elseif (mb_strpos($normalized, 'giảm giá') !== false || mb_strpos($normalized, 'khuyến mãi') !== false) {
			$filters['sort'] = 'discount';
		}

		$categoryKeywords = array('chân váy', 'áo', 'quần', 'đầm', 'váy', 'giày', 'bộ');
		foreach ($categoryKeywords as $kw) {
			if (mb_strpos($normalized, $kw) !== false) {
				$filters['category_keyword'] = $kw;
				break;
			}
		}

		if (mb_strpos($normalized, 'bộ') !== false || mb_strpos($normalized, 'set') !== false) {
			$filters['name_keyword'] = 'bộ';
		}

		$color = $this->extractColor($normalized);
		if ($color !== '') {
			$filters['color'] = $color;
		}

		return $filters;
	}

	// Soft product filters from free-text query.
	public function productFilters($message)
	{
		return $this->extractProductFilters($this->normalize($message));
	}

	protected function extractColor($normalized)
	{
		if (preg_match('/màu\s+([a-zà-ỹ]+)/u', $normalized, $m)) {
			return trim($m[1]);
		}
		$colors = array(
			'đỏ', 'do', 'xanh', 'đen', 'den', 'trắng', 'trang', 'hồng', 'hong', 'vàng', 'vang',
			'nâu', 'nau', 'be', 'kem', 'xám', 'xam', 'ghi', 'tím', 'tim', 'cam', 'navy', 'burgundy',
		);
		foreach ($colors as $c) {
			if (mb_strpos($normalized, $c) !== false) {
				return $c;
			}
		}
		return '';
	}

	// Exchange/return size — not pre-purchase size advice.
	protected function isExchangeQuery($normalized)
	{
		$phrases = array(
			'đổi size', 'doi size', 'đổi cỡ', 'doi co', 'đổi sang size', 'đổi sang cỡ',
			'thay size', 'thay cỡ', 'đổi hàng', 'đổi mẫu', 'hoàn size', 'trả size', 'đổi lại size',
		);
		if ($this->matchesAny($normalized, $phrases)) {
			return true;
		}
		if (mb_strpos($normalized, 'đổi') !== false
			&& (mb_strpos($normalized, 'size') !== false || mb_strpos($normalized, 'cỡ') !== false)) {
			return true;
		}
		return false;
	}

	protected function isSizeAdviceQuery($normalized)
	{
		$sizeKeywords = array(
			'tư vấn size', 'bảng size', 'size nào', 'size gì', 'size bao nhiêu', 'size mấy',
			'mặc size', 'chọn size', 'lấy size', 'cỡ nào', 'cỡ gì', 'cỡ bao nhiêu', 'wear size',
		);
		if ($this->matchesAny($normalized, $sizeKeywords)) {
			return true;
		}
		if ($this->hasBodyMetrics($normalized)) {
			return true;
		}
		if (mb_strpos($normalized, 'size') !== false || mb_strpos($normalized, 'cỡ') !== false) {
			return (bool) preg_match('/\d/u', $normalized);
		}
		return false;
	}

	protected function hasBodyMetrics($normalized)
	{
		return $this->extractHeightCm($normalized) > 0 || $this->extractWeightKg($normalized) > 0;
	}

	protected function extractBodyMetrics($normalized)
	{
		$filters = array(
			'height_cm' => $this->extractHeightCm($normalized),
			'weight_kg' => $this->extractWeightKg($normalized),
		);
		if (preg_match('/\bnam\b/u', $normalized) && mb_strpos($normalized, 'nữ') === false) {
			$filters['gender'] = 'nam';
		} elseif (mb_strpos($normalized, 'nữ') !== false) {
			$filters['gender'] = 'nu';
		}
		return $filters;
	}

	protected function extractHeightCm($normalized)
	{
		if (preg_match('/(\d)\s*m\s*(\d{1,2})\b/u', $normalized, $m)) {
			return ((int) $m[1] * 100) + (int) $m[2];
		}
		if (preg_match('/\b1m(\d{2})\b/u', $normalized, $m)) {
			return 100 + (int) $m[1];
		}
		if (preg_match('/cao\s*(\d{2,3})\s*(cm)?\b/u', $normalized, $m)) {
			return (int) $m[1];
		}
		if (preg_match('/(\d{3})\s*cm\b/u', $normalized, $m)) {
			return (int) $m[1];
		}
		return 0;
	}

	protected function extractWeightKg($normalized)
	{
		if (preg_match('/(\d{2,3}(?:[.,]\d+)?)\s*(kg|ký|kilo|kilogram)\b/u', $normalized, $m)) {
			return (int) round((float) str_replace(',', '.', $m[1]));
		}
		if (preg_match('/nặng\s*(\d{2,3})\b/u', $normalized, $m)) {
			return (int) $m[1];
		}
		if (preg_match('/(\d{2,3})\s*ký\b/u', $normalized, $m)) {
			return (int) $m[1];
		}
		return 0;
	}
}
