<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý bình luận</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-6">Quản lý Bình luận</h1>
        <table class="w-full border-collapse border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Bài viết ID</th>
                    <th class="border p-2">User ID</th>
                    <th class="border p-2">Nội dung</th>
                    <th class="border p-2">Trạng thái</th>
                    <th class="border p-2">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td class="border p-2 text-center"><?= $comment['id'] ?></td>
                        <td class="border p-2 text-center"><?= $comment['post_id'] ?></td>
                        <td class="border p-2 text-center"><?= $comment['user_id'] ?></td>
                        <td class="border p-2"><?= htmlspecialchars($comment['content']) ?></td>
                        <td class="border p-2 text-center">
                            <?= $comment['status'] == 1 ? '<span class="text-green-600 font-semibold">Đã duyệt</span>' : '<span class="text-yellow-600 font-semibold">Chờ duyệt</span>' ?>
                        </td>
                        <td class="border p-2 text-center">
                            <?php if ($comment['status'] == 0): ?>
                                <a href="index.php?controller=comment&action=approve&id=<?= $comment['id'] ?>" class="text-green-600 mr-2">Duyệt</a>
                            <?php endif; ?>
                            <a href="index.php?controller=comment&action=delete&id=<?= $comment['id'] ?>" onclick="return confirm('Xóa bình luận này?')" class="text-red-600">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>