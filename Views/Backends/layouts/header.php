<?php
/**
 * Khung trên khu quản trị (sidebar + topbar).
 *
 * Biến nhận từ controller: $pageTitle, $activeMenu, $breadcrumb
 * ($breadcrumb là mảng ['label' => ..., 'url' => ... | null]).
 */
$pageTitle = $pageTitle ?? 'Quản trị';
$activeMenu = $activeMenu ?? '';
$breadcrumb = $breadcrumb ?? [];
$adminUser = Permission::user();
$staffName = $adminUser['fullname'] ?? 'Quản trị viên';
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
    <link rel="stylesheet" href="Public/css/admin.css">
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

        <div class="ms-auto d-flex align-items-center gap-2">
            <a class="btn btn-sm btn-outline-secondary d-none d-md-inline-flex"
               href="index.php?controller=product&amp;action=index" target="_blank" rel="noopener">
                <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>Xem website
            </a>
            <div class="dropdown">
                <button class="btn btn-sm btn-light border dropdown-toggle d-flex align-items-center gap-2"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle" aria-hidden="true"></i>
                    <span class="d-none d-sm-inline"><?= e($staffName) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><span class="dropdown-item-text small text-muted"><?= e($adminUser['email'] ?? '') ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="index.php?controller=auth&amp;action=profile">
                        <i class="bi bi-person-gear me-2" aria-hidden="true"></i>Hồ sơ cá nhân</a></li>
                    <li>
                        <form method="post" action="index.php?controller=auth&amp;action=logout" class="px-3 py-1">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                <i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i>Đăng xuất
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
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
