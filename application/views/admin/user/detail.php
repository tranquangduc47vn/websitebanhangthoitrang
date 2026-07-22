<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item"><a href="<?php echo admin_url('user/order/'.$transaction->user_id); ?>">Đơn của user</a></li>
			<li class="breadcrumb-item active" aria-current="page">Chi tiết đơn</li>
		</ol>
	</nav>
</div>

<div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mb-3">
	<div>
		<strong>Trạng thái: </strong>
		<?php switch (trim($transaction->status)) {
			case '0': echo "<span class='label label-danger'>Đang chờ xử lý</span>"; break;
			case '1': echo "<span class='label label-info'>Đã xác nhận</span>"; break;
			case '2': echo "<span class='label label-primary'>Đang giao hàng</span>"; break;
			case '3': echo "<span class='label label-success'>Thành công</span>"; break;
			case '4': echo "<span class='label label-default'>Đã hủy / Hoàn</span>"; break;
			default: echo "<span class='label label-warning'>".$transaction->status."</span>"; break;
		} ?>
	</div>
</div>

<div class="admin-card mb-3">
	<div class="admin-card-header">Thông tin khách hàng</div>
	<div class="admin-card-body table-responsive">
		<table class="table admin-table table-bordered mb-0">
			<tbody>
				<tr><td style="width:150px"><strong>Họ và tên</strong></td><td><?php echo $transaction->user_name; ?></td></tr>
				<tr><td><strong>Email</strong></td><td><?php echo $transaction->user_email; ?></td></tr>
				<tr><td><strong>Số điện thoại</strong></td><td><?php echo $transaction->user_phone; ?></td></tr>
				<tr><td><strong>Địa chỉ</strong></td><td><?php echo $transaction->user_address; ?></td></tr>
				<tr><td><strong>Tin nhắn</strong></td><td><?php echo !empty($transaction->message) ? $transaction->message : '<i>Không có ghi chú</i>'; ?></td></tr>
				<tr><td><strong>Ngày đặt</strong></td><td><?php echo is_numeric($transaction->created) ? mdate("%H:%i:%s %d/%m/%Y", $transaction->created) : $transaction->created; ?></td></tr>
				<tr><td><strong>Thanh toán</strong></td><td>
					<?php if (isset($transaction->payment) && ($transaction->payment == 'Chuyển khoản' || $transaction->payment == 'VietQR')) { ?>
						<span class="label label-warning">Chuyển khoản</span>
					<?php } else { ?>
						<span class="label label-default">COD</span>
					<?php } ?>
				</td></tr>
			</tbody>
		</table>
	</div>
</div>

<div class="admin-card mb-3">
	<div class="admin-card-header">Sản phẩm đã mua</div>
	<div class="admin-card-body">
		<div class="table-responsive">
			<table class="table table-hover admin-table table-bordered">
				<thead>
					<tr>
						<th class="text-center">STT</th>
						<th>Sản phẩm</th>
						<th class="text-center">SL</th>
						<th>Thành tiền</th>
					</tr>
				</thead>
				<tbody>
					<?php $stt = 0; foreach ($list_product as $value) { $stt++; ?>
						<tr>
							<td class="text-center fw-semibold"><?php echo $stt; ?></td>
							<td>
								<img src="<?php echo base_url(); ?>upload/product/<?php echo $value->image_link; ?>" alt="" class="admin-product-thumb">
								<strong><?php echo $value->name; ?></strong>
								<?php if (!empty($value->size) || !empty($value->color)) { ?>
									<div class="mt-1">
										<span class="label label-default">Size: <?php echo !empty($value->size) ? $value->size : 'Mặc định'; ?></span>
										<span class="label label-info">Màu: <?php echo !empty($value->color) ? $value->color : 'Mặc định'; ?></span>
									</div>
								<?php } ?>
							</td>
							<td class="text-center fw-semibold"><?php echo $value->qty; ?></td>
							<td><strong class="text-danger"><?php echo number_format($value->price); ?></strong> VNĐ</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<p class="text-muted small mt-3 mb-0">* Chỉ xem đối chiếu — đổi trạng thái tại Quản lý đơn đặt hàng.</p>
		<a href="<?php echo admin_url('user/order/'.$transaction->user_id); ?>" class="btn btn-default mt-2"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại</a>
	</div>
</div>
