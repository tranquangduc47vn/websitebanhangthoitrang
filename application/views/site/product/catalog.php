<?php
    $catalog_id = isset($catalog_p->id) ? $catalog_p->id : $this->uri->segment(3);
    if (!isset($filter_action)) {
        $filter_action = build_category_url($catalog_id);
    }
    if (!isset($main_catalog_title) && isset($title_catalog)) {
        $main_catalog_title = $title_catalog;
    }
    $site_asset_url = site_asset_url('');

    $chosen_cats = $this->input->get('category');
    if ($chosen_cats && !is_array($chosen_cats)) {
        $chosen_cats = array($chosen_cats);
    } elseif (!$chosen_cats) {
        $chosen_cats = array();
    }

    $url_sizes = $this->input->get('size');
    if (empty($url_sizes) && isset($_GET['size'])) {
        $url_sizes = $_GET['size'];
    }
    $chosen_sizes_arr = array();
    if (!empty($url_sizes)) {
        $chosen_sizes_arr = is_array($url_sizes) ? $url_sizes : array($url_sizes);
        $chosen_sizes_arr = array_map('trim', array_map('strval', $chosen_sizes_arr));
    }

    $url_colors = $this->input->get('color');
    if (empty($url_colors) && isset($_GET['color'])) {
        $url_colors = $_GET['color'];
    }
    $chosen_colors_arr = array();
    if (!empty($url_colors)) {
        $chosen_colors_arr = is_array($url_colors) ? $url_colors : array($url_colors);
        $chosen_colors_arr = array_map('trim', $chosen_colors_arr);
    }

    $chosen_price = $this->input->get('price_range');
?>
<link rel="stylesheet" href="<?php echo $site_asset_url; ?>css/catalog-luxury.css?v=26">
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 jm-catalog-wrapper jm-catalog-lux jm-catalog-lux--sidebar-pinned">

    <div class="jm-toolbar jm-catalog-header">
        <h1 class="jm-catalog-title">
            <?php echo isset($main_catalog_title) ? $main_catalog_title : (isset($catalog_p->name) ? $catalog_p->name : 'DANH SÁCH SẢN PHẨM'); ?>
        </h1>
        <div class="jm-toolbar-actions">
            <div class="jm-product-counter">
                Hiển thị <span class="jm-catalog-count"><?php echo isset($total) ? $total : (isset($product_list) ? count($product_list) : 0); ?></span> sản phẩm
            </div>
            <div class="jm-sort-box">
                <label class="jm-label-sort">Xem theo</label>
                <form method="GET" action="<?php echo $filter_action; ?>">
                    <?php 
                        if(is_array($this->input->get('category'))) {
                            foreach($this->input->get('category') as $cat) {
                                echo '<input type="hidden" name="category[]" value="'.htmlspecialchars($cat).'">';
                            }
                        }
                        if(is_array($this->input->get('size'))) {
                            foreach($this->input->get('size') as $sz) {
                                echo '<input type="hidden" name="size[]" value="'.htmlspecialchars($sz).'">';
                            }
                        }
                        if(is_array($this->input->get('color'))) {
                            foreach($this->input->get('color') as $cl) {
                                echo '<input type="hidden" name="color[]" value="'.htmlspecialchars($cl).'">';
                            }
                        }
                        if($this->input->get('price_range')) {
                            echo '<input type="hidden" name="price_range" value="'.htmlspecialchars($this->input->get('price_range')).'">';
                        }
                    ?>
                    <select class="form-control jm-select-sort" name="sort" onchange="this.form.submit()">
                        <?php if (!empty($is_hot_list)) { ?>
                        <option value="hot" <?php if ($this->input->get('sort') === 'hot' || $this->input->get('sort') === '' || $this->input->get('sort') === false) echo 'selected'; ?>>Bán chạy nhất</option>
                        <option value="new" <?php if ($this->input->get('sort') == 'new') echo 'selected'; ?>>Mới nhất</option>
                        <?php } else { ?>
                        <option value="new" <?php if($this->input->get('sort')=='new' || !$this->input->get('sort')) echo 'selected'; ?>>Mới nhất</option>
                        <?php } ?>
                        <option value="price_asc" <?php if($this->input->get('sort')=='price_asc') echo 'selected'; ?>>Giá tăng dần</option>
                        <option value="price_desc" <?php if($this->input->get('sort')=='price_desc') echo 'selected'; ?>>Giá giảm dần</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <div class="row jm-main-layout-flex">
        <aside class="col-xs-12 col-sm-4 col-md-3 jm-sidebar-filter">
            <div class="jm-catalog-sidebar-card">
            <h2 class="jm-filter-main-title">TÌM KIẾM</h2>
            
            <form method="GET" action="<?php echo $filter_action; ?>" id="filter-form">
                
                <?php if($this->input->get('sort')): ?>
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($this->input->get('sort')); ?>">
                <?php endif; ?>

                <div class="jm-filter-group">
                    <div class="jm-filter-heading jm-filter-heading--static">
                        <span>Dòng sản phẩm</span>
                    </div>
                    <div id="filter-category" class="collapse in">
                        <div class="jm-checkbox-list">
                            <?php 
                                if (isset($catalog_list) && !empty($catalog_list)):
                                    foreach ($catalog_list as $parent): 
                                        $parent_checked = in_array($parent->id, $chosen_cats);
                            ?>
                                <label class="jm-checkbox-item jm-checkbox-item--parent">
                                    <input type="checkbox" name="category[]" value="<?php echo htmlspecialchars($parent->id); ?>" <?php echo $parent_checked ? 'checked' : ''; ?>> 
                                    <span class="jm-checkmark"></span> <?php echo $parent->name; ?>
                                </label>
                                
                                <?php if (!empty($parent->subs)): ?>
                                    <div class="jm-sub-category-list">
                                        <?php 
                                            foreach ($parent->subs as $sub): 
                                                $sub_checked = in_array($sub->id, $chosen_cats);
                                        ?>
                                            <label class="jm-checkbox-item jm-checkbox-item--sub">
                                                <input type="checkbox" name="category[]" value="<?php echo htmlspecialchars($sub->id); ?>" <?php echo $sub_checked ? 'checked' : ''; ?>> 
                                                <span class="jm-checkmark"></span> <?php echo $sub->name; ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                            <?php 
                                    endforeach;
                                endif; 
                            ?>
                        </div>
                    </div>
                </div>

                <div class="jm-filter-group">
                    <div class="jm-filter-heading jm-filter-heading--static">
                        <span>Kích thước</span>
                    </div>
                    <div id="filter-size" class="collapse in">
                        <div class="jm-size-options">
                            <?php 
                                $dynamic_sizes = array();
                                if (isset($product_list) && !empty($product_list)) {
                                    foreach ($product_list as $p_item) {
                                        if (!empty($p_item->size)) {
                                            $p_sizes = explode(',', $p_item->size);
                                            foreach ($p_sizes as $sz_val) {
                                                $sz_val = trim($sz_val);
                                                if ($sz_val !== '' && !in_array($sz_val, $dynamic_sizes)) {
                                                    $dynamic_sizes[] = $sz_val;
                                                }
                                            }
                                        }
                                    }
                                }

                                if (empty($dynamic_sizes)) {
                                    $dynamic_sizes = array('S', 'M', 'L', 'XL', 'XXL');
                                }

                                if (!empty($chosen_sizes_arr)) {
                                    $dynamic_sizes = array_values(array_unique(array_merge($dynamic_sizes, $chosen_sizes_arr)));
                                }
                                
                                sort($dynamic_sizes);

                                foreach ($dynamic_sizes as $size):
                                    $size_clean = trim((string)$size);
                                    $is_checked = in_array($size_clean, $chosen_sizes_arr);
                            ?>
                            <label class="jm-size-label <?php echo $is_checked ? 'active' : ''; ?>" style="cursor:pointer; margin-right:5px; display: inline-block; position: relative;">
                                <input type="checkbox" name="size[]" value="<?php echo htmlspecialchars($size_clean); ?>" <?php echo $is_checked ? 'checked' : ''; ?> style="position: absolute; opacity: 0; width: 100%; height: 100%; margin: 0; cursor: pointer; left: 0; top: 0;">
                                <span class="jm-size-box"><?php echo $size_clean; ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="jm-filter-group">
                    <div class="jm-filter-heading jm-filter-heading--static">
                        <span>Màu sắc</span>
                    </div>
                    <div id="filter-color" class="collapse in">
                        <div class="jm-color-scroll-container">
                            <div class="jm-color-options">
                                <?php 
                                    $available_colors = [
                                        'Đen'        => '#000000',
                                        'Trắng'      => '#ffffff',
                                        'Đỏ'         => '#dd021b',
                                        'Vàng'       => '#fcd535',
                                        'Xanh dương' => '#2196f3',
                                        'Xanh lá'    => '#4caf50',
                                        'Hồng'       => '#ffb6c1',
                                        'Xám'        => '#808080',
                                        'Nâu'        => '#8b5a2b',
                                        'Kem'        => '#f5f5dc',
                                        'Cam'        => '#ff9800',
                                        'Tím'        => '#9c27b0'
                                    ];

                                    if (!empty($chosen_colors_arr)) {
                                        foreach ($chosen_colors_arr as $chosen_color_name) {
                                            if ($chosen_color_name !== '' && !isset($available_colors[$chosen_color_name])) {
                                                $available_colors[$chosen_color_name] = '#e0e0e0';
                                            }
                                        }
                                    }

                                    foreach ($available_colors as $color_name => $hex_code):
                                        $is_checked = in_array($color_name, $chosen_colors_arr);
                                        $border_style = ($hex_code == '#ffffff' || $hex_code == '#f5f5dc') ? 'border: 1px solid #ddd;' : '';
                                        $tick_color = ($hex_code == '#ffffff' || $hex_code == '#f5f5dc') ? '#333333' : '#ffffff';
                                ?>
                                    <label class="jm-color-label <?php echo $is_checked ? 'active' : ''; ?>" title="<?php echo htmlspecialchars($color_name); ?>" style="display: inline-block; cursor: pointer; position: relative; user-select: none; margin: 0;">
                                        <input type="checkbox" name="color[]" value="<?php echo htmlspecialchars($color_name); ?>" <?php echo $is_checked ? 'checked' : ''; ?> style="position: absolute; opacity: 0; width: 100%; height: 100%; margin: 0; cursor: pointer; left: 0; top: 0;">
                                        
                                        <span class="jm-color-circle" style="display: flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 50%; background-color: <?php echo $hex_code; ?>; <?php echo $border_style; ?> transition: all 0.2s ease; box-shadow: inset 0 1px 2px rgba(0,0,0,0.15);">
                                            <?php if ($is_checked): ?>
                                                <span class="jm-tick-icon" style="color: <?php echo $tick_color; ?>; font-size: 14px; font-weight: bold; line-height: 1;">✓</span>
                                            <?php endif; ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

<div class="jm-filter-group">
    <div class="jm-filter-heading jm-filter-heading--static">
        <span>Giá</span>
    </div>
    <div id="filter-price" class="collapse in">
        <p class="jm-price-filter-hint">Chọn khoảng giá</p>
        <div class="jm-radio-list">
            
            <label class="jm-radio-item">
                <input type="radio" name="price_range" value="" <?php echo empty($chosen_price) ? 'checked' : ''; ?>>
                <span>Tất cả mức giá</span>
            </label>
            
            <label class="jm-radio-item">
                <input type="radio" name="price_range" value="0-200000" <?php echo $chosen_price == '0-200000' ? 'checked' : ''; ?>>
                <span>Dưới 200.000đ</span>
            </label>
            
            <label class="jm-radio-item">
                <input type="radio" name="price_range" value="200000-500000" <?php echo $chosen_price == '200000-500000' ? 'checked' : ''; ?>>
                <span>200.000đ - 500.000đ</span>
            </label>
            
            <label class="jm-radio-item">
                <input type="radio" name="price_range" value="500000-1000000" <?php echo $chosen_price == '500000-1000000' ? 'checked' : ''; ?>>
                <span>500.000đ - 1.000.000đ</span>
            </label>
            
            <label class="jm-radio-item">
                <input type="radio" name="price_range" value="1000000-999999999" <?php echo $chosen_price == '1000000-999999999' ? 'checked' : ''; ?>>
                <span>Trên 1.000.000đ</span>
            </label>
            
        </div>
    </div>
</div>
            </form>
            </div>
        </aside>

        <main class="col-xs-12 col-sm-8 col-md-9 jm-product-content">

            <div id="jm-catalog-results" class="jm-catalog-results">
            <div class="jm-product-grid">
                <?php 
                $has_products = false;
                $pagination_links = '';
                if (isset($total) && $total > 0) {
                    $has_products = true;
                } elseif (isset($product_list) && is_array($product_list) && count($product_list) > 0) {
                    $has_products = true;
                }

                if ($has_products) { 
                    foreach ($product_list as $value) {
                        // SỬA LỖI CHÍNH TẢ: covert_vi_to_en thành convert_vi_to_en
                        $product_link = build_product_url($value);
                        $in_stock = product_is_in_stock($value);
                ?>
                        <div class="jm-grid-item">
                            <article class="jm-product-card<?php echo $in_stock ? '' : ' jm-product-card--out-of-stock'; ?>">
                                
                                <div class="jm-product-thumb-wrapper">
                                    <?php echo product_discount_badge_html($value); ?>
                                    <?php if (!$in_stock) { echo product_out_of_stock_badge_html(); } ?>
                                    <a href="<?php echo $product_link; ?>" class="jm-img-link">
                                        <img src="<?php echo base_url(); ?>upload/product/<?php echo $value->image_link; ?>" alt="<?php echo htmlspecialchars(product_display_name($value->name), ENT_QUOTES, 'UTF-8'); ?>" class="jm-product-img">
                                    </a>

                                    <?php if ($in_stock) { ?>
                                    <a href="<?php echo site_url('gio-hang/them/' . (int) $value->id); ?>" class="jm-catalog-add-cart">Thêm vào giỏ</a>
                                    <?php } else { ?>
                                    <span class="jm-catalog-add-cart jm-catalog-add-cart--disabled">Hết hàng</span>
                                    <?php } ?>
                                </div>
                                
                                <div class="jm-product-info">
                                    <h3 class="jm-product-name">
                                        <a href="<?php echo $product_link; ?>"><?php echo product_display_name($value->name); ?></a>
                                    </h3>
                                    
                                    <div class="jm-product-meta">
                                        <div class="jm-price-box">
                                            <?php if ($value->discount > 0) { 
                                                $new_price = $value->price - $value->discount; 
                                            ?>
                                                <span class="jm-price-new"><span class="jm-price-amount"><?php echo number_format($new_price); ?></span><span class="jm-price-currency">đ</span></span>
                                                <del class="jm-price-old"><span class="jm-price-amount"><?php echo number_format($value->price); ?></span><span class="jm-price-currency">đ</span></del>
                                            <?php } else { ?>
                                                <span class="jm-price-new"><span class="jm-price-amount"><?php echo number_format($value->price); ?></span><span class="jm-price-currency">đ</span></span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    
                                    <div class="jm-product-rating">
                                        <span class="jm-stars">★★★★★</span>
                                        <?php if (!empty($is_hot_list)) { ?>
                                        <span class="jm-sales-count">(<?php echo (int) $value->buyed; ?> đã bán)</span>
                                        <?php } else { ?>
                                        <span class="jm-sales-count">(<?php echo $value->view; ?> đã xem)</span>
                                        <?php } ?>
                                    </div>
                                </div>

                            </article>
                        </div>
                    <?php } ?>

                    <?php $pagination_links = $this->pagination->create_links(); ?>

                <?php } else {
                    $empty_context = 'trong danh mục này';
                    if (!empty($empty_catalog_message)) {
                        $empty_context = $empty_catalog_message;
                    } elseif (!empty($is_search_page)) {
                        $search_kw = isset($keyword) ? trim((string) $keyword) : '';
                        $empty_context = $search_kw !== ''
                            ? 'với từ khóa «' . htmlspecialchars($search_kw, ENT_QUOTES, 'UTF-8') . '»'
                            : 'với từ khóa tìm kiếm của bạn';
                    } elseif (!empty($empty_catalog_label)) {
                        $empty_context = 'trong mục ' . htmlspecialchars($empty_catalog_label, ENT_QUOTES, 'UTF-8');
                    } elseif (isset($title_catalog) && $title_catalog !== '') {
                        $empty_context = 'trong mục ' . htmlspecialchars(mb_strtolower($title_catalog, 'UTF-8'), ENT_QUOTES, 'UTF-8');
                    }
                ?>
                    <div class="jm-empty-catalog">
                        <p>Không có sản phẩm nào phù hợp <?php echo $empty_context; ?>.</p>
                    </div>
                <?php } ?>
            </div>

            <?php if (!empty($pagination_links)) { ?>
            <nav class="jm-catalog-pagination" aria-label="Phân trang sản phẩm">
                <?php echo $pagination_links; ?>
            </nav>
            <?php } ?>
            </div>
        </main>

    </div>
</div>

<script src="<?php echo $site_asset_url; ?>js/catalog-luxury.js?v=6"></script>
