<?php
$query_string = $_SERVER['QUERY_STRING'] ?? '';
$redirect_query = htmlspecialchars($query_string, ENT_QUOTES, 'UTF-8');
?>
<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item active" aria-current="page">Tồn kho</li>
		</ol>
	</nav>
</div>

<?php $this->load->helper('export'); admin_export_toolbar('inventory'); ?>

<div class="row g-3 mb-3">
	<div class="col-md-4">
		<div class="admin-card h-100">
			<div class="admin-card-body py-3">
				<div class="text-muted small text-uppercase fw-semibold">Tổng sản phẩm</div>
				<div class="fs-4 fw-semibold"><?php echo number_format($stats['total']); ?></div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="admin-card h-100 border-danger-subtle">
			<div class="admin-card-body py-3">
				<div class="text-danger small text-uppercase fw-semibold">Hết hàng</div>
				<div class="fs-4 fw-semibold text-danger"><?php echo number_format($stats['out_of_stock']); ?></div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="admin-card h-100 border-warning-subtle">
			<div class="admin-card-body py-3">
				<div class="text-warning small text-uppercase fw-semibold">Sắp hết (≤ 10)</div>
				<div class="fs-4 fw-semibold"><?php echo number_format($stats['low_stock']); ?></div>
			</div>
		</div>
	</div>
</div>

<div class="admin-card mb-3">
	<div class="admin-card-body">
		<form class="row g-2 align-items-end" action="<?php echo admin_url('inventory/index'); ?>" method="get">
			<div class="col-md-4">
				<label class="form-label small mb-1" for="inv-name">Tên sản phẩm</label>
				<input type="search" name="name" id="inv-name" class="form-control form-control-sm"
					value="<?php echo htmlspecialchars($search_name, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Tìm theo tên…">
			</div>
			<div class="col-md-3">
				<label class="form-label small mb-1" for="inv-catalog">Danh mục</label>
				<select name="catalog_id" id="inv-catalog" class="form-select form-select-sm">
					<option value="">— Tất cả —</option>
					<?php foreach ($catalog as $parent) { ?>
						<optgroup label="<?php echo htmlspecialchars($parent->name, ENT_QUOTES, 'UTF-8'); ?>">
							<?php if (!empty($parent->sub)) {
								foreach ($parent->sub as $sub) { ?>
									<option value="<?php echo (int) $sub->id; ?>" <?php echo ((string) $search_catalog === (string) $sub->id) ? 'selected' : ''; ?>>
										<?php echo htmlspecialchars($sub->name, ENT_QUOTES, 'UTF-8'); ?>
									</option>
								<?php }
							} ?>
						</optgroup>
					<?php } ?>
				</select>
			</div>
			<div class="col-md-3">
				<label class="form-label small mb-1" for="inv-stock">Trạng thái tồn</label>
				<select name="stock" id="inv-stock" class="form-select form-select-sm">
					<option value="" <?php echo $stock_filter === '' || $stock_filter === null ? 'selected' : ''; ?>>Tất cả</option>
					<option value="out" <?php echo $stock_filter === 'out' ? 'selected' : ''; ?>>Hết hàng</option>
					<option value="low" <?php echo $stock_filter === 'low' ? 'selected' : ''; ?>>Sắp hết (1–10)</option>
					<option value="ok" <?php echo $stock_filter === 'ok' ? 'selected' : ''; ?>>Đủ hàng (&gt; 10)</option>
				</select>
			</div>
			<div class="col-md-2 d-flex gap-2">
				<button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fa-solid fa-filter me-1"></i> Lọc</button>
				<a href="<?php echo admin_url('inventory'); ?>" class="btn btn-outline-secondary btn-sm">Xóa</a>
			</div>
		</form>
	</div>
</div>

<div class="admin-card">
	<div class="admin-card-header">
		<span>Danh sách tồn kho</span>
		<span class="text-muted small"><?php echo number_format($total); ?> sản phẩm</span>
	</div>
	<div class="admin-card-body">
		<?php if (!empty($message_success)) { ?>
			<div class="alert alert-success"><strong>Thành công!</strong> <?php echo $message_success; ?></div>
		<?php } ?>
		<?php if (!empty($message_fail)) { ?>
			<div class="alert alert-danger"><strong>Lỗi!</strong> <?php echo $message_fail; ?></div>
		<?php } ?>

		<?php if (empty($can_adjust_inventory)) { ?>
			<div class="alert alert-info py-2 small mb-3">Role <strong>User</strong>: chỉ xem tồn kho. Nhập kho dành cho <strong>Admin</strong> và <strong>Mod</strong>.</div>
		<?php } ?>

		<div class="table-responsive">
			<table class="table table-hover admin-table align-middle">
				<thead>
					<tr>
						<th style="width: 56px;">Ảnh</th>
						<th>Sản phẩm</th>
						<th>Danh mục</th>
						<th class="text-center">Đã bán</th>
						<th class="text-center">Tồn kho</th>
						<?php if (!empty($can_adjust_inventory)) { ?>
						<th style="min-width: 220px;">Nhập thêm</th>
						<?php } ?>
					</tr>
				</thead>
				<tbody>
				<?php if (!empty($products)) {
					foreach ($products as $row) {
						$qty = isset($row->quantity) ? (int) $row->quantity : 0;
						$img = !empty($row->image_link) ? base_url('upload/product/' . $row->image_link) : base_url('upload/product/default.jpg');
				?>
					<tr>
						<td>
							<img src="<?php echo $img; ?>" alt="" class="rounded" width="48" height="48" style="object-fit: cover;">
						</td>
						<td>
							<div class="fw-semibold"><?php echo htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'); ?></div>
							<small class="text-muted">#<?php echo (int) $row->id; ?></small>
						</td>
						<td><?php echo htmlspecialchars($row->namecatalog ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
						<td class="text-center"><?php echo number_format(isset($row->buyed) ? (int) $row->buyed : 0); ?></td>
						<td class="text-center">
							<?php if ($qty <= 0) { ?>
								<span class="badge text-bg-danger">Hết hàng</span>
								<div class="fw-bold mt-1">0</div>
							<?php } elseif ($qty <= 10) { ?>
								<span class="badge text-bg-warning">Sắp hết</span>
								<div class="fw-bold mt-1"><?php echo number_format($qty); ?></div>
							<?php } else { ?>
								<span class="badge text-bg-success">Ổn định</span>
								<div class="fw-bold mt-1"><?php echo number_format($qty); ?></div>
							<?php } ?>
						</td>
						<?php if (!empty($can_adjust_inventory)) { ?>
						<td>
							<form class="d-flex flex-wrap gap-2 align-items-center" method="post" action="<?php echo admin_url('inventory/adjust'); ?>">
								<input type="hidden" name="product_id" value="<?php echo (int) $row->id; ?>">
								<input type="hidden" name="redirect_query" value="<?php echo $redirect_query; ?>">
								<input type="number" name="add_qty" class="form-control form-control-sm" style="width: 100px;" min="1" max="999999" placeholder="SL +" required>
								<button type="submit" class="btn btn-sm btn-dark">
									<i class="fa-solid fa-boxes-stacked me-1"></i> Nhập kho
								</button>
							</form>
						</td>
						<?php } ?>
					</tr>
				<?php }
				} else { ?>
					<tr>
						<td colspan="<?php echo !empty($can_adjust_inventory) ? 6 : 5; ?>" class="text-center text-muted py-4">Không có sản phẩm phù hợp bộ lọc.</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="admin-table-footer admin-table-footer--center">
			<?php echo $this->pagination->create_links(); ?>
		</div>
	</div>
</div>
