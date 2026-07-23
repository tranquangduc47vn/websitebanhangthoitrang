<!DOCTYPE html>
<html lang="vi">
<head>
	<?php $this->load->view('site/head', $this->data); ?>
</head>
<body class="jm-page-auth jm-page-auth-standalone">
	<div class="jm-auth-standalone-wrap">
		<a href="<?php echo base_url('dang-nhap'); ?>" class="jm-auth-back" title="Quay lại đăng nhập">
			<span class="jm-auth-back-icon" aria-hidden="true">←</span>
			Quay lại
		</a>

		<div class="jm-auth-card">
			<div class="jm-auth-card-head">
				<h1 class="jm-auth-title">Quên mật khẩu</h1>
				<p class="jm-auth-subtitle">Nhập email đăng ký — chúng tôi sẽ gửi liên kết đặt lại mật khẩu</p>
			</div>

			<?php if (!empty($message_success)) { ?>
				<div class="jm-auth-alert jm-auth-alert-success"><?php echo htmlspecialchars($message_success, ENT_QUOTES, 'UTF-8'); ?></div>
			<?php } ?>
			<?php if (!empty($message_fail)) { ?>
				<div class="jm-auth-alert jm-auth-alert-error"><?php echo htmlspecialchars($message_fail, ENT_QUOTES, 'UTF-8'); ?></div>
			<?php } ?>

			<form class="jm-auth-form" method="post" action="<?php echo base_url('quen-mat-khau'); ?>" novalidate>
				<div class="jm-auth-field">
					<label for="forgotEmail">Email</label>
					<input type="email" class="jm-auth-input" id="forgotEmail" name="email"
						placeholder="email@example.com"
						value="<?php echo htmlspecialchars(set_value('email'), ENT_QUOTES, 'UTF-8'); ?>"
						autocomplete="email" required>
					<?php echo form_error('email', '<span class="jm-auth-field-error">', '</span>'); ?>
				</div>

				<button type="submit" class="jm-auth-submit">Gửi liên kết đặt lại</button>
			</form>

			<p class="jm-auth-footer-link">
				Nhớ mật khẩu?
				<a href="<?php echo base_url('dang-nhap'); ?>">Đăng nhập</a>
			</p>
		</div>
	</div>
</body>
</html>
