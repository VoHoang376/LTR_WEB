<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý bài viết</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto bg-white p-6 rounded shadow">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Quản lý Bài viết</h1>
            <a href="index.php?controller=post&action=create" class="bg-blue-600 text-white px-4 py-2 rounded">Thêm bài viết</a>
        </div>
        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Tìm kiếm bài viết..." class="border p-2 rounded mb-4 w-full">
        <table class="w-full border-collapse border" id="postTable">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Tiêu đề</th>
                    <th class="border p-2">Slug</th>
                    <th class="border p-2">Từ khóa SEO</th>
                    <th class="border p-2">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td class="border p-2 text-center"><?= $post['id'] ?></td>
                        <td class="border p-2"><?= htmlspecialchars($post['title']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($post['slug']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($post['meta_keywords']) ?></td>
                        <td class="border p-2 text-center">
                            <a href="index.php?controller=post&action=edit&id=<?= $post['id'] ?>" class="text-blue-600 mr-2">Sửa</a>
                            <a href="index.php?controller=post&action=delete&id=<?= $post['id'] ?>" onclick="return confirm('Xóa bài này?')" class="text-red-600">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        function filterTable() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("#postTable tbody tr");
            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
            });
        }
    </script>
</body>
</html>