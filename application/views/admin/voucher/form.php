<?php
$is_edit = !empty($voucher);
$admin_form_title = $is_edit ? 'Sửa voucher' : 'Thêm voucher';
$admin_form_breadcrumb = 'Voucher';
$admin_form_back_url = admin_url('voucher');
$this->load->helper('loyalty');
$v = $voucher;
$this->load->view('admin/partials/form_open');
?>
<form class="admin-form" action="" method="post">
	<div class="row">
		<div class="col-md-6 mb-3">
			<label class="form-label">Mã voucher <span class="text-danger">*</span></label>
			<input type="text" name="code" class="form-control text-uppercase" required
				value="<?php echo $is_edit ? htmlspecialchars($v->code, ENT_QUOTES, 'UTF-8') : set_value('code'); ?>">
		</div>
		<div class="col-md-6 mb-3">
			<label class="form-label">Tên hiển thị</label>
			<input type="text" name="name" class="form-control"
				value="<?php echo $is_edit ? htmlspecialchars($v->name, ENT_QUOTES, 'UTF-8') : set_value('name'); ?>">
		</div>
	</div>
	<div class="mb-3">
		<label class="form-label">Mô tả ngắn</label>
		<input type="text" name="description" class="form-control"
			value="<?php echo $is_edit ? htmlspecialchars($v->description, ENT_QUOTES, 'UTF-8') : set_value('description'); ?>">
	</div>
	<div class="row">
		<div class="col-md-4 mb-3">
			<label class="form-label">Loại giảm</label>
			<select name="discount_type" class="form-select">
				<option value="fixed" <?php echo ($is_edit && $v->discount_type === 'fixed') ? 'selected' : ''; ?>>Số tiền cố định (₫)</option>
				<option value="percent" <?php echo ($is_edit && $v->discount_type === 'percent') ? 'selected' : ''; ?>>Phần trăm (%)</option>
			</select>
		</div>
		<div class="col-md-4 mb-3">
			<label class="form-label">Giá trị giảm</label>
			<input type="number" name="discount_value" class="form-control" min="0" required
				value="<?php echo $is_edit ? (int) $v->discount_value : set_value('discount_value', 0); ?>">
		</div>
		<div class="col-md-4 mb-3">
			<label class="form-label">Giảm tối đa (₫, % only)</label>
			<input type="number" name="max_discount" class="form-control" min="0"
				value="<?php echo $is_edit ? (int) $v->max_discount : set_value('max_discount', 0); ?>">
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 mb-3">
			<label class="form-label">Đơn tối thiểu (₫)</label>
			<input type="number" name="min_order_amount" class="form-control" min="0"
				value="<?php echo $is_edit ? (int) $v->min_order_amount : set_value('min_order_amount', 0); ?>">
		</div>
		<div class="col-md-4 mb-3">
			<label class="form-label">Hạng tối thiểu</label>
			<select name="tier_min" class="form-select">
				<?php foreach (array('member', 'silver', 'gold', 'vip') as $tier) { ?>
					<option value="<?php echo $tier; ?>" <?php echo ($is_edit && $v->tier_min === $tier) ? 'selected' : ''; ?>><?php echo loyalty_tier_label($tier); ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="col-md-4 mb-3">
			<label class="form-label">ID khách riêng (0 = công khai)</label>
			<input type="number" name="user_id" class="form-control" min="0"
				value="<?php echo $is_edit ? (int) $v->user_id : set_value('user_id', 0); ?>">
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 mb-3">
			<label class="form-label">Giới hạn lượt dùng (0 = không giới hạn)</label>
			<input type="number" name="usage_limit" class="form-control" min="0"
				value="<?php echo $is_edit ? (int) $v->usage_limit : set_value('usage_limit', 0); ?>">
		</div>
		<div class="col-md-4 mb-3">
			<label class="form-label">Lượt / khách</label>
			<input type="number" name="per_user_limit" class="form-control" min="1"
				value="<?php echo $is_edit ? (int) $v->per_user_limit : set_value('per_user_limit', 1); ?>">
		</div>
		<div class="col-md-4 mb-3 d-flex align-items-end">
			<label class="form-check mb-2">
				<input type="checkbox" name="is_active" value="1" class="form-check-input" <?php echo (!$is_edit || (int) $v->is_active === 1) ? 'checked' : ''; ?>>
				<span class="form-check-label">Đang kích hoạt</span>
			</label>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 mb-3">
			<label class="form-label">Hiệu lực từ (để trống = ngay)</label>
			<input type="date" name="valid_from" class="form-control"
				value="<?php echo ($is_edit && (int) $v->valid_from > 0) ? date('Y-m-d', (int) $v->valid_from) : ''; ?>">
		</div>
		<div class="col-md-6 mb-3">
			<label class="form-label">Hiệu lực đến (để trống = không hết hạn)</label>
			<input type="date" name="valid_to" class="form-control"
				value="<?php echo ($is_edit && (int) $v->valid_to > 0) ? date('Y-m-d', (int) $v->valid_to) : ''; ?>">
		</div>
	</div>
	<div class="admin-form-actions">
		<button type="submit" name="submit" value="1" class="btn btn-primary">Lưu</button>
		<a href="<?php echo admin_url('voucher'); ?>" class="btn btn-default">Hủy</a>
	</div>
</form>
<?php $this->load->view('admin/partials/form_close'); ?>
