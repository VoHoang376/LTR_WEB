<?php
/** Quản trị – Duyệt / Ẩn / Xóa bình luận (Thành viên #4). */
$pageTitle = 'Quản lý bình luận';
$activeAdminPage = 'comments';
require __DIR__ . '/../Layouts/Backend/header.php';
?>

<div class="space-y-6">
    <?php if (!empty($flash)): ?>
        <div class="rounded-xl border px-4 py-3 text-sm <?= $flash['type'] === 'danger' ? 'border-red-200 bg-red-50 text-red-800' : 'border-green-200 bg-green-50 text-green-800' ?>" role="status">
            <?= $escape($flash['message']) ?>
        </div>
    <?php endif; ?>

    <!-- Bộ lọc trạng thái -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
            <a href="index.php?controller=admin&amp;action=comments"
                class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $statusFilter === null ? 'bg-brand-500 text-white' : 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-50' ?>">
                Tất cả (<?= (int) $allCount ?>)
            </a>
            <a href="index.php?controller=admin&amp;action=comments&amp;status=0"
                class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $statusFilter === 0 ? 'bg-amber-500 text-white' : 'border border-amber-200 bg-white text-amber-700 hover:bg-amber-50' ?>">
                Chờ duyệt (<?= (int) $pendingCount ?>)
            </a>
            <a href="index.php?controller=admin&amp;action=comments&amp;status=1"
                class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $statusFilter === 1 ? 'bg-emerald-600 text-white' : 'border border-emerald-200 bg-white text-emerald-700 hover:bg-emerald-50' ?>">
                Đã duyệt
            </a>
            <a href="index.php?controller=admin&amp;action=comments&amp;status=2"
                class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $statusFilter === 2 ? 'bg-gray-600 text-white' : 'border border-gray-200 bg-white text-gray-600 hover:bg-gray-50' ?>">
                Đã ẩn
            </a>
        </div>
        <span class="text-xs text-gray-500">
            Trang <?= (int) $pagination['page'] ?> / <?= (int) $pagination['totalPages'] ?> (<?= (int) $pagination['total'] ?> bình luận)
        </span>
    </div>

    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-500 dark:border-gray-800 dark:bg-white/[0.02]">
                    <tr>
                        <th class="px-4 py-3.5">ID</th>
                        <th class="px-4 py-3.5">Người gửi</th>
                        <th class="px-4 py-3.5">Bài viết</th>
                        <th class="px-4 py-3.5">Nội dung bình luận</th>
                        <th class="px-4 py-3.5">Trạng thái</th>
                        <th class="px-4 py-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <?php if (empty($comments)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">Chưa có bình luận nào trong mục này.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <?php $st = (int) $comment['status']; ?>
                            <tr>
                                <td class="px-4 py-3.5 font-mono text-xs text-gray-400">#<?= (int) $comment['id'] ?></td>
                                <td class="px-4 py-3.5">
                                    <div class="font-medium text-gray-800 dark:text-white"><?= $escape($comment['author_name']) ?></div>
                                    <div class="text-xs text-gray-400"><?= $escape($comment['author_email']) ?></div>
                                </td>
                                <td class="px-4 py-3.5 max-w-xs">
                                    <a href="index.php?controller=post&amp;action=show&amp;slug=<?= rawurlencode($comment['post_slug']) ?>#comments"
                                        target="_blank" rel="noopener"
                                        class="text-xs font-medium text-brand-500 hover:underline">
                                        <?= $escape($comment['post_title']) ?>
                                    </a>
                                    <div class="text-[11px] text-gray-400"><?= $escape(format_datetime($comment['created_at'])) ?></div>
                                </td>
                                <td class="px-4 py-3.5 max-w-md text-xs text-gray-700 dark:text-gray-200">
                                    <?= $escape($comment['content']) ?>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <?php if ($st === 1): ?>
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Đã duyệt</span>
                                    <?php elseif ($st === 2): ?>
                                        <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Đã ẩn</span>
                                    <?php else: ?>
                                        <span class="rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700">Chờ duyệt</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <?php if ($st !== 1): ?>
                                            <form method="post" action="index.php?controller=admin&amp;action=comment-status" class="inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= (int) $comment['id'] ?>">
                                                <input type="hidden" name="status" value="1">
                                                <button type="submit" class="rounded border border-emerald-200 px-2 py-1 text-xs font-medium text-emerald-700 hover:bg-emerald-50">
                                                    Duyệt
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form method="post" action="index.php?controller=admin&amp;action=comment-status" class="inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= (int) $comment['id'] ?>">
                                                <input type="hidden" name="status" value="2">
                                                <button type="submit" class="rounded border border-gray-200 px-2 py-1 text-xs text-gray-600 hover:bg-gray-100">
                                                    Ẩn
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <form method="post" action="index.php?controller=admin&amp;action=comment-delete" class="inline"
                                                data-confirm="Bạn có chắc muốn xóa bình luận này?">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int) $comment['id'] ?>">
                                            <button type="submit" class="rounded border border-red-200 px-2 py-1 text-xs text-red-600 hover:bg-red-50">
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($pagination['totalPages'] > 1): ?>
            <nav class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-5 py-3" aria-label="Phân trang">
                <span class="text-xs text-gray-500">Trang <?= (int) $pagination['page'] ?> / <?= (int) $pagination['totalPages'] ?></span>
                <div class="flex items-center gap-1">
                    <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                        <a href="<?= $escape(query_url(['page' => $i])) ?>"
                            class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $i === $pagination['page'] ? 'bg-brand-500 text-white' : 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-100' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </nav>
        <?php endif; ?>
    </section>
</div>

<?php require __DIR__ . '/../Layouts/Backend/footer.php'; ?>
