<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item active" aria-current="page">Vận chuyển</li>
		</ol>
	</nav>
</div>

<div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
	<a href="<?php echo admin_url('vanchuyen/add'); ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Thêm chính sách</a>
</div>

<?php if (isset($message) && $message) { ?>
	<div class="alert alert-success"><?php echo $message; ?></div>
<?php } ?>

<div class="admin-card">
	<div class="admin-card-header">
		<span>Danh sách</span>
		<div class="admin-search">
			<i class="fa-solid fa-search"></i>
			<input type="search" class="form-control" placeholder="Tìm..." data-admin-table-search="vanchuyenListTable">
		</div>
	</div>
	<div class="admin-card-body">
		<div class="table-responsive">
			<table class="table table-hover admin-table" id="vanchuyenListTable">
				<thead>
					<tr>
						<th class="text-center">ID</th>
						<th>Ảnh</th>
						<th>Tiêu đề</th>
						<th class="text-center">Ngày cập nhật</th>
						<th class="text-center">Hành động</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($list)) { ?>
						<?php foreach ($list as $row) { ?>
							<tr>
								<td class="text-center fw-semibold"><?php echo $row->id; ?></td>
								<td><img src="<?php echo base_url('upload/' . $row->image); ?>" style="width:80px;border-radius:8px;" onerror="this.src='<?php echo base_url('upload/no-image.jpg'); ?>'"></td>
								<td><?php echo htmlspecialchars($row->title, ENT_QUOTES, 'UTF-8'); ?></td>
								<td class="text-center text-muted small"><?php echo date('d/m/Y H:i', strtotime($row->created_at)); ?></td>
								<td class="text-center">
									<a class="btn btn-sm btn-outline-primary me-1" href="<?php echo admin_url('vanchuyen/edit/' . $row->id); ?>"><i class="fa-solid fa-pen"></i></a>
									<a class="btn btn-sm btn-outline-danger" href="<?php echo admin_url('vanchuyen/delete/' . $row->id); ?>" onclick="return confirm('Xóa chính sách này?')"><i class="fa-solid fa-trash"></i></a>
								</td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr><td colspan="5" class="admin-empty">Chưa có chính sách vận chuyển.</td></tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
