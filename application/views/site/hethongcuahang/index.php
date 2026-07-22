<div class="gt-wrapper-fix">

    <div class="row" style="display: flex; gap: 30px; flex-wrap: wrap; align-items: flex-start;">
        
        <div style="flex: 1; min-width: 300px;">
            <h2 style="font-size: 24px; font-weight: bold; margin-top: 0; margin-bottom: 20px; text-transform: uppercase; color: #111;">Hệ thống cửa hàng</h2>
            <div style="width: 100%; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <iframe
                    id="store-map"
                    class="store-map-iframe"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3918.8758107946264!2d106.57329537415924!3d10.820814789330674!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752b5254ad2b21%3A0x90cbf42aa07ac0e9!2zSOG7kyBCxqFpIE1JTkggTEFO!5e0!3m2!1svi!2s!4v1781060366454!5m2!1svi!2s"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>

        <div style="width: 360px; flex-shrink: 0; background: #fafafa; padding: 25px 20px; border-radius: 8px; border: 1px solid #eee; height: fit-content;">
            <h4 style="font-size: 15px; font-weight: bold; margin-top: 0; margin-bottom: 15px; text-transform: uppercase; color: #222; border-bottom: 2px solid #222; padding-bottom: 10px; letter-spacing: 0.5px;">
                Chọn tỉnh thành
            </h4>
            
            <select id="select-city" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 20px; background: #fff; font-size: 14px; font-weight: 500; cursor: pointer;">
                <option value="all">-- Tất cả tỉnh thành --</option>
                <option value="Hà Nội">Hà Nội</option>
                <option value="TP. Hồ Chí Minh">TP. Hồ Chí Minh</option>
            </select>

            <div style="max-height: 380px; overflow-y: auto; padding-right: 5px;">
                <?php if(!empty($list_stores)): ?>
                    <?php foreach($list_stores as $store): 
                        $map_url = !empty($store->map_link) ? $store->map_link : 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3918.8758107946264!2d106.57329537415924!3d10.820814789330674!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752b5254ad2b21%3A0x90cbf42aa07ac0e9!2zSOG7kyBCxqFpIE1JTkggTEFO!5e0!3m2!1svi!2s!4v1781060366454!5m2!1svi!2s';
                    ?>
                        <div class="store-item" 
                             data-city="<?php echo trim($store->city); ?>" 
                             data-maps="<?php echo $map_url; ?>">
                            <strong class="store-name" style="display: block; color: #111; font-size: 14px; margin-bottom: 6px; text-transform: uppercase;">
                                📍 <?php echo $store->name; ?>
                            </strong>
                            <p style="margin: 0; font-size: 13px; color: #555; line-height: 1.5;">
                                <?php echo $store->address; ?>
                            </p>
                            <?php if(!empty($store->phone)): ?>
                                <small style="color: #666; display: block; margin-top: 4px; font-weight: 500;">
                                    Hotline: <span style="color: #c0392b;"><?php echo $store->phone; ?></span>
                                </small>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 20px; color: #999; font-style: italic;">
                        Hệ thống đang cập nhật dữ liệu cửa hàng...
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo site_asset_url('js/hethongcuahang.js?v=3'); ?>"></script>