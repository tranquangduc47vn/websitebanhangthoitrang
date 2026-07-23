<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>">Trang chủ</a></li>
			<li class="breadcrumb-item"><a href="<?php echo admin_url('inventory'); ?>">Tồn kho</a></li>
			<li class="breadcrumb-item active">Lịch sử biến động</li>
		</ol>
	</nav>
</div>

<h1 class="h4 mb-3">Lịch sử biến động kho</h1>

<div class="admin-card mb-3">
	<div class="admin-card-body">
		<form class="row g-2 align-items-end" method="get" action="<?php echo admin_url('stock-movements'); ?>">
			<div class="col-md-3">
				<label class="form-label small mb-1">SKU</label>
				<input type="search" name="sku" class="form-control form-control-sm" value="<?php echo html_escape($filter_sku); ?>">
			</div>
			<div class="col-md-3">
				<label class="form-label small mb-1">Loại</label>
				<select name="type" class="form-select form-select-sm">
					<option value="">— Tất cả —</option>
					<option value="in" <?php echo $filter_type === 'in' ? 'selected' : ''; ?>>Nhập (in)</option>
					<option value="out" <?php echo $filter_type === 'out' ? 'selected' : ''; ?>>Xuất (out)</option>
					<option value="adjust" <?php echo $filter_type === 'adjust' ? 'selected' : ''; ?>>Kiểm kê (adjust)</option>
				</select>
			</div>
			<div class="col-md-2">
				<label class="form-label small mb-1 d-block">&nbsp;</label>
				<a href="<?php echo admin_url('stock-movements'); ?>" class="btn btn-outline-secondary btn-sm w-100">Xóa lọc</a>
				<noscript><button type="submit" class="btn btn-primary btn-sm w-100 mt-1">Lọc</button></noscript>
			</div>
		</form>
	</div>
</div>

<div class="admin-card">
	<div class="table-responsive">
		<table class="table table-sm align-middle mb-0">
			<thead>
				<tr>
					<th>Thời gian</th>
					<th>SKU</th>
					<th>Sản phẩm</th>
					<th>Loại</th>
					<th class="text-end">Trước</th>
					<th class="text-end">Thay đổi</th>
					<th class="text-end">Sau</th>
					<th>Tham chiếu</th>
					<th>Ghi chú</th>
				</tr>
			</thead>
			<tbody>
			<?php if (!empty($list)) {
				foreach ($list as $m) {
					$ref = $m->reference_type . ' #' . (int) $m->reference_id;
			?>
				<tr>
					<td class="small"><?php echo date('d/m/Y H:i', (int) $m->created); ?></td>
					<td><code><?php echo html_escape($m->sku ?: '—'); ?></code></td>
					<td><?php echo html_escape($m->product_name ?: ('#' . $m->product_id)); ?></td>
					<td><span class="badge text-bg-secondary"><?php echo html_escape($m->movement_type); ?></span></td>
					<td class="text-end"><?php echo number_format((int) $m->before_qty); ?></td>
					<td class="text-end"><?php echo ($m->qty_change > 0 ? '+' : '') . number_format((int) $m->qty_change); ?></td>
					<td class="text-end fw-semibold"><?php echo number_format((int) $m->after_qty); ?></td>
					<td class="small"><?php echo html_escape($ref); ?></td>
					<td class="small text-muted"><?php echo html_escape($m->note); ?></td>
				</tr>
			<?php }
			} else { ?>
				<tr><td colspan="9" class="text-center text-muted py-4">Chưa có biến động.</td></tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="admin-table-footer admin-table-footer--center p-2">
		<?php echo $this->pagination->create_links(); ?>
	</div>
</div>
