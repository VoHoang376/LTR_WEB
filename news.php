<?php
require_once 'models/PostModel.php';
$postModel = new PostModel();
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;
$allPosts = $postModel->getAllPosts();
if ($keyword !== '') {
    $allPosts = array_filter($allPosts, function($p) use ($keyword) {
        return stripos($p['title'], $keyword) !== false || stripos($p['summary'], $keyword) !== false;
    });
}
$totalPosts = count($allPosts);
$totalPages = ceil($totalPosts / $limit);
$posts = array_slice($allPosts, $offset, $limit);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tin tức</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-6xl mx-auto">
        <form method="GET" action="news.php" class="mb-6 flex gap-2">
            <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Tìm kiếm bài viết..." class="border p-2 rounded w-full">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Tìm</button>
        </form>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($posts as $post): ?>
                <div class="bg-white rounded shadow overflow-hidden">
                    <img src="<?= htmlspecialchars($post['thumbnail']) ?>" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h2 class="text-xl font-bold mb-2"><?= htmlspecialchars($post['title']) ?></h2>
                        <p class="text-gray-600 text-sm mb-4"><?= htmlspecialchars($post['summary']) ?></p>
                        <a href="news-detail.php?id=<?= $post['id'] ?>" class="text-blue-600 font-semibold">Đọc tiếp</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-6 flex justify-center gap-2">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="news.php?page=<?= $i ?>&keyword=<?= urlencode($keyword) ?>" class="px-3 py-1 border rounded <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-white' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>