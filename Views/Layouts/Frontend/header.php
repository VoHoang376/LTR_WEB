<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$pageTitle = isset($pageTitle) ? $pageTitle : 'LTR Company';
$currentPage = isset($currentPage) ? $currentPage : '';
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="LTR Company – Giải pháp công nghệ toàn diện cho doanh nghiệp. Sản phẩm chất lượng cao, dịch vụ chuyên nghiệp.">
    <title><?= $escape($pageTitle) ?> | LTR Company</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-yEPZ7jP9S0QKiUQ0m3jjsO4ProIW7wvR9gXkI7JOGa2XVsw53Yke2bsdkBbjmmiX"
        crossorigin="anonymous">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="Public/css/frontend.css?v=<?= file_exists(__DIR__ . '/../../../Public/css/frontend.css') ? filemtime(__DIR__ . '/../../../Public/css/frontend.css') : time() ?>" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-building me-1 text-primary"></i>LTR Company
            </a>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavigation"
                aria-controls="mainNavigation"
                aria-expanded="false"
                aria-label="Mở menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavigation">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'home' ? ' active fw-semibold' : '' ?>" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'about' ? ' active fw-semibold' : '' ?>" href="index.php?controller=about">Giới thiệu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'product' ? ' active fw-semibold' : '' ?>" href="index.php?controller=product">Sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'post' ? ' active fw-semibold' : '' ?>" href="index.php?controller=post">Tin tức</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'contact' ? ' active fw-semibold' : '' ?>" href="index.php?controller=contact">Liên hệ</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <?php if (isset($_SESSION['role']) && (int)$_SESSION['role'] === 0): ?>
                            <a class="btn btn-outline-secondary btn-sm" href="index.php?controller=admin&amp;action=dashboard">
                                <i class="bi bi-speedometer2 me-1"></i>Quản trị
                            </a>
                        <?php endif; ?>
                        <a class="nav-link" href="index.php?controller=auth&amp;action=profile">
                            <i class="bi bi-person-circle me-1"></i><?= $escape($_SESSION['fullname'] ?? 'Tài khoản') ?>
                        </a>
                    <?php else: ?>
                        <a class="nav-link" href="index.php?controller=auth&amp;action=login">Đăng nhập</a>
                        <a class="btn btn-primary btn-sm" href="index.php?controller=auth&amp;action=register">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <main<?= !empty($fullWidth) ? '' : ' class="container py-4 py-lg-5"' ?>>
