<!DOCTYPE html>
<html lang="vi">
<head>
	<?php $this->load->view('site/head', $this->data); ?>
</head>
<body class="jm-page-auth jm-page-auth-standalone">
	<div class="jm-auth-standalone-wrap">
		<a href="<?php echo base_url(); ?>" class="jm-auth-back" title="Về trang chủ">
			<span class="jm-auth-back-icon" aria-hidden="true">←</span>
			Quay lại
		</a>

		<div class="jm-auth-card">
			<div class="jm-auth-card-head">
				<h1 class="jm-auth-title">Đăng nhập</h1>
				<p class="jm-auth-subtitle">Chào mừng bạn quay lại <?php echo htmlspecialchars(shop_name(), ENT_QUOTES, 'UTF-8'); ?></p>
			</div>

			<?php if (!empty($message_success)) { ?>
				<div class="jm-auth-alert jm-auth-alert-success"><?php echo htmlspecialchars($message_success, ENT_QUOTES, 'UTF-8'); ?></div>
			<?php } ?>
			<?php if (!empty($message_fail)) { ?>
				<div class="jm-auth-alert jm-auth-alert-error"><?php echo htmlspecialchars($message_fail, ENT_QUOTES, 'UTF-8'); ?></div>
			<?php } ?>

			<form class="jm-auth-form" method="post" action="<?php echo base_url('user/login'); ?>" novalidate>
				<?php echo form_error('login', '<div class="jm-auth-field-error jm-auth-field-error-block">', '</div>'); ?>

				<div class="jm-auth-field">
					<label for="inputEmail3">Email</label>
					<input type="email" class="jm-auth-input" id="inputEmail3" name="email"
						placeholder="email@example.com"
						value="<?php echo htmlspecialchars(set_value('email'), ENT_QUOTES, 'UTF-8'); ?>"
						autocomplete="email" required>
					<?php echo form_error('email', '<span class="jm-auth-field-error">', '</span>'); ?>
				</div>

				<div class="jm-auth-field">
					<label for="inputPassword3">Mật khẩu</label>
					<input type="password" class="jm-auth-input" id="inputPassword3" name="password"
						placeholder="••••••••" autocomplete="current-password" required>
					<?php echo form_error('password', '<span class="jm-auth-field-error">', '</span>'); ?>
				</div>

				<p class="jm-auth-forgot">
					<a href="<?php echo base_url('quen-mat-khau'); ?>">Quên mật khẩu?</a>
				</p>

				<button type="submit" class="jm-auth-submit">Đăng nhập</button>
			</form>

			<p class="jm-auth-footer-link">
				Chưa có tài khoản?
				<a href="<?php echo base_url('dang-ky'); ?>">Đăng ký ngay</a>
			</p>
		</div>
	</div>
</body>
</html>
