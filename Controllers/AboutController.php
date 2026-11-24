<?php

require_once __DIR__ . '/BaseController.php';

class AboutController extends BaseController
{
    private $contentModel;

    public function __construct(PDO $db)
    {
        parent::__construct($db);
        require_once __DIR__ . '/../Models/ContentModel.php';
        $this->contentModel = new ContentModel($db);
    }

    public function publicIndex()
    {
        $sections = $this->contentModel->getAboutSections();
        foreach ($sections as &$section) {
            $section['content'] = $this->sanitizeRichText($section['content'] ?? '');
        }
        unset($section);

        $pageTitle = 'Giới thiệu';
        $currentPage = 'about';
        require __DIR__ . '/../Views/Frontends/about.php';
    }

    public function adminIndex()
    {
        $this->requireAdmin();
        $page = $this->findPage();
        $pagination = $this->pagination(
            $this->contentModel->countAboutSections(),
            $page,
            10
        );
        $sections = $this->contentModel->getAboutSections(
            $pagination['perPage'],
            $pagination['offset']
        );
        $csrfToken = $this->csrfToken();
        $message = $this->consumeFlash();
        $pageTitle = 'Quản lý nội dung giới thiệu';
        $activeAdminPage = 'about';
        $paginationUrl = 'index.php?controller=adminAbout&action=index';

        require __DIR__ . '/../Views/Backends/about/index.php';
    }

    public function create()
    {
        $this->requireAdmin();
        $section = $this->emptySection();
        $error = null;
        $isEdit = false;
        $pageTitle = 'Thêm nội dung giới thiệu';
        $this->renderForm($section, $error, $isEdit, $pageTitle);
    }

    public function store()
    {
        $admin = $this->requireAdmin();
        $this->requirePost();
        $section = $this->readSectionInput();
        $error = $this->validateCsrfAndSection($section);

        if ($error !== null) {
            http_response_code($error === 'Phiên biểu mẫu không hợp lệ. Vui lòng tải lại trang.' ? 400 : 422);
            $isEdit = false;
            $pageTitle = 'Thêm nội dung giới thiệu';
            $this->renderForm($section, $error, $isEdit, $pageTitle);
            return;
        }

        $imagePath = null;
        $error = $this->storeAboutImage($_FILES['image'] ?? null, $imagePath);
        if ($error !== null) {
            http_response_code(422);
            $isEdit = false;
            $pageTitle = 'Thêm nội dung giới thiệu';
            $this->renderForm($section, $error, $isEdit, $pageTitle);
            return;
        }

        try {
            $this->contentModel->createAboutSection(
                $section['section_title'],
                $section['content'],
                $imagePath,
                $section['sort_order']
            );
        } catch (PDOException $exception) {
            $this->deleteStoredImage($imagePath);
            throw $exception;
        }

        $this->redirectWithMessage(
            'index.php?controller=adminAbout&action=index',
            'Đã thêm nội dung giới thiệu.'
        );
    }

    public function edit()
    {
        $this->requireAdmin();
        $section = $this->loadSectionFromRequest();
        if (!$section) {
            return;
        }

        $error = null;
        $isEdit = true;
        $pageTitle = 'Chỉnh sửa nội dung giới thiệu';
        $this->renderForm($section, $error, $isEdit, $pageTitle);
    }

    public function update()
    {
        $this->requireAdmin();
        $this->requirePost();
        $id = $this->requestedId();
        $existingSection = $this->contentModel->findAboutSection($id);
        if (!$existingSection) {
            http_response_code(404);
            exit('Không tìm thấy mục giới thiệu.');
        }

        $section = $this->readSectionInput();
        $section['id'] = $id;
        $section['image_url'] = $existingSection['image_url'];
        $error = $this->validateCsrfAndSection($section);

        if ($error !== null) {
            http_response_code($error === 'Phiên biểu mẫu không hợp lệ. Vui lòng tải lại trang.' ? 400 : 422);
            $isEdit = true;
            $pageTitle = 'Chỉnh sửa nội dung giới thiệu';
            $this->renderForm($section, $error, $isEdit, $pageTitle);
            return;
        }

        $removeImage = !empty($_POST['remove_image']);
        $newImagePath = null;
        $error = $this->storeAboutImage($_FILES['image'] ?? null, $newImagePath);
        if ($error !== null) {
            http_response_code(422);
            $isEdit = true;
            $pageTitle = 'Chỉnh sửa nội dung giới thiệu';
            $this->renderForm($section, $error, $isEdit, $pageTitle);
            return;
        }

        if ($newImagePath !== null) {
            $imagePath = $newImagePath;
        } elseif ($removeImage) {
            $imagePath = null;
        } else {
            $imagePath = $existingSection['image_url'];
        }

        try {
            $this->contentModel->updateAboutSection(
                $id,
                $section['section_title'],
                $section['content'],
                $imagePath,
                $section['sort_order']
            );
        } catch (PDOException $exception) {
            if ($newImagePath !== null) {
                $this->deleteStoredImage($newImagePath);
            }
            throw $exception;
        }

        if ($newImagePath !== null || $removeImage) {
            $this->deleteStoredImage($existingSection['image_url']);
        }

        $this->redirectWithMessage(
            'index.php?controller=adminAbout&action=index',
            'Đã cập nhật nội dung giới thiệu.'
        );
    }

    public function delete()
    {
        $this->requireAdmin();
        $this->requirePost();
        if (!$this->hasValidCsrfToken()) {
            http_response_code(400);
            exit('Phiên biểu mẫu không hợp lệ. Vui lòng tải lại trang.');
        }

        $id = $this->requestedId();
        $section = $this->contentModel->findAboutSection($id);
        if (!$section) {
            http_response_code(404);
            exit('Không tìm thấy mục giới thiệu.');
        }

        $this->contentModel->deleteAboutSection($id);
        $this->deleteStoredImage($section['image_url']);

        $this->redirectWithMessage(
            'index.php?controller=adminAbout&action=index',
            'Đã xóa nội dung giới thiệu.'
        );
    }

    private function renderForm($section, $error, $isEdit, $pageTitle)
    {
        $csrfToken = $this->csrfToken();
        $activeAdminPage = 'about';
        require __DIR__ . '/../Views/Backends/about/form.php';
    }

    private function emptySection()
    {
        return [
            'id' => null,
            'section_title' => '',
            'content' => '',
            'image_url' => null,
            'sort_order' => 0,
        ];
    }

    private function readSectionInput()
    {
        return [
            'section_title' => $this->postedString('section_title'),
            'content' => $this->postedString('content'),
            'sort_order' => $this->postedString('sort_order', '0'),
        ];
    }

    private function validateCsrfAndSection(&$section)
    {
        if (!$this->hasValidCsrfToken()) {
            return 'Phiên biểu mẫu không hợp lệ. Vui lòng tải lại trang.';
        }
        if (mb_strlen($section['section_title'], 'UTF-8') < 2
            || mb_strlen($section['section_title'], 'UTF-8') > 255) {
            return 'Tiêu đề phải có từ 2 đến 255 ký tự.';
        }
        if (strlen($section['content']) > 200000) {
            return 'Nội dung không được vượt quá 200 KB.';
        }

        $section['content'] = $this->sanitizeRichText($section['content']);
        $plainText = trim(html_entity_decode(strip_tags($section['content']), ENT_QUOTES, 'UTF-8'));
        if ($plainText === '') {
            return 'Vui lòng nhập nội dung giới thiệu.';
        }

        $sortOrder = filter_var(
            $section['sort_order'],
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 0, 'max_range' => 2147483647]]
        );
        if ($sortOrder === false) {
            return 'Thứ tự hiển thị phải là số nguyên không âm.';
        }

        $section['sort_order'] = $sortOrder;
        return null;
    }

    private function loadSectionFromRequest()
    {
        $id = $this->requestedId();
        $section = $this->contentModel->findAboutSection($id);
        if (!$section) {
            http_response_code(404);
            exit('Không tìm thấy mục giới thiệu.');
        }

        $section['content'] = $this->sanitizeRichText($section['content'] ?? '');
        return $section;
    }

    private function requestedId()
    {
        $id = filter_var($_POST['id'] ?? $_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id === null || $id < 1) {
            http_response_code(400);
            exit('Mã nội dung không hợp lệ.');
        }

        return $id;
    }

    private function storeAboutImage($file, &$imagePath)
    {
        if ($file === null || (isset($file['error']) && $file['error'] === UPLOAD_ERR_NO_FILE)) {
            return null;
        }
        if (!is_array($file) || !isset($file['error'], $file['tmp_name'], $file['size'])
            || $file['error'] !== UPLOAD_ERR_OK) {
            return 'Không thể tải ảnh lên. Vui lòng thử lại.';
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            return 'Ảnh không được vượt quá 2 MB.';
        }

        $imageInfo = is_uploaded_file($file['tmp_name']) ? getimagesize($file['tmp_name']) : false;
        if ($imageInfo === false) {
            return 'Tệp tải lên không phải ảnh hợp lệ.';
        }

        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($extensions[$imageInfo['mime']])) {
            return 'Chỉ chấp nhận ảnh JPG, PNG hoặc WebP.';
        }

        $uploadDirectory = __DIR__ . '/../Public/uploads/about';
        if (!is_dir($uploadDirectory)
            && !mkdir($uploadDirectory, 0755, true)
            && !is_dir($uploadDirectory)) {
            return 'Không thể tạo thư mục lưu ảnh.';
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$imageInfo['mime']];
        if (!move_uploaded_file($file['tmp_name'], $uploadDirectory . '/' . $filename)) {
            return 'Không thể lưu ảnh lên máy chủ.';
        }

        $imagePath = 'about/' . $filename;
        return null;
    }

    private function deleteStoredImage($imagePath)
    {
        if (!is_string($imagePath)
            || !preg_match('/^about\/[a-f0-9]{32}\.(jpg|png|webp)$/', $imagePath)) {
            return;
        }

        $file = __DIR__ . '/../Public/uploads/' . $imagePath;
        if (is_file($file) && !unlink($file)) {
            throw new RuntimeException('Không thể xóa ảnh cũ khỏi máy chủ.');
        }
    }
}
