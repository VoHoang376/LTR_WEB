<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$pageTitle = isset($pageTitle) ? $pageTitle : 'Quản trị';
$activeAdminPage = isset($activeAdminPage) ? $activeAdminPage : 'dashboard';
$activeMenu = $activeAdminPage;
$settings = isset($settings) && is_array($settings) && $settings !== [] ? $settings : site_settings();
$companyName = !empty($settings['company_name']) ? $settings['company_name'] : site_name();
$adminCsrfToken = csrf_token();
$adminUser = Permission::user();
$adminName = $adminUser['fullname'] ?? ($_SESSION['fullname'] ?? 'Quản trị viên');
$adminCssVer = file_exists(__DIR__ . '/../../../Public/css/admin.css')
    ? filemtime(__DIR__ . '/../../../Public/css/admin.css')
    : 1;
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $escape($pageTitle) ?> | Quản trị <?= $escape($companyName) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="<?= $escape($adminCsrfToken) ?>">
    <?php if (!empty($settings['company_favicon'])): ?>
        <link rel="icon" href="Public/uploads/company/<?= $escape($settings['company_favicon']) ?>">
    <?php else: ?>
        <link rel="icon" type="image/svg+xml" href="Public/img/favicon.svg">
    <?php endif; ?>
    <link href="Public/vendor/tailadmin/style.css" rel="stylesheet">
    <link href="Public/vendor/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
    <?php if (!empty($includeQuill)): ?>
        <link
            href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css"
            rel="stylesheet"
            integrity="sha384-ecIckRi4QlKYya/FQUbBUjS4qp65jF/J87Guw5uzTbO1C1Jfa/6kYmd6dXUF6D7i"
            crossorigin="anonymous">
    <?php endif; ?>
    <link href="Public/css/admin.css?v=<?= (int) $adminCssVer ?>" rel="stylesheet">
</head>
<body class="tailadmin-shell">

<aside class="admin-sidebar" id="adminSidebar" aria-label="Menu quản trị">
    <div class="sidebar-brand">
        <span class="brand-mark" aria-hidden="true"><i class="bi bi-box-seam-fill"></i></span>
        <div>
            <strong><?= $escape($companyName) ?></strong>
            <small>Trang quản trị</small>
        </div>
    </div>

    <?php require __DIR__ . '/../../Backends/_menu.php'; ?>

    <div class="sidebar-footer">
        <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
        Bài tập lớn Lập trình Web – HK1 2026-2027.
    </div>
</aside>
<script>(function(){try{var s=document.getElementById('adminSidebar');var y=sessionStorage.getItem('ltr_admin_sidebar_scroll');if(s&&y!==null){s.scrollTop=parseInt(y,10)||0;}}catch(e){}})();</script>

<div class="admin-backdrop" id="adminBackdrop"></div>

<div class="admin-main">
    <header class="admin-topbar">
        <button type="button" class="sidebar-toggle" id="sidebarToggle"
                aria-controls="adminSidebar" aria-expanded="false" aria-label="Mở/đóng menu">
            <i class="bi bi-list" aria-hidden="true"></i>
        </button>

        <div>
            <h1 class="page-heading"><?= $escape($pageTitle) ?></h1>
        </div>

        <div class="admin-topbar-actions">
            <a class="topbar-btn" href="index.php" target="_blank" rel="noopener">
                <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                <span>Xem website</span>
            </a>
            <span class="topbar-role-badge is-admin">Admin</span>
            <span class="text-sm font-medium text-gray-700"><?= $escape($adminName) ?></span>
            <form method="post" action="index.php?controller=auth&amp;action=logout" style="margin:0;">
                <input type="hidden" name="csrf_token" value="<?= $escape($adminCsrfToken) ?>">
                <button class="topbar-btn topbar-btn-danger" type="submit">
                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                    <span>Đăng xuất</span>
                </button>
            </form>
        </div>
    </header>

    <main class="admin-content" id="admin-content">
