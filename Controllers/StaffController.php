<?php

require_once __DIR__ . '/../Models/ContactModel.php';
require_once __DIR__ . '/../Models/ContentModel.php';
require_once __DIR__ . '/../Models/ReviewModel.php';
require_once __DIR__ . '/../Models/CommentModel.php';

/**
 * Kênh Nhân viên Chăm sóc Khách hàng (role = 2).
 *
 * Quyền hạn tách biệt hoàn toàn với Quản trị viên (role = 0) và Thành viên (role = 1):
 *   - Giải quyết & trả lời các yêu cầu Liên hệ khách hàng (contacts)
 *   - Trả lời & quản lý câu hỏi Hỏi/Đáp (faqs) do khách/thành viên gửi
 *   - Giải quyết, trả lời phản hồi / đánh giá sản phẩm (reviews)
 *   - Kiểm duyệt bình luận bài viết (comments)
 *
 * Mọi action đều yêu cầu Permission::requireStaff().
 */
class StaffController
{
    private $db;
    private $staff;
    private $contactModel;
    private $contentModel;
    private $reviewModel;
    private $commentModel;

    public function __construct(PDO $db)
    {
        $this->staff = Permission::requireStaff();
        $this->db = $db;
        $this->contactModel = new ContactModel($db);
        $this->contentModel = new ContentModel($db);
        $this->reviewModel = new ReviewModel($db);
        $this->commentModel = new CommentModel($db);
    }

    public function dashboard()
    {
        $pageTitle = 'Bảng điều khiển CSKH';
        $activeStaffPage = 'dashboard';

        $unprocessedContacts = $this->contactModel->countUnprocessed();
        $totalContacts = $this->contactModel->countAll();
        $pendingFaqs = $this->contentModel->countPendingFaqs();
        $totalFaqs = $this->contentModel->countFaqs();
        $unrepliedReviews = $this->reviewModel->countUnreplied();
        $totalReviews = $this->reviewModel->countForStaff('all');
        $visibleComments = $this->commentModel->countVisible();
        $hiddenComments = $this->commentModel->countHidden();
        $totalComments = $this->commentModel->countAll();

        $recentContacts = $this->contactModel->getPaginated(1, 5, 0);
        $recentPendingFaqs = $this->contentModel->getFaqs(5, 0, 0);
        $recentUnrepliedReviews = $this->reviewModel->getForStaff(5, 0, 'unreplied');

        require __DIR__ . '/../Views/Backends/staff/dashboard.php';
    }

    /* ===================================================== 1. Liên hệ KH */

    public function contacts()
    {
        $pageTitle = 'Giải quyết liên hệ khách hàng';
        $activeStaffPage = 'contacts';

        $statusFilter = isset($_GET['status']) && $_GET['status'] !== '' ? (int) $_GET['status'] : null;
        $perPage = 8;
        $totalItems = $this->contactModel->countAll($statusFilter);
        $pagination = paginate($totalItems, $perPage, max(1, (int) ($_GET['page'] ?? 1)));
        $contacts = $this->contactModel->getPaginated($pagination['page'], $pagination['perPage'], $statusFilter);
        $allCount = $this->contactModel->countAll();
        $unprocessedCount = $this->contactModel->countUnprocessed();

        $flash = flash_get('staff');

        require __DIR__ . '/../Views/Backends/staff/contacts.php';
    }

    public function replyContact()
    {
        $this->guardPost('index.php?controller=staff&action=contacts');

        $id = (int) ($_POST['id'] ?? 0);
        $replyMessage = trim((string) ($_POST['reply_message'] ?? ''));

        if ($id <= 0 || !$this->contactModel->findById($id)) {
            flash_set('staff', 'Không tìm thấy yêu cầu liên hệ.', 'danger');
        } elseif (mb_strlen($replyMessage, 'UTF-8') < 5 || mb_strlen($replyMessage, 'UTF-8') > 3000) {
            flash_set('staff', 'Nội dung phản hồi phải có từ 5 đến 3.000 ký tự.', 'danger');
        } else {
            $this->contactModel->reply($id, $replyMessage, (int) $this->staff['id']);
            flash_set('staff', 'Đã gửi phản hồi cho liên hệ #' . $id . ' và đánh dấu Đã phản hồi.', 'success');
        }

        redirect('index.php?controller=staff&action=contacts');
    }

    public function updateContactStatus()
    {
        $this->guardPost('index.php?controller=staff&action=contacts');

        $id = (int) ($_POST['id'] ?? 0);
        $newStatus = (int) ($_POST['status'] ?? 0);
        if ($id > 0 && in_array($newStatus, [0, 1], true)) {
            $this->contactModel->updateStatus($id, $newStatus, (int) $this->staff['id']);
            flash_set('staff', 'Đã cập nhật trạng thái liên hệ #' . $id . '.', 'success');
        }

        redirect('index.php?controller=staff&action=contacts');
    }

    public function deleteContact()
    {
        $this->guardPost('index.php?controller=staff&action=contacts');

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->contactModel->delete($id);
            flash_set('staff', 'Đã xóa liên hệ #' . $id . '.', 'success');
        }

        redirect('index.php?controller=staff&action=contacts');
    }

    /* ===================================================== 2. Hỏi / Đáp FAQ */

    public function faqs()
    {
        $pageTitle = 'Trả lời & Quản lý Hỏi/Đáp (FAQ)';
        $activeStaffPage = 'faqs';

        $statusFilter = isset($_GET['status']) && $_GET['status'] !== '' ? (int) $_GET['status'] : null;
        $perPage = 10;
        $totalItems = $this->contentModel->countFaqs($statusFilter);
        $pagination = paginate($totalItems, $perPage, max(1, (int) ($_GET['page'] ?? 1)));
        $faqs = $this->contentModel->getFaqs($pagination['perPage'], $pagination['offset'], $statusFilter);
        $allCount = $this->contentModel->countFaqs();
        $pendingCount = $this->contentModel->countPendingFaqs();

        $flash = flash_get('staff');

        require __DIR__ . '/../Views/Backends/staff/faqs.php';
    }

    public function faqCreate()
    {
        $faq = [
            'id' => null,
            'asker_name' => $this->staff['fullname'],
            'asker_email' => $this->staff['email'],
            'category' => '',
            'question' => '',
            'answer' => '',
            'sort_order' => 0,
            'status' => 1,
        ];
        $error = null;
        $isEdit = false;
        $pageTitle = 'Thêm câu hỏi & trả lời mới';
        $activeStaffPage = 'faqs';

        require __DIR__ . '/../Views/Backends/staff/faq_form.php';
    }

    public function faqStore()
    {
        $this->guardPost('index.php?controller=staff&action=faqs');

        $faq = $this->readFaqInput();
        $error = $this->validateFaq($faq);
        if ($error !== null) {
            http_response_code(422);
            $isEdit = false;
            $pageTitle = 'Thêm câu hỏi & trả lời mới';
            $activeStaffPage = 'faqs';
            require __DIR__ . '/../Views/Backends/staff/faq_form.php';
            return;
        }

        $this->contentModel->createFaq(
            (int) $this->staff['id'],
            $faq['category'],
            $faq['question'],
            $faq['answer'],
            $faq['sort_order'],
            $faq['status'],
            $this->staff['fullname'],
            $this->staff['email']
        );

        flash_set('staff', 'Đã thêm câu hỏi thường gặp mới.', 'success');
        redirect('index.php?controller=staff&action=faqs');
    }

    public function faqEdit()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $faq = $this->contentModel->findFaq($id);
        if (!$faq) {
            flash_set('staff', 'Không tìm thấy câu hỏi.', 'danger');
            redirect('index.php?controller=staff&action=faqs');
        }

        // Nếu câu hỏi đang chờ trả lời (status = 0), mặc định gợi ý chuyển sang "Hiển thị (1)" sau khi trả lời.
        if ((int) $faq['status'] === 0) {
            $faq['status'] = 1;
        }

        $error = null;
        $isEdit = true;
        $pageTitle = 'Trả lời / Chỉnh sửa câu hỏi #' . $id;
        $activeStaffPage = 'faqs';

        require __DIR__ . '/../Views/Backends/staff/faq_form.php';
    }

    public function faqUpdate()
    {
        $this->guardPost('index.php?controller=staff&action=faqs');

        $id = (int) ($_POST['id'] ?? 0);
        $existing = $this->contentModel->findFaq($id);
        if (!$existing) {
            flash_set('staff', 'Không tìm thấy câu hỏi.', 'danger');
            redirect('index.php?controller=staff&action=faqs');
        }

        $faq = $this->readFaqInput();
        $faq['id'] = $id;
        $faq['asker_name'] = $existing['asker_name'];
        $faq['asker_email'] = $existing['asker_email'];
        $error = $this->validateFaq($faq);

        if ($error !== null) {
            http_response_code(422);
            $isEdit = true;
            $pageTitle = 'Trả lời / Chỉnh sửa câu hỏi #' . $id;
            $activeStaffPage = 'faqs';
            require __DIR__ . '/../Views/Backends/staff/faq_form.php';
            return;
        }

        $this->contentModel->updateFaq(
            $id,
            $faq['category'],
            $faq['question'],
            $faq['answer'],
            $faq['sort_order'],
            $faq['status'],
            (int) $this->staff['id']
        );

        flash_set('staff', 'Đã lưu câu trả lời cho câu hỏi #' . $id . '.', 'success');
        redirect('index.php?controller=staff&action=faqs');
    }

    public function faqDelete()
    {
        $this->guardPost('index.php?controller=staff&action=faqs');

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0 && $this->contentModel->deleteFaq($id)) {
            flash_set('staff', 'Đã xóa câu hỏi #' . $id . '.', 'success');
        }

        redirect('index.php?controller=staff&action=faqs');
    }

    /* ============================================= 3. Phản hồi Sản phẩm */

    public function reviews()
    {
        $pageTitle = 'Phản hồi & Đánh giá sản phẩm';
        $activeStaffPage = 'reviews';

        $filter = (string) ($_GET['filter'] ?? 'all');
        if (!in_array($filter, ['all', 'unreplied', 'approved', 'hidden', 'pending'], true)) {
            $filter = 'all';
        }

        $perPage = 10;
        $totalItems = $this->reviewModel->countForStaff($filter);
        $pagination = paginate($totalItems, $perPage, max(1, (int) ($_GET['page'] ?? 1)));
        $reviews = $this->reviewModel->getForStaff($pagination['perPage'], $pagination['offset'], $filter);
        $allCount = $this->reviewModel->countForStaff('all');
        $unrepliedCount = $this->reviewModel->countUnreplied();

        $flash = flash_get('staff');

        require __DIR__ . '/../Views/Backends/staff/reviews.php';
    }

    public function replyReview()
    {
        $this->guardPost('index.php?controller=staff&action=reviews');

        $id = (int) ($_POST['id'] ?? 0);
        $reply = trim((string) ($_POST['staff_reply'] ?? ''));

        if ($id <= 0) {
            flash_set('staff', 'Không tìm thấy đánh giá sản phẩm.', 'danger');
        } elseif (mb_strlen($reply, 'UTF-8') < 3 || mb_strlen($reply, 'UTF-8') > 2000) {
            flash_set('staff', 'Nội dung phản hồi sản phẩm phải có từ 3 đến 2.000 ký tự.', 'danger');
        } else {
            $this->reviewModel->reply($id, $reply, (int) $this->staff['id']);
            flash_set('staff', 'Đã gửi phản hồi CSKH cho đánh giá #' . $id . '.', 'success');
        }

        redirect('index.php?controller=staff&action=reviews');
    }

    public function updateReviewStatus()
    {
        $this->guardPost('index.php?controller=staff&action=reviews');

        $id = (int) ($_POST['id'] ?? 0);
        $status = (int) ($_POST['status'] ?? 1);
        if ($id > 0 && in_array($status, [0, 1, 2], true)) {
            $this->reviewModel->updateStatus($id, $status);
            flash_set('staff', 'Đã cập nhật trạng thái đánh giá #' . $id . '.', 'success');
        }

        redirect('index.php?controller=staff&action=reviews');
    }

    public function deleteReview()
    {
        $this->guardPost('index.php?controller=staff&action=reviews');

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->reviewModel->delete($id);
            flash_set('staff', 'Đã xóa đánh giá #' . $id . '.', 'success');
        }

        redirect('index.php?controller=staff&action=reviews');
    }

    /* ========================================== 4. Bình luận Bài viết */

    public function comments()
    {
        $pageTitle = 'Quản lý & Gỡ bình luận vi phạm';
        $activeStaffPage = 'comments';

        $statusFilter = isset($_GET['status']) && $_GET['status'] !== '' ? (int) $_GET['status'] : null;
        $perPage = 12;
        $totalItems = $this->commentModel->countAll($statusFilter);
        $pagination = paginate($totalItems, $perPage, max(1, (int) ($_GET['page'] ?? 1)));
        $comments = $this->commentModel->getAllComments($pagination['perPage'], $pagination['offset'], $statusFilter);
        $visibleCount = $this->commentModel->countVisible();
        $hiddenCount = $this->commentModel->countHidden();
        $allCount = $this->commentModel->countAll();

        $flash = flash_get('staff');

        require __DIR__ . '/../Views/Backends/staff/comments.php';
    }

    public function updateCommentStatus()
    {
        $this->guardPost('index.php?controller=staff&action=comments');

        $id = (int) ($_POST['id'] ?? 0);
        $status = (int) ($_POST['status'] ?? 2);
        if ($id > 0 && in_array($status, [1, 2], true)) {
            $this->commentModel->updateCommentStatus($id, $status);
            $msg = $status === 2
                ? 'Đã gỡ (ẩn) bình luận #' . $id . ' khỏi bài viết.'
                : 'Đã khôi phục hiển thị bình luận #' . $id . '.';
            flash_set('staff', $msg, 'success');
        }

        redirect('index.php?controller=staff&action=comments');
    }

    public function deleteComment()
    {
        $this->guardPost('index.php?controller=staff&action=comments');

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->commentModel->deleteComment($id);
            flash_set('staff', 'Đã xóa bình luận #' . $id . '.', 'success');
        }

        redirect('index.php?controller=staff&action=comments');
    }

    /* ======================================================= Helpers */

    private function guardPost($redirectUrl)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
            flash_set('staff', 'Yêu cầu không hợp lệ hoặc phiên đã hết hạn.', 'danger');
            redirect($redirectUrl);
        }
    }

    private function readFaqInput()
    {
        return [
            'category' => trim((string) ($_POST['category'] ?? '')),
            'question' => trim((string) ($_POST['question'] ?? '')),
            'answer' => trim((string) ($_POST['answer'] ?? '')),
            'sort_order' => trim((string) ($_POST['sort_order'] ?? '0')),
            'status' => trim((string) ($_POST['status'] ?? '1')),
        ];
    }

    private function validateFaq(array &$faq)
    {
        if (mb_strlen($faq['question'], 'UTF-8') < 5 || mb_strlen($faq['question'], 'UTF-8') > 255) {
            return 'Câu hỏi phải có từ 5 đến 255 ký tự.';
        }
        if (!in_array($faq['status'], ['0', '1', '2'], true)) {
            return 'Trạng thái không hợp lệ.';
        }
        if ($faq['status'] === '1' && $faq['answer'] === '') {
            return 'Cần nhập nội dung câu trả lời trước khi công khai câu hỏi.';
        }
        if (mb_strlen($faq['answer'], 'UTF-8') > 10000) {
            return 'Câu trả lời không được vượt quá 10.000 ký tự.';
        }
        if (mb_strlen($faq['category'], 'UTF-8') > 100) {
            return 'Danh mục không được vượt quá 100 ký tự.';
        }
        $sortOrder = filter_var($faq['sort_order'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($sortOrder === false) {
            return 'Thứ tự hiển thị phải là số nguyên không âm.';
        }

        $faq['sort_order'] = $sortOrder;
        $faq['status'] = (int) $faq['status'];

        return null;
    }
}
