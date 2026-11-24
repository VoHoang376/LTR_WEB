<?php

/**
 * Phân quyền tách biệt rõ ràng 3 vai trò:
 *   - ROLE_ADMIN  (0): Quản trị viên hệ thống (Sản phẩm, Danh mục, Đơn hàng, Bài viết,
 *                      Giới thiệu, Bảng giá, Cấu hình, Quản lý tài khoản).
 *                      KHÔNG mua hàng/giỏ hàng và KHÔNG vào kênh CSKH.
 *   - ROLE_MEMBER (1): Thành viên / Khách hàng (Mua hàng, Đơn hàng của tôi,
 *                      Đánh giá sản phẩm, Bình luận, Đặt câu hỏi FAQ, Liên hệ).
 *                      KHÔNG vào trang Quản trị hay kênh CSKH.
 *   - ROLE_STAFF  (2): Nhân viên Chăm sóc khách hàng (Giải quyết & trả lời Liên hệ,
 *                      Trả lời FAQ người dùng gửi, Phản hồi đánh giá sản phẩm,
 *                      Kiểm duyệt bình luận).
 *                      KHÔNG mua hàng/giỏ hàng và KHÔNG vào trang Quản trị hệ thống.
 */
if (!class_exists('Permission')) {
    class Permission
    {
        const ROLE_ADMIN = 0;
        const ROLE_MEMBER = 1;
        const ROLE_STAFF = 2;

        private static $db = null;
        private static $user = null;
        private static $loaded = false;

        /** Gán kết nối PDO cho lớp này (gọi một lần ở index.php). */
        public static function setDb(PDO $db)
        {
            self::$db = $db;
            self::$user = null;
            self::$loaded = false;
        }

        private static function db()
        {
            if (self::$db instanceof PDO) {
                return self::$db;
            }
            if (isset($GLOBALS['db']) && $GLOBALS['db'] instanceof PDO) {
                return $GLOBALS['db'];
            }
            throw new RuntimeException('Permission chưa được cấu hình kết nối CSDL.');
        }

        /** Người dùng đang đăng nhập (mảng dữ liệu) hoặc null. */
        public static function user()
        {
            if (self::$loaded) {
                return self::$user;
            }
            self::$loaded = true;
            self::$user = null;

            if (empty($_SESSION['user_id'])) {
                return null;
            }

            $statement = self::db()->prepare(
                'SELECT id, email, fullname, phone, address, avatar, role, status
                 FROM users WHERE id = :id LIMIT 1'
            );
            $statement->execute(['id' => (int) $_SESSION['user_id']]);
            $user = $statement->fetch();

            if (!$user || (int) $user['status'] !== 1) {
                unset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['fullname']);

                return null;
            }

            self::$user = $user;
            $_SESSION['role'] = (int) $user['role'];
            $_SESSION['fullname'] = $user['fullname'];

            return self::$user;
        }

        public static function id()
        {
            $user = self::user();

            return $user === null ? null : (int) $user['id'];
        }

        public static function role()
        {
            $user = self::user();

            return $user === null ? null : (int) $user['role'];
        }

        public static function roleLabel($role)
        {
            $labels = [
                self::ROLE_ADMIN => 'Quản trị viên',
                self::ROLE_MEMBER => 'Thành viên',
                self::ROLE_STAFF => 'Nhân viên CSKH',
            ];

            return $labels[(int) $role] ?? 'Không xác định';
        }

        public static function isLoggedIn()
        {
            return self::user() !== null;
        }

        public static function isAdmin()
        {
            return self::role() === self::ROLE_ADMIN;
        }

        public static function isMember()
        {
            return self::role() === self::ROLE_MEMBER;
        }

        public static function isStaff()
        {
            return self::role() === self::ROLE_STAFF;
        }

        /** Chỉ khách vãng lai hoặc Thành viên (role = 1) mới được mua hàng / dùng giỏ hàng. */
        public static function canShop()
        {
            $role = self::role();

            return $role === null || $role === self::ROLE_MEMBER;
        }

        /** Bắt buộc đăng nhập (bất kỳ vai trò nào, ví dụ trang Hồ sơ cá nhân). */
        public static function requireLogin()
        {
            if (self::isLoggedIn()) {
                return self::user();
            }

            $_SESSION['redirect_after_login'] = self::currentUrl();
            redirect('index.php?controller=auth&action=login');
        }

        /**
         * Bắt buộc tài khoản Thành viên (role = 1) — dùng cho Giỏ hàng, Đặt hàng,
         * Đơn hàng của tôi, Bình luận bài viết, Đánh giá sản phẩm, Đặt câu hỏi FAQ.
         */
        public static function requireMember(
            $message = 'Chức năng này chỉ dành cho tài khoản Thành viên (Khách hàng).',
            $loginMessage = null,
            $redirectAfterLogin = null
        ) {
            if (!self::isLoggedIn()) {
                $notice = $loginMessage !== null && $loginMessage !== ''
                    ? $loginMessage
                    : 'Vui lòng đăng nhập bằng tài khoản Thành viên (Khách hàng) để sử dụng chức năng này.';
                $targetUrl = is_string($redirectAfterLogin) && $redirectAfterLogin !== ''
                    ? $redirectAfterLogin
                    : self::currentUrl();

                if (function_exists('is_ajax_request') && is_ajax_request()) {
                    $_SESSION['redirect_after_login'] = $targetUrl;
                    json_response([
                        'ok' => false,
                        'require_login' => true,
                        'login_url' => 'index.php?controller=auth&action=login',
                        'message' => $notice,
                    ], 401);
                }

                if (function_exists('flash_set')) {
                    flash_set('auth', $notice, 'warning');
                }
                $_SESSION['redirect_after_login'] = $targetUrl;
                redirect('index.php?controller=auth&action=login');
            }
            if (!self::isMember()) {
                if (function_exists('is_ajax_request') && is_ajax_request()) {
                    json_response(['ok' => false, 'message' => $message], 403);
                }
                self::deny($message);
            }

            return self::user();
        }

        /** Bắt buộc Khách vãng lai hoặc Thành viên (chặn Quản trị viên & Nhân viên CSKH mua hàng). */
        public static function requireCustomer($message = 'Tài khoản Quản trị viên và Nhân viên CSKH chỉ phục vụ quản lý/hỗ trợ, không được thực hiện chức năng mua hàng.')
        {
            if (!self::canShop()) {
                if (function_exists('is_ajax_request') && is_ajax_request()) {
                    json_response(['ok' => false, 'message' => $message], 403);
                }
                self::deny($message);
            }

            return self::user();
        }

        /** Bắt buộc quyền Quản trị viên hệ thống (role = 0). */
        public static function requireAdmin()
        {
            if (!self::isLoggedIn()) {
                $_SESSION['redirect_after_login'] = self::currentUrl();
                redirect('index.php?controller=auth&action=login');
            }
            if (!self::isAdmin()) {
                self::deny('Khu vực này chỉ dành riêng cho Quản trị viên hệ thống.');
            }

            return self::user();
        }

        /** Bắt buộc quyền Nhân viên Chăm sóc khách hàng (role = 2). */
        public static function requireStaff()
        {
            if (!self::isLoggedIn()) {
                $_SESSION['redirect_after_login'] = self::currentUrl();
                redirect('index.php?controller=auth&action=login');
            }
            if (!self::isStaff()) {
                self::deny('Khu vực này chỉ dành riêng cho Nhân viên Chăm sóc khách hàng.');
            }

            return self::user();
        }

        /** Trang lỗi 403 rõ ràng kèm đường dẫn về đúng phân hệ của tài khoản hiện tại. */
        public static function deny($reason)
        {
            http_response_code(403);
            header('Content-Type: text/html; charset=utf-8');
            $homeUrl = 'index.php';
            $homeLabel = 'Về trang chủ';
            if (self::isAdmin()) {
                $homeUrl = 'index.php?controller=admin&action=dashboard';
                $homeLabel = 'Về trang Quản trị';
            } elseif (self::isStaff()) {
                $homeUrl = 'index.php?controller=staff&action=dashboard';
                $homeLabel = 'Về kênh Chăm sóc khách hàng';
            }

            echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8">'
                . '<meta name="viewport" content="width=device-width, initial-scale=1">'
                . '<title>403 – Không có quyền truy cập</title>'
                . '<link rel="stylesheet" href="Public/vendor/bootstrap/css/bootstrap.min.css">'
                . '</head><body class="bg-light d-flex align-items-center min-vh-100">'
                . '<div class="container"><div class="card border-0 shadow-sm mx-auto" style="max-width:520px">'
                . '<div class="card-body p-4 p-md-5 text-center">'
                . '<span class="badge text-bg-danger mb-3">HTTP 403</span>'
                . '<h1 class="h4 fw-bold mb-2">Không có quyền truy cập</h1>'
                . '<p class="text-secondary mb-4">' . htmlspecialchars($reason, ENT_QUOTES, 'UTF-8') . '</p>'
                . '<div class="d-flex justify-content-center gap-2 flex-wrap">'
                . '<a class="btn btn-primary" href="' . htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($homeLabel, ENT_QUOTES, 'UTF-8') . '</a>'
                . '<a class="btn btn-outline-secondary" href="index.php">Trang chủ</a>'
                . '</div></div></div></div></body></html>';
            exit;
        }

        /** URL hiện tại (chỉ phần nội bộ) để quay lại sau khi đăng nhập. */
        private static function currentUrl()
        {
            $uri = $_SERVER['REQUEST_URI'] ?? '';

            // Chỉ lưu URL tương đối, bỏ host và scheme để tránh open redirect.
            $path = parse_url($uri, PHP_URL_PATH) ?: 'index.php';
            $query = parse_url($uri, PHP_URL_QUERY);
            $script = basename((string) $path);
            if ($script !== 'index.php') {
                $path = 'index.php';
            }
            $url = ltrim($path, '/');
            if ($url === '' || strpos($url, 'index.php') !== 0) {
                $url = 'index.php';
            }

            return $query ? $url . '?' . $query : $url;
        }
    }
}
