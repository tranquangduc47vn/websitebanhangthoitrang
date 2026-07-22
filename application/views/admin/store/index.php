<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item active" aria-current="page">Hệ thống cửa hàng</li>
		</ol>
	</nav>
</div>

<div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
	<a href="<?php echo admin_url('store/add'); ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Thêm cửa hàng</a>
</div>

<div class="admin-card">
	<div class="admin-card-header">
		<span>Danh sách showroom</span>
		<div class="admin-search">
			<i class="fa-solid fa-search"></i>
			<input type="search" class="form-control" placeholder="Tìm cửa hàng..." data-admin-table-search="storeListTable">
		</div>
	</div>
	<div class="admin-card-body">
		<?php if (!empty($message_success)) { ?><div class="alert alert-success"><?php echo $message_success; ?></div><?php } ?>
		<?php if (!empty($message_fail)) { ?><div class="alert alert-danger"><?php echo $message_fail; ?></div><?php } ?>
		<div class="table-responsive">
			<table class="table table-hover admin-table" id="storeListTable">
				<thead>
					<tr>
						<th>Tên showroom</th>
						<th>Khu vực</th>
						<th>Địa chỉ</th>
						<th>Hotline</th>
						<th>Bản đồ</th>
						<th class="text-center">Hành động</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($list_stores)) { ?>
						<?php foreach ($list_stores as $value) { ?>
							<tr>
								<td><strong><?php echo htmlspecialchars($value->name, ENT_QUOTES, 'UTF-8'); ?></strong></td>
								<td><span class="badge bg-primary"><?php echo htmlspecialchars($value->city, ENT_QUOTES, 'UTF-8'); ?></span></td>
								<td><?php echo htmlspecialchars($value->address, ENT_QUOTES, 'UTF-8'); ?></td>
								<td><strong class="text-danger"><?php echo htmlspecialchars($value->phone, ENT_QUOTES, 'UTF-8'); ?></strong></td>
								<td>
									<?php if (!empty($value->map_link)) { ?>
										<span class="label label-success">Đã nhúng link</span>
									<?php } else { ?>
										<span class="label label-warning">Trống bản đồ</span>
									<?php } ?>
								</td>
								<td class="text-center">
									<a class="btn btn-sm btn-outline-primary me-1" href="<?php echo admin_url('store/edit/' . $value->id); ?>"><i class="fa-solid fa-pen"></i></a>
									<a class="btn btn-sm btn-outline-danger" href="<?php echo admin_url('store/delete/' . $value->id); ?>" onclick="return confirm('Bạn chắc chắn muốn xóa cửa hàng này?')"><i class="fa-solid fa-trash"></i></a>
								</td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr><td colspan="6" class="admin-empty">Không có dữ liệu cửa hàng.</td></tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
