<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$settings = isset($settings) && is_array($settings) && $settings !== [] ? $settings : site_settings();
$companyName = !empty($settings['company_name']) ? $settings['company_name'] : site_name();
$pageTitle = isset($pageTitle) ? $pageTitle : 'Trang chủ';
$metaDescription = isset($metaDescription) && $metaDescription !== ''
    ? $metaDescription
    : ($settings['company_slogan'] ?? 'LTR Technology – Giải pháp công nghệ và thiết bị văn phòng toàn diện cho doanh nghiệp.');
$metaKeywords = isset($metaKeywords) ? (string) $metaKeywords : '';
$ogImage = isset($ogImage) ? $ogImage : null;

$currentPage = isset($currentPage) && $currentPage !== ''
    ? $currentPage
    : (isset($activeNav) ? $activeNav : '');
// Chuẩn hoá tên tab active giữa các thành viên (product/products, post/news)
if ($currentPage === 'products') {
    $currentPage = 'product';
} elseif ($currentPage === 'news') {
    $currentPage = 'post';
}

$isAdmin = Permission::isAdmin();
$isStaff = Permission::isStaff();
$isMember = Permission::isMember();
$canShop = Permission::canShop();
$cartCount = $isMember ? cart_item_count() : 0;
$authUser = Permission::user();
$frontendCssVer = file_exists(__DIR__ . '/../../../Public/css/frontend.css')
    ? filemtime(__DIR__ . '/../../../Public/css/frontend.css')
    : 1;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $escape($pageTitle) ?> | <?= $escape($companyName) ?></title>
    <meta name="description" content="<?= $escape($metaDescription) ?>">
    <?php if ($metaKeywords !== ''): ?>
        <meta name="keywords" content="<?= $escape($metaKeywords) ?>">
    <?php endif; ?>
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="<?= $escape(csrf_token()) ?>">
    <meta property="og:site_name" content="<?= $escape($companyName) ?>">
    <meta property="og:title" content="<?= $escape($pageTitle) ?> | <?= $escape($companyName) ?>">
    <meta property="og:description" content="<?= $escape($metaDescription) ?>">
    <meta property="og:type" content="website">
    <?php if ($ogImage !== null): ?>
        <meta property="og:image" content="<?= $escape($ogImage) ?>">
    <?php endif; ?>
    <?php if (!empty($settings['company_favicon'])): ?>
        <link rel="icon" href="Public/uploads/company/<?= $escape($settings['company_favicon']) ?>">
    <?php else: ?>
        <link rel="icon" type="image/svg+xml" href="Public/img/favicon.svg">
    <?php endif; ?>
    <link rel="stylesheet" href="Public/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="Public/vendor/bootstrap-icons/bootstrap-icons.min.css">
    <link rel="stylesheet" href="Public/css/frontend.css?v=<?= (int) $frontendCssVer ?>">
    <link rel="stylesheet" href="Public/css/content.css">
</head>
<body>
<a class="skip-link" href="#main-content">Bỏ qua điều hướng</a>

<header class="site-header">
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top shadow-sm" aria-label="Điều hướng chính">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
                <?php if (!empty($settings['company_logo'])): ?>
                    <img src="Public/uploads/company/<?= $escape($settings['company_logo']) ?>"
                         alt="Logo <?= $escape($companyName) ?>"
                         style="height: 36px; width: auto; object-fit: contain;">
                <?php else: ?>
                    <span class="brand-mark" aria-hidden="true"><i class="bi bi-box-seam-fill"></i></span>
                <?php endif; ?>
                <span class="brand-text"><?= $escape($companyName) ?><span>Thiết bị &amp; giải pháp</span></span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#mainNavigation" aria-controls="mainNavigation"
                    aria-expanded="false" aria-label="Mở menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavigation">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 mt-2 mt-lg-0 gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'home' ? ' active' : '' ?>" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'about' ? ' active' : '' ?>" href="index.php?controller=about">Giới thiệu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'product' ? ' active' : '' ?>" href="index.php?controller=product&amp;action=index">Sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'pricing' ? ' active' : '' ?>" href="index.php?controller=pricing">Bảng giá</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'post' ? ' active' : '' ?>" href="index.php?controller=post&amp;action=index">Tin tức</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'faq' ? ' active' : '' ?>" href="index.php?controller=faq">Hỏi/Đáp</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'contact' ? ' active' : '' ?>" href="index.php?controller=contact">Liên hệ</a>
                    </li>
                </ul>

                <div class="d-flex flex-wrap align-items-center gap-2 pb-3 pb-lg-0">
                    <?php if ($isMember): ?>
                        <a class="cart-link" href="index.php?controller=cart&amp;action=index"
                           aria-label="Giỏ hàng, số lượng sản phẩm">
                            <i class="bi bi-cart3" aria-hidden="true"></i>
                            <span class="d-none d-xl-inline">Giỏ hàng</span>
                            <span class="cart-count<?= $cartCount === 0 ? ' is-empty' : '' ?>"
                                  data-cart-count><?= (int) $cartCount ?></span>
                        </a>
                    <?php elseif ($authUser === null): ?>
                        <a class="cart-link" href="index.php?controller=cart&amp;action=index"
                           data-require-login="Bạn chưa đăng nhập nên không thể sử dụng giỏ hàng hay đặt hàng. Vui lòng đăng nhập bằng tài khoản Thành viên (Khách hàng)."
                           data-login-redirect="index.php?controller=cart&amp;action=index"
                           aria-label="Giỏ hàng (yêu cầu đăng nhập)">
                            <i class="bi bi-cart3" aria-hidden="true"></i>
                            <span class="d-none d-xl-inline">Giỏ hàng</span>
                        </a>
                    <?php endif; ?>

                    <?php if ($authUser !== null): ?>
                        <div class="dropdown">
                            <button class="btn btn-soft btn-sm dropdown-toggle d-flex align-items-center gap-2"
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle" aria-hidden="true"></i>
                                <span class="d-none d-md-inline"><?= $escape($authUser['fullname']) ?></span>
                                <?php if ($isAdmin): ?>
                                    <span class="badge text-bg-primary">Admin</span>
                                <?php elseif ($isStaff): ?>
                                    <span class="badge text-bg-success">CSKH</span>
                                <?php endif; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <span class="dropdown-item-text small text-muted">
                                        <?= $escape($authUser['email']) ?><br>
                                        <strong class="text-body"><?= $escape(Permission::roleLabel($authUser['role'])) ?></strong>
                                    </span>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <?php if ($isAdmin): ?>
                                    <li>
                                        <a class="dropdown-item fw-semibold text-primary" href="index.php?controller=admin&amp;action=dashboard">
                                            <i class="bi bi-speedometer2 me-2" aria-hidden="true"></i>Trang quản trị
                                        </a>
                                    </li>
                                <?php elseif ($isStaff): ?>
                                    <li>
                                        <a class="dropdown-item fw-semibold text-success" href="index.php?controller=staff&amp;action=dashboard">
                                            <i class="bi bi-headset me-2" aria-hidden="true"></i>Kênh Chăm sóc KH
                                        </a>
                                    </li>
                                <?php elseif ($isMember): ?>
                                    <li>
                                        <a class="dropdown-item" href="index.php?controller=order&amp;action=index">
                                            <i class="bi bi-receipt me-2" aria-hidden="true"></i>Đơn hàng của tôi
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <a class="dropdown-item" href="index.php?controller=auth&amp;action=profile">
                                        <i class="bi bi-person-gear me-2" aria-hidden="true"></i>Hồ sơ cá nhân
                                    </a>
                                </li>
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
                        <a class="btn btn-soft btn-sm" href="index.php?controller=auth&amp;action=login">Đăng nhập</a>
                        <a class="btn btn-brand btn-sm" href="index.php?controller=auth&amp;action=register">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>

<main id="main-content"<?= !empty($fullWidth) ? ' class="pb-4"' : ' class="container py-4 py-lg-5"' ?>>
    <?php
    $flashAlerts = [];
    foreach (['global', 'cart', 'orders'] as $flashKey) {
        $flash = flash_get($flashKey);
        if ($flash !== null) {
            $flashAlerts[] = $flash;
        }
    }
    ?>
    <?php if ($flashAlerts !== []): ?>
        <div class="<?= !empty($fullWidth) ? 'container mt-3' : 'mb-3' ?>">
            <?php foreach ($flashAlerts as $flash): ?>
                <div class="alert alert-<?= $escape($flash['type']) ?> alert-dismissible fade show" role="alert">
                    <?= $escape($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
