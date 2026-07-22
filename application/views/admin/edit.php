<?php
$admin_form_title = 'Chỉnh sửa bài viết tuyển dụng';
$admin_form_breadcrumb = 'Tuyển dụng';
$admin_form_back_url = admin_url('tuyendung');
$this->load->view('admin/partials/form_open');
?>
<form class="admin-form" action="" method="post" role="form">
	<div class="mb-3">
		<label class="form-label">Tiêu đề tuyển dụng <span class="text-danger">*</span></label>
		<input type="text" name="title" class="form-control" value="<?php echo $info->title; ?>" required>
	</div>
	<div class="mb-3">
		<label class="form-label">Đường dẫn hình ảnh (URL Image)</label>
		<input type="text" name="image" class="form-control" value="<?php echo $info->image; ?>">
	</div>
	<div class="mb-3">
		<label class="form-label">Mô tả ngắn (Intro)</label>
		<textarea name="intro" class="form-control" rows="3"><?php echo $info->intro; ?></textarea>
	</div>
	<div class="mb-3">
		<label class="form-label">Nội dung chi tiết công việc</label>
		<textarea name="content" id="editor_content_edit" class="form-control" rows="10"><?php echo $info->content; ?></textarea>
	</div>
	<div class="admin-form-actions">
		<button type="submit" class="btn btn-primary">Cập nhật dữ liệu</button>
		<a href="<?php echo admin_url('tuyendung'); ?>" class="btn btn-default">Hủy bỏ</a>
	</div>
</form>
<?php $this->load->view('admin/partials/form_close'); ?>
<script>
	if (typeof CKEDITOR !== 'undefined') {
		CKEDITOR.replace('editor_content_edit');
	}
</script>
