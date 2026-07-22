<div class="container">

    <div class="slider-wrapper">
        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" data-interval="3000" data-pause="false">

           

            <!-- THANH TÌM KIẾM -->
            <div class="slider-search-box">
                <form action="<?php echo site_url('tim-kiem'); ?>" method="get" class="slider-search-form">
                    <div class="slider-search-field">
                        <input type="text"
                               name="key-search"
                               class="slider-search-input"
                               placeholder="Tìm kiếm sản phẩm..."
                               autocomplete="off"
                               aria-label="Tìm kiếm sản phẩm">
                        <button class="slider-search-btn" type="submit">
                            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                            <span>Tìm kiếm</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">

            <?php $i = 0; ?>
            <?php foreach ($slider as $value) { ?>

                <div class="item <?php echo ($i == 0) ? 'active' : ''; ?>">

                    <a href="<?php echo $value->link; ?>">
                        <img src="<?php echo base_url('upload/slider/'.$value->image_link); ?>"
                             alt="<?php echo htmlspecialchars($value->name ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </a>

                    <div class="carousel-caption">
                    </div>

                </div>

            <?php $i++; ?>
            <?php } ?>

        </div>

         </div>

    </div>
</div>