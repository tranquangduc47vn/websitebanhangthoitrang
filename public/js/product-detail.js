/**
 * Tệp xử lý JS dùng chung cho sản phẩm
 */
$(document).ready(function() {

    // --- [ĐOẠN CŨ] KHU VỰC 1: XỬ LÝ TRANG CHI TIẾT SẢN PHẨM ---
    // (Giữ nguyên các hàm cộng trừ số lượng, chọn ảnh phóng to,... của bạn nếu có)


    // --- [ĐOẠN MỚI] KHU VỰC 2: XỬ LÝ BỘ LỌC SIZE TẠI TRANG DANH MỤC ---
    $('.jm-size-label').off('click').on('click', function(e) {
        e.preventDefault(); // Ngăn trình duyệt tự click đúp lỗi logic
        
        var checkbox = $(this).find('input[type="checkbox"]');
        var isChecked = checkbox.prop('checked');
        
        // Đảo ngược trạng thái checkbox
        checkbox.prop('checked', !isChecked);
        
        // Gửi form đi, PHP bên trên sẽ tự động vẽ màu đen khi trang load lại hoàn tất
        $('#filter-form').submit();
    });


    // --- KHU VỰC 3: XỬ LÝ CLICK CHỌN MÀU SẮC (ĐÃ ĐƯỢC ĐƯA VÀO READY ĐỂ CHẠY CHUẨN) ---
    $('.jm-color-label').off('click').on('click', function(e) {
        e.preventDefault(); // Ngăn chặn sự kiện click đúp lặp lại lỗi mảng
        
        var checkbox = $(this).find('input[type="checkbox"]');
        var isChecked = checkbox.prop('checked');
        
        // Đảo ngược trạng thái checkbox ẩn
        checkbox.prop('checked', !isChecked);
        
        // Thực hiện submit gửi dữ liệu bộ lọc lên Server
        $('#filter-form').submit();
    });

});