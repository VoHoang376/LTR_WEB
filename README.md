# LTR_WEB

Website công ty – doanh nghiệp (Bài tập lớn Lập trình Web, HK1 2026-2027).
Ứng dụng viết bằng **PHP thuần theo mô hình MVC**, không dùng framework PHP, giao diện
**Bootstrap 5**, khu quản trị theo phong cách **TailAdmin**.
## Giao diện và template dùng chung

- Frontend dùng Bootstrap 5.3.9. Layout chung nằm tại `Views/Layouts/Frontend/`; các view dùng chung header/footer, navbar responsive và stylesheet tại `Public/css/frontend.css`.
- Backend dùng TailAdmin Free 2.5.1 (HTML/Tailwind CSS), không dùng TailAdmin Laravel hay PHP framework. Layout PHP chung nằm tại `Views/Layouts/Backend/`; stylesheet TailAdmin được đóng gói tại `Public/vendor/tailadmin/style.css`, kèm giấy phép MIT tại `Public/vendor/tailadmin/LICENSE`. Tương tác menu responsive dùng JavaScript thuần trong `Public/js/admin.js`.
- Các view xác thực hiện sử dụng layout Bootstrap; dashboard quản trị sử dụng layout TailAdmin. Khi thêm trang mới, khai báo `$pageTitle` (và `$activeAdminPage` cho admin), sau đó include layout tương ứng.

Nguồn TailAdmin Free: [repository](https://github.com/TailAdmin/tailadmin-free-tailwind-dashboard-template), [tài liệu](https://tailadmin.com/docs), [demo miễn phí](https://free-demo.tailadmin.com/). TailAdmin Free được phát hành theo MIT License. Bootstrap được tải qua jsDelivr CDN.
Trình soạn thảo Quill 2.0.3 được tải qua jsDelivr CDN và phát hành theo BSD 3-Clause License.

## Chạy hệ thống xác thực

## Yêu cầu môi trường

- PHP ≥ 7.4 (đã kiểm thử trên PHP 8.2.12) với các extension `pdo_mysql`, `fileinfo`, `mbstring`
- MySQL/MariaDB (đã kiểm thử trên MariaDB 10.4.32)
- Apache (XAMPP) hoặc `php -S` khi phát triển

## Cài đặt

1. Chép thư mục dự án vào `htdocs` (hoặc trỏ VirtualHost) và trỏ document root vào thư mục này.
2. Tạo CSDL và import cấu trúc + dữ liệu mẫu (đã gộp toàn bộ 4 thành viên & `task3_migration`):

   ```bash
   mysql -u root < Database/web_cong_ty.sql
   ```

3. Cấu hình kết nối bằng biến môi trường `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`.
   Mặc định: `localhost`, `web_cong_ty`, `root`, mật khẩu rỗng.
4. Mở `index.php` (Trang chủ) hoặc `index.php?controller=admin&action=dashboard` (Quản trị).

## Tài khoản mẫu (có sẵn trong `Database/web_cong_ty.sql`)

| Email | Mật khẩu | Vai trò |
| --- | --- | --- |
| `admin@ltr.vn` | `Admin@123` | Quản trị viên hệ thống (`role = 0`) |
| `member1@ltr.vn` | `Member@123` | Thành viên / Khách hàng (`role = 1`) |
| `member2@ltr.vn` | `Member@123` | Thành viên / Khách hàng (`role = 1`) |
| `staff@ltr.vn` | `Staff@123` | Nhân viên Chăm sóc khách hàng (`role = 2`) |

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
- Hồ sơ, ảnh đại diện và đổi mật khẩu: `index.php?controller=auth&action=profile`
- Khu vực quản trị (chỉ role `0`): `index.php?controller=admin&action=dashboard`
- Giới thiệu công khai: `index.php?controller=about&action=index`
- Hỏi/Đáp công khai: `index.php?controller=faq&action=index`
- Quản lý Giới thiệu (admin): `index.php?controller=adminAbout&action=index`
- Quản lý Hỏi/Đáp (admin): `index.php?controller=adminFaq&action=index`

Tài khoản đăng ký mới luôn có role thành viên (`1`). Để cấp quyền quản trị, cập nhật cột `role` thành `0` cho tài khoản đó trong bảng `users`. Ảnh tải lên là JPG, PNG hoặc WebP, tối đa 2 MB.

Các trang quản trị Giới thiệu và Hỏi/Đáp yêu cầu đã import `about_sections` và các cột `sort_order`, `created_at` của bảng `faqs` từ `Database/web_cong_ty.sql`.

Đăng nhập sử dụng PHP session mặc định, lưu session trên máy chủ; schema không tạo bảng `sessions`.

## Material
- **Link LaTeX Report:** [Assigment_Report](https://prism.openai.com/?u=bf57d44a-8b72-490d-ba55-219b7216ca92&pg=1&m=main.tex&d=7)
