# Phần #3 – Sản phẩm · Giỏ hàng · Đơn hàng

Tài liệu bàn giao cho bài tập lớn Lập trình Web (HK1 2026-2027), phần công việc #3:
giao diện danh sách/chi tiết sản phẩm, giỏ hàng, đặt hàng, cùng các tính năng quản trị
sản phẩm, danh mục và đơn hàng.

Ảnh chụp màn hình cho báo cáo nằm trong [`docs/screenshots`](screenshots).

---

## 1. Phạm vi đã hiện thực

### 1.1. Giao diện người dùng (Bootstrap 5, responsive)

| Chức năng | Mô tả |
| --- | --- |
| Danh sách sản phẩm | Lưới card; tìm theo từ khoá (tên + mô tả); lọc theo danh mục (pill) và khoảng giá; sắp xếp mới nhất / giá tăng / giá giảm / tên A–Z; phân trang 9 sản phẩm/trang; ảnh `loading="lazy"`; badge tồn kho (Còn hàng / Sắp hết / Hết hàng). |
| Chi tiết sản phẩm | Gallery ảnh (ảnh chính + ảnh phụ đổi được), giá, tồn kho, thông số kỹ thuật, mô tả, chọn số lượng có giới hạn theo tồn kho, thêm vào giỏ, sản phẩm cùng danh mục, khối chính sách bán hàng. |
| Giỏ hàng | Lưu trong session (chỉ `product_id => quantity`); thêm/sửa/xoá/xoá tất cả **bằng AJAX (fetch)**; badge và tạm tính tự cập nhật; vẫn POST được khi tắt JavaScript (`<noscript>`). |
| Thanh toán | Chỉ thành viên đã đăng nhập; form thông tin giao hàng (họ tên, SĐT, địa chỉ, ghi chú), chọn COD hoặc chuyển khoản; tóm tắt đơn; trang xác nhận có mã đơn và hướng dẫn chuyển khoản. |
| Đơn hàng của tôi | Danh sách đơn có phân trang + badge trạng thái; chi tiết đơn với timeline trạng thái; tự huỷ đơn khi còn "Chờ xác nhận". |

### 1.2. Quản trị (layout TailAdmin: sidebar tối + topbar + card + bảng)

| Chức năng | Mô tả |
| --- | --- |
| Sản phẩm | Danh sách có **tìm kiếm, lọc danh mục/trạng thái, sắp xếp, phân trang 10/trang**; thêm/sửa/xoá; **upload ảnh đại diện** và **nhiều ảnh phụ**; quản lý **tồn kho**; ẩn/hiện. Sản phẩm đã phát sinh đơn hàng thì **không xoá cứng** mà chuyển sang "Ngừng kinh doanh" để giữ lịch sử. |
| Danh mục | CRUD, slug tự sinh, chặn trùng slug, **chặn xoá khi danh mục còn sản phẩm**. |
| Đơn hàng | Danh sách có lọc trạng thái (kèm số lượng), tìm theo mã/tên/SĐT/email, lọc theo khoảng ngày, phân trang 10/trang; chi tiết đơn; **đổi trạng thái theo đúng luồng**; huỷ đơn tự động **hoàn lại tồn kho**. |

---

## 2. Cấu trúc mã nguồn (phần #3)

```
Core/
  helpers.php                 e(), csrf_field(), csrf_verify(), upload_image(), slugify(),
                              format_price(), paginate(), render_pagination(), flash..., query_url()
  Permission.php              Permission::requireLogin() / requireAdmin() (đọc role từ DB)
Models/
  ProductModel.php            search (tìm/lọc/sắp xếp/phân trang), CRUD, ảnh phụ, tồn kho
  CategoryModel.php           CRUD danh mục, đếm sản phẩm
  CartModel.php               giỏ hàng trong session
  OrderModel.php              tạo đơn (transaction), đọc đơn, đổi trạng thái
Controllers/
  ProductController.php       danh sách + chi tiết
  CartController.php          JSON cho AJAX + fallback POST thường
  CheckoutController.php      validate, tạo đơn, trang xác nhận
  OrderController.php         lịch sử đơn của thành viên
  AdminProductController.php  CRUD sản phẩm, upload ảnh
  AdminCategoryController.php CRUD danh mục
  AdminOrderController.php    danh sách + đổi trạng thái đơn
Views/
  Frontends/partials/         header (navbar, SEO meta) + footer
  Frontends/products/         index.php, show.php
  Frontends/cart/             index.php
  Frontends/checkout/         index.php, success.php
  Frontends/orders/           index.php, show.php
  Frontends/errors/           404.php
  Backends/layouts/           header.php, footer.php
  Backends/_menu.php          menu sidebar (mỗi người thêm nhóm của mình)
  Backends/products/          index.php, form.php
  Backends/categories/        index.php, form.php
  Backends/orders/            index.php, show.php
Public/
  css/frontend.css, css/admin.css
  js/cart.js (AJAX giỏ hàng), js/validate.js (validate client, preview ảnh), js/admin.js
  uploads/products/           ảnh sản phẩm (kể cả ảnh mẫu .svg)
  img/no-image.svg, img/favicon.svg
  vendor/                     Bootstrap 5.3.3 + Bootstrap Icons (bản local, chạy offline)
Database/
  task3_migration.sql         nâng cấp CSDL cho nhóm đã import dump cũ
  seed_task3.sql              dữ liệu mẫu (users, categories, products, orders)
docs/screenshots/             ảnh chụp màn hình cho báo cáo
```

---

## 3. Thiết kế cơ sở dữ liệu (phần #3)

### 3.1. Bảng sử dụng

| Bảng | Ý nghĩa | Trường chính |
| --- | --- | --- |
| `categories` | Danh mục sản phẩm | `id`, `name`, `slug`, `status` |
| `products` | Sản phẩm | `id`, `category_id`, `name`, `slug`, `price`, **`stock`**, `thumbnail`, `description`, `specifications`, `status`, `created_at` |
| `product_images` | Ảnh phụ (gallery) | `id`, `product_id`, `image`, `sort_order` |
| `orders` | Đơn hàng | `id`, `user_id`, **`receiver_name`**, `total_amount`, `shipping_address`, `receiver_phone`, **`payment_method`**, **`note`**, `status`, `created_at` |
| `order_details` | Chi tiết đơn | `order_id`, `product_id`, **`product_name`**, `quantity`, `price` |
| `users` | Tài khoản (dùng chung) | `id`, `email`, `password`, `fullname`, `role`, `status` |

### 3.2. Thay đổi so với dump gốc `web_cong_ty.sql`

Các thay đổi đều **bổ sung** (không phá vỡ phần của người khác):

1. `products.stock` – số lượng tồn kho (kiểm tra khi thêm giỏ và trừ khi đặt hàng).
2. `product_images` – bảng mới cho thư viện ảnh phụ của sản phẩm.
3. `orders.receiver_name`, `orders.payment_method`, `orders.note` – phục vụ thanh toán.
4. `orders.status` mở rộng thành 5 bước:
   `0 Chờ xác nhận → 1 Đã xác nhận → 2 Đang giao → 3 Hoàn tất`, huỷ = `4`.
5. `order_details.product_name` – lưu tên sản phẩm tại **thời điểm mua** (sản phẩm có thể đổi tên/xoá).
6. Thêm chỉ mục: `idx_orders_status`, `idx_orders_created_at`, `idx_products_status`, `idx_products_price`.

> Nếu nhóm đã import dump cũ: chạy `Database/task3_migration.sql` (đã kiểm chứng cho ra cấu
> trúc **trùng khớp** với dump mới).

### 3.3. Quan hệ

```
users 1 ─── n orders 1 ─── n order_details n ─── 1 products n ─── 1 categories
products 1 ─── n product_images
```

---

## 4. Luồng nghiệp vụ quan trọng

### 4.1. Đặt hàng (transaction + khoá dòng)

`OrderModel::create()`:

1. `beginTransaction()`.
2. `SELECT ... FROM products WHERE id IN (...) FOR UPDATE` – khoá các dòng sản phẩm, tránh hai
   người mua cùng lúc làm âm tồn kho.
3. Đọc lại **giá, tồn kho, trạng thái** từ CSDL; từ chối nếu sản phẩm ngừng bán, số lượng > tồn kho.
4. `INSERT orders` (tổng tiền tính từ giá trong CSDL) → `INSERT order_details` (lưu `product_name`
   và đơn giá tại thời điểm mua).
5. `UPDATE products SET stock = stock - :qty WHERE id = :id AND stock >= :qty` – nếu `rowCount()`
   khác 1 thì ném lỗi để rollback.
6. `commit()`; mọi `Throwable` → `rollBack()` và trả thông báo thân thiện cho người dùng.

### 4.2. Trạng thái đơn hàng

Luồng hợp lệ được khai báo tập trung trong `OrderModel::TRANSITIONS`:

```
0 Chờ xác nhận → 1 Đã xác nhận → 2 Đang giao → 3 Hoàn tất
0/1 → 4 Đã huỷ (hoàn lại tồn kho đúng một lần)
```

Chuyển trạng thái không hợp lệ bị chặn ở phía server (kể cả khi sửa request). Thành viên chỉ được
huỷ đơn của mình khi đơn còn "Chờ xác nhận".

### 4.3. Giỏ hàng

Session chỉ lưu `product_id => quantity`. Mỗi lần hiển thị/đặt hàng, controller nạp lại sản phẩm
từ CSDL → **giá luôn là giá thật**, sản phẩm bị ẩn/xoá tự động bị loại khỏi giỏ.

---

## 5. Bảo mật đã hiện thực

| Lỗ hổng | Cách phòng chống trong bài | Nơi áp dụng |
| --- | --- | --- |
| SQL Injection | 100% truy vấn dùng PDO prepared statement; tham số `LIMIT/OFFSET` bind kiểu `PARAM_INT`; sắp xếp dùng whitelist `ProductModel::SORTS`; `LIKE` được escape ký tự `%`, `_`. | Tất cả model |
| XSS | Mọi output qua `e()` (`htmlspecialchars` với `ENT_QUOTES`); tên sản phẩm chứa `<script>` đã được kiểm thử và in ra dạng `&lt;script&gt;`. | Tất cả view |
| CSRF | `csrf_field()` trong mọi form POST; `csrf_verify()` dùng `hash_equals`; token 32 byte. Thiếu token → 419 (AJAX) hoặc chặn thao tác. Áp dụng cả cho giỏ hàng, đặt hàng, huỷ đơn, xoá sản phẩm/danh mục, đổi trạng thái. | Controller + view |
| Sửa giá từ client | Không nhận giá từ form/session; giá đọc lại từ CSDL **trong transaction** khi tạo đơn; đơn giá lưu vào `order_details`. | `OrderModel::create` |
| Số lượng âm / vượt tồn kho | Ép kiểu số nguyên, giới hạn 1–99, so với `stock` **ở cả JS (`max`, `data-max`) và PHP**; kiểm tra lại lần cuối bằng `UPDATE ... AND stock >= :qty`. | Cart/Checkout/OrderModel |
| Upload file nguy hiểm | Chỉ `upload_image()`: giới hạn 2 MB, kiểm tra MIME thật bằng `finfo` + `getimagesize`, chỉ nhận JPG/PNG/WebP, đổi tên ngẫu nhiên 16 byte, lưu ngoài đường dẫn do người dùng nhập; file giả mạo bị từ chối (đã test). | Admin sản phẩm |
| Phân quyền | `Permission::requireAdmin()` ở **constructor** của cả 3 controller quản trị (không quên action nào); role/status đọc lại từ DB nên tài khoản bị khoá mất quyền ngay; 403 khi không đủ quyền. | Admin |
| Xem dữ liệu người khác | Đơn hàng của thành viên luôn lọc `WHERE user_id = :current_user`; xem đơn người khác trả 404. | `OrderModel::findForUser` |
| Open redirect | Sau đăng nhập chỉ chấp nhận URL nội bộ dạng `index.php?...`; không dùng `HTTP_REFERER` để chuyển hướng. | `AuthController::takeRedirectTarget`, `CartController` |
| Spam / gửi trùng | Giới hạn 5 giây giữa hai lần đặt hàng trong cùng phiên; giỏ hàng chặn số lượng > 99. | `CheckoutController` |
| Lộ thông tin lỗi | Lỗi CSDL bắt bằng `RuntimeException`/`Throwable`, hiển thị thông báo thân thiện. | Checkout/Admin |

---

## 6. Kiểm tra dữ liệu hai phía

- **Client (JS + HTML5)**: `required`, `minlength`, `maxlength`, `pattern="0[0-9]{9}"`,
  `min/max` cho tồn kho; `Public/js/validate.js` bật `.was-validated` của Bootstrap và focus ô lỗi
  đầu tiên; chặn chọn ảnh > 2 MB và xem trước ảnh; `data-confirm` cho thao tác xoá.
- **Server (PHP)**: kiểm tra độ dài, định dạng email/SĐT, khoảng giá, tồn kho, slug trùng, trạng
  thái hợp lệ, số lượng, CSRF. Thông báo lỗi hiển thị ngay cạnh từng trường.

---

## 7. SEO

- Mỗi trang có `title` + `meta description` riêng (theo danh mục/từ khoá/sản phẩm).
- Thẻ Open Graph: `og:site_name`, `og:type`, `og:title`, `og:description`, `og:image` (trang chi tiết).
- Dữ liệu cấu trúc **JSON-LD `Product`** (tên, ảnh, SKU, giá, tình trạng còn hàng) cho trang chi tiết.
- URL sản phẩm dạng slug: `index.php?controller=product&action=show&slug=...`; `slug` tự sinh từ
  tên tiếng Việt có dấu (`slugify()`), kiểm tra trùng.
- `alt` cho ảnh, `aria-label` cho nút/icon, breadcrumb, `caption` cho bảng, skip-link.

---

## 8. Kiểm thử đã thực hiện

### 8.1. Kiểm thử tự động end-to-end (PHP built-in server + MariaDB thật)

Bộ test gọi HTTP thật (có cookie/phiên, có CSRF) và kiểm tra cả CSDL sau mỗi thao tác:

- **70/70 test case đạt** – gồm:
  - Danh sách: tìm kiếm, lọc giá, lọc danh mục, sắp xếp, phân trang, trang 2.
  - Chi tiết: đúng slug, 404 với slug sai, gallery, JSON-LD.
  - Giỏ hàng: thêm (AJAX), chặn vượt tồn kho (422), cập nhật số lượng, xoá, thiếu CSRF (419).
  - Đặt hàng: chặn SĐT sai (không tạo đơn), tạo đơn đúng, tổng tiền lấy từ DB, lưu đơn giá,
    trừ tồn kho, lịch sử đơn, chặn xem đơn người khác (404), huỷ đơn + hoàn tồn kho, giỏ trống.
  - Quản trị: tìm kiếm/lọc sản phẩm, thêm/sửa/xoá sản phẩm, slug tự sinh, chặn trùng slug,
    sản phẩm có đơn hàng chỉ bị ẩn, thiếu CSRF bị chặn, CRUD danh mục, chặn xoá danh mục còn
    sản phẩm, lọc/tìm đơn, đổi trạng thái đúng luồng, chặn chuyển sai luồng,
    thành viên thường nhận 403, khách bị chuyển tới đăng nhập.
  - Bảo mật: chèn `<script>` vào tên sản phẩm → output escape; thử SQL injection → không lỗi.

### 8.2. Upload ảnh

- Upload ảnh đại diện → lưu đúng tên ngẫu nhiên, file tồn tại trong `Public/uploads/products`.
- Upload 2 ảnh phụ → tạo 2 bản ghi `product_images` + 2 file.
- Xoá ảnh phụ → xoá cả bản ghi và file trên đĩa.
- Upload file `.png` giả (nội dung text) → bị từ chối, không tạo sản phẩm.
- Xoá sản phẩm → dọn ảnh đại diện + ảnh phụ khỏi ổ đĩa.

### 8.3. Chuẩn W3C

Dùng **Nu HTML Checker** (`https://validator.w3.org/nu/`) kiểm tra 12 trang render thực tế
(danh sách, chi tiết, giỏ hàng, thanh toán, đơn hàng, 404, 4 trang quản trị, chi tiết đơn):

```
product-list  errors=0 warnings=0      admin-products      errors=0 warnings=0
product-detail errors=0 warnings=0     admin-product-form  errors=0 warnings=0
cart          errors=0 warnings=0      admin-categories    errors=0 warnings=0
checkout      errors=0 warnings=0      admin-orders        errors=0 warnings=0
orders        errors=0 warnings=0      admin-order-detail  errors=0 warnings=0
order-detail  errors=0 warnings=0      404                 errors=0 warnings=0
```

### 8.4. Responsive

Đã chụp và kiểm tra giao diện ở 1366×900 và 390×844 (điện thoại):
`docs/screenshots/frontend-product-list.png` và `frontend-product-list-mobile.png`.
Grid tự chuyển 3 → 2 → 1 cột, bảng quản trị cuộn ngang, sidebar quản trị thu gọn thành menu mở/đóng.

### 8.5. Trình duyệt

Kiểm thử render trên Chromium (Microsoft Edge headless). Trước khi demo nên mở lại bằng
Firefox và Chrome để đối chiếu (không dùng API đặc thù trình duyệt).

---

## 9. Việc cần báo cả nhóm

Các file **dùng chung** đã bị sửa (cần thông báo khi ghép nhánh):

1. `index.php` – thêm route cho `product`, `cart`, `checkout`, `order` và bảng route quản trị
   (`admin&action=products|categories|orders|...`); route `auth` giữ nguyên.
2. `Controllers/AuthController.php` – thêm `takeRedirectTarget()`: sau khi đăng nhập, quay lại
   trang trước đó nếu là URL nội bộ (ví dụ bấm "Thanh toán" khi chưa đăng nhập). Không ảnh hưởng
   luồng cũ.
3. `web_cong_ty.sql` – bổ sung cột/bảng của phần #3 (mục 3.2).
4. `Core/helpers.php`, `Core/Permission.php` – hàm/quy ước dùng chung mà trang 6 tài liệu phân công
   yêu cầu. Mọi hàm được bọc `function_exists()` và `Permission` bọc `class_exists()`, nên nếu
   phần công việc chung bổ sung bản khác thì không xung đột fatal error.
5. `Views/Backends/_menu.php` – menu sidebar quản trị; các bạn khác thêm nhóm menu của mình vào mảng
   `$menuGroups`.

Ghi chú: `Public/vendor/` chứa Bootstrap 5.3.3 và Bootstrap Icons (bản local) để chạy offline.

### Gợi ý tạo nhánh và commit

```bash
git checkout -b feature/san-pham-gio-hang
git add Core Models Views Public Database docs README.md \
        Controllers/CartController.php Controllers/CheckoutController.php \
        Controllers/OrderController.php Controllers/ProductController.php \
        Controllers/AdminProductController.php Controllers/AdminCategoryController.php \
        Controllers/AdminOrderController.php
git add index.php Controllers/AuthController.php web_cong_ty.sql   # file dùng chung: nhớ báo nhóm
git commit -m "Phần #3: sản phẩm, giỏ hàng, đặt hàng, đơn hàng + quản trị"
```

### Ghi chú thêm

- `Views/Backends/dashboard.php` vẫn là trang tạm của phần công việc chung (chưa dùng layout
  quản trị mới). Khi phần chung hoàn thiện, chỉ cần bọc nội dung bằng
  `Views/Backends/layouts/header.php` + `footer.php` là đồng bộ giao diện; menu "Tổng quan" đã trỏ
  sẵn tới `admin&action=dashboard`.
- Khi Người 1 làm trang chủ, thêm mục "Trang chủ" vào `Views/Frontends/partials/header.php` và đổi
  `href` của logo thành `index.php`.
- Trang chủ hiện chưa có nên route mặc định của `index.php` (khi không có `controller`) vẫn giữ
  nguyên hành vi cũ: chuyển tới đăng nhập/hồ sơ.

---

## 10. Gợi ý dàn ý báo cáo cho phần #3

1. **Giới thiệu** (phần chung của nhóm): website công ty – doanh nghiệp, mô hình MVC tự viết.
2. **Cơ sở lý thuyết** (phần #4 chủ trì, phần #3 đóng góp): Bootstrap 5, AJAX/fetch, PDO và
   prepared statement, mô hình session, transaction & khoá dòng trong MySQL.
3. **Thiết kế ứng dụng**:
   - Sơ đồ ERD (mục 3.3) + giải thích ý nghĩa từng bảng/trường của `products`, `product_images`,
     `categories`, `orders`, `order_details`.
   - Cấu trúc mã nguồn (mục 2) và luồng request: `index.php` (router) → Controller → Model → View.
   - Flowchart: *Đặt hàng* (mục 4.1) và *Đổi trạng thái đơn* (mục 4.2).
4. **Bảo mật** (phần #3 phụ trách chính): bảng ở mục 5, phân tích sâu:
   - Vì sao không được tin giá từ client (demo: sửa `product_id`/giá trong request).
   - Vì sao đặt hàng phải dùng transaction + `FOR UPDATE`.
   - CSRF: token sinh thế nào, kiểm tra ở đâu, vì sao dùng `hash_equals`.
   - Upload file: vì sao kiểm tra `finfo` chứ không tin phần mở rộng/`Content-Type`.
5. **Hiện thực**: đính ảnh trong `docs/screenshots`:
   - `frontend-product-list.png`, `frontend-product-list-mobile.png` – danh sách + responsive.
   - `frontend-product-detail.png` – chi tiết + gallery.
   - `frontend-cart.png` – giỏ hàng AJAX.
   - `frontend-checkout.png`, `frontend-checkout-validate.png` – thanh toán + validate 2 phía.
   - `frontend-orders.png` – lịch sử đơn.
   - `admin-products.png`, `admin-product-form.png`, `admin-categories.png`,
     `admin-orders.png`, `admin-order-detail.png` – CRUD + đổi trạng thái.
6. **Cài đặt**: mục "Cài đặt" trong `README.md` (PHP ≥ 7.4, MySQL, import SQL, cấu hình `DB_*`).
7. **Kiểm thử**: mục 8 (70 test case, W3C 0 lỗi, responsive, trình duyệt).

---

## 11. Checklist "xong" (theo trang 6 tài liệu phân công)

- [x] Mọi form có validate JS **và** validate PHP.
- [x] Mọi form POST có CSRF; query dùng prepared statement; output qua `e()`.
- [x] Trang admin gọi `Permission::requireAdmin()` (đặt trong constructor của controller).
- [x] Danh sách ở admin có phân trang (và tìm kiếm theo yêu cầu đề).
- [x] Ảnh upload lên server qua `upload_image()`.
- [x] Frontend có title/meta description riêng, responsive.
- [x] HTML/CSS qua W3C validator (0 lỗi, 0 cảnh báo); test Chrome/Edge/Firefox.
- [x] Đã có ảnh chụp màn hình (`docs/screenshots`) và dàn ý báo cáo (mục 10).

### Nếu trễ tiến độ thì cắt gì trước

Cắt trước: hiệu ứng trang trí, sản phẩm liên quan, gallery ảnh phụ, lịch sử đơn của thành viên.
Giữ bằng được: danh sách + tìm kiếm/lọc/phân trang, chi tiết, giỏ hàng, đặt hàng (transaction,
giá từ DB), admin CRUD sản phẩm + danh mục, quản lý đơn + đổi trạng thái, validate hai phía,
CSRF, upload ảnh, phân quyền.
