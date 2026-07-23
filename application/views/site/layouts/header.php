<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$wf_cart_total = isset($total_items) ? (int) $total_items : 0;
$wf_carts = (isset($carts) && is_array($carts)) ? $carts : array();

$wf_nav_men = base_url('moi');
$wf_nav_women = base_url('moi');
$wf_nav_family = base_url('moi');
if (isset($catalog) && is_array($catalog)) {
	foreach ($catalog as $parent) {
		if (isset($parent->name) && $parent->name === 'Thời trang nam') {
			$wf_nav_men = build_category_url($parent->id);
		}
		if ((int) $parent->id === 8) {
			$wf_nav_women = build_category_url($parent->id);
		}
		if ((int) $parent->id === 9) {
			$wf_nav_family = build_category_url($parent->id);
		}
	}
}
?>
<div class="wf-chrome-2026">
	<div class="wf-chrome-shell">
		<header class="wf-chrome-header" id="wfChromeHeader" role="banner">
			<div class="wf-chrome-header__inner">
				<a href="<?php echo base_url(); ?>" class="wf-chrome-logo" title="Trang chủ">
					<img src="<?php echo base_url(); ?>upload/logo.png" alt="Logo">
				</a>

				<button type="button" class="wf-chrome-nav-toggle" id="wfChromeNavToggle" aria-expanded="false" aria-controls="wfChromeNavDrawer" aria-label="Mở menu">
					<i class="fa-solid fa-bars" aria-hidden="true"></i>
				</button>

				<nav class="wf-chrome-nav" id="wfChromeNavDrawer" aria-label="Menu chính">
					<ul class="wf-chrome-menu">
						<li class="wf-chrome-menu__item is-active"><a href="<?php echo base_url(); ?>">HOME</a></li>
						<li class="wf-chrome-menu__item"><a href="<?php echo htmlspecialchars($wf_nav_men, ENT_QUOTES, 'UTF-8'); ?>">THỜI TRANG NAM</a></li>
						<li class="wf-chrome-menu__item"><a href="<?php echo htmlspecialchars($wf_nav_women, ENT_QUOTES, 'UTF-8'); ?>">THỜI TRANG NỮ</a></li>
						<li class="wf-chrome-menu__item"><a href="<?php echo htmlspecialchars($wf_nav_family, ENT_QUOTES, 'UTF-8'); ?>">THỜI TRANG GIA ĐÌNH</a></li>
					</ul>
				</nav>

				<div class="jm-header-icons wf-chrome-actions">
					<div class="wf-chrome-greeting-wrap">
						<?php if (isset($user) && !empty($user->name)) {
							$wf_greeting_name = product_display_name($user->name);
						?>
							<p class="wf-chrome-greeting">
								<span class="wf-chrome-greeting__label">Xin chào</span>
								<span class="wf-chrome-greeting__name"><?php echo htmlspecialchars($wf_greeting_name, ENT_QUOTES, 'UTF-8'); ?></span>
							</p>
						<?php } else { ?>
							<p class="wf-chrome-greeting wf-chrome-greeting--guest">
								<span class="wf-chrome-greeting__label">Xin chào</span>
								<a class="wf-chrome-greeting__name wf-chrome-greeting__link" href="<?php echo build_login_url(); ?>">Đăng nhập</a>
							</p>
						<?php } ?>
					</div>

					<div class="jm-icon-slot jm-user-slot">
						<?php if (!isset($user)) { ?>
							<button type="button" class="jm-icon-btn jm-icon-toggle wf-chrome-icon-btn" title="Tài khoản" aria-label="Tài khoản" aria-expanded="false" aria-haspopup="true">
								<i class="fa-regular fa-user" aria-hidden="true"></i>
							</button>
							<ul class="jm-icon-popover jm-user-popover wf-chrome-popover wf-chrome-popover--menu">
								<li><a href="<?php echo build_login_url(); ?>">Đăng nhập</a></li>
								<li><a href="<?php echo build_register_url(); ?>">Đăng ký</a></li>
							</ul>
						<?php } else { ?>
							<button type="button" class="jm-icon-btn jm-icon-toggle wf-chrome-icon-btn" title="Tài khoản" aria-label="Tài khoản" aria-expanded="false" aria-haspopup="true">
								<i class="fa-solid fa-user" aria-hidden="true"></i>
							</button>
							<ul class="jm-icon-popover jm-user-popover wf-chrome-popover wf-chrome-popover--menu">
								<li><a href="<?php echo base_url('user'); ?>">Tài khoản</a></li>
								<li><a href="<?php echo base_url('user/logout'); ?>">Đăng xuất</a></li>
							</ul>
						<?php } ?>
					</div>

					<div class="jm-icon-slot jm-cart-slot">
						<button type="button" class="jm-icon-btn jm-icon-toggle wf-chrome-icon-btn" title="Giỏ hàng" aria-label="Giỏ hàng" aria-expanded="false" aria-haspopup="true">
							<i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
							<?php if ($wf_cart_total > 0) { ?>
								<span class="jm-icon-dot wf-chrome-cart-badge"><?php echo $wf_cart_total > 99 ? '99+' : $wf_cart_total; ?></span>
							<?php } ?>
						</button>
						<div class="jm-icon-popover jm-cart-popover wf-chrome-popover">
							<?php if ($wf_cart_total > 0) { ?>
								<ul class="jm-cart-mini-list wf-chrome-cart-list">
									<?php foreach ($wf_carts as $items) { ?>
										<li>
											<img src="<?php echo base_url('upload/product/' . ($items['options']['image_link'] ?? 'no-image.jpg')); ?>" alt="">
											<div>
												<span class="jm-cart-mini-name"><?php echo htmlspecialchars(product_display_name($items['name']), ENT_QUOTES, 'UTF-8'); ?></span>
												<span class="jm-cart-mini-meta"><?php echo (int) $items['qty']; ?> × <?php echo number_format($items['subtotal']); ?>đ</span>
											</div>
										</li>
									<?php } ?>
								</ul>
								<a href="<?php echo build_cart_url(); ?>" class="jm-cart-mini-link wf-chrome-cart-link">Xem giỏ hàng</a>
							<?php } else { ?>
								<p class="jm-cart-mini-empty">Giỏ hàng trống</p>
								<a href="<?php echo base_url('moi'); ?>" class="jm-cart-mini-link wf-chrome-cart-link">Mua sắm</a>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</header>

		<div class="wf-chrome-trust" aria-label="Cam kết dịch vụ">
			<div class="wf-chrome-trust__inner">
				<nav class="wf-chrome-trust__nav" aria-label="Liên kết dịch vụ">
					<a href="<?php echo base_url('hethongcuahang'); ?>" class="wf-chrome-trust__link">
						<i class="fa-solid fa-location-dot" aria-hidden="true"></i> Hệ thống cửa hàng
					</a>
					<a href="<?php echo base_url('VanChuyen'); ?>" class="wf-chrome-trust__link">
						<i class="fa-solid fa-truck-fast" aria-hidden="true"></i> Thông tin vận chuyển
					</a>
					<a href="<?php echo base_url('tich-diem'); ?>" class="wf-chrome-trust__link">
						<i class="fa-solid fa-star" aria-hidden="true"></i> Chính sách tích điểm
					</a>
				</nav>
			</div>
		</div>
	</div>
	<div class="wf-chrome-header-spacer" id="wfChromeHeaderSpacer" aria-hidden="true"></div>
</div>
