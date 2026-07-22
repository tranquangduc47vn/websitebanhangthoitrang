<?php
$admin_form_title = 'Chỉnh sửa bài viết';
$admin_form_breadcrumb = 'Tin tức';
$admin_form_back_url = admin_url('news');
$this->load->view('admin/partials/form_open');
?>
<?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
<form class="admin-form" action="" method="post" enctype="multipart/form-data">
	<div class="mb-3">
		<label class="form-label">Tiêu đề tin tức <span class="text-danger">*</span></label>
		<input type="text" name="title" class="form-control" value="<?php echo (set_value('title')) ? set_value('title') : $news->title; ?>" required>
	</div>
	<div class="mb-3">
		<label class="form-label">Ảnh đại diện hiện tại</label>
		<img src="<?php echo base_url('upload/news/'.$news->image_link); ?>" width="120" class="d-block mb-2 rounded border" onerror="this.style.display='none';">
		<input type="file" name="image" class="form-control">
		<div class="form-text">Chỉ chọn nếu muốn thay ảnh mới.</div>
	</div>
	<div class="mb-3">
		<label class="form-label">Mô tả ngắn (Intro)</label>
		<textarea name="intro" rows="3" class="form-control"><?php echo (set_value('intro')) ? set_value('intro') : $news->intro; ?></textarea>
	</div>
	<div class="mb-3">
		<label class="form-label">Nội dung chi tiết <span class="text-danger">*</span></label>
		<textarea name="content" rows="10" class="form-control" required><?php echo (set_value('content')) ? set_value('content') : $news->content; ?></textarea>
	</div>
	<div class="admin-form-actions">
		<button type="submit" class="btn btn-primary">Cập nhật bài viết</button>
		<a href="<?php echo admin_url('news'); ?>" class="btn btn-default">Hủy bỏ</a>
	</div>
</form>
<?php $this->load->view('admin/partials/form_close'); ?>
