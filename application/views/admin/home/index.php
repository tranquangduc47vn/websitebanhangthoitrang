<?php
$CI =& get_instance();
$CI->load->helper(array('admin_dashboard', 'admin', 'permission'));

$period = $CI->input->get('period') ? $CI->input->get('period') : '30d';
$from = $CI->input->get('from') ? $CI->input->get('from') : '';
$to = $CI->input->get('to') ? $CI->input->get('to') : '';

$dash = admin_dashboard_get_data(array(
	'period' => $period,
	'from' => $from,
	'to' => $to,
));
$range = $dash['range'];
$login = isset($login) ? $login : $CI->session->userdata('login');

$dash_perm = array(
	'orders' => admin_dashboard_section_allowed('orders', $login),
	'revenue' => admin_dashboard_section_allowed('revenue', $login),
	'products' => admin_dashboard_section_allowed('products', $login),
	'inventory' => admin_dashboard_section_allowed('inventory', $login),
	'users' => admin_dashboard_section_allowed('users', $login),
	'catalog' => admin_dashboard_section_allowed('catalog', $login),
	'voucher' => admin_dashboard_section_allowed('voucher', $login),
	'posts' => admin_dashboard_section_allowed('posts', $login),
	'slider' => admin_dashboard_section_allowed('slider', $login),
	'staff' => admin_dashboard_section_allowed('staff', $login),
);
$dash_has_quick = $dash_perm['products'] || $dash_perm['voucher'] || $dash_perm['posts'] || $dash_perm['slider'] || $dash_perm['staff'];
$notify_groups = admin_dashboard_notification_groups($dash, $dash_perm, $login);
$dash_has_notify = count($notify_groups) > 0;

$admin_asset = base_url('assets/admin/');
$filter_base = admin_url('home');
$period_key = $range['key'];
?>

<link rel="stylesheet" href="<?php echo $admin_asset; ?>css/admin-dashboard.css?v=11">

<div class="adm-dash">
	<div class="adm-dash-hero">
		<div>
			<h2 class="adm-dash-hero__title">Tổng quan kinh doanh</h2>
			<p class="adm-dash-hero__sub">Theo dõi đơn hàng, doanh thu và tồn kho · <strong><?php echo htmlspecialchars($range['label'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
		</div>
		<form class="adm-dash-filter" method="get" action="<?php echo $filter_base; ?>">
			<div class="adm-dash-filter__pills" role="group" aria-label="Khoảng thời gian">
				<?php
				$pills = array(
					'today' => 'Hôm nay',
					'7d' => '7 ngày',
					'30d' => '30 ngày',
					'month' => 'Tháng này',
					'year' => 'Năm nay',
					'custom' => 'Tùy chọn',
				);
				foreach ($pills as $key => $label) {
					$checked = ($period_key === $key) ? ' checked' : '';
				?>
					<label>
						<input type="radio" name="period" value="<?php echo $key; ?>"<?php echo $checked; ?>>
						<span><?php echo $label; ?></span>
					</label>
				<?php } ?>
			</div>
			<div class="adm-dash-filter__custom" id="admDashCustomRange" <?php echo $period_key === 'custom' ? '' : 'hidden'; ?>>
				<label>Từ <input type="date" name="from" value="<?php echo htmlspecialchars($from, ENT_QUOTES, 'UTF-8'); ?>"></label>
				<label>Đến <input type="date" name="to" value="<?php echo htmlspecialchars($to, ENT_QUOTES, 'UTF-8'); ?>"></label>
				<button type="submit" class="btn btn-sm btn-primary">Áp dụng</button>
			</div>
			<?php if ($period_key !== 'custom') { ?>
				<noscript><button type="submit" class="btn btn-sm btn-outline-primary mt-2">Lọc</button></noscript>
			<?php } ?>
		</form>
	</div>

	<?php $this->load->helper('export'); admin_export_toolbar('dashboard'); ?>

	<div class="row g-3 mb-4">
		<?php foreach ($dash['kpis'] as $kpi) {
			if (!admin_dashboard_kpi_allowed($kpi['key'], $login)) {
				continue;
			}
		?>
			<div class="col-6 col-xl-3">
				<div class="adm-dash-kpi">
					<div class="adm-dash-kpi__label"><?php echo htmlspecialchars($kpi['label'], ENT_QUOTES, 'UTF-8'); ?></div>
					<div class="adm-dash-kpi__value"><?php echo admin_dashboard_format_kpi($kpi['value'], $kpi['format']); ?></div>
					<?php if (!empty($kpi['hint'])) { ?>
						<div class="adm-dash-kpi__hint"><?php echo htmlspecialchars($kpi['hint'], ENT_QUOTES, 'UTF-8'); ?></div>
					<?php } ?>
					<?php if ($kpi['key'] !== 'low_stock') {
						echo admin_dashboard_kpi_delta_html($kpi['delta']);
					} else { ?>
						<span class="adm-dash-kpi__delta adm-dash-kpi__delta--flat">Theo tồn hiện tại</span>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
	</div>

	<?php if ($dash_has_quick || $dash_has_notify) { ?>
	<div class="row g-3 mb-4">
		<?php if ($dash_has_quick) { ?>
		<div class="col-12">
			<div class="adm-dash-card adm-dash-card--quick">
				<div class="adm-dash-card__head"><i class="fa-solid fa-bolt text-warning me-2"></i>Thao tác nhanh</div>
				<div class="adm-dash-card__body">
					<div class="adm-dash-quick">
						<?php if ($dash_perm['products']) { ?>
							<a href="<?php echo admin_url('products/add'); ?>"><i class="fa-solid fa-shirt"></i>Thêm sản phẩm</a>
						<?php } ?>
						<?php if ($dash_perm['voucher']) { ?>
							<a href="<?php echo admin_url('voucher/add'); ?>"><i class="fa-solid fa-ticket"></i>Thêm voucher</a>
						<?php } ?>
						<?php if ($dash_perm['posts']) { ?>
							<a href="<?php echo admin_url('posts/add'); ?>"><i class="fa-solid fa-newspaper"></i>Thêm bài viết</a>
						<?php } ?>
						<?php if ($dash_perm['slider']) { ?>
							<a href="<?php echo admin_url('slider/add'); ?>"><i class="fa-solid fa-images"></i>Thêm slider</a>
						<?php } ?>
						<?php if ($dash_perm['staff']) { ?>
							<a href="<?php echo admin_url('admin/add'); ?>"><i class="fa-solid fa-user-plus"></i>Thêm nhân viên</a>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
		<?php if ($dash_has_notify) { ?>
		<div class="col-12">
			<div class="adm-dash-card adm-dash-card--notify">
				<div class="adm-dash-card__head"><i class="fa-solid fa-bell text-primary me-2"></i>Thông báo</div>
				<div class="adm-dash-card__body adm-dash-card__body--flush">
					<?php $this->load->view('admin/home/partials/notifications_feed', array('notify_groups' => $notify_groups)); ?>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
	<?php } ?>

	<?php if ($dash_perm['orders'] && $dash_perm['revenue']) { ?>
	<div class="row g-3 mb-4">
		<div class="col-12">
			<div class="adm-dash-card">
				<div class="adm-dash-card__head"><i class="fa-solid fa-chart-line text-primary me-2"></i>Doanh thu theo thời gian</div>
				<div class="adm-dash-card__body">
					<div class="adm-dash-chart"><canvas id="admChartRevenue" aria-label="Biểu đồ doanh thu"></canvas></div>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>

	<?php if ($dash_perm['orders'] && $dash_perm['products']) { ?>
	<div class="row g-3 mb-4">
		<div class="col-12">
			<div class="adm-dash-card">
				<div class="adm-dash-card__head"><i class="fa-solid fa-fire text-danger me-2"></i>Top sản phẩm bán chạy</div>
				<div class="adm-dash-card__body p-0">
					<?php if (!empty($dash['top_products'])) { ?>
						<div class="table-responsive adm-dash-table-wrap">
							<table class="table table-hover admin-table mb-0" id="dashboardTopProducts">
								<thead>
									<tr>
										<th>Sản phẩm</th>
										<th>Bán</th>
										<?php if ($dash_perm['revenue']) { ?><th>DT</th><?php } ?>
										<?php if ($dash_perm['inventory']) { ?><th>Tồn</th><?php } ?>
										<th>±</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($dash['top_products'] as $product) {
										$trend = (float) $product->trend_pct;
										$trend_class = $trend > 0 ? 'up' : ($trend < 0 ? 'down' : 'flat');
									?>
										<tr>
											<td>
												<div class="adm-dash-product">
													<img src="<?php echo base_url('upload/product/' . $product->image_link); ?>" alt="">
													<span><?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?></span>
												</div>
											</td>
											<td><?php echo number_format((int) $product->total_sold); ?></td>
											<?php if ($dash_perm['revenue']) { ?><td><?php echo number_format((int) $product->total_amount); ?></td><?php } ?>
											<?php if ($dash_perm['inventory']) { ?><td><?php echo (int) $product->quantity; ?></td><?php } ?>
											<td><span class="adm-dash-trend--<?php echo $trend_class; ?>"><?php echo ($trend > 0 ? '+' : '') . number_format($trend, 1, ',', '.'); ?>%</span></td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					<?php } else { ?>
						<div class="admin-empty p-4">Chưa có dữ liệu trong kỳ đã chọn.</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>

	<?php if ($dash_perm['orders']) { ?>
	<div class="row g-3 mb-4">
		<div class="<?php echo ($dash_perm['users'] || $dash_perm['catalog']) ? 'col-12 col-xl-7' : 'col-12'; ?>">
			<div class="adm-dash-card">
				<div class="adm-dash-card__head adm-dash-card__head--between">
					<span><i class="fa-solid fa-hourglass-half text-warning me-2"></i>Đơn cần xử lý</span>
					<a href="<?php echo admin_url('orders'); ?>" class="btn btn-sm btn-outline-primary">Tất cả đơn</a>
				</div>
				<div class="adm-dash-card__body">
					<?php if (!empty($dash['pending_orders'])) { ?>
						<div class="table-responsive adm-dash-table-wrap">
							<table class="table table-hover admin-table mb-0" id="dashboardPendingOrders">
								<thead>
									<tr>
										<th>Mã</th>
										<th>Khách</th>
										<th>SĐT</th>
										<?php if ($dash_perm['revenue']) { ?><th>Tiền</th><?php } ?>
										<th>TT</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($dash['pending_orders'] as $order) { ?>
										<tr>
											<td class="fw-semibold">#<?php echo (int) $order->id; ?></td>
											<td><?php echo htmlspecialchars($order->user_name, ENT_QUOTES, 'UTF-8'); ?></td>
											<td><?php echo htmlspecialchars($order->user_phone, ENT_QUOTES, 'UTF-8'); ?></td>
											<?php if ($dash_perm['revenue']) { ?><td><?php echo number_format((int) $order->amount, 0, ',', '.'); ?> ₫</td><?php } ?>
											<td><span class="admin-status-badge admin-status-<?php echo (int) $order->status; ?>"><?php echo admin_order_status_text($order->status); ?></span></td>
											<td class="text-nowrap">
												<a class="btn btn-sm btn-outline-secondary" href="<?php echo admin_url('orders/detail/' . (int) $order->id); ?>">Xem</a>
												<?php if ((string) $order->status === '0') { ?>
													<a class="btn btn-sm btn-primary" href="<?php echo admin_url('orders/accept/' . (int) $order->id); ?>">Xác nhận</a>
												<?php } ?>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					<?php } else { ?>
						<div class="admin-empty">Không có đơn mới hoặc chờ xác nhận.</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php if ($dash_perm['users'] || $dash_perm['catalog']) { ?>
		<div class="col-12 col-xl-5 adm-dash-col-stack">
			<?php if ($dash_perm['users']) { ?>
			<div class="adm-dash-card mb-3">
				<div class="adm-dash-card__head"><i class="fa-solid fa-crown text-warning me-2"></i>Top khách hàng</div>
				<div class="adm-dash-card__body p-0">
					<?php if (!empty($dash['top_customers'])) { ?>
						<table class="table admin-table mb-0">
							<thead><tr><th>Khách</th><th>Đơn</th><?php if ($dash_perm['revenue']) { ?><th>Chi tiêu</th><?php } ?></tr></thead>
							<tbody>
								<?php foreach ($dash['top_customers'] as $c) { ?>
									<tr>
										<td><?php echo htmlspecialchars($c->user_name, ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo (int) $c->order_count; ?></td>
										<?php if ($dash_perm['revenue']) { ?><td><?php echo number_format((int) $c->total_spent, 0, ',', '.'); ?> ₫</td><?php } ?>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<div class="admin-empty p-3">Chưa có dữ liệu.</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
			<?php if ($dash_perm['catalog']) { ?>
			<div class="adm-dash-card">
				<div class="adm-dash-card__head"><i class="fa-solid fa-folder-tree text-primary me-2"></i>Top danh mục</div>
				<div class="adm-dash-card__body p-0">
					<?php if (!empty($dash['top_categories'])) { ?>
						<table class="table admin-table mb-0">
							<thead><tr><th>Danh mục</th><th>Bán</th><?php if ($dash_perm['revenue']) { ?><th>DT</th><?php } ?></tr></thead>
							<tbody>
								<?php foreach ($dash['top_categories'] as $cat) { ?>
									<tr>
										<td><?php echo htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo number_format((int) $cat->total_sold); ?></td>
										<?php if ($dash_perm['revenue']) { ?><td><?php echo number_format((int) $cat->total_amount, 0, ',', '.'); ?> ₫</td><?php } ?>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<div class="admin-empty p-3">Chưa có dữ liệu.</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
	</div>
	<?php } ?>

	<?php if ($dash_perm['inventory']) { ?>
	<div class="row g-3 mb-4 adm-dash-row-stock">
		<div class="col-12">
			<div class="adm-dash-card">
				<div class="adm-dash-card__head adm-dash-card__head--between"><i class="fa-solid fa-box-open text-danger me-2"></i>Sản phẩm sắp hết hàng (≤ <?php echo (int) $dash['low_stock_threshold']; ?>)</div>
				<div class="adm-dash-card__body adm-dash-card__body--table p-0">
					<?php if (!empty($dash['low_stock'])) { ?>
						<div class="table-responsive adm-dash-table-wrap">
							<table class="table table-hover admin-table adm-dash-stock-table mb-0">
								<thead><tr><th>Sản phẩm</th><th class="adm-dash-stock-table__num">Tồn</th><th class="adm-dash-stock-table__num">Giá</th><th class="adm-dash-stock-table__act">Thao tác</th></tr></thead>
								<tbody>
									<?php foreach ($dash['low_stock'] as $p) { ?>
										<tr>
											<td>
												<div class="adm-dash-product">
													<img src="<?php echo base_url('upload/product/' . $p->image_link); ?>" alt="">
													<span><?php echo htmlspecialchars($p->name, ENT_QUOTES, 'UTF-8'); ?></span>
												</div>
											</td>
											<td class="adm-dash-stock-table__num"><strong class="text-danger"><?php echo (int) $p->quantity; ?></strong></td>
											<td class="adm-dash-stock-table__num"><?php echo number_format((int) $p->price, 0, ',', '.'); ?> ₫</td>
											<td class="adm-dash-stock-table__act">
												<?php if ($dash_perm['products']) { ?>
												<a class="btn btn-sm btn-outline-primary" href="<?php echo admin_url('products/edit/' . (int) $p->id); ?>">Nhập kho</a>
												<?php } elseif (admin_can('inventory.adjust', $login)) { ?>
												<a class="btn btn-sm btn-outline-primary" href="<?php echo admin_url('inventory'); ?>">Tồn kho</a>
												<?php } ?>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					<?php } else { ?>
						<div class="admin-empty p-4">Không có sản phẩm nào dưới ngưỡng cảnh báo.</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>

	<?php
	$chart_payload = array(
		'labels' => $dash['charts']['labels'],
		'revenue' => $dash['charts']['revenue'],
	);
	if ($dash_perm['revenue']) {
	?>
<script src="<?php echo $admin_asset; ?>js/chart.min.js"></script>
<script>
window.admDashboardChart = <?php echo json_encode($chart_payload, JSON_UNESCAPED_UNICODE); ?>;
</script>
	<?php } ?>
<script src="<?php echo $admin_asset; ?>js/admin-dashboard.js?v=4"></script>
</div>
