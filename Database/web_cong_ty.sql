-- =====================================================================
-- CSDL hợp nhất toàn dự án LTR_WEB (BTL Lập trình Web – HK1 2026-2027)
-- Bao gồm đầy đủ cấu trúc chung, phần #1, #2, #3 (đã gộp task3_migration)
-- và phần #4, kèm phân quyền 3 vai trò tách biệt & dữ liệu mẫu.
--
-- Tài khoản mẫu (password_hash PHP):
--   admin@ltr.vn   / Admin@123   (Quản trị viên hệ thống, role = 0)
--   member1@ltr.vn / Member@123  (Thành viên / Khách hàng, role = 1)
--   member2@ltr.vn / Member@123  (Thành viên / Khách hàng, role = 1)
--   staff@ltr.vn   / Staff@123   (Nhân viên chăm sóc khách hàng, role = 2)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `web_cong_ty`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE `web_cong_ty`;

SET NAMES utf8mb4;
SET time_zone = '+07:00';
SET FOREIGN_KEY_CHECKS = 0;

-- Xoá các bảng cũ (nếu có) theo thứ tự ngược khoá ngoại để import lại sạch sẽ
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `order_details`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `product_images`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `faqs`;
DROP TABLE IF EXISTS `about_sections`;
DROP TABLE IF EXISTS `contacts`;
DROP TABLE IF EXISTS `pricing_plans`;
DROP TABLE IF EXISTS `company_info`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `users`;

-- ---------------------------------------------------------------------
-- 1. Người dùng & Phân quyền (3 vai trò tách biệt: 0 Admin, 1 Member, 2 Care Staff)
-- ---------------------------------------------------------------------
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT 'default_avatar.png',
  `role` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Quản trị viên, 1: Thành viên, 2: Nhân viên CSKH',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Bị khóa, 1: Hoạt động',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. Cấu hình công ty, Liên hệ & Bảng giá (Thành viên #1 + CSKH xử lý liên hệ)
-- ---------------------------------------------------------------------
CREATE TABLE `settings` (
  `key_name` varchar(100) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `key_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`key_name`),
  KEY `idx_settings_updated_by` (`updated_by`),
  CONSTRAINT `settings_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `company_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_company_info_setting_key` (`setting_key`),
  KEY `idx_company_info_updated_by` (`updated_by`),
  CONSTRAINT `company_info_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `handled_by` int(11) DEFAULT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(191) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `reply_message` text DEFAULT NULL COMMENT 'Nội dung phản hồi của nhân viên CSKH',
  `replied_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Chưa xử lý, 1: Đã phản hồi',
  `is_read` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Chưa đọc, 1: Đã đọc',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_contacts_handled_by` (`handled_by`),
  KEY `idx_contacts_status_created` (`status`, `created_at`),
  CONSTRAINT `contacts_ibfk_1` FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pricing_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `billing_cycle` varchar(50) NOT NULL DEFAULT 'tháng',
  `description` varchar(255) DEFAULT NULL,
  `features` text DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Ẩn, 1: Hiển thị',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_pricing_status_sort` (`status`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. Giới thiệu & Hỏi/Đáp FAQ (Thành viên #2 + User gửi câu hỏi, CSKH trả lời)
-- ---------------------------------------------------------------------
CREATE TABLE `about_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_title` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL COMMENT 'Đường dẫn tương đối ảnh tải lên trong Public/uploads',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_about_sections_sort_order` (`sort_order`, `id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL COMMENT 'ID người gửi (NULL nếu khách vãng lai)',
  `asker_name` varchar(100) DEFAULT NULL COMMENT 'Tên người đặt câu hỏi',
  `asker_email` varchar(191) DEFAULT NULL COMMENT 'Email người đặt câu hỏi',
  `category` varchar(100) DEFAULT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text DEFAULT NULL COMMENT 'Câu trả lời của nhân viên CSKH',
  `answered_by` int(11) DEFAULT NULL,
  `answered_at` timestamp NULL DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Chờ trả lời, 1: Đã trả lời & Hiển thị, 2: Đã ẩn',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_faqs_created_by` (`created_by`),
  KEY `idx_faqs_answered_by` (`answered_by`),
  KEY `idx_faqs_sort_order` (`status`, `sort_order`, `id`),
  CONSTRAINT `faqs_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `faqs_ibfk_2` FOREIGN KEY (`answered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. Danh mục, Sản phẩm, Ảnh phụ, Đơn hàng (hỗ trợ cả khách vãng lai) & Đánh giá
-- ---------------------------------------------------------------------
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Ẩn, 1: Hiển thị',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug` (`slug`),
  KEY `idx_categories_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0 COMMENT 'Số lượng tồn kho',
  `thumbnail` varchar(255) DEFAULT NULL COMMENT 'Tên tệp ảnh tải lên trong Public/uploads/products',
  `description` text DEFAULT NULL,
  `specifications` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Ngừng kinh doanh, 1: Đang bán',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_products_slug` (`slug`),
  KEY `idx_products_category` (`category_id`),
  KEY `idx_products_status` (`status`),
  KEY `idx_products_price` (`price`),
  KEY `idx_products_catalog` (`status`, `category_id`, `created_at`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_product_images_product` (`product_id`),
  CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'NULL nếu khách chưa đăng nhập đặt hàng',
  `receiver_name` varchar(100) NOT NULL DEFAULT '',
  `receiver_email` varchar(191) DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `shipping_address` varchar(255) NOT NULL,
  `receiver_phone` varchar(20) NOT NULL,
  `payment_method` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: COD, 1: Chuyển khoản',
  `note` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Chờ xác nhận, 1: Đã xác nhận, 2: Đang giao, 3: Hoàn tất, 4: Đã hủy',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_orders_user` (`user_id`),
  KEY `idx_orders_status` (`status`),
  KEY `idx_orders_created_at` (`created_at`),
  KEY `idx_orders_status_created` (`status`, `created_at`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_details` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL DEFAULT '',
  `quantity` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL COMMENT 'Đơn giá tại thời điểm mua',
  PRIMARY KEY (`order_id`, `product_id`),
  KEY `idx_order_details_product` (`product_id`),
  CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL DEFAULT 5,
  `comment` text DEFAULT NULL,
  `staff_reply` text DEFAULT NULL COMMENT 'Phản hồi của nhân viên CSKH',
  `replied_by` int(11) DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Chờ duyệt, 1: Hiển thị, 2: Đã ẩn',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_reviews_user` (`user_id`),
  KEY `idx_reviews_product` (`product_id`),
  KEY `idx_reviews_replied_by` (`replied_by`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`replied_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. Tin tức / Bài viết & Bình luận (Thành viên #4)
-- ---------------------------------------------------------------------
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL COMMENT 'Tên tệp ảnh tải lên trong Public/uploads/posts',
  `content` longtext NOT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(320) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Nháp, 1: Đã xuất bản',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_posts_slug` (`slug`),
  KEY `idx_posts_user` (`user_id`),
  KEY `idx_posts_status_created` (`status`, `created_at`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1: Đang hiển thị, 2: Đã gỡ (ẩn)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_comments_post_status_created` (`post_id`, `status`, `created_at`),
  KEY `idx_comments_user` (`user_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- DỮ LIỆU MẪU TOÀN HỆ THỐNG (ĐỂ CHẠY THỬ NGAY)
-- =====================================================================

-- Users (0: Admin, 1: Member, 2: Care Staff)
INSERT INTO `users` (`id`, `email`, `password`, `fullname`, `phone`, `address`, `avatar`, `role`, `status`, `created_at`) VALUES
(1, 'admin@ltr.vn', '$2y$10$lEBNzgNsCbHiTVm31pPls.plxdiS8NsFlhBdXNclAyZFetW43JFAe', 'Quản trị viên', '0901234567', '268 Lý Thường Kiệt, Phường 14, Quận 10, TP. Hồ Chí Minh', 'default_avatar.png', 0, 1, '2026-10-01 08:00:00'),
(2, 'member1@ltr.vn', '$2y$10$pOhS1C2wU8rkb6CTaJ.0aOLgGO7mF2YcS1Ptc22/D3kdJZVPGyGoi', 'Nguyễn Văn An', '0912345678', 'Số 25 Nguyễn Trãi, Thanh Xuân, Hà Nội', 'default_avatar.png', 1, 1, '2026-10-05 09:15:00'),
(3, 'member2@ltr.vn', '$2y$10$pOhS1C2wU8rkb6CTaJ.0aOLgGO7mF2YcS1Ptc22/D3kdJZVPGyGoi', 'Trần Thị Bình', '0987654321', 'Số 78 Lê Lợi, Quận 1, TP. Hồ Chí Minh', 'default_avatar.png', 1, 1, '2026-10-07 14:30:00'),
(4, 'staff@ltr.vn', '$2y$10$5hiPKwINbd.pmkVjCGxT1O2cgRfbur7lrc9X837jDknZkgkYa.eQe', 'Lê Thu Hà (CSKH)', '0909888777', '268 Lý Thường Kiệt, Phường 14, Quận 10, TP. Hồ Chí Minh', 'default_avatar.png', 2, 1, '2026-10-02 08:30:00');

-- Settings
INSERT INTO `settings` (`key_name`, `updated_by`, `key_value`) VALUES
('company_name', 1, 'LTR Technology'),
('company_slogan', 1, 'Giải Pháp Công Nghệ & Thiết Bị Văn Phòng Toàn Diện'),
('company_phone', 1, '(028) 3864 7256'),
('company_email', 1, 'contact@ltr.vn'),
('company_address', 1, '268 Lý Thường Kiệt, Phường 14, Quận 10, TP. Hồ Chí Minh'),
('company_hours', 1, 'Thứ 2 – Thứ 6: 8:00 – 17:30 | Thứ 7: 8:00 – 12:00'),
('company_intro', 1, 'Nhà phân phối thiết bị văn phòng, máy tính, hạ tầng mạng và giải pháp camera an ninh chính hãng cho doanh nghiệp. Bảo hành tận nơi, hỗ trợ kỹ thuật 24/7.');

-- Pricing Plans
INSERT INTO `pricing_plans` (`id`, `name`, `price`, `billing_cycle`, `description`, `features`, `is_featured`, `sort_order`, `status`) VALUES
(1, 'Gói Bảo Trì Laptop / PC', 299000.00, 'máy / tháng', 'Phù hợp cho cá nhân và văn phòng nhỏ cần chăm sóc và tối ưu thiết bị định kỳ', 'Vệ sinh phần cứng & tra keo tản nhiệt định kỳ\nCài đặt hệ điều hành và phần mềm bản quyền\nHỗ trợ xử lý lỗi kỹ thuật từ xa qua UltraViewer\nKiểm tra và tối ưu hiệu suất ổ cứng, RAM\nMiễn phí kiểm tra tình trạng pin và bàn phím', 0, 1, 1),
(2, 'Gói Lắp Đặt & Triển Khai Văn Phòng', 1299000.00, 'gói', 'Lựa chọn tối ưu khi setup hoặc nâng cấp hệ sinh thái thiết bị cho công ty', 'Lắp ráp và cài đặt hoàn thiện dàn máy tính văn phòng\nKết nối & chia sẻ máy in không dây nội bộ tốc độ cao\nThi công đường truyền mạng LAN ổn định, bảo mật\nBảo hành 1 đổi 1 linh kiện tận nơi trong 24 giờ\nHỗ trợ kỹ thuật tại chỗ ưu tiên trong ngày', 1, 2, 1),
(3, 'Gói Thuê Thiết Bị IT Trọn Gói', 3490000.00, 'tháng', 'Giải pháp tối ưu chi phí đầu tư thiết bị phần cứng ban đầu cho doanh nghiệp', 'Trang bị dàn Laptop & Máy in văn phòng đời mới\nMiễn phí 100% mực in và vật tư hao mòn hàng tháng\nBảo dưỡng, vệ sinh thiết bị định kỳ tận nơi\nKỹ thuật viên IT hỗ trợ xử lý sự cố tại chỗ 24/7\nĐổi máy mới tương đương lập tức nếu phát sinh lỗi', 0, 3, 1);

-- Contacts mẫu
INSERT INTO `contacts` (`id`, `handled_by`, `fullname`, `email`, `phone`, `subject`, `message`, `reply_message`, `replied_at`, `status`, `is_read`, `created_at`) VALUES
(1, NULL, 'Lê Minh Tuấn', 'tuan.le@doanhnghiep.vn', '0909112233', 'Tư vấn lắp đặt máy in văn phòng', 'Công ty tôi có 30 nhân sự, cần tư vấn lắp đặt 2 máy in laser hai mặt và hệ thống Wi-Fi ổn định cho 2 tầng văn phòng.', NULL, NULL, 0, 0, '2026-11-18 09:30:00'),
(2, 4, 'Phạm Ngọc Lan', 'lan.pham@abc.com.vn', '0918445566', 'Báo giá gói bảo trì định kỳ', 'Xin gửi báo giá chi tiết cho gói bảo trì 15 bộ PC và 5 laptop tại Quận 3, TP.HCM.', 'Chào chị Lan, bộ phận CSKH đã gửi bảng báo giá chi tiết qua email và sẽ gọi xác nhận lịch khảo sát vào sáng mai.', '2026-11-16 15:10:00', 1, 1, '2026-11-16 14:20:00');

-- About Sections
INSERT INTO `about_sections` (`id`, `section_title`, `content`, `image_url`, `sort_order`, `created_at`) VALUES
(1, 'Về LTR Technology', '<p><strong>LTR Technology</strong> là đơn vị chuyên cung cấp giải pháp phần cứng, thiết bị văn phòng, hạ tầng mạng và hệ thống camera an ninh cho doanh nghiệp vừa và nhỏ tại Việt Nam.</p><p>Với hơn 8 năm đồng hành cùng hơn 500 khách hàng doanh nghiệp, chúng tôi cam kết 100% sản phẩm chính hãng, minh bạch hóa đơn VAT và dịch vụ bảo hành tận nơi nhanh chóng.</p>', NULL, 1, '2026-10-10 08:00:00'),
(2, 'Tầm nhìn & Sứ mệnh', '<ul><li><strong>Tầm nhìn:</strong> Trở thành đối tác hạ tầng công nghệ tin cậy hàng đầu cho doanh nghiệp Việt trong kỷ nguyên chuyển đổi số.</li><li><strong>Sứ mệnh:</strong> Tối ưu chi phí đầu tư thiết bị IT và giúp khách hàng vận hành văn phòng ổn định, an toàn, không gián đoạn.</li></ul>', NULL, 2, '2026-10-10 08:05:00'),
(3, 'Giá trị cốt lõi', '<p>Chúng tôi lấy <strong>Chất lượng thật – Hỗ trợ nhanh – Minh bạch chi phí</strong> làm kim chỉ nam trong mọi dự án triển khai, từ đơn hàng thiết bị lẻ đến gói thuê thiết bị văn phòng trọn gói.</p>', NULL, 3, '2026-10-10 08:10:00');

-- FAQs (4 câu đã trả lời + 1 câu người dùng gửi đang chờ CSKH phản hồi)
INSERT INTO `faqs` (`id`, `created_by`, `asker_name`, `asker_email`, `category`, `question`, `answer`, `answered_by`, `answered_at`, `sort_order`, `status`, `created_at`) VALUES
(1, 4, 'LTR Technology', 'staff@ltr.vn', 'Đặt hàng & Giao nhận', 'LTR Technology có giao hàng và lắp đặt tận nơi không?', 'Có. Chúng tôi miễn phí giao hàng và hỗ trợ cài đặt cơ bản trong nội thành TP.HCM và Hà Nội. Với khách hàng tỉnh, đơn hàng được đóng gói chống sốc và gửi chuyển phát nhanh toàn quốc.', 4, '2026-10-12 09:00:00', 1, 1, '2026-10-12 09:00:00'),
(2, 4, 'LTR Technology', 'staff@ltr.vn', 'Bảo hành', 'Chính sách bảo hành sản phẩm như thế nào?', 'Tất cả thiết bị đều được bảo hành chính hãng từ 12 đến 36 tháng theo tiêu chuẩn nhà sản xuất. Lỗi kỹ thuật trong 7 ngày đầu được hỗ trợ đổi mới 1-1 nhanh chóng.', 4, '2026-10-12 09:05:00', 2, 1, '2026-10-12 09:05:00'),
(3, 4, 'LTR Technology', 'staff@ltr.vn', 'Thanh toán', 'Công ty có xuất hóa đơn VAT cho doanh nghiệp không?', 'Có. Chúng tôi xuất hóa đơn điện tử VAT đầy đủ cho mọi đơn hàng doanh nghiệp và hỗ trợ cả thanh toán COD lẫn chuyển khoản ngân hàng.', 4, '2026-10-12 09:10:00', 3, 1, '2026-10-12 09:10:00'),
(4, 4, 'LTR Technology', 'staff@ltr.vn', 'Hỗ trợ kỹ thuật', 'Khi thiết bị gặp sự cố thì tôi liên hệ hỗ trợ bằng cách nào?', 'Bạn có thể gọi trực tiếp hotline (028) 3864 7256 hoặc gửi yêu cầu qua trang Liên hệ. Kỹ thuật viên sẽ hỗ trợ từ xa trong vòng 30 phút hoặc có mặt tận nơi trong ngày.', 4, '2026-10-12 09:15:00', 4, 1, '2026-10-12 09:15:00'),
(5, 2, 'Nguyễn Văn An', 'member1@ltr.vn', 'Bảo hành', 'Nếu mua số lượng 10 laptop cho công ty thì có được chiết khấu và bảo hành tận nơi không?', NULL, NULL, NULL, 10, 0, '2026-11-20 10:00:00');

-- Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `status`) VALUES
(1, 'Máy in & Thiết bị văn phòng', 'may-in-thiet-bi-van-phong', 1),
(2, 'Máy tính & Laptop', 'may-tinh-laptop', 1),
(3, 'Thiết bị mạng', 'thiet-bi-mang', 1),
(4, 'Phụ kiện máy tính', 'phu-kien-may-tinh', 1),
(5, 'Camera an ninh', 'camera-an-ninh', 1);

-- Products
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `price`, `stock`, `thumbnail`, `description`, `specifications`, `status`, `created_at`) VALUES
(1, 1, 'Máy in laser đen trắng HP LaserJet Pro M404dn', 'may-in-laser-hp-laserjet-pro-m404dn', 5290000.00, 12, 'printer.svg',
 'Máy in laser đen trắng khổ A4 dành cho văn phòng vừa và nhỏ, tốc độ 38 trang/phút, in hai mặt tự động giúp tiết kiệm giấy và thời gian. Kết nối mạng LAN và USB, hỗ trợ in bảo mật và in di động.',
 'Tốc độ in: 38 trang/phút\nĐộ phân giải: 1200 x 1200 dpi\nKhổ giấy: A4\nKết nối: USB 2.0, LAN Gigabit\nIn hai mặt: Tự động\nBảo hành: 12 tháng',
 1, '2026-10-20 08:30:00'),
(2, 1, 'Máy in màu đa năng Epson EcoTank L3250', 'may-in-mau-epson-ecotank-l3250', 4890000.00, 8, 'printer.svg',
 'Máy in màu 3 trong 1 (in – scan – copy) sử dụng bình mực EcoTank tiết kiệm chi phí, in được ảnh chất lượng cao và kết nối Wi-Fi tiện lợi cho gia đình và văn phòng nhỏ.',
 'Công nghệ: In phun màu\nChức năng: In, Scan, Copy\nKết nối: Wi-Fi, USB\nKhổ giấy: A4\nBảo hành: 12 tháng',
 1, '2026-10-21 09:00:00'),
(3, 1, 'Máy scan tài liệu Canon DR-C240', 'may-scan-tai-lieu-canon-dr-c240', 12900000.00, 3, 'scanner.svg',
 'Máy scan chuyên dụng cho văn phòng, nạp giấy tự động 60 tờ, tốc độ 45 trang/phút, phù hợp số hoá hồ sơ và tài liệu kế toán.',
 'Tốc độ scan: 45 trang/phút\nBộ nạp giấy: ADF 60 tờ\nĐộ phân giải: 600 dpi\nKết nối: USB 2.0\nBảo hành: 12 tháng',
 1, '2026-10-22 10:15:00'),
(4, 2, 'Laptop Dell Inspiron 15 3530 Core i5', 'laptop-dell-inspiron-15-3530-core-i5', 16490000.00, 6, 'laptop.svg',
 'Laptop văn phòng cấu hình cân bằng với CPU Intel Core i5 thế hệ 13, màn hình 15.6 inch Full HD, bàn phím số tiện dụng cho công việc kế toán và học tập.',
 'CPU: Intel Core i5-1334U\nRAM: 16GB DDR4\nỔ cứng: SSD 512GB NVMe\nMàn hình: 15.6 inch Full HD\nHệ điều hành: Windows 11\nBảo hành: 12 tháng',
 1, '2026-10-25 08:00:00'),
(5, 2, 'Laptop Lenovo ThinkPad E14 Gen 5', 'laptop-lenovo-thinkpad-e14-gen-5', 21990000.00, 4, 'laptop.svg',
 'Laptop doanh nhân bền bỉ, bàn phím tốt nhất phân khúc, bảo mật vân tay và camera che vật lý, phù hợp làm việc di động.',
 'CPU: AMD Ryzen 7 7730U\nRAM: 16GB DDR4\nỔ cứng: SSD 512GB\nMàn hình: 14 inch WUXGA\nBảo mật: Vân tay, TPM 2.0\nBảo hành: 24 tháng',
 1, '2026-10-26 08:00:00'),
(6, 2, 'Máy tính để bàn HP ProDesk 400 G9', 'may-tinh-de-ban-hp-prodesk-400-g9', 13290000.00, 5, 'desktop.svg',
 'Máy tính để bàn doanh nghiệp dạng thùng nhỏ gọn, hoạt động êm ái, dễ nâng cấp và bảo trì, phù hợp cho phòng ban văn phòng.',
 'CPU: Intel Core i5-12500\nRAM: 8GB DDR4 (2 khe)\nỔ cứng: SSD 256GB + HDD 1TB\nCổng: 8 x USB, 2 x DisplayPort\nBảo hành: 12 tháng',
 1, '2026-10-27 08:00:00'),
(7, 3, 'Router Wi-Fi 6 TP-Link Archer AX23', 'router-wi-fi-6-tp-link-archer-ax23', 1590000.00, 30, 'router.svg',
 'Router Wi-Fi 6 tốc độ 1800Mbps, 4 ăng-ten ngoài, hỗ trợ OneMesh và IPv6, đáp ứng tốt nhu cầu làm việc và giải trí cho hộ gia đình.',
 'Chuẩn Wi-Fi: 802.11ax (Wi-Fi 6)\nTốc độ: 1201Mbps (5GHz) + 574Mbps (2.4GHz)\nCổng: 1 WAN + 4 LAN Gigabit\nBảo hành: 24 tháng',
 1, '2026-10-28 08:00:00'),
(8, 3, 'Switch mạng 24 cổng Cisco CBS250-24T', 'switch-mang-24-cong-cisco-cbs250-24t', 6750000.00, 7, 'switch.svg',
 'Switch quản lý được 24 cổng Gigabit dành cho doanh nghiệp, hỗ trợ VLAN, QoS và quản trị qua giao diện web trực quan.',
 'Cổng: 24 x Gigabit Ethernet\nQuản lý: Web, SNMP, CLI\nTính năng: VLAN, QoS, LACP\nBảo hành: 12 tháng',
 1, '2026-10-29 08:00:00'),
(9, 4, 'Bàn phím cơ không dây Logitech MX Keys', 'ban-phim-co-khong-day-logitech-mx-keys', 2690000.00, 20, 'keyboard.svg',
 'Bàn phím cao cấp cho người làm việc nhiều, phím lõm ôm ngón tay, đèn nền tự động, kết nối tới 3 thiết bị và pin dùng 10 ngày.',
 'Kết nối: Bluetooth, USB Receiver\nSố thiết bị: 3\nĐèn nền: Cảm biến tự động\nPin: Sạc USB-C, 10 ngày\nTương thích: Windows, macOS, Linux',
 1, '2026-10-30 08:00:00'),
(10, 4, 'Màn hình Dell 27 inch P2723D', 'man-hinh-dell-27-inch-p2723d', 5990000.00, 9, 'monitor.svg',
 'Màn hình 27 inch độ phân giải QHD, tấm nền IPS màu sắc chính xác, chân đế ergonomic xoay dựng dọc, phù hợp thiết kế đồ hoạ.',
 'Kích thước: 27 inch\nĐộ phân giải: 2560 x 1440 (QHD)\nTấm nền: IPS, 99% sRGB\nCổng: HDMI, DisplayPort, USB Hub\nBảo hành: 36 tháng',
 1, '2026-11-01 08:00:00'),
(11, 5, 'Camera IP Hikvision DS-2CD1043G2', 'camera-ip-hikvision-ds-2cd1043g2', 1890000.00, 25, 'camera.svg',
 'Camera IP 4MP ngoài trời, hồng ngoại 30m, chống nước IP67, hỗ trợ ghi hình liên tục và xem từ xa qua điện thoại.',
 'Độ phân giải: 4MP (2560 x 1440)\nHồng ngoại: 30m\nChống nước: IP67\nKết nối: PoE, ONVIF\nBảo hành: 24 tháng',
 1, '2026-11-02 08:00:00'),
(12, 5, 'Bộ đầu ghi + 4 camera Dahua 2MP', 'bo-dau-ghi-4-camera-dahua-2mp', 7490000.00, 0, 'camera.svg',
 'Trọn bộ giải pháp camera cho cửa hàng và gia đình gồm đầu ghi 4 kênh và 4 camera 2MP, cài đặt đơn giản, xem từ xa qua ứng dụng.',
 'Đầu ghi: 4 kênh XVR, HDD 1TB\nCamera: 4 x 2MP dome\nHồng ngoại: 20m\nTính năng: Xem từ xa, phát hiện chuyển động\nBảo hành: 24 tháng',
 1, '2026-11-03 08:00:00');

-- Product Images
INSERT INTO `product_images` (`product_id`, `image`, `sort_order`) VALUES
(1, 'printer.svg', 0), (1, 'printer-alt.svg', 1), (1, 'printer-detail.svg', 2),
(2, 'printer.svg', 0), (2, 'printer-alt.svg', 1),
(3, 'scanner.svg', 0), (3, 'scanner-alt.svg', 1),
(4, 'laptop.svg', 0), (4, 'laptop-alt.svg', 1), (4, 'laptop-detail.svg', 2),
(5, 'laptop.svg', 0), (5, 'laptop-alt.svg', 1),
(6, 'desktop.svg', 0), (6, 'desktop-alt.svg', 1),
(7, 'router.svg', 0), (7, 'router-alt.svg', 1),
(8, 'switch.svg', 0), (8, 'switch-alt.svg', 1),
(9, 'keyboard.svg', 0), (9, 'keyboard-alt.svg', 1),
(10, 'monitor.svg', 0), (10, 'monitor-alt.svg', 1),
(11, 'camera.svg', 0), (11, 'camera-alt.svg', 1),
(12, 'camera.svg', 0), (12, 'camera-alt.svg', 1);

-- Product Reviews (Phản hồi sản phẩm — CSKH trả lời)
INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `rating`, `comment`, `staff_reply`, `replied_by`, `replied_at`, `status`, `created_at`) VALUES
(1, 2, 1, 5, 'Máy in chạy êm, in hai mặt rất nhanh, cắm LAN vào văn phòng nhận ngay.', 'Cảm ơn anh An đã tin dùng sản phẩm của LTR Technology! Cần hỗ trợ hộp mực thay thế anh cứ liên hệ CSKH nhé.', 4, '2026-11-10 10:30:00', 1, '2026-11-10 09:00:00'),
(2, 3, 4, 4, 'Máy chạy mượt, màn hình đẹp. Cho mình hỏi có hỗ trợ nâng cấp thêm 1 thanh RAM 16GB tại cửa hàng không?', NULL, NULL, NULL, 1, '2026-11-19 11:20:00'),
(3, 2, 7, 5, 'Router Wi-Fi 6 phát sóng xuyên tường tốt, họp trực tuyến không còn bị lag.', NULL, NULL, NULL, 1, '2026-11-20 08:45:00');

-- Orders & Order Details
INSERT INTO `orders` (`id`, `user_id`, `receiver_name`, `receiver_email`, `total_amount`, `shipping_address`, `receiver_phone`, `payment_method`, `note`, `status`, `created_at`) VALUES
(1, 2, 'Nguyễn Văn An', 'member1@ltr.vn', 5290000.00, 'Số 25 Nguyễn Trãi, Thanh Xuân, Hà Nội', '0912345678', 0, 'Giao giờ hành chính', 3, '2026-11-05 09:20:00'),
(2, 2, 'Nguyễn Văn An', 'member1@ltr.vn', 5870000.00, 'Số 25 Nguyễn Trãi, Thanh Xuân, Hà Nội', '0912345678', 1, NULL, 1, '2026-11-15 15:40:00'),
(3, 3, 'Trần Thị Bình', 'member2@ltr.vn', 16490000.00, 'Số 78 Lê Lợi, Quận 1, TP. Hồ Chí Minh', '0987654321', 0, 'Gọi trước khi giao', 0, '2026-11-19 10:05:00'),
(4, 3, 'Trần Thị Bình', 'member2@ltr.vn', 5990000.00, 'Số 78 Lê Lợi, Quận 1, TP. Hồ Chí Minh', '0987654321', 0, NULL, 4, '2026-11-10 16:30:00');

INSERT INTO `order_details` (`order_id`, `product_id`, `product_name`, `quantity`, `price`) VALUES
(1, 1, 'Máy in laser đen trắng HP LaserJet Pro M404dn', 1, 5290000.00),
(2, 9, 'Bàn phím cơ không dây Logitech MX Keys', 1, 2690000.00),
(2, 7, 'Router Wi-Fi 6 TP-Link Archer AX23', 2, 1590000.00),
(3, 4, 'Laptop Dell Inspiron 15 3530 Core i5', 1, 16490000.00),
(4, 10, 'Màn hình Dell 27 inch P2723D', 1, 5990000.00);

-- Posts
INSERT INTO `posts` (`id`, `user_id`, `title`, `slug`, `summary`, `thumbnail`, `content`, `meta_keywords`, `meta_description`, `status`, `created_at`) VALUES
(1, 1, 'Kinh nghiệm chọn máy in laser tiết kiệm chi phí cho văn phòng 20–50 người', 'kinh-nghiem-chon-may-in-laser-van-phong',
 'Hướng dẫn đánh giá tốc độ in, tính năng in 2 mặt tự động và kết nối mạng LAN để tối ưu chi phí vận hành văn phòng.',
 'news-1.svg',
 'Khi trang bị máy in cho văn phòng từ 20 đến 50 nhân sự, yếu tố quan trọng nhất không chỉ là giá mua ban đầu mà còn là chi phí bản in và độ ổn định khi nhiều người cùng gửi lệnh in.\n\n1. Ưu tiên máy có in 2 mặt tự động (Duplex) để giảm gần 50% lượng giấy tiêu thụ mỗi tháng.\n2. Chọn kết nối LAN Gigabit hoặc Wi-Fi nội bộ giúp mọi máy tính dùng chung dễ dàng không cần máy chủ trung gian.\n3. Kiểm tra công suất khuyến nghị hàng tháng và hộp mực thay thế phổ biến như HP LaserJet Pro M404dn.\n\nNếu cần khảo sát trực tiếp nhu cầu in ấn của công ty, đội ngũ LTR Technology luôn sẵn sàng tư vấn miễn phí.',
 'máy in laser, thiết bị văn phòng, HP LaserJet, tiết kiệm chi phí',
 'Hướng dẫn chi tiết cách chọn máy in laser phù hợp cho văn phòng vừa và nhỏ, tiết kiệm mực và giấy in.',
 1, '2026-11-18 08:30:00'),
(2, 1, 'Vì sao doanh nghiệp nên nâng cấp hạ tầng lên chuẩn Wi-Fi 6 trong năm 2026?', 'vi-sao-doanh-nghiep-nen-nang-cap-wifi-6',
 'Wi-Fi 6 mang lại băng thông cao hơn, giảm độ trễ họp trực tuyến và chịu tải tốt khi nhiều thiết bị truy cập đồng thời.',
 'news-2.svg',
 'Trong môi trường làm việc hiện đại với hàng chục laptop, điện thoại và thiết bị IoT kết nối cùng lúc, chuẩn Wi-Fi cũ thường xuyên xảy ra tình trạng rớt mạng hoặc giật lag khi họp trực tuyến.\n\nChuẩn 802.11ax (Wi-Fi 6) với công nghệ OFDMA và MU-MIMO cho phép router phục vụ nhiều thiết bị song song mượt mà hơn, đồng thời tăng cường bảo mật chuẩn WPA3.\n\nKết hợp Switch quản lý VLAN và Router Wi-Fi 6 giúp phân tách mạng nội bộ và mạng khách an toàn tuyệt đối.',
 'wifi 6, thiết bị mạng, nâng cấp mạng văn phòng, router doanh nghiệp',
 'Tìm hiểu lợi ích của chuẩn Wi-Fi 6 đối với hiệu suất làm việc và bảo mật mạng nội bộ doanh nghiệp.',
 1, '2026-11-14 10:15:00'),
(3, 1, 'Giải pháp lắp đặt hệ thống Camera IP an ninh chuẩn IP67 cho kho xưởng', 'giai-phap-camera-ip-an-ninh-ip67',
 'Những tiêu chí cần lưu ý về độ phân giải, hồng ngoại ban đêm và cấp nguồn PoE khi triển khai camera cho nhà xưởng.',
 'news-3.svg',
 'Giám sát kho hàng và bãi xe đòi hỏi hệ thống camera hoạt động bền bỉ 24/7 trong điều kiện bụi bẩn, mưa nắng và thiếu sáng.\n\nSử dụng camera IP chuẩn chống nước IP67 độ phân giải từ 4MP trở lên giúp ghi lại hình ảnh sắc nét cả ban đêm nhờ đèn hồng ngoại tầm xa 30m.\n\nCông nghệ cấp nguồn qua cáp mạng (PoE) giúp rút gọn dây dẫn, dễ bảo trì và đảm bảo tính thẩm mỹ cho công trình.',
 'camera ip, camera an ninh, hikvision, lắp đặt camera kho xưởng',
 'Tư vấn lựa chọn và thi công hệ thống camera IP chuẩn IP67 bền bỉ, rõ nét cho kho xưởng và văn phòng.',
 1, '2026-11-10 14:00:00'),
(4, 1, 'Lợi ích của dịch vụ bảo trì IT định kỳ đối với doanh nghiệp vừa và nhỏ', 'loi-ich-dich-vu-bao-tri-it-dinh-ky',
 'Chủ động phòng ngừa lỗi phần cứng, sao lưu dữ liệu an toàn và giảm thiểu thời gian gián đoạn công việc.',
 'news-4.svg',
 'Nhiều doanh nghiệp nhỏ chỉ gọi kỹ thuật viên khi máy tính bị hỏng nặng hoặc mất dữ liệu, khiến chi phí khắc phục cao gấp nhiều lần.\n\nVới gói bảo trì định kỳ hàng tháng, hệ thống máy tính được vệ sinh tản nhiệt, cập nhật vá lỗi bảo mật và kiểm tra sức khỏe ổ cứng SSD sớm.\n\nNhờ đó nhân sự luôn làm việc trơn tru, kéo dài tuổi thọ thiết bị thêm 2–3 năm.',
 'bảo trì it, sửa chữa máy tính văn phòng, dịch vụ it doanh nghiệp',
 'Khám phá lý do dịch vụ bảo trì IT định kỳ giúp doanh nghiệp tiết kiệm chi phí và vận hành ổn định.',
 1, '2026-11-06 09:45:00');

-- Comments
INSERT INTO `comments` (`id`, `post_id`, `user_id`, `content`, `status`, `created_at`) VALUES
(1, 1, 2, 'Bài viết rất hữu ích! Văn phòng mình vừa mua mẫu HP M404dn bên LTR, in 2 mặt cực nhanh và êm.', 1, '2026-11-18 11:20:00'),
(2, 1, 3, 'Cho mình hỏi mẫu máy in này hộp mực in được khoảng bao nhiêu trang ạ?', 1, '2026-11-19 08:15:00'),
(3, 2, 2, 'Nâng cấp lên Wi-Fi 6 xong họp Zoom ổn định hẳn, cảm ơn đội kỹ thuật đã tư vấn.', 1, '2026-11-20 09:00:00');

COMMIT;
