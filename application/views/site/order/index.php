<?php
$checkout_asset = site_asset_url('');
$total_fmt = number_format((int) $total_amount, 0, ',', '.') . ' ₫';
$cv = (isset($checkout_voucher) && is_array($checkout_voucher)) ? $checkout_voucher : null;
$has_applied = ($cv && (int) $cv['discount'] > 0 && (int) $cv['cart_subtotal'] === (int) $total_amount);
$display_final = $has_applied ? (int) $cv['final'] : (int) $total_amount;
$display_final_fmt = number_format($display_final, 0, ',', '.') . ' ₫';
$voucher_init_code = $has_applied ? $cv['code'] : set_value('voucher_code');
?>
<link rel="stylesheet" href="<?php echo $checkout_asset; ?>css/checkout-luxury.css?v=5">

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 clearpadding jm-checkout-lux">
	<div class="jm-checkout-lux__inner">

		<?php if (isset($message) && !empty($message)) { ?>
			<div class="jm-checkout-flash" role="alert"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
		<?php } ?>

		<section class="jm-checkout-panel" aria-labelledby="jm-checkout-title">
			<header class="jm-checkout-panel__head">
				<h1 id="jm-checkout-title" class="jm-checkout-panel__title">Thông tin thanh toán</h1>
				<?php if (!empty($user)) { ?>
					<p class="jm-checkout-panel__loyalty">Tích điểm khi đơn hoàn thành · <a href="<?php echo base_url('tich-diem'); ?>">Chính sách</a></p>
				<?php } else { ?>
					<p class="jm-checkout-panel__loyalty"><a href="<?php echo base_url('dang-nhap'); ?>">Đăng nhập</a> để dùng voucher và tích điểm.</p>
				<?php } ?>
			</header>

			<form class="jm-checkout-form" id="jmCheckoutForm" enctype="multipart/form-data" method="post" novalidate>
				<div class="jm-checkout-field">
					<label for="jm-checkout-name">Họ và tên</label>
					<input id="jm-checkout-name" type="text" name="name" class="form-control" value="<?php echo (!empty($user)) ? htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8') : ''; ?>" required>
					<?php echo form_error('name', '<span class="jm-checkout-error">', '</span>'); ?>
				</div>

				<div class="jm-checkout-field">
					<label for="jm-checkout-email">Email</label>
					<input id="jm-checkout-email" name="email" type="email" class="form-control" value="<?php echo (!empty($user)) ? htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8') : ''; ?>" required>
					<?php echo form_error('email', '<span class="jm-checkout-error">', '</span>'); ?>
				</div>

				<div class="jm-checkout-field">
					<label for="jm-checkout-phone">Số điện thoại</label>
					<input id="jm-checkout-phone" name="phone" type="text" class="form-control" value="<?php echo (!empty($user)) ? htmlspecialchars($user->phone, ENT_QUOTES, 'UTF-8') : ''; ?>" required>
					<?php echo form_error('phone', '<span class="jm-checkout-error">', '</span>'); ?>
				</div>

				<div class="jm-checkout-field">
					<label for="jm-checkout-address">Địa chỉ</label>
					<input id="jm-checkout-address" name="address" type="text" class="form-control" value="<?php echo (!empty($user)) ? htmlspecialchars($user->address, ENT_QUOTES, 'UTF-8') : ''; ?>" required>
					<?php echo form_error('address', '<span class="jm-checkout-error">', '</span>'); ?>
				</div>

				<div class="jm-checkout-field">
					<label for="jm-checkout-message">Lời nhắn</label>
					<textarea id="jm-checkout-message" name="message" class="form-control" rows="4"><?php echo set_value('message'); ?></textarea>
					<?php echo form_error('message', '<span class="jm-checkout-error">', '</span>'); ?>
				</div>

				<div class="jm-checkout-field">
					<label for="jm-checkout-voucher">Mã voucher</label>
					<div class="jm-checkout-voucher-row">
						<input id="jm-checkout-voucher" type="text" name="voucher_code" class="form-control text-uppercase" autocomplete="off"
							placeholder="<?php echo !empty($user) ? 'Nhập mã giảm giá' : 'Cần đăng nhập'; ?>"
							<?php echo empty($user) ? 'disabled' : ''; ?>
							value="<?php echo htmlspecialchars($voucher_init_code, ENT_QUOTES, 'UTF-8'); ?>">
						<button type="button" class="jm-checkout-voucher-btn" id="jmCheckoutVoucherBtn" <?php echo empty($user) ? 'disabled' : ''; ?>>Xác nhận</button>
					</div>
					<p class="jm-checkout-voucher-msg" id="jmCheckoutVoucherMsg" role="status" aria-live="polite" hidden></p>
				</div>

				<div class="jm-checkout-pay">
					<p class="jm-checkout-pay__title">Phương thức thanh toán</p>
					<label class="jm-checkout-pay__option">
						<input type="radio" name="payment" value="COD" checked>
						<span class="jm-checkout-pay__text">
							Thanh toán khi nhận hàng (COD)
							<span class="jm-checkout-pay__hint">Thanh toán tiền mặt khi nhận hàng</span>
						</span>
					</label>
					<label class="jm-checkout-pay__option">
						<input type="radio" name="payment" value="Chuyển khoản">
						<span class="jm-checkout-pay__text">
							Chuyển khoản ngân hàng (VietQR)
							<span class="jm-checkout-pay__hint">Quét mã QR sau khi xác nhận đơn</span>
						</span>
					</label>
				</div>

				<div class="jm-checkout-summary" id="jmCheckoutSummary" aria-live="polite">
					<div class="jm-checkout-summary__row jm-checkout-summary__row--discount" id="jmCheckoutDiscountRow" <?php echo $has_applied ? '' : 'hidden'; ?>>
						<span>Giảm voucher</span>
						<strong id="jmCheckoutDiscountAmt">−<?php echo $has_applied ? number_format((int) $cv['discount'], 0, ',', '.') . ' ₫' : '0 ₫'; ?></strong>
					</div>
					<div class="jm-checkout-summary__total">
						<span class="jm-checkout-summary__label">Tổng thanh toán</span>
						<div class="jm-checkout-summary__amounts">
							<span class="jm-checkout-summary__old" id="jmCheckoutOldTotal" <?php echo $has_applied ? '' : 'hidden'; ?>><?php echo $total_fmt; ?></span>
							<strong class="jm-checkout-summary__new" id="jmCheckoutNewTotal"><?php echo $has_applied ? $display_final_fmt : $total_fmt; ?></strong>
						</div>
					</div>
				</div>

				<button type="submit" class="jm-checkout-submit">Xác nhận đặt hàng</button>
			</form>
		</section>
	</div>
</div>

<script>
window.jmCheckoutVoucher = <?php echo json_encode(array(
	'applyUrl' => isset($checkout_apply_url) ? $checkout_apply_url : '',
	'subtotal' => (int) $total_amount,
	'subtotalFmt' => $total_fmt,
	'loggedIn' => !empty($user),
	'applied' => $has_applied ? array(
		'discount' => (int) $cv['discount'],
		'final' => (int) $cv['final'],
		'discountFmt' => '−' . number_format((int) $cv['discount'], 0, ',', '.') . ' ₫',
		'finalFmt' => $display_final_fmt,
	) : null,
), JSON_UNESCAPED_UNICODE); ?>;
</script>
<script src="<?php echo $checkout_asset; ?>js/checkout-voucher.js?v=2"></script>
