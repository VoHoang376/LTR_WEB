<?php
$pageTitle = 'Phản hồi & Đánh giá sản phẩm';
$activeStaffPage = 'reviews';
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
            <a href="index.php?controller=staff&amp;action=reviews"
               class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $filter === 'all' ? 'bg-brand-500 text-white' : 'border border-gray-200 bg-white text-gray-700' ?>">
                Tất cả (<?= (int) $allCount ?>)
            </a>
            <a href="index.php?controller=staff&amp;action=reviews&amp;filter=unreplied"
               class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $filter === 'unreplied' ? 'bg-amber-500 text-white' : 'border border-amber-200 bg-white text-amber-700' ?>">
                Chưa phản hồi (<?= (int) $unrepliedCount ?>)
            </a>
            <a href="index.php?controller=staff&amp;action=reviews&amp;filter=approved"
               class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $filter === 'approved' ? 'bg-emerald-600 text-white' : 'border border-emerald-200 bg-white text-emerald-700' ?>">
                Đang hiển thị
            </a>
            <a href="index.php?controller=staff&amp;action=reviews&amp;filter=hidden"
               class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $filter === 'hidden' ? 'bg-gray-600 text-white' : 'border border-gray-200 bg-white text-gray-600' ?>">
                Đã ẩn
            </a>
        </div>
        <span class="text-xs text-gray-500">Trang <?= (int) $pagination['page'] ?> / <?= (int) $pagination['totalPages'] ?></span>
    </div>

    <div class="space-y-4">
        <?php if (empty($reviews)): ?>
            <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-400">
                Chưa có đánh giá sản phẩm nào trong mục này.
            </div>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <?php $st = (int) $review['status']; ?>
                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-3 border-b border-gray-100 pb-3">
                        <div>
                            <a href="index.php?controller=product&amp;action=show&amp;slug=<?= rawurlencode($review['product_slug']) ?>#reviews"
                               target="_blank" rel="noopener"
                               class="text-sm font-semibold text-brand-500 hover:underline">
                                <?= $escape($review['product_name']) ?> &nearr;
                            </a>
                            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs">
                                <span class="font-semibold text-amber-500"><?= str_repeat('★', (int) $review['rating']) . str_repeat('☆', 5 - (int) $review['rating']) ?></span>
                                <span class="font-medium text-gray-800"><?= $escape($review['author_name']) ?></span>
                                <span class="text-gray-400">(<?= $escape($review['author_email']) ?>)</span>
                                <span class="text-gray-400">· <?= $escape(format_datetime($review['created_at'])) ?></span>
                                <?php if ($st === 1): ?>
                                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-medium text-emerald-700">Hiển thị</span>
                                <?php else: ?>
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-600">Đang ẩn</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <form method="post" action="index.php?controller=staff&amp;action=review-status" class="inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $review['id'] ?>">
                                <input type="hidden" name="status" value="<?= $st === 1 ? 2 : 1 ?>">
                                <button type="submit" class="rounded border border-gray-200 px-2.5 py-1 text-xs text-gray-600 hover:bg-gray-50">
                                    <?= $st === 1 ? 'Ẩn' : 'Hiển thị' ?>
                                </button>
                            </form>
                            <form method="post" action="index.php?controller=staff&amp;action=review-delete" class="inline"
                                  data-confirm="Bạn có chắc muốn xóa đánh giá này?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $review['id'] ?>">
                                <button type="submit" class="rounded border border-red-200 px-2.5 py-1 text-xs text-red-600 hover:bg-red-50">Xóa</button>
                            </form>
                        </div>
                    </div>

                    <p class="mt-3 rounded-xl bg-gray-50 p-3.5 text-sm text-gray-700" style="white-space: pre-line;">
                        <?= $escape($review['comment']) ?>
                    </p>

                    <?php if (!empty($review['staff_reply'])): ?>
                        <div class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50/70 p-3.5 text-sm text-emerald-900">
                            <p class="text-xs font-semibold text-emerald-700 mb-1">
                                Phản hồi CSKH<?= !empty($review['staff_name']) ? ' (' . $escape($review['staff_name']) . ')' : '' ?><?= !empty($review['replied_at']) ? ' · ' . $escape(format_datetime($review['replied_at'])) : '' ?>:
                            </p>
                            <p style="white-space: pre-line;"><?= $escape($review['staff_reply']) ?></p>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="index.php?controller=staff&amp;action=review-reply" class="mt-4 space-y-2">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $review['id'] ?>">
                        <label class="block text-xs font-medium text-gray-600" for="review-reply-<?= (int) $review['id'] ?>">
                            <?= !empty($review['staff_reply']) ? 'Chỉnh sửa phản hồi CSKH:' : 'Trả lời phản hồi sản phẩm (hiển thị công khai dưới đánh giá):' ?>
                        </label>
                        <textarea id="review-reply-<?= (int) $review['id'] ?>" name="staff_reply" rows="2" required minlength="3" maxlength="2000"
                                  placeholder="Cảm ơn quý khách đã phản hồi..."
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none"><?= $escape($review['staff_reply'] ?? '') ?></textarea>
                        <button type="submit" class="rounded-lg bg-brand-500 px-4 py-1.5 text-xs font-medium text-white hover:bg-brand-600">
                            Gửi phản hồi CSKH
                        </button>
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
