<?php
$site_asset_url = site_asset_url('');
?>
<link rel="stylesheet" href="<?php echo $site_asset_url; ?>css/home-bestsellers.css?v=4">

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
    function render_bestseller_card($value, $rank = 0)
    {
        $product_link = build_product_url($value);
        $rank = (int) $rank;
        $rank_class = ($rank >= 1 && $rank <= 3) ? ' jm-bs-card--rank-' . $rank : '';
        $name = product_display_name($value->name);
        $img = base_url('upload/product/' . $value->image_link);
        ?>
        <li class="jm-bs-card<?php echo $rank_class; ?>">
            <?php if ($rank > 0) { ?>
                <span class="jm-bs-card__rank" aria-hidden="true">#<?php echo $rank; ?></span>
            <?php } ?>
            <div class="jm-bs-card__media">
                <?php if ($value->discount > 0 && $value->price > 0) {
                    $percent = min(99, (int) round(($value->discount / $value->price) * 100));
                    if ($percent >= 1) {
                    ?>
                    <span class="jm-bs-card__badge-sale">−<?php echo $percent; ?>%</span>
                <?php }
                } ?>
                <a href="<?php echo $product_link; ?>" class="jm-bs-card__img-link">
                    <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
                </a>
                <a href="<?php echo site_url('gio-hang/them/' . (int) $value->id); ?>" class="jm-bs-card__cart-btn">
                    Thêm giỏ hàng
                </a>
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
        <p class="jm-bestsellers-lux__eyebrow">JM Dress Design</p>
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
            $rank = 0;
            foreach ($hot_product as $value) {
                $rank++;
                render_bestseller_card($value, $rank);
            }
        } else {
            echo '<li class="jm-bestsellers-lux__empty">Chưa có sản phẩm bán chạy.</li>';
        }
        ?>
    </ul>
</section>

<script src="<?php echo site_asset_url('js/custom.js'); ?>"></script>
