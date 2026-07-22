<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item active" aria-current="page">Thống kê doanh thu</li>
		</ol>
	</nav>
</div>

<?php $this->load->helper('export'); admin_export_toolbar('revenue'); ?>
<?php admin_export_toolbar('top_products'); ?>

<div class="admin-card mb-3">
	<div class="admin-card-header">Bộ lọc thống kê</div>
	<div class="admin-card-body">
		<div class="btn-group flex-wrap mb-3" role="group">
			<a href="<?php echo admin_url('report?type=day'); ?>" class="btn btn-sm <?php echo ($current_type == 'day') ? 'btn-primary' : 'btn-outline-secondary'; ?>">Hôm nay</a>
			<a href="<?php echo admin_url('report?type=week'); ?>" class="btn btn-sm <?php echo ($current_type == 'week') ? 'btn-primary' : 'btn-outline-secondary'; ?>">Tuần này</a>
			<a href="<?php echo admin_url('report?type=month'); ?>" class="btn btn-sm <?php echo ($current_type == 'month') ? 'btn-primary' : 'btn-outline-secondary'; ?>">Tháng này</a>
			<a href="<?php echo admin_url('report?type=year'); ?>" class="btn btn-sm <?php echo ($current_type == 'year') ? 'btn-primary' : 'btn-outline-secondary'; ?>">Năm nay</a>
		</div>
		<form method="get" action="<?php echo admin_url('report'); ?>" class="row g-2 align-items-end">
			<div class="col-md-3">
				<label class="form-label">Từ ngày</label>
				<input type="date" name="date_from" class="form-control" value="<?php echo $this->input->get('date_from'); ?>">
			</div>
			<div class="col-md-3">
				<label class="form-label">Đến ngày</label>
				<input type="date" name="date_to" class="form-control" value="<?php echo $this->input->get('date_to'); ?>">
			</div>
			<div class="col-md-4">
				<button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-filter me-1"></i> Lọc dữ liệu</button>
				<a href="<?php echo admin_url('report'); ?>" class="btn btn-default btn-sm">Xóa bộ lọc</a>
			</div>
		</form>
	</div>
</div>

<div class="row g-3 mb-3">
	<div class="col-md-4">
		<div class="admin-stat-card">
			<div>
				<div class="stat-value"><?php echo number_format($total_orders); ?></div>
				<div class="stat-label">Đơn hàng thành công</div>
			</div>
			<span class="admin-stat-icon icon-primary"><i class="fa-solid fa-bag-shopping"></i></span>
		</div>
	</div>
	<div class="col-md-4">
		<div class="admin-stat-card">
			<div>
				<div class="stat-value"><?php echo number_format($total_qty_sold); ?></div>
				<div class="stat-label">Số lượng bán ra</div>
			</div>
			<span class="admin-stat-icon icon-warning"><i class="fa-solid fa-boxes-stacked"></i></span>
		</div>
	</div>
	<div class="col-md-4">
		<div class="admin-stat-card">
			<div>
				<div class="stat-value"><?php echo number_format($total_revenue); ?></div>
				<div class="stat-label">Doanh số thực thu (VNĐ)</div>
			</div>
			<span class="admin-stat-icon icon-success"><i class="fa-solid fa-chart-line"></i></span>
		</div>
	</div>
</div>

<div class="admin-card mb-3">
	<div class="admin-card-header">Biểu đồ doanh thu theo tháng (Năm <?php echo date('Y'); ?>)</div>
	<div class="admin-card-body">
		<canvas class="main-chart" id="revenue-bar-chart" height="200"></canvas>
	</div>
</div>

<div class="admin-card">
	<div class="admin-card-header">
		<span>Chi tiết bán ra &amp; tồn kho</span>
		<div class="admin-search">
			<i class="fa-solid fa-search"></i>
			<input type="search" class="form-control" placeholder="Tìm sản phẩm..." data-admin-table-search="reportProductsTable">
		</div>
	</div>
	<div class="admin-card-body">
		<div class="table-responsive">
			<table class="table table-hover admin-table" id="reportProductsTable">
				<thead>
					<tr>
						<th class="text-center">STT</th>
						<th class="text-center">Mã SP</th>
						<th>Tên sản phẩm</th>
						<th class="text-end">Giá bán</th>
						<th class="text-center">Đã bán</th>
						<th class="text-center">Tồn kho</th>
						<th class="text-center">Trạng thái</th>
					</tr>
				</thead>
				<tbody>
					<?php $stt = 0; foreach ($products_report as $row) { $stt++; ?>
						<tr>
							<td class="text-center fw-semibold"><?php echo $stt; ?></td>
							<td class="text-center">#<?php echo $row->id; ?></td>
							<td><strong><?php echo htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'); ?></strong></td>
							<td class="text-end text-danger fw-semibold"><?php echo number_format($row->price); ?> VNĐ</td>
							<td class="text-center fw-semibold"><?php echo $row->da_ban ? $row->da_ban : 0; ?></td>
							<td class="text-center fw-semibold"><?php echo isset($row->quantity) ? $row->quantity : 0; ?></td>
							<td class="text-center">
								<?php
								$ton_kho = isset($row->quantity) ? $row->quantity : 0;
								if ($ton_kho <= 0) {
									echo '<span class="label label-danger">Hết hàng</span>';
								} elseif ($ton_kho <= 5) {
									echo '<span class="label label-warning">Sắp hết hàng</span>';
								} else {
									echo '<span class="label label-success">An toàn</span>';
								}
								?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
window.addEventListener('DOMContentLoaded', function() {
	var revenueData = <?php echo $chart_data; ?>;
	var barChartData = {
		labels: ["Tháng 1","Tháng 2","Tháng 3","Tháng 4","Tháng 5","Tháng 6","Tháng 7","Tháng 8","Tháng 9","Tháng 10","Tháng 11","Tháng 12"],
		datasets: [{
			fillColor: "rgba(37, 99, 235, 0.2)",
			strokeColor: "rgba(37, 99, 235, 1)",
			pointColor: "rgba(37, 99, 235, 1)",
			pointStrokeColor: "#fff",
			pointHighlightFill: "#fff",
			pointHighlightStroke: "rgba(37, 99, 235, 1)",
			data: revenueData
		}]
	};
	var chartElement = document.getElementById("revenue-bar-chart");
	if (chartElement && typeof Chart !== 'undefined') {
		var ctx = chartElement.getContext("2d");
		window.myBar = new Chart(ctx).Bar(barChartData, {
			responsive: true,
			scaleLineColor: "rgba(0,0,0,.05)",
			scaleGridLineColor: "rgba(0,0,0,.05)",
			scaleFontColor: "#64748b"
		});
	}
});
</script>
