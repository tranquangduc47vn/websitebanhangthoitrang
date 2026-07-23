<?php
$site_asset_url = site_asset_url('');
?>
<link rel="stylesheet" href="<?php echo $site_asset_url; ?>css/home-bestsellers.css?v=9">

<div class="row home-collection-banners" style="margin-top: 20px; margin-bottom: 40px;">
    <?php
    if (isset($list_banner) && !empty($list_banner)) {
        $count = 0;
        $total = count($list_banner);

        foreach ($list_banner as $bn) {
            if ($count % 2 == 0) {
                $row_class = ($count >= $total - 2) ? 'row banner-flex-row no-bottom-margin' : 'row banner-flex-row';
                echo '<div class="' . $row_class . '">';
            }
            $count++;
        ?>
            <div class="col-xs-12 col-sm-6 banner-item">
                <a href="<?php echo base_url($bn->link); ?>">
                    <img src="<?php echo base_url('upload/slider/' . $bn->image_link); ?>" alt="<?php echo htmlspecialchars($bn->name, ENT_QUOTES, 'UTF-8'); ?>">
                </a>
            </div>
        <?php
            if ($count % 2 == 0 || $count == $total) {
                echo '</div>';
            }
        }
    } else {
        echo '<p class="text-center" style="width:100%; padding: 20px; color: red; font-weight: bold;">Hệ thống chưa tìm thấy dữ liệu trong bảng banner danh mục. Hãy kiểm tra lại kết nối Database!</p>';
    }
    ?>
</div>

<?php
if (!function_exists('render_bestseller_card')) {
    function render_bestseller_card($value)
    {
        $product_link = build_product_url($value);
        $name = product_display_name($value->name);
        $img = base_url('upload/product/' . $value->image_link);
        $in_stock = product_is_in_stock($value);
        ?>
        <li class="jm-bs-card<?php echo $in_stock ? '' : ' jm-bs-card--out-of-stock'; ?>">
            <div class="jm-bs-card__media">
                <?php if (product_show_discount_badge($value)) { ?>
                    <?php echo product_discount_badge_html($value, 'jm-bs-card__badge-sale'); ?>
                <?php } ?>
                <?php if (!$in_stock) { echo product_out_of_stock_badge_html('jm-bs-card__badge-oos'); } ?>
                <a href="<?php echo $product_link; ?>" class="jm-bs-card__img-link">
                    <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
                </a>
                <?php if ($in_stock) { ?>
                <a href="<?php echo site_url('gio-hang/them/' . (int) $value->id); ?>" class="jm-bs-card__cart-btn">
                    Thêm giỏ hàng
                </a>
                <?php } else { ?>
                <span class="jm-bs-card__cart-btn jm-bs-card__cart-btn--disabled">Hết hàng</span>
                <?php } ?>
            </div>
            <div class="jm-bs-card__body">
                <h3 class="jm-bs-card__name">
                    <a href="<?php echo $product_link; ?>"><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></a>
                </h3>
                <div class="jm-bs-card__prices">
                    <?php if ($value->discount > 0) {
                        $new_price = $value->price - $value->discount;
                        ?>
                        <span class="jm-bs-card__price"><?php echo number_format($new_price); ?>đ</span>
                        <span class="jm-bs-card__price-old"><?php echo number_format($value->price); ?>đ</span>
                    <?php } else { ?>
                        <span class="jm-bs-card__price"><?php echo number_format($value->price); ?>đ</span>
                    <?php } ?>
                </div>
                <span class="jm-bs-card__sold">
                    <i class="fa-solid fa-fire-flame-curved" aria-hidden="true"></i>
                    <?php echo number_format((int) $value->buyed); ?> đã bán
                </span>
            </div>
        </li>
        <?php
    }
}
?>

<section class="jm-bestsellers-lux" id="phan-ban-chay" aria-labelledby="jm-bestsellers-title">
    <header class="jm-bestsellers-lux__head">
        <p class="jm-bestsellers-lux__eyebrow"><?php echo htmlspecialchars(shop_name(), ENT_QUOTES, 'UTF-8'); ?></p>
        <h2 class="jm-bestsellers-lux__title" id="jm-bestsellers-title">Sản phẩm bán chạy</h2>
        <div class="jm-bestsellers-lux__rule" aria-hidden="true"></div>
        <p class="jm-bestsellers-lux__sub">Những thiết kế được khách yêu thích nhất — cập nhật theo lượt bán thực tế.</p>
        <a href="<?php echo base_url('ban-chay'); ?>" class="jm-bestsellers-lux__cta">
            Xem tất cả <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
        </a>
    </header>

    <ul class="jm-bestsellers-lux__grid">
        <?php
        if (!empty($hot_product)) {
            foreach ($hot_product as $value) {
                render_bestseller_card($value);
            }
        } else {
            echo '<li class="jm-bestsellers-lux__empty">Chưa có sản phẩm bán chạy.</li>';
        }
        ?>
    </ul>
</section>

<script src="<?php echo site_asset_url('js/custom.js'); ?>"></script>
