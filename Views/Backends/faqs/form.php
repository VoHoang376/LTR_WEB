<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$formAction = $isEdit
    ? 'index.php?controller=adminFaq&action=update'
    : 'index.php?controller=adminFaq&action=store';
require __DIR__ . '/../../Layouts/Backend/header.php';
?>
<?php if ($error !== null): ?>
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
        <?= $escape($error) ?>
    </div>
<?php endif; ?>
<section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
    <form id="faq-form" method="post" action="<?= $escape($formAction) ?>">
        <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= (int) $faq['id'] ?>">
        <?php endif; ?>
        <div class="mb-4">
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300" for="question">Câu hỏi</label>
            <input
                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm"
                id="question"
                name="question"
                type="text"
                value="<?= $escape($faq['question']) ?>"
                maxlength="255"
                required>
        </div>
        <div class="mb-4">
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300" for="answer">Câu trả lời</label>
            <textarea
                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm"
                id="answer"
                name="answer"
                rows="7"
                maxlength="10000"
                required><?= $escape($faq['answer']) ?></textarea>
        </div>
        <div class="mb-4 grid gap-4 md:grid-cols-3">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300" for="category">Danh mục (không bắt buộc)</label>
                <input
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm"
                    id="category"
                    name="category"
                    type="text"
                    value="<?= $escape($faq['category']) ?>"
                    maxlength="100">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300" for="sort_order">Thứ tự hiển thị</label>
                <input
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm"
                    id="sort_order"
                    name="sort_order"
                    type="number"
                    min="0"
                    max="2147483647"
                    value="<?= (int) $faq['sort_order'] ?>"
                    required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300" for="status">Trạng thái</label>
                <select class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm" id="status" name="status" required>
                    <option value="1" <?= (int) $faq['status'] === 1 ? 'selected' : '' ?>>Hiển thị</option>
                    <option value="0" <?= (int) $faq['status'] === 0 ? 'selected' : '' ?>>Ẩn</option>
                </select>
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <button class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white" type="submit">Lưu</button>
            <a class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700" href="index.php?controller=adminFaq&amp;action=index">Quay lại</a>
        </div>
    </form>
</section>
<script>
document.getElementById('faq-form').addEventListener('submit', function (event) {
    var question = document.getElementById('question');
    var answer = document.getElementById('answer');
    question.setCustomValidity(question.value.trim().length < 5 ? 'Câu hỏi cần ít nhất 5 ký tự.' : '');
    answer.setCustomValidity(answer.value.trim() === '' ? 'Vui lòng nhập câu trả lời.' : '');
    if (!this.checkValidity()) {
        event.preventDefault();
        this.reportValidity();
    }
});
</script>
<?php require __DIR__ . '/../../Layouts/Backend/footer.php'; ?>
