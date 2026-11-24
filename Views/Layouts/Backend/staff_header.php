<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$pageTitle = isset($pageTitle) ? $pageTitle : 'Chăm sóc khách hàng';
$activeStaffPage = isset($activeStaffPage) ? $activeStaffPage : 'dashboard';
$settings = site_settings();
$companyName = !empty($settings['company_name']) ? $settings['company_name'] : site_name();
$staffCsrfToken = csrf_token();
$staffUser = Permission::user();
$staffName = $staffUser['fullname'] ?? 'Nhân viên CSKH';
$adminCssVer = file_exists(__DIR__ . '/../../../Public/css/admin.css')
    ? filemtime(__DIR__ . '/../../../Public/css/admin.css')
    : 1;

$staffMenuGroups = [
    [
        'title' => 'Chăm sóc khách hàng',
        'items' => [
            ['key' => 'dashboard', 'label' => 'Tổng quan CSKH', 'icon' => 'bi-headset', 'url' => 'index.php?controller=staff&amp;action=dashboard'],
            ['key' => 'contacts', 'label' => 'Giải quyết liên hệ', 'icon' => 'bi-envelope-check', 'url' => 'index.php?controller=staff&amp;action=contacts'],
            ['key' => 'faqs', 'label' => 'Trả lời Hỏi / Đáp (FAQ)', 'icon' => 'bi-question-circle', 'url' => 'index.php?controller=staff&amp;action=faqs'],
            ['key' => 'reviews', 'label' => 'Phản hồi sản phẩm', 'icon' => 'bi-star-half', 'url' => 'index.php?controller=staff&amp;action=reviews'],
            ['key' => 'comments', 'label' => 'Quản lý bình luận', 'icon' => 'bi-chat-dots', 'url' => 'index.php?controller=staff&amp;action=comments'],
        ],
    ],
    [
        'title' => 'Tài khoản',
        'items' => [
            ['key' => 'profile', 'label' => 'Hồ sơ cá nhân', 'icon' => 'bi-person-gear', 'url' => 'index.php?controller=auth&amp;action=profile'],
            ['key' => 'site', 'label' => 'Xem website', 'icon' => 'bi-globe2', 'url' => 'index.php'],
        ],
    ],
];
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $escape($pageTitle) ?> | CSKH <?= $escape($companyName) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="<?= $escape($staffCsrfToken) ?>">
    <?php if (!empty($settings['company_favicon'])): ?>
        <link rel="icon" href="Public/uploads/company/<?= $escape($settings['company_favicon']) ?>">
    <?php else: ?>
        <link rel="icon" type="image/svg+xml" href="Public/img/favicon.svg">
    <?php endif; ?>
    <link href="Public/vendor/tailadmin/style.css" rel="stylesheet">
    <link href="Public/vendor/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
    <link href="Public/css/admin.css?v=<?= (int) $adminCssVer ?>" rel="stylesheet">
</head>
<body class="tailadmin-shell staff-shell">

<aside class="admin-sidebar" id="adminSidebar" aria-label="Menu chăm sóc khách hàng">
    <div class="sidebar-brand">
        <span class="brand-mark" aria-hidden="true"><i class="bi bi-headset"></i></span>
        <div>
            <strong><?= $escape($companyName) ?></strong>
            <small>Kênh nhân viên CSKH</small>
        </div>
    </div>

    <?php foreach ($staffMenuGroups as $group): ?>
        <p class="sidebar-group-title"><?= $escape($group['title']) ?></p>
        <ul class="sidebar-nav">
            <?php foreach ($group['items'] as $item): ?>
                <li>
                    <a class="<?= $activeStaffPage === $item['key'] ? 'active' : '' ?>" href="<?= $item['url'] ?>">
                        <i class="bi <?= $escape($item['icon']) ?>" aria-hidden="true"></i>
                        <span><?= $escape($item['label']) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>

    <div class="sidebar-footer">
        <i class="bi bi-shield-check me-1" aria-hidden="true"></i>
        Khu vực riêng cho Nhân viên CSKH.
    </div>
</aside>
<script>(function(){try{var s=document.getElementById('adminSidebar');var y=sessionStorage.getItem('ltr_staff_sidebar_scroll');if(s&&y!==null){s.scrollTop=parseInt(y,10)||0;}}catch(e){}})();</script>

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
            <span class="topbar-role-badge is-staff">CSKH</span>
            <span class="text-sm font-medium text-gray-700"><?= $escape($staffName) ?></span>
            <form method="post" action="index.php?controller=auth&amp;action=logout" style="margin:0;">
                <input type="hidden" name="csrf_token" value="<?= $escape($staffCsrfToken) ?>">
                <button class="topbar-btn topbar-btn-danger" type="submit">
                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                    <span>Đăng xuất</span>
                </button>
            </form>
        </div>
    </header>

    <main class="admin-content" id="admin-content">
