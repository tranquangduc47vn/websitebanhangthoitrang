<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item active" aria-current="page">Đơn đặt hàng</li>
		</ol>
	</nav>
</div>

<?php $this->load->helper('export'); admin_export_toolbar('orders'); ?>

<div class="admin-card mb-3">
	<div class="admin-card-body">
		<form class="row g-2 align-items-end" action="<?php echo admin_url('orders'); ?>" method="get">
			<div class="col-md-3">
				<label class="form-label small mb-1" for="ord-q">Tên / SĐT</label>
				<input type="search" name="q" id="ord-q" class="form-control form-control-sm"
					value="<?php echo htmlspecialchars(isset($filter_q) ? $filter_q : '', ENT_QUOTES, 'UTF-8'); ?>"
					placeholder="Tìm khách hàng…">
			</div>
			<div class="col-md-2">
				<label class="form-label small mb-1" for="ord-status">Trạng thái</label>
				<select name="status" id="ord-status" class="form-select form-select-sm">
					<?php
					$statuses = array(
						'' => '— Tất cả —',
						'0' => 'Chờ xử lý',
						'1' => 'Đã xác nhận',
						'2' => 'Đang giao',
						'3' => 'Thành công',
						'4' => 'Đã hủy / Hoàn',
					);
					$cur_status = isset($filter_status) ? (string) $filter_status : '';
					foreach ($statuses as $val => $label) {
						echo '<option value="' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '"'
							. ($cur_status === (string) $val ? ' selected' : '') . '>'
							. htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
					}
					?>
				</select>
			</div>
			<div class="col-md-2">
				<label class="form-label small mb-1" for="ord-payment">Thanh toán</label>
				<select name="payment" id="ord-payment" class="form-select form-select-sm">
					<?php
					$payments = array(
						'' => '— Tất cả —',
						'cod' => 'COD',
						'transfer' => 'Chuyển khoản',
					);
					$cur_payment = isset($filter_payment) ? (string) $filter_payment : '';
					foreach ($payments as $val => $label) {
						echo '<option value="' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '"'
							. ($cur_payment === (string) $val ? ' selected' : '') . '>'
							. htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
					}
					?>
				</select>
			</div>
			<div class="col-md-2">
				<label class="form-label small mb-1" for="ord-date-from">Từ ngày</label>
				<input type="date" name="date_from" id="ord-date-from" class="form-control form-control-sm"
					value="<?php echo htmlspecialchars(isset($filter_date_from) ? $filter_date_from : '', ENT_QUOTES, 'UTF-8'); ?>">
			</div>
			<div class="col-md-2">
				<label class="form-label small mb-1" for="ord-date-to">Đến ngày</label>
				<input type="date" name="date_to" id="ord-date-to" class="form-control form-control-sm"
					value="<?php echo htmlspecialchars(isset($filter_date_to) ? $filter_date_to : '', ENT_QUOTES, 'UTF-8'); ?>">
			</div>
			<div class="col-md-1 d-flex gap-2">
				<button type="submit" class="btn btn-primary btn-sm flex-grow-1" title="Lọc"><i class="fa-solid fa-filter"></i></button>
				<a href="<?php echo admin_url('orders'); ?>" class="btn btn-outline-secondary btn-sm" title="Xóa lọc"><i class="fa-solid fa-xmark"></i></a>
			</div>
		</form>
	</div>
</div>

<div class="admin-card">
	<div class="admin-card-header">
		<span>Danh sách đơn</span>
		<span class="text-muted small"><?php echo number_format(isset($total) ? (int) $total : 0); ?> đơn</span>
	</div>
	<div class="admin-card-body">
		<div class="table-responsive">
			<table class="table table-hover admin-table" id="transactionListTable">
				<thead>
					<tr>
						<th class="text-center" style="width:60px">STT</th>
						<th>Tên khách hàng</th>
						<th>Ngày đặt</th>
						<th>Số ĐT</th>
						<th>Giá tiền</th>
						<th>Hình thức</th>
						<th class="text-center">Trạng thái</th>
						<th class="text-center">Hành động</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$stt = isset($pagination_offset) ? (int) $pagination_offset : 0;
					foreach ($transaction as $value) {
						$stt++;
					?>
						<tr>
							<td class="text-center fw-semibold"><?php echo $stt; ?></td>
							<td><?php echo htmlspecialchars($value->user_name, ENT_QUOTES, 'UTF-8'); ?></td>
							<td><?php echo mdate('%H:%i:%s %d/%m/%Y', $value->created); ?></td>
							<td><?php echo htmlspecialchars($value->user_phone, ENT_QUOTES, 'UTF-8'); ?></td>
							<td><strong class="text-danger"><?php echo number_format($value->amount); ?></strong> VNĐ</td>
							<td>
								<?php if (isset($value->payment) && $value->payment == 'Chuyển khoản') { ?>
									<span class="label label-warning">Chuyển khoản</span>
								<?php } else { ?>
									<span class="label label-default">COD</span>
								<?php } ?>
							</td>
							<td class="text-center">
								<?php switch ($value->status) {
									case '0': echo "<span class='label label-danger'>Đang chờ xử lý</span>"; break;
									case '1': echo "<span class='label label-info'>Đã xác nhận</span>"; break;
									case '2': echo "<span class='label label-primary'>Đang giao hàng</span>"; break;
									case '3': echo "<span class='label label-success'>Thành công</span>"; break;
									case '4': echo "<span class='label label-default'>Đã hủy / Hoàn</span>"; break;
									default: echo "<span class='label label-danger'>Đang chờ</span>"; break;
								} ?>
							</td>
							<td class="text-center text-nowrap">
								<a class="btn btn-sm btn-outline-primary me-1" href="<?php echo admin_url('transaction/detail/' . $value->id); ?>" title="Chi tiết"><i class="fa-solid fa-eye"></i></a>
								<a class="btn btn-sm btn-outline-danger" href="<?php echo admin_url('transaction/del/' . $value->id); ?>" onclick="return confirm('Bạn chắc chắn muốn xóa vĩnh viễn đơn hàng này?');"><i class="fa-solid fa-trash"></i></a>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<?php if (!empty($this->pagination)) { ?>
			<div class="admin-table-footer"><?php echo $this->pagination->create_links(); ?></div>
		<?php } ?>
	</div>
</div>
