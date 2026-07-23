<?php
$admin_form_title = isset($title) ? $title : 'Thêm cửa hàng';
$admin_form_breadcrumb = 'Hệ thống cửa hàng';
$admin_form_back_url = admin_url('store');
$this->load->view('admin/partials/form_open');
?>
<form class="admin-form" action="<?php echo admin_url('store/add'); ?>" method="POST" role="form">

	<div class="mb-3">
		<label for="name" class="form-label">Tên Showroom / Cửa hàng <span class="text-danger">*</span></label>
		<input type="text" name="name" id="name" required class="form-control" value="" placeholder="Ví dụ: <?php echo htmlspecialchars(shop_name(), ENT_QUOTES, 'UTF-8'); ?> 140 Cầu Giấy">
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label for="city" class="form-label">Tỉnh / Thành phố <span class="text-danger">*</span></label>
			<select name="city" id="city" required class="form-select">
				<option value="Hà Nội">Hà Nội</option>
				<option value="TP. Hồ Chí Minh">TP. Hồ Chí Minh</option>
			</select>
		</div>
		<div class="col-md-6 mb-3">
			<label for="phone" class="form-label">Hotline</label>
			<input type="text" name="phone" id="phone" class="form-control" value="" placeholder="Ví dụ: 0964170476">
		</div>
	</div>

	<div class="mb-3">
		<label for="address" class="form-label">Địa chỉ chi tiết <span class="text-danger">*</span></label>
		<input type="text" name="address" id="address" required class="form-control" value="" placeholder="Số 140 Cầu Giấy, Phường Quan Hoa, Quận Cầu Giấy, Hà Nội">
	</div>

	<div class="mb-3 p-3 rounded border border-warning-subtle bg-warning-subtle">
		<label for="map_link" class="form-label">Link nhúng Google Maps (iframe src)</label>
		<textarea name="map_link" id="map_link" class="form-control font-monospace" rows="3" placeholder="https://www.google.com/maps/embed..."></textarea>
	</div>

	<div class="admin-form-actions">
		<button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Thêm mới</button>
		<a href="<?php echo admin_url('store'); ?>" class="btn btn-default">Hủy bỏ</a>
	</div>
</form>
<?php $this->load->view('admin/partials/form_close'); ?>
