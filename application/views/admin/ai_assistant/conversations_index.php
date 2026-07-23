<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item active" aria-current="page">Hội thoại trợ lý AI</li>
		</ol>
	</nav>
</div>

<p class="admin-page-subtitle mb-3">Lịch sử hội thoại giữa khách hàng và Trợ lý AI.</p>

<div class="admin-card mb-3">
	<div class="admin-card-body">
		<form class="d-flex gap-2" action="" method="get">
			<input type="text" name="q" class="form-control" placeholder="Tìm theo mã hội thoại hoặc token khách..."
				value="<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?>">
			<noscript><button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i></button></noscript>
		</form>
	</div>
</div>

<div class="admin-card">
	<div class="admin-card-body table-responsive">
		<table class="table table-hover admin-table">
			<thead>
				<tr>
					<th>Mã</th>
					<th>Khách hàng</th>
					<th>Trạng thái</th>
					<th>Bắt đầu</th>
					<th>Cập nhật cuối</th>
					<th class="text-center">Thao tác</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($list)) { ?>
					<?php foreach ($list as $row) { ?>
						<tr>
							<td>#<?php echo (int) $row->id; ?></td>
							<td>
								<?php if ((int) $row->user_id > 0) { ?>
									Khách #<?php echo (int) $row->user_id; ?>
								<?php } else { ?>
									Khách vãng lai <code><?php echo htmlspecialchars(substr((string) $row->guest_token, 0, 8), ENT_QUOTES, 'UTF-8'); ?></code>
								<?php } ?>
							</td>
							<td>
								<?php
									$statusLabel = array('open' => 'Đang mở', 'handed_off' => 'Đã chuyển NV', 'closed' => 'Đã đóng');
									echo isset($statusLabel[$row->status]) ? $statusLabel[$row->status] : htmlspecialchars($row->status, ENT_QUOTES, 'UTF-8');
								?>
							</td>
							<td><?php echo date('d/m/Y H:i', (int) $row->started); ?></td>
							<td><?php echo date('d/m/Y H:i', (int) $row->last_message); ?></td>
							<td class="text-center">
								<a class="btn btn-sm btn-outline-primary" href="<?php echo admin_url('ai-assistant/conversations/detail/' . (int) $row->id); ?>" title="Xem chi tiết"><i class="fa-solid fa-eye"></i></a>
							</td>
						</tr>
					<?php } ?>
				<?php } else { ?>
					<tr><td colspan="6" class="admin-empty">Chưa có hội thoại nào.</td></tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
