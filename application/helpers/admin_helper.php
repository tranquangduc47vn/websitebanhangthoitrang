<?php 
	function admin_url($url='')
	{
		$parts = explode('/', ltrim($url, '/'));
		$canonical = array(
			'product' => 'products',
			'user' => 'users',
			'transaction' => 'orders',
			'news' => 'posts',
		);
		if (!empty($parts[0]) && isset($canonical[$parts[0]])) {
			$parts[0] = $canonical[$parts[0]];
			$url = implode('/', $parts);
		}
		return base_url('admin/'.$url);
	}

	// Admin header title from current URL; override with $admin_header_title in view.
	function admin_header_title()
	{
		$CI =& get_instance();
		$seg2 = $CI->uri->segment(2);
		$seg3 = $CI->uri->segment(3);

		if ($seg2 === '' || $seg2 === false) {
			$seg2 = 'home';
		}

		if ($seg2 === 'orders' && $seg3 === 'chat') {
			return 'Hỗ trợ khách hàng';
		}
		if ($seg2 === 'support-chat') {
			if ($seg3 === 'chat') {
				return 'Chat hỗ trợ khách hàng';
			}
			return 'Hỗ trợ khách hàng';
		}
		if ($seg2 === 'orders' && $seg3 === 'detail') {
			return 'Chi tiết đơn đặt hàng';
		}
		if ($seg2 === 'users' && $seg3 === 'order') {
			return 'Đơn đặt hàng khách hàng';
		}
		if ($seg2 === 'users' && $seg3 === 'detail') {
			return 'Chi tiết đơn đặt hàng';
		}
		if ($seg2 === 'report') {
			return 'Thống kê doanh thu & số lượng bán';
		}
		if ($seg2 === 'ai-assistant') {
			if ($seg3 === 'faq') {
				return 'Quản lý FAQ trợ lý AI';
			}
			if ($seg3 === 'conversations') {
				return 'Lịch sử hội thoại trợ lý AI';
			}
			return 'Cấu hình trợ lý AI';
		}

		$map = array(
			'home' => array(
				'default' => 'Tổng quan quản trị',
			),
			'catalog' => array(
				'default' => 'Quản lý danh mục',
				'add' => 'Thêm danh mục',
				'edit' => 'Sửa danh mục',
			),
			'product' => array(
				'default' => 'Quản lý sản phẩm',
				'add' => 'Thêm sản phẩm',
				'edit' => 'Chỉnh sửa sản phẩm',
			),
			'products' => array(
				'default' => 'Quản lý sản phẩm',
				'add' => 'Thêm sản phẩm',
				'edit' => 'Chỉnh sửa sản phẩm',
			),
			'inventory' => array(
				'default' => 'Quản lý tồn kho',
			),
			'slider' => array(
				'default' => 'Quản lý slider',
				'add' => 'Thêm slider',
				'edit' => 'Chỉnh sửa slider',
			),
			'transaction' => array(
				'default' => 'Quản lý đơn đặt hàng',
			),
			'orders' => array(
				'default' => 'Quản lý đơn đặt hàng',
			),
			'user' => array(
				'default' => 'Danh sách khách hàng',
			),
			'users' => array(
				'default' => 'Danh sách khách hàng',
			),
			'admin' => array(
				'default' => 'Danh sách quản trị viên',
				'add' => 'Thêm quản trị viên',
				'edit' => 'Sửa thông tin thành viên',
			),
			'news' => array(
				'default' => 'Quản lý tin tức',
				'add' => 'Thêm tin tức mới',
				'edit' => 'Chỉnh sửa bài viết',
			),
			'posts' => array(
				'default' => 'Quản lý tin tức',
				'add' => 'Thêm tin tức mới',
				'edit' => 'Chỉnh sửa bài viết',
			),
			'page' => array(
				'default' => 'Quản lý trang tĩnh',
				'add' => 'Thêm trang tĩnh mới',
				'edit' => 'Chỉnh sửa trang',
			),
			'pages' => array(
				'default' => 'Quản lý trang tĩnh',
				'add' => 'Thêm trang tĩnh mới',
				'edit' => 'Chỉnh sửa trang',
			),
			'banner' => array(
				'default' => 'Quản lý banner danh mục',
				'edit' => 'Chỉnh sửa banner',
			),
			'store' => array(
				'default' => 'Quản lý hệ thống cửa hàng',
				'add' => 'Thêm cửa hàng',
				'edit' => 'Sửa cửa hàng',
			),
			'tuyendung' => array(
				'default' => 'Quản lý tuyển dụng',
				'add' => 'Thêm vị trí tuyển dụng',
				'edit' => 'Chỉnh sửa tuyển dụng',
			),
			'hoptac' => array(
				'default' => 'Quản lý hợp tác kinh doanh',
				'add' => 'Thêm bài viết hợp tác',
				'edit' => 'Chỉnh sửa bài hợp tác',
			),
			'vanchuyen' => array(
				'default' => 'Chính sách vận chuyển',
				'add' => 'Thêm chính sách vận chuyển',
				'edit' => 'Sửa chính sách vận chuyển',
			),
			'voucher' => array(
				'default' => 'Quản lý voucher',
				'add' => 'Thêm voucher',
				'edit' => 'Sửa voucher',
			),
		);

		if (!isset($map[$seg2])) {
			return 'Webshop Admin';
		}

		$entry = $map[$seg2];
		if ($seg3 && isset($entry[$seg3])) {
			return $entry[$seg3];
		}

		return $entry['default'];
	}

	// Order status labels (0–4), shared admin + dashboard.
	function admin_order_status_text($status)
	{
		switch ((string) $status) {
			case '0':
				return 'Mới đặt';
			case '1':
				return 'Đã xác nhận';
			case '2':
				return 'Đang giao hàng';
			case '3':
				return 'Hoàn thành';
			case '4':
				return 'Đã hủy / Hoàn';
			default:
				return 'Không rõ';
		}
	}

	// Prevent browser cache on admin pages after logout.
	function admin_no_cache_headers()
	{
		$CI =& get_instance();
		if (!isset($CI->output)) {
			return;
		}
		$CI->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
		$CI->output->set_header('Pragma: no-cache');
		$CI->output->set_header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
	}
?>