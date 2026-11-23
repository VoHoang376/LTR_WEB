<?php
$pageTitle = 'Bảng điều khiển';
$activeAdminPage = 'dashboard';
require __DIR__ . '/../Layouts/Backend/header.php';
?>

<div class="space-y-6">
    <!-- Stat Cards -->
    <section class="admin-dashboard-grid">
        <article class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Liên hệ chưa xử lý</h2>
                <?php if (!empty($unprocessedContacts)): ?>
                    <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                        Cần xem
                    </span>
                <?php endif; ?>
            </div>
            <p class="mt-3 text-3xl font-bold <?= !empty($unprocessedContacts) ? 'text-amber-600 dark:text-amber-400' : 'text-gray-800 dark:text-white' ?>">
                <?= (int) ($unprocessedContacts ?? 0) ?>
            </p>
            <p class="mt-2 text-xs text-gray-500">
                <a href="index.php?controller=admin&amp;action=contacts&amp;status=0" class="text-brand-500 hover:underline">
                    Xem danh sách &rarr;
                </a>
            </p>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <h2 class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Tổng số liên hệ</h2>
            <p class="mt-3 text-3xl font-bold text-gray-800 dark:text-white">
                <?= (int) ($totalContacts ?? 0) ?>
            </p>
            <p class="mt-2 text-xs text-gray-500">
                <a href="index.php?controller=admin&amp;action=contacts" class="text-brand-500 hover:underline">
                    Quản lý toàn bộ &rarr;
                </a>
            </p>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <h2 class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Cấu hình website</h2>
            <p class="mt-2 text-base font-semibold text-gray-800 dark:text-white">Thông tin công ty</p>
            <p class="mt-1 text-xs text-gray-500">Hotline, Email, Địa chỉ, Slogan.</p>
            <p class="mt-3 text-xs">
                <a href="index.php?controller=admin&amp;action=settings" class="text-brand-500 hover:underline">
                    Chỉnh sửa cấu hình &rarr;
                </a>
            </p>
        </article>
    </section>

    <!-- Recent Contacts Table -->
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-800">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Liên hệ mới nhất</h2>
            <a href="index.php?controller=admin&amp;action=contacts" class="text-xs font-medium text-brand-500 hover:underline">
                Xem tất cả &rarr;
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-white/[0.02] dark:text-gray-400">
                    <tr>
                        <th class="px-5 py-3">Khách hàng</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Lời nhắn</th>
                        <th class="px-5 py-3">Thời gian</th>
                        <th class="px-5 py-3">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <?php if (empty($recentContacts)): ?>
                        <tr>
                            <td colspan="5" class="px-5 py-6 text-center text-gray-400">Chưa có liên hệ nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentContacts as $c): ?>
                            <tr>
                                <td class="px-5 py-3.5 font-medium text-gray-800 dark:text-white"><?= $escape($c['fullname']) ?></td>
                                <td class="px-5 py-3.5 text-xs text-gray-500"><?= $escape($c['email']) ?></td>
                                <td class="px-5 py-3.5 text-xs max-w-xs truncate"><?= $escape($c['message']) ?></td>
                                <td class="px-5 py-3.5 text-xs text-gray-400"><?= date('d/m H:i', strtotime($c['created_at'])) ?></td>
                                <td class="px-5 py-3.5 text-xs">
                                    <?php if ((int)$c['status'] === 1): ?>
                                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-medium text-emerald-700">Đã phản hồi</span>
                                    <?php else: ?>
                                        <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-medium text-amber-700">Chưa xử lý</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../Layouts/Backend/footer.php'; ?>
