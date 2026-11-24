<?php
$pageTitle = 'Giải quyết liên hệ khách hàng';
$activeStaffPage = 'contacts';
require __DIR__ . '/../../Layouts/Backend/staff_header.php';
?>

<div class="space-y-6">
    <?php if (!empty($flash)): ?>
        <div class="rounded-xl border px-4 py-3 text-sm <?= $flash['type'] === 'danger' ? 'border-red-200 bg-red-50 text-red-800' : 'border-green-200 bg-green-50 text-green-800' ?>">
            <?= $escape($flash['message']) ?>
        </div>
    <?php endif; ?>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
            <a href="index.php?controller=staff&amp;action=contacts"
               class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $statusFilter === null ? 'bg-brand-500 text-white' : 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-50' ?>">
                Tất cả (<?= (int) $allCount ?>)
            </a>
            <a href="index.php?controller=staff&amp;action=contacts&amp;status=0"
               class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $statusFilter === 0 ? 'bg-amber-500 text-white' : 'border border-amber-200 bg-white text-amber-700 hover:bg-amber-50' ?>">
                Chưa xử lý (<?= (int) $unprocessedCount ?>)
            </a>
            <a href="index.php?controller=staff&amp;action=contacts&amp;status=1"
               class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $statusFilter === 1 ? 'bg-emerald-600 text-white' : 'border border-emerald-200 bg-white text-emerald-700 hover:bg-emerald-50' ?>">
                Đã phản hồi
            </a>
        </div>
        <span class="text-xs text-gray-500">Trang <?= (int) $pagination['page'] ?> / <?= (int) $pagination['totalPages'] ?> (<?= (int) $pagination['total'] ?> liên hệ)</span>
    </div>

    <div class="space-y-4">
        <?php if (empty($contacts)): ?>
            <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-400">
                Chưa có yêu cầu liên hệ nào trong mục này.
            </div>
        <?php else: ?>
            <?php foreach ($contacts as $contact): ?>
                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-3 border-b border-gray-100 pb-3">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-mono text-xs text-gray-400">#<?= (int) $contact['id'] ?></span>
                                <h2 class="text-base font-semibold text-gray-800"><?= $escape($contact['fullname']) ?></h2>
                                <?php if ((int) $contact['status'] === 1): ?>
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Đã phản hồi</span>
                                <?php else: ?>
                                    <span class="rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700">Chưa xử lý</span>
                                <?php endif; ?>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                <?= $escape($contact['email']) ?>
                                <?php if (!empty($contact['phone'])): ?> · <?= $escape($contact['phone']) ?><?php endif; ?>
                                · Gửi lúc <?= $escape(format_datetime($contact['created_at'])) ?>
                            </p>
                        </div>
                        <form method="post" action="index.php?controller=staff&amp;action=contact-delete"
                              data-confirm="Bạn có chắc muốn xóa liên hệ này?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int) $contact['id'] ?>">
                            <button type="submit" class="rounded border border-red-200 px-2.5 py-1 text-xs text-red-600 hover:bg-red-50">Xóa</button>
                        </form>
                    </div>

                    <div class="mt-3 rounded-xl bg-gray-50 p-3.5 text-sm text-gray-700" style="white-space: pre-line;">
                        <?= $escape($contact['message']) ?>
                    </div>

                    <?php if (!empty($contact['reply_message'])): ?>
                        <div class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50/70 p-3.5 text-sm text-emerald-900">
                            <p class="text-xs font-semibold text-emerald-700 mb-1">
                                Phản hồi đã ghi nhận<?= !empty($contact['handler_name']) ? ' bởi ' . $escape($contact['handler_name']) : '' ?><?= !empty($contact['replied_at']) ? ' (' . $escape(format_datetime($contact['replied_at'])) . ')' : '' ?>:
                            </p>
                            <p style="white-space: pre-line;"><?= $escape($contact['reply_message']) ?></p>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="index.php?controller=staff&amp;action=contact-reply" class="mt-4 space-y-2">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $contact['id'] ?>">
                        <label class="block text-xs font-medium text-gray-600" for="reply-contact-<?= (int) $contact['id'] ?>">
                            <?= !empty($contact['reply_message']) ? 'Cập nhật nội dung phản hồi:' : 'Nhập nội dung giải quyết / phản hồi cho khách hàng:' ?>
                        </label>
                        <textarea id="reply-contact-<?= (int) $contact['id'] ?>" name="reply_message" rows="2" required minlength="5" maxlength="3000"
                                  placeholder="Ghi nội dung phản hồi đã/sẽ gửi khách hàng..."
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none"><?= $escape($contact['reply_message'] ?? '') ?></textarea>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="submit" class="rounded-lg bg-brand-500 px-4 py-1.5 text-xs font-medium text-white hover:bg-brand-600">
                                Lưu phản hồi &amp; Đánh dấu Đã xử lý
                            </button>
                        </div>
                    </form>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($pagination['totalPages'] > 1): ?>
        <nav class="flex items-center justify-between pt-2" aria-label="Phân trang">
            <span class="text-xs text-gray-500">Trang <?= (int) $pagination['page'] ?> / <?= (int) $pagination['totalPages'] ?></span>
            <div class="flex gap-1">
                <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                    <a href="<?= $escape(query_url(['page' => $i])) ?>"
                       class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $i === $pagination['page'] ? 'bg-brand-500 text-white' : 'border border-gray-200 bg-white text-gray-700' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </nav>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../../Layouts/Backend/footer.php'; ?>
