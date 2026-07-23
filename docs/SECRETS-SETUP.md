# Cấu hình secret — gửi riêng, không commit GitHub

Repo **không chứa** mật khẩu / API key. Mỗi người clone tự tạo 3 file local từ file mẫu.

Chủ repo gửi giá trị thật qua **Zalo / Discord / chat riêng** (không paste lên GitHub).

---

## Bước 1 — Copy file mẫu (mỗi người làm một lần)

Trong thư mục gốc project (`webshop`):

```powershell
copy application\config\database.php.example application\config\database.php
copy application\config\ai.php.example application\config\ai.php
copy application\config\mail.local.example.php application\config\mail.local.php
```

---

## Bước 2 — Điền key vào đúng file

### 1) Database — `application/config/database.php`

| Biến | Ý nghĩa | Ví dụ |
|------|---------|-------|
| `hostname` | MySQL host | `localhost` |
| `username` | User MySQL | `root` |
| `password` | Mật khẩu MySQL | *(gửi riêng)* |
| `database` | Tên database | `webshop` |

Import DB: `databaseaddphpmyadmin/webshop.sql` rồi (tuỳ chọn) `php databaseaddphpmyadmin/run_migrations.php`.

**Không có DB → web không chạy** (dù SMTP/AI đã cấu hình).

---

### 2) AI chat — `application/config/ai.php`

| Biến | Ý nghĩa | Ghi chú |
|------|---------|---------|
| `ai_provider` | `'gemini'` hoặc `'openai'` | Team đang dùng Gemini |
| `ai_gemini_api_key` | API key Google AI | *(gửi riêng)* — lấy tại [Google AI Studio](https://aistudio.google.com/apikey) |
| `ai_openai_api_key` | API key OpenAI | Chỉ cần nếu `ai_provider = openai` |

Bật/tắt widget AI trên site: bảng `ai_setting` trong DB (admin).

**Test:** mở storefront → icon chat góc phải → gửi tin nhắn thử.

---

### 3) Email SMTP — `application/config/mail.local.php`

| Biến | Ý nghĩa | Ghi chú |
|------|---------|---------|
| `mail_from_email` | Email hiển thị người gửi | Nên **trùng** Gmail đăng nhập |
| `mail_from_name` | Tên hiển thị | `qD Design` |
| `mail_smtp_user` | Gmail đăng nhập SMTP | *(gửi riêng)* |
| `mail_smtp_pass` | **App Password** 16 ký tự | *(gửi riêng)* — không phải mật khẩu Gmail thường |
| `mail_smtp_host` | `smtp.gmail.com` | Giữ mặc định |
| `mail_smtp_port` | `587` | Giữ mặc định |
| `mail_smtp_secure` | `tls` | Giữ mặc định |

Gmail cần bật **xác minh 2 bước** rồi tạo App Password.

**Test:**

```powershell
php tests/test_smtp.php
php tests/test_smtp.php email-cua-ban@gmail.com
```

Hoặc test qua web: `/dang-ky` (email chào mừng) hoặc `/quen-mat-khau`.

---

## Mẫu tin nhắn gửi riêng cho bạn (chủ repo copy, điền, gửi chat)

```
=== Webshop — config local (KHÔNG đăng GitHub) ===

1) Database — paste vào application/config/database.php
   hostname: localhost
   username: root
   password: [ĐIỀN]
   database: webshop

2) AI — paste vào application/config/ai.php
   ai_provider: gemini
   ai_gemini_api_key: [ĐIỀN]

3) SMTP — paste vào application/config/mail.local.php
   mail_from_email: [ĐIỀN]
   mail_smtp_user: [ĐIỀN]   (cùng email Gmail)
   mail_smtp_pass: [ĐIỀN]   (App Password 16 ký tự)

Setup:
- copy 3 file .example → bỏ .example (xem docs/SECRETS-SETUP.md)
- import databaseaddphpmyadmin/webshop.sql
- php databaseaddphpmyadmin/run_migrations.php
```

---

## File không được commit (`.gitignore`)

- `application/config/database.php`
- `application/config/ai.php`
- `application/config/mail.local.php`

File **có trên GitHub** (để trống sẵn): `*.example.php`.

---

## Checklist nhanh

| Tính năng | File | Key cần |
|-----------|------|---------|
| Web + admin | `database.php` | MySQL user/password |
| Chat AI | `ai.php` | `ai_gemini_api_key` |
| Email đăng ký / quên MK | `mail.local.php` | Gmail + App Password |
