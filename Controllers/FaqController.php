<?php

require_once __DIR__ . '/BaseController.php';

class FaqController extends BaseController
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
        $faqs = $this->contentModel->getPublishedFaqs();
        $user = Permission::user();
        $myQuestions = Permission::isMember() ? $this->contentModel->getUserSubmittedFaqs((int) $user['id']) : [];
        $faqFlash = flash_get('faq');
        $faqError = $_SESSION['faq_form_error'] ?? null;
        $faqOld = $_SESSION['faq_form_old'] ?? [
            'asker_name' => (string) ($user['fullname'] ?? ''),
            'asker_email' => (string) ($user['email'] ?? ''),
            'category' => '',
            'question' => '',
        ];
        unset($_SESSION['faq_form_error'], $_SESSION['faq_form_old']);

        $pageTitle = 'Câu hỏi thường gặp';
        $currentPage = 'faq';
        require __DIR__ . '/../Views/Frontends/faq.php';
    }

    /** Chỉ Thành viên đã đăng nhập (role = 1) mới được gửi câu hỏi mới để chờ Nhân viên CSKH trả lời. */
    public function ask()
    {
        $user = Permission::requireMember(
            'Tài khoản Quản trị viên và Nhân viên CSKH không sử dụng form đặt câu hỏi của khách hàng.',
            'Vui lòng đăng nhập bằng tài khoản Thành viên (Khách hàng) để đặt câu hỏi Hỏi/Đáp.',
            'index.php?controller=faq&action=index'
        );
        $this->requirePost();

        $input = [
            'asker_name' => trim((string) ($_POST['asker_name'] ?? ($user['fullname'] ?? ''))),
            'asker_email' => trim((string) ($_POST['asker_email'] ?? ($user['email'] ?? ''))),
            'category' => trim((string) ($_POST['category'] ?? '')),
            'question' => trim((string) ($_POST['question'] ?? '')),
        ];

        $error = null;
        if (!csrf_verify()) {
            $error = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
        } elseif (mb_strlen($input['asker_name'], 'UTF-8') < 2 || mb_strlen($input['asker_name'], 'UTF-8') > 100) {
            $error = 'Họ tên phải có từ 2 đến 100 ký tự.';
        } elseif (strlen($input['asker_email']) > 191 || !filter_var($input['asker_email'], FILTER_VALIDATE_EMAIL)) {
            $error = 'Vui lòng nhập địa chỉ email hợp lệ.';
        } elseif (mb_strlen($input['category'], 'UTF-8') > 100) {
            $error = 'Chủ đề không được vượt quá 100 ký tự.';
        } elseif (mb_strlen($input['question'], 'UTF-8') < 10 || mb_strlen($input['question'], 'UTF-8') > 255) {
            $error = 'Nội dung câu hỏi phải có từ 10 đến 255 ký tự.';
        }

        if ($error !== null) {
            $_SESSION['faq_form_error'] = $error;
            $_SESSION['faq_form_old'] = $input;
            redirect('index.php?controller=faq&action=index#ask-question');
        }

        $this->contentModel->createCustomerQuestion(
            (int) $user['id'],
            $input['asker_name'],
            $input['asker_email'],
            $input['category'],
            $input['question']
        );

        flash_set('faq', 'Câu hỏi của bạn đã được ghi nhận và chuyển tới Nhân viên Chăm sóc khách hàng. Chúng tôi sẽ phản hồi sớm nhất!', 'success');
        redirect('index.php?controller=faq&action=index#ask-question');
    }

    public function adminIndex()
    {
        $this->requireAdmin();
        $pagination = $this->pagination(
            $this->contentModel->countFaqs(),
            $this->findPage(),
            10
        );
        $faqs = $this->contentModel->getFaqs(
            $pagination['perPage'],
            $pagination['offset']
        );
        $csrfToken = $this->csrfToken();
        $message = $this->consumeFlash();
        $pageTitle = 'Quản lý câu hỏi thường gặp';
        $activeAdminPage = 'faq';
        $paginationUrl = 'index.php?controller=adminFaq&action=index';

        require __DIR__ . '/../Views/Backends/faqs/index.php';
    }

    public function create()
    {
        $this->requireAdmin();
        $faq = $this->emptyFaq();
        $error = null;
        $isEdit = false;
        $pageTitle = 'Thêm câu hỏi';
        $this->renderForm($faq, $error, $isEdit, $pageTitle);
    }

    public function store()
    {
        $admin = $this->requireAdmin();
        $this->requirePost();
        $faq = $this->readFaqInput();
        $error = $this->validateFaq($faq);
        if ($error !== null) {
            http_response_code($error === 'Phiên biểu mẫu không hợp lệ. Vui lòng tải lại trang.' ? 400 : 422);
            $isEdit = false;
            $pageTitle = 'Thêm câu hỏi';
            $this->renderForm($faq, $error, $isEdit, $pageTitle);
            return;
        }

        $this->contentModel->createFaq(
            $admin['id'],
            $faq['category'],
            $faq['question'],
            $faq['answer'],
            $faq['sort_order'],
            $faq['status']
        );

        $this->redirectWithMessage(
            'index.php?controller=adminFaq&action=index',
            'Đã thêm câu hỏi thường gặp.'
        );
    }

    public function edit()
    {
        $this->requireAdmin();
        $faq = $this->contentModel->findFaq($this->requestedId());
        if (!$faq) {
            http_response_code(404);
            exit('Không tìm thấy câu hỏi.');
        }

        $error = null;
        $isEdit = true;
        $pageTitle = 'Chỉnh sửa câu hỏi';
        $this->renderForm($faq, $error, $isEdit, $pageTitle);
    }

    public function update()
    {
        $this->requireAdmin();
        $this->requirePost();
        $id = $this->requestedId();
        if (!$this->contentModel->findFaq($id)) {
            http_response_code(404);
            exit('Không tìm thấy câu hỏi.');
        }

        $faq = $this->readFaqInput();
        $faq['id'] = $id;
        $error = $this->validateFaq($faq);
        if ($error !== null) {
            http_response_code($error === 'Phiên biểu mẫu không hợp lệ. Vui lòng tải lại trang.' ? 400 : 422);
            $isEdit = true;
            $pageTitle = 'Chỉnh sửa câu hỏi';
            $this->renderForm($faq, $error, $isEdit, $pageTitle);
            return;
        }

        $this->contentModel->updateFaq(
            $id,
            $faq['category'],
            $faq['question'],
            $faq['answer'],
            $faq['sort_order'],
            $faq['status']
        );

        $this->redirectWithMessage(
            'index.php?controller=adminFaq&action=index',
            'Đã cập nhật câu hỏi thường gặp.'
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
        if (!$this->contentModel->deleteFaq($id)) {
            http_response_code(404);
            exit('Không tìm thấy câu hỏi.');
        }

        $this->redirectWithMessage(
            'index.php?controller=adminFaq&action=index',
            'Đã xóa câu hỏi thường gặp.'
        );
    }

    private function renderForm($faq, $error, $isEdit, $pageTitle)
    {
        $csrfToken = $this->csrfToken();
        $activeAdminPage = 'faq';
        require __DIR__ . '/../Views/Backends/faqs/form.php';
    }

    private function emptyFaq()
    {
        return [
            'id' => null,
            'category' => '',
            'question' => '',
            'answer' => '',
            'sort_order' => 0,
            'status' => 1,
        ];
    }

    private function readFaqInput()
    {
        return [
            'category' => $this->postedString('category'),
            'question' => $this->postedString('question'),
            'answer' => $this->postedString('answer'),
            'sort_order' => $this->postedString('sort_order', '0'),
            'status' => $this->postedString('status', '1'),
        ];
    }

    private function validateFaq(&$faq)
    {
        if (!$this->hasValidCsrfToken()) {
            return 'Phiên biểu mẫu không hợp lệ. Vui lòng tải lại trang.';
        }
        if (mb_strlen($faq['question'], 'UTF-8') < 5
            || mb_strlen($faq['question'], 'UTF-8') > 255) {
            return 'Câu hỏi phải có từ 5 đến 255 ký tự.';
        }
        if ($faq['answer'] === '' || mb_strlen($faq['answer'], 'UTF-8') > 10000) {
            return 'Câu trả lời không được để trống và tối đa 10.000 ký tự.';
        }
        if (mb_strlen($faq['category'], 'UTF-8') > 100) {
            return 'Danh mục không được vượt quá 100 ký tự.';
        }

        $sortOrder = filter_var(
            $faq['sort_order'],
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 0, 'max_range' => 2147483647]]
        );
        if ($sortOrder === false) {
            return 'Thứ tự hiển thị phải là số nguyên không âm.';
        }

        if (!in_array($faq['status'], ['0', '1'], true)) {
            return 'Trạng thái câu hỏi không hợp lệ.';
        }

        $faq['sort_order'] = $sortOrder;
        $faq['status'] = (int) $faq['status'];
        return null;
    }

    private function requestedId()
    {
        $id = filter_var($_POST['id'] ?? $_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id === null || $id < 1) {
            http_response_code(400);
            exit('Mã câu hỏi không hợp lệ.');
        }

        return $id;
    }
}
