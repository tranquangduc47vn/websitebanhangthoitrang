<!DOCTYPE html>
<html lang="vi">
<head>
	<?php $this->load->view('admin/layouts/head'); ?>
</head>
<body class="admin-app admin-login-page">
	<div class="admin-login-shell">
		<section class="admin-login-hero" aria-hidden="true">
			<div class="admin-login-hero-inner">
				<span class="admin-brand-mark admin-login-hero-logo">W</span>
				<h2 class="admin-login-hero-title">Webshop Admin</h2>
				<p class="admin-login-hero-text">
					Quản lý sản phẩm, đơn hàng và nội dung cửa hàng thời trang — mọi thứ trong một bảng điều khiển gọn gàng.
				</p>
				<ul class="admin-login-hero-features">
					<li><i class="fa-solid fa-chart-line"></i> Tổng quan doanh thu</li>
					<li><i class="fa-solid fa-shield-halved"></i> Truy cập bảo mật</li>
					<li><i class="fa-solid fa-mobile-screen"></i> Giao diện responsive</li>
				</ul>
			</div>
			<div class="admin-login-hero-pattern"></div>
		</section>

		<main class="admin-login-main">
			<div class="admin-login-card">
				<header class="admin-login-card-head">
					<span class="admin-brand-mark d-lg-none mb-3">W</span>
					<h1 class="admin-login-title">Chào mừng trở lại</h1>
					<p class="admin-login-subtitle">Đăng nhập tài khoản quản trị của bạn</p>
				</header>

				<form class="admin-form admin-login-form" method="post" novalidate>
					<div class="mb-3">
						<label class="form-label" for="email">E-mail</label>
						<div class="admin-login-input-wrap">
							<i class="fa-solid fa-envelope admin-login-input-icon" aria-hidden="true"></i>
							<input class="form-control" id="email" placeholder="email@example.com" name="email" type="email" autofocus value="<?php echo set_value('email'); ?>" autocomplete="username">
						</div>
						<div class="text-danger small mt-1"><?php echo form_error('email'); ?></div>
					</div>

					<div class="mb-3">
						<label class="form-label" for="password">Mật khẩu</label>
						<div class="admin-login-input-wrap">
							<i class="fa-solid fa-lock admin-login-input-icon" aria-hidden="true"></i>
							<input class="form-control" id="password" placeholder="Nhập mật khẩu" name="password" type="password" autocomplete="current-password">
						</div>
					</div>

					<?php if (form_error('login')) { ?>
						<div class="alert alert-danger d-flex align-items-start gap-2 py-2 mb-3" role="alert">
							<i class="fa-solid fa-circle-exclamation mt-1 flex-shrink-0"></i>
							<span><?php echo form_error('login'); ?></span>
						</div>
					<?php } ?>

					<div class="form-check mb-4">
						<input class="form-check-input" name="remember" type="checkbox" value="Remember Me" id="remember">
						<label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
					</div>

					<button type="submit" class="btn btn-primary btn-lg w-100 admin-login-submit">
						<i class="fa-solid fa-right-to-bracket me-2"></i>Đăng nhập
					</button>
				</form>

				<p class="admin-login-footnote">
					<i class="fa-solid fa-lock me-1"></i> Chỉ dành cho nhân viên được cấp quyền.
				</p>
			</div>
		</main>
	</div>
	<?php $admin_footer_minimal = true; $this->load->view('admin/layouts/scripts'); ?>
</body>
</html>
