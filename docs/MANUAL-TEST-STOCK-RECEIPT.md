# Manual test — Module nhập kho

Chạy migration trước:

```bash
php databaseaddphpmyadmin/run_migrations.php
```

Chạy test tự động (CLI):

```bash
php tests/manual_stock_receipt.php
```

## Checklist UI (Admin)

| # | Bước | Kỳ vọng | PASS/FAIL |
|---|------|---------|-----------|
| 1 | Đăng nhập admin → menu **Nhập kho** | Thấy danh sách phiếu | |
| 2 | **Tạo phiếu** → thêm dòng SP + size + màu + SL → Lưu nháp | Phiếu `draft`, mã `PNYYYYMMDDNNN` | |
| 3 | Vào **Tồn kho** trước khi xác nhận | Tồn biến thể **chưa** tăng | |
| 4 | Chi tiết phiếu → **Xác nhận nhập kho** | Trạng thái `confirmed`, tồn tăng đúng SL | |
| 5 | **Lịch sử biến động** | Có dòng `in`, `before_qty` → `after_qty` | |
| 6 | Phiếu đã xác nhận | Không có nút Sửa / Xác nhận lại | |
| 7 | Tạo phiếu nháp mới → **Hủy phiếu** | Trạng thái `cancelled`, tồn không đổi | |
| 8 | Role **User** (nếu có) | Xem được; không tạo/xác nhận | |

## Ghi chú kỹ thuật

- Bảng kho mới dùng **InnoDB** (transaction + `FOR UPDATE`).
- `product.quantity` được **đồng bộ** = tổng `product_inventory` sau xác nhận phiếu.
- Draft **không** ghi `stock_movements`.
