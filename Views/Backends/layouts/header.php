<?php
/**
 * Khung trên khu quản trị cho các trang Sản phẩm / Danh mục / Đơn hàng (Bootstrap 5),
 * đồng bộ 100% khung sidebar xanh đậm & topbar với Views/Layouts/Backend/header.php.
 */
$pageTitle = $pageTitle ?? 'Quản trị';
$activeMenu = $activeMenu ?? '';
$breadcrumb = $breadcrumb ?? [];
$adminUser = Permission::user();
$staffName = $adminUser['fullname'] ?? 'Quản trị viên';
$adminCssVer = file_exists(__DIR__ . '/../../../Public/css/admin.css')
    ? filemtime(__DIR__ . '/../../../Public/css/admin.css')
    : 1;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | Quản trị <?= e(site_name()) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <link rel="icon" type="image/svg+xml" href="Public/img/favicon.svg">
    <link rel="stylesheet" href="Public/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="Public/vendor/bootstrap-icons/bootstrap-icons.min.css">
    <link rel="stylesheet" href="Public/css/admin.css?v=<?= (int) $adminCssVer ?>">
</head>
<body class="admin-body">
<a class="visually-hidden-focusable bg-white p-2 position-absolute" href="#admin-content">Bỏ qua điều hướng</a>

<aside class="admin-sidebar" id="adminSidebar" aria-label="Menu quản trị">
    <div class="sidebar-brand">
        <span class="brand-mark" aria-hidden="true"><i class="bi bi-box-seam-fill"></i></span>
        <div>
            <strong><?= e(site_name()) ?></strong>
            <small>Trang quản trị</small>
        </div>
    </div>

    <?php require __DIR__ . '/../_menu.php'; ?>

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
            <h1 class="page-heading"><?= e($pageTitle) ?></h1>
            <?php if ($breadcrumb !== []): ?>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="index.php?controller=admin&amp;action=dashboard">Quản trị</a>
                        </li>
                        <?php foreach ($breadcrumb as $crumb): ?>
                            <li class="breadcrumb-item<?= empty($crumb['url']) ? ' active' : '' ?>"
                                <?= empty($crumb['url']) ? 'aria-current="page"' : '' ?>>
                                <?php if (!empty($crumb['url'])): ?>
                                    <a href="<?= e($crumb['url']) ?>"><?= e($crumb['label']) ?></a>
                                <?php else: ?>
                                    <?= e($crumb['label']) ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </nav>
            <?php endif; ?>
        </div>

        <div class="admin-topbar-actions">
            <a class="topbar-btn d-none d-md-inline-flex" href="index.php" target="_blank" rel="noopener">
                <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                <span>Xem website</span>
            </a>
            <span class="topbar-role-badge is-admin">Admin</span>
            <span class="small fw-medium text-secondary d-none d-sm-inline"><?= e($staffName) ?></span>
            <form method="post" action="index.php?controller=auth&amp;action=logout" class="m-0">
                <?= csrf_field() ?>
                <button type="submit" class="topbar-btn topbar-btn-danger">
                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                    <span>Đăng xuất</span>
                </button>
            </form>
        </div>
    </header>

    <main class="admin-content" id="admin-content">
        <?php foreach (['admin', 'admin_gallery', 'admin_warning', 'global'] as $flashKey): ?>
            <?php $flash = flash_get($flashKey); ?>
            <?php if ($flash !== null): ?>
                <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                    <?= e($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
