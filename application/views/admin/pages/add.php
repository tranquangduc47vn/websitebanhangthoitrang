<?php
$admin_form_title = 'Thêm trang tĩnh mới';
$admin_form_breadcrumb = 'Trang tĩnh';
$admin_form_back_url = admin_url('pages');
$this->load->view('admin/partials/form_open');
?>
<form class="admin-form" action="" method="post" role="form">

	<div class="mb-3">
		<label class="form-label">Tiêu đề trang</label>
		<input class="form-control" name="title" value="" required placeholder="Nhập tiêu đề trang (Ví dụ: Giới thiệu)">
	</div>

	<div class="mb-3">
		<label class="form-label">Đường dẫn (Slug)</label>
		<input class="form-control" name="slug" value="gioi-thieu" required>
		<div class="form-text">Mặc định nên để là <code>gioi-thieu</code> để khớp menu ngoài trang chủ.</div>
	</div>

	<div class="mb-3">
		<label class="form-label">Nội dung trang</label>
		<textarea class="form-control" name="content" rows="15" required placeholder="Nhập nội dung bài viết vào đây..."></textarea>
	</div>

	<div class="admin-form-actions">
		<button type="submit" name="submit" value="true" class="btn btn-primary">Thêm mới trang</button>
		<a href="<?php echo admin_url('pages'); ?>" class="btn btn-default">Hủy bỏ</a>
	</div>
</form>
<?php $this->load->view('admin/partials/form_close'); ?>
