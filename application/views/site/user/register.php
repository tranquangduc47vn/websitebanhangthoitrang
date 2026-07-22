<!DOCTYPE html>
<html lang="vi">
<head>
	<?php $this->load->view('site/head', $this->data); ?>
</head>
<body class="jm-page-auth jm-page-auth-standalone jm-page-auth-standalone--scroll">
	<div class="jm-auth-standalone-wrap jm-auth-standalone-wrap-register">
		<a href="<?php echo base_url('dang-nhap'); ?>" class="jm-auth-back" title="Quay lại đăng nhập">
			<span class="jm-auth-back-icon" aria-hidden="true">←</span>
			Quay lại
		</a>

		<div class="jm-auth-card">
			<div class="jm-auth-card-head">
				<h1 class="jm-auth-title">Đăng ký</h1>
				<p class="jm-auth-subtitle">Tạo tài khoản để mua sắm tại JM Dress Design</p>
			</div>

			<?php if (!empty($message_success)) { ?>
				<div class="jm-auth-alert jm-auth-alert-success"><?php echo htmlspecialchars($message_success, ENT_QUOTES, 'UTF-8'); ?></div>
			<?php } ?>
			<?php if (!empty($message_fail)) { ?>
				<div class="jm-auth-alert jm-auth-alert-error"><?php echo htmlspecialchars($message_fail, ENT_QUOTES, 'UTF-8'); ?></div>
			<?php } ?>

			<form class="jm-auth-form" method="post" action="<?php echo base_url('user/register'); ?>" novalidate>
				<div class="jm-auth-field">
					<label for="regName">Họ tên</label>
					<input type="text" class="jm-auth-input" id="regName" name="name"
						placeholder="Nguyễn Văn A"
						value="<?php echo htmlspecialchars(set_value('name'), ENT_QUOTES, 'UTF-8'); ?>"
						autocomplete="name" required>
					<?php echo form_error('name', '<span class="jm-auth-field-error">', '</span>'); ?>
				</div>

				<div class="jm-auth-field">
					<label for="regEmail">Email</label>
					<input type="email" class="jm-auth-input" id="regEmail" name="email"
						placeholder="email@example.com"
						value="<?php echo htmlspecialchars(set_value('email'), ENT_QUOTES, 'UTF-8'); ?>"
						autocomplete="email" required>
					<?php echo form_error('email', '<span class="jm-auth-field-error">', '</span>'); ?>
				</div>

				<div class="jm-auth-field">
					<label for="regPassword">Mật khẩu</label>
					<input type="password" class="jm-auth-input" id="regPassword" name="password"
						placeholder="••••••••" autocomplete="new-password" required>
					<?php echo form_error('password', '<span class="jm-auth-field-error">', '</span>'); ?>
				</div>

				<div class="jm-auth-field">
					<label for="regRePassword">Nhập lại mật khẩu</label>
					<input type="password" class="jm-auth-input" id="regRePassword" name="re_password"
						placeholder="••••••••" autocomplete="new-password" required>
					<?php echo form_error('re_password', '<span class="jm-auth-field-error">', '</span>'); ?>
				</div>

				<div class="jm-auth-field">
					<label>Địa chỉ (Việt Nam) <span class="jm-auth-required">*</span></label>
					<p class="jm-auth-hint">Chọn đúng tỉnh, quận/huyện, phường/xã — không nhập tỉnh thành tùy ý.</p>
					<select class="jm-auth-input jm-auth-select" id="regProvince" name="province_id" required>
						<option value="">— Đang tải tỉnh thành —</option>
					</select>
					<?php echo form_error('province_id', '<span class="jm-auth-field-error">', '</span>'); ?>
					<select class="jm-auth-input jm-auth-select" id="regDistrict" name="district_id" required disabled>
						<option value="">— Chọn quận / huyện —</option>
					</select>
					<?php echo form_error('district_id', '<span class="jm-auth-field-error">', '</span>'); ?>
					<select class="jm-auth-input jm-auth-select" id="regWard" name="ward_id" required disabled>
						<option value="">— Chọn phường / xã —</option>
					</select>
					<?php echo form_error('ward_id', '<span class="jm-auth-field-error">', '</span>'); ?>
				</div>

				<div class="jm-auth-field">
					<label for="regAddressNote">Ghi chú địa chỉ (số nhà, tên đường, …) <span class="jm-auth-required">*</span></label>
					<input type="text" class="jm-auth-input" id="regAddressNote" name="address_note"
						placeholder="Ví dụ: 123 Nguyễn Trãi, tòa A, căn 502"
						value="<?php echo htmlspecialchars(set_value('address_note'), ENT_QUOTES, 'UTF-8'); ?>"
						autocomplete="address-line1" required maxlength="255">
					<?php echo form_error('address_note', '<span class="jm-auth-field-error">', '</span>'); ?>
				</div>

				<div class="jm-auth-field">
					<label for="regPhone">Số điện thoại</label>
					<input type="tel" class="jm-auth-input" id="regPhone" name="phone"
						placeholder="09xxxxxxxx"
						value="<?php echo htmlspecialchars(set_value('phone'), ENT_QUOTES, 'UTF-8'); ?>"
						autocomplete="tel" required>
					<?php echo form_error('phone', '<span class="jm-auth-field-error">', '</span>'); ?>
				</div>

				<button type="submit" class="jm-auth-submit">Đăng ký</button>
			</form>

			<p class="jm-auth-footer-link">
				Đã có tài khoản?
				<a href="<?php echo base_url('dang-nhap'); ?>">Đăng nhập</a>
			</p>
		</div>
	</div>
	<script src="<?php echo public_url(); ?>js/jquery-3.1.1.js"></script>
	<script src="<?php echo site_asset_url('js/vn-address.js?v=1'); ?>"></script>
	<script>
	(function ($) {
		var form = new VnAddressForm({
			province: '#regProvince',
			district: '#regDistrict',
			ward: '#regWard',
			dataUrl: <?php echo json_encode(vn_address_json_url()); ?>,
			initial: {
				province_id: <?php echo json_encode(set_value('province_id')); ?>,
				district_id: <?php echo json_encode(set_value('district_id')); ?>,
				ward_id: <?php echo json_encode(set_value('ward_id')); ?>
			}
		});
		form.init().fail(function () {
			$('#regProvince').html('<option value="">Không tải được dữ liệu địa giới</option>');
		});
	})(jQuery);
	</script>
</body>
</html>
