<?php
$admin_form_title = 'Thêm bài viết hợp tác';
$admin_form_breadcrumb = 'Hợp tác';
$admin_form_back_url = admin_url('hoptac');
$this->load->view('admin/partials/form_open');
?>
<form class="admin-form" action="" method="post" enctype="multipart/form-data">
	<div class="mb-3">
		<label class="form-label">Tiêu đề bài viết <span class="text-danger">*</span></label>
		<input type="text" name="title" class="form-control" required>
	</div>
	<div class="mb-3">
		<label class="form-label">Hình ảnh đại diện</label>
		<input type="file" name="image" class="form-control">
	</div>
	<div class="mb-3">
		<label class="form-label">Mô tả ngắn (Intro)</label>
		<textarea name="intro" rows="3" class="form-control"></textarea>
	</div>
	<div class="mb-3">
		<label class="form-label">Nội dung chi tiết</label>
		<textarea name="content" rows="8" class="form-control"></textarea>
	</div>
	<div class="admin-form-actions">
		<button type="submit" class="btn btn-primary">Lưu bài viết</button>
		<a href="<?php echo admin_url('hoptac'); ?>" class="btn btn-default">Hủy bỏ</a>
	</div>
</form>
<?php $this->load->view('admin/partials/form_close'); ?>
