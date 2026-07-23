# Manual test — Bảo mật mật khẩu

Chạy script tự động trước:

```powershell
php tests/manual_password_security.php
php tests/manual_auth_flow.php
```

Kỳ vọng: **FAIL: 0** (SMTP có thể SKIP nếu chưa cấu hình mail).

---

## Checklist thủ công (storefront)

| # | Kịch bản | Cách test | Kỳ vọng | PASS/FAIL |
|---|----------|-----------|---------|-----------|
| 1 | Đăng ký mật khẩu yếu | `/dang-ky`, mật khẩu `1234567` (7 ký tự) | Báo lỗi validation | |
| 2 | Đăng ký thiếu chữ | Mật khẩu `12345678` | Báo cần ít nhất 1 chữ | |
| 3 | Đăng ký thiếu số | Mật khẩu `Abcdefgh` | Báo cần ít nhất 1 số | |
| 4 | Đăng ký hợp lệ | Mật khẩu `TestPass123` + form đầy đủ | Đăng ký OK, redirect đăng nhập | |
| 5 | Đăng nhập bcrypt | Đăng nhập tài khoản vừa tạo (4) | Vào được `/user` | |
| 6 | Đăng nhập MD5 cũ | Tài khoản cũ trong DB (hash 32 hex) | Vẫn đăng nhập được | |
| 7 | Migrate MD5 → bcrypt | Sau (6), kiểm tra DB cột `user.password` | Hash bắt đầu `$2y$` | |
| 8 | Sai mật khẩu | Email đúng, mật khẩu sai | Thông báo lỗi, không vào | |
| 9 | Quên mật khẩu | `/quen-mat-khau` + email hợp lệ | Thông báo chung (không lộ email) | |
| 10 | Reset mật khẩu | Mở link email `/dat-lai-mat-khau/{token}` | Form đặt lại mật khẩu | |
| 11 | Reset mật khẩu yếu | Mật khẩu mới `abcdefgh` | Bị từ chối (thiếu số) | |
| 12 | Reset thành công | Mật khẩu mới `NewPass99` | Redirect đăng nhập, login OK | |
| 13 | Token hết hạn | Link cũ > 15 phút hoặc đã dùng | Báo link không hợp lệ | |
| 14 | Đổi mật khẩu | `/user` → icon bút Mật khẩu | Modal đổi mật khẩu | |
| 15 | Đổi MK sai cũ | Nhập sai mật khẩu hiện tại | Báo lỗi | |
| 16 | Đổi MK thành công | Cũ đúng + mới `Change99x` | Flash success, login bằng MK mới | |

---

## Checklist admin

| # | Kịch bản | Cách test | Kỳ vọng | PASS/FAIL |
|---|----------|-----------|---------|-----------|
| A1 | Admin login MD5 cũ | `/admin/login` tài khoản admin cũ | Đăng nhập OK | |
| A2 | Admin migrate hash | Sau A1, xem `admin.password` trong DB | `$2y$...` nếu trước đó là MD5 | |
| A3 | Thêm staff MK yếu | Admin → thêm NV, MK `12345678` | Validation từ chối | |
| A4 | Thêm staff MK mạnh | MK `StaffPass1` | Tạo OK, login staff OK | |

---

## Kiểm tra DB nhanh

```sql
-- Hash bcrypt (60 ký tự, bắt đầu $2y$)
SELECT id, email, LEFT(password, 4) AS algo, LENGTH(password) AS len FROM user LIMIT 10;
SELECT id, email, LEFT(password, 4) AS algo, LENGTH(password) AS len FROM admin LIMIT 10;
```

| algo | Ý nghĩa |
|------|---------|
| `$2y$` | bcrypt (mới) |
| hex 32 ký tự | MD5 legacy (chưa login lại) |

---

## File liên quan

| File | Vai trò |
|------|---------|
| `application/helpers/password_helper.php` | hash, verify, strength, reset token |
| `application/controllers/User.php` | register, login, forgot, reset, password_change |
| `application/controllers/admin/Login.php` | admin login + rehash |
| `application/controllers/admin/Admin.php` | tạo/sửa staff với bcrypt |
