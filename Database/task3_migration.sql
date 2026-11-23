-- =====================================================================
-- BTL Lập trình web – Phần #3 (Sản phẩm · Giỏ hàng · Đơn hàng)
-- Migration bổ sung các cột/bảng còn thiếu so với web_cong_ty.sql ban đầu.
--
-- Cách dùng (chỉ chạy MỘT lần trên DB đã import web_cong_ty.sql cũ):
--   mysql -u root web_cong_ty < Database/task3_migration.sql
--
-- Lưu ý: file web_cong_ty.sql trong repo đã được cập nhật đầy đủ các thay đổi
-- dưới đây, nên nếu import lại từ đầu thì KHÔNG cần chạy file này.
-- =====================================================================

SET NAMES utf8mb4;

-- 1. Sản phẩm: thêm tồn kho (phục vụ kiểm tra số lượng và trừ kho khi đặt hàng)
ALTER TABLE `products`
  ADD COLUMN `stock` int(11) NOT NULL DEFAULT 0 COMMENT 'Số lượng tồn kho' AFTER `price`,
  MODIFY COLUMN `status` tinyint(4) DEFAULT 1 COMMENT '0: Ngừng kinh doanh, 1: Đang bán',
  ADD KEY `idx_products_status` (`status`),
  ADD KEY `idx_products_price` (`price`);

-- 2. Đơn hàng: thêm tên người nhận, phương thức thanh toán, ghi chú
--    và mở rộng trạng thái thành 5 bước theo nghiệp vụ.
ALTER TABLE `orders`
  ADD COLUMN `receiver_name` varchar(100) NOT NULL DEFAULT '' AFTER `user_id`,
  ADD COLUMN `payment_method` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: COD, 1: Chuyển khoản' AFTER `receiver_phone`,
  ADD COLUMN `note` varchar(255) DEFAULT NULL AFTER `payment_method`,
  MODIFY COLUMN `status` tinyint(4) NOT NULL DEFAULT 0
    COMMENT '0: Chờ xác nhận, 1: Đã xác nhận, 2: Đang giao, 3: Hoàn tất, 4: Đã hủy',
  ADD KEY `idx_orders_status` (`status`),
  ADD KEY `idx_orders_created_at` (`created_at`);

-- 3. Chi tiết đơn: lưu tên sản phẩm tại thời điểm mua (sản phẩm có thể đổi tên/xoá)
ALTER TABLE `order_details`
  ADD COLUMN `product_name` varchar(255) NOT NULL DEFAULT '' AFTER `product_id`,
  MODIFY COLUMN `price` decimal(15,2) NOT NULL COMMENT 'Đơn giá tại thời điểm mua';

-- 4. Thư viện ảnh phụ của sản phẩm (trang chi tiết dạng gallery)
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
