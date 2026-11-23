<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$pageTitle = isset($pageTitle) ? $pageTitle : 'LTR Company';
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $escape($pageTitle) ?> | LTR Company</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-yEPZ7jP9S0QKiUQ0m3jjsO4ProIW7wvR9gXkI7JOGa2XVsw53Yke2bsdkBbjmmiX"
        crossorigin="anonymous">
    <link href="Public/css/frontend.css" rel="stylesheet">
    <link href="Public/css/content.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="index.php">LTR Company</a>
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
                <div class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <a class="nav-link" href="index.php?controller=about&amp;action=index">Giới thiệu</a>
                    <a class="nav-link" href="index.php?controller=faq&amp;action=index">Hỏi/Đáp</a>
                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <a class="nav-link" href="index.php?controller=auth&amp;action=profile">Hồ sơ cá nhân</a>
                    <?php else: ?>
                        <a class="nav-link" href="index.php?controller=auth&amp;action=login">Đăng nhập</a>
                        <a class="btn btn-primary btn-sm" href="index.php?controller=auth&amp;action=register">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <main class="container py-4 py-lg-5">
