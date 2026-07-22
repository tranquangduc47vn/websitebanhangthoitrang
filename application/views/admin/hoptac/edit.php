<?php
$admin_form_title = 'Chỉnh sửa bài hợp tác';
$admin_form_breadcrumb = 'Hợp tác';
$admin_form_back_url = admin_url('hoptac');
$this->load->view('admin/partials/form_open');
?>
<form class="admin-form" action="" method="post" enctype="multipart/form-data">
	<div class="mb-3">
		<label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
		<input type="text" name="title" class="form-control" value="<?php echo $info->title; ?>" required>
	</div>
	<div class="mb-3">
		<label class="form-label">Hình ảnh</label>
		<?php if ($info->image) { ?>
			<img src="<?php echo base_url('upload/'.$info->image); ?>" class="d-block mb-2 rounded" style="width:120px">
		<?php } ?>
		<input type="file" name="image" class="form-control">
	</div>
	<div class="mb-3">
		<label class="form-label">Intro</label>
		<textarea name="intro" rows="3" class="form-control"><?php echo $info->intro; ?></textarea>
	</div>
	<div class="mb-3">
		<label class="form-label">Nội dung</label>
		<textarea name="content" rows="8" class="form-control"><?php echo $info->content; ?></textarea>
	</div>
	<div class="admin-form-actions">
		<button type="submit" class="btn btn-primary">Cập nhật</button>
		<a href="<?php echo admin_url('hoptac'); ?>" class="btn btn-default">Hủy bỏ</a>
	</div>
</form>
<?php $this->load->view('admin/partials/form_close'); ?>
