<?php
$admin_form_title = 'Chỉnh sửa: ' . $page['title'];
$admin_form_breadcrumb = 'Trang tĩnh';
$admin_form_back_url = admin_url('pages');
$this->load->view('admin/partials/form_open');
?>
<form class="admin-form" action="" method="post" role="form">

	<div class="mb-3">
		<label class="form-label">Tiêu đề trang</label>
		<input class="form-control" name="title" value="<?php echo $page['title']; ?>" required>
	</div>

	<div class="mb-3">
		<label class="form-label">Đường dẫn (Slug)</label>
		<input class="form-control" value="<?php echo $page['slug']; ?>" disabled>
		<div class="form-text">Đường dẫn cố định — không nên đổi để tránh lỗi link footer.</div>
	</div>

	<div class="mb-3">
		<label class="form-label">Nội dung trang (HTML)</label>
		<textarea class="form-control" name="content" rows="15" required><?php echo $page['content']; ?></textarea>
	</div>

	<div class="admin-form-actions">
		<button type="submit" name="submit" value="true" class="btn btn-primary">Lưu cập nhật</button>
		<a href="<?php echo admin_url('page'); ?>" class="btn btn-default">Hủy bỏ</a>
	</div>
</form>
<?php $this->load->view('admin/partials/form_close'); ?>
