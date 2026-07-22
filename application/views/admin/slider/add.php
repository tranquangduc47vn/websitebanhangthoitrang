<?php
$admin_form_title = 'Thêm slider';
$admin_form_breadcrumb = 'Slider';
$admin_form_back_url = admin_url('slider');
$this->load->view('admin/partials/form_open');
?>
				<form class="form-horizontal admin-form" name="" method="post" enctype="multipart/form-data">
				  <div class="form-group">
				    <label for="inputEmail3" class="col-sm-2 control-label">Tên slider</label>
				    <div class="col-sm-5">
				      <input type="text" name='name' class="form-control" id="inputEmail3" placeholder="" value="<?php echo set_value('name'); ?>">
				    </div>
				    <div class="col-sm-4">
				    	<?php echo form_error('name'); ?>
					</div>
				  </div>
				  <div class="form-group">
				    <label for="inputEmail3" class="col-sm-2 control-label">Hình ảnh</label>
				    <div class="col-sm-5">
				      <input type="file" id="image" name="image">
				    </div>
				  </div>
				  <div class="form-group">
				    <label for="inputEmail3" class="col-sm-2 control-label">Liên kết</label>
				    <div class="col-sm-5">
				      <input type="text" name='link' class="form-control" id="inputEmail3" placeholder="" value="<?php echo set_value('link'); ?>">
				    </div>
				    <div class="col-sm-4">
				    	<?php echo form_error('link'); ?>
					</div>
				  </div>
				  <div class="form-group">
				    <label for="inputEmail3" class="col-sm-2 control-label">Thứ tự</label>
				    <div class="col-sm-5">
				        <select class="form-control" name="sort_order">
						  <?php for ($i=1; $i < 6 ; $i++) { ?>
						  	<option value="<?php echo $i ?>"><?php echo $i ?></option>
						  <?php } ?>
						</select>
						<div class="col-sm-4">
				    	<?php echo form_error('sort_order'); ?>
					</div>
				    </div>
				  </div>
				  <div class="form-group">
				    <div class="col-sm-offset-2 col-sm-5">
				      <button type="submit" class="btn btn-primary">Thêm mới</button>
				    </div>
				  </div>
				</form>
<?php $this->load->view('admin/partials/form_close'); ?>
