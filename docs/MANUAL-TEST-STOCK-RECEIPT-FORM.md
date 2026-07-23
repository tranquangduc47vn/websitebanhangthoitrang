# Manual test — Form Tạo phiếu nhập (bộ lọc + tìm biến thể)

**URL:** `/admin/receipts/create` hoặc `/admin/stock-receipts/add`  
**Yêu cầu:** Tài khoản admin có quyền `stock.manage`  
**Chuẩn bị:** Có ít nhất 1 NCC, ≥2 sản phẩm, ≥10 biến thể (nên có variant tồn = 0 và tồn > 0)

---

## A. Giao diện & bộ lọc

| # | Bước kiểm tra | Kỳ vọng | Kết quả |
|---|---------------|---------|---------|
| A1 | Mở trang Tạo phiếu nhập | Không còn dropdown biến thể dài; có thanh lọc Danh mục / Sản phẩm / Màu / Size / ô tìm | |
| A2 | Ô Sản phẩm (Tom Select) gõ tên | Gợi ý AJAX, không load toàn bộ SP | |
| A3 | Chọn Danh mục → gõ Sản phẩm | Chỉ SP thuộc danh mục đó | |
| A4 | Chọn Sản phẩm | Dropdown Màu / Size chỉ hiện giá trị có trên biến thể SP đó | |
| A5 | Gõ SKU vào ô tìm | Bảng biến thể lọc theo SKU | |
| A6 | Gõ tên SP / màu / size | Bảng lọc đúng (OR trên các trường) | |
| A7 | Kết hợp nhiều bộ lọc | Kết quả thu hẹp đúng (AND) | |
| A8 | Phân trang (>25 biến thể) | Nút Trước/Sau, nhãn trang X/Y | |

## B. Bảng chọn biến thể

| # | Bước kiểm tra | Kỳ vọng | Kết quả |
|---|---------------|---------|---------|
| B1 | Bảng hiển thị cột SKU, Màu, Size, Tồn, Giá nhập, SL, Thêm | Đủ cột | |
| B2 | Biến thể tồn = 0 | Số tồn màu đỏ + badge cảnh báo `!` | |
| B3 | Nhập SL + Giá nhập → **Thêm** | Dòng xuất hiện ở “Chi tiết phiếu nhập” | |
| B4 | Thêm cùng biến thể lần 2 từ bảng picker | Nút “Đã thêm”, không trùng dòng | |
| B5 | Giá nhập mặc định | Lấy `cost_price` variant nếu có | |

## C. Biến thể đã dùng gần đây

| # | Bước kiểm tra | Kỳ vọng | Kết quả |
|---|---------------|---------|---------|
| C1 | Sau khi đã tạo ≥1 phiếu nhập trước đó | Hiện khu vực “Biến thể đã dùng gần đây” (≤10) | |
| C2 | Bấm **Thêm** trên chip gần đây | Thêm vào chi tiết phiếu | |
| C3 | Tài khoản mới chưa từng tạo phiếu | Khu vực ẩn hoặc trống | |

## D. Chi tiết phiếu & lưu

| # | Bước kiểm tra | Kỳ vọng | Kết quả |
|---|---------------|---------|---------|
| D1 | Sửa SL / giá trên dòng đã thêm | Thành tiền & tổng cập nhật | |
| D2 | Xóa dòng | Tổng giảm; picker cho phép thêm lại | |
| D3 | Lưu không có dòng | Alert “thêm ít nhất một dòng” | |
| D4 | Lưu phiếu nháp hợp lệ | Redirect view phiếu; dòng đúng variant/qty/cost | |
| D5 | Sửa phiếu nháp (edit) | Dòng cũ load đúng SKU/màu/size/qty/giá | |
| D6 | Xác nhận phiếu | Tồn tăng, `stock_movements` type `in` | |

## E. Hiệu năng & API

| # | Bước kiểm tra | Kỳ vọng | Kết quả |
|---|---------------|---------|---------|
| E1 | DevTools Network: mở form | Không request load 500+ variant một lần | |
| E2 | `GET /admin/receipts/search_variants?page=1&per_page=25` | JSON `{ ok, items, total, pages }` | |
| E3 | `GET /admin/receipts/filter_products?q=...` | JSON Tom Select `{ results, pagination }` | |
| E4 | DB ≥5000 variants (hoặc giả lập LIMIT) | Trang phản hồi <3s, UI không treo | |

---

## Ghi chú tester

- Ghi **PASS** / **FAIL** vào cột Kết quả.
- Nếu FAIL: ghi thêm mô tả + screenshot / response JSON.
- Sau purge sản phẩm: cần tạo lại SP + biến thể trước khi test B/D/E.

---

## Automated smoke (CLI)

```bash
php tests/manual_stock_receipt_form.php
```

Chạy kiểm tra model/query (không thay browser test).

---

**Tester:** _______________  
**Ngày:** _______________  
**Tổng PASS / FAIL:** ___ / ___
