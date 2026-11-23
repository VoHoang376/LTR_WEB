<?php

class AdminController
{
    private $db;
    private $userModel;
    private $contactModel;
    private $settingModel;
    private $pricingModel;
    private $currentUser;

    public function __construct(PDO $db)
    {
        $this->db = $db;

        require_once __DIR__ . '/../Models/UserModel.php';
        require_once __DIR__ . '/../Models/ContactModel.php';
        require_once __DIR__ . '/../Models/SettingModel.php';
        require_once __DIR__ . '/../Models/PricingModel.php';

        $this->userModel = new UserModel($db);
        $this->contactModel = new ContactModel($db);
        $this->settingModel = new SettingModel($db);
        $this->pricingModel = new PricingModel($db);

        $this->requireAdmin();
    }

    private function requireAdmin()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $user = $this->userModel->findById($_SESSION['user_id']);
        if (!$user || (int) $user['status'] !== 1) {
            $_SESSION = [];
            session_destroy();
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        if ((int) $user['role'] !== 0) {
            http_response_code(403);
            exit('Bạn không có quyền truy cập trang quản trị. Vui lòng liên hệ quản trị viên.');
        }

        $this->currentUser = $user;
    }

    public function dashboard()
    {
        $pageTitle = 'Bảng điều khiển';
        $activeAdminPage = 'dashboard';

        $totalContacts = $this->contactModel->countAll();
        $unprocessedContacts = $this->contactModel->countUnprocessed();
        $recentContacts = $this->contactModel->getPaginated(1, 5);

        require __DIR__ . '/../Views/Backends/dashboard.php';
    }

    public function contacts()
    {
        $pageTitle = 'Quản lý liên hệ';
        $activeAdminPage = 'contacts';

        $statusFilter = isset($_GET['status']) && $_GET['status'] !== '' ? (int) $_GET['status'] : null;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 8;

        $totalItems = $this->contactModel->countAll($statusFilter);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $contacts = $this->contactModel->getPaginated($page, $perPage, $statusFilter);
        $unprocessedCount = $this->contactModel->countUnprocessed();

        $success = $_SESSION['admin_success'] ?? null;
        $error = $_SESSION['admin_error'] ?? null;
        unset($_SESSION['admin_success'], $_SESSION['admin_error']);

        require __DIR__ . '/../Views/Backends/contacts/index.php';
    }

    public function updateContactStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->hasValidCsrfToken()) {
            $_SESSION['admin_error'] = 'Yêu cầu không hợp lệ hoặc đã hết hạn.';
            header('Location: index.php?controller=admin&action=contacts');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $newStatus = (int) ($_POST['status'] ?? 0);

        if ($id > 0 && in_array($newStatus, [0, 1], true)) {
            $this->contactModel->updateStatus($id, $newStatus, $this->currentUser['id']);
            $_SESSION['admin_success'] = 'Đã cập nhật trạng thái liên hệ thành công.';
        } else {
            $_SESSION['admin_error'] = 'Dữ liệu cập nhật không hợp lệ.';
        }

        header('Location: index.php?controller=admin&action=contacts');
        exit;
    }

    public function deleteContact()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->hasValidCsrfToken()) {
            $_SESSION['admin_error'] = 'Yêu cầu không hợp lệ hoặc đã hết hạn.';
            header('Location: index.php?controller=admin&action=contacts');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->contactModel->delete($id);
            $_SESSION['admin_success'] = 'Đã xóa liên hệ thành công.';
        }

        header('Location: index.php?controller=admin&action=contacts');
        exit;
    }

    public function settings()
    {
        $pageTitle = 'Cấu hình website';
        $activeAdminPage = 'settings';
        $success = null;
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->hasValidCsrfToken()) {
                $error = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
            } else {
                $companyName = trim($_POST['company_name'] ?? '');
                $companySlogan = trim($_POST['company_slogan'] ?? '');
                $companyPhone = trim($_POST['company_phone'] ?? '');
                $companyEmail = trim($_POST['company_email'] ?? '');
                $companyAddress = trim($_POST['company_address'] ?? '');
                $companyHours = trim($_POST['company_hours'] ?? '');
                $companyIntro = trim($_POST['company_intro'] ?? '');

                if ($companyName === '') {
                    $error = 'Tên công ty không được để trống.';
                } elseif ($companyEmail !== '' && !filter_var($companyEmail, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Địa chỉ email công ty không hợp lệ.';
                } else {
                    $allowedImages = [
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/webp' => 'webp',
                        'image/x-icon' => 'ico',
                        'image/vnd.microsoft.icon' => 'ico',
                    ];

                    $uploadData = [];

                    // Xử lý xóa hoặc tải lên Logo
                    if (!empty($_POST['remove_logo'])) {
                        $currentLogo = $this->settingModel->get('company_logo');
                        if ($currentLogo && file_exists(__DIR__ . '/../Public/uploads/company/' . $currentLogo)) {
                            @unlink(__DIR__ . '/../Public/uploads/company/' . $currentLogo);
                        }
                        $uploadData['company_logo'] = '';
                    } elseif (!empty($_FILES['company_logo']['name'])) {
                        list($logoFile, $uploadError) = $this->saveUploadedFile($_FILES['company_logo'], $allowedImages, 'logo');
                        if ($uploadError) {
                            $error = $uploadError;
                        } elseif ($logoFile) {
                            // Xóa logo cũ nếu có
                            $currentLogo = $this->settingModel->get('company_logo');
                            if ($currentLogo && file_exists(__DIR__ . '/../Public/uploads/company/' . $currentLogo)) {
                                @unlink(__DIR__ . '/../Public/uploads/company/' . $currentLogo);
                            }
                            $uploadData['company_logo'] = $logoFile;
                        }
                    }

                    // Xử lý xóa hoặc tải lên Favicon
                    if ($error === null) {
                        if (!empty($_POST['remove_favicon'])) {
                            $currentFavicon = $this->settingModel->get('company_favicon');
                            if ($currentFavicon && file_exists(__DIR__ . '/../Public/uploads/company/' . $currentFavicon)) {
                                @unlink(__DIR__ . '/../Public/uploads/company/' . $currentFavicon);
                            }
                            $uploadData['company_favicon'] = '';
                        } elseif (!empty($_FILES['company_favicon']['name'])) {
                            list($favFile, $uploadError) = $this->saveUploadedFile($_FILES['company_favicon'], $allowedImages, 'favicon');
                            if ($uploadError) {
                                $error = $uploadError;
                            } elseif ($favFile) {
                                // Xóa favicon cũ nếu có
                                $currentFavicon = $this->settingModel->get('company_favicon');
                                if ($currentFavicon && file_exists(__DIR__ . '/../Public/uploads/company/' . $currentFavicon)) {
                                    @unlink(__DIR__ . '/../Public/uploads/company/' . $currentFavicon);
                                }
                                $uploadData['company_favicon'] = $favFile;
                            }
                        }
                    }

                    if ($error === null) {
                        $saveData = array_merge([
                            'company_name' => $companyName,
                            'company_slogan' => $companySlogan,
                            'company_phone' => $companyPhone,
                            'company_email' => $companyEmail,
                            'company_address' => $companyAddress,
                            'company_hours' => $companyHours,
                            'company_intro' => $companyIntro,
                        ], $uploadData);

                        $this->settingModel->saveMultiple($saveData, $this->currentUser['id']);
                        $success = 'Đã lưu cấu hình website và tải lên hình ảnh thành công!';
                    }
                }
            }
        }

        $settings = $this->settingModel->getAll();

        require __DIR__ . '/../Views/Backends/settings/index.php';
    }

    private function saveUploadedFile($file, $allowedMimes, $prefix)
    {
        if ($file === null || (isset($file['error']) && $file['error'] === UPLOAD_ERR_NO_FILE)) {
            return [null, null];
        }
        if (!is_array($file) || !isset($file['error'], $file['tmp_name'], $file['size']) || $file['error'] !== UPLOAD_ERR_OK) {
            return [null, 'Tải tệp lên thất bại. Vui lòng thử lại.'];
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            return [null, 'Dung lượng ảnh không được vượt quá 2 MB.'];
        }

        $imageInfo = is_uploaded_file($file['tmp_name']) ? @getimagesize($file['tmp_name']) : false;
        $mime = $imageInfo !== false ? $imageInfo['mime'] : '';

        if (!isset($allowedMimes[$mime])) {
            return [null, 'Chỉ chấp nhận tệp ảnh JPG, PNG, WebP hoặc ICO (cho favicon).'];
        }

        $uploadDirectory = __DIR__ . '/../Public/uploads/company';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
            return [null, 'Không thể tạo thư mục lưu trữ ảnh.'];
        }

        $filename = $prefix . '_' . bin2hex(random_bytes(10)) . '.' . $allowedMimes[$mime];
        if (!move_uploaded_file($file['tmp_name'], $uploadDirectory . '/' . $filename)) {
            return [null, 'Không thể lưu ảnh lên máy chủ.'];
        }

        return [$filename, null];
    }

    public function pricing()
    {
        $pageTitle = 'Quản lý bảng giá';
        $activeAdminPage = 'pricing';

        $plans = $this->pricingModel->getAll();
        $editingPlan = null;
        if (!empty($_GET['edit'])) {
            $editingPlan = $this->pricingModel->findById((int) $_GET['edit']);
        }

        $success = $_SESSION['admin_success'] ?? null;
        $error = $_SESSION['admin_error'] ?? null;
        unset($_SESSION['admin_success'], $_SESSION['admin_error']);

        require __DIR__ . '/../Views/Backends/pricing/index.php';
    }

    public function savePricing()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->hasValidCsrfToken()) {
            $_SESSION['admin_error'] = 'Yêu cầu không hợp lệ hoặc đã hết hạn.';
            header('Location: index.php?controller=admin&action=pricing');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $price = (float) ($_POST['price'] ?? 0);
        $billingCycle = trim($_POST['billing_cycle'] ?? 'tháng');
        $description = trim($_POST['description'] ?? '');
        $features = trim($_POST['features'] ?? '');
        $isFeatured = !empty($_POST['is_featured']) ? 1 : 0;
        $sortOrder = (int) ($_POST['sort_order'] ?? 1);
        $status = (int) ($_POST['status'] ?? 1);

        if ($name === '') {
            $_SESSION['admin_error'] = 'Tên gói dịch vụ không được để trống.';
        } elseif ($price < 0) {
            $_SESSION['admin_error'] = 'Giá dịch vụ không được là số âm.';
        } else {
            if ($id > 0) {
                $this->pricingModel->update($id, $name, $price, $billingCycle, $description, $features, $isFeatured, $sortOrder, $status);
                $_SESSION['admin_success'] = 'Đã cập nhật gói dịch vụ thành công!';
            } else {
                $this->pricingModel->create($name, $price, $billingCycle, $description, $features, $isFeatured, $sortOrder, $status);
                $_SESSION['admin_success'] = 'Đã thêm gói dịch vụ mới thành công!';
            }
        }

        header('Location: index.php?controller=admin&action=pricing');
        exit;
    }

    public function deletePricing()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->hasValidCsrfToken()) {
            $_SESSION['admin_error'] = 'Yêu cầu không hợp lệ hoặc đã hết hạn.';
            header('Location: index.php?controller=admin&action=pricing');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->pricingModel->delete($id);
            $_SESSION['admin_success'] = 'Đã xóa gói dịch vụ thành công.';
        }

        header('Location: index.php?controller=admin&action=pricing');
        exit;
    }

    private function hasValidCsrfToken()
    {
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }
}
