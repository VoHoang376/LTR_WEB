<?php

require_once __DIR__ . '/../Models/CommentModel.php';

/**
 * Thành viên #4: Quản trị bình luận (duyệt, ẩn, xóa, phân trang).
 */
class CommentController
{
    private $commentModel;

    public function __construct(PDO $db)
    {
        Permission::requireAdmin();
        $this->commentModel = new CommentModel($db);
    }

    public function adminIndex()
    {
        $statusFilter = isset($_GET['status']) && $_GET['status'] !== '' ? (int) $_GET['status'] : null;
        $perPage = 12;
        $totalItems = $this->commentModel->countAll($statusFilter);
        $pagination = paginate($totalItems, $perPage, max(1, (int) ($_GET['page'] ?? 1)));
        $comments = $this->commentModel->getAllComments($pagination['perPage'], $pagination['offset'], $statusFilter);
        $pendingCount = $this->commentModel->countPending();
        $allCount = $this->commentModel->countAll();

        $flash = flash_get('admin_comments');
        $pageTitle = 'Quản lý bình luận';
        $activeAdminPage = 'comments';

        require __DIR__ . '/../Views/comments/index.php';
    }

    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
            flash_set('admin_comments', 'Yêu cầu cập nhật không hợp lệ.', 'danger');
            redirect('index.php?controller=admin&action=comments');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $status = (int) ($_POST['status'] ?? 1);

        if ($id > 0 && in_array($status, [0, 1, 2], true)) {
            $this->commentModel->updateCommentStatus($id, $status);
            $labels = [0 => 'Chờ duyệt', 1 => 'Đã duyệt', 2 => 'Đã ẩn'];
            flash_set('admin_comments', 'Đã chuyển bình luận #' . $id . ' sang "' . $labels[$status] . '".', 'success');
        } else {
            flash_set('admin_comments', 'Dữ liệu trạng thái không hợp lệ.', 'danger');
        }

        redirect('index.php?controller=admin&action=comments');
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
            flash_set('admin_comments', 'Yêu cầu xóa không hợp lệ.', 'danger');
            redirect('index.php?controller=admin&action=comments');
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->commentModel->deleteComment($id);
            flash_set('admin_comments', 'Đã xóa bình luận #' . $id . '.', 'success');
        }

        redirect('index.php?controller=admin&action=comments');
    }
}
