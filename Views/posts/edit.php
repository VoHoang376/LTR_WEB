<?php
/** Quản trị – Chỉnh sửa bài viết (Thành viên #4). */
$pageTitle = 'Chỉnh sửa bài viết';
$activeAdminPage = 'posts';
require __DIR__ . '/../Layouts/Backend/header.php';
?>

<div class="max-w-4xl">
    <?php if (!empty($error)): ?>
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <?= $escape($error) ?>
        </div>
    <?php endif; ?>

    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <form method="post" action="index.php?controller=admin&amp;action=post-update" enctype="multipart/form-data" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
            <input type="hidden" name="MAX_FILE_SIZE" value="2097152">

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300" for="post-title">
                    Tiêu đề bài viết <span class="text-red-500">*</span>
                </label>
                <input type="text" id="post-title" name="title" value="<?= $escape($post['title']) ?>"
                       minlength="3" maxlength="255" required
                       class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300" for="post-slug">
                        Đường dẫn (Slug)
                    </label>
                    <input type="text" id="post-slug" name="slug" value="<?= $escape($post['slug']) ?>"
                           maxlength="255" required
                           class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300" for="post-status">
                        Trạng thái
                    </label>
                    <select id="post-status" name="status"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
                        <option value="1" <?= (string) ($post['status'] ?? '1') === '1' ? 'selected' : '' ?>>Đã xuất bản</option>
                        <option value="0" <?= (string) ($post['status'] ?? '1') === '0' ? 'selected' : '' ?>>Bản nháp</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300" for="post-summary">
                    Tóm tắt ngắn
                </label>
                <textarea id="post-summary" name="summary" rows="2"
                          class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none"><?= $escape($post['summary'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300" for="post-content">
                    Nội dung chi tiết <span class="text-red-500">*</span>
                </label>
                <textarea id="post-content" name="content" rows="8" minlength="10" required
                          class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none"><?= $escape($post['content']) ?></textarea>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300" for="post-thumbnail">
                    Thay ảnh đại diện mới (JPG, PNG, WebP – tối đa 2 MB; để trống nếu giữ ảnh cũ)
                </label>
                <input type="file" id="post-thumbnail" name="thumbnail" accept="image/jpeg,image/png,image/webp"
                       class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <?php if (!empty($post['thumbnail'])): ?>
                    <div class="mt-3 flex items-center justify-between gap-3 rounded-lg border border-gray-200 bg-gray-50 p-2.5">
                        <img src="<?= $escape(upload_url($post['thumbnail'], 'posts')) ?>"
                             alt="Ảnh hiện tại" class="h-20 rounded-lg border border-gray-200 object-cover">
                        <label class="flex items-center gap-2 text-xs font-medium text-red-600 cursor-pointer select-none">
                            <input type="checkbox" name="remove_thumbnail" value="1" class="h-4 w-4 rounded border-gray-300 text-red-600">
                            <span>Gỡ ảnh đại diện (về không có ảnh / mặc định)</span>
                        </label>
                    </div>
                <?php endif; ?>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300" for="post-keywords">
                        Từ khóa SEO (Meta Keywords)
                    </label>
                    <input type="text" id="post-keywords" name="meta_keywords" value="<?= $escape($post['meta_keywords'] ?? '') ?>"
                           maxlength="255"
                           class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300" for="post-meta-desc">
                        Mô tả SEO (Meta Description)
                    </label>
                    <input type="text" id="post-meta-desc" name="meta_description" value="<?= $escape($post['meta_description'] ?? '') ?>"
                           maxlength="320"
                           class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                    Cập nhật bài viết
                </button>
                <a href="index.php?controller=admin&amp;action=posts"
                   class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Quay lại
                </a>
            </div>
        </form>
    </section>
</div>

<?php require __DIR__ . '/../Layouts/Backend/footer.php'; ?>
