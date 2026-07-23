<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>">Trang chủ</a></li>
			<li class="breadcrumb-item active">Nhà cung cấp</li>
		</ol>
	</nav>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
	<h1 class="h4 mb-0">Nhà cung cấp</h1>
	<?php if (!empty($can_manage)) { ?>
		<a href="<?php echo admin_url('suppliers/add'); ?>" class="btn btn-primary btn-sm">Thêm NCC</a>
	<?php } ?>
</div>

<div class="admin-card">
	<div class="table-responsive">
		<table class="table table-sm mb-0">
			<thead><tr><th>Mã</th><th>Tên</th><th>Liên hệ</th><th>Điện thoại</th><th>Email</th><th>Trạng thái</th></tr></thead>
			<tbody>
			<?php foreach ($list as $s) { ?>
				<tr>
					<td><?php echo html_escape($s->code); ?></td>
					<td><?php echo html_escape($s->name); ?></td>
					<td><?php echo html_escape($s->contact_name); ?></td>
					<td><?php echo html_escape($s->phone); ?></td>
					<td><?php echo html_escape($s->email); ?></td>
					<td><?php echo (int) $s->status === 1 ? 'Hoạt động' : 'Ngưng'; ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</div>
