<?php
session_start();
require_once 'models/PostModel.php';
require_once 'models/CommentModel.php';
$postModel = new PostModel();
$commentModel = new CommentModel();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = $postModel->getPostById($id);
if (!$post) {
    die("Bài viết không tồn tại");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $content = trim($_POST['content']);
    if ($content !== '') {
        $commentModel->createComment($id, $_SESSION['user_id'], $content);
        header("Location: news-detail.php?id=" . $id);
        exit();
    }
}
$allComments = $commentModel->getAllComments();
$comments = array_filter($allComments, function($c) use ($id) {
    return $c['post_id'] == $id && $c['status'] == 1;
});
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="keywords" content="<?= htmlspecialchars($post['meta_keywords']) ?>">
    <meta name="description" content="<?= htmlspecialchars($post['meta_description']) ?>">
    <title><?= htmlspecialchars($post['title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <a href="news.php" class="text-blue-600 mb-4 inline-block">&larr; Quay lại</a>
        <h1 class="text-3xl font-bold mb-4"><?= htmlspecialchars($post['title']) ?></h1>
        <img src="<?= htmlspecialchars($post['thumbnail']) ?>" class="w-full h-80 object-cover rounded mb-6">
        <div class="prose max-w-none mb-8"><?= nl2br(htmlspecialchars($post['content'])) ?></div>
        <hr class="my-6">
        <h3 class="text-xl font-bold mb-4">Bình luận</h3>
        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" class="mb-6">
                <textarea name="content" rows="3" required class="w-full border p-2 rounded mb-2" placeholder="Nhập bình luận..."></textarea>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Gửi bình luận</button>
            </form>
        <?php else: ?>
            <p class="text-red-500 mb-6">Vui lòng đăng nhập để bình luận.</p>
        <?php endif; ?>
        <div class="space-y-4">
            <?php foreach ($comments as $comment): ?>
                <div class="border-b pb-2">
                    <p class="font-semibold">User #<?= htmlspecialchars($comment['user_id']) ?></p>
                    <p class="text-gray-700"><?= htmlspecialchars($comment['content']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>