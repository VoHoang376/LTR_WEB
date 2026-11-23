-- =====================================================================
-- BTL Lập trình web – Phần #3: dữ liệu mẫu để demo
-- Chạy SAU khi đã có cấu trúc DB (web_cong_ty.sql + task3_migration.sql):
--   mysql -u root web_cong_ty < Database/seed_task3.sql
--
-- Tài khoản mẫu (mật khẩu lưu bằng password_hash của PHP):
--   admin@ltr.vn   / Admin@123   (quản trị viên, role = 0)
--   member1@ltr.vn / Member@123  (thành viên)
--   member2@ltr.vn / Member@123  (thành viên)
-- =====================================================================

SET NAMES utf8mb4;
SET time_zone = '+07:00';

-- ---------------------------------------------------------------- users
INSERT INTO `users` (`id`, `email`, `password`, `fullname`, `phone`, `address`, `avatar`, `role`, `status`, `created_at`) VALUES
(1, 'admin@ltr.vn', '$2y$10$lEBNzgNsCbHiTVm31pPls.plxdiS8NsFlhBdXNclAyZFetW43JFAe', 'Quản trị viên', '0901234567', 'Số 1 Đại Cồ Việt, Hai Bà Trưng, Hà Nội', 'default_avatar.png', 0, 1, '2026-10-01 08:00:00'),
(2, 'member1@ltr.vn', '$2y$10$pOhS1C2wU8rkb6CTaJ.0aOLgGO7mF2YcS1Ptc22/D3kdJZVPGyGoi', 'Nguyễn Văn An', '0912345678', 'Số 25 Nguyễn Trãi, Thanh Xuân, Hà Nội', 'default_avatar.png', 1, 1, '2026-10-05 09:15:00'),
(3, 'member2@ltr.vn', '$2y$10$pOhS1C2wU8rkb6CTaJ.0aOLgGO7mF2YcS1Ptc22/D3kdJZVPGyGoi', 'Trần Thị Bình', '0987654321', 'Số 78 Lê Lợi, Quận 1, TP. Hồ Chí Minh', 'default_avatar.png', 1, 1, '2026-10-07 14:30:00');

-- ----------------------------------------------------------- categories
INSERT INTO `categories` (`id`, `name`, `slug`, `status`) VALUES
(1, 'Máy in & Thiết bị văn phòng', 'may-in-thiet-bi-van-phong', 1),
(2, 'Máy tính & Laptop', 'may-tinh-laptop', 1),
(3, 'Thiết bị mạng', 'thiet-bi-mang', 1),
(4, 'Phụ kiện máy tính', 'phu-kien-may-tinh', 1),
(5, 'Camera an ninh', 'camera-an-ninh', 1);

-- ------------------------------------------------------------- products
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

-- -------------------------------------------------------- product_images
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

-- --------------------------------------------------------------- orders
INSERT INTO `orders` (`id`, `user_id`, `receiver_name`, `total_amount`, `shipping_address`, `receiver_phone`, `payment_method`, `note`, `status`, `created_at`) VALUES
(1, 2, 'Nguyễn Văn An', 5290000.00, 'Số 25 Nguyễn Trãi, Thanh Xuân, Hà Nội', '0912345678', 0, 'Giao giờ hành chính', 3, '2026-11-05 09:20:00'),
(2, 2, 'Nguyễn Văn An', 5870000.00, 'Số 25 Nguyễn Trãi, Thanh Xuân, Hà Nội', '0912345678', 1, NULL, 1, '2026-11-15 15:40:00'),
(3, 3, 'Trần Thị Bình', 16490000.00, 'Số 78 Lê Lợi, Quận 1, TP. Hồ Chí Minh', '0987654321', 0, 'Gọi trước khi giao', 0, '2026-11-19 10:05:00'),
(4, 3, 'Trần Thị Bình', 5990000.00, 'Số 78 Lê Lợi, Quận 1, TP. Hồ Chí Minh', '0987654321', 0, NULL, 4, '2026-11-10 16:30:00');

INSERT INTO `order_details` (`order_id`, `product_id`, `product_name`, `quantity`, `price`) VALUES
(1, 1, 'Máy in laser đen trắng HP LaserJet Pro M404dn', 1, 5290000.00),
(2, 9, 'Bàn phím cơ không dây Logitech MX Keys', 1, 2690000.00),
(2, 7, 'Router Wi-Fi 6 TP-Link Archer AX23', 2, 1590000.00),
(3, 4, 'Laptop Dell Inspiron 15 3530 Core i5', 1, 16490000.00),
(4, 10, 'Màn hình Dell 27 inch P2723D', 1, 5990000.00);
