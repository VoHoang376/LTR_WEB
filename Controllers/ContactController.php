<?php

class ContactController
{
    private $contactModel;
    private $settingModel;

    public function __construct(PDO $db)
    {
        require_once __DIR__ . '/../Models/ContactModel.php';
        require_once __DIR__ . '/../Models/SettingModel.php';
        $this->contactModel = new ContactModel($db);
        $this->settingModel = new SettingModel($db);
    }

    public function index()
    {
        $settings = $this->settingModel->getAll();
        $error = null;
        $success = null;
        $old = [
            'fullname' => '',
            'email' => '',
            'phone' => '',
            'message' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['fullname'] = trim($_POST['fullname'] ?? '');
            $old['email'] = trim($_POST['email'] ?? '');
            $old['phone'] = trim($_POST['phone'] ?? '');
            $old['message'] = trim($_POST['message'] ?? '');

            if (!$this->hasValidCsrfToken()) {
                $error = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
            } elseif (mb_strlen($old['fullname'], 'UTF-8') < 2 || mb_strlen($old['fullname'], 'UTF-8') > 100) {
                $error = 'Họ tên phải có từ 2 đến 100 ký tự.';
            } elseif (strlen($old['email']) > 100 || !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
                $error = 'Vui lòng nhập địa chỉ email hợp lệ.';
            } elseif ($old['phone'] !== '' && !preg_match('/^[0-9+\-\s()]{7,20}$/', $old['phone'])) {
                $error = 'Số điện thoại không hợp lệ.';
            } elseif (mb_strlen($old['message'], 'UTF-8') < 10) {
                $error = 'Nội dung lời nhắn phải có ít nhất 10 ký tự.';
            } elseif (mb_strlen($old['message'], 'UTF-8') > 5000) {
                $error = 'Nội dung lời nhắn không được vượt quá 5000 ký tự.';
            } else {
                $this->contactModel->create(
                    $old['fullname'],
                    $old['email'],
                    $old['phone'],
                    $old['message']
                );
                $success = 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong thời gian sớm nhất.';
                $old = ['fullname' => '', 'email' => '', 'phone' => '', 'message' => ''];
            }
        }

        $csrfToken = $this->csrfToken();
        $pageTitle = 'Liên hệ';
        $currentPage = 'contact';
        require __DIR__ . '/../Views/Frontends/contact/index.php';
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
}
