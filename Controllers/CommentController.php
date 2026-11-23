<?php
require_once 'models/CommentModel.php';
class CommentController {
    private $commentModel;
    public function __construct() {
        $this->commentModel = new CommentModel();
    }
    public function index() {
        $comments = $this->commentModel->getAllComments();
        require_once 'views/comments/index.php';
    }
    public function approve($id) {
        $this->commentModel->approveComment($id);
        header("Location: index.php?controller=comment&action=index");
        exit();
    }
    public function delete($id) {
        $this->commentModel->deleteComment($id);
        header("Location: index.php?controller=comment&action=index");
        exit();
    }
}
?>