<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>">Trang chủ</a></li>
			<li class="breadcrumb-item active">Phiếu nhập kho</li>
		</ol>
	</nav>
</div>

<?php if (!empty($message)) { ?>
	<div class="alert alert-success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
<?php } ?>
<?php if (!empty($message_fail)) { ?>
	<div class="alert alert-danger"><?php echo htmlspecialchars($message_fail, ENT_QUOTES, 'UTF-8'); ?></div>
<?php } ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
	<h1 class="h4 mb-0">Phiếu nhập kho</h1>
	<div class="d-flex gap-2">
		<a href="<?php echo admin_url('stock-inventory'); ?>" class="btn btn-outline-secondary btn-sm">Tồn kho</a>
		<a href="<?php echo admin_url('stock-inventory/movements'); ?>" class="btn btn-outline-secondary btn-sm">Lịch sử biến động</a>
		<?php if (!empty($can_manage)) { ?>
			<a href="<?php echo admin_url('stock-receipts/add'); ?>" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Tạo phiếu</a>
		<?php } ?>
	</div>
</div>

<div class="admin-card mb-3">
	<div class="admin-card-body">
		<form class="row g-2 align-items-end" method="get" action="<?php echo admin_url('stock-receipts'); ?>">
			<div class="col-md-4">
				<label class="form-label small mb-1">Mã phiếu</label>
				<input type="search" name="code" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filter_code ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="PN20260723…">
			</div>
			<div class="col-md-3">
				<label class="form-label small mb-1">Trạng thái</label>
				<select name="status" class="form-select form-select-sm">
					<option value="">— Tất cả —</option>
					<option value="draft" <?php echo ($filter_status ?? '') === 'draft' ? 'selected' : ''; ?>>Nháp</option>
					<option value="confirmed" <?php echo ($filter_status ?? '') === 'confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
					<option value="cancelled" <?php echo ($filter_status ?? '') === 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
				</select>
			</div>
			<div class="col-md-2">
				<label class="form-label small mb-1 d-block">&nbsp;</label>
				<a href="<?php echo admin_url('stock-receipts'); ?>" class="btn btn-outline-secondary btn-sm w-100">Xóa lọc</a>
				<noscript><button type="submit" class="btn btn-primary btn-sm w-100 mt-1">Lọc</button></noscript>
			</div>
		</form>
	</div>
</div>

<div class="admin-card">
	<div class="table-responsive">
		<table class="table table-hover align-middle mb-0">
			<thead>
				<tr>
					<th>Mã phiếu</th>
					<th>NCC</th>
					<th>SL</th>
					<th class="text-end">Tổng tiền nhập</th>
					<th>Trạng thái</th>
					<th>Ngày tạo</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php if (empty($list)) { ?>
				<tr><td colspan="7" class="text-center text-muted py-4">Chưa có phiếu nhập.</td></tr>
			<?php } else {
				$this->load->library('stock_service');
				foreach ($list as $row) {
					$badge = 'secondary';
					if ($row->status === 'draft') { $badge = 'warning'; }
					if ($row->status === 'confirmed') { $badge = 'success'; }
					if ($row->status === 'cancelled') { $badge = 'danger'; }
			?>
				<tr>
					<td class="fw-semibold"><?php echo htmlspecialchars($row->receipt_code, ENT_QUOTES, 'UTF-8'); ?></td>
					<td><?php echo htmlspecialchars($row->supplier_name ?: '—', ENT_QUOTES, 'UTF-8'); ?></td>
					<td><?php echo number_format((int) $row->total_qty); ?></td>
					<td class="text-end"><?php
						$amount = isset($row->total_amount) ? (float) $row->total_amount : 0;
						if ($amount <= 0 && isset($row->lines_total)) {
							$amount = (float) $row->lines_total;
						}
						echo $amount > 0 ? number_format($amount, 0, ',', '.') . ' ₫' : '—';
					?></td>
					<td><span class="badge text-bg-<?php echo $badge; ?>"><?php echo htmlspecialchars($this->stock_service->status_label($row->status), ENT_QUOTES, 'UTF-8'); ?></span></td>
					<td><?php echo !empty($row->created) ? date('d/m/Y H:i', (int) $row->created) : '—'; ?></td>
					<td class="text-end"><a href="<?php echo admin_url('stock-receipts/view/' . (int) $row->id); ?>" class="btn btn-sm btn-outline-primary">Chi tiết</a></td>
				</tr>
			<?php }
			} ?>
			</tbody>
		</table>
	</div>
</div>
