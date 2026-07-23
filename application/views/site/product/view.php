<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 clearpaddingr jm-pdp-lux">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 clearpadding">
        
        <link rel="stylesheet" href="<?php echo site_asset_url('css/style-detail.css'); ?>?v=14" type="text/css">
        <link rel="stylesheet" href="<?php echo site_asset_url('css/stock-notice-modal.css'); ?>?v=1" type="text/css">

        <script src="<?php echo site_asset_url('js/product-detail.js'); ?>?v=3" type="text/javascript"></script>

        <script type="text/javascript">
        $(document).ready(function() {
            var alreadyRated = <?php echo !empty($user_already_rated) ? 'true' : 'false'; ?>;
            var myScore = <?php echo (!empty($user_review) && isset($user_review->stars)) ? (int) $user_review->stars : 0; ?>;

            $('.raty_big_summary').raty({
                score: function() {
                    return $(this).attr('data-score');
                },
                half: true,
                readOnly: true
            });

            function showReviewThankYou(score, isUpdate) {
                var title = isUpdate ? 'Cảm ơn quý khách!' : 'Cảm ơn quý khách!';
                var text = isUpdate
                    ? 'Đánh giá của quý khách đã được cập nhật thành ' + score + ' sao. Ý kiến quý khách giúp chúng tôi cải thiện chất lượng sản phẩm và dịch vụ.'
                    : 'Cảm ơn quý khách đã dành thời gian đánh giá sản phẩm. Ý kiến của quý khách rất quý giá và giúp chúng tôi phục vụ tốt hơn mỗi ngày.';
                $('#jmReviewThankTitle').text(title);
                $('#jmReviewThankText').text(text);
                $('#jmReviewThankScore').text(score + '/5 sao');
                if ($('#jmReviewThankStars').data('raty')) {
                    $('#jmReviewThankStars').raty('score', score);
                } else {
                    $('#jmReviewThankStars').raty({
                        score: score,
                        readOnly: true,
                        half: false
                    });
                }
                $('#jmReviewThankModal').modal('show');
            }

            $('#jmReviewThankModal').on('hidden.bs.modal', function() {
                window.location.reload();
            });

            $('.raty_detailt').raty({
                score: function() {
                    if (alreadyRated && myScore > 0) {
                        return myScore;
                    }
                    return $(this).attr('data-score');
                },
                half: false,
                readOnly: false,
                click: function(score, evt) {
                    $.ajax({
                        url: '<?php echo base_url('product/raty'); ?>',
                        type: 'POST',
                        data: {'id': '<?php echo $product->id; ?>', 'score': score},
                        dataType: 'json',
                        success: function(data) {
                            if (data.complete) {
                                if (data.updated || !alreadyRated) {
                                    showReviewThankYou(score, !!data.updated);
                                } else {
                                    alert(data.msg);
                                }
                                return;
                            }
                            alert(data.msg);
                        },
                        error: function() {
                            alert('Không gửi được đánh giá, vui lòng thử lại.');
                        }
                    });
                }
            });

            $('#btn-raty-undo').on('click', function(e) {
                e.preventDefault();
                if (!confirm('Bạn muốn hoàn tác đánh giá này?')) {
                    return;
                }
                $.ajax({
                    url: '<?php echo base_url('product/raty-undo'); ?>',
                    type: 'POST',
                    data: {'id': '<?php echo $product->id; ?>'},
                    dataType: 'json',
                    success: function(data) {
                        if (data.complete) {
                            window.location.reload();
                            return;
                        }
                        alert(data.msg);
                    }
                });
            });
        });
        </script>

        <div class="panel panel-info jm-pdp-panel" style="margin-bottom: 15px; border: none; box-shadow: none;">
            <div class="panel-body jm-pdp-panel-body" style="padding: 0;">
                
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-5 jm-pdp-gallery-col">
                    <div class="jm-gallery-container">
                        <div class="jm-thumb-vertical-box">
                            <ul id="thumblist" class="jm-thumb-vertical">
                                <li>
                                    <?php $main_img = base_url('upload/product/' . $product->image_link); ?>
                                    <a class="zoomThumbActive" href="javascript:void(0);"
                                       data-small="<?php echo htmlspecialchars($main_img, ENT_QUOTES, 'UTF-8'); ?>"
                                       data-large="<?php echo htmlspecialchars($main_img, ENT_QUOTES, 'UTF-8'); ?>">
                                        <img src="<?php echo $main_img; ?>" alt="thumbnail">
                                    </a>
                                </li>
                                <?php if(is_array($image_list)): ?>
                                    <?php foreach ($image_list as $value) {
                                        $thumb_src = base_url('upload/product/' . $value);
                                    ?>
                                        <li>
                                            <a href="javascript:void(0);"
                                               data-small="<?php echo htmlspecialchars($thumb_src, ENT_QUOTES, 'UTF-8'); ?>"
                                               data-large="<?php echo htmlspecialchars($thumb_src, ENT_QUOTES, 'UTF-8'); ?>">
                                                <img src="<?php echo $thumb_src; ?>" alt="thumbnail-gallery">
                                            </a>
                                        </li>
                                    <?php } ?> 
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="jm-main-image-box">
                            <?php $main_img = base_url('upload/product/' . $product->image_link); ?>
                            <div class="jm-pdp-main-stage" id="jm-pdp-main-stage">
                                <img src="<?php echo $main_img; ?>" alt="<?php echo htmlspecialchars(product_display_name($product->name), ENT_QUOTES, 'UTF-8'); ?>" class="jm-pdp-main-img" id="jm-pdp-main-img" width="340" height="440">
                            </div>
                        </div>
                        <div style="clear: both;"></div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-7 jm-pdp-info-col">
                    <div class="jm-pdp-info-sticky">
                    <h1 class="style2-product-title"><?php echo product_display_name($product->name); ?></h1>
                    <p class="style2-sku">Mã sản phẩm: <strong><?php echo $product->id; ?></strong></p>

                    <?php
                    $pdp_price_fmt = function ($amount) {
                        return number_format((float) $amount, 0, ',', '.') . ' ₫';
                    };
                    if ($product->discount > 0) {
                        $price_new = $product->price - $product->discount;
                    }
                    ?>
                    <div class="style2-price-block">
                        <?php if ($product->discount > 0) { ?>
                            <?php if (product_show_discount_badge($product)) { ?>
                                <p class="style2-price-badge-line"><?php echo product_discount_badge_html($product, 'jm-badge-discount jm-pdp-sale-badge'); ?></p>
                            <?php } ?>
                            <p class="style2-price-old-line"><del><?php echo $pdp_price_fmt($product->price); ?></del></p>
                            <p class="style2-price-current"><?php echo $pdp_price_fmt($price_new); ?></p>
                        <?php } else { ?>
                            <p class="style2-price-current"><?php echo $pdp_price_fmt($product->price); ?></p>
                        <?php } ?>
                    </div>

                    <div class="style2-product-desc"><?php echo $product->content; ?></div>
                    <hr class="style2-divider">

                    <div class="style2-meta-box">
                        <p class="style2-meta-item"><strong>Size hiện có:</strong> <?php echo !empty($product->size) ? $product->size : 'Freesize'; ?></p>
                        
                        <p class="style2-meta-item"><strong>Số lượt xem:</strong> <?php echo number_format($product->view); ?></p>
                        
                        <?php 
                        $variant_stock_map = isset($variant_stock_map) ? $variant_stock_map : array();
                        $has_any_stock = false;
                        foreach ($variant_stock_map as $variant_row) {
                            if (!empty($variant_row['stock'])) {
                                $has_any_stock = true;
                                break;
                            }
                        }
                        if (empty($variant_stock_map)) {
                            $has_any_stock = isset($product->quantity) ? ((int) $product->quantity > 0) : false;
                        }
                        $raty_tb = ($product->rate_count > 0) ? ($product->rate_total / $product->rate_count) : 0;
                        $my_review_stars = (!empty($user_review) && isset($user_review->stars)) ? (int) $user_review->stars : 0;
                        ?>
                        
                        <p class="style2-meta-item style2-rating">
                            <strong>Đánh giá:</strong> 
                            <span class="raty_detailt" id="<?php echo $product->id; ?>" data-score="<?php echo round($raty_tb, 1); ?>" style="display:inline-block; vertical-align: middle; margin-right: 5px;"></span>
                            (<?php echo round($raty_tb, 1); ?>/5) | <span class="rate_count"><?php echo $product->rate_count; ?></span> đánh giá
                            <?php if (!empty($user_already_rated) && $my_review_stars > 0) { ?>
                                <span class="jm-my-review-badge">Bạn: <?php echo $my_review_stars; ?> sao</span>
                            <?php } ?>
                        </p>
                    </div>
                    
                    <form action="<?php echo site_url('gio-hang/them/' . (int) $product->id); ?>" method="post" id="form-add-to-cart">
                        
                        <div class="style2-attr-group">
                            <label class="style2-attr-title">Màu sắc:</label>
                            <ul class="style2-attr-list-rect" id="color-select">
                                <?php 
                                $first_color = '';
                                if(!empty($product_colors)) {
                                    foreach ($product_colors as $key => $color) {
                                        $color = trim($color);
                                        if($key === 0) $first_color = $color;

                                        $bg_hex = '#ffffff'; 
                                        $lower_color = mb_strtolower($color, 'UTF-8');

                                        if (mb_strpos($lower_color, 'đen') !== false || mb_strpos($lower_color, 'black') !== false) { $bg_hex = '#000000'; }
                                        elseif (mb_strpos($lower_color, 'trắng') !== false || mb_strpos($lower_color, 'white') !== false) { $bg_hex = '#ffffff'; }
                                        elseif (mb_strpos($lower_color, 'đỏ') !== false || mb_strpos($lower_color, 'red') !== false) { $bg_hex = '#ff0000'; }
                                        elseif (mb_strpos($lower_color, 'vàng') !== false || mb_strpos($lower_color, 'yellow') !== false) { $bg_hex = '#ffcc00'; }
                                        elseif (mb_strpos($lower_color, 'cam') !== false || mb_strpos($lower_color, 'orange') !== false) { $bg_hex = '#ff781f'; }
                                        elseif (mb_strpos($lower_color, 'xanh dương') !== false || mb_strpos($lower_color, 'xanh lam') !== false || mb_strpos($lower_color, 'blue') !== false) { $bg_hex = '#0066cc'; }
                                        elseif (mb_strpos($lower_color, 'xanh lá') !== false || mb_strpos($lower_color, 'green') !== false) { $bg_hex = '#00a651'; }
                                        elseif (mb_strpos($lower_color, 'hồng') !== false || mb_strpos($lower_color, 'pink') !== false) { $bg_hex = '#ffb6c1'; }
                                        elseif (mb_strpos($lower_color, 'xám') !== false || mb_strpos($lower_color, 'ghi') !== false || mb_strpos($lower_color, 'gray') !== false) { $bg_hex = '#808080'; }
                                        elseif (mb_strpos($lower_color, 'nâu') !== false || mb_strpos($lower_color, 'brown') !== false) { $bg_hex = '#8b5a2b'; }
                                        elseif (mb_strpos($lower_color, 'kem') !== false || mb_strpos($lower_color, 'be') !== false || mb_strpos($lower_color, 'beige') !== false || mb_strpos($lower_color, 'cream') !== false) { $bg_hex = '#f5f5dc'; }
                                        else { $bg_hex = '#e0e0e0'; }
                                        ?>
                                        <li class="style2-rect-box <?php echo ($key === 0) ? 'active' : ''; ?>" 
                                            data-value="<?php echo $color; ?>" 
                                            style="background-color: <?php echo $bg_hex; ?>;" 
                                            title="<?php echo $color; ?>">
                                            <?php echo $color; ?>
                                        </li>
                                    <?php } 
                                } else {
                                    $first_color = 'Mặc định';
                                    echo '<li class="style2-rect-box active" data-value="Mặc định" style="background-color: #ffffff;">Mặc định</li>';
                                }
                                ?>
                            </ul>
                            <input type="hidden" name="color" id="input-color" value="<?php echo $first_color; ?>">
                        </div>

                        <div class="style2-attr-group">
                            <label class="style2-attr-title">Kích cỡ:</label>
                            <ul class="style2-attr-list-square" id="size-select">
                                <?php 
                                $first_size = '';
                                if(!empty($product_sizes)) {
                                    foreach ($product_sizes as $key => $size) {
                                        $size = trim($size);
                                        if($key === 0) $first_size = $size;
                                        ?>
                                        <li class="style2-square-box <?php echo ($key === 0) ? 'active' : ''; ?>" data-value="<?php echo $size; ?>"><?php echo $size; ?></li>
                                        <?php
                                    }
                                } else {
                                    $first_size = 'Freesize';
                                    echo '<li class="style2-square-box active" data-value="Freesize">Freesize</li>';
                                }
                                ?>
                            </ul>
                            <input type="hidden" name="size" id="input-size" value="<?php echo $first_size; ?>">
                        </div>

                        <?php if ($has_any_stock): ?>
                            <div class="style2-attr-group" style="margin-bottom: 35px !important;">
                                <label class="style2-attr-title">Số lượng:</label>
                                <div class="style2-qty-wrapper">
                                    <button type="button" class="style2-qty-btn" id="btn-sub">-</button>
                                    <input type="text" name="qty" id="input-qty" class="style2-qty-input" value="1" readonly>
                                    <button type="button" class="style2-qty-btn" id="btn-sum">+</button>
                                </div>
                            </div>
                            
                            <input type="hidden" name="id" value="<?php echo $product->id; ?>">
                            <input type="hidden" name="variant_id" id="input-variant-id" value="">
                            
                            <div class="style2-action-buttons">
                                <button type="submit" class="style2-btn-addcart" id="btn-add-to-cart">
                                    <span class="glyphicon glyphicon-shopping-cart" style="margin-right: 8px;"></span> THÊM VÀO GIỎ HÀNG
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="style2-action-buttons" style="margin-top: 30px;">
                                <button type="button" class="style2-btn-addcart" disabled style="background: #ccc !important; color: #666 !important; cursor: not-allowed !important; max-width: 320px !important;">
                                    ❌ TẠM HẾT HÀNG
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>

                    <script type="text/javascript">
                        $(document).ready(function() {
                            var variantStocks = <?php echo json_encode($variant_stock_map, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
                            var legacyProductStock = <?php echo empty($variant_stock_map) ? (int) (isset($product->quantity) ? $product->quantity : 0) : 0; ?>;
                            var productName = <?php echo json_encode($product->name, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

                            function variantKey(color, size) {
                                return String(color).trim() + '||' + String(size).trim();
                            }

                            function resolveVariant(color, size) {
                                var key = variantKey(color, size);
                                if (variantStocks[key]) {
                                    return variantStocks[key];
                                }
                                if (legacyProductStock > 0) {
                                    return { id: 0, stock: legacyProductStock, color: color, size: size };
                                }
                                return { id: 0, stock: 0, color: color, size: size };
                            }

                            function showStockNotice(color, size, stock) {
                                if (window.QD && window.QD.showStockNotice) {
                                    window.QD.showStockNotice({
                                        product_name: productName,
                                        color: color,
                                        size: size,
                                        stock: stock
                                    });
                                }
                            }

                            var qtyInput = $('#input-qty');
                            var addBtn = $('#btn-add-to-cart');
                            var maxStock = 0;

                            function refreshVariantStock() {
                                var color = $('#input-color').val();
                                var size = $('#input-size').val();
                                var variant = resolveVariant(color, size);
                                maxStock = parseInt(variant.stock, 10) || 0;
                                $('#input-variant-id').val(variant.id || '');

                                var currentVal = parseInt(qtyInput.val(), 10);
                                if (isNaN(currentVal) || currentVal < 1) {
                                    currentVal = 1;
                                }
                                if (maxStock > 0 && currentVal > maxStock) {
                                    qtyInput.val(maxStock);
                                } else if (maxStock <= 0) {
                                    qtyInput.val(1);
                                }

                                if (addBtn.length) {
                                    addBtn.prop('disabled', maxStock <= 0);
                                }
                            }

                            $('#color-select .style2-rect-box').click(function() {
                                $('#color-select .style2-rect-box').removeClass('active');
                                $(this).addClass('active');
                                $('#input-color').val($(this).attr('data-value'));
                                refreshVariantStock();
                            });

                            $('#size-select .style2-square-box').click(function() {
                                $('#size-select .style2-square-box').removeClass('active');
                                $(this).addClass('active');
                                $('#input-size').val($(this).attr('data-value'));
                                refreshVariantStock();
                            });

                            $('#btn-sum').click(function() {
                                var color = $('#input-color').val();
                                var size = $('#input-size').val();
                                var currentVal = parseInt(qtyInput.val(), 10);
                                if (!isNaN(currentVal) && currentVal < maxStock) {
                                    qtyInput.val(currentVal + 1);
                                } else {
                                    showStockNotice(color, size, maxStock);
                                }
                            });
                            $('#btn-sub').click(function() {
                                var currentVal = parseInt(qtyInput.val(), 10);
                                if (!isNaN(currentVal) && currentVal > 1) { qtyInput.val(currentVal - 1); }
                            });

                            $('#form-add-to-cart').on('submit', function(e) {
                                var color = $('#input-color').val();
                                var size = $('#input-size').val();
                                var qty = parseInt(qtyInput.val(), 10) || 0;
                                if (maxStock <= 0 || qty > maxStock) {
                                    e.preventDefault();
                                    showStockNotice(color, size, maxStock);
                                }
                            });

                            refreshVariantStock();
                        });
                    </script>
                    </div><!-- .jm-pdp-info-sticky -->
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center style2-services-container">
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                        <img src="<?php echo base_url(); ?>upload/icon/services.png" alt="service-icon" class="style2-service-icon">
                        <p class="style2-service-text">Phục vụ chu đáo</p>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                        <img src="<?php echo base_url(); ?>upload/icon/ship.png" alt="ship-icon" class="style2-service-icon">
                        <p class="style2-service-text">Trao hàng đúng hẹn</p>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                        <img src="<?php echo base_url(); ?>upload/icon/services.png" alt="return-icon" class="style2-service-icon">
                        <p class="style2-service-text">Đổi hàng trong 24h</p>
                    </div>
                </div>

            </div>
        </div>

        <div class="panel panel-info jm-review-panel">
            <div class="panel-heading"><h3 class="panel-title">Đánh giá sản phẩm</h3></div>
            <div class="panel-body">
                <p class="jm-review-count-title"><?php echo $product->rate_count; ?> đánh giá</p>
                <div class="jm-review-dashboard">
                    <div class="jm-review-score-box">
                        <span class="jm-big-score"><?php echo round($raty_tb, 1); ?></span><span class="jm-max-score">/ 5</span>
                        <div class="raty_big_summary" data-score="<?php echo round($raty_tb, 1); ?>" style="margin-top: 8px;"></div>
                    </div>
                    <div class="jm-review-progress-box">
                        <?php
                        $review_breakdown = isset($review_breakdown) ? $review_breakdown : array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);
                        $rate_5_count = (int) $review_breakdown[5];
                        $rate_4_count = (int) $review_breakdown[4];
                        $rate_3_count = (int) $review_breakdown[3];
                        $rate_2_count = (int) $review_breakdown[2];
                        $rate_1_count = (int) $review_breakdown[1];
                        $percent5 = ($product->rate_count > 0) ? ($rate_5_count / $product->rate_count) * 100 : 0;
                        $percent4 = ($product->rate_count > 0) ? ($rate_4_count / $product->rate_count) * 100 : 0;
                        $percent3 = ($product->rate_count > 0) ? ($rate_3_count / $product->rate_count) * 100 : 0;
                        $percent2 = ($product->rate_count > 0) ? ($rate_2_count / $product->rate_count) * 100 : 0;
                        $percent1 = ($product->rate_count > 0) ? ($rate_1_count / $product->rate_count) * 100 : 0;
                        ?>
                        <div class="jm-progress-row">
                            <span class="jm-star-label">5 sao</span>
                            <div class="jm-progress-bar-bg"><div class="jm-progress-bar-fill" style="width: <?php echo $percent5; ?>%;"></div></div>
                            <span class="jm-star-count">(<?php echo $rate_5_count; ?>)</span>
                        </div>
                        <div class="jm-progress-row">
                            <span class="jm-star-label">4 sao</span>
                            <div class="jm-progress-bar-bg"><div class="jm-progress-bar-fill" style="width: <?php echo $percent4; ?>%;"></div></div>
                            <span class="jm-star-count">(<?php echo $rate_4_count; ?>)</span>
                        </div>
                        <div class="jm-progress-row">
                            <span class="jm-star-label">3 sao</span>
                            <div class="jm-progress-bar-bg"><div class="jm-progress-bar-fill" style="width: <?php echo $percent3; ?>%;"></div></div>
                            <span class="jm-star-count">(<?php echo $rate_3_count; ?>)</span>
                        </div>
                        <div class="jm-progress-row">
                            <span class="jm-star-label">2 sao</span>
                            <div class="jm-progress-bar-bg"><div class="jm-progress-bar-fill" style="width: <?php echo $percent2; ?>%;"></div></div>
                            <span class="jm-star-count">(<?php echo $rate_2_count; ?>)</span>
                        </div>
                        <div class="jm-progress-row">
                            <span class="jm-star-label">1 sao</span>
                            <div class="jm-progress-bar-bg"><div class="jm-progress-bar-fill" style="width: <?php echo $percent1; ?>%;"></div></div>
                            <span class="jm-star-count">(<?php echo $rate_1_count; ?>)</span>
                        </div>
                    </div>
                </div>

                <?php if (!empty($user_already_rated)) { ?>
                    <p class="jm-review-note">
                        Bạn đã đánh giá <?php echo (int) $my_review_stars; ?> sao.
                        Nhấn sao khác để đổi hoặc
                        <button type="button" class="jm-review-undo-btn" id="btn-raty-undo">Hoàn tác đánh giá</button>
                    </p>
                <?php } else { ?>
                    <p class="jm-review-note">Nhấn vào sao phía trên để đánh giá sản phẩm.</p>
                <?php } ?>

                <?php if (!empty($product_reviews)) { ?>
                <div class="jm-review-history">
                    <h4 class="jm-review-history-title">Lịch sử đánh giá</h4>
                    <ul class="jm-review-history-list">
                        <?php foreach ($product_reviews as $review) {
                            $is_mine = (!empty($user_review) && (int) $user_review->id === (int) $review->id);
                        ?>
                        <li class="jm-review-history-item<?php echo $is_mine ? ' jm-review-history-item--mine' : ''; ?>">
                            <div class="jm-review-history-head">
                                <strong class="jm-review-user"><?php echo htmlspecialchars($review->user_name, ENT_QUOTES, 'UTF-8'); ?><?php echo $is_mine ? ' (Bạn)' : ''; ?></strong>
                                <span class="jm-review-stars-inline raty_readonly" data-score="<?php echo (int) $review->stars; ?>"></span>
                                <span class="jm-review-stars-text"><?php echo (int) $review->stars; ?>/5 sao</span>
                                <span class="jm-review-date"><?php echo date('d/m/Y H:i', (int) $review->created); ?></span>
                            </div>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
                <script type="text/javascript">
                $(document).ready(function() {
                    $('.raty_readonly').raty({
                        score: function() { return $(this).attr('data-score'); },
                        readOnly: true,
                        half: false
                    });
                });
                </script>
                <?php } else { ?>
                <p class="jm-review-empty">Chưa có lịch sử đánh giá.</p>
                <?php } ?>
            </div>
        </div>

        <div class="panel panel-info jm-pdp-related">
            <div class="panel-heading jm-pdp-related__head">
                <h3 class="panel-title jm-pdp-related__title">Sản phẩm liên quan</h3>
            </div>
            <div class="panel-body jm-pdp-related__body">
                <div class="product-slider jm-pdp-related-slider">
                    <div class="product-track">
                        <?php for($i=1; $i<=2; $i++): ?>
                            <?php foreach ($productsub as $value) {
                                $rel_name = product_display_name($value->name);
                            ?>
                            <div class="item-box jm-pdp-related-item">
                                <article class="product_item jm-pdp-related-card">
                                    <div class="product-image jm-pdp-related-card__media">
                                        <?php echo product_discount_badge_html($value, 'jm-badge-discount jm-pdp-related-badge'); ?>
                                        <a href="<?php echo build_product_url($value); ?>">
                                            <img src="<?php echo base_url('upload/product/'.$value->image_link); ?>" alt="<?php echo htmlspecialchars($rel_name, ENT_QUOTES, 'UTF-8'); ?>">
                                        </a>
                                    </div>
                                    <div class="jm-pdp-related-card__body">
                                        <h4 class="product_name jm-pdp-related-card__name">
                                            <a href="<?php echo build_product_url($value); ?>"><?php echo $rel_name; ?></a>
                                        </h4>
                                        <?php if ($value->discount > 0) {
                                            $new_price = $value->price - $value->discount; ?>
                                            <p class="jm-pdp-related-card__price">
                                                <span class="price"><?php echo number_format($new_price, 0, ',', '.'); ?> ₫</span>
                                                <del class="product-discount"><?php echo number_format($value->price, 0, ',', '.'); ?> ₫</del>
                                            </p>
                                        <?php } else { ?>
                                            <p class="jm-pdp-related-card__price">
                                                <span class="price"><?php echo number_format($value->price, 0, ',', '.'); ?> ₫</span>
                                            </p>
                                        <?php } ?>
                                        <p class="jm-pdp-related-card__meta">
                                            <span><?php echo (int) $value->view; ?> lượt xem</span>
                                            <span>·</span>
                                            <span><?php echo (int) $value->buyed; ?> đã bán</span>
                                        </p>
                                        <a href="<?php echo site_url('gio-hang/them/' . (int) $value->id); ?>" class="jm-pdp-related-card__cta">Thêm vào giỏ</a>
                                    </div>
                                </article>
                            </div>
                            <?php } ?>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php $this->load->view('site/partials/stock_notice_modal'); ?>

        <script src="<?php echo site_asset_url('js/stock-notice-modal.js'); ?>?v=1"></script>

        <div class="modal fade jm-review-thank-modal" id="jmReviewThankModal" tabindex="-1" role="dialog" aria-labelledby="jmReviewThankTitle" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content jm-review-thank-modal__content">
                    <div class="modal-body jm-review-thank-modal__body">
                        <div class="jm-review-thank-modal__icon" aria-hidden="true">★</div>
                        <h4 class="jm-review-thank-modal__title" id="jmReviewThankTitle">Cảm ơn quý khách!</h4>
                        <div id="jmReviewThankStars" class="jm-review-thank-modal__stars"></div>
                        <p class="jm-review-thank-modal__score" id="jmReviewThankScore"></p>
                        <p class="jm-review-thank-modal__text" id="jmReviewThankText"></p>
                        <button type="button" class="btn jm-review-thank-modal__btn" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>