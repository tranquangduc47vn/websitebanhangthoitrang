<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item active" aria-current="page">Trang tĩnh</li>
		</ol>
	</nav>
</div>

<div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
	<a href="<?php echo admin_url('pages/add'); ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Thêm mới</a>
</div>

<div class="admin-card">
	<div class="admin-card-header">
		<span>Danh sách trang</span>
		<div class="admin-search">
			<i class="fa-solid fa-search"></i>
			<input type="search" class="form-control" placeholder="Tìm trang..." data-admin-table-search="pagesListTable">
		</div>
	</div>
	<div class="admin-card-body">
		<div class="table-responsive">
			<table class="table table-hover admin-table" id="pagesListTable">
				<thead>
					<tr>
						<th>ID</th>
						<th>Tiêu đề trang</th>
						<th>Slug</th>
						<th>Hành động</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($list)) { ?>
						<?php foreach ($list as $row) { ?>
							<tr>
								<td><?php echo $row['id']; ?></td>
								<td><strong><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
								<td><code><?php echo htmlspecialchars($row['slug'], ENT_QUOTES, 'UTF-8'); ?></code></td>
								<td>
									<a href="<?php echo admin_url('pages/edit/' . $row['id']); ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen me-1"></i> Sửa nội dung</a>
								</td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr><td colspan="4" class="admin-empty">Chưa có trang tĩnh nào.</td></tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
