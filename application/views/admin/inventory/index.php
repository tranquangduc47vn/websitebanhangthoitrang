<?php
$filters = isset($filters) ? $filters : array();
$search_name = isset($filters['name']) ? $filters['name'] : '';
$search_catalog = isset($filters['catalog_id']) ? $filters['catalog_id'] : '';
$search_sku = isset($filters['sku']) ? $filters['sku'] : '';
$stock_filter = isset($filters['stock']) ? $filters['stock'] : '';
$this->load->helper('export');
?>
<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>">Trang chủ</a></li>
			<li class="breadcrumb-item active">Tồn kho</li>
		</ol>
	</nav>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
	<h1 class="h4 mb-0">Tồn kho theo biến thể</h1>
	<div class="d-flex gap-2">
		<a href="<?php echo admin_url('receipts'); ?>" class="btn btn-outline-secondary btn-sm">Phiếu nhập</a>
		<a href="<?php echo admin_url('stock-movements'); ?>" class="btn btn-outline-secondary btn-sm">Lịch sử biến động</a>
	</div>
</div>

<?php admin_export_toolbar('inventory'); ?>

<div class="row g-3 mb-3">
	<div class="col-md-3">
		<div class="admin-card h-100"><div class="admin-card-body py-3">
			<div class="text-muted small">Tổng biến thể</div>
			<div class="fs-4 fw-semibold"><?php echo number_format($stats['total']); ?></div>
		</div></div>
	</div>
	<div class="col-md-3">
		<div class="admin-card h-100 border-danger-subtle"><div class="admin-card-body py-3">
			<div class="text-danger small">Hết hàng</div>
			<div class="fs-4 fw-semibold text-danger"><?php echo number_format($stats['out_of_stock']); ?></div>
		</div></div>
	</div>
	<div class="col-md-3">
		<div class="admin-card h-100 border-warning-subtle"><div class="admin-card-body py-3">
			<div class="text-warning small">Sắp hết</div>
			<div class="fs-4 fw-semibold"><?php echo number_format($stats['low_stock']); ?></div>
		</div></div>
	</div>
	<div class="col-md-3">
		<div class="admin-card h-100"><div class="admin-card-body py-3">
			<div class="text-muted small">Giá trị tồn (cost)</div>
			<div class="fs-5 fw-semibold"><?php echo number_format($stats['stock_value']); ?> ₫</div>
		</div></div>
	</div>
</div>

<div class="admin-card mb-3">
	<div class="admin-card-body">
		<form class="row g-2 align-items-end" method="get" action="<?php echo admin_url('inventory'); ?>">
			<div class="col-md-3">
				<label class="form-label small mb-1">Tên SP</label>
				<input type="search" name="name" class="form-control form-control-sm" value="<?php echo html_escape($search_name); ?>">
			</div>
			<div class="col-md-2">
				<label class="form-label small mb-1">SKU</label>
				<input type="search" name="sku" class="form-control form-control-sm" value="<?php echo html_escape($search_sku); ?>">
			</div>
			<div class="col-md-3">
				<label class="form-label small mb-1">Danh mục</label>
				<select name="catalog_id" class="form-select form-select-sm">
					<option value="">— Tất cả —</option>
					<?php foreach ($catalog as $parent) {
						if (!empty($parent->sub)) {
							foreach ($parent->sub as $sub) { ?>
								<option value="<?php echo (int) $sub->id; ?>" <?php echo (string) $search_catalog === (string) $sub->id ? 'selected' : ''; ?>>
									<?php echo html_escape($sub->name); ?>
								</option>
							<?php }
						}
					} ?>
				</select>
			</div>
			<div class="col-md-2">
				<label class="form-label small mb-1">Trạng thái</label>
				<select name="stock" class="form-select form-select-sm">
					<option value="">Tất cả</option>
					<option value="out" <?php echo $stock_filter === 'out' ? 'selected' : ''; ?>>Hết hàng</option>
					<option value="low" <?php echo $stock_filter === 'low' ? 'selected' : ''; ?>>Sắp hết</option>
					<option value="ok" <?php echo $stock_filter === 'ok' ? 'selected' : ''; ?>>Còn hàng</option>
				</select>
			</div>
			<div class="col-md-2">
				<label class="form-label small mb-1 d-block">&nbsp;</label>
				<a href="<?php echo admin_url('inventory'); ?>" class="btn btn-outline-secondary btn-sm w-100">Xóa lọc</a>
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
					<th>SKU</th>
					<th>Sản phẩm</th>
					<th>Màu</th>
					<th>Size</th>
					<th class="text-end">Tồn</th>
					<th class="text-end">Tối thiểu</th>
					<th>Trạng thái</th>
					<?php if (!empty($can_adjust)) { ?><th></th><?php } ?>
				</tr>
			</thead>
			<tbody>
			<?php
			if (!empty($list)) {
				foreach ($list as $row) {
					$stock = (int) $row->stock;
					$min = (int) $row->min_stock;
					if ($stock <= 0) {
						$st = array('label' => 'Hết hàng', 'class' => 'danger');
					} elseif ($stock <= $min) {
						$st = array('label' => 'Sắp hết', 'class' => 'warning');
					} else {
						$st = array('label' => 'Còn hàng', 'class' => 'success');
					}
			?>
				<tr>
					<td><code><?php echo html_escape($row->sku); ?></code></td>
					<td><?php echo html_escape($row->product_name); ?></td>
					<td><?php echo html_escape($row->color !== '' ? $row->color : '—'); ?></td>
					<td><?php echo html_escape($row->size !== '' ? $row->size : '—'); ?></td>
					<td class="text-end fw-semibold"><?php echo number_format((int) $row->stock); ?></td>
					<td class="text-end"><?php echo number_format((int) $row->min_stock); ?></td>
					<td><span class="badge text-bg-<?php echo $st['class']; ?>"><?php echo $st['label']; ?></span></td>
					<?php if (!empty($can_adjust)) { ?>
					<td class="text-end">
						<a href="<?php echo admin_url('inventory/adjust/' . (int) $row->id); ?>" class="btn btn-outline-secondary btn-sm">Kiểm kê</a>
					</td>
					<?php } ?>
				</tr>
			<?php }
			} else { ?>
				<tr><td colspan="<?php echo !empty($can_adjust) ? 8 : 7; ?>" class="text-center text-muted py-4">Không có dữ liệu.</td></tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="admin-table-footer admin-table-footer--center p-2">
		<?php echo $this->pagination->create_links(); ?>
	</div>
</div>
