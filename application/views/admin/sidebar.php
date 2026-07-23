<?php
$current = $this->uri->segment(2);
$segment3 = $this->uri->segment(3);
$is_transaction_chat = ($current === 'orders' && $segment3 === 'chat');

function admin_menu_active($current, $items, $segment3 = null)
{
	if (!in_array($current, (array)$items)) {
		return '';
	}
	if ($current === 'orders' && $segment3 === 'chat') {
		return '';
	}
	return 'active';
}

$admin_menu_main = array(
	array('slug' => 'home', 'url' => admin_url('home'), 'icon' => 'fa-solid fa-gauge-high', 'label' => 'Trang chủ'),
	array('slug' => 'products', 'url' => admin_url('products'), 'icon' => 'fa-solid fa-shirt', 'label' => 'Sản phẩm'),
	array('slug' => 'slider', 'url' => admin_url('slider'), 'icon' => 'fa-solid fa-images', 'label' => 'Slider'),
	array('slug' => array('orders', 'report'), 'url' => admin_url('orders'), 'icon' => 'fa-solid fa-receipt', 'label' => 'Đơn đặt hàng'),
	array('slug' => 'users', 'url' => admin_url('users'), 'icon' => 'fa-solid fa-users', 'label' => 'Khách hàng'),
	array('slug' => 'voucher', 'url' => admin_url('voucher'), 'icon' => 'fa-solid fa-ticket', 'label' => 'Voucher'),
	array('slug' => 'admin', 'url' => admin_url('admin'), 'icon' => 'fa-solid fa-user-shield', 'label' => 'Nhân viên'),
);

$admin_menu_warehouse = array(
	array('slug' => 'inventory', 'url' => admin_url('inventory'), 'icon' => 'fa-solid fa-boxes-stacked', 'label' => 'Tồn kho'),
	array('slug' => 'receipts', 'url' => admin_url('receipts'), 'icon' => 'fa-solid fa-dolly', 'label' => 'Phiếu nhập'),
	array('slug' => 'inventory-adjust', 'url' => admin_url('inventory/low-stock'), 'icon' => 'fa-solid fa-clipboard-check', 'label' => 'Kiểm kê kho'),
	array('slug' => 'stock-movements', 'url' => admin_url('stock-movements'), 'icon' => 'fa-solid fa-clock-rotate-left', 'label' => 'Lịch sử biến động'),
	array('slug' => 'suppliers', 'url' => admin_url('suppliers'), 'icon' => 'fa-solid fa-truck-field', 'label' => 'Nhà cung cấp'),
);

$admin_menu_info = array(
	array('slug' => 'catalog', 'url' => admin_url('catalog'), 'icon' => 'fa-solid fa-folder-tree', 'label' => 'Danh mục'),
	array('slug' => 'posts', 'url' => admin_url('posts'), 'icon' => 'fa-solid fa-newspaper', 'label' => 'Quản lý tin tức'),
	array('slug' => 'page', 'url' => admin_url('page'), 'icon' => 'fa-solid fa-file-lines', 'label' => 'Quản lý trang'),
	array('slug' => 'banner', 'url' => admin_url('banner'), 'icon' => 'fa-solid fa-panorama', 'label' => 'Banner danh mục'),
	array('slug' => 'store', 'url' => admin_url('store'), 'icon' => 'fa-solid fa-map-location-dot', 'label' => 'Quản lý bản đồ'),
	array('slug' => 'tuyendung', 'url' => admin_url('tuyendung'), 'icon' => 'fa-solid fa-briefcase', 'label' => 'Tuyển dụng'),
	array('slug' => 'hoptac', 'url' => admin_url('hoptac'), 'icon' => 'fa-solid fa-handshake', 'label' => 'Quản lý hợp tác'),
	array('slug' => 'vanchuyen', 'url' => admin_url('vanchuyen'), 'icon' => 'fa-solid fa-truck', 'label' => 'Chính sách vận chuyển'),
);

$info_slugs = array('catalog', 'posts', 'page', 'banner', 'store', 'tuyendung', 'hoptac', 'vanchuyen');
$info_section_open = in_array($current, $info_slugs, true);
?>

<div class="offcanvas offcanvas-start offcanvas-lg admin-sidebar" tabindex="-1" id="adminSidebar"
	aria-labelledby="adminSidebarLabel" data-bs-scroll="true">
	<div class="offcanvas-header d-lg-none">
		<h5 class="offcanvas-title" id="adminSidebarLabel">Menu quản trị</h5>
		<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
	</div>
	<div class="offcanvas-body p-0 d-flex flex-column">
		<div class="admin-sidebar-brand">
			<span class="admin-brand-mark">W</span>
			<div class="admin-sidebar-brand-title">
				Webshop Admin
				<small>Thời trang &amp; bán lẻ</small>
			</div>
		</div>

		<nav class="admin-nav nav flex-column flex-grow-1" aria-label="Admin navigation">
			<div class="admin-nav-section">Tổng quan</div>
			<?php
			$sidebar_login = $this->session->userdata('login');
			foreach ($admin_menu_main as $item) {
				if (!admin_sidebar_allowed($item['slug'], $sidebar_login)) {
					continue;
				}
			?>
				<a class="nav-link <?php echo admin_menu_active($current, $item['slug'], $segment3); ?>"
					href="<?php echo $item['url']; ?>">
					<i class="<?php echo $item['icon']; ?> admin-nav-icon" aria-hidden="true"></i>
					<span><?php echo $item['label']; ?></span>
				</a>
			<?php } ?>

			<?php
			$warehouse_visible = array();
			foreach ($admin_menu_warehouse as $item) {
				if (admin_sidebar_allowed($item['slug'], $sidebar_login)) {
					$warehouse_visible[] = $item;
				}
			}
			if (!empty($warehouse_visible)) {
				$wh_slugs = array('inventory', 'receipts', 'inventory-adjust', 'stock-movements', 'suppliers', 'stock-receipts', 'stock-inventory');
				$wh_open = in_array($current, $wh_slugs, true) || ($current === 'stock_receipt') || ($current === 'stock_movements') || ($current === 'suppliers');
				$wh_has_active = false;
				foreach ($warehouse_visible as $item) {
					if (admin_menu_active($current, $item['slug'], $segment3) === 'active') {
						$wh_has_active = true;
						break;
					}
				}
				$wh_expanded = $wh_open || $wh_has_active;
			?>
				<div class="admin-nav-group <?php echo $wh_expanded ? 'is-open' : ''; ?>">
					<button type="button"
						class="nav-link admin-nav-group-toggle w-100 border-0 bg-transparent text-start"
						data-bs-toggle="collapse"
						data-bs-target="#adminNavWarehouse"
						aria-expanded="<?php echo $wh_expanded ? 'true' : 'false'; ?>"
						aria-controls="adminNavWarehouse">
						<i class="fa-solid fa-warehouse admin-nav-icon" aria-hidden="true"></i>
						<span class="flex-grow-1">Quản lý kho</span>
						<i class="fa-solid fa-chevron-down admin-nav-group-chevron" aria-hidden="true"></i>
					</button>
					<div class="collapse <?php echo $wh_expanded ? 'show' : ''; ?>" id="adminNavWarehouse">
						<div class="admin-nav-sub">
							<?php foreach ($warehouse_visible as $item) { ?>
								<a class="nav-link admin-nav-sub-link <?php echo admin_menu_active($current, $item['slug'], $segment3); ?>"
									href="<?php echo $item['url']; ?>">
									<i class="<?php echo $item['icon']; ?> admin-nav-icon" aria-hidden="true"></i>
									<span><?php echo $item['label']; ?></span>
								</a>
							<?php } ?>
						</div>
					</div>
				</div>
			<?php } ?>

			<?php
			$info_visible = array();
			foreach ($admin_menu_info as $item) {
				if (admin_sidebar_allowed($item['slug'], $sidebar_login)) {
					$info_visible[] = $item;
				}
			}
			if (!empty($info_visible)) {
				$info_has_active = false;
				foreach ($info_visible as $item) {
					if (admin_menu_active($current, $item['slug'], $segment3) === 'active') {
						$info_has_active = true;
						break;
					}
				}
				$info_expanded = $info_section_open || $info_has_active;
			?>
				<div class="admin-nav-group <?php echo $info_expanded ? 'is-open' : ''; ?>">
					<button type="button"
						class="nav-link admin-nav-group-toggle w-100 border-0 bg-transparent text-start"
						data-bs-toggle="collapse"
						data-bs-target="#adminNavInfo"
						aria-expanded="<?php echo $info_expanded ? 'true' : 'false'; ?>"
						aria-controls="adminNavInfo">
						<i class="fa-solid fa-circle-info admin-nav-icon" aria-hidden="true"></i>
						<span class="flex-grow-1">Thông tin</span>
						<i class="fa-solid fa-chevron-down admin-nav-group-chevron" aria-hidden="true"></i>
					</button>
					<div class="collapse <?php echo $info_expanded ? 'show' : ''; ?>" id="adminNavInfo">
						<div class="admin-nav-sub">
							<?php foreach ($info_visible as $item) { ?>
								<a class="nav-link admin-nav-sub-link <?php echo admin_menu_active($current, $item['slug'], $segment3); ?>"
									href="<?php echo $item['url']; ?>">
									<i class="<?php echo $item['icon']; ?> admin-nav-icon" aria-hidden="true"></i>
									<span><?php echo $item['label']; ?></span>
								</a>
							<?php } ?>
						</div>
					</div>
				</div>
			<?php } ?>

			<div class="admin-nav-section">Hỗ trợ</div>
			<?php if (admin_can('order.chat', $sidebar_login)) {
				$this->load->helper('ai');
				$waiting_badge = function_exists('support_waiting_count') ? support_waiting_count() : 0;
				$is_support_chat = ($current === 'support-chat');
			?>
			<a class="nav-link <?php echo $is_support_chat ? 'active' : ''; ?>"
				href="<?php echo admin_url('support-chat'); ?>">
				<i class="fa-solid fa-headset admin-nav-icon" aria-hidden="true"></i>
				<span>Hỗ trợ khách hàng</span>
				<?php if ($waiting_badge > 0) { ?>
					<span class="admin-nav-badge" id="support-waiting-badge"><?php echo (int) $waiting_badge; ?></span>
				<?php } ?>
			</a>
			<?php } ?>

			<?php if (admin_can('ai.manage', $sidebar_login)) {
				$ai_current = ($current === 'ai-assistant');
				$ai_segment = $ai_current ? $segment3 : null;
			?>
			<div class="admin-nav-section">Trợ lý AI</div>
			<a class="nav-link <?php echo ($ai_current && ($ai_segment === null || $ai_segment === 'settings')) ? 'active' : ''; ?>"
				href="<?php echo admin_url('ai-assistant/settings'); ?>">
				<i class="fa-solid fa-sliders admin-nav-icon" aria-hidden="true"></i>
				<span>Cấu hình AI</span>
			</a>
			<a class="nav-link <?php echo ($ai_current && $ai_segment === 'faq') ? 'active' : ''; ?>"
				href="<?php echo admin_url('ai-assistant/faq'); ?>">
				<i class="fa-solid fa-circle-question admin-nav-icon" aria-hidden="true"></i>
				<span>FAQ</span>
			</a>
			<a class="nav-link <?php echo ($ai_current && $ai_segment === 'conversations') ? 'active' : ''; ?>"
				href="<?php echo admin_url('ai-assistant/conversations'); ?>">
				<i class="fa-solid fa-robot admin-nav-icon" aria-hidden="true"></i>
				<span>Hội thoại AI</span>
			</a>
			<?php } ?>
		</nav>
	</div>
</div>

<script>
(function () {
	var group = document.querySelector('.admin-nav-group');
	var panel = document.getElementById('adminNavInfo');
	if (!group || !panel || typeof bootstrap === 'undefined') {
		return;
	}
	panel.addEventListener('show.bs.collapse', function () {
		group.classList.add('is-open');
	});
	panel.addEventListener('hide.bs.collapse', function () {
		group.classList.remove('is-open');
	});
})();
</script>
