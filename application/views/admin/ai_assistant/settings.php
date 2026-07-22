<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item active" aria-current="page">Cấu hình trợ lý AI</li>
		</ol>
	</nav>
</div>

<p class="admin-page-subtitle mb-3">Bật/tắt Trợ lý AI và chuyển giao nhân viên nội bộ.</p>

<?php if (!empty($message)) { ?>
	<div class="alert alert-success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
<?php } ?>
<?php if (!empty($message_fail)) { ?>
	<div class="alert alert-danger"><?php echo htmlspecialchars($message_fail, ENT_QUOTES, 'UTF-8'); ?></div>
<?php } ?>

<div class="admin-card">
	<div class="admin-card-body">
		<form class="admin-form" action="" method="post">
			<div class="row">
				<div class="col-md-4 mb-3">
					<label class="form-check form-switch">
						<input type="checkbox" name="ai_enabled" value="1" class="form-check-input" role="switch" <?php echo $settings['ai_enabled'] ? 'checked' : ''; ?>>
						<span class="form-check-label"><strong>Bật Trợ lý AI</strong></span>
					</label>
					<div class="form-text">Hiện widget Hỗ trợ khách hàng trên storefront.</div>
				</div>
				<div class="col-md-4 mb-3">
					<label class="form-check form-switch">
						<input type="checkbox" name="staff_support_enabled" value="1" class="form-check-input" role="switch" <?php echo $settings['staff_support_enabled'] ? 'checked' : ''; ?>>
						<span class="form-check-label"><strong>Bật chuyển giao nhân viên</strong></span>
					</label>
					<div class="form-text">Cho phép chuyển sang nhân viên trong cùng cửa sổ chat.</div>
				</div>
				<div class="col-md-4 mb-3">
					<label class="form-check form-switch">
						<input type="checkbox" name="fallback_to_staff" value="1" class="form-check-input" role="switch" <?php echo $settings['fallback_to_staff'] ? 'checked' : ''; ?>>
						<span class="form-check-label"><strong>Khi AI không trả lời được → chuyển nhân viên</strong></span>
					</label>
				</div>
			</div>

			<div class="mb-3">
				<label class="form-label">Lời chào mặc định</label>
				<input type="text" name="welcome_message" class="form-control" maxlength="255"
					value="<?php echo htmlspecialchars($settings['welcome_message'], ENT_QUOTES, 'UTF-8'); ?>">
			</div>

			<div class="mb-3">
				<label class="form-label">Giờ làm việc (hiển thị khi khách hỏi)</label>
				<input type="text" name="working_hours_text" class="form-control" maxlength="255"
					value="<?php echo htmlspecialchars($settings['working_hours_text'], ENT_QUOTES, 'UTF-8'); ?>">
			</div>

			<div class="admin-form-actions">
				<button type="submit" name="submit" value="1" class="btn btn-primary">Lưu cấu hình</button>
			</div>
		</form>
	</div>
</div>
