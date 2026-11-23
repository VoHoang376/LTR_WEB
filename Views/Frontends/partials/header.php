<?php
/**
 * Khung trên của giao diện người dùng (header + navbar).
 *
 * Biến nhận từ controller: $pageTitle, $metaDescription, $activeNav (tuỳ chọn),
 * $ogImage (tuỳ chọn – dùng cho thẻ Open Graph của trang chi tiết).
 */
$pageTitle = $pageTitle ?? 'Trang chủ';
$metaDescription = $metaDescription ?? 'LTR Technology – giải pháp thiết bị văn phòng, máy tính và camera an ninh cho doanh nghiệp.';
$activeNav = $activeNav ?? '';
$ogImage = $ogImage ?? null;

$cartCount = cart_item_count();
$authUser = Permission::user();
$isAdmin = $authUser !== null && (int) $authUser['role'] === 0;

$navClass = static function ($key) use ($activeNav) {
    return 'nav-link' . ($activeNav === $key ? ' active' : '');
};
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e(site_name()) ?></title>
    <meta name="description" content="<?= e($metaDescription) ?>">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <meta property="og:site_name" content="<?= e(site_name()) ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($metaDescription) ?>">
    <?php if ($ogImage !== null): ?>
        <meta property="og:image" content="<?= e($ogImage) ?>">
    <?php endif; ?>
    <link rel="icon" type="image/svg+xml" href="Public/img/favicon.svg">
    <link rel="stylesheet" href="Public/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="Public/vendor/bootstrap-icons/bootstrap-icons.min.css">
    <link rel="stylesheet" href="Public/css/frontend.css">
</head>
<body>
<a class="skip-link" href="#main-content">Bỏ qua điều hướng</a>

<header class="site-header">
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top" aria-label="Điều hướng chính">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php?controller=product&amp;action=index">
                <span class="brand-mark" aria-hidden="true"><i class="bi bi-box-seam-fill"></i></span>
                <span class="brand-text"><?= e(site_name()) ?><span>Thiết bị &amp; giải pháp</span></span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Mở menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 mt-2 mt-lg-0">
                    <li class="nav-item">
                        <a class="<?= e($navClass('products')) ?>"
                           href="index.php?controller=product&amp;action=index">Sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="<?= e($navClass('orders')) ?>"
                           href="index.php?controller=order&amp;action=index">Đơn hàng của tôi</a>
                    </li>
                    <li class="nav-item">
                        <a class="<?= e($navClass('cart')) ?>"
                           href="index.php?controller=cart&amp;action=index">Giỏ hàng</a>
                    </li>
                </ul>

                <div class="d-flex flex-wrap align-items-center gap-2 pb-3 pb-lg-0">
                    <a class="cart-link" href="index.php?controller=cart&amp;action=index"
                       aria-label="Giỏ hàng, số lượng sản phẩm">
                        <i class="bi bi-cart3" aria-hidden="true"></i>
                        <span class="d-none d-sm-inline">Giỏ hàng</span>
                        <span class="cart-count<?= $cartCount === 0 ? ' is-empty' : '' ?>"
                              data-cart-count><?= (int) $cartCount ?></span>
                    </a>

                    <?php if ($authUser !== null): ?>
                        <div class="dropdown">
                            <button class="btn btn-soft dropdown-toggle d-flex align-items-center gap-2"
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle" aria-hidden="true"></i>
                                <span class="d-none d-md-inline"><?= e($authUser['fullname']) ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><span class="dropdown-item-text small text-muted"><?= e($authUser['email']) ?></span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="index.php?controller=auth&amp;action=profile">
                                    <i class="bi bi-person-gear me-2" aria-hidden="true"></i>Hồ sơ cá nhân</a></li>
                                <li><a class="dropdown-item" href="index.php?controller=order&amp;action=index">
                                    <i class="bi bi-receipt me-2" aria-hidden="true"></i>Đơn hàng của tôi</a></li>
                                <?php if ($isAdmin): ?>
                                    <li><a class="dropdown-item" href="index.php?controller=admin&amp;action=dashboard">
                                        <i class="bi bi-speedometer2 me-2" aria-hidden="true"></i>Trang quản trị</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
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
                    <?php else: ?>
                        <a class="btn btn-soft" href="index.php?controller=auth&amp;action=login">Đăng nhập</a>
                        <a class="btn btn-brand" href="index.php?controller=auth&amp;action=register">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>

<main id="main-content" class="pb-4">
    <div class="container mt-3">
        <?php foreach (['global', 'cart', 'orders'] as $flashKey): ?>
            <?php $flash = flash_get($flashKey); ?>
            <?php if ($flash !== null): ?>
                <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                    <?= e($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
