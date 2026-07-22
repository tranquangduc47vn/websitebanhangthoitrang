<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item active" aria-current="page">Quản trị viên</li>
		</ol>
	</nav>
</div>

<div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
	<a href="<?php echo admin_url('admin/add'); ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Thêm mới</a>
</div>

<div class="admin-card">
	<div class="admin-card-header">
		<span>Danh sách</span>
		<div class="admin-search">
			<i class="fa-solid fa-search"></i>
			<input type="search" class="form-control" placeholder="Tìm kiếm..." data-admin-table-search="adminListTable">
		</div>
	</div>
	<div class="admin-card-body">
		<?php if (!empty($message_success)) { ?>
			<div class="alert alert-success"><strong>Thành công!</strong> <?php echo $message_success; ?></div>
		<?php } ?>
		<?php if (!empty($message_fail)) { ?>
			<div class="alert alert-danger"><strong>Lỗi!</strong> <?php echo $message_fail; ?></div>
		<?php } ?>
		<div class="table-responsive">
			<table class="table table-hover admin-table" id="adminListTable">
				<thead>
					<tr>
						<th>ID</th>
						<th>Họ tên</th>
						<th>Email</th>
						<th>Level</th>
						<th class="text-center">Hành động</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($admin as $value) { ?>
						<tr>
							<td><span class="fw-semibold"><?php echo $value->id; ?></span></td>
							<td><?php echo htmlspecialchars($value->name, ENT_QUOTES, 'UTF-8'); ?></td>
							<td><?php echo htmlspecialchars($value->email, ENT_QUOTES, 'UTF-8'); ?></td>
							<td>
								<span class="label label-<?php
									echo (int) $value->level === ROLE_ADMIN ? 'danger' : ((int) $value->level === ROLE_MOD ? 'warning' : 'info');
								?>"><?php echo htmlspecialchars(admin_role_label($value->level), ENT_QUOTES, 'UTF-8'); ?></span>
							</td>
							<td class="text-center">
								<div class="admin-table-actions">
									<a class="btn btn-sm btn-outline-primary" href="<?php echo admin_url('admin/edit/' . $value->id); ?>" title="Sửa"><i class="fa-solid fa-pen"></i></a>
									<?php if ($value->level != ROLE_ADMIN && (int) $this->session->userdata('login')->level === ROLE_ADMIN) { ?>
										<a class="btn btn-sm btn-outline-danger" href="<?php echo admin_url('admin/del/' . $value->id); ?>" title="Xóa" onclick="return confirm('Bạn chắc chắn muốn xóa tài khoản này?')"><i class="fa-solid fa-trash"></i></a>
									<?php } else { ?>
										<span class="admin-table-actions-slot" aria-hidden="true"></span>
									<?php } ?>
								</div>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
