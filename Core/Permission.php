<?php

/**
 * Phân quyền dùng chung.
 *
 * Quy ước của nhóm:
 *   - Dòng đầu mọi action của trang quản trị: Permission::requireAdmin();
 *   - Action dành cho thành viên đã đăng nhập:   Permission::requireLogin();
 *   - Không đọc trực tiếp $_SESSION['role'] trong controller.
 *
 * Role và trạng thái tài khoản được đọc lại từ CSDL (không tin session) để
 * tài khoản bị khoá/xoá mất quyền ngay lập tức.
 */
if (!class_exists('Permission')) {
    class Permission
    {
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
                unset($_SESSION['user_id']);

                return null;
            }

            self::$user = $user;

            return self::$user;
        }

        public static function id()
        {
            $user = self::user();

            return $user === null ? null : (int) $user['id'];
        }

        public static function isLoggedIn()
        {
            return self::user() !== null;
        }

        public static function isAdmin()
        {
            $user = self::user();

            return $user !== null && (int) $user['role'] === 0;
        }

        /** Bắt buộc đăng nhập; chưa đăng nhập thì chuyển tới trang đăng nhập. */
        public static function requireLogin()
        {
            if (self::isLoggedIn()) {
                return self::user();
            }

            $_SESSION['redirect_after_login'] = self::currentUrl();
            redirect('index.php?controller=auth&action=login');
        }

        /** Bắt buộc quyền quản trị (role = 0). */
        public static function requireAdmin()
        {
            if (!self::isLoggedIn()) {
                $_SESSION['redirect_after_login'] = self::currentUrl();
                redirect('index.php?controller=auth&action=login');
            }
            if (!self::isAdmin()) {
                http_response_code(403);
                header('Content-Type: text/html; charset=utf-8');
                echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8">'
                    . '<title>403 – Không có quyền</title></head><body>'
                    . '<h1>403 – Không có quyền truy cập</h1>'
                    . '<p>Tài khoản của bạn không có quyền quản trị.</p>'
                    . '<p><a href="index.php">Về trang chủ</a></p></body></html>';
                exit;
            }

            return self::user();
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
