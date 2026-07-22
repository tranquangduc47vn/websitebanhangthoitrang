<div class="jm-fullscreen-wrapper">
    <header class="jm-header-container">
        <div class="jm-header-main">
            
            <div class="jm-logo-area">
                <a href="<?php echo base_url(); ?>">
                    <img src="<?php echo base_url(); ?>upload/logo.png" alt="Logo">
                </a>
            </div>

            <nav class="jm-nav-navigation">
                <ul class="jm-menu-list">
                    <li class="active"><a href="<?php echo base_url(); ?>">HOME</a></li>
                    <li class="jm-dropdown">
                        <a href="#" class="jm-dropdown-toggle">
                            THỜI TRANG NAM <span class="jm-caret"></span>
                        </a>
                        <ul class="jm-dropdown-menu">
                            <?php
                            if(isset($catalog))
                            {
                                foreach($catalog as $parent)
                                {
                                    if($parent->name == 'Thời trang nam')
                                    {
                                        if(isset($parent->sub) && is_array($parent->sub))
                                        {
                                            foreach($parent->sub as $sub)
                                            {
                                                ?>
                                                <li>
                                                    <a href="<?php echo build_category_url($sub->id); ?>">
                                                        <?php echo $sub->name; ?>
                                                    </a>
                                                </li>
                                                <?php
                                            }
                                        }
                                    }
                                }
                            }
                            ?>
                        </ul>
                    </li>

                    <li class="jm-dropdown">
                        <a href="#" class="jm-dropdown-toggle">
                            THỜI TRANG NỮ <span class="jm-caret"></span>
                        </a>
                        <ul class="jm-dropdown-menu">
                            <?php
                            if(isset($catalog))
                            {
                                foreach($catalog as $parent)
                                {
                                    if($parent->id == 8)
                                    {
                                        if(isset($parent->sub) && is_array($parent->sub))
                                        {
                                            foreach($parent->sub as $sub)
                                            {
                                                ?>
                                                <li>
                                                    <a href="<?php echo build_category_url($sub->id); ?>">
                                                        <?php echo $sub->name; ?>
                                                    </a>
                                                </li>
                                                <?php
                                            }
                                        }
                                    }
                                }
                            }
                            ?>
                        </ul>
                    </li>

                    <li class="jm-dropdown">
                        <a href="#" class="jm-dropdown-toggle">
                            THỜI TRANG GIA ĐÌNH <span class="jm-caret"></span>
                        </a>
                        <ul class="jm-dropdown-menu">
                            <?php
                            if(isset($catalog))
                            {
                                foreach($catalog as $parent)
                                {
                                    if($parent->id == 9)
                                    {
                                        if(isset($parent->sub) && is_array($parent->sub))
                                        {
                                            foreach($parent->sub as $sub)
                                            {
                                                ?>
                                                <li>
                                                    <a href="<?php echo build_category_url($sub->id); ?>">
                                                        <?php echo $sub->name; ?>
                                                    </a>
                                                </li>
                                                <?php
                                            }
                                        }
                                    }
                                }
                            }
                            ?>
                        </ul>
                    </li>
                    </ul>
            </nav>

            <div class="jm-header-right-meta">
                <div class="jm-header-icons">
                    <div class="jm-icon-slot">
                        <button type="button" class="jm-icon-btn jm-icon-toggle" title="Tìm kiếm" aria-label="Tìm kiếm" aria-expanded="false" aria-haspopup="true">
                            <svg class="jm-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <circle cx="11" cy="11" r="7"/>
                                <path d="M20 20l-3-3"/>
                            </svg>
                        </button>
                        <div class="jm-icon-popover jm-search-popover">
                            <form action="<?php echo site_url('tim-kiem'); ?>" method="get">
                                <input type="search" name="key-search" placeholder="Tìm sản phẩm..." autocomplete="off">
                                <button type="submit">Tìm</button>
                            </form>
                        </div>
                    </div>

                    <div class="jm-icon-slot jm-user-slot">
                        <?php if (!isset($user)) { ?>
                            <button type="button" class="jm-icon-btn jm-icon-toggle" title="Tài khoản" aria-label="Tài khoản" aria-expanded="false" aria-haspopup="true">
                                <svg class="jm-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <circle cx="12" cy="8" r="4"/>
                                    <path d="M5 20c0-4 3.5-6 7-6s7 2 7 6"/>
                                </svg>
                            </button>
                            <ul class="jm-icon-popover jm-user-popover">
                                <li><a href="<?php echo build_login_url(); ?>">Đăng nhập</a></li>
                                <li><a href="<?php echo build_register_url(); ?>">Đăng ký</a></li>
                            </ul>
                        <?php } else { ?>
                            <button type="button" class="jm-icon-btn jm-icon-toggle" title="Tài khoản" aria-label="Tài khoản" aria-expanded="false" aria-haspopup="true">
                                <svg class="jm-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <circle cx="12" cy="8" r="4"/>
                                    <path d="M5 20c0-4 3.5-6 7-6s7 2 7 6"/>
                                </svg>
                            </button>
                            <ul class="jm-icon-popover jm-user-popover">
                                <li><a href="<?php echo base_url('user'); ?>">Tài khoản</a></li>
                                <li><a href="<?php echo base_url('user/logout'); ?>">Đăng xuất</a></li>
                            </ul>
                        <?php } ?>
                    </div>

                    <div class="jm-icon-slot jm-cart-slot">
                        <?php $this->load->view('site/cart/cart_sh'); ?>
                    </div>
                </div>
            </div>

        </div>
    </header>

    <div class="jm-commitment-bar">
        <div class="jm-commitment-main-inner">
            <div class="jm-commitment-item">
                <span class="glyphicon glyphicon-map-marker"></span>
                <li><a href="<?php echo base_url('hethongcuahang'); ?>">Hệ thống cửa hàng</a></li>
            </div>
            <div class="jm-commitment-item">
                <span class="glyphicon glyphicon-transfer"></span>
                <li><a href="<?php echo base_url('VanChuyen'); ?>">Thông tin vận chuyển</a></li>
            </div>
            <div class="jm-commitment-item">
                <span class="glyphicon glyphicon-list-alt"></span>
                <span class="commit-text">Chính sách tích điểm</span>
            </div>
        </div>
    </div>
</div>