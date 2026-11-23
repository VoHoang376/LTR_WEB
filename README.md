# LTR_WEB

## Giao diện và template dùng chung

- Frontend dùng Bootstrap 5.3.9. Layout chung nằm tại `Views/Layouts/Frontend/`; các view dùng chung header/footer, navbar responsive và stylesheet tại `Public/css/frontend.css`.
- Backend dùng TailAdmin Free 2.5.1 (HTML/Tailwind CSS), không dùng TailAdmin Laravel hay PHP framework. Layout PHP chung nằm tại `Views/Layouts/Backend/`; stylesheet TailAdmin được đóng gói tại `Public/vendor/tailadmin/style.css`, kèm giấy phép MIT tại `Public/vendor/tailadmin/LICENSE`. Tương tác menu responsive dùng JavaScript thuần trong `Public/js/admin.js`.
- Các view xác thực hiện sử dụng layout Bootstrap; dashboard quản trị sử dụng layout TailAdmin. Khi thêm trang mới, khai báo `$pageTitle` (và `$activeAdminPage` cho admin), sau đó include layout tương ứng.

Nguồn TailAdmin Free: [repository](https://github.com/TailAdmin/tailadmin-free-tailwind-dashboard-template), [tài liệu](https://tailadmin.com/docs), [demo miễn phí](https://free-demo.tailadmin.com/). TailAdmin Free được phát hành theo MIT License. Bootstrap được tải qua jsDelivr CDN.
Trình soạn thảo Quill 2.0.3 được tải qua jsDelivr CDN và phát hành theo BSD 3-Clause License.

## Chạy hệ thống xác thực

1. Import `web_cong_ty.sql` vào MariaDB/MySQL. Schema mặc định là `web_cong_ty`.
2. Cấu hình kết nối bằng các biến môi trường `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`. Mặc định: `localhost`, `web_cong_ty`, `root`, mật khẩu rỗng.
3. Mở dự án qua Apache/XAMPP và truy cập `index.php?controller=auth&action=register`.

Các màn hình có sẵn:

- Đăng ký: `index.php?controller=auth&action=register`
- Đăng nhập: `index.php?controller=auth&action=login`
- Hồ sơ, ảnh đại diện và đổi mật khẩu: `index.php?controller=auth&action=profile`
- Khu vực quản trị (chỉ role `0`): `index.php?controller=admin&action=dashboard`
- Giới thiệu công khai: `index.php?controller=about&action=index`
- Hỏi/Đáp công khai: `index.php?controller=faq&action=index`
- Quản lý Giới thiệu (admin): `index.php?controller=adminAbout&action=index`
- Quản lý Hỏi/Đáp (admin): `index.php?controller=adminFaq&action=index`

Tài khoản đăng ký mới luôn có role thành viên (`1`). Để cấp quyền quản trị, cập nhật cột `role` thành `0` cho tài khoản đó trong bảng `users`. Ảnh tải lên là JPG, PNG hoặc WebP, tối đa 2 MB.

Các trang quản trị Giới thiệu và Hỏi/Đáp yêu cầu đã import `about_sections` và các cột `sort_order`, `created_at` của bảng `faqs` từ `web_cong_ty.sql`.

Đăng nhập sử dụng PHP session mặc định, lưu session trên máy chủ; schema không tạo bảng `sessions`.

## Material
- **Link LaTeX Report:** [Assigment_Report](https://prism.openai.com/?u=bf57d44a-8b72-490d-ba55-219b7216ca92&pg=1&m=main.tex&d=7)
