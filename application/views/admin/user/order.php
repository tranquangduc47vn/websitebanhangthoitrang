<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item"><a href="<?php echo admin_url('user'); ?>">Khách hàng</a></li>
			<li class="breadcrumb-item active" aria-current="page">Đơn hàng — <?php echo isset($user->name) ? htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8') : ''; ?></li>
		</ol>
	</nav>
</div>

<div class="admin-card mb-3">
	<div class="admin-card-body">
		<div class="table-responsive">
			<table class="table table-hover admin-table table-bordered">
				<thead>
					<tr>
						<th class="text-center" style="width:60px">STT</th>
						<th>Tên khách hàng</th>
						<th>Ngày đặt</th>
						<th>Số ĐT</th>
						<th>Giá tiền</th>
						<th>Trạng thái</th>
						<th class="text-center">Hành động</th>
					</tr>
				</thead>
				<tbody>
					<?php $stt = 0; foreach ($order as $value) { $stt++; ?>
						<tr>
							<td class="text-center fw-semibold"><?php echo $stt; ?></td>
							<td><?php echo htmlspecialchars($value->user_name, ENT_QUOTES, 'UTF-8'); ?></td>
							<td><?php echo is_numeric($value->created) ? mdate("%H:%i:%s %d/%m/%Y", $value->created) : $value->created; ?></td>
							<td><?php echo htmlspecialchars($value->user_phone, ENT_QUOTES, 'UTF-8'); ?></td>
							<td><strong><?php echo number_format($value->amount); ?></strong> VNĐ</td>
							<td>
								<?php switch (trim($value->status)) {
									case '0': echo "<span class='label label-danger'>Đang chờ xử lý</span>"; break;
									case '1': echo "<span class='label label-info'>Đã xác nhận</span>"; break;
									case '2': echo "<span class='label label-primary'>Đang giao hàng</span>"; break;
									case '3': echo "<span class='label label-success'>Thành công</span>"; break;
									case '4': echo "<span class='label label-default'>Đã hủy / Hoàn</span>"; break;
									default: echo "<span class='label label-warning'>".$value->status."</span>"; break;
								} ?>
							</td>
							<td class="text-center">
								<a href="<?php echo admin_url('user/detail/'.$value->id); ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-eye me-1"></i> Chi tiết</a>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<a href="<?php echo admin_url('user'); ?>" class="btn btn-default"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách khách hàng</a>
