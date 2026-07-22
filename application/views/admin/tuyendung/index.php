<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item active" aria-current="page">Tuyển dụng</li>
		</ol>
	</nav>
</div>

<div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
	<a href="<?php echo admin_url('tuyendung/add'); ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Thêm mới</a>
</div>

<?php if ($this->session->flashdata('message')) { ?>
	<div class="alert alert-success"><?php echo $this->session->flashdata('message'); ?></div>
<?php } ?>

<div class="admin-card">
	<div class="admin-card-header">
		<span>Vị trí tuyển dụng</span>
		<div class="admin-search">
			<i class="fa-solid fa-search"></i>
			<input type="search" class="form-control" placeholder="Tìm..." data-admin-table-search="tuyendungListTable">
		</div>
	</div>
	<div class="admin-card-body">
		<div class="table-responsive">
			<table class="table table-hover admin-table" id="tuyendungListTable">
				<thead>
					<tr>
						<th>ID</th>
						<th>Ảnh</th>
						<th>Tiêu đề</th>
						<th class="text-center">Ngày đăng</th>
						<th class="text-center">Hành động</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($list)) { ?>
						<?php foreach ($list as $row) { ?>
							<tr>
								<td class="fw-semibold"><?php echo $row->id; ?></td>
								<td><img src="<?php echo base_url('upload/' . $row->image); ?>" width="80" height="50" style="object-fit:cover;border-radius:8px;" onerror="this.src='<?php echo base_url('upload/no-image.jpg'); ?>'"></td>
								<td><strong><?php echo htmlspecialchars($row->title, ENT_QUOTES, 'UTF-8'); ?></strong></td>
								<td class="text-center"><?php echo date('d/m/Y H:i', strtotime($row->created_at)); ?></td>
								<td class="text-center">
									<a class="btn btn-sm btn-outline-primary me-1" href="<?php echo admin_url('tuyendung/edit/' . $row->id); ?>"><i class="fa-solid fa-pen"></i></a>
									<a class="btn btn-sm btn-outline-danger" href="<?php echo admin_url('tuyendung/delete/' . $row->id); ?>" onclick="return confirm('Bạn chắc chắn muốn xóa bài viết này?')"><i class="fa-solid fa-trash"></i></a>
								</td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr><td colspan="5" class="admin-empty">Chưa có bài tuyển dụng.</td></tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
