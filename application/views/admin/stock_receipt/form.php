<?php
$is_edit = !empty($receipt);
$action = $is_edit
	? admin_url('stock-receipts/edit/' . (int) $receipt->id)
	: admin_url('stock-receipts/add');
$form_items = !empty($items) ? $items : array();
$line_variants = !empty($line_variants) ? $line_variants : array();
$initial_lines = array();
foreach ($form_items as $item) {
	$vid = isset($item->variant_id) ? (int) $item->variant_id : 0;
	if ($vid <= 0) {
		continue;
	}
	$variant = isset($line_variants[$vid]) ? $line_variants[$vid] : null;
	$initial_lines[] = array(
		'variant_id' => $vid,
		'sku' => $variant ? (string) $variant->sku : '',
		'product_name' => $variant ? (string) $variant->product_name : (isset($item->product_name) ? (string) $item->product_name : ''),
		'color' => $variant ? (string) $variant->color : (isset($item->color) ? (string) $item->color : ''),
		'size' => $variant ? (string) $variant->size : (isset($item->size) ? (string) $item->size : ''),
		'qty' => isset($item->qty) ? (int) $item->qty : 1,
		'unit_cost' => isset($item->unit_cost) ? (float) $item->unit_cost : 0,
	);
}
$admin_asset_url = base_url('assets/admin/');
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.bootstrap5.min.css">
<link href="<?php echo $admin_asset_url; ?>css/stock-receipt-form.css?v=2" rel="stylesheet">

<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>">Trang chủ</a></li>
			<li class="breadcrumb-item"><a href="<?php echo admin_url('stock-receipts'); ?>">Phiếu nhập</a></li>
			<li class="breadcrumb-item active"><?php echo $is_edit ? 'Sửa phiếu' : 'Tạo phiếu'; ?></li>
		</ol>
	</nav>
</div>

<h1 class="h4 mb-3"><?php echo $is_edit ? 'Sửa phiếu nháp' : 'Tạo phiếu nhập (nháp)'; ?></h1>

<form method="post" action="<?php echo $action; ?>" id="stock-receipt-form">
	<input type="hidden" name="submit" value="1">

	<div class="admin-card mb-3">
		<div class="admin-card-body row g-3">
			<div class="col-md-4">
				<label class="form-label">Nhà cung cấp</label>
				<select name="supplier_id" class="form-select form-select-sm">
					<option value="0">— Không chọn —</option>
					<?php foreach ($suppliers as $s) { ?>
						<option value="<?php echo (int) $s->id; ?>" <?php echo ($is_edit && (int) $receipt->supplier_id === (int) $s->id) ? 'selected' : ''; ?>>
							<?php echo htmlspecialchars($s->code . ' — ' . $s->name, ENT_QUOTES, 'UTF-8'); ?>
						</option>
					<?php } ?>
				</select>
			</div>
			<div class="col-md-8">
				<label class="form-label">Ghi chú</label>
				<input type="text" name="note" class="form-control form-control-sm" maxlength="500"
					value="<?php echo $is_edit ? htmlspecialchars($receipt->note, ENT_QUOTES, 'UTF-8') : ''; ?>">
			</div>
		</div>
	</div>

	<div class="admin-card mb-3 receipt-picker-card">
		<div class="admin-card-header">
			<span><i class="fa-solid fa-filter me-1"></i> Chọn biến thể nhập kho</span>
		</div>
		<div class="admin-card-body">
			<div class="row g-2 align-items-end receipt-filter-bar mb-3">
				<div class="col-md-2">
					<label class="form-label small mb-1" for="filter-catalog">Danh mục</label>
					<select id="filter-catalog" class="form-select form-select-sm">
						<option value="">— Tất cả —</option>
						<?php foreach ($catalogs as $cat) { ?>
							<option value="<?php echo (int) $cat->id; ?>"><?php echo html_escape($cat->name); ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-md-3">
					<label class="form-label small mb-1" for="filter-product">Sản phẩm</label>
					<select id="filter-product" placeholder="Tìm sản phẩm…"></select>
				</div>
				<div class="col-md-2">
					<label class="form-label small mb-1" for="filter-color">Màu</label>
					<select id="filter-color" class="form-select form-select-sm">
						<option value="">— Tất cả —</option>
					</select>
				</div>
				<div class="col-md-2">
					<label class="form-label small mb-1" for="filter-size">Size</label>
					<select id="filter-size" class="form-select form-select-sm">
						<option value="">— Tất cả —</option>
					</select>
				</div>
				<div class="col-md-3">
					<label class="form-label small mb-1" for="filter-q">SKU / tên / màu / size</label>
					<div class="input-group input-group-sm">
						<input type="search" id="filter-q" class="form-control" placeholder="VD: SP001, áo thun, đen, M">
					</div>
				</div>
			</div>

			<div class="recent-variants-wrap mb-3" id="recent-variants-wrap" hidden>
				<div class="small text-muted mb-2"><i class="fa-solid fa-clock-rotate-left me-1"></i> Biến thể đã dùng gần đây</div>
				<div class="recent-variants-list" id="recent-variants-list"></div>
			</div>

			<div class="variant-picker-meta d-flex justify-content-between align-items-center mb-2">
				<div class="small text-muted" id="variant-picker-summary">Nhập từ khóa hoặc chọn bộ lọc để tìm biến thể.</div>
				<div class="btn-group btn-group-sm" id="variant-picker-pagination" hidden>
					<button type="button" class="btn btn-outline-secondary" id="picker-prev" disabled>&laquo; Trước</button>
					<span class="btn btn-outline-secondary disabled" id="picker-page-label">1/1</span>
					<button type="button" class="btn btn-outline-secondary" id="picker-next" disabled>Sau &raquo;</button>
				</div>
			</div>

			<div class="table-responsive">
				<table class="table table-sm align-middle mb-0" id="variant-picker">
					<thead>
						<tr>
							<th>SKU</th>
							<th>Sản phẩm</th>
							<th>Màu</th>
							<th>Size</th>
							<th class="text-end">Tồn hiện tại</th>
							<th style="width:120px">Giá nhập</th>
							<th style="width:90px">SL nhập</th>
							<th style="width:120px" class="text-end">Tổng tiền nhập</th>
							<th style="width:70px"></th>
						</tr>
					</thead>
					<tbody id="variant-picker-body">
						<tr class="picker-empty">
							<td colspan="9" class="text-center text-muted py-4">Dùng bộ lọc phía trên để tìm biến thể.</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="admin-card mb-3">
		<div class="admin-card-header d-flex justify-content-between align-items-center">
			<span>Chi tiết phiếu nhập</span>
			<span class="badge bg-secondary" id="receipt-line-count">0 dòng</span>
		</div>
		<div class="table-responsive">
			<table class="table table-sm align-middle mb-0" id="receipt-lines">
				<thead>
					<tr>
						<th>SKU</th>
						<th>Sản phẩm</th>
						<th>Màu</th>
						<th>Size</th>
						<th style="width:100px">SL</th>
						<th style="width:120px" class="text-end">Tổng tiền nhập</th>
						<th style="width:120px">Giá nhập</th>
						<th style="width:40px"></th>
					</tr>
				</thead>
				<tbody id="receipt-lines-body">
					<tr class="receipt-empty" id="receipt-empty-row">
						<td colspan="8" class="text-center text-muted py-4">Chưa có dòng nhập. Thêm biến thể từ bảng phía trên.</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="5" class="text-end">Tổng tiền</th>
						<th class="text-end" id="receipt-total">0</th>
						<th colspan="2"></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>

	<div class="d-flex gap-2">
		<button type="submit" class="btn btn-primary" id="btn-save-receipt">Lưu phiếu nháp</button>
		<a href="<?php echo admin_url('stock-receipts'); ?>" class="btn btn-outline-secondary">Huỷ</a>
	</div>
</form>

<script>
window.STOCK_RECEIPT_FORM = <?php echo json_encode(array(
	'urls' => isset($receipt_form_urls) ? $receipt_form_urls : array(),
	'initialLines' => $initial_lines,
), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
<script src="<?php echo $admin_asset_url; ?>js/stock-receipt-form.js?v=2"></script>
