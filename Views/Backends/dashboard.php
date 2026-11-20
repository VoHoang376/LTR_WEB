<?php
$pageTitle = 'Bảng điều khiển';
$activeAdminPage = 'dashboard';
require __DIR__ . '/../Layouts/Backend/header.php';
?>
<section class="admin-dashboard-grid">
    <article class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Tài khoản</h2>
        <p class="mt-2 text-lg font-semibold text-gray-800 dark:text-white">Quản trị viên</p>
        <p class="mt-1 text-sm text-gray-500">Xin chào, <?= $escape($_SESSION['fullname'] ?? '') ?>.</p>
    </article>
    <article class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Giao diện</h2>
        <p class="mt-2 text-lg font-semibold text-gray-800 dark:text-white">TailAdmin</p>
        <p class="mt-1 text-sm text-gray-500">Template quản trị responsive.</p>
    </article>
    <article class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Website</h2>
        <p class="mt-2 text-lg font-semibold text-gray-800 dark:text-white">LTR Company</p>
        <p class="mt-1 text-sm text-gray-500">Khu vực quản trị nội dung website.</p>
    </article>
</section>
<?php require __DIR__ . '/../Layouts/Backend/footer.php'; ?>
