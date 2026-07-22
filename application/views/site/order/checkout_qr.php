<?php
$checkout_asset = site_asset_url('');
$amount_fmt = number_format((int) $transaction->amount, 0, ',', '.') . ' ₫';
?>
<link rel="stylesheet" href="<?php echo $checkout_asset; ?>css/checkout-luxury.css?v=5">

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 clearpadding jm-checkout-lux">
	<div class="jm-checkout-lux__inner">

		<section class="jm-checkout-panel jm-checkout-qr" aria-labelledby="jm-checkout-qr-title">
			<header class="jm-checkout-panel__head">
				<h1 id="jm-checkout-qr-title" class="jm-checkout-panel__title">Thanh toán chuyển khoản</h1>
				<p class="jm-checkout-qr__meta">
					Đơn hàng <strong>#<?php echo (int) $transaction->id; ?></strong>
					<span class="jm-checkout-qr__amount"><?php echo $amount_fmt; ?></span>
				</p>
			</header>

			<div class="jm-checkout-qr__body">
				<p class="jm-checkout-qr__lead">Mở app ngân hàng và chọn <strong>Quét mã QR</strong> để thanh toán.</p>

				<div class="jm-checkout-qr__frame">
					<img src="<?php echo htmlspecialchars($qr_image_url, ENT_QUOTES, 'UTF-8'); ?>" alt="Mã QR VietQR đơn hàng #<?php echo (int) $transaction->id; ?>" width="280" height="280">
				</div>

				<div class="jm-checkout-qr__notes">
					<p class="jm-checkout-qr__notes-title">Lưu ý</p>
					<ul>
						<li>Số tiền và nội dung chuyển khoản đã được điền sẵn — vui lòng không chỉnh sửa.</li>
						<li>Sau khi chuyển khoản, đơn hàng sẽ được xác nhận trong thời gian sớm nhất.</li>
					</ul>
				</div>

				<div class="jm-checkout-qr__actions">
					<a href="<?php echo base_url('user'); ?>" class="jm-checkout-submit jm-checkout-qr__btn">Xem lịch sử đơn hàng</a>
					<a href="<?php echo base_url(); ?>" class="jm-checkout-qr__link-home">Tiếp tục mua sắm</a>
				</div>
			</div>
		</section>
	</div>
</div>
