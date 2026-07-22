<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Dashboard read-only queries; view: admin/home/index.php

if (!function_exists('admin_dashboard_resolve_period')) {
	function admin_dashboard_resolve_period($period, $from_date = '', $to_date = '')
	{
		$period = strtolower(trim((string) $period));
		$now = time();
		$today_start = strtotime('today', $now);
		$today_end = strtotime('tomorrow', $today_start) - 1;

		switch ($period) {
			case 'today':
				$start = $today_start;
				$end = $now;
				$label = 'Hôm nay';
				break;
			case '7d':
				$start = strtotime('-6 days', $today_start);
				$end = $now;
				$label = '7 ngày qua';
				break;
			case 'month':
				$start = strtotime(date('Y-m-01 00:00:00'));
				$end = $now;
				$label = 'Tháng này';
				break;
			case 'year':
				$start = strtotime(date('Y-01-01 00:00:00'));
				$end = $now;
				$label = 'Năm nay';
				break;
			case 'custom':
				$start = admin_dashboard_parse_date($from_date, $today_start);
				$end = admin_dashboard_parse_date($to_date, $today_end);
				if ($end < $start) {
					$tmp = $start;
					$start = $end;
					$end = $tmp;
				}
				$end = strtotime('23:59:59', $end);
				$label = date('d/m/Y', $start) . ' – ' . date('d/m/Y', $end);
				break;
			case '30d':
			default:
				$period = '30d';
				$start = strtotime('-29 days', $today_start);
				$end = $now;
				$label = '30 ngày qua';
				break;
		}

		$span = max(1, $end - $start + 1);
		$prev_end = $start - 1;
		$prev_start = $prev_end - $span + 1;

		return array(
			'key' => $period,
			'start' => (int) $start,
			'end' => (int) $end,
			'prev_start' => (int) $prev_start,
			'prev_end' => (int) $prev_end,
			'label' => $label,
		);
	}
}

if (!function_exists('admin_dashboard_parse_date')) {
	function admin_dashboard_parse_date($str, $fallback)
	{
		$str = trim((string) $str);
		if ($str === '') {
			return (int) $fallback;
		}
		$ts = strtotime($str);
		return $ts ? (int) $ts : (int) $fallback;
	}
}

if (!function_exists('admin_dashboard_pct_change')) {
	function admin_dashboard_pct_change($current, $previous)
	{
		$current = (float) $current;
		$previous = (float) $previous;
		if ($previous <= 0) {
			return $current > 0 ? 100.0 : 0.0;
		}
		return round((($current - $previous) / $previous) * 100, 1);
	}
}

if (!function_exists('admin_dashboard_kpi_delta_html')) {
	function admin_dashboard_kpi_delta_html($pct)
	{
		$pct = (float) $pct;
		if ($pct > 0) {
			return '<span class="adm-dash-kpi__delta adm-dash-kpi__delta--up"><i class="fa-solid fa-arrow-trend-up"></i> +' . number_format($pct, 1, ',', '.') . '%</span>';
		}
		if ($pct < 0) {
			return '<span class="adm-dash-kpi__delta adm-dash-kpi__delta--down"><i class="fa-solid fa-arrow-trend-down"></i> ' . number_format($pct, 1, ',', '.') . '%</span>';
		}
		return '<span class="adm-dash-kpi__delta adm-dash-kpi__delta--flat"><i class="fa-solid fa-minus"></i> 0%</span>';
	}
}

if (!function_exists('admin_dashboard_get_data')) {
	function admin_dashboard_get_data(array $input = array())
	{
		$CI =& get_instance();
		$CI->load->database();
		$CI->load->helper('admin');

		$period = isset($input['period']) ? $input['period'] : '30d';
		$from = isset($input['from']) ? $input['from'] : '';
		$to = isset($input['to']) ? $input['to'] : '';
		$range = admin_dashboard_resolve_period($period, $from, $to);
		$low_stock_threshold = 10;

		$kpis = admin_dashboard_build_kpis($range, $low_stock_threshold);
		$charts = admin_dashboard_build_charts($range);
		$top_products = admin_dashboard_top_products($range, 8);
		$pending_orders = admin_dashboard_pending_orders(12);
		$top_customers = admin_dashboard_top_customers($range, 6);
		$top_categories = admin_dashboard_top_categories($range, 6);
		$low_stock = admin_dashboard_low_stock($low_stock_threshold, 8);
		$notifications = admin_dashboard_notifications($range, $low_stock_threshold);

		return array(
			'range' => $range,
			'low_stock_threshold' => $low_stock_threshold,
			'kpis' => $kpis,
			'charts' => $charts,
			'top_products' => $top_products,
			'pending_orders' => $pending_orders,
			'top_customers' => $top_customers,
			'top_categories' => $top_categories,
			'low_stock' => $low_stock,
			'notifications' => $notifications,
			'filter_query' => admin_dashboard_filter_query($range, $from, $to),
		);
	}
}

if (!function_exists('admin_dashboard_filter_query')) {
	function admin_dashboard_filter_query($range, $from, $to)
	{
		$q = array('period' => $range['key']);
		if ($range['key'] === 'custom') {
			if ($from !== '') {
				$q['from'] = $from;
			}
			if ($to !== '') {
				$q['to'] = $to;
			}
		}
		return http_build_query($q);
	}
}

if (!function_exists('admin_dashboard_tx_between')) {
	function admin_dashboard_tx_between($start, $end)
	{
		return array(
			'created >= ' => (int) $start,
			'created <= ' => (int) $end,
		);
	}
}

if (!function_exists('admin_dashboard_build_kpis')) {
	function admin_dashboard_build_kpis($range, $low_stock_threshold)
	{
		$CI =& get_instance();
		$s = $range['start'];
		$e = $range['end'];
		$ps = $range['prev_start'];
		$pe = $range['prev_end'];

		$orders_primary = admin_dashboard_count_tx($s, $e, null);
		$orders_primary_prev = admin_dashboard_count_tx($ps, $pe, null);

		$revenue_primary = admin_dashboard_sum_revenue($s, $e);
		$revenue_primary_prev = admin_dashboard_sum_revenue($ps, $pe);

		$orders_label = 'Đơn hàng';
		$revenue_label = 'Doanh thu';
		$period_hint = 'Trong ' . $range['label'];

		$pending = admin_dashboard_count_tx($s, $e, array('0', '1'));
		$pending_prev = admin_dashboard_count_tx($ps, $pe, array('0', '1'));

		$shipping = admin_dashboard_count_tx($s, $e, array('2'));
		$shipping_prev = admin_dashboard_count_tx($ps, $pe, array('2'));

		$completed = admin_dashboard_count_tx($s, $e, array('3'));
		$completed_prev = admin_dashboard_count_tx($ps, $pe, array('3'));

		$cancelled = admin_dashboard_count_tx($s, $e, array('4'));
		$cancelled_prev = admin_dashboard_count_tx($ps, $pe, array('4'));

		$new_users = admin_dashboard_count_users($s, $e);
		$new_users_prev = admin_dashboard_count_users($ps, $pe);

		$low_stock = admin_dashboard_count_low_stock($low_stock_threshold);

		return array(
			array('key' => 'orders_today', 'label' => $orders_label, 'value' => $orders_primary, 'delta' => admin_dashboard_pct_change($orders_primary, $orders_primary_prev), 'format' => 'int', 'hint' => $period_hint),
			array('key' => 'revenue_today', 'label' => $revenue_label, 'value' => $revenue_primary, 'delta' => admin_dashboard_pct_change($revenue_primary, $revenue_primary_prev), 'format' => 'money', 'hint' => $period_hint),
			array('key' => 'pending', 'label' => 'Đơn chờ xử lý', 'value' => $pending, 'delta' => admin_dashboard_pct_change($pending, $pending_prev), 'format' => 'int', 'hint' => 'Trong ' . $range['label']),
			array('key' => 'shipping', 'label' => 'Đơn đang giao', 'value' => $shipping, 'delta' => admin_dashboard_pct_change($shipping, $shipping_prev), 'format' => 'int', 'hint' => 'Trong ' . $range['label']),
			array('key' => 'completed', 'label' => 'Đơn hoàn thành', 'value' => $completed, 'delta' => admin_dashboard_pct_change($completed, $completed_prev), 'format' => 'int', 'hint' => 'Trong ' . $range['label']),
			array('key' => 'cancelled', 'label' => 'Đơn đã hủy', 'value' => $cancelled, 'delta' => admin_dashboard_pct_change($cancelled, $cancelled_prev), 'format' => 'int', 'hint' => 'Trong ' . $range['label']),
			array('key' => 'new_users', 'label' => 'Khách hàng mới', 'value' => $new_users, 'delta' => admin_dashboard_pct_change($new_users, $new_users_prev), 'format' => 'int', 'hint' => 'Trong ' . $range['label']),
			array('key' => 'low_stock', 'label' => 'Sản phẩm sắp hết hàng', 'value' => $low_stock, 'delta' => 0, 'format' => 'int', 'hint' => 'Tồn ≤ ' . (int) $low_stock_threshold),
		);
	}
}

if (!function_exists('admin_dashboard_count_tx')) {
	function admin_dashboard_count_tx($start, $end, $statuses = null)
	{
		$CI =& get_instance();
		$CI->db->where(admin_dashboard_tx_between($start, $end));
		if ($statuses !== null) {
			$CI->db->where_in('status', $statuses);
		}
		return (int) $CI->db->count_all_results('transaction');
	}
}

if (!function_exists('admin_dashboard_sum_revenue')) {
	function admin_dashboard_sum_revenue($start, $end)
	{
		$CI =& get_instance();
		$row = $CI->db
			->select_sum('amount')
			->where(admin_dashboard_tx_between($start, $end))
			->where('status', '3')
			->get('transaction')
			->row();
		return !empty($row->amount) ? (int) $row->amount : 0;
	}
}

if (!function_exists('admin_dashboard_count_users')) {
	function admin_dashboard_count_users($start, $end)
	{
		$CI =& get_instance();
		$CI->db->where('created >=', (int) $start);
		$CI->db->where('created <=', (int) $end);
		return (int) $CI->db->count_all_results('user');
	}
}

if (!function_exists('admin_dashboard_count_low_stock')) {
	function admin_dashboard_count_low_stock($threshold)
	{
		$CI =& get_instance();
		$CI->db->where('quantity <=', (int) $threshold);
		return (int) $CI->db->count_all_results('product');
	}
}

if (!function_exists('admin_dashboard_build_charts')) {
	function admin_dashboard_build_charts($range)
	{
		$labels = array();
		$revenue = array();
		$orders = array();

		$day_span = (int) floor(($range['end'] - $range['start']) / 86400) + 1;
		if ($day_span <= 45) {
			for ($ts = strtotime('midnight', $range['start']); $ts <= $range['end']; $ts += 86400) {
				$day_end = min($ts + 86399, $range['end']);
				$labels[] = date('d/m', $ts);
				$revenue[] = admin_dashboard_sum_revenue($ts, $day_end);
				$orders[] = admin_dashboard_count_tx($ts, $day_end, null);
			}
		} else {
			$cursor = $range['start'];
			while ($cursor <= $range['end']) {
				$chunk_end = min($cursor + (6 * 86400), $range['end']);
				$labels[] = date('d/m', $cursor);
				$revenue[] = admin_dashboard_sum_revenue($cursor, $chunk_end);
				$orders[] = admin_dashboard_count_tx($cursor, $chunk_end, null);
				$cursor = $chunk_end + 1;
			}
		}

		$status_counts = array(
			'0' => admin_dashboard_count_tx($range['start'], $range['end'], array('0')),
			'1' => admin_dashboard_count_tx($range['start'], $range['end'], array('1')),
			'2' => admin_dashboard_count_tx($range['start'], $range['end'], array('2')),
			'3' => admin_dashboard_count_tx($range['start'], $range['end'], array('3')),
			'4' => admin_dashboard_count_tx($range['start'], $range['end'], array('4')),
		);

		return array(
			'labels' => $labels,
			'revenue' => $revenue,
			'orders' => $orders,
			'status_pie' => array(
				'labels' => array('Mới đặt', 'Đã xác nhận', 'Đang giao', 'Hoàn thành', 'Đã hủy'),
				'values' => array_values($status_counts),
			),
		);
	}
}

if (!function_exists('admin_dashboard_top_products')) {
	function admin_dashboard_top_products($range, $limit = 8)
	{
		$CI =& get_instance();
		$limit = (int) $limit;
		$sql = "
			SELECT p.id, p.name, p.image_link, p.price, p.quantity,
				SUM(o.qty) AS total_sold,
				SUM(o.amount) AS total_amount
			FROM `order` o
			INNER JOIN `product` p ON o.product_id = p.id
			INNER JOIN `transaction` t ON o.transaction_id = t.id
			WHERE t.status = '3'
				AND t.created >= ? AND t.created <= ?
			GROUP BY p.id, p.name, p.image_link, p.price, p.quantity
			ORDER BY total_sold DESC
			LIMIT {$limit}
		";
		$rows = $CI->db->query($sql, array($range['start'], $range['end']))->result();

		$span = max(1, $range['end'] - $range['start'] + 1);
		$prev_start = $range['prev_start'];
		$prev_end = $range['prev_end'];

		foreach ($rows as $row) {
			$prev = $CI->db->query("
				SELECT COALESCE(SUM(o.qty), 0) AS sold
				FROM `order` o
				INNER JOIN `transaction` t ON o.transaction_id = t.id
				WHERE t.status = '3' AND o.product_id = ?
					AND t.created >= ? AND t.created <= ?
			", array($row->id, $prev_start, $prev_end))->row();
			$prev_sold = $prev ? (int) $prev->sold : 0;
			$row->trend_pct = admin_dashboard_pct_change((int) $row->total_sold, $prev_sold);
		}
		return $rows;
	}
}

if (!function_exists('admin_dashboard_pending_orders')) {
	function admin_dashboard_pending_orders($limit = 12)
	{
		$CI =& get_instance();
		return $CI->db
			->select('id, user_name, user_phone, amount, status, created')
			->from('transaction')
			->where_in('status', array('0', '1'))
			->order_by('id', 'DESC')
			->limit((int) $limit)
			->get()
			->result();
	}
}

if (!function_exists('admin_dashboard_top_customers')) {
	function admin_dashboard_top_customers($range, $limit = 6)
	{
		$CI =& get_instance();
		$limit = (int) $limit;
		$sql = "
			SELECT user_id, user_name, user_email, user_phone,
				COUNT(*) AS order_count,
				SUM(amount) AS total_spent
			FROM `transaction`
			WHERE status = '3'
				AND created >= ? AND created <= ?
				AND user_id > 0
			GROUP BY user_id, user_name, user_email, user_phone
			ORDER BY total_spent DESC
			LIMIT {$limit}
		";
		return $CI->db->query($sql, array($range['start'], $range['end']))->result();
	}
}

if (!function_exists('admin_dashboard_top_categories')) {
	function admin_dashboard_top_categories($range, $limit = 6)
	{
		$CI =& get_instance();
		$limit = (int) $limit;
		$sql = "
			SELECT c.id, c.name,
				SUM(o.qty) AS total_sold,
				SUM(o.amount) AS total_amount
			FROM `order` o
			INNER JOIN `product` p ON o.product_id = p.id
			INNER JOIN `catalog` c ON p.catalog_id = c.id
			INNER JOIN `transaction` t ON o.transaction_id = t.id
			WHERE t.status = '3'
				AND t.created >= ? AND t.created <= ?
			GROUP BY c.id, c.name
			ORDER BY total_sold DESC
			LIMIT {$limit}
		";
		return $CI->db->query($sql, array($range['start'], $range['end']))->result();
	}
}

if (!function_exists('admin_dashboard_low_stock')) {
	function admin_dashboard_low_stock($threshold, $limit = 8)
	{
		$CI =& get_instance();
		return $CI->db
			->select('id, name, image_link, quantity, price')
			->from('product')
			->where('quantity <=', (int) $threshold)
			->order_by('quantity', 'ASC')
			->limit((int) $limit)
			->get()
			->result();
	}
}

if (!function_exists('admin_dashboard_notifications')) {
	function admin_dashboard_notifications($range, $low_stock_threshold)
	{
		$CI =& get_instance();

		$new_orders = $CI->db
			->select('id, user_name, amount, created')
			->from('transaction')
			->where('status', '0')
			->order_by('id', 'DESC')
			->limit(5)
			->get()
			->result();

		$stock_items = $CI->db
			->select('id, name, quantity')
			->from('product')
			->where('quantity <=', (int) $low_stock_threshold)
			->order_by('quantity', 'ASC')
			->limit(5)
			->get()
			->result();

		$review_products = $CI->db
			->select('id, name, rate_count, rate_total')
			->from('product')
			->where('rate_count >', 0)
			->order_by('rate_count', 'DESC')
			->limit(5)
			->get()
			->result();

		$new_users = $CI->db
			->select('id, name, email, created')
			->from('user')
			->where('created >=', (int) $range['start'])
			->where('created <=', (int) $range['end'])
			->order_by('id', 'DESC')
			->limit(5)
			->get()
			->result();

		return array(
			'new_orders' => $new_orders,
			'low_stock' => $stock_items,
			'reviews' => $review_products,
			'new_contacts' => $new_users,
		);
	}
}

if (!function_exists('admin_dashboard_notification_groups')) {
	function admin_dashboard_notification_groups(array $dash, array $perm, $login = null)
	{
		$CI =& get_instance();
		$CI->load->helper(array('admin', 'permission'));
		if ($login === null) {
			$login = $CI->session->userdata('login');
		}

		$range = isset($dash['range']) ? $dash['range'] : admin_dashboard_resolve_period('30d', '', '');
		$threshold = isset($dash['low_stock_threshold']) ? (int) $dash['low_stock_threshold'] : 10;
		$now = time();

		$groups = array();

		if (!empty($perm['orders'])) {
			$pending_new = (int) $CI->db->where('status', '0')->count_all_results('transaction');
			$shipping = (int) $CI->db->where('status', '2')->count_all_results('transaction');
			$failed = (int) $CI->db->where('status', '4')->count_all_results('transaction');
			$badge = $pending_new + $shipping + $failed;
			$lines = array(
				admin_dashboard_notify_summary_line(
					$pending_new,
					'Không có đơn mới cần xác nhận.',
					'Có %s đơn mới cần xác nhận.'
				),
				admin_dashboard_notify_summary_line(
					$shipping,
					'Không có đơn đang giao.',
					'Có %s đơn đang giao.'
				),
				admin_dashboard_notify_summary_line(
					$failed,
					'Không có đơn giao thất bại / hủy cần xem.',
					'Có %s đơn giao thất bại hoặc đã hủy.'
				),
			);
			$groups[] = array(
				'id' => 'orders',
				'priority' => 10,
				'title' => 'Đơn hàng',
				'icon' => 'fa-solid fa-cart-shopping',
				'tone' => ($pending_new > 0 || $failed > 0) ? 'danger' : ($shipping > 0 ? 'warning' : 'success'),
				'badge' => $badge,
				'lines' => $lines,
				'action_label' => $badge > 0 ? 'Xử lý' : 'Xem chi tiết',
				'action_url' => admin_url('orders'),
			);
		}

		if (!empty($perm['inventory'])) {
			$low_count = admin_dashboard_count_low_stock($threshold);
			$out_count = (int) $CI->db->where('quantity', 0)->count_all_results('product');
			$badge = $low_count + $out_count;
			$lines = array(
				admin_dashboard_notify_summary_line(
					$low_count,
					'Không có sản phẩm sắp hết hàng.',
					'Có %s sản phẩm sắp hết hàng (≤ ' . $threshold . ').'
				),
				admin_dashboard_notify_summary_line(
					$out_count,
					'Không có sản phẩm hết hàng.',
					'Có %s sản phẩm đã hết hàng.'
				),
			);
			$groups[] = array(
				'id' => 'inventory',
				'priority' => 20,
				'title' => 'Kho hàng',
				'icon' => 'fa-solid fa-boxes-stacked',
				'tone' => $out_count > 0 ? 'danger' : ($low_count > 0 ? 'warning' : 'success'),
				'badge' => $badge,
				'lines' => $lines,
				'action_label' => $badge > 0 ? 'Xử lý' : 'Xem chi tiết',
				'action_url' => admin_url('inventory'),
			);
		}

		$show_customers = !empty($perm['products']) || !empty($perm['users']);
		if ($show_customers) {
			$review_pending = 0;
			if (!empty($perm['products'])) {
				$review_pending = (int) $CI->db->where('rate_count >', 0)->count_all_results('product');
			}
			$new_contacts = 0;
			if (!empty($perm['users'])) {
				$new_contacts = (int) $CI->db
					->where('created >=', (int) $range['start'])
					->where('created <=', (int) $range['end'])
					->count_all_results('user');
			}
			$returns = 0;
			$badge = $review_pending + $new_contacts + $returns;
			$lines = array();
			if (!empty($perm['products'])) {
				$lines[] = admin_dashboard_notify_summary_line(
					$review_pending,
					'Không có đánh giá mới cần duyệt.',
					'Có %s sản phẩm có đánh giá cần duyệt.'
				);
			}
			if (!empty($perm['users'])) {
				$lines[] = admin_dashboard_notify_summary_line(
					$new_contacts,
					'Không có liên hệ / khách mới trong kỳ.',
					'Có %s liên hệ / khách mới trong ' . $range['label'] . '.'
				);
			}
			$lines[] = admin_dashboard_notify_summary_line(
				$returns,
				'Không có yêu cầu đổi trả mới.',
				'Có %s yêu cầu đổi trả cần xử lý.'
			);
			$action_url = !empty($perm['users']) ? admin_url('users') : admin_url('products');
			$groups[] = array(
				'id' => 'customers',
				'priority' => 40,
				'title' => 'Khách hàng',
				'icon' => 'fa-solid fa-users',
				'tone' => $badge > 0 ? 'info' : 'success',
				'badge' => $badge,
				'lines' => $lines,
				'action_label' => ($review_pending + $returns) > 0 ? 'Xử lý' : 'Xem chi tiết',
				'action_url' => $action_url,
			);
		}

		if (!empty($perm['voucher']) && $CI->db->table_exists('voucher')) {
			$soon_7 = $now + (7 * 86400);
			$soon_30 = $now + (30 * 86400);
			$voucher_soon = (int) $CI->db
				->where('is_active', 1)
				->where('valid_to >', 0)
				->where('valid_to >=', $now)
				->where('valid_to <=', $soon_7)
				->count_all_results('voucher');
			$program_soon = (int) $CI->db
				->where('is_active', 1)
				->where('valid_to >', 0)
				->where('valid_to >', $soon_7)
				->where('valid_to <=', $soon_30)
				->count_all_results('voucher');
			$badge = $voucher_soon + $program_soon;
			$lines = array(
				admin_dashboard_notify_summary_line(
					$voucher_soon,
					'Không có voucher sắp hết hạn.',
					'Có %s voucher hết hạn trong 7 ngày tới.'
				),
				admin_dashboard_notify_summary_line(
					$program_soon,
					'Không có chương trình khuyến mãi sắp kết thúc.',
					'Có %s chương trình / voucher sắp kết thúc (30 ngày).'
				),
			);
			$groups[] = array(
				'id' => 'promotions',
				'priority' => 50,
				'title' => 'Khuyến mãi',
				'icon' => 'fa-solid fa-tags',
				'tone' => $voucher_soon > 0 ? 'warning' : ($program_soon > 0 ? 'info' : 'success'),
				'badge' => $badge,
				'lines' => $lines,
				'action_label' => $badge > 0 ? 'Xử lý' : 'Xem chi tiết',
				'action_url' => admin_url('voucher'),
			);
		}

		if (admin_can('panel.home', $login)) {
			$backup_line = admin_dashboard_notify_backup_line();
			$sys_ok = ($backup_line['ok'] !== false);
			$groups[] = array(
				'id' => 'system',
				'priority' => 30,
				'title' => 'Hệ thống',
				'icon' => 'fa-solid fa-server',
				'tone' => $sys_ok ? 'success' : 'warning',
				'badge' => $sys_ok ? 0 : 1,
				'lines' => array(
					$backup_line['text'],
					$sys_ok
						? 'Trạng thái hệ thống: hoạt động bình thường.'
						: 'Trạng thái hệ thống: cần kiểm tra backup.',
				),
				'action_label' => 'Xem chi tiết',
				'action_url' => admin_url('store'),
			);
		}

		usort($groups, function ($a, $b) {
			if ($a['priority'] === $b['priority']) {
				return 0;
			}
			return ($a['priority'] < $b['priority']) ? -1 : 1;
		});

		return $groups;
	}
}

if (!function_exists('admin_dashboard_notify_summary_line')) {
	function admin_dashboard_notify_summary_line($count, $zero_text, $nonzero_template)
	{
		$count = (int) $count;
		if ($count <= 0) {
			return $zero_text;
		}
		return sprintf($nonzero_template, number_format($count, 0, ',', '.'));
	}
}

if (!function_exists('admin_dashboard_notify_backup_line')) {
	function admin_dashboard_notify_backup_line()
	{
		$candidates = array(
			FCPATH . 'backup',
			FCPATH . 'backups',
			APPPATH . 'backups',
		);
		$latest = 0;
		foreach ($candidates as $dir) {
			if (!is_dir($dir)) {
				continue;
			}
			foreach (array('*.sql', '*.zip', '*.gz', '*.tar') as $pattern) {
				$files = glob($dir . DIRECTORY_SEPARATOR . $pattern);
				if (!is_array($files)) {
					continue;
				}
				foreach ($files as $file) {
					if (!is_file($file)) {
						continue;
					}
					$m = @filemtime($file);
					if ($m && $m > $latest) {
						$latest = $m;
					}
				}
			}
		}
		if ($latest <= 0) {
			return array(
				'ok' => null,
				'text' => 'Backup gần nhất: chưa có file backup trên máy chủ.',
			);
		}
		$days = (int) floor((time() - $latest) / 86400);
		$date = date('d/m/Y H:i', $latest);
		if ($days > 7) {
			return array(
				'ok' => false,
				'text' => 'Backup gần nhất: ' . $date . ' (cách đây ' . $days . ' ngày).',
			);
		}
		return array(
			'ok' => true,
			'text' => 'Backup gần nhất: ' . $date . '.',
		);
	}
}

if (!function_exists('admin_dashboard_format_kpi')) {
	function admin_dashboard_format_kpi($value, $format)
	{
		if ($format === 'money') {
			return number_format((int) $value, 0, ',', '.') . ' ₫';
		}
		return number_format((int) $value, 0, ',', '.');
	}
}

if (!function_exists('admin_dashboard_is_staff')) {
	function admin_dashboard_is_staff($login)
	{
		if (!$login || !isset($login->level)) {
			return false;
		}
		$level = (int) $login->level;
		return $level === ROLE_ADMIN || $level === ROLE_MOD;
	}
}

// Dashboard section permissions — aligned with admin_sidebar_allowed / admin_can.
if (!function_exists('admin_dashboard_section_allowed')) {
	function admin_dashboard_section_allowed($section, $login = null)
	{
		$CI =& get_instance();
		$CI->load->helper('permission');
		if ($login === null) {
			$login = $CI->session->userdata('login');
		}
		if (!$login) {
			return false;
		}

		switch ($section) {
			case 'orders':
				return admin_can('order.manage', $login);
			case 'revenue':
				return admin_can('order.manage', $login) && admin_dashboard_is_staff($login);
			case 'products':
				return admin_sidebar_allowed('products', $login);
			case 'inventory':
				return admin_sidebar_allowed('inventory', $login);
			case 'users':
				return admin_sidebar_allowed('users', $login);
			case 'catalog':
				return admin_sidebar_allowed('catalog', $login);
			case 'voucher':
				return admin_sidebar_allowed('voucher', $login);
			case 'posts':
				return admin_sidebar_allowed('posts', $login);
			case 'slider':
				return admin_sidebar_allowed('slider', $login);
			case 'staff':
				return admin_sidebar_allowed('admin', $login);
			default:
				return false;
		}
	}
}

if (!function_exists('admin_dashboard_kpi_allowed')) {
	function admin_dashboard_kpi_allowed($key, $login = null)
	{
		$map = array(
			'orders_today' => 'orders',
			'revenue_today' => 'revenue',
			'pending' => 'orders',
			'shipping' => 'orders',
			'completed' => 'orders',
			'cancelled' => 'orders',
			'new_users' => 'users',
			'low_stock' => 'inventory',
		);
		$section = isset($map[$key]) ? $map[$key] : 'orders';
		return admin_dashboard_section_allowed($section, $login);
	}
}
