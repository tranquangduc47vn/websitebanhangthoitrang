<?php
$admin_form_title = 'Thêm vị trí tuyển dụng';
$admin_form_breadcrumb = 'Tuyển dụng';
$admin_form_back_url = admin_url('tuyendung');
$this->load->view('admin/partials/form_open');
?>
<form class="admin-form" action="<?php echo admin_url('tuyendung/add'); ?>" method="post" enctype="multipart/form-data">
	<div class="mb-3">
		<label class="form-label">Tiêu đề tuyển dụng</label>
		<input type="text" name="title" class="form-control" required placeholder="Ví dụ: TUYỂN DỤNG NHÂN VIÊN SOURCING">
	</div>
	<div class="mb-3">
		<label class="form-label">Hình ảnh minh họa</label>
		<input type="file" name="image" class="form-control" required>
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
		<a href="<?php echo admin_url('tuyendung'); ?>" class="btn btn-default">Quay lại</a>
	</div>
</form>
<?php $this->load->view('admin/partials/form_close'); ?>
