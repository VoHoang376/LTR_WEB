# LTR_WEB

## Giao diện và template dùng chung

- Frontend dùng Bootstrap 5.3.9. Layout chung nằm tại `Views/Layouts/Frontend/`; các view dùng chung header/footer, navbar responsive và stylesheet tại `Public/css/frontend.css`.
- Backend dùng TailAdmin Free 2.5.1 (HTML/Tailwind CSS), không dùng TailAdmin Laravel hay PHP framework. Layout PHP chung nằm tại `Views/Layouts/Backend/`; stylesheet TailAdmin được đóng gói tại `Public/vendor/tailadmin/style.css`, kèm giấy phép MIT tại `Public/vendor/tailadmin/LICENSE`. Tương tác menu responsive dùng JavaScript thuần trong `Public/js/admin.js`.
- Các view xác thực hiện sử dụng layout Bootstrap; dashboard quản trị sử dụng layout TailAdmin. Khi thêm trang mới, khai báo `$pageTitle` (và `$activeAdminPage` cho admin), sau đó include layout tương ứng.

Nguồn TailAdmin Free: [repository](https://github.com/TailAdmin/tailadmin-free-tailwind-dashboard-template), [tài liệu](https://tailadmin.com/docs), [demo miễn phí](https://free-demo.tailadmin.com/). TailAdmin Free được phát hành theo MIT License. Bootstrap được tải qua jsDelivr CDN.

## Chạy hệ thống xác thực

1. Import `web_cong_ty.sql` vào MariaDB/MySQL. Schema mặc định là `web_cong_ty`.
2. Cấu hình kết nối bằng các biến môi trường `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`. Mặc định: `localhost`, `web_cong_ty`, `root`, mật khẩu rỗng.
3. Mở dự án qua Apache/XAMPP và truy cập `index.php?controller=auth&action=register`.

Các màn hình có sẵn:

- Đăng ký: `index.php?controller=auth&action=register`
- Đăng nhập: `index.php?controller=auth&action=login`
- Hồ sơ, ảnh đại diện và đổi mật khẩu: `index.php?controller=auth&action=profile`
- Khu vực quản trị (chỉ role `0`): `index.php?controller=admin&action=dashboard`

Tài khoản đăng ký mới luôn có role thành viên (`1`). Để cấp quyền quản trị, cập nhật cột `role` thành `0` cho tài khoản đó trong bảng `users`. Ảnh tải lên là JPG, PNG hoặc WebP, tối đa 2 MB.
## Material
- **Link LaTeX Report:** [Assigment_Report](https://prism.openai.com/?u=bf57d44a-8b72-490d-ba55-219b7216ca92&pg=1&m=main.tex&d=7)
