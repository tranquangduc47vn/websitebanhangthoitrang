<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/ai/PolicyContextProvider.php';
require_once APPPATH . 'services/ai/ProductRecommender.php';
require_once APPPATH . 'services/ai/OrderLookupService.php';

class ShopKnowledgeProvider {

	public function buildBase($userId = 0)
	{
		$parts = array();

		$policy = new PolicyContextProvider();
		$parts[] = $policy->build();

		$catalog = $this->buildCatalogSummary();
		if ($catalog !== '') {
			$parts[] = $catalog;
		}

		$bestsellers = $this->buildBestsellers();
		if ($bestsellers !== '') {
			$parts[] = $bestsellers;
		}

		$faqs = $this->buildFaqSummary();
		if ($faqs !== '') {
			$parts[] = $faqs;
		}

		$vouchers = $this->buildVoucherSummary();
		if ($vouchers !== '') {
			$parts[] = $vouchers;
		}

		$userId = (int) $userId;
		if ($userId > 0) {
			$orders = $this->buildUserOrders($userId);
			if ($orders !== '') {
				$parts[] = $orders;
			}
		}

		return implode("\n\n", array_filter($parts));
	}

	public function searchProducts(array $filters, $limit = 5)
	{
		$recommender = new ProductRecommender();
		return $recommender->recommend($filters, $limit);
	}

	public function formatProductList(array $products, $title = 'Sản phẩm liên quan')
	{
		if (empty($products)) {
			return 'Không tìm thấy sản phẩm phù hợp trong dữ liệu hiện có.';
		}
		$lines = array($title . ':');
		foreach ($products as $p) {
			$line = '- ' . $p['name'] . ': ' . $p['price_fmt'] . ' (' . $p['url'] . ')';
			if (!empty($p['color'])) {
				$line .= ' — màu: ' . $p['color'];
			}
			$lines[] = $line;
		}
		return implode("\n", $lines);
	}

	protected function buildCatalogSummary()
	{
		$CI = get_instance();
		if (!$CI->db->table_exists('catalog')) {
			return '';
		}
		$rows = $CI->db->select('name')
			->from('catalog')
			->where('parent_id', 1)
			->order_by('sort_order', 'ASC')
			->limit(20)
			->get()
			->result();
		if (empty($rows)) {
			return '';
		}
		$names = array();
		foreach ($rows as $row) {
			$names[] = $row->name;
		}
		return 'Danh mục chính cửa hàng: ' . implode(', ', $names) . '.';
	}

	protected function buildBestsellers()
	{
		$CI = get_instance();
		$CI->config->load('ai', true);
		$limit = (int) $CI->config->item('ai_knowledge_bestseller_limit', 'ai');
		if ($limit <= 0) {
			$limit = 8;
		}
		$products = $this->searchProducts(array('sort' => 'bestseller'), $limit);
		return $this->formatProductList($products, 'Sản phẩm bán chạy / gợi ý');
	}

	protected function buildFaqSummary()
	{
		$CI = get_instance();
		if (!$CI->db->table_exists('ai_faq')) {
			return '';
		}
		$CI->load->model('ai_faq_model');
		$rows = $CI->ai_faq_model->get_active();
		if (empty($rows)) {
			return '';
		}
		$CI->config->load('ai', true);
		$max = (int) $CI->config->item('ai_knowledge_faq_limit', 'ai');
		if ($max <= 0) {
			$max = 30;
		}
		$lines = array('FAQ cửa hàng (tham khảo):');
		$count = 0;
		foreach ($rows as $faq) {
			if ($count >= $max) {
				break;
			}
			$answer = trim(strip_tags((string) $faq->answer));
			if (mb_strlen($answer, 'UTF-8') > 180) {
				$answer = mb_substr($answer, 0, 180, 'UTF-8') . '…';
			}
			$lines[] = '• Hỏi: ' . $faq->question . ' → Trả lời: ' . $answer;
			$count++;
		}
		return implode("\n", $lines);
	}

	protected function buildVoucherSummary()
	{
		$CI = get_instance();
		if (!$CI->db->table_exists('voucher')) {
			return '';
		}
		$now = time();
		$rows = $CI->db->select('code, name, description, discount_type, discount_value, min_order_amount, tier_min, valid_from, valid_to')
			->from('voucher')
			->where('is_active', 1)
			->where('user_id', 0)
			->group_start()
				->where('valid_from', 0)
				->or_where('valid_from <=', $now)
			->group_end()
			->group_start()
				->where('valid_to', 0)
				->or_where('valid_to >=', $now)
			->group_end()
			->order_by('id', 'DESC')
			->limit(10)
			->get()
			->result();
		if (empty($rows)) {
			return 'Khuyến mãi/voucher: hiện không có mã công khai trong hệ thống.';
		}
		$lines = array('Voucher / khuyến mãi đang hiệu lực (mã công khai):');
		foreach ($rows as $v) {
			$val = (int) $v->discount_value;
			$disc = ($v->discount_type === 'percent')
				? $val . '%'
				: number_format($val, 0, ',', '.') . ' ₫';
			$label = trim((string) $v->name);
			if ($label === '') {
				$label = trim((string) $v->description);
			}
			$extra = array();
			if ((int) $v->min_order_amount > 0) {
				$extra[] = 'đơn tối thiểu ' . number_format((int) $v->min_order_amount, 0, ',', '.') . ' ₫';
			}
			if (!empty($v->tier_min) && $v->tier_min !== 'member') {
				$extra[] = 'hạng ' . $v->tier_min . ' trở lên';
			}
			$suffix = empty($extra) ? '' : ' (' . implode(', ', $extra) . ')';
			$lines[] = '- Mã ' . $v->code . ': ' . $label . ' — giảm ' . $disc . $suffix;
		}
		return implode("\n", $lines);
	}

	protected function buildUserOrders($userId)
	{
		$lookup = new OrderLookupService();
		$orders = $lookup->recentOrdersForUser($userId, 5);
		if (empty($orders)) {
			return 'Đơn hàng của khách đang đăng nhập: chưa có đơn nào.';
		}
		$lines = array('Đơn hàng gần đây của khách đang đăng nhập:');
		foreach ($orders as $o) {
			$lines[] = '- Đơn #' . $o['id'] . ' ngày ' . $o['created_fmt'] . ': ' . $o['status_text'] . ', tổng ' . $o['amount_fmt'];
		}
		return implode("\n", $lines);
	}
}
