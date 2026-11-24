<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$includeQuill = true;
$formAction = $isEdit
    ? 'index.php?controller=adminAbout&action=update'
    : 'index.php?controller=adminAbout&action=store';
$imageUrl = !empty($section['image_url'])
    ? upload_url($section['image_url'], 'about')
    : null;
require __DIR__ . '/../../Layouts/Backend/header.php';
?>
<?php if ($error !== null): ?>
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
        <?= $escape($error) ?>
    </div>
<?php endif; ?>
<section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
    <form id="about-section-form" method="post" enctype="multipart/form-data" action="<?= $escape($formAction) ?>">
        <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= (int) $section['id'] ?>">
        <?php endif; ?>
        <div class="mb-4">
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300" for="section_title">Tiêu đề mục</label>
            <input
                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm outline-none focus:border-brand-500"
                id="section_title"
                name="section_title"
                type="text"
                value="<?= $escape($section['section_title']) ?>"
                maxlength="255"
                required>
        </div>
        <div class="mb-4">
            <span class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nội dung</span>
            <div id="about-editor" class="about-editor mb-3"></div>
            <label class="sr-only" for="about-content">Nội dung giới thiệu</label>
            <textarea
                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm"
                id="about-content"
                name="content"
                rows="8"
                required><?= $escape($section['content']) ?></textarea>
            <p class="mt-2 text-xs text-gray-500">Có thể định dạng tiêu đề, đoạn văn, danh sách và liên kết.</p>
        </div>
        <div class="mb-4 grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300" for="image">Ảnh minh họa (JPG, PNG, WebP; tối đa 2 MB)</label>
                <input
                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                    id="image"
                    name="image"
                    type="file"
                    accept="image/jpeg,image/png,image/webp">
                <?php if ($imageUrl !== null): ?>
                    <div class="mt-3 flex items-center justify-between gap-3 rounded-lg border border-gray-200 bg-gray-50 p-2.5">
                        <img class="h-20 rounded-lg object-cover" src="<?= $escape($imageUrl) ?>" alt="Ảnh hiện tại">
                        <label class="flex items-center gap-2 text-xs font-medium text-red-600 cursor-pointer select-none">
                            <input type="checkbox" name="remove_image" value="1" class="h-4 w-4 rounded border-gray-300 text-red-600">
                            <span>Gỡ ảnh minh họa (không dùng ảnh)</span>
                        </label>
                    </div>
                <?php endif; ?>
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
                    value="<?= (int) $section['sort_order'] ?>"
                    required>
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <button class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white" type="submit">Lưu</button>
            <a class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700" href="index.php?controller=adminAbout&amp;action=index">Quay lại</a>
        </div>
    </form>
</section>
<script
    src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"
    integrity="sha384-utBUCeG4SYaCm4m7GQZYr8Hy8Fpy3V4KGjBZaf4WTKOcwhCYpt/0PfeEe3HNlwx8"
    crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('about-section-form');
    var content = document.getElementById('about-content');
    var editorElement = document.getElementById('about-editor');

    if (typeof Quill === 'undefined' || !form || !content || !editorElement) {
        return;
    }

    var editor = new Quill(editorElement, {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['blockquote', 'link'],
                ['clean']
            ]
        }
    });

    if (content.value.trim() !== '') {
        editor.root.innerHTML = content.value;
    }
    content.hidden = true;
    content.required = false;

    form.addEventListener('submit', function (event) {
        content.value = editor.root.innerHTML;
        if (editor.getText().trim() === '') {
            event.preventDefault();
            window.alert('Vui lòng nhập nội dung giới thiệu.');
            editor.focus();
        }
    });
});
</script>
<?php require __DIR__ . '/../../Layouts/Backend/footer.php'; ?>
