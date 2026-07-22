<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PolicyContextProvider {

	public function build()
	{
		$CI = get_instance();
		$db = $CI->db;
		$lines = array();

		if ($db->table_exists('stores')) {
			$store = $db->select('name, address, phone')->from('stores')->order_by('id', 'ASC')->limit(1)->get()->row();
			if ($store) {
				$line = 'Cửa hàng: ' . $store->name;
				if (!empty($store->address)) {
					$line .= ' - Địa chỉ: ' . $store->address;
				}
				if (!empty($store->phone)) {
					$line .= ' - Hotline: ' . $store->phone;
				}
				$lines[] = $line;
			}
		}

		$CI->load->model('ai_setting_model');
		$hours = $CI->ai_setting_model->get('working_hours_text', '');
		if ($hours !== '') {
			$lines[] = 'Giờ làm việc: ' . $hours;
		}

		if ($db->table_exists('van_chuyen')) {
			$vc = $db->select('title, intro')->from('van_chuyen')->limit(1)->get()->row();
			if ($vc) {
				$lines[] = 'Chính sách vận chuyển (' . $vc->title . '): ' . $this->plain($vc->intro, 400);
			}
		}

		if ($db->table_exists('pages')) {
			$page = $db->select('content')->from('pages')->where('slug', 'gioi-thieu')->limit(1)->get()->row();
			if ($page && !empty($page->content)) {
				$lines[] = 'Giới thiệu cửa hàng: ' . $this->plain($page->content, 400);
			}
		}

		$lines[] = 'Email hỗ trợ: marketing@jm.com.vn';
		$lines[] = 'Quy trình mua hàng trên website (3 bước): (1) Chọn sản phẩm phù hợp và thêm vào giỏ hàng; (2) Mở trang Giỏ hàng để kiểm tra sản phẩm, số lượng và cập nhật nếu cần; (3) Tiến hành Thanh toán — điền thông tin nhận hàng và chọn hình thức thanh toán (COD khi nhận hàng hoặc chuyển khoản/QR theo hướng dẫn).';
		$lines[] = 'Chính sách thanh toán: hỗ trợ thanh toán khi nhận hàng (COD) và chuyển khoản/QR khi đặt hàng.';
		$lines[] = 'Chính sách đổi trả: đổi trả trong 7 ngày kể từ khi nhận hàng nếu sản phẩm còn nguyên tem mác, chưa qua sử dụng.';
		$lines[] = $this->buildExchangeSizeGuide();
		$lines[] = $this->buildSizeGuide();

		return implode("\n", $lines);
	}

	// Post-purchase size exchange — not pre-purchase sizing.
	public function buildExchangeSizeGuide()
	{
		return 'Cách đổi size sau khi mua:'
			. "\n1) Trong vòng 7 ngày kể từ khi nhận hàng, sản phẩm còn nguyên tem mác và chưa qua sử dụng."
			. "\n2) Liên hệ nhân viên (chat widget hoặc hotline) và cung cấp mã đơn + size hiện tại + size muốn đổi."
			. "\n3) Nếu shop còn size cần đổi, nhân viên sẽ hướng dẫn gửi hàng đổi hoặc đổi tại cửa hàng."
			. "\n4) Phí vận chuyển đổi size (nếu có) sẽ được thông báo cụ thể khi xác nhận đơn đổi."
			. "\nLưu ý: câu hỏi \"đổi size\" là đổi hàng đã mua — khác với tư vấn chọn size trước khi mua.";
	}

	// Reference size chart for height/weight advice.
	public function buildSizeGuide()
	{
		return 'Bảng size tham khảo phổ biến (form regular — có thể chênh 1 size tùy kiểu dáng):'
			. "\n- Nữ: S (45–50 kg, 150–158 cm) | M (50–58 kg, 158–165 cm) | L (58–65 kg, 165–170 cm) | XL (65–72 kg, 170–175 cm)"
			. "\n- Nam: S (50–58 kg, 160–168 cm) | M (58–65 kg, 168–172 cm) | L (65–72 kg, 172–178 cm) | XL (72–80 kg, 178–185 cm)"
			. "\nGợi ý nhanh:"
			. "\n• 1m70, 54 kg → M (nữ) hoặc S–M (nam)"
			. "\n• 1m70, 65 kg → L (nam/nữ tùy form; nam ôm vừa thì M, rộng thì L)"
			. "\n• 1m65, 50 kg → S–M (nữ)"
			. "\nThích rộng/oversize → lên 1 size; thích ôm → xuống 1 size. Nếu có bảng size trên trang sản phẩm thì ưu tiên theo trang đó.";
	}

	protected function plain($html, $maxLen)
	{
		$text = trim(strip_tags((string) $html));
		$text = preg_replace('/\s+/u', ' ', $text);
		if (mb_strlen($text, 'UTF-8') > $maxLen) {
			$text = mb_substr($text, 0, $maxLen, 'UTF-8') . '...';
		}
		return $text;
	}
}
