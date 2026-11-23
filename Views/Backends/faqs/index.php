<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
require __DIR__ . '/../../Layouts/Backend/header.php';
?>
<?php if ($message !== null): ?>
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status">
        <?= $escape($message) ?>
    </div>
<?php endif; ?>
<section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-gray-500">Thêm, chỉnh sửa, sắp xếp và ẩn câu hỏi thường gặp.</p>
        <a
            class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600"
            href="index.php?controller=adminFaq&amp;action=create">
            Thêm câu hỏi
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-3 py-3" scope="col">Câu hỏi</th>
                    <th class="px-3 py-3" scope="col">Danh mục</th>
                    <th class="px-3 py-3" scope="col">Thứ tự</th>
                    <th class="px-3 py-3" scope="col">Trạng thái</th>
                    <th class="px-3 py-3" scope="col">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                <?php foreach ($faqs as $faq): ?>
                    <tr>
                        <td class="px-3 py-3 font-medium text-gray-800 dark:text-white"><?= $escape($faq['question']) ?></td>
                        <td class="px-3 py-3 text-gray-600"><?= $escape($faq['category'] ?: '—') ?></td>
                        <td class="px-3 py-3 text-gray-600"><?= (int) $faq['sort_order'] ?></td>
                        <td class="px-3 py-3">
                            <?php if ((int) $faq['status'] === 1): ?>
                                <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">Đang hiển thị</span>
                            <?php else: ?>
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">Đang ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 py-3">
                            <div class="flex items-center gap-3">
                                <a
                                    class="font-medium text-brand-500"
                                    href="index.php?controller=adminFaq&amp;action=edit&amp;id=<?= (int) $faq['id'] ?>">
                                    Sửa
                                </a>
                                <form
                                    method="post"
                                    action="index.php?controller=adminFaq&amp;action=delete"
                                    data-confirm="Bạn có chắc muốn xóa câu hỏi này?">
                                    <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $faq['id'] ?>">
                                    <button class="font-medium text-red-600" type="submit">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$faqs): ?>
                    <tr>
                        <td class="px-3 py-8 text-center text-gray-500" colspan="5">Chưa có câu hỏi thường gặp.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php require __DIR__ . '/../partials/pagination.php'; ?>
</section>
<?php require __DIR__ . '/../../Layouts/Backend/footer.php'; ?>
