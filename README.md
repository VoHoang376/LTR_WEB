# LTR_WEB

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
