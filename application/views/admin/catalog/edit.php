<?php
$admin_form_title = 'Sửa danh mục';
$admin_form_breadcrumb = 'Danh mục';
$admin_form_back_url = admin_url('catalog');
$this->load->view('admin/partials/form_open');
?>
<form class="form-horizontal admin-form" action="<?php echo admin_url('catalog/edit/'.$catalog->id); ?>" method="post">

	<div class="form-group">
		<label for="name" class="col-sm-2 control-label">Tên danh mục</label>
		<div class="col-sm-5">
			<input type="text" name='name' class="form-control" id="name" value="<?php echo set_value('name', $catalog->name); ?>">
		</div>
		<div class="col-sm-4 text-danger">
			<?php echo form_error('name', '<div class="alert alert-danger py-1 px-2 mb-0" role="alert">', '</div>'); ?>
		</div>
	</div>

	<div class="form-group">
		<label for="description" class="col-sm-2 control-label">Mô tả</label>
		<div class="col-sm-5">
			<textarea class="form-control" rows="3" id="description" name="description"><?php echo set_value('description', $catalog->description); ?></textarea>
		</div>
		<div class="col-sm-4 text-danger">
			<?php echo form_error('description'); ?>
		</div>
	</div>

	<div class="form-group">
		<label for="parent_id" class="col-sm-2 control-label">Danh mục cha</label>
		<div class="col-sm-5">
			<select class="form-control" name="parent_id" id="parent_id">
				<option value='0' <?php echo set_select('parent_id', '0', ($catalog->parent_id == 0)); ?>>Menu gốc</option>
				<option value='1' <?php echo set_select('parent_id', '1', ($catalog->parent_id == 1)); ?>>Thời trang</option>
				<?php foreach ($list as $value) {
					if ($value->id != $catalog->id) {
						if ($value->parent_id > 0) { ?>
							<option value="<?php echo $value->id; ?>" <?php echo set_select('parent_id', $value->id, ($catalog->parent_id == $value->id)); ?>>&nbsp;&nbsp;&nbsp;<?php echo $value->name; ?></option>
						<?php } else { ?>
							<option value="<?php echo $value->id; ?>" <?php echo set_select('parent_id', $value->id, ($catalog->parent_id == $value->id)); ?>><?php echo $value->name; ?></option>
				<?php } } } ?>
			</select>
		</div>
		<div class="col-sm-4 text-danger">
			<?php echo form_error('parent_id'); ?>
		</div>
	</div>

	<div class="form-group">
		<label for="sort_order" class="col-sm-2 control-label">Thứ tự</label>
		<div class="col-sm-5">
			<select class="form-control" name="sort_order" id="sort_order">
				<?php for ($i = 1; $i < 10; $i++) { ?>
					<option value='<?php echo $i; ?>' <?php echo set_select('sort_order', $i, ($catalog->sort_order == $i)); ?>><?php echo $i; ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="col-sm-4 text-danger">
			<?php echo form_error('sort_order'); ?>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-5">
			<button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Lưu thay đổi</button>
			<a href="<?php echo admin_url('catalog'); ?>" class="btn btn-default">Hủy bỏ</a>
		</div>
	</div>
</form>
<?php $this->load->view('admin/partials/form_close'); ?>
