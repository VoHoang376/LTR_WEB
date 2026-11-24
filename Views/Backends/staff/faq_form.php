<?php
$activeStaffPage = 'faqs';
$formAction = $isEdit
    ? 'index.php?controller=staff&action=faq-update'
    : 'index.php?controller=staff&action=faq-store';
require __DIR__ . '/../../Layouts/Backend/staff_header.php';
?>

<div class="max-w-3xl">
    <?php if (!empty($error)): ?>
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <?= $escape($error) ?>
        </div>
    <?php endif; ?>

    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <?php if ($isEdit && !empty($faq['asker_name'])): ?>
            <div class="mb-5 rounded-xl bg-blue-50/70 border border-blue-100 px-4 py-3 text-xs text-blue-900">
                <strong>Người đặt câu hỏi:</strong> <?= $escape($faq['asker_name']) ?>
                <?= !empty($faq['asker_email']) ? ' (' . $escape($faq['asker_email']) . ')' : '' ?>
                <?php if (!empty($faq['created_at'])): ?> · Gửi lúc <?= $escape(format_datetime($faq['created_at'])) ?><?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= $escape($formAction) ?>" class="space-y-4">
            <?= csrf_field() ?>
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int) $faq['id'] ?>">
            <?php endif; ?>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700" for="faq-question">Câu hỏi <span class="text-red-500">*</span></label>
                <input type="text" id="faq-question" name="question" value="<?= $escape($faq['question']) ?>"
                       minlength="5" maxlength="255" required
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700" for="faq-answer">Nội dung trả lời của CSKH <span class="text-red-500">*</span></label>
                <textarea id="faq-answer" name="answer" rows="6" maxlength="10000"
                          placeholder="Nhập câu trả lời chi tiết cho khách hàng..."
                          class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none"><?= $escape($faq['answer'] ?? '') ?></textarea>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700" for="faq-category">Chủ đề</label>
                    <input type="text" id="faq-category" name="category" value="<?= $escape($faq['category'] ?? '') ?>"
                           maxlength="100" placeholder="Bảo hành, Thanh toán..."
                           class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700" for="faq-sort">Thứ tự</label>
                    <input type="number" id="faq-sort" name="sort_order" min="0" value="<?= (int) $faq['sort_order'] ?>" required
                           class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700" for="faq-status">Trạng thái</label>
                    <select id="faq-status" name="status" class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm">
                        <option value="1" <?= (int) $faq['status'] === 1 ? 'selected' : '' ?>>Công khai trên trang FAQ</option>
                        <option value="0" <?= (int) $faq['status'] === 0 ? 'selected' : '' ?>>Chờ trả lời</option>
                        <option value="2" <?= (int) $faq['status'] === 2 ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                    Lưu câu trả lời
                </button>
                <a href="index.php?controller=staff&amp;action=faqs"
                   class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Quay lại
                </a>
            </div>
        </form>
    </section>
</div>

<?php require __DIR__ . '/../../Layouts/Backend/footer.php'; ?>
