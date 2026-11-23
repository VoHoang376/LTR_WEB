<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$pageTitle = isset($pageTitle) ? $pageTitle : 'Quản trị';
$activeAdminPage = isset($activeAdminPage) ? $activeAdminPage : 'dashboard';
$settings = isset($settings) ? $settings : [];
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$adminCsrfToken = $_SESSION['csrf_token'];
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $escape($pageTitle) ?> | Quản trị LTR</title>
    <?php if (!empty($settings['company_favicon'])): ?>
        <link rel="icon" href="Public/uploads/company/<?= $escape($settings['company_favicon']) ?>">
    <?php endif; ?>
    <link href="Public/vendor/tailadmin/style.css" rel="stylesheet">
    <link href="Public/css/admin.css" rel="stylesheet">
    <?php if (!empty($includeQuill)): ?>
        <link
            href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css"
            rel="stylesheet"
            integrity="sha384-ecIckRi4QlKYya/FQUbBUjS4qp65jF/J87Guw5uzTbO1C1Jfa/6kYmd6dXUF6D7i"
            crossorigin="anonymous">
    <?php endif; ?>
    <link href="Public/css/admin.css?v=<?= file_exists(__DIR__ . '/../../../Public/css/admin.css') ? filemtime(__DIR__ . '/../../../Public/css/admin.css') : time() ?>" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-black">
    <div class="min-h-screen xl:flex">
        <aside
            id="admin-sidebar"
            class="sidebar admin-sidebar fixed top-0 left-0 z-50 flex h-screen w-72.5 flex-col overflow-y-auto border-r border-gray-200 bg-white px-5 transition-transform duration-300 xl:static xl:translate-x-0 dark:border-gray-800 dark:bg-black">
            <div class="sidebar-header flex items-center justify-between gap-2 pt-8 pb-7">
                <a class="text-xl font-semibold text-gray-800 dark:text-white" href="index.php?controller=admin&amp;action=dashboard">
                    LTR Company
                    <span class="mt-1 block text-xs font-normal text-gray-500">Khu vực quản trị</span>
                </a>
            </div>
            <nav aria-label="Menu quản trị">
                <h2 class="mb-4 text-xs leading-5 text-gray-400 uppercase">Menu Quản trị</h2>
                <ul class="mb-6 flex flex-col gap-1">
                    <li>
                        <a
                            class="menu-item group <?= $activeAdminPage === 'dashboard' ? 'menu-item-active' : 'menu-item-inactive' ?>"
                            href="index.php?controller=admin&amp;action=dashboard"
                            <?= $activeAdminPage === 'dashboard' ? 'aria-current="page"' : '' ?>>
                            <span class="menu-item-text">Bảng điều khiển</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="menu-item group <?= $activeAdminPage === 'about' ? 'menu-item-active' : 'menu-item-inactive' ?>"
                            href="index.php?controller=adminAbout&amp;action=index">
                            <span class="menu-item-text">Giới thiệu</span>
                            class="menu-item group <?= $activeAdminPage === 'contacts' ? 'menu-item-active' : 'menu-item-inactive' ?>"
                            href="index.php?controller=admin&amp;action=contacts"
                            <?= $activeAdminPage === 'contacts' ? 'aria-current="page"' : '' ?>>
                            <span class="menu-item-text">Quản lý liên hệ</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="menu-item group <?= $activeAdminPage === 'faq' ? 'menu-item-active' : 'menu-item-inactive' ?>"
                            href="index.php?controller=adminFaq&amp;action=index">
                            <span class="menu-item-text">Câu hỏi thường gặp</span>
                            class="menu-item group <?= $activeAdminPage === 'settings' ? 'menu-item-active' : 'menu-item-inactive' ?>"
                            href="index.php?controller=admin&amp;action=settings"
                            <?= $activeAdminPage === 'settings' ? 'aria-current="page"' : '' ?>>
                            <span class="menu-item-text">Cấu hình website</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="menu-item group <?= $activeAdminPage === 'pricing' ? 'menu-item-active' : 'menu-item-inactive' ?>"
                            href="index.php?controller=admin&amp;action=pricing"
                            <?= $activeAdminPage === 'pricing' ? 'aria-current="page"' : '' ?>>
                            <span class="menu-item-text">Quản lý bảng giá</span>
                        </a>
                    </li>
                </ul>

                <h2 class="mb-4 text-xs leading-5 text-gray-400 uppercase">Cá nhân &amp; Hệ thống</h2>
                <ul class="mb-6 flex flex-col gap-1">
                    <li>
                        <a
                            class="menu-item group menu-item-inactive"
                            href="index.php?controller=auth&amp;action=profile">
                            <span class="menu-item-text">Hồ sơ cá nhân</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="menu-item group menu-item-inactive"
                            href="index.php"
                            target="_blank">
                            <span class="menu-item-text">Xem trang ngoài &nearr;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <button
            id="admin-sidebar-backdrop"
            class="admin-sidebar-backdrop"
            type="button"
            aria-label="Đóng menu"
            hidden></button>

        <div class="admin-header-content relative flex flex-1 flex-col">
            <header class="sticky top-0 z-40 flex w-full border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-black">
                <div class="admin-header-inner flex grow flex-col items-center justify-between">
                    <div class="admin-header-main flex w-full items-center justify-between gap-2 border-b border-gray-200 px-3 py-3 dark:border-gray-800 sm:gap-4 lg:py-4">
                        <button
                            class="admin-sidebar-toggle z-50 flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 text-gray-500"
                            type="button"
                            data-sidebar-toggle
                            aria-controls="admin-sidebar"
                            aria-expanded="false"
                            aria-label="Mở menu quản trị">
                            <span aria-hidden="true">&#9776;</span>
                        </button>
                        <span class="text-sm font-medium text-gray-800 dark:text-white"><?= $escape($pageTitle) ?></span>
                    </div>
                    <div class="flex w-full items-center justify-end gap-3 px-5 py-3">
                        <span class="text-sm text-gray-500 dark:text-gray-300">
                            <?= $escape($_SESSION['fullname'] ?? 'Quản trị viên') ?>
                        </span>
                        <form method="post" action="index.php?controller=auth&amp;action=logout">
                            <input type="hidden" name="csrf_token" value="<?= $escape($adminCsrfToken) ?>">
                            <button class="text-sm font-medium text-brand-500 hover:underline" type="submit">Đăng xuất</button>
                        </form>
                    </div>
                </div>
            </header>
            <main class="admin-content p-4 md:p-6">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white"><?= $escape($pageTitle) ?></h1>
                </div>
