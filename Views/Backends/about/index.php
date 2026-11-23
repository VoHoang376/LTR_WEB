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
        <p class="text-sm text-gray-500">Quản lý nội dung trang Giới thiệu, tầm nhìn và sứ mệnh.</p>
        <a
            class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600"
            href="index.php?controller=adminAbout&amp;action=create">
            Thêm nội dung
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-3 py-3" scope="col">Ảnh</th>
                    <th class="px-3 py-3" scope="col">Tiêu đề</th>
                    <th class="px-3 py-3" scope="col">Thứ tự</th>
                    <th class="px-3 py-3" scope="col">Cập nhật</th>
                    <th class="px-3 py-3" scope="col">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                <?php foreach ($sections as $section): ?>
                    <tr>
                        <td class="px-3 py-3">
                            <?php if (!empty($section['image_url'])): ?>
                                <img
                                    class="h-12 w-16 rounded-lg object-cover"
                                    src="<?= $escape('Public/uploads/' . rawurlencode(basename($section['image_url']))) ?>"
                                    alt="">
                            <?php else: ?>
                                <span class="text-gray-400">Chưa có ảnh</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 py-3">
                            <span class="font-medium text-gray-800 dark:text-white"><?= $escape($section['section_title']) ?></span>
                            <span class="mt-1 block max-w-md truncate text-gray-500"><?= $escape(mb_substr(strip_tags((string) ($section['content'] ?? '')), 0, 100, 'UTF-8')) ?></span>
                        </td>
                        <td class="px-3 py-3 text-gray-600"><?= (int) $section['sort_order'] ?></td>
                        <td class="px-3 py-3 text-gray-600"><?= $escape($section['created_at']) ?></td>
                        <td class="px-3 py-3">
                            <div class="flex items-center gap-3">
                                <a
                                    class="font-medium text-brand-500"
                                    href="index.php?controller=adminAbout&amp;action=edit&amp;id=<?= (int) $section['id'] ?>">
                                    Sửa
                                </a>
                                <form
                                    method="post"
                                    action="index.php?controller=adminAbout&amp;action=delete"
                                    data-confirm="Bạn có chắc muốn xóa mục giới thiệu này?">
                                    <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $section['id'] ?>">
                                    <button class="font-medium text-red-600" type="submit">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$sections): ?>
                    <tr>
                        <td class="px-3 py-8 text-center text-gray-500" colspan="5">Chưa có nội dung giới thiệu.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php require __DIR__ . '/../partials/pagination.php'; ?>
</section>
<?php require __DIR__ . '/../../Layouts/Backend/footer.php'; ?>
