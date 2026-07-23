# Manual test — Module SP / Tồn kho / Phiếu nhập (v2)

## CLI tự động

```bash
php databaseaddphpmyadmin/run_migrations.php
php tests/manual_stock_module.php
```

Kỳ vọng: **20/20 PASS**.

## Checklist UI Admin

| # | Bước | Kỳ vọng | PASS/FAIL |
|---|------|---------|-----------|
| 1 | Tạo SP mới (không có field tồn kho) | Flash: tồn = 0, tạo biến thể | |
| 2 | Sửa SP → tab biến thể | Có bảng SKU/màu/size, không cột tồn | |
| 3 | **Quản lý kho → Tồn kho** | SKU, màu, size, trạng thái Còn/Sắp hết/Hết | |
| 4 | Tạo phiếu nhập draft | Chọn variant, SL, giá nhập, tổng tiền | |
| 5 | Trước xác nhận: tồn variant không đổi | PASS | |
| 6 | Xác nhận phiếu | Tồn +SL, phiếu locked | |
| 7 | **Lịch sử biến động** | Dòng `in`, before → after | |
| 8 | **Kiểm kê** (adjust) | Nhập lý do, ghi `adjust` | |
| 9 | Đặt hàng storefront | Trừ variant, ghi `out` | |
| 10 | Export tồn kho (admin) | Excel/PDF (TCPDF) | |

## Quy ước kỹ thuật

- Bảng master: **`product`** (giữ tương thích storefront), không đổi tên `products`.
- Tồn thực: **`product_variants.stock`**; `product.quantity` = cache tổng.
- Mọi thay đổi tồn qua: phiếu nhập / đơn hàng / kiểm kê → **`stock_movements`**.
