<?php
$admin_form_title = 'Thêm sản phẩm';
$admin_form_breadcrumb = 'Sản phẩm';
$admin_form_back_url = admin_url('product');
$this->load->view('admin/partials/form_open');
?>
				<form class="form-horizontal admin-form" action="<?php echo admin_url('product/add'); ?>" method="post" enctype="multipart/form-data" data-product-price-form>
				
					<div class="form-group">
						<label for="name" class="col-sm-2 control-label">Tên sản phẩm</label>
						<div class="col-sm-5">
							<input type="text" name='name' class="form-control" id="name" placeholder="Ví dụ: Áo sơ mi nam lụa" value="<?php echo set_value('name'); ?>">
						</div>
						<div class="col-sm-4 text-danger">
							<?php echo form_error('name'); ?>
						</div>
					</div>

					<div class="form-group">
						<label for="image" class="col-sm-2 control-label">Hình ảnh</label>
						<div class="col-sm-5">
							<input type="file" id="image" name="image" required accept="image/jpeg,image/png,image/gif,image/webp,image/bmp,.jpg,.jpeg,.png,.webp,.gif,.bmp">
							<p class="help-block" style="font-size:11px;color:#777;margin-top:6px;">JPG, PNG, GIF, WEBP — tối đa 5MB</p>
						</div>
					</div>

					<div class="form-group">
						<label for="list_image" class="col-sm-2 control-label">Hình ảnh kèm theo</label>
						<div class="col-sm-5">
							<input type="file" id="list_image" name="list_image[]" multiple accept="image/jpeg,image/png,image/gif,image/webp,image/bmp,.jpg,.jpeg,.png,.webp,.gif,.bmp">
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label">Danh mục</label>
						<div class="col-sm-5">
							<select class="form-control" name="catalog_id">
								<option value="">--- Chọn danh mục sản phẩm ---</option>
								<?php 
								foreach ($catalog as $value) { 
									if (count($value->sub) > 0) { 
										?>
										<option value="<?php echo $value->id; ?>" disabled style="font-weight: bold; color: #000; background-color: #eee;">
											<?php echo $value->name; ?>
										</option>
										<?php 
										foreach ($value->sub as $val) { 
											?>
											<option value="<?php echo $val->id; ?>" <?php echo set_select('catalog_id', $val->id); ?>>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;— <?php echo $val->name; ?>
											</option>
											<?php 
										}
									} else {
										?>
										<option value="<?php echo $value->id; ?>" <?php echo set_select('catalog_id', $value->id); ?>>
											<?php echo $value->name; ?>
										</option>
										<?php 
									} 
								} 
								?>
							</select>
						</div>
						<div class="col-sm-4 text-danger">
							<?php echo form_error('catalog_id'); ?>
						</div>
					</div>

					<div class="form-group">
						<label for="price" class="col-sm-2 control-label">Giá tiền (VNĐ)</label>
						<div class="col-sm-5">
							<input type="text" name="price" class="form-control" id="price" inputmode="numeric"
								placeholder="Ví dụ: 250,000" value="<?php echo set_value('price'); ?>">
						</div>
						<div class="col-sm-4 text-danger">
							<?php echo form_error('price'); ?>
						</div>
					</div>

					<div class="form-group">
						<label for="discount" class="col-sm-2 control-label">Giảm giá (%)</label>
						<div class="col-sm-5">
							<div class="input-group">
								<input type="text" name="discount" class="form-control" id="discount" inputmode="numeric"
									placeholder="Ví dụ: 15" maxlength="3"
									value="<?php echo html_escape(set_value('discount')); ?>">
								<span class="input-group-text">%</span>
							</div>
							<input type="hidden" name="discount_type" value="percent">
							<p class="help-block text-muted" data-discount-preview style="font-size:11px;margin-top:6px;margin-bottom:0;"></p>
						</div>
					</div>

					<script src="<?php echo base_url('assets/admin/js/product-price.js?v=2'); ?>"></script>

					<div class="form-group">
						<label for="quantity" class="col-sm-2 control-label">Số lượng tồn kho</label>
						<div class="col-sm-5">
							<input type="number" name='quantity' class="form-control" id="quantity" placeholder="Ví dụ: 100" value="<?php echo set_value('quantity', 0); ?>" min="0">
						</div>
						<div class="col-sm-4 text-danger">
							<?php echo form_error('quantity'); ?>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label">Màu sắc</label>
						<div class="col-sm-8">
							<div class="color-checkbox-wrapper" style="display: flex; flex-wrap: wrap; gap: 15px; padding-top: 7px;">
								<?php
								$available_colors = [
									'Đen' => '#000000',
									'Trắng' => '#ffffff',
									'Đỏ' => '#dd021b',
									'Vàng' => '#fcd535',
									'Xanh dương' => '#2196f3',
									'Xanh lá' => '#4caf50',
									'Hồng' => '#ffb6c1',
									'Xám' => '#808080',
									'Nâu' => '#8b5a2b',
									'Kem' => '#f5f5dc',
									'Cam' => '#ff9800',
									'Tím' => '#9c27b0'
								];

								$old_colors = $this->input->post('color') ? $this->input->post('color') : [];

								foreach ($available_colors as $color_name => $hex_code):
									$checked = in_array($color_name, $old_colors) ? 'checked' : '';
									$border = ($hex_code == '#ffffff') ? '1px solid #ccc' : '1px solid ' . $hex_code;
								?>
									<label style="display: flex; align-items: center; gap: 6px; font-weight: normal; cursor: pointer; user-select: none;">
										<input type="checkbox" name="color[]" value="<?php echo $color_name; ?>" <?php echo $checked; ?> style="cursor: pointer;">
										<span style="display: inline-block; width: 16px; height: 16px; border-radius: 50%; background-color: <?php echo $hex_code; ?>; border: <?php echo $border; ?>; vertical-align: middle;"></span>
										<?php echo $color_name; ?>
									</label>
								<?php endforeach; ?>
							</div>
							<p class="help-block" style="font-size: 11px; color: #777; margin-top: 8px; clear: both;">* Tích chọn các màu sắc có sẵn của sản phẩm này.</p>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label">Kích cỡ (Size)</label>
						<div class="col-sm-8">
							<div class="size-checkbox-wrapper" style="display: flex; flex-wrap: wrap; gap: 20px; padding-top: 7px;">
								<?php
								$available_sizes = ['S', 'M', 'L', 'XL', 'XXL'];
								$old_sizes = $this->input->post('size') ? $this->input->post('size') : [];

								foreach ($available_sizes as $size_name):
									$checked = in_array($size_name, $old_sizes) ? 'checked' : '';
								?>
									<label style="display: flex; align-items: center; gap: 6px; font-weight: normal; cursor: pointer; user-select: none; font-size: 14px;">
										<input type="checkbox" name="size[]" value="<?php echo $size_name; ?>" <?php echo $checked; ?> style="cursor: pointer; width: 16px; height: 16px;">
										<strong><?php echo $size_name; ?></strong>
									</label>
								<?php endforeach; ?>
							</div>
							<p class="help-block" style="font-size: 11px; color: #777; margin-top: 8px; clear: both;">* Tích chọn các kích cỡ có sẵn của sản phẩm này.</p>
						</div>
					</div>

					<div class="form-group">
						<label for="content" class="col-sm-2 control-label">Chi tiết</label>
						<div class="col-sm-8">
							<textarea class="form-control" rows="3" name="content" id='content' required><?php echo set_value('content'); ?></textarea>
						</div>
					</div>
					<script>CKEDITOR.replace('content');</script>
					
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-5">
							<button type="submit" class="btn btn-primary">Thêm mới</button>
							<a href="<?php echo admin_url('product'); ?>" class="btn btn-default">Hủy bỏ</a>
						</div>
					</div>
				</form>
<?php $this->load->view('admin/partials/form_close'); ?>