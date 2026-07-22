<?php
$admin_form_title = 'Sửa thông tin thành viên';
$admin_form_breadcrumb = 'Nhân viên';
$admin_form_back_url = admin_url('admin');
$this->load->view('admin/partials/form_open');
?>
<form class="form-horizontal admin-form" name="" method="post">

	<div class="form-group">
		<label for="name" class="col-sm-2 control-label">Họ tên</label>
		<div class="col-sm-5">
			<input type="text" name='name' class="form-control" id="name" value="<?php echo set_value('name', $admin->name); ?>">
		</div>
		<div class="col-sm-4 text-danger"><?php echo form_error('name'); ?></div>
	</div>

	<div class="form-group">
		<label for="email" class="col-sm-2 control-label">Email</label>
		<div class="col-sm-5">
			<input type="email" name='email' class="form-control" id="email" value="<?php echo set_value('email', $admin->email); ?>">
		</div>
		<div class="col-sm-4 text-danger"><?php echo form_error('email'); ?></div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<p class="text-warning mb-2"><em>* Nếu thay đổi mật khẩu thì mới nhập vào ô dưới đây</em></p>
		</div>
		<label for="password" class="col-sm-2 control-label">Mật khẩu</label>
		<div class="col-sm-5">
			<input type="password" name='password' class="form-control" id="password" placeholder="Bỏ trống nếu không đổi">
		</div>
		<div class="col-sm-4 text-danger"><?php echo form_error('password'); ?></div>
	</div>

	<div class="form-group">
		<label for="re_password" class="col-sm-2 control-label">Nhập lại mật khẩu</label>
		<div class="col-sm-5">
			<input type="password" name='re_password' class="form-control" id="re_password" placeholder="Bỏ trống nếu không đổi">
		</div>
		<div class="col-sm-4 text-danger"><?php echo form_error('re_password'); ?></div>
	</div>

	<div class="form-group">
		<label for="level" class="col-sm-2 control-label">Phân quyền</label>
		<div class="col-sm-5">
			<select class="form-control" name="level" id="level">
				<option value="">--- CHỌN ---</option>
				<?php if ((int) $this->session->userdata('login')->level === ROLE_ADMIN) { ?>
					<option value="<?php echo ROLE_ADMIN; ?>" <?php echo set_select('level', ROLE_ADMIN, ($admin->level == ROLE_ADMIN)); ?>>Admin</option>
				<?php } ?>
				<option value="<?php echo ROLE_MOD; ?>" <?php echo set_select('level', ROLE_MOD, ($admin->level == ROLE_MOD)); ?>>Mod</option>
				<option value="<?php echo ROLE_USER; ?>" <?php echo set_select('level', ROLE_USER, ($admin->level == ROLE_USER)); ?>>User</option>
			</select>
		</div>
		<div class="col-sm-4 text-danger"><?php echo form_error('level'); ?></div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-5">
			<button type="submit" class="btn btn-primary">Lưu thay đổi</button>
			<a href="<?php echo admin_url('admin'); ?>" class="btn btn-default">Hủy bỏ</a>
		</div>
	</div>
</form>
<?php $this->load->view('admin/partials/form_close'); ?>
