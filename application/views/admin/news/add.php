<?php
$admin_form_title = 'Thêm tin tức mới';
$admin_form_breadcrumb = 'Tin tức';
$admin_form_back_url = admin_url('news');
$this->load->view('admin/partials/form_open');
?>
<?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
<form class="admin-form" action="" method="post" enctype="multipart/form-data">
	<div class="mb-3">
		<label class="form-label">Tiêu đề tin tức <span class="text-danger">*</span></label>
		<input type="text" name="title" class="form-control" value="<?php echo set_value('title'); ?>" required>
	</div>
	<div class="mb-3">
		<label class="form-label">Ảnh đại diện</label>
		<input type="file" name="image" class="form-control">
	</div>
	<div class="mb-3">
		<label class="form-label">Mô tả ngắn (Intro)</label>
		<textarea name="intro" rows="3" class="form-control"><?php echo set_value('intro'); ?></textarea>
	</div>
	<div class="mb-3">
		<label class="form-label">Nội dung chi tiết <span class="text-danger">*</span></label>
		<textarea name="content" rows="10" class="form-control" required><?php echo set_value('content'); ?></textarea>
	</div>
	<div class="admin-form-actions">
		<button type="submit" class="btn btn-primary">Thêm bài viết</button>
		<a href="<?php echo admin_url('news'); ?>" class="btn btn-default">Hủy bỏ</a>
	</div>
</form>
<?php $this->load->view('admin/partials/form_close'); ?>
