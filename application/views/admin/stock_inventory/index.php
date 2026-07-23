<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>">Trang chủ</a></li>
			<li class="breadcrumb-item active">Tồn kho</li>
		</ol>
	</nav>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
	<h1 class="h4 mb-0">Tồn kho</h1>
	<div class="d-flex gap-2">
		<a href="<?php echo admin_url('stock-receipts'); ?>" class="btn btn-outline-secondary btn-sm">Phiếu nhập</a>
		<a href="<?php echo admin_url('stock-inventory/movements'); ?>" class="btn btn-outline-secondary btn-sm">Lịch sử biến động</a>
	</div>
</div>

<div class="admin-card mb-3">
	<div class="admin-card-body">
		<form class="row g-2 align-items-end" method="get" action="<?php echo admin_url('stock-inventory'); ?>">
			<div class="col-md-4">
				<label class="form-label small mb-1">Tên sản phẩm</label>
				<input type="search" name="name" class="form-control form-control-sm" value="<?php echo htmlspecialchars($search_name, ENT_QUOTES, 'UTF-8'); ?>">
			</div>
			<div class="col-md-3">
				<label class="form-label small mb-1">Danh mục</label>
				<select name="catalog_id" class="form-select form-select-sm">
					<option value="">— Tất cả —</option>
					<?php foreach ($catalog as $parent) {
						if (!empty($parent->sub)) {
							foreach ($parent->sub as $sub) { ?>
								<option value="<?php echo (int) $sub->id; ?>" <?php echo ((string) $search_catalog === (string) $sub->id) ? 'selected' : ''; ?>>
									<?php echo htmlspecialchars($sub->name, ENT_QUOTES, 'UTF-8'); ?>
								</option>
							<?php }
						}
					} ?>
				</select>
			</div>
			<div class="col-md-3">
				<label class="form-label small mb-1">Trạng thái</label>
				<select name="stock" class="form-select form-select-sm">
					<option value="">Tất cả</option>
					<option value="out" <?php echo $stock_filter === 'out' ? 'selected' : ''; ?>>Hết hàng</option>
					<option value="low" <?php echo $stock_filter === 'low' ? 'selected' : ''; ?>>Sắp hết</option>
					<option value="ok" <?php echo $stock_filter === 'ok' ? 'selected' : ''; ?>>Đủ hàng</option>
				</select>
			</div>
			<div class="col-md-2">
				<label class="form-label small mb-1 d-block">&nbsp;</label>
				<a href="<?php echo admin_url('stock-inventory'); ?>" class="btn btn-outline-secondary btn-sm w-100">Xóa lọc</a>
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
					<th>Sản phẩm</th>
					<th>Danh mục</th>
					<th>Size</th>
					<th>Màu</th>
					<th>Tồn</th>
					<th>Cập nhật</th>
				</tr>
			</thead>
			<tbody>
			<?php if (empty($rows)) { ?>
				<tr><td colspan="6" class="text-center text-muted py-4">Chưa có tồn biến thể. Xác nhận phiếu nhập để cộng kho.</td></tr>
			<?php } else {
				foreach ($rows as $row) { ?>
				<tr>
					<td><?php echo htmlspecialchars($row->product_name, ENT_QUOTES, 'UTF-8'); ?></td>
					<td><?php echo htmlspecialchars($row->catalog_name ?: '—', ENT_QUOTES, 'UTF-8'); ?></td>
					<td><?php echo $row->size !== '' ? htmlspecialchars($row->size, ENT_QUOTES, 'UTF-8') : '—'; ?></td>
					<td><?php echo $row->color !== '' ? htmlspecialchars($row->color, ENT_QUOTES, 'UTF-8') : '—'; ?></td>
					<td class="fw-semibold <?php echo (int) $row->quantity <= 0 ? 'text-danger' : ''; ?>"><?php echo number_format((int) $row->quantity); ?></td>
					<td class="small text-muted"><?php echo !empty($row->updated) ? date('d/m/Y H:i', (int) $row->updated) : '—'; ?></td>
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
