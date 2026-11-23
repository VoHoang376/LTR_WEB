<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa bài viết</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-6">Cập nhật Bài viết</h1>
        <form action="index.php?controller=post&action=update&id=<?= $post['id'] ?>" method="POST" class="space-y-4">
            <div>
                <label class="block font-semibold">Tiêu đề</label>
                <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="block font-semibold">Slug</label>
                <input type="text" name="slug" value="<?= htmlspecialchars($post['slug']) ?>" required class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="block font-semibold">Tóm tắt</label>
                <textarea name="summary" rows="2" class="w-full border p-2 rounded"><?= htmlspecialchars($post['summary']) ?></textarea>
            </div>
            <div>
                <label class="block font-semibold">Nội dung</label>
                <textarea name="content" rows="6" required class="w-full border p-2 rounded"><?= htmlspecialchars($post['content']) ?></textarea>
            </div>
            <div>
                <label class="block font-semibold">Link ảnh Thumbnail</label>
                <input type="text" name="thumbnail" value="<?= htmlspecialchars($post['thumbnail']) ?>" class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="block font-semibold">Meta Keywords (SEO)</label>
                <input type="text" name="meta_keywords" value="<?= htmlspecialchars($post['meta_keywords']) ?>" class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="block font-semibold">Meta Description (SEO)</label>
                <textarea name="meta_description" rows="2" class="w-full border p-2 rounded"><?= htmlspecialchars($post['meta_description']) ?></textarea>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Cập nhật</button>
                <a href="index.php?controller=post&action=index" class="bg-gray-500 text-white px-4 py-2 rounded">Hủy</a>
            </div>
        </form>
    </div>
</body>
</html>