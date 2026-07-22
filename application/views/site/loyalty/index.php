<?php
$this->load->helper('loyalty');
$asset = site_asset_url('');
?>
<link rel="stylesheet" href="<?php echo $asset; ?>css/account-luxury.css?v=3">

<div class="col-xs-12 jm-account-lux">
	<div class="jm-account-lux__inner">
		<section class="jm-account-panel">
			<div class="jm-account-panel__head">
				<h1 class="jm-account-panel__title">Chính sách tích điểm</h1>
				<p class="jm-account-panel__sub">JM Dress Design · Khách mua nhiều được ưu đãi hơn</p>
			</div>
			<div class="jm-account-panel__body jm-loyalty-policy">
				<h2 class="jm-loyalty-policy__h">Cách tích điểm</h2>
				<ul>
					<li>Mỗi <strong>10.000 ₫</strong> thanh toán thực tế (sau voucher) khi đơn <strong>hoàn thành</strong> được <strong>1 điểm</strong> cơ bản.</li>
					<li>Hạng cao hơn nhận thêm điểm: Bạc ×1,1 · Vàng ×1,25 · VIP ×1,5.</li>
					<li>Điểm gắn với tài khoản đăng nhập; vui lòng đăng nhập trước khi đặt hàng để cộng điểm.</li>
				</ul>

				<h2 class="jm-loyalty-policy__h">Hạng thành viên</h2>
				<table class="jm-loyalty-tier-table">
					<thead>
						<tr><th>Hạng</th><th>Điều kiện (một trong hai)</th></tr>
					</thead>
					<tbody>
						<tr><td><?php echo loyalty_tier_label('member'); ?></td><td>Mặc định khi đăng ký</td></tr>
						<tr><td><?php echo loyalty_tier_label('silver'); ?></td><td>≥ 3 đơn hoàn thành hoặc tổng chi tiêu ≥ 5.000.000 ₫</td></tr>
						<tr><td><?php echo loyalty_tier_label('gold'); ?></td><td>≥ 10 đơn hoàn thành hoặc tổng chi tiêu ≥ 20.000.000 ₫</td></tr>
						<tr><td><?php echo loyalty_tier_label('vip'); ?></td><td>≥ 25 đơn hoàn thành hoặc tổng chi tiêu ≥ 50.000.000 ₫</td></tr>
					</tbody>
				</table>

				<h2 class="jm-loyalty-policy__h">Voucher</h2>
				<p>Nhập mã tại bước thanh toán. Một số mã chỉ dành cho hạng Bạc trở lên hoặc khách được chỉ định. Xem mã khả dụng trong trang <a href="<?php echo base_url('user'); ?>">Tài khoản</a> sau khi đăng nhập.</p>

				<p class="jm-loyalty-policy__note">Điểm và hạng có thể được điều chỉnh khi đơn hàng bị hủy sau khi đã hoàn thành.</p>
			</div>
		</section>
	</div>
</div>
