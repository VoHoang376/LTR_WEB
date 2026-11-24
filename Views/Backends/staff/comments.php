<?php
$pageTitle = 'Quản lý & Gỡ bình luận vi phạm';
$activeStaffPage = 'comments';
require __DIR__ . '/../../Layouts/Backend/staff_header.php';
?>

<div class="space-y-6">
    <?php if (!empty($flash)): ?>
        <div class="rounded-xl border px-4 py-3 text-sm <?= $flash['type'] === 'danger' ? 'border-red-200 bg-red-50 text-red-800' : 'border-green-200 bg-green-50 text-green-800' ?>">
            <?= $escape($flash['message']) ?>
        </div>
    <?php endif; ?>

    <p class="text-xs text-gray-500">
        Bình luận của thành viên được hiển thị ngay trên bài viết. Nếu phát hiện nội dung không phù hợp, bạn có thể bấm <strong>Gỡ (Ẩn)</strong> hoặc <strong>Xóa vĩnh viễn</strong>.
    </p>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
            <a href="index.php?controller=staff&amp;action=comments"
               class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $statusFilter === null ? 'bg-brand-500 text-white' : 'border border-gray-200 bg-white text-gray-700' ?>">
                Tất cả (<?= (int) $allCount ?>)
            </a>
            <a href="index.php?controller=staff&amp;action=comments&amp;status=1"
               class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $statusFilter === 1 ? 'bg-emerald-600 text-white' : 'border border-emerald-200 bg-white text-emerald-700' ?>">
                Đang hiển thị (<?= (int) $visibleCount ?>)
            </a>
            <a href="index.php?controller=staff&amp;action=comments&amp;status=2"
               class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $statusFilter === 2 ? 'bg-gray-700 text-white' : 'border border-gray-200 bg-white text-gray-600' ?>">
                Đã gỡ / Ẩn (<?= (int) $hiddenCount ?>)
            </a>
        </div>
        <span class="text-xs text-gray-500">Trang <?= (int) $pagination['page'] ?> / <?= (int) $pagination['totalPages'] ?></span>
    </div>

    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3.5">ID</th>
                        <th class="px-4 py-3.5">Người gửi</th>
                        <th class="px-4 py-3.5">Bài viết</th>
                        <th class="px-4 py-3.5">Nội dung</th>
                        <th class="px-4 py-3.5">Trạng thái</th>
                        <th class="px-4 py-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($comments)): ?>
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Chưa có bình luận nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <?php $st = (int) $comment['status']; ?>
                            <tr>
                                <td class="px-4 py-3.5 font-mono text-xs text-gray-400">#<?= (int) $comment['id'] ?></td>
                                <td class="px-4 py-3.5">
                                    <div class="font-medium text-gray-800"><?= $escape($comment['author_name']) ?></div>
                                    <div class="text-xs text-gray-400"><?= $escape($comment['author_email']) ?></div>
                                </td>
                                <td class="px-4 py-3.5 max-w-xs">
                                    <a href="index.php?controller=post&amp;action=show&amp;slug=<?= rawurlencode($comment['post_slug']) ?>#comments"
                                       target="_blank" rel="noopener" class="text-xs font-medium text-brand-500 hover:underline">
                                        <?= $escape($comment['post_title']) ?>
                                    </a>
                                    <div class="text-[11px] text-gray-400"><?= $escape(format_datetime($comment['created_at'])) ?></div>
                                </td>
                                <td class="px-4 py-3.5 max-w-md text-xs text-gray-700"><?= $escape($comment['content']) ?></td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <?php if ($st === 1): ?>
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Đang hiển thị</span>
                                    <?php else: ?>
                                        <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Đã gỡ (Ẩn)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <?php if ($st === 1): ?>
                                            <form method="post" action="index.php?controller=staff&amp;action=comment-status" class="inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= (int) $comment['id'] ?>">
                                                <input type="hidden" name="status" value="2">
                                                <button type="submit" class="rounded border border-amber-300 px-2.5 py-1 text-xs font-medium text-amber-800 hover:bg-amber-50">Gỡ (Ẩn)</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="post" action="index.php?controller=staff&amp;action=comment-status" class="inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= (int) $comment['id'] ?>">
                                                <input type="hidden" name="status" value="1">
                                                <button type="submit" class="rounded border border-emerald-200 px-2.5 py-1 text-xs font-medium text-emerald-700 hover:bg-emerald-50">Khôi phục</button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="post" action="index.php?controller=staff&amp;action=comment-delete" class="inline"
                                              data-confirm="Xóa vĩnh viễn bình luận này?">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int) $comment['id'] ?>">
                                            <button type="submit" class="rounded border border-red-200 px-2.5 py-1 text-xs text-red-600 hover:bg-red-50">Xóa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php require __DIR__ . '/../../Layouts/Backend/footer.php'; ?>
