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
				<h1 class="jm-auth-title">Đặt lại mật khẩu</h1>
				<p class="jm-auth-subtitle">Nhập mật khẩu mới cho tài khoản của bạn</p>
			</div>

			<?php if (!empty($message_success)) { ?>
				<div class="jm-auth-alert jm-auth-alert-success"><?php echo htmlspecialchars($message_success, ENT_QUOTES, 'UTF-8'); ?></div>
			<?php } ?>
			<?php if (!empty($message_fail)) { ?>
				<div class="jm-auth-alert jm-auth-alert-error"><?php echo htmlspecialchars($message_fail, ENT_QUOTES, 'UTF-8'); ?></div>
			<?php } ?>

			<?php if (empty($token_valid)) { ?>
				<div class="jm-auth-alert jm-auth-alert-error">
					Liên kết không hợp lệ hoặc đã hết hạn (15 phút, chỉ dùng 1 lần).
				</div>
				<p class="jm-auth-footer-link">
					<a href="<?php echo base_url('quen-mat-khau'); ?>">Yêu cầu liên kết mới</a>
				</p>
			<?php } else { ?>
				<form class="jm-auth-form" method="post" action="<?php echo base_url('dat-lai-mat-khau/' . htmlspecialchars($reset_token, ENT_QUOTES, 'UTF-8')); ?>" novalidate>
					<input type="hidden" name="token" value="<?php echo htmlspecialchars($reset_token, ENT_QUOTES, 'UTF-8'); ?>">

					<div class="jm-auth-field">
						<label for="resetPassword">Mật khẩu mới</label>
						<input type="password" class="jm-auth-input" id="resetPassword" name="password"
							placeholder="Tối thiểu 8 ký tự, có chữ và số" autocomplete="new-password" required minlength="8">
						<p class="jm-auth-hint">Ít nhất 8 ký tự, gồm ít nhất 1 chữ cái và 1 chữ số.</p>
						<?php echo form_error('password', '<span class="jm-auth-field-error">', '</span>'); ?>
					</div>

					<div class="jm-auth-field">
						<label for="resetRePassword">Nhập lại mật khẩu</label>
						<input type="password" class="jm-auth-input" id="resetRePassword" name="re_password"
							placeholder="••••••••" autocomplete="new-password" required>
						<?php echo form_error('re_password', '<span class="jm-auth-field-error">', '</span>'); ?>
					</div>

					<button type="submit" class="jm-auth-submit">Lưu mật khẩu mới</button>
				</form>
			<?php } ?>
		</div>
	</div>
</body>
</html>
