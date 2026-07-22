<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item active" aria-current="page">Voucher</li>
		</ol>
	</nav>
</div>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
	<p class="admin-page-subtitle mb-0">Mã giảm giá theo hạng thành viên hoặc khách cụ thể.</p>
	<a href="<?php echo admin_url('voucher/add'); ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Thêm voucher</a>
</div>

<?php if (!empty($message)) { ?>
	<div class="alert alert-success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
<?php } ?>
<?php if (!empty($message_fail)) { ?>
	<div class="alert alert-danger"><?php echo htmlspecialchars($message_fail, ENT_QUOTES, 'UTF-8'); ?></div>
<?php } ?>

<?php $this->load->helper('export'); admin_export_toolbar('voucher'); ?>

<div class="admin-card">
	<div class="admin-card-body table-responsive">
		<table class="table table-hover admin-table">
			<thead>
				<tr>
					<th>Mã</th>
					<th>Tên</th>
					<th>Giảm</th>
					<th>Hạng tối thiểu</th>
					<th>Đã dùng</th>
					<th>Trạng thái</th>
					<th class="text-center">Thao tác</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($list)) { ?>
					<?php foreach ($list as $row) {
						$disc = ($row->discount_type === 'percent')
							? (int) $row->discount_value . '%'
							: number_format((int) $row->discount_value, 0, ',', '.') . ' ₫';
						$used = (int) $row->used_count;
						$limit = (int) $row->usage_limit;
						$used_txt = $limit > 0 ? $used . ' / ' . $limit : (string) $used;
					?>
						<tr>
							<td><code><?php echo htmlspecialchars($row->code, ENT_QUOTES, 'UTF-8'); ?></code></td>
							<td><?php echo htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'); ?></td>
							<td><?php echo $disc; ?></td>
							<td><?php echo loyalty_tier_label($row->tier_min); ?></td>
							<td><?php echo $used_txt; ?></td>
							<td data-voucher-status="<?php echo (int) $row->id; ?>"><?php echo (int) $row->is_active === 1 ? 'Đang bật' : 'Tắt'; ?></td>
							<td class="text-center">
								<div class="admin-voucher-actions">
									<label class="form-check form-switch admin-voucher-switch mb-0" title="Bật / tắt voucher">
										<input type="checkbox" class="form-check-input js-voucher-toggle" role="switch"
											aria-label="Bật tắt voucher <?php echo htmlspecialchars($row->code, ENT_QUOTES, 'UTF-8'); ?>"
											data-url="<?php echo admin_url('voucher/toggle_active/' . (int) $row->id); ?>"
											<?php echo (int) $row->is_active === 1 ? 'checked' : ''; ?>>
									</label>
									<a class="btn btn-sm btn-outline-primary" href="<?php echo admin_url('voucher/edit/' . (int) $row->id); ?>" title="Sửa"><i class="fa-solid fa-pen"></i></a>
									<a class="btn btn-sm btn-outline-danger" href="<?php echo admin_url('voucher/delete/' . (int) $row->id); ?>" title="Xóa" onclick="return confirm('Xóa voucher này?');"><i class="fa-solid fa-trash"></i></a>
								</div>
							</td>
						</tr>
					<?php } ?>
				<?php } else { ?>
					<tr><td colspan="7" class="admin-empty">Chưa có voucher.</td></tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>

<style>
.admin-voucher-actions {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 0.5rem;
	flex-wrap: nowrap;
}
.admin-voucher-switch {
	display: inline-flex;
	align-items: center;
	padding-left: 2.5em;
	min-height: 1.5rem;
	cursor: pointer;
}
.admin-voucher-switch .form-check-input {
	width: 2.5em;
	height: 1.25em;
	margin-left: -2.5em;
	cursor: pointer;
}
</style>
<script>
(function () {
	document.querySelectorAll('.js-voucher-toggle').forEach(function (input) {
		input.addEventListener('change', function () {
			var url = input.getAttribute('data-url');
			var row = input.closest('tr');
			var statusCell = row ? row.querySelector('[data-voucher-status]') : null;
			var prev = !input.checked;

			fetch(url, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'X-Requested-With': 'XMLHttpRequest' },
			})
				.then(function (res) { return res.json(); })
				.then(function (data) {
					if (!data.ok) {
						input.checked = prev;
						return;
					}
					if (statusCell && data.label) {
						statusCell.textContent = data.label;
					}
				})
				.catch(function () {
					input.checked = prev;
				});
		});
	});
})();
</script>
