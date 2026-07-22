jQuery(document).ready(function($) {
    
    // =========================================================================
    // 1. SỰ KIỆN CLICK CHỌN SHOWROOM -> ĐỔI BẢN ĐỒ GOOGLE MAPS
    // =========================================================================
    $(document).on('click', '.store-item', function() {
        var newMapSrc = $(this).attr('data-maps');
        
        if (newMapSrc && newMapSrc !== '') {
            $('#store-map').attr('src', newMapSrc);
        }
        
        // Hiệu ứng đổi màu nền làm nổi bật showroom đang chọn
        $('.store-item').css('background', 'transparent'); 
        $('.store-item').find('.store-name').css('color', '#111');
        
        $(this).css('background', '#f0f4f8'); 
        $(this).find('.store-name').css('color', '#0056b3');
    });

    // =========================================================================
    // 2. SỰ KIỆN LỌC CỬA HÀNG (SỬ DỤNG STYLE INLINE ĐỂ KHÁNG ĐÈ CSS CŨ)
    // =========================================================================
    $('#select-city').on('change', function() {
        var selectedCity = $(this).val().trim().toLowerCase(); 
        
        if (selectedCity === 'all') {
            // Nếu chọn tất cả: Xóa bỏ thuộc tính ẩn inline để trả lại trạng thái hiển thị gốc
            $('.store-item').setProperty('display', 'block', 'important');
            $('.store-item').css('display', '');
        } else {
            $('.store-item').each(function() {
                var storeCity = $(this).attr('data-city') ? $(this).attr('data-city').trim().toLowerCase() : '';
                
                if (storeCity.indexOf(selectedCity) !== -1 || selectedCity.indexOf(storeCity) !== -1) {
                    // Nếu KHỚP tỉnh thành: Ép hiển thị bằng style inline có !important cao nhất
                    this.style.setProperty('display', 'block', 'important');
                } else {
                    // Nếu KHÔNG KHỚP: Ép ẩn tuyệt đối bằng style inline có !important cao nhất
                    this.style.setProperty('display', 'none', 'important');
                }
            });
        }
        
        // Sau khi lọc xong, tự động click vào phần tử đầu tiên đang hiển thị để cập nhật bản đồ
        $('.store-item').filter(function() {
            return $(this).css('display') !== 'none';
        }).first().trigger('click');
    });

    // Kích hoạt hiển thị bản đồ cho cửa hàng đầu tiên khi trang vừa tải xong
    $('.store-item').first().trigger('click');
});