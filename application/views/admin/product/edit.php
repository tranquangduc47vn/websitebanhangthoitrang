<?php
$admin_form_title = 'Chỉnh sửa sản phẩm';
$admin_form_breadcrumb = 'Sản phẩm';
$admin_form_back_url = admin_url('product');
$this->load->view('admin/partials/form_open');
?>
				<form class="form-horizontal admin-form" action="<?php echo admin_url('product/edit/'.$product->id); ?>" method="post" enctype="multipart/form-data">
					
					<div class="form-group">
						<label for="name" class="col-sm-2 control-label">Tên sản phẩm</label>
						<div class="col-sm-5">
							<input type="text" name='name' class="form-control" id="name" value="<?php echo set_value('name', $product->name); ?>">
						</div>
						<div class="col-sm-4 text-danger">
							<?php echo form_error('name'); ?>
						</div>
					</div>

					<div class="form-group">
						<label for="image" class="col-sm-2 control-label">Hình ảnh chính</label>
						<div class="col-sm-10">
							<?php if(!empty($product->image_link)): ?>
								<img src="<?php echo base_url('upload/product/'.$product->image_link); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; float: left; margin-right: 15px; border: 1px solid #ddd; padding: 2px;">
							<?php endif; ?>
							<div style="float: left; width: 250px;">
								<input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp,image/bmp,.jpg,.jpeg,.png,.webp,.gif,.bmp">
								<p class="help-block" style="font-size:11px;color:#777;margin-top:6px;">JPG, PNG, GIF, WEBP — tối đa 5MB. Để trống nếu giữ ảnh cũ.</p>
								<p class="help-block" style="font-size: 11px;">* Chỉ chọn nếu muốn thay đổi ảnh đại diện mới.</p>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="list_image" class="col-sm-2 control-label">Hình ảnh kèm theo</label>
						<div class="col-sm-10">
							<div style="margin-bottom: 10px; overflow: hidden;">
								<?php
								$image_list = json_decode($product->image_list);
								if (is_array($image_list)) {
									foreach ($image_list as $img_val) {
										?>
										<img src="<?php echo base_url('upload/product/'.$img_val); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; float: left; margin-right: 10px; border: 1px solid #ddd; padding: 2px;">
										<?php
									}
								}
								?>
							</div>
							<div style="width: 250px;">
								<input type="file" id="list_image" name="list_image[]" multiple accept="image/jpeg,image/png,image/gif,image/webp,image/bmp,.jpg,.jpeg,.png,.webp,.gif,.bmp">
								<p class="help-block" style="font-size: 11px; color: #777;">* Chỉ chọn nếu muốn thay đổi danh sách ảnh kèm theo mới.</p>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="content" class="col-sm-2 control-label">Chi tiết</label>
						<div class="col-sm-8">
							<textarea class="form-control" rows="3" name="content" id='content'><?php echo set_value('content', $product->content); ?></textarea>
						</div>
					</div>
					<script>CKEDITOR.replace('content');</script>
				
					<div class="form-group">
						<label class="col-sm-2 control-label">Danh mục</label>
						<div class="col-sm-5">
							<select class="form-control" name="catalog_id">
								<option value="">--- Chọn danh mục sản phẩm ---</option>
								<?php 
								foreach ($catalog as $value) { 
									?>
									<option value="<?php echo $value->id; ?>" disabled style="font-weight: bold; color: #000; background-color: #eee;">
										<?php echo $value->name; ?>
									</option>
									
									<?php 
									if (isset($value->sub) && !empty($value->sub)) {
										foreach ($value->sub as $val) { 
											?>
											<option value="<?php echo $val->id; ?>" <?php echo set_select('catalog_id', $val->id, ($product->catalog_id == $val->id)); ?>>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;— <?php echo $val->name; ?>
											</option>
											<?php 
										}
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
							<input type="text" name='price' class="form-control" id="price" value="<?php echo set_value('price', number_format($product->price)); ?>">
						</div>
						<div class="col-sm-4 text-danger">
							<?php echo form_error('price'); ?>
						</div>
					</div>

					<div class="form-group">
						<label for="discount" class="col-sm-2 control-label">Giảm giá (VNĐ)</label>
						<div class="col-sm-5">
							<input type="text" name='discount' class="form-control" id="discount" value="<?php echo set_value('discount', number_format($product->discount)); ?>">
						</div>
					</div>

					<div class="form-group">
						<label for="quantity" class="col-sm-2 control-label">Số lượng tồn kho</label>
						<div class="col-sm-5">
							<input type="number" name='quantity' class="form-control" id="quantity" placeholder="Ví dụ: 100" value="<?php echo set_value('quantity', isset($product->quantity) ? intval($product->quantity) : 0); ?>" min="0">
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

								$current_colors = isset($product->color) ? explode(',', $product->color) : [];
								$current_colors = array_map('trim', $current_colors);

								if (is_array($this->input->post('color'))) {
									$current_colors = $this->input->post('color');
								}

								foreach ($available_colors as $color_name => $hex_code):
									$checked = in_array($color_name, $current_colors) ? 'checked' : '';
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

								$current_sizes = isset($product->size) ? explode(',', $product->size) : [];
								$current_sizes = array_map('trim', $current_sizes);

								if (is_array($this->input->post('size'))) {
									$current_sizes = $this->input->post('size');
								}

								foreach ($available_sizes as $size_name):
									$checked = in_array($size_name, $current_sizes) ? 'checked' : '';
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
						<div class="col-sm-offset-2 col-sm-5">
							<button type="submit" class="btn btn-primary">Lưu thay đổi</button>
							<a href="<?php echo admin_url('product'); ?>" class="btn btn-default">Hủy bỏ</a>
						</div>
					</div>
				</form>

				<?php
				$login = $this->session->userdata('login');
				if ($login && admin_can('product.delete_single', $login)) {
				?>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-8">
				<div class="admin-danger-zone">
					<div id="product-delete-message"></div>
					<h5 class="admin-danger-zone__title"><i class="fa-solid fa-triangle-exclamation me-1"></i> Xóa sản phẩm</h5>
					<p class="admin-danger-zone__text">
						Xóa vĩnh viễn <strong><?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?></strong>
						(#<?php echo (int) $product->id; ?>). Ảnh trên server cũng bị xóa. Thao tác không hoàn tác.
					</p>
					<button type="button" class="btn btn-danger btn-sm" id="admin-product-delete-btn"
						data-id="<?php echo (int) $product->id; ?>"
						data-name="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>">
						<i class="fa-solid fa-trash me-1"></i> Xóa sản phẩm này
					</button>
				</div>
				<script>
				(function ($) {
					$('#admin-product-delete-btn').on('click', function () {
						var id = $(this).data('id');
						var name = $(this).data('name');
						if (!confirm('Bạn chắc chắn muốn xóa sản phẩm "' + name + '"?\nDữ liệu và hình ảnh sẽ mất hoàn toàn.')) {
							return;
						}
						var $btn = $(this).prop('disabled', true);
						$.ajax({
							url: '<?php echo admin_url('product/del'); ?>',
							type: 'post',
							data: { id: id },
							success: function (response) {
								if (String(response).trim() === 'success') {
									window.location.href = '<?php echo admin_url('products'); ?>';
									return;
								}
								$btn.prop('disabled', false);
								$('#product-delete-message').html('<div class="alert alert-danger mb-2">Không thể xóa sản phẩm. Kiểm tra quyền hoặc thử lại.</div>');
							},
							error: function () {
								$btn.prop('disabled', false);
								$('#product-delete-message').html('<div class="alert alert-danger mb-2">Lỗi kết nối khi xóa sản phẩm.</div>');
							}
						});
					});
				})(jQuery);
				</script>
					</div>
				</div>
				<?php } ?>
<?php $this->load->view('admin/partials/form_close'); ?>