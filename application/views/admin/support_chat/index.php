<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item active" aria-current="page">Hỗ trợ khách hàng</li>
		</ol>
	</nav>
</div>

<p class="admin-page-subtitle mb-3">Quản lý hội thoại AI và nhân viên trong một luồng chat thống nhất.</p>

<?php if (empty($schema_ready)) { ?>
<div class="alert alert-warning">
	<strong>Cần cập nhật database:</strong> chạy file
	<code>databaseaddphpmyadmin/ai_support_chat_upgrade.sql</code>
	trên DB webshop để bật đầy đủ tính năng nhân viên (staff_id, staff_name, unread).
</div>
<?php } ?>

<?php
$stats = isset($stats) ? $stats : array();
$waiting_count = isset($waiting_count) ? (int) $waiting_count : 0;
?>

<div class="row g-3 mb-4">
	<div class="col-6 col-md-3">
		<div class="admin-card h-100"><div class="admin-card-body py-3">
			<div class="small text-muted">Chờ nhân viên</div>
			<div class="h4 mb-0 text-warning"><?php echo (int) ($stats['waiting'] ?? 0); ?></div>
		</div></div>
	</div>
	<div class="col-6 col-md-3">
		<div class="admin-card h-100"><div class="admin-card-body py-3">
			<div class="small text-muted">Đang hỗ trợ</div>
			<div class="h4 mb-0 text-success"><?php echo (int) ($stats['active_staff'] ?? 0); ?></div>
		</div></div>
	</div>
	<div class="col-6 col-md-3">
		<div class="admin-card h-100"><div class="admin-card-body py-3">
			<div class="small text-muted">AI đang trả lời</div>
			<div class="h4 mb-0 text-primary"><?php echo (int) ($stats['ai_active'] ?? 0); ?></div>
		</div></div>
	</div>
	<div class="col-6 col-md-3">
		<div class="admin-card h-100"><div class="admin-card-body py-3">
			<div class="small text-muted">Đã kết thúc</div>
			<div class="h4 mb-0 text-secondary"><?php echo (int) ($stats['closed'] ?? 0); ?></div>
		</div></div>
	</div>
</div>

<div class="admin-card mb-3">
	<div class="admin-card-body">
		<form class="row g-2 align-items-end" method="get" action="<?php echo admin_url('support-chat'); ?>">
			<div class="col-md-4">
				<label class="form-label small">Trạng thái</label>
				<select name="status" class="form-select form-select-sm">
					<?php
					$statuses = array(
						'all' => 'Tất cả',
						'ai_active' => 'AI đang trả lời',
						'waiting_staff' => 'Chờ nhân viên',
						'staff_joined' => 'Đang hỗ trợ',
						'closed' => 'Đã kết thúc',
					);
					$filter_status = isset($filter_status) ? $filter_status : 'all';
					foreach ($statuses as $val => $label) {
						echo '<option value="' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '"'
							. ($filter_status === $val ? ' selected' : '') . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
					}
					?>
				</select>
			</div>
			<div class="col-md-4">
				<label class="form-label small">Tìm kiếm</label>
				<input type="text" name="q" class="form-control form-control-sm" placeholder="ID, guest token..."
					value="<?php echo htmlspecialchars(isset($keyword) ? $keyword : '', ENT_QUOTES, 'UTF-8'); ?>">
			</div>
			<div class="col-md-2">
				<label class="form-check mt-4">
					<input type="checkbox" name="unread" value="1" class="form-check-input" <?php echo !empty($unread_only) ? 'checked' : ''; ?>>
					<span class="form-check-label small">Chưa đọc</span>
				</label>
			</div>
			<div class="col-md-2">
				<label class="form-label small mb-1 d-block">&nbsp;</label>
				<a href="<?php echo admin_url('support-chat'); ?>" class="btn btn-outline-secondary btn-sm w-100">Xóa lọc</a>
				<noscript><button type="submit" class="btn btn-primary btn-sm w-100 mt-1">Lọc</button></noscript>
			</div>
		</form>
	</div>
</div>

<div class="admin-card">
	<div class="admin-card-body p-0">
		<div class="table-responsive">
			<table class="table table-hover mb-0 align-middle">
				<thead class="table-light">
					<tr>
						<th>#</th>
						<th>Khách</th>
						<th>Trạng thái</th>
						<th>Nhân viên</th>
						<th>Cập nhật</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php if (empty($list)) { ?>
					<tr><td colspan="6" class="text-center text-muted py-4">Chưa có hội thoại.</td></tr>
				<?php } else {
					require_once APPPATH . 'services/support/SupportChatService.php';
					$svc = new SupportChatService();
					foreach ($list as $row) {
						$st = $svc->normalizeStatus($row->status);
						$badge = 'secondary';
						$label = $st;
						if ($st === 'waiting_staff') { $badge = 'warning'; $label = 'Chờ nhân viên'; }
						elseif ($st === 'staff_joined') { $badge = 'success'; $label = 'Đang hỗ trợ'; }
						elseif ($st === 'ai_active') { $badge = 'primary'; $label = 'AI'; }
						elseif ($st === 'closed') { $badge = 'secondary'; $label = 'Đã kết thúc'; }
						$guest = (int) $row->user_id > 0 ? 'User #' . (int) $row->user_id : 'Khách #' . (int) $row->id;
				?>
					<tr class="<?php echo (property_exists($row, 'unread_staff') && !empty($row->unread_staff)) ? 'table-warning' : ''; ?>">
						<td><?php echo (int) $row->id; ?></td>
						<td><?php echo htmlspecialchars($guest, ENT_QUOTES, 'UTF-8'); ?></td>
						<td><span class="badge bg-<?php echo $badge; ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></span></td>
						<td><?php echo htmlspecialchars((string) (isset($row->staff_name) ? $row->staff_name : ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td class="small text-muted"><?php echo date('d/m/Y H:i', (int) $row->last_message); ?></td>
						<td class="text-end">
							<a class="btn btn-outline-primary btn-sm" href="<?php echo admin_url('support-chat/chat/' . (int) $row->id); ?>">Mở chat</a>
						</td>
					</tr>
				<?php } } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
