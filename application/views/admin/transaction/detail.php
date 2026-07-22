<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item"><a href="<?php echo admin_url('transaction'); ?>">Đơn hàng</a></li>
			<li class="breadcrumb-item active" aria-current="page">Chi tiết #<?php echo $transaction->id; ?></li>
		</ol>
	</nav>
</div>

<div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mb-3">
	<div>
		<strong>Trạng thái: </strong>
		<?php switch ($transaction->status) {
			case '0': echo "<span class='label label-danger'>Đang chờ xử lý</span>"; break;
			case '1': echo "<span class='label label-info'>Đã xác nhận</span>"; break;
			case '2': echo "<span class='label label-primary'>Đang giao hàng</span>"; break;
			case '3': echo "<span class='label label-success'>Thành công</span>"; break;
			case '4': echo "<span class='label label-default'>Đã hủy / Hoàn</span>"; break;
			default: echo "<span class='label label-danger'>Đang chờ</span>"; break;
		} ?>
	</div>
</div>

<div class="admin-card mb-3">
	<div class="admin-card-header">Thông tin khách hàng</div>
	<div class="admin-card-body table-responsive">
		<table class="table admin-table table-bordered mb-0">
			<tbody>
				<tr>
					<td style="width: 150px"><strong>Họ và tên</strong></td>
					<td><?php echo $transaction->user_name; ?></td>
				</tr>
				<tr>
					<td><strong>Email</strong></td>
					<td><?php echo $transaction->user_email; ?></td>
				</tr>
				<tr>
					<td><strong>Số điện thoại</strong></td>
					<td><?php echo $transaction->user_phone; ?></td>
				</tr>
				<tr>
					<td><strong>Địa chỉ</strong></td>
					<td><?php echo $transaction->user_address; ?></td>
				</tr>
				<tr>
					<td><strong>Tin nhắn</strong></td>
					<td><?php echo $transaction->message; ?></td>
				</tr>
				<tr>
					<td><strong>Ngày đặt</strong></td>
					<td><?php echo mdate("%H:%i:%s %d/%m/%Y", $transaction->created); ?></td>
				</tr>
				<tr>
					<td><strong>Hình thức thanh toán</strong></td>
					<td>
						<?php if (isset($transaction->payment) && $transaction->payment == 'Chuyển khoản') { ?>
							<strong class="text-warning"><i class="fa-solid fa-credit-card me-1"></i> Chuyển khoản ngân hàng (VietQR)</strong>
						<?php } else { ?>
							<strong class="text-secondary"><i class="fa-solid fa-money-bill me-1"></i> Thanh toán tiền mặt khi nhận hàng (COD)</strong>
						<?php } ?>
					</td>
				</tr>
				<?php
				$disc = isset($transaction->discount_amount) ? (int) $transaction->discount_amount : 0;
				$vcode = isset($transaction->voucher_code) ? trim((string) $transaction->voucher_code) : '';
				if ($vcode !== '' || $disc > 0) { ?>
				<tr>
					<td><strong>Voucher</strong></td>
					<td>
						<?php if ($vcode !== '') { ?><code><?php echo htmlspecialchars($vcode, ENT_QUOTES, 'UTF-8'); ?></code><?php } ?>
						<?php if ($disc > 0) { ?> — Giảm <strong class="text-success"><?php echo number_format($disc, 0, ',', '.'); ?> ₫</strong><?php } ?>
					</td>
				</tr>
				<tr>
					<td><strong>Thanh toán thực tế</strong></td>
					<td><strong class="text-danger"><?php echo number_format((int) $transaction->amount, 0, ',', '.'); ?> ₫</strong></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>

<div class="admin-card mb-3">
	<div class="admin-card-header">Chi tiết sản phẩm đã mua</div>
	<div class="admin-card-body">
		<div class="table-responsive">
			<table class="table table-hover admin-table table-bordered">
				<thead>
					<tr>
						<th class="text-center" style="width: 60px;">STT</th>
						<th>Hình ảnh & Tên sản phẩm</th>
						<th class="text-center" style="width: 100px;">Số lượng</th>
						<th style="width: 150px;">Thành tiền</th>
						<?php if ($transaction->status == '0') { ?>
							<th class="text-center" style="width: 100px;">Hành động</th>
						<?php } ?>
					</tr>
				</thead>
				<tbody>
							<?php 
							$stt = 0;
							foreach ($list_product as $value) { 
								$stt = $stt + 1;
							?>
								<tr>
									<td style="vertical-align: middle; text-align: center;"><strong><?php echo $stt; ?></strong></td>
									
									<td style="vertical-align: middle;">
										<img src="<?php echo base_url(); ?>upload/product/<?php echo $value->image_link; ?>" alt="" class="admin-product-thumb">
										<strong style="font-size: 14px;"><?php echo $value->name; ?></strong>
										
										<?php if(!empty($value->size) || !empty($value->color)): ?>
											<div style="margin-top: 5px;">
												<span class="label label-default" style="background-color: #777; color: #fff; font-size: 11px; padding: 2px 6px; font-weight: bold; margin-right: 5px; border-radius: 3px;">
													Size: <?php echo !empty($value->size) ? $value->size : 'Mặc định'; ?>
												</span>
												<span class="label label-info" style="background-color: #5bc0de; color: #fff; font-size: 11px; padding: 2px 6px; font-weight: bold; border-radius: 3px;">
													Màu: <?php echo !empty($value->color) ? $value->color : 'Mặc định'; ?>
												</span>
											</div>
										<?php endif; ?>
									</td>
									
									<td style="vertical-align: middle; text-align: center;"><strong><?php echo $value->qty; ?></strong></td>
									<td style="vertical-align: middle;"><strong style="color: red;"><?php echo number_format($value->price); ?></strong> VNĐ</td>
									
									<?php if ($transaction->status == '0') { ?>
										<td class="text-center" style="vertical-align: middle;">
											<a href="<?php echo admin_url('transaction/deldetail/'.$value->order_id) ?>" title="Xóa sản phẩm này khỏi đơn" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn chắc chắn muốn xóa sản phẩm này?')">
												<i class="fa-solid fa-trash"></i>
											</a>
										</td> 
									<?php } ?>
								</tr>
							<?php } ?>
						</tbody>
			</table>
		</div>
		<hr>
		<div class="p-3 rounded border bg-light">
			<h4 class="h6 fw-bold mb-3">Thao tác xử lý đơn hàng</h4>
			<?php if ($transaction->status == '0') { ?>
				<a href="<?php echo admin_url('transaction/accept/'.$transaction->id); ?>" class="btn btn-success"><i class="fa-solid fa-check me-1"></i> Xác nhận đơn hàng này</a>
				<a href="#" class="btn btn-danger" onclick="var reason = prompt('Nhập lý do hủy đơn hàng này:'); if(reason != null && reason != '') { window.location.href = '<?php echo admin_url('transaction/cancel/'.$transaction->id); ?>?reason=' + encodeURIComponent(reason); } return false;"><i class="fa-solid fa-ban me-1"></i> Hủy bỏ đơn</a>
			<?php } elseif ($transaction->status == '1') { ?>
				<a href="<?php echo admin_url('transaction/ship/'.$transaction->id); ?>" class="btn btn-primary"><i class="fa-solid fa-truck me-1"></i> Bàn giao cho Shipper giao hàng</a>
				<a href="#" class="btn btn-danger" onclick="var reason = prompt('Nhập lý do hủy đơn hàng này:'); if(reason != null && reason != '') { window.location.href = '<?php echo admin_url('transaction/cancel/'.$transaction->id); ?>?reason=' + encodeURIComponent(reason); } return false;"><i class="fa-solid fa-ban me-1"></i> Hủy bỏ đơn</a>
			<?php } elseif ($transaction->status == '2') { ?>
				<a href="<?php echo admin_url('transaction/complete/'.$transaction->id); ?>" class="btn btn-success"><i class="fa-solid fa-circle-check me-1"></i> Xác nhận: Khách đã nhận hàng thành công</a>
				<a href="#" class="btn btn-warning" onclick="var reason = prompt('Nhập lý do khách trả hàng / bùng hàng:'); if(reason != null && reason != '') { window.location.href = '<?php echo admin_url('transaction/cancel/'.$transaction->id); ?>?reason=' + encodeURIComponent(reason); } return false;"><i class="fa-solid fa-rotate-left me-1"></i> Khách bùng hàng (Hoàn đơn)</a>
			<?php } elseif ($transaction->status == '3') { ?>
				<div class="alert alert-success mb-0"><i class="fa-solid fa-circle-check me-1"></i> <strong>Đơn hàng hoàn tất:</strong> Đơn hàng đã được giao thành công và hệ thống đã ghi nhận doanh thu cùng số lượng sản phẩm bán ra.</div>
			<?php } elseif ($transaction->status == '4') { ?>
				<div class="alert alert-warning mb-0">
					<i class="fa-solid fa-circle-info me-1"></i> Đơn hàng này đã bị <strong>Hủy bỏ hoặc Hoàn trả</strong>.<br>
					<strong class="d-inline-block mt-1">Lý do hệ thống ghi nhận:</strong>
					<span class="text-danger fw-bold"><?php echo !empty($transaction->reason) ? $transaction->reason : 'Không có lý do cụ thể.'; ?></span>
				</div>
			<?php } ?>
			<a href="<?php echo admin_url('transaction'); ?>" class="btn btn-default ms-1"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách</a>
		</div>
	</div>
</div>