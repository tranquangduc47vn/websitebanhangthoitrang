<?php
$admin_form_title = 'Chỉnh sửa banner #' . $banner->sort_order;
$admin_form_breadcrumb = 'Banner';
$admin_form_back_url = admin_url('banner');
$this->load->view('admin/partials/form_open');
?>
<form class="admin-form" id="form" action="" method="post" enctype="multipart/form-data">
	<div class="mb-3">
		<label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
		<input name="name" class="form-control" value="<?php echo $banner->name; ?>" type="text" required>
	</div>
	<div class="mb-3">
		<label class="form-label">Hình ảnh hiện tại</label>
		<img src="<?php echo base_url('upload/slider/'.$banner->image_link); ?>" height="100" class="d-block mb-2 rounded border">
	</div>
	<div class="mb-3">
		<label class="form-label">Ảnh mới (tùy chọn)</label>
		<input type="file" id="image" name="image" class="form-control">
		<div class="form-text">Để trống nếu không đổi ảnh.</div>
	</div>
	<div class="mb-3">
		<label class="form-label">Đường dẫn liên kết <span class="text-danger">*</span></label>
		<input name="link" class="form-control" value="<?php echo $banner->link; ?>" type="text" required>
	</div>
	<div class="admin-form-actions">
		<button type="submit" name="submit" class="btn btn-primary">Cập nhật ngay</button>
		<a href="<?php echo admin_url('banner'); ?>" class="btn btn-default">Quay lại</a>
	</div>
</form>
<?php $this->load->view('admin/partials/form_close'); ?>
