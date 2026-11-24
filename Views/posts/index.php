<?php
/** Quản trị – Danh sách bài viết / tin tức (Thành viên #4). */
$pageTitle = 'Quản lý bài viết';
$activeAdminPage = 'posts';
require __DIR__ . '/../Layouts/Backend/header.php';
?>

<div class="space-y-6">
    <?php if (!empty($flash)): ?>
        <div class="rounded-xl border px-4 py-3 text-sm <?= $flash['type'] === 'danger' ? 'border-red-200 bg-red-50 text-red-800' : 'border-green-200 bg-green-50 text-green-800' ?>" role="status">
            <?= $escape($flash['message']) ?>
        </div>
    <?php endif; ?>

    <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <form method="get" action="index.php" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="controller" value="admin">
                <input type="hidden" name="action" value="posts">
                <input type="search" name="q" value="<?= $escape($keyword ?? '') ?>"
                        placeholder="Tìm tiêu đề, từ khóa SEO..."
                        class="rounded-lg border border-gray-300 px-3.5 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                <button type="submit" class="rounded-lg border border-gray-200 bg-gray-50 px-3.5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
                    Tìm kiếm
                </button>
                <?php if (!empty($keyword)): ?>
                    <a href="index.php?controller=admin&amp;action=posts" class="text-xs text-gray-500 hover:underline">Xóa lọc</a>
                <?php endif; ?>
            </form>

            <a href="index.php?controller=admin&amp;action=post-create"
                class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                + Thêm bài viết
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-500 dark:border-gray-800 dark:bg-white/[0.02]">
                    <tr>
                        <th class="px-4 py-3">Ảnh</th>
                        <th class="px-4 py-3">Tiêu đề &amp; Slug</th>
                        <th class="px-4 py-3">Từ khóa SEO</th>
                        <th class="px-4 py-3">Trạng thái</th>
                        <th class="px-4 py-3">Ngày tạo</th>
                        <th class="px-4 py-3 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <?php if (empty($posts)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">Chưa có bài viết nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <img src="<?= $escape(upload_url($post['thumbnail'], 'posts')) ?>"
                                        alt="" class="h-12 w-16 rounded-lg object-cover border border-gray-200">
                                </td>
                                <td class="px-4 py-3">
                                    <a href="index.php?controller=post&amp;action=show&amp;slug=<?= rawurlencode($post['slug']) ?>"
                                        target="_blank" rel="noopener"
                                        class="font-medium text-gray-800 hover:text-brand-500 dark:text-white">
                                        <?= $escape($post['title']) ?>
                                    </a>
                                    <div class="mt-0.5 text-xs text-gray-400">/<?= $escape($post['slug']) ?></div>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 max-w-xs truncate">
                                    <?= $escape($post['meta_keywords'] ?: '—') ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <?php if ((int) ($post['status'] ?? 1) === 1): ?>
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Đã xuất bản</span>
                                    <?php else: ?>
                                        <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Bản nháp</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">
                                    <?= $escape(format_datetime($post['created_at'])) ?>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="index.php?controller=admin&amp;action=post-edit&amp;id=<?= (int) $post['id'] ?>"
                                            class="font-medium text-brand-500 hover:underline">Sửa</a>
                                        <form method="post" action="index.php?controller=admin&amp;action=post-delete"
                                                data-confirm="Bạn có chắc muốn xóa bài viết này?">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
                                            <button type="submit" class="font-medium text-red-600 hover:underline">Xóa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($pagination) && $pagination['totalPages'] > 1): ?>
            <nav class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4" aria-label="Phân trang">
                <p class="text-xs text-gray-500">
                    Trang <?= (int) $pagination['page'] ?> / <?= (int) $pagination['totalPages'] ?>
                    (Tổng <?= (int) $pagination['total'] ?> bài viết)
                </p>
                <div class="flex items-center gap-1">
                    <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                        <a href="<?= $escape(query_url(['page' => $i])) ?>"
                            class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $i === $pagination['page'] ? 'bg-brand-500 text-white' : 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </nav>
        <?php endif; ?>
    </section>
</div>

<?php require __DIR__ . '/../Layouts/Backend/footer.php'; ?>
