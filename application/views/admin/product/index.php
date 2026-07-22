<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item active" aria-current="page">Sản phẩm</li>
		</ol>
	</nav>
</div>

<?php $this->load->helper('export'); admin_export_toolbar('products'); ?>

<div class="admin-card mb-3">
	<div class="admin-card-body">
		<form class="row g-2 align-items-end" action="<?php echo admin_url('product/index'); ?>" method="get">
			<div class="col-md-4">
				<label for="search_name" class="form-label">Tên sản phẩm</label>
				<input type="text" name="name" class="form-control form-control-sm" id="search_name" placeholder="Nhập tên cần tìm..." value="<?php echo isset($search_name) ? $search_name : ''; ?>">
			</div>
			<div class="col-md-4">
				<label for="search_catalog" class="form-label">Danh mục</label>
				<select name="catalog_id" class="form-select form-select-sm" id="search_catalog">
							<option value="">-- Tất cả danh mục --</option>
							<?php 
							foreach ($catalog as $value) { 
								if (count($value->sub) > 0) { 
									?>
									<option value="<?php echo $value->id; ?>" disabled style="font-weight: bold; color: #000; background-color: #eee;">
										<?php echo $value->name; ?>
									</option>
									<?php 
									foreach ($value->sub as $val) { 
										$selected = (isset($search_catalog) && $search_catalog == $val->id) ? 'selected' : '';
										?>
										<option value="<?php echo $val->id; ?>" <?php echo $selected; ?>>
											&nbsp;&nbsp;&nbsp;— <?php echo $val->name; ?>
										</option>
										<?php 
									}
								} else {
									$selected = (isset($search_catalog) && $search_catalog == $value->id) ? 'selected' : '';
									?>
									<option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo $value->name; ?></option>
									<?php 
								} 
							} 
							?>
					</select>
			</div>
			<div class="col-md-4">
				<button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-search me-1"></i> Tìm kiếm</button>
				<?php if (!empty($search_name) || !empty($search_catalog)) { ?>
					<a href="<?php echo admin_url('product'); ?>" class="btn btn-default btn-sm">Xóa bộ lọc</a>
				<?php } ?>
			</div>
		</form>
	</div>
</div>

<div id="message"></div>

<?php if (!empty($message_success)) { ?>
	<div class="alert alert-success"><strong>Thành công!</strong> <?php echo $message_success; ?></div>
<?php } ?>
<?php if (!empty($message_fail)) { ?>
	<div class="alert alert-danger"><strong>Lỗi!</strong> <?php echo $message_fail; ?></div>
<?php } ?>

<div class="admin-card">
	<div class="admin-card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
		<span>Quản lý sản phẩm (Tìm thấy: <strong><?php echo $total; ?></strong>)</span>
		<a href="<?php echo admin_url('product/add'); ?>" class="btn btn-sm btn-success"><i class="fa-solid fa-plus me-1"></i> Thêm sản phẩm</a>
	</div>
	<div class="admin-card-body">
				<div class="table-responsive">
					<table class="table table-hover table-bordered admin-table">
						<thead>
							<tr class="info">
								<?php if ($this->session->userdata('login')->level == ROLE_ADMIN): ?>
									<th width="3%" class="text-center"><input type="checkbox" id="select-all" /></th>
								<?php endif; ?>
								
								<th class="text-center" width="5%">ID</th>
								<th>Tên sản phẩm</th>
								<th>Danh mục</th>
								<th>Màu sắc</th>
								<th>Kích cỡ</th>
								<th>Giá bán</th>		
								<th class="text-center" width="8%">Kho hàng</th> <th class="text-center">Lượt xem</th>
								<th class="text-center">Đánh giá</th>
								<th class="text-center" width="10%">Hành động</th>
							</tr>
						</thead>
						<tbody>
						<?php if(!empty($product)): ?>
							<?php foreach ($product as $value) { ?>
							<tr>
								<?php if ($this->session->userdata('login')->level == ROLE_ADMIN): ?>
									<td style="vertical-align: middle; text-align: center;">
										<input type="checkbox" name="checkbox[]" class="case" value="<?php echo $value->id; ?>" />
									</td>
								<?php endif; ?>

								<td style="vertical-align: middle; text-align: center;">
									<strong><?php echo $value->id; ?></strong>
								</td>

								<td style="vertical-align: middle;">
									<?php if(!empty($value->image_link)): ?>
										<img src="<?php echo base_url('upload/product/'.$value->image_link); ?>" alt="" style="width:50px; height:50px; object-fit:cover; float:left; margin-right:10px; border:1px solid #ddd;">
									<?php endif; ?>
									<strong style="color: #333;"><?php echo $value->name; ?></strong>
									<p style="font-size:11px; margin: 4px 0 0 0; color: #777;">
										Đã bán: <?php echo $value->buyed; ?>
									</p>
								</td>

								<td style="vertical-align: middle">
									<span><?php echo $value->namecatalog; ?></span>
								</td>

								<td style="vertical-align: middle">
									<?php echo !empty($value->color) ? $value->color : '-'; ?>
								</td>

								<td style="vertical-align: middle">
									<?php echo !empty($value->size) ? $value->size : '-'; ?>
								</td>

								<td style="vertical-align: middle">
									<?php if($value->discount > 0):   
										$price_new = $value->price - $value->discount; ?>
										<strong style="color:red;"><?php echo number_format($price_new); ?> đ</strong><br>
										<del style="color:#999; font-size:12px;"><?php echo number_format($value->price); ?> đ</del>
									<?php else: ?>
										<strong><?php echo number_format($value->price); ?> đ</strong>
									<?php endif; ?>
								</td>

								<td style="vertical-align: middle; text-align:center;">
									<?php 
									$qty_stock = isset($value->quantity) ? intval($value->quantity) : 0;
									if($qty_stock == 0) {
										echo '<span class="label label-danger" style="display:inline-block; margin-bottom:4px;">Hết hàng</span>';
									} else if ($qty_stock <= 10) {
										echo '<span class="label label-warning" style="display:inline-block; margin-bottom:4px;">Cảnh báo</span><br>';
										echo '<strong>'.$qty_stock.'</strong> cái';
									} else {
										echo '<span class="label label-success" style="display:inline-block; margin-bottom:4px;">An toàn</span><br>';
										echo '<strong>'.$qty_stock.'</strong> cái';
									}
									?>
								</td>

								<td style="vertical-align: middle; text-align:center;">
									<?php echo $value->view; ?>
								</td>

								<td style="vertical-align: middle; text-align:center;">
									<?php
									$avg = 0;
									if(isset($value->rate_count) && $value->rate_count > 0) {
										$avg = round($value->rate_total / $value->rate_count, 1);
									}
									echo $avg;
									?> ⭐
									<br>
									<small style="color:#999;">(<?php echo isset($value->rate_count) ? $value->rate_count : 0; ?> lượt)</small>
								</td>

								<td class="text-center">
									<div class="admin-table-actions">
										<a href="<?php echo admin_url('product/edit/'.$value->id); ?>" title="Sửa" class="btn btn-sm btn-outline-primary">
											<i class="fa-solid fa-pen"></i>
										</a>
										<?php if ($this->session->userdata('login')->level == ROLE_ADMIN) { ?>
											<a id="<?php echo $value->id; ?>" title="Xóa" class="btn btn-sm btn-outline-danger remove">
												<i class="fa-solid fa-trash"></i>
											</a>
										<?php } else { ?>
											<span class="admin-table-actions-slot" aria-hidden="true"></span>
										<?php } ?>
									</div>
								</td>
							</tr>
							<?php } ?>
						<?php else: ?>
							<tr>
								<td colspan="<?php echo ($this->session->userdata('login')->level == ROLE_ADMIN) ? '12' : '11'; ?>" class="text-center">Không tìm thấy sản phẩm nào phù hợp với bộ lọc!</td>
							</tr>
						<?php endif; ?>
						</tbody>
					</table>
					
					<div class="admin-table-footer">
						<?php if ($this->session->userdata('login')->level == ROLE_ADMIN) { ?>
							<button type="button" class="btn btn-danger btn-sm" id="submit-del">Xóa mục đã chọn</button>
						<?php } ?>
						<div><?php echo $this->pagination->create_links(); ?></div>
					</div>
				</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$("#select-all").click(function () {
			$(".case").prop('checked', $(this).prop('checked'));
		});

		$(".case").click(function(){
			if($(".case").length == $(".case:checked").length) {
				$("#select-all").prop("checked", true);
			} else {
				$("#select-all").prop("checked", false);
			}
		});

		// Xóa đơn lẻ từng sản phẩm
		$('.remove').click(function(e){
			e.preventDefault();
			if(confirm('Bạn có chắc chắn muốn xóa sản phẩm này? Sau khi xóa dữ liệu hình ảnh sẽ mất hoàn toàn.')) {
				var container = $(this).closest('tr');
				var id = $(this).attr('id');
				
				$.ajax({
					url: '<?php echo admin_url('product/del'); ?>',
					type: 'post',
					data: { id: id },
					success: function(response){
						if(response.trim() == 'success') {
							container.slideUp('slow', function(){
								container.remove();
							});
							$('#message').html('<div class="alert alert-success">Xóa sản phẩm thành công!</div>');
						} else {
							$('#message').html('<div class="alert alert-danger">Lỗi: Bạn không có quyền xóa hoặc sản phẩm không tồn tại!</div>');
						}
					}
				});
			}
		});

		// Xóa loạt mục qua checkbox
		$('#submit-del').click(function(e){
			e.preventDefault();
			var selected_checkboxes = $("input[name='checkbox[]']:checked");
			if(selected_checkboxes.length === 0) {
				alert('Vui lòng chọn ít nhất một sản phẩm thời trang để xóa!');
				return false;
			}
			
			if(confirm('Cảnh báo hệ thống: Bạn chắc chắn muốn xóa loạt sản phẩm đã chọn?')) {
				var ids = [];
				selected_checkboxes.each(function(){
					ids.push($(this).val());
				});
				
				$.ajax({
					url: '<?php echo admin_url('product'); ?>', 
					type: 'post',
					data: { checkbox: ids },
					success: function(response){
						location.reload();
					},
					error: function(){
						alert('Hệ thống từ chối thực hiện tác vụ này!');
					}
				});
			}
		});
	});
</script>