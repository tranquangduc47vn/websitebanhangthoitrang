<?php
$admin_form_title = 'Chỉnh sửa tuyển dụng #' . $info->id;
$admin_form_breadcrumb = 'Tuyển dụng';
$admin_form_back_url = admin_url('tuyendung');
$this->load->view('admin/partials/form_open');
?>
<form class="admin-form" action="<?php echo admin_url('tuyendung/edit/'.$info->id); ?>" method="post" enctype="multipart/form-data">
	<div class="mb-3">
		<label class="form-label">Tiêu đề tuyển dụng</label>
		<input type="text" name="title" class="form-control" required value="<?php echo $info->title; ?>">
	</div>
	<div class="mb-3">
		<label class="form-label">Hình ảnh hiện tại</label>
		<img src="<?php echo base_url('upload/'.$info->image); ?>" width="120" height="75" class="d-block mb-2 rounded border" style="object-fit:cover" onerror="this.src='<?php echo base_url('upload/no-image.jpg'); ?>'">
		<input type="file" name="image" class="form-control">
		<div class="form-text">Để trống nếu không đổi ảnh.</div>
	</div>
	<div class="mb-3">
		<label class="form-label">Intro</label>
		<textarea name="intro" rows="3" class="form-control"><?php echo $info->intro; ?></textarea>
	</div>
	<div class="mb-3">
		<label class="form-label">Nội dung chi tiết</label>
		<textarea name="content" rows="8" class="form-control"><?php echo $info->content; ?></textarea>
	</div>
	<div class="admin-form-actions">
		<button type="submit" class="btn btn-primary">Cập nhật bài viết</button>
		<a href="<?php echo admin_url('tuyendung'); ?>" class="btn btn-default">Quay lại</a>
	</div>
</form>
<?php $this->load->view('admin/partials/form_close'); ?>
