<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item"><a href="<?php echo admin_url('user'); ?>">Khách hàng</a></li>
			<li class="breadcrumb-item active" aria-current="page">Thông tin — <?php echo htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'); ?></li>
		</ol>
	</nav>
</div>

<div class="admin-card mb-3">
	<div class="admin-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
		<span>Thông tin khách hàng</span>
		<span class="badge bg-light text-dark border">Chỉ xem</span>
	</div>
	<div class="admin-card-body table-responsive">
		<table class="table admin-table table-bordered mb-0">
			<tbody>
				<tr>
					<td style="width: 180px"><strong>ID</strong></td>
					<td><?php echo (int) $user->id; ?></td>
				</tr>
				<tr>
					<td><strong>Họ và tên</strong></td>
					<td><?php echo htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'); ?></td>
				</tr>
				<tr>
					<td><strong>Email</strong></td>
					<td><?php echo htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8'); ?></td>
				</tr>
				<tr>
					<td><strong>Số điện thoại</strong></td>
					<td><?php echo htmlspecialchars($user->phone, ENT_QUOTES, 'UTF-8'); ?></td>
				</tr>
				<tr>
					<td><strong>Địa chỉ chính</strong></td>
					<td><?php echo htmlspecialchars($user->address, ENT_QUOTES, 'UTF-8'); ?></td>
				</tr>
				<tr>
					<td><strong>Ngày đăng ký</strong></td>
					<td><?php echo format_user_registered($user, isset($addresses) ? $addresses : array()); ?></td>
				</tr>
				<tr>
					<td><strong>Hạng thành viên</strong></td>
					<td><?php echo loyalty_tier_label(isset($user->loyalty_tier) ? $user->loyalty_tier : 'member'); ?></td>
				</tr>
				<tr>
					<td><strong>Điểm tích lũy</strong></td>
					<td><?php echo number_format(isset($user->loyalty_points) ? (int) $user->loyalty_points : 0); ?> điểm</td>
				</tr>
				<tr>
					<td><strong>Tổng chi tiêu</strong></td>
					<td><?php echo number_format(isset($user->loyalty_lifetime_spend) ? (int) $user->loyalty_lifetime_spend : 0); ?> ₫</td>
				</tr>
				<tr>
					<td><strong>Đơn hoàn thành</strong></td>
					<td><?php echo (int) (isset($user->loyalty_completed_orders) ? $user->loyalty_completed_orders : 0); ?> đơn</td>
				</tr>
				<tr>
					<td><strong>Tổng đơn hàng</strong></td>
					<td><?php echo (int) $order_count; ?> đơn</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<?php if (!empty($addresses)) { ?>
<div class="admin-card mb-3">
	<div class="admin-card-header">Sổ địa chỉ đã lưu</div>
	<div class="admin-card-body table-responsive">
		<table class="table admin-table table-bordered table-hover mb-0">
			<thead>
				<tr>
					<th style="width: 60px">#</th>
					<th>Ghi chú</th>
					<th>Địa chỉ</th>
					<th class="text-center" style="width: 100px">Mặc định</th>
				</tr>
			</thead>
			<tbody>
				<?php $stt = 0; foreach ($addresses as $addr) { $stt++; ?>
					<tr>
						<td class="text-center fw-semibold"><?php echo $stt; ?></td>
						<td><?php echo htmlspecialchars($addr->address_note, ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars($addr->address_line, ENT_QUOTES, 'UTF-8'); ?></td>
						<td class="text-center">
							<?php if ((int) $addr->is_default === 1) { ?>
								<span class="label label-success">Mặc định</span>
							<?php } else { ?>
								<span class="text-muted">—</span>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<?php } ?>

<div class="d-flex flex-wrap gap-2">
	<a href="<?php echo admin_url('user'); ?>" class="btn btn-default"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách</a>
	<a href="<?php echo admin_url('user/order/' . $user->id); ?>" class="btn btn-outline-primary"><i class="fa-solid fa-list me-1"></i> Xem đơn hàng</a>
</div>
