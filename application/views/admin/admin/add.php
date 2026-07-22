<?php
$admin_form_title = 'Thêm quản trị viên';
$admin_form_breadcrumb = 'Nhân viên';
$admin_form_back_url = admin_url('admin');
$this->load->view('admin/partials/form_open');
?>
<form class="form-horizontal admin-form" name="" method="post">

	<div class="form-group">
		<label for="name" class="col-sm-2 control-label">Họ tên</label>
		<div class="col-sm-5">
			<input type="text" name='name' class="form-control" id="name" placeholder="Nhập họ tên" value="<?php echo set_value('name'); ?>">
		</div>
		<div class="col-sm-4 text-danger"><?php echo form_error('name'); ?></div>
	</div>

	<div class="form-group">
		<label for="email" class="col-sm-2 control-label">Email</label>
		<div class="col-sm-5">
			<input type="email" name='email' class="form-control" id="email" placeholder="Nhập địa chỉ email" value="<?php echo set_value('email'); ?>">
		</div>
		<div class="col-sm-4 text-danger"><?php echo form_error('email'); ?></div>
	</div>

	<div class="form-group">
		<label for="password" class="col-sm-2 control-label">Mật khẩu</label>
		<div class="col-sm-5">
			<input type="password" name='password' class="form-control" id="password" placeholder="Nhập mật khẩu">
		</div>
		<div class="col-sm-4 text-danger"><?php echo form_error('password'); ?></div>
	</div>

	<div class="form-group">
		<label for="re_password" class="col-sm-2 control-label">Nhập lại mật khẩu</label>
		<div class="col-sm-5">
			<input type="password" name='re_password' class="form-control" id="re_password" placeholder="Nhập lại mật khẩu">
		</div>
		<div class="col-sm-4 text-danger"><?php echo form_error('re_password'); ?></div>
	</div>

	<div class="form-group">
		<label for="level" class="col-sm-2 control-label">Phân quyền</label>
		<div class="col-sm-5">
			<select class="form-control" name="level" id="level">
				<option value="">--- CHỌN ---</option>
				<?php if ((int) $this->session->userdata('login')->level === ROLE_ADMIN) { ?>
					<option value="<?php echo ROLE_ADMIN; ?>" <?php echo set_select('level', ROLE_ADMIN); ?>>Admin</option>
				<?php } ?>
				<option value="<?php echo ROLE_MOD; ?>" <?php echo set_select('level', ROLE_MOD); ?>>Mod</option>
				<option value="<?php echo ROLE_USER; ?>" <?php echo set_select('level', ROLE_USER); ?>>User</option>
			</select>
		</div>
		<div class="col-sm-4 text-danger"><?php echo form_error('level'); ?></div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-5">
			<button type="submit" class="btn btn-primary">Thêm mới</button>
			<a href="<?php echo admin_url('admin'); ?>" class="btn btn-default">Hủy bỏ</a>
		</div>
	</div>
</form>
<?php $this->load->view('admin/partials/form_close'); ?>
