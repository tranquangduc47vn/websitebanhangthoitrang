<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item active" aria-current="page">Khách hàng</li>
		</ol>
	</nav>
</div>

<?php $this->load->helper('export'); admin_export_toolbar('customers'); ?>

<div class="admin-card">
	<div class="admin-card-header">
		<span>Danh sách</span>
		<div class="admin-search">
			<i class="fa-solid fa-search"></i>
			<input type="search" class="form-control" placeholder="Tìm khách hàng..." data-admin-table-search="userListTable">
		</div>
	</div>
	<div class="admin-card-body">
		<div class="table-responsive">
			<table class="table table-hover admin-table" id="userListTable">
				<thead>
					<tr>
						<th>ID</th>
						<th>Họ tên</th>
						<th>Email</th>
						<th>Địa chỉ</th>
						<th class="text-center">Hành động</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($user as $value) { ?>
						<tr>
							<td><span class="fw-semibold"><?php echo $value->id; ?></span></td>
							<td><?php echo htmlspecialchars($value->name, ENT_QUOTES, 'UTF-8'); ?></td>
							<td><?php echo htmlspecialchars($value->email, ENT_QUOTES, 'UTF-8'); ?></td>
							<td><?php echo htmlspecialchars($value->address, ENT_QUOTES, 'UTF-8'); ?></td>
							<td class="text-center">
								<a class="btn btn-sm btn-outline-primary me-1" href="<?php echo admin_url('user/view/' . $value->id); ?>" title="Xem thông tin"><i class="fa-solid fa-eye"></i></a>
								<a class="btn btn-sm btn-outline-secondary me-1" href="<?php echo admin_url('user/order/' . $value->id); ?>" title="Đơn hàng"><i class="fa-solid fa-list"></i></a>
								<a class="btn btn-sm btn-outline-danger" href="<?php echo admin_url('user/del/' . $value->id); ?>" title="Xóa" onclick="return confirm('Bạn chắc chắn muốn xóa')"><i class="fa-solid fa-trash"></i></a>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
