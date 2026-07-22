<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item active" aria-current="page">Banner danh mục</li>
		</ol>
	</nav>
</div>

<p class="admin-page-subtitle mb-3">Tổng số: <?php echo !empty($list) ? count($list) : 0; ?> banner</p>

<?php if (!empty($message)) { ?>
	<div class="alert alert-success"><strong>Thông báo:</strong> <?php echo $message; ?></div>
<?php } ?>

<div class="admin-card">
	<div class="admin-card-header">
		<span>Danh sách banner</span>
		<div class="admin-search">
			<i class="fa-solid fa-search"></i>
			<input type="search" class="form-control" placeholder="Tìm banner..." data-admin-table-search="bannerListTable">
		</div>
	</div>
	<div class="admin-card-body">
		<div class="table-responsive">
			<table class="table table-hover admin-table" id="bannerListTable">
				<thead>
					<tr>
						<th class="text-center">STT</th>
						<th>Hình ảnh</th>
						<th>Tên hiển thị</th>
						<th>Link</th>
						<th class="text-center">Hành động</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($list)) { ?>
						<?php foreach ($list as $row) { ?>
							<tr>
								<td class="text-center fw-semibold"><?php echo $row->sort_order; ?></td>
								<td><img src="<?php echo base_url('upload/slider/' . $row->image_link); ?>" width="100" height="60" style="object-fit:cover;border-radius:8px;" onerror="this.src='<?php echo base_url('upload/no-image.jpg'); ?>'"></td>
								<td><strong><?php echo htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'); ?></strong></td>
								<td><code class="small"><?php echo htmlspecialchars($row->link, ENT_QUOTES, 'UTF-8'); ?></code></td>
								<td class="text-center">
									<a class="btn btn-sm btn-outline-primary" href="<?php echo admin_url('banner/edit/' . $row->id); ?>"><i class="fa-solid fa-pen me-1"></i> Sửa</a>
								</td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr><td colspan="5" class="admin-empty">Chưa có banner nào.</td></tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
