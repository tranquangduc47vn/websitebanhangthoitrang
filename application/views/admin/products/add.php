<?php
$admin_form_title = 'Thêm sản phẩm';
$admin_form_breadcrumb = 'Sản phẩm';
$admin_form_back_url = admin_url('products');
$this->load->view('admin/partials/form_open');

$available_colors = array(
	'Đen' => '#000000',
	'Trắng' => '#ffffff',
	'Đỏ' => '#dd021b',
	'Vàng' => '#fcd535',
	'Xanh dương' => '#2196f3',
	'Xanh lá' => '#4caf50',
	'Hồng' => '#ffb6c1',
	'Xám' => '#808080',
	'Nâu' => '#8b5a2b',
	'Kem' => '#f5f5dc',
	'Cam' => '#ff9800',
	'Tím' => '#9c27b0',
);
$available_sizes = array('S', 'M', 'L', 'XL', 'XXL');
$old_colors = (array) $this->input->post('color');
$old_sizes = (array) $this->input->post('size');
?>

<form class="admin-form admin-product-form" action="<?php echo admin_url('products/add'); ?>" method="post" enctype="multipart/form-data" data-product-price-form>
	<div class="row g-4">
		<div class="col-12 col-xl-8">
			<section class="admin-form-section">
				<div class="row g-3">
					<div class="col-12">
						<label class="form-label" for="name">Tên sản phẩm <span class="text-danger">*</span></label>
						<input type="text" name="name" class="form-control form-control-lg" id="name"
							placeholder="Áo sơ mi nữ cổ nơ" value="<?php echo html_escape(set_value('name')); ?>">
						<?php echo form_error('name'); ?>
					</div>

					<div class="col-12">
						<label class="form-label" for="catalog_id">Danh mục <span class="text-danger">*</span></label>
						<select class="form-select" name="catalog_id" id="catalog_id">
							<option value="">Chọn danh mục</option>
							<?php foreach ($catalog as $value) { ?>
								<?php if (!empty($value->sub)) { ?>
									<optgroup label="<?php echo html_escape($value->name); ?>">
										<?php foreach ($value->sub as $val) { ?>
											<option value="<?php echo (int) $val->id; ?>" <?php echo set_select('catalog_id', $val->id); ?>>
												<?php echo html_escape($val->name); ?>
											</option>
										<?php } ?>
									</optgroup>
								<?php } else { ?>
									<option value="<?php echo (int) $value->id; ?>" <?php echo set_select('catalog_id', $value->id); ?>>
										<?php echo html_escape($value->name); ?>
									</option>
								<?php } ?>
							<?php } ?>
						</select>
						<?php echo form_error('catalog_id'); ?>
					</div>

					<div class="col-12 col-md-4">
						<label class="form-label" for="price">Giá bán <span class="text-danger">*</span></label>
						<div class="input-group">
							<input type="text" name="price" class="form-control" id="price"
								placeholder="250,000" inputmode="numeric" value="<?php echo html_escape(set_value('price')); ?>">
							<span class="input-group-text">₫</span>
						</div>
						<?php echo form_error('price'); ?>
					</div>

					<div class="col-12 col-md-4">
						<label class="form-label" for="discount">Giảm giá (%)</label>
						<div class="input-group">
							<input type="text" name="discount" class="form-control" id="discount"
								placeholder="15" inputmode="numeric" maxlength="3"
								value="<?php echo html_escape(set_value('discount')); ?>">
							<span class="input-group-text">%</span>
						</div>
						<input type="hidden" name="discount_type" value="percent">
						<p class="form-text text-muted mb-0" data-discount-preview></p>
					</div>
				</div>
			</section>
		</div>

		<div class="col-12 col-xl-4">
			<section class="admin-form-section h-100">
				<label class="admin-upload-zone" for="image">
					<input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp,image/bmp,.jpg,.jpeg,.png,.webp,.gif,.bmp" required data-image-input="main">
					<span class="admin-upload-placeholder" data-upload-placeholder="main">
						<i class="fa-solid fa-cloud-arrow-up"></i>
						<strong>Ảnh đại diện</strong>
						<small class="d-block mt-1 text-muted">JPG, PNG, GIF, WEBP — tối đa 5MB</small>
					</span>
					<img alt="Xem trước" data-image-preview="main">
				</label>

				<label class="form-label mt-3" for="list_image">Ảnh kèm theo</label>
				<input class="form-control" type="file" id="list_image" name="list_image[]" accept="image/jpeg,image/png,image/gif,image/webp,image/bmp,.jpg,.jpeg,.png,.webp,.gif,.bmp" multiple data-gallery-input>
				<div class="admin-upload-gallery" data-gallery-preview></div>
			</section>
		</div>

		<div class="col-12">
			<section class="admin-form-section">
				<div class="row g-4">
					<div class="col-12 col-lg-8">
						<label class="form-label">Màu sắc</label>
						<div class="admin-option-grid admin-color-grid">
							<?php foreach ($available_colors as $color_name => $hex_code) { ?>
								<label class="admin-option-chip">
									<input type="checkbox" name="color[]" value="<?php echo html_escape($color_name); ?>"
										<?php echo in_array($color_name, $old_colors, true) ? 'checked' : ''; ?>>
									<span class="admin-option-chip-content">
										<span class="admin-color-dot" style="--chip-color: <?php echo $hex_code; ?>"></span>
										<?php echo html_escape($color_name); ?>
									</span>
								</label>
							<?php } ?>
						</div>
					</div>

					<div class="col-12 col-lg-4">
						<label class="form-label">Size</label>
						<div class="admin-option-grid admin-size-grid">
							<?php foreach ($available_sizes as $size_name) { ?>
								<label class="admin-option-chip admin-size-chip">
									<input type="checkbox" name="size[]" value="<?php echo $size_name; ?>"
										<?php echo in_array($size_name, $old_sizes, true) ? 'checked' : ''; ?>>
									<span class="admin-option-chip-content"><?php echo $size_name; ?></span>
								</label>
							<?php } ?>
						</div>
					</div>
				</div>
				<p class="form-text text-muted mt-2 mb-0">Sau khi lưu, hệ thống tự tạo biến thể theo tổ hợp màu × size. Tồn kho mặc định = 0 — dùng <strong>Phiếu nhập</strong> để cập nhật số lượng.</p>
			</section>
		</div>

		<div class="col-12">
			<section class="admin-form-section">
				<label class="form-label" for="content">Mô tả</label>
				<textarea class="form-control" rows="8" name="content" id="content" required><?php echo html_escape(set_value('content')); ?></textarea>
			</section>
		</div>
	</div>

	<div class="admin-product-form-actions">
		<a href="<?php echo admin_url('products'); ?>" class="btn btn-outline-secondary">Hủy</a>
		<button type="submit" class="btn btn-primary px-4">Thêm sản phẩm</button>
	</div>
</form>

<script src="<?php echo base_url('assets/admin/js/product-price.js?v=2'); ?>"></script>
<script>
(function () {
	'use strict';

	function previewMainImage(input) {
		var preview = document.querySelector('[data-image-preview="main"]');
		var placeholder = document.querySelector('[data-upload-placeholder="main"]');
		if (!preview || !input.files || !input.files[0]) return;
		preview.src = URL.createObjectURL(input.files[0]);
		preview.style.display = 'block';
		if (placeholder) placeholder.style.display = 'none';
	}

	var mainInput = document.querySelector('[data-image-input="main"]');
	if (mainInput) {
		mainInput.addEventListener('change', function () { previewMainImage(mainInput); });
	}

	var galleryInput = document.querySelector('[data-gallery-input]');
	var gallery = document.querySelector('[data-gallery-preview]');
	if (galleryInput && gallery) {
		galleryInput.addEventListener('change', function () {
			gallery.innerHTML = '';
			Array.prototype.slice.call(galleryInput.files || []).forEach(function (file) {
				var image = document.createElement('img');
				image.src = URL.createObjectURL(file);
				image.alt = file.name;
				gallery.appendChild(image);
			});
		});
	}

	if (window.CKEDITOR) {
		CKEDITOR.replace('content');
	}
})();
</script>

<?php $this->load->view('admin/partials/form_close'); ?>
