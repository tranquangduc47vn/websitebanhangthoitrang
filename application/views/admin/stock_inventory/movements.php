<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>">Trang chủ</a></li>
			<li class="breadcrumb-item"><a href="<?php echo admin_url('stock-inventory'); ?>">Tồn kho</a></li>
			<li class="breadcrumb-item active">Lịch sử biến động</li>
		</ol>
	</nav>
</div>

<h1 class="h4 mb-3">Lịch sử biến động kho</h1>

<div class="admin-card mb-3">
	<div class="admin-card-body">
		<form class="row g-2 align-items-end" method="get" action="<?php echo admin_url('stock-inventory/movements'); ?>">
			<div class="col-md-3">
				<label class="form-label small mb-1">Product ID</label>
				<input type="number" name="product_id" class="form-control form-control-sm" min="0" value="<?php echo (int) $filter_product_id; ?>">
			</div>
			<div class="col-md-3">
				<label class="form-label small mb-1">Loại</label>
				<select name="type" class="form-select form-select-sm">
					<option value="">Tất cả</option>
					<option value="in" <?php echo $filter_type === 'in' ? 'selected' : ''; ?>>Nhập (in)</option>
					<option value="out" <?php echo $filter_type === 'out' ? 'selected' : ''; ?>>Xuất (out)</option>
					<option value="adjust" <?php echo $filter_type === 'adjust' ? 'selected' : ''; ?>>Điều chỉnh</option>
				</select>
			</div>
			<div class="col-md-2">
				<label class="form-label small mb-1 d-block">&nbsp;</label>
				<a href="<?php echo admin_url('stock-inventory/movements'); ?>" class="btn btn-outline-secondary btn-sm w-100">Xóa lọc</a>
				<noscript><button type="submit" class="btn btn-primary btn-sm w-100 mt-1">Lọc</button></noscript>
			</div>
		</form>
	</div>
</div>

<div class="admin-card">
	<div class="table-responsive">
		<table class="table table-sm table-hover align-middle mb-0">
			<thead>
				<tr>
					<th>Thời gian</th>
					<th>Sản phẩm</th>
					<th>Size</th>
					<th>Màu</th>
					<th>Loại</th>
					<th>Thay đổi</th>
					<th>Trước → Sau</th>
					<th>Tham chiếu</th>
					<th>Ghi chú</th>
				</tr>
			</thead>
			<tbody>
			<?php if (empty($list)) { ?>
				<tr><td colspan="9" class="text-center text-muted py-4">Chưa có biến động.</td></tr>
			<?php } else {
				foreach ($list as $m) { ?>
				<tr>
					<td class="small"><?php echo date('d/m/Y H:i', (int) $m->created); ?></td>
					<td><?php echo htmlspecialchars($m->product_name ?: ('#' . $m->product_id), ENT_QUOTES, 'UTF-8'); ?></td>
					<td><?php echo $m->size !== '' ? htmlspecialchars($m->size, ENT_QUOTES, 'UTF-8') : '—'; ?></td>
					<td><?php echo $m->color !== '' ? htmlspecialchars($m->color, ENT_QUOTES, 'UTF-8') : '—'; ?></td>
					<td><code><?php echo htmlspecialchars($m->movement_type, ENT_QUOTES, 'UTF-8'); ?></code></td>
					<td class="<?php echo (int) $m->qty_change >= 0 ? 'text-success' : 'text-danger'; ?> fw-semibold">
						<?php echo ((int) $m->qty_change > 0 ? '+' : '') . number_format((int) $m->qty_change); ?>
					</td>
					<td><?php echo number_format((int) $m->before_qty) . ' → ' . number_format((int) $m->after_qty); ?></td>
					<td class="small"><?php echo htmlspecialchars($m->reference_type . ' #' . $m->reference_id, ENT_QUOTES, 'UTF-8'); ?></td>
					<td class="small"><?php echo htmlspecialchars($m->note, ENT_QUOTES, 'UTF-8'); ?></td>
				</tr>
			<?php }
			} ?>
			</tbody>
		</table>
	</div>
	<?php if (!empty($total) && $total > 0) { ?>
		<div class="admin-card-body border-top py-2"><?php echo $this->pagination->create_links(); ?></div>
	<?php } ?>
</div>
