<?php
$cart_asset = site_asset_url('');
?>
<link rel="stylesheet" href="<?php echo $cart_asset; ?>css/cart-luxury.css?v=4">

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 clearpadding jm-cart-lux">
	<div class="jm-cart-lux__inner">

		<?php if (isset($message) && !empty($message)) { ?>
			<div class="jm-cart-flash" role="alert"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
		<?php } ?>

		<?php if ($total_items > 0) { ?>
			<section class="jm-cart-panel" aria-labelledby="jm-cart-title">
				<header class="jm-cart-panel__head">
					<h1 id="jm-cart-title" class="jm-cart-panel__title">Giỏ hàng</h1>
					<p class="jm-cart-panel__count"><?php echo (int) $total_items; ?> sản phẩm</p>
				</header>

				<div class="jm-cart-table-wrap">
					<table class="jm-cart-table">
						<thead>
							<tr>
								<th scope="col" class="jm-cart-col--idx">#</th>
								<th scope="col" class="jm-cart-col--product">Sản phẩm</th>
								<th scope="col" class="jm-cart-col--qty">Số lượng</th>
								<th scope="col" class="jm-cart-col--sub">Thành tiền</th>
								<th scope="col" class="jm-cart-col--act"><span class="sr-only">Xóa</span></th>
							</tr>
						</thead>
						<tbody>
						<?php
							$i = 0;
							$total_price = 0;
							foreach ($carts as $items) {
								$total_price += $items['subtotal'];
								$i++;
								$img = base_url('upload/product/' . (isset($items['options']['image_link']) ? $items['options']['image_link'] : 'default.jpg'));
								$product_url = !empty($items['id']) ? build_product_url((int) $items['id']) : '#';
						?>
							<tr class="jm-cart-row">
								<td class="jm-cart-col--idx" data-label="#"><?php echo $i; ?></td>
								<td class="jm-cart-col--product" data-label="Sản phẩm">
									<div class="jm-cart-product">
										<a href="<?php echo $product_url; ?>" class="jm-cart-product__thumb">
											<img src="<?php echo $img; ?>" alt="">
										</a>
										<div class="jm-cart-product__info">
											<p class="jm-cart-product__name">
												<a href="<?php echo $product_url; ?>"><?php echo product_display_name($items['name']); ?></a>
											</p>
											<?php if (!empty($items['options']['size']) || !empty($items['options']['color'])) { ?>
												<p class="jm-cart-product__opts">
													<?php if (!empty($items['options']['size'])) { ?>
														<span>Size <?php echo htmlspecialchars($items['options']['size'], ENT_QUOTES, 'UTF-8'); ?></span>
													<?php } ?>
													<?php if (!empty($items['options']['color'])) { ?>
														<span>Màu <?php echo htmlspecialchars($items['options']['color'], ENT_QUOTES, 'UTF-8'); ?></span>
													<?php } ?>
												</p>
											<?php } ?>
										</div>
									</div>
								</td>
								<td class="jm-cart-col--qty" data-label="Số lượng">
									<div class="jm-cart-qty">
										<a class="jm-cart-qty__btn" href="<?php echo site_url('gio-hang/update/' . $items['rowid'] . '/sub'); ?>" aria-label="Giảm số lượng">−</a>
										<span class="jm-cart-qty__val"><?php echo (int) $items['qty']; ?></span>
										<a class="jm-cart-qty__btn" href="<?php echo site_url('gio-hang/update/' . $items['rowid'] . '/sum'); ?>" aria-label="Tăng số lượng">+</a>
									</div>
								</td>
								<td class="jm-cart-col--sub" data-label="Thành tiền">
									<span class="jm-cart-line-price"><?php echo number_format($items['subtotal'], 0, ',', '.'); ?> ₫</span>
								</td>
								<td class="jm-cart-col--act" data-label="">
									<a class="jm-cart-remove" href="<?php echo site_url('gio-hang/del/' . $items['rowid']); ?>" title="Xóa sản phẩm" aria-label="Xóa sản phẩm">
										<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
									</a>
								</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div>

				<footer class="jm-cart-summary">
					<div class="jm-cart-summary__row">
						<a class="jm-cart-clear" href="<?php echo site_url('gio-hang/del'); ?>">Xóa toàn bộ</a>
						<div class="jm-cart-summary__total">
							<span class="jm-cart-summary__label">Tổng cộng</span>
							<strong class="jm-cart-summary__amount"><?php echo number_format($total_price, 0, ',', '.'); ?> ₫</strong>
						</div>
					</div>
					<a href="<?php echo build_checkout_url(); ?>" class="jm-cart-checkout">Tiến hành thanh toán</a>
				</footer>
			</section>
		<?php } else { ?>
			<section class="jm-cart-panel jm-cart-panel--empty">
				<header class="jm-cart-panel__head">
					<h1 class="jm-cart-panel__title">Giỏ hàng</h1>
					<p class="jm-cart-panel__count">0 sản phẩm</p>
				</header>
				<div class="jm-cart-empty">
					<img src="<?php echo base_url('upload/cart-empty.png'); ?>" alt="" class="jm-cart-empty__img">
					<p class="jm-cart-empty__text">Giỏ hàng của bạn đang trống</p>
					<a href="<?php echo base_url('ban-chay'); ?>" class="jm-cart-empty__cta">Tiếp tục mua sắm</a>
				</div>
			</section>
		<?php } ?>
	</div><!-- .jm-cart-lux__inner -->
</div>
