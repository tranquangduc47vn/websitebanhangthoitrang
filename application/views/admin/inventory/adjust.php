<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('inventory'); ?>">Tồn kho</a></li>
			<li class="breadcrumb-item active">Kiểm kê</li>
		</ol>
	</nav>
</div>

<h1 class="h4 mb-3">Kiểm kê / điều chỉnh tồn</h1>

<div class="admin-card">
	<div class="admin-card-body">
		<p class="mb-2"><strong><?php echo html_escape($product_name); ?></strong></p>
		<p class="text-muted small mb-3">SKU: <code><?php echo html_escape($variant->sku); ?></code>
			· Màu: <?php echo html_escape($variant->color ?: '—'); ?>
			· Size: <?php echo html_escape($variant->size ?: '—'); ?>
			· Tồn hiện tại: <strong><?php echo number_format((int) $variant->stock); ?></strong></p>

		<form method="post" action="<?php echo admin_url('inventory/adjust/' . (int) $variant->id); ?>" class="row g-3" style="max-width: 520px;">
			<input type="hidden" name="variant_id" value="<?php echo (int) $variant->id; ?>">
			<div class="col-12">
				<label class="form-label">Tồn sau điều chỉnh</label>
				<input type="number" name="new_qty" class="form-control" min="0" required
					value="<?php echo (int) $variant->stock; ?>">
			</div>
			<div class="col-12">
				<label class="form-label">Lý do <span class="text-danger">*</span></label>
				<textarea name="note" class="form-control" rows="3" required placeholder="Kiểm kê định kỳ, hàng hỏng, sai sót nhập liệu…"></textarea>
			</div>
			<div class="col-12 d-flex gap-2">
				<button type="submit" class="btn btn-primary">Lưu điều chỉnh</button>
				<a href="<?php echo admin_url('inventory'); ?>" class="btn btn-outline-secondary">Hủy</a>
			</div>
		</form>
	</div>
</div>
