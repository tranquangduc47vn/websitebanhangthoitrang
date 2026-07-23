<?php
$admin_form_title = 'Chỉnh sửa sản phẩm';
$admin_form_breadcrumb = 'Sản phẩm';
$admin_form_back_url = admin_url('product');
$this->load->view('admin/partials/form_open');
?>
				<form class="form-horizontal admin-form" action="<?php echo admin_url('product/edit/'.$product->id); ?>" method="post" enctype="multipart/form-data" data-product-price-form>
					
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
						<label class="col-sm-2 control-label">Hình ảnh sản phẩm</label>
						<div class="col-sm-10">
							<?php
							$image_list = json_decode($product->image_list);
							if (!is_array($image_list)) {
								$image_list = array();
							}
							$image_list = array_values(array_filter($image_list));
							$posted_main = $this->input->post('product_image_main');
							$posted_list = $this->input->post('product_image_list');
							$hidden_main = ($posted_main !== null && $posted_main !== false)
								? (string) $posted_main
								: (string) $product->image_link;
							$hidden_list = ($posted_list !== null && $posted_list !== false)
								? (string) $posted_list
								: json_encode($image_list);
							?>
							<link rel="stylesheet" href="<?php echo base_url('assets/admin/css/product-images.css?v=2'); ?>">
							<div class="adm-product-images" id="admProductImages" data-adm-product-images
								data-upload-base="<?php echo base_url('upload/product/'); ?>">
								<input type="hidden" name="product_image_main" id="product_image_main"
									value="<?php echo htmlspecialchars($hidden_main, ENT_QUOTES, 'UTF-8'); ?>">
								<input type="hidden" name="product_image_list" id="product_image_list"
									value="<?php echo htmlspecialchars($hidden_list, ENT_QUOTES, 'UTF-8'); ?>">
								<div id="product_images_remove_container"></div>

								<div class="adm-img-zone adm-img-zone--main">
									<div class="adm-img-zone__label">Hình ảnh chính</div>
									<p class="adm-img-zone__hint">Kéo thả để đặt ảnh đại diện</p>
									<div class="adm-img-zone__drop" data-zone="main" id="zone-main"></div>
									<div class="adm-img-zone__upload">
										<input type="file" id="image" name="image" class="form-control form-control-sm"
											accept="image/jpeg,image/png,image/gif,image/webp,image/bmp,.jpg,.jpeg,.png,.webp,.gif,.bmp">
										<p class="help-block" style="font-size:11px;color:#777;margin-top:6px;">Upload ảnh mới thay thế ảnh chính (JPG, PNG, WEBP — tối đa 5MB)</p>
									</div>
								</div>

								<div class="adm-img-zone adm-img-zone--gallery">
									<div class="adm-img-zone__label">Hình ảnh kèm theo</div>
									<p class="adm-img-zone__hint">Kéo thả sắp xếp · kéo sang ảnh chính hoặc xóa trực tiếp</p>
									<div class="adm-img-zone__drop" data-zone="gallery" id="zone-gallery"></div>
									<div class="adm-img-zone__upload">
										<input type="file" id="list_image" name="list_image[]" class="form-control form-control-sm" multiple
											accept="image/jpeg,image/png,image/gif,image/webp,image/bmp,.jpg,.jpeg,.png,.webp,.gif,.bmp">
										<p class="help-block" style="font-size:11px;color:#777;margin-top:6px;">Thêm ảnh mới vào danh sách kèm theo</p>
									</div>
								</div>
							</div>
							<script src="<?php echo base_url('assets/admin/js/product-images.js?v=1'); ?>"></script>
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
							<input type="text" name="price" class="form-control" id="price" inputmode="numeric"
								placeholder="Ví dụ: 1,455,000" value="<?php echo set_value('price', number_format($product->price)); ?>">
						</div>
						<div class="col-sm-4 text-danger">
							<?php echo form_error('price'); ?>
						</div>
					</div>

					<?php
					if ($this->input->post('discount') !== false && $this->input->post('discount') !== null) {
						$discount_display = set_value('discount');
					} elseif ((int) $product->discount_percent > 0) {
						$discount_display = (string) (int) $product->discount_percent;
					} elseif ((int) $product->price > 0 && (int) $product->discount > 0) {
						$discount_display = (string) (int) round($product->discount / $product->price * 100);
					} else {
						$discount_display = '';
					}
					?>
					<div class="form-group">
						<label for="discount" class="col-sm-2 control-label">Giảm giá (%)</label>
						<div class="col-sm-5">
							<div class="input-group">
								<input type="text" name="discount" class="form-control" id="discount" inputmode="numeric"
									placeholder="Ví dụ: 15" maxlength="3"
									value="<?php echo html_escape($discount_display); ?>">
								<span class="input-group-text">%</span>
							</div>
							<input type="hidden" name="discount_type" value="percent">
							<p class="help-block text-muted" data-discount-preview style="font-size:11px;margin-top:6px;margin-bottom:0;"></p>
						</div>
					</div>

					<script src="<?php echo base_url('assets/admin/js/product-price.js?v=2'); ?>"></script>

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
							<p class="help-block" style="font-size: 11px; color: #777; margin-top: 8px; clear: both;">* Lưu sản phẩm để tự tạo biến thể mới theo tổ hợp màu × size (tồn kho cập nhật qua phiếu nhập).</p>
						</div>
					</div>

					<?php if (!empty($variants)) { ?>
					<div class="form-group">
						<label class="col-sm-2 control-label">Biến thể</label>
						<div class="col-sm-8">
							<div class="table-responsive">
								<table class="table table-sm table-bordered">
									<thead>
										<tr>
											<th>SKU</th>
											<th>Màu</th>
											<th>Size</th>
											<th>Giá bán</th>
											<th>Giá nhập</th>
											<th>Trạng thái</th>
										</tr>
									</thead>
									<tbody>
									<?php foreach ($variants as $v) { ?>
										<tr>
											<td><code><?php echo html_escape($v->sku); ?></code></td>
											<td><?php echo html_escape($v->color !== '' ? $v->color : '—'); ?></td>
											<td><?php echo html_escape($v->size !== '' ? $v->size : '—'); ?></td>
											<td><?php echo number_format((float) $v->price); ?> ₫</td>
											<td><?php echo number_format((float) $v->cost_price); ?> ₫</td>
											<td><?php echo (int) $v->status === 1 ? 'Đang bán' : 'Ngưng'; ?></td>
										</tr>
									<?php } ?>
									</tbody>
								</table>
							</div>
							<p class="help-block text-muted mb-0">Tồn kho xem tại menu <a href="<?php echo admin_url('inventory'); ?>">Tồn kho</a>.</p>
						</div>
					</div>
					<?php } ?>

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
