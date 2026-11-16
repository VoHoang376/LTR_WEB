<?php

class AuthController
{
    private $userModel;

    public function __construct(PDO $db)
    {
        require_once __DIR__ . '/../Models/UserModel.php';
        $this->userModel = new UserModel($db);
    }

    public function register()
    {
        $error = null;
        $old = [
            'email' => '',
            'fullname' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['email'] = trim($_POST['email'] ?? '');
            $old['fullname'] = trim($_POST['fullname'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirmation = $_POST['password_confirmation'] ?? '';

            if (!$this->hasValidCsrfToken()) {
                $error = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
            } elseif (strlen($old['email']) > 191 || !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
                $error = 'Vui lòng nhập địa chỉ email hợp lệ.';
            } elseif (mb_strlen($old['fullname'], 'UTF-8') < 2 || mb_strlen($old['fullname'], 'UTF-8') > 100) {
                $error = 'Họ tên phải có từ 2 đến 100 ký tự.';
            } elseif (strlen($password) < 8) {
                $error = 'Mật khẩu phải có ít nhất 8 ký tự.';
            } elseif ($password !== $passwordConfirmation) {
                $error = 'Mật khẩu xác nhận không khớp.';
            } elseif ($this->userModel->findByEmail($old['email'])) {
                $error = 'Email này đã được đăng ký.';
            } else {
                $userId = $this->userModel->create(
                    $old['email'],
                    password_hash($password, PASSWORD_DEFAULT),
                    $old['fullname'],
                    '',
                    ''
                );

                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $userId;
                header('Location: index.php?controller=auth&action=profile');
                exit;
            }
        }

        $csrfToken = $this->csrfToken();
        require __DIR__ . '/../Views/Frontends/auth/register.php';
    }

    public function login()
    {
        $error = null;
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!$this->hasValidCsrfToken()) {
                $error = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
            } else {
                $user = $this->userModel->findByEmail($email);

                if (!$user || !password_verify($password, $user['password'])) {
                    $error = 'Email hoặc mật khẩu không chính xác.';
                } elseif ((int) $user['status'] !== 1) {
                    $error = 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.';
                } else {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = (int) $user['id'];
                    $_SESSION['role'] = (int) $user['role'];
                    $_SESSION['fullname'] = $user['fullname'];

                    if ((int) $user['role'] === 0) {
                        header('Location: index.php?controller=admin&action=dashboard');
                    } else {
                        header('Location: index.php');
                    }
                    exit;
                }
            }
        }

        $csrfToken = $this->csrfToken();
        require __DIR__ . '/../Views/Frontends/auth/login.php';
    }

    public function logout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->hasValidCsrfToken()) {
            http_response_code(400);
            exit('Yêu cầu đăng xuất không hợp lệ.');
        }

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $cookie = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $cookie['path'],
                $cookie['domain'],
                $cookie['secure'],
                $cookie['httponly']
            );
        }
        session_destroy();

        header('Location: index.php?controller=auth&action=login');
        exit;
    }

    public function profile()
    {
        $this->requireLogin();
        $user = $this->userModel->findById($_SESSION['user_id']);
        if (!$user) {
            $_SESSION = [];
            session_destroy();
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->hasValidCsrfToken()) {
                $error = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
            } else {
                $fullname = trim($_POST['fullname'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $address = trim($_POST['address'] ?? '');
                $newAvatar = $user['avatar'] ?: 'default_avatar.png';

                if (mb_strlen($fullname, 'UTF-8') < 2 || mb_strlen($fullname, 'UTF-8') > 100) {
                    $error = 'Họ tên phải có từ 2 đến 100 ký tự.';
                } elseif (mb_strlen($phone, 'UTF-8') > 20 || mb_strlen($address, 'UTF-8') > 255) {
                    $error = 'Số điện thoại hoặc địa chỉ vượt quá độ dài cho phép.';
                } else {
                    $currentPassword = $_POST['current_password'] ?? '';
                    $newPassword = $_POST['new_password'] ?? '';
                    $passwordConfirmation = $_POST['password_confirmation'] ?? '';

                    if ($newPassword !== '' || $currentPassword !== '' || $passwordConfirmation !== '') {
                        $storedPassword = $this->passwordHash($user['id']);
                        if ($storedPassword === null || !password_verify($currentPassword, $storedPassword)) {
                            $error = 'Mật khẩu hiện tại không chính xác.';
                        } elseif (strlen($newPassword) < 8) {
                            $error = 'Mật khẩu mới phải có ít nhất 8 ký tự.';
                        } elseif ($newPassword !== $passwordConfirmation) {
                            $error = 'Mật khẩu mới xác nhận không khớp.';
                        }
                    }

                    if ($error === null) {
                        $error = $this->storeAvatar($_FILES['avatar'] ?? null, $newAvatar);
                    }
                    if ($error === null) {
                        $this->userModel->updateProfile(
                            $user['id'],
                            $fullname,
                            $phone,
                            $address,
                            $newAvatar
                        );

                        if ($newPassword !== '') {
                            $this->userModel->updatePassword(
                                $user['id'],
                                password_hash($newPassword, PASSWORD_DEFAULT)
                            );
                            $success = 'Đã cập nhật hồ sơ và mật khẩu.';
                        } else {
                            $success = 'Đã cập nhật hồ sơ.';
                        }

                        $user = $this->userModel->findById($_SESSION['user_id']);
                        $_SESSION['fullname'] = $user['fullname'];
                    }
                }
            }
        }

        $csrfToken = $this->csrfToken();
        require __DIR__ . '/../Views/Frontends/auth/profile.php';
    }

    private function passwordHash($id)
    {
        return $this->userModel->findPasswordById($id);
    }

    private function requireLogin()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    private function csrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    private function hasValidCsrfToken()
    {
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    private function storeAvatar($file, &$avatar)
    {
        if ($file === null || (isset($file['error']) && $file['error'] === UPLOAD_ERR_NO_FILE)) {
            return null;
        }
        if (!is_array($file) || !isset($file['error'], $file['tmp_name'], $file['size'])
            || $file['error'] !== UPLOAD_ERR_OK) {
            return 'Không thể tải ảnh lên. Vui lòng thử lại.';
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            return 'Ảnh đại diện không được vượt quá 2 MB.';
        }

        $imageInfo = is_uploaded_file($file['tmp_name']) ? getimagesize($file['tmp_name']) : false;
        if ($imageInfo === false) {
            return 'Tệp tải lên không phải là ảnh hợp lệ.';
        }

        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        $mimeType = $imageInfo['mime'];
        if (!isset($extensions[$mimeType])) {
            return 'Chỉ chấp nhận ảnh JPG, PNG hoặc WebP.';
        }

        $uploadDirectory = __DIR__ . '/../Public/uploads/avatars';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
            return 'Không thể tạo thư mục lưu ảnh.';
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mimeType];
        if (!move_uploaded_file($file['tmp_name'], $uploadDirectory . '/' . $filename)) {
            return 'Không thể lưu ảnh đại diện.';
        }

        $avatar = $filename;
        return null;
    }
}
