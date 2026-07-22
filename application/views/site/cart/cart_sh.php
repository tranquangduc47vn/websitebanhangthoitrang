<?php
$total_items = isset($total_items) ? (int) $total_items : 0;
$carts = (isset($carts) && is_array($carts)) ? $carts : array();
?>
<button type="button" class="jm-icon-btn jm-icon-toggle" title="Giỏ hàng" aria-label="Giỏ hàng" aria-expanded="false" aria-haspopup="true">
	<svg class="jm-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
		<path d="M6 7h15l-1.5 9h-12z"/>
		<path d="M6 7l-1-3H2"/>
		<circle cx="9" cy="20" r="1"/>
		<circle cx="18" cy="20" r="1"/>
	</svg>
	<?php if ($total_items > 0) { ?>
		<span class="jm-icon-dot"><?php echo $total_items > 99 ? '99+' : $total_items; ?></span>
	<?php } ?>
</button>
<div class="jm-icon-popover jm-cart-popover">
	<?php if ($total_items > 0) { ?>
		<ul class="jm-cart-mini-list">
			<?php foreach ($carts as $items) { ?>
				<li>
					<img src="<?php echo base_url('upload/product/' . ($items['options']['image_link'] ?? 'no-image.jpg')); ?>" alt="">
					<div>
						<span class="jm-cart-mini-name"><?php echo htmlspecialchars(product_display_name($items['name']), ENT_QUOTES, 'UTF-8'); ?></span>
						<span class="jm-cart-mini-meta"><?php echo (int) $items['qty']; ?> × <?php echo number_format($items['subtotal']); ?>đ</span>
					</div>
				</li>
			<?php } ?>
		</ul>
		<a href="<?php echo build_cart_url(); ?>" class="jm-cart-mini-link">Xem giỏ hàng</a>
	<?php } else { ?>
		<p class="jm-cart-mini-empty">Giỏ hàng trống</p>
		<a href="<?php echo base_url('moi'); ?>" class="jm-cart-mini-link">Mua sắm</a>
	<?php } ?>
</div>
