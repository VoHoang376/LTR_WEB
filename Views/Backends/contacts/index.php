<?php
$pageTitle = 'Quản lý liên hệ';
$activeAdminPage = 'contacts';
require __DIR__ . '/../../Layouts/Backend/header.php';
?>

<div class="space-y-6">
    <!-- Notifications -->
    <?php if (!empty($success)): ?>
        <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-900/20 dark:text-green-300">
            <?= $escape($success) ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300">
            <?= $escape($error) ?>
        </div>
    <?php endif; ?>

    <!-- Action Bar & Filter -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-wrap items-center gap-2">
            <a href="index.php?controller=admin&amp;action=contacts"
               class="rounded-lg px-3 py-1.5 text-xs font-medium transition <?= $statusFilter === null ? 'bg-brand-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700' ?>">
                Tất cả (<?= (int)$totalItems ?>)
            </a>
            <a href="index.php?controller=admin&amp;action=contacts&amp;status=0"
               class="rounded-lg px-3 py-1.5 text-xs font-medium transition <?= $statusFilter === 0 ? 'bg-amber-500 text-white' : 'bg-white text-amber-700 hover:bg-amber-50 border border-amber-200 dark:bg-gray-800 dark:text-amber-400 dark:border-gray-700' ?>">
                Chưa xử lý (<?= (int)$unprocessedCount ?>)
            </a>
            <a href="index.php?controller=admin&amp;action=contacts&amp;status=1"
               class="rounded-lg px-3 py-1.5 text-xs font-medium transition <?= $statusFilter === 1 ? 'bg-emerald-600 text-white' : 'bg-white text-emerald-700 hover:bg-emerald-50 border border-emerald-200 dark:bg-gray-800 dark:text-emerald-400 dark:border-gray-700' ?>">
                Đã phản hồi
            </a>
        </div>
        <div class="text-xs text-gray-500 dark:text-gray-400">
            Trang <?= (int)$page ?> / <?= (int)$totalPages ?> (Tổng <?= (int)$totalItems ?> liên hệ)
        </div>
    </div>

    <!-- Contacts Table -->
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-500 dark:border-gray-800 dark:bg-white/[0.02] dark:text-gray-400">
                    <tr>
                        <th class="px-5 py-3.5">ID</th>
                        <th class="px-5 py-3.5">Khách hàng</th>
                        <th class="px-5 py-3.5">Thông tin liên hệ</th>
                        <th class="px-5 py-3.5">Lời nhắn</th>
                        <th class="px-5 py-3.5">Thời gian</th>
                        <th class="px-5 py-3.5">Trạng thái</th>
                        <th class="px-5 py-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <?php if (empty($contacts)): ?>
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-gray-400">
                                Chưa có liên hệ nào trong danh mục này.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($contacts as $contact): ?>
                            <tr class="transition hover:bg-gray-50/70 dark:hover:bg-white/[0.02]">
                                <td class="px-5 py-4 font-mono text-xs text-gray-400">#<?= (int)$contact['id'] ?></td>
                                <td class="px-5 py-4 font-medium text-gray-800 dark:text-white">
                                    <?= $escape($contact['fullname']) ?>
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    <div class="text-gray-800 dark:text-gray-200"><?= $escape($contact['email']) ?></div>
                                    <?php if (!empty($contact['phone'])): ?>
                                        <div class="text-gray-400"><?= $escape($contact['phone']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 max-w-xs">
                                    <p class="truncate text-xs text-gray-600 dark:text-gray-300" title="<?= $escape($contact['message']) ?>">
                                        <?= $escape($contact['message']) ?>
                                    </p>
                                    <button type="button"
                                            onclick="alert('<?= $escape(addslashes($contact['fullname'])) ?> gửi:\n\n<?= $escape(addslashes($contact['message'])) ?>')"
                                            class="mt-1 text-[11px] text-brand-500 hover:underline">
                                        Xem trọn vẹn
                                    </button>
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-400 whitespace-nowrap">
                                    <?= date('d/m/Y H:i', strtotime($contact['created_at'])) ?>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <?php if ((int)$contact['status'] === 1): ?>
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                            ● Đã phản hồi
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                            ● Chưa xử lý
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Toggle Status Form -->
                                        <form method="post" action="index.php?controller=admin&amp;action=contact_status" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= $escape($adminCsrfToken) ?>">
                                            <input type="hidden" name="id" value="<?= (int)$contact['id'] ?>">
                                            <input type="hidden" name="status" value="<?= (int)$contact['status'] === 1 ? 0 : 1 ?>">
                                            <button type="submit"
                                                    class="rounded border border-gray-200 px-2 py-1 text-xs text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                                                    title="Chuyển trạng thái">
                                                <?= (int)$contact['status'] === 1 ? 'Chưa xử lý' : 'Đã phản hồi' ?>
                                            </button>
                                        </form>

                                        <!-- Delete Form -->
                                        <form method="post" action="index.php?controller=admin&amp;action=contact_delete" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa liên hệ này?');">
                                            <input type="hidden" name="csrf_token" value="<?= $escape($adminCsrfToken) ?>">
                                            <input type="hidden" name="id" value="<?= (int)$contact['id'] ?>">
                                            <button type="submit"
                                                    class="rounded border border-red-200 px-2 py-1 text-xs text-red-600 hover:bg-red-50 dark:border-red-900/50 dark:text-red-400 dark:hover:bg-red-900/20"
                                                    title="Xóa liên hệ">
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-5 py-3 dark:border-gray-800 dark:bg-white/[0.02]">
                <div class="text-xs text-gray-500">
                    Trang <?= (int)$page ?> trên tổng số <?= (int)$totalPages ?> trang
                </div>
                <div class="flex items-center gap-1">
                    <?php if ($page > 1): ?>
                        <a href="index.php?controller=admin&amp;action=contacts&amp;page=<?= $page - 1 ?><?= $statusFilter !== null ? '&amp;status=' . $statusFilter : '' ?>"
                           class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            &laquo; Trước
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="index.php?controller=admin&amp;action=contacts&amp;page=<?= $i ?><?= $statusFilter !== null ? '&amp;status=' . $statusFilter : '' ?>"
                           class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $i === $page ? 'bg-brand-500 text-white' : 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="index.php?controller=admin&amp;action=contacts&amp;page=<?= $page + 1 ?><?= $statusFilter !== null ? '&amp;status=' . $statusFilter : '' ?>"
                           class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            Sau &raquo;
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../../Layouts/Backend/footer.php'; ?>
