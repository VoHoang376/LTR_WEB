# LTR_WEB

Website công ty – doanh nghiệp (Bài tập lớn Lập trình Web, HK1 2026-2027).
Ứng dụng viết bằng **PHP thuần theo mô hình MVC**, không dùng framework PHP, giao diện
**Bootstrap 5**, khu quản trị theo phong cách **TailAdmin**.

## Yêu cầu môi trường

- PHP ≥ 7.4 (đã kiểm thử trên PHP 8.2.12) với các extension `pdo_mysql`, `fileinfo`, `mbstring`
- MySQL/MariaDB (đã kiểm thử trên MariaDB 10.4.32)
- Apache (XAMPP) hoặc `php -S` khi phát triển

## Cài đặt

1. Chép thư mục dự án vào `htdocs` (hoặc trỏ VirtualHost) và trỏ document root vào thư mục này.
2. Tạo CSDL và import cấu trúc:

   ```bash
   mysql -u root -e "CREATE DATABASE web_cong_ty CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   mysql -u root web_cong_ty < web_cong_ty.sql
   ```

   > `web_cong_ty.sql` **đã bao gồm** các thay đổi của phần #3 (cột `stock`, bảng
   > `product_images`, các cột của bảng `orders`). Nếu nhóm đã import bản dump cũ thì chỉ
   > cần chạy thêm một lần: `mysql -u root web_cong_ty < Database/task3_migration.sql`.

3. (Tuỳ chọn – khuyến nghị khi demo) Nạp dữ liệu mẫu cho phần #3:

   ```bash
   mysql -u root web_cong_ty < Database/seed_task3.sql
   ```

4. Cấu hình kết nối bằng biến môi trường `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`.
   Mặc định: `localhost`, `web_cong_ty`, `root`, mật khẩu rỗng.
5. Mở `index.php?controller=product&action=index` (trang danh sách sản phẩm).

## Tài khoản mẫu (sau khi nạp `Database/seed_task3.sql`)

| Email | Mật khẩu | Vai trò |
| --- | --- | --- |
| `admin@ltr.vn` | `Admin@123` | Quản trị viên (`role = 0`) |
| `member1@ltr.vn` | `Member@123` | Thành viên (`role = 1`) |
| `member2@ltr.vn` | `Member@123` | Thành viên (`role = 1`) |

Đăng ký mới luôn có role thành viên (`1`). Muốn cấp quyền quản trị, cập nhật cột `role`
thành `0` trong bảng `users`.

## Màn hình

### Dùng chung (phần công việc chung)

- Đăng ký: `index.php?controller=auth&action=register`
- Đăng nhập: `index.php?controller=auth&action=login`
- Hồ sơ, ảnh đại diện, đổi mật khẩu: `index.php?controller=auth&action=profile`
- Khu vực quản trị: `index.php?controller=admin&action=dashboard`

### Phần #3 – Sản phẩm · Giỏ hàng · Đơn hàng

**Phía người dùng**

| Chức năng | URL |
| --- | --- |
| Danh sách sản phẩm (tìm kiếm, lọc danh mục/khoảng giá, sắp xếp, phân trang) | `index.php?controller=product&action=index` |
| Chi tiết sản phẩm (gallery, thông số, sản phẩm liên quan) | `index.php?controller=product&action=show&slug=...` |
| Giỏ hàng (AJAX thêm/sửa/xoá) | `index.php?controller=cart&action=index` |
| Thanh toán | `index.php?controller=checkout&action=index` |
| Xác nhận đặt hàng | `index.php?controller=checkout&action=success&id=...` |
| Đơn hàng của tôi | `index.php?controller=order&action=index` |
| Chi tiết đơn (chỉ đơn của mình) | `index.php?controller=order&action=show&id=...` |

**Quản trị**

| Chức năng | URL |
| --- | --- |
| Quản lý sản phẩm (tìm kiếm, lọc, phân trang) | `index.php?controller=admin&action=products` |
| Thêm sản phẩm | `index.php?controller=admin&action=product-create` |
| Sửa sản phẩm + thư viện ảnh | `index.php?controller=admin&action=product-edit&id=...` |
| Quản lý danh mục | `index.php?controller=admin&action=categories` |
| Quản lý đơn hàng (lọc trạng thái, tìm kiếm, lọc ngày) | `index.php?controller=admin&action=orders` |
| Chi tiết đơn + đổi trạng thái | `index.php?controller=admin&action=order-show&id=...` |

Chi tiết thiết kế, bảo mật, kiểm thử và dàn ý báo cáo: xem [`docs/TASK3.md`](docs/TASK3.md).

## Quy ước code

- Truy vấn CSDL: luôn dùng **PDO prepared statement** (`Config/database.php` tắt emulate prepare).
- In ra view: luôn qua `e()` (trừ nội dung WYSIWYG đã lọc theo whitelist).
- Mọi form POST có `csrf_field()` và kiểm tra `csrf_verify()`; thao tác xoá dùng POST.
- Upload ảnh: chỉ dùng `upload_image()` (JPG/PNG/WebP, ≤ 2 MB, kiểm tra bằng `finfo` + `getimagesize`).
- Phân quyền: `Permission::requireAdmin()` cho mọi action quản trị,
  `Permission::requireLogin()` cho action của thành viên. Không đọc trực tiếp `$_SESSION['role']`.
- Giá trị trong bảng `settings` khi in ra đều qua `e()`.
