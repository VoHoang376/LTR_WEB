<?php if ($pagination['totalPages'] > 1): ?>
    <nav class="mt-4 flex flex-wrap items-center justify-between gap-3" aria-label="Phân trang">
        <p class="text-sm text-gray-500">
            Trang <?= (int) $pagination['page'] ?> / <?= (int) $pagination['totalPages'] ?>
            (<?= (int) $pagination['totalItems'] ?> mục)
        </p>
        <div class="flex items-center gap-2">
            <?php if ($pagination['page'] > 1): ?>
                <a
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50"
                    href="<?= $escape($paginationUrl . '&page=' . ($pagination['page'] - 1)) ?>">
                    Trước
                </a>
            <?php endif; ?>
            <?php if ($pagination['page'] < $pagination['totalPages']): ?>
                <a
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50"
                    href="<?= $escape($paginationUrl . '&page=' . ($pagination['page'] + 1)) ?>">
                    Tiếp
                </a>
            <?php endif; ?>
        </div>
    </nav>
<?php endif; ?>
