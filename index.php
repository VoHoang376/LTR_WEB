<?php

session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
]);
session_start();

require_once __DIR__ . '/Config/database.php';
require_once __DIR__ . '/Core/helpers.php';
require_once __DIR__ . '/Core/Permission.php';
require_once __DIR__ . '/Models/UserModel.php';

Permission::setDb($db);

$controller = trim((string) ($_GET['controller'] ?? ''));
$action = trim((string) ($_GET['action'] ?? ''));

/* ---------------------------------------------------------- Trang chủ */
if ($controller === '' || $controller === 'home') {
    require_once __DIR__ . '/Controllers/HomeController.php';
    (new HomeController($db))->index();
    exit;
}

/* ----------------------------------------------------- Xác thực (Auth) */
if ($controller === 'auth') {
    require_once __DIR__ . '/Controllers/AuthController.php';
    $authController = new AuthController($db);
    $authActions = [
        '' => 'login',
        'login' => 'login',
        'register' => 'register',
        'logout' => 'logout',
        'profile' => 'profile',
    ];

    if (isset($authActions[$action])) {
        $authController->{$authActions[$action]}();
        exit;
    }
}

/* ------------------------------------------- Phần #1: Bảng giá & Liên hệ */
if ($controller === 'pricing' && ($action === '' || $action === 'index')) {
    require_once __DIR__ . '/Controllers/PricingController.php';
    (new PricingController($db))->index();
    exit;
}

if ($controller === 'contact' && ($action === '' || $action === 'index')) {
    require_once __DIR__ . '/Controllers/ContactController.php';
    (new ContactController($db))->index();
    exit;
}

/* ------------------------------------------ Phần #2: Giới thiệu & FAQ */
if ($controller === 'about' && ($action === '' || $action === 'index')) {
    require_once __DIR__ . '/Controllers/AboutController.php';
    (new AboutController($db))->publicIndex();
    exit;
}

if ($controller === 'faq') {
    require_once __DIR__ . '/Controllers/FaqController.php';
    $faqController = new FaqController($db);
    $resolvedAction = $action === '' ? 'index' : $action;
    if ($resolvedAction === 'index') {
        $faqController->publicIndex();
        exit;
    }
    if ($resolvedAction === 'ask') {
        $faqController->ask();
        exit;
    }
}

// Quản trị Giới thiệu (thuộc Quản trị viên – role = 0)
if ($controller === 'adminAbout') {
    Permission::requireAdmin();
    $aboutAdminRoutes = [
        '' => 'adminIndex',
        'index' => 'adminIndex',
        'create' => 'create',
        'store' => 'store',
        'edit' => 'edit',
        'update' => 'update',
        'delete' => 'delete',
    ];
    if (isset($aboutAdminRoutes[$action])) {
        require_once __DIR__ . '/Controllers/AboutController.php';
        (new AboutController($db))->{$aboutAdminRoutes[$action]}();
        exit;
    }
}

// Alias cũ adminFaq -> chuyển về kênh Nhân viên CSKH (role = 2)
if ($controller === 'adminFaq') {
    Permission::requireStaff();
    redirect('index.php?controller=staff&action=faqs');
}

/* ------------------------------------- Phần #3: Sản phẩm · Giỏ · Đơn hàng */
if ($controller === 'product') {
    $resolvedAction = $action === '' ? 'index' : $action;
    if (in_array($resolvedAction, ['index', 'show', 'review'], true)) {
        require_once __DIR__ . '/Controllers/ProductController.php';
        (new ProductController($db))->{$resolvedAction}();
        exit;
    }
}

if ($controller === 'cart') {
    $resolvedAction = $action === '' ? 'index' : $action;
    if (in_array($resolvedAction, ['index', 'add', 'update', 'remove', 'clear'], true)) {
        require_once __DIR__ . '/Controllers/CartController.php';
        (new CartController($db))->{$resolvedAction}();
        exit;
    }
}

if ($controller === 'checkout') {
    $resolvedAction = $action === '' ? 'index' : $action;
    if (in_array($resolvedAction, ['index', 'success'], true)) {
        require_once __DIR__ . '/Controllers/CheckoutController.php';
        (new CheckoutController($db))->{$resolvedAction}();
        exit;
    }
}

if ($controller === 'order') {
    $resolvedAction = $action === '' ? 'index' : $action;
    if (in_array($resolvedAction, ['index', 'show', 'cancel'], true)) {
        require_once __DIR__ . '/Controllers/OrderController.php';
        (new OrderController($db))->{$resolvedAction}();
        exit;
    }
}

/* --------------------------------------- Phần #4: Tin tức công khai */
if ($controller === 'post' || $controller === 'news') {
    require_once __DIR__ . '/Controllers/PostController.php';
    $postController = new PostController($db);
    $resolvedAction = $action === '' ? 'index' : $action;

    if ($resolvedAction === 'index') {
        $postController->publicIndex();
        exit;
    }
    if ($resolvedAction === 'show' || $resolvedAction === 'detail') {
        $postController->publicShow();
        exit;
    }

    $legacyPostAdmin = [
        'admin' => 'adminIndex',
        'create' => 'create',
        'store' => 'store',
        'edit' => 'edit',
        'update' => 'update',
        'delete' => 'delete',
    ];
    if (isset($legacyPostAdmin[$resolvedAction])) {
        $postController->{$legacyPostAdmin[$resolvedAction]}();
        exit;
    }
}

/* ---------------------------------- Kênh Nhân viên Chăm sóc KH (role = 2) */
if ($controller === 'staff') {
    Permission::requireStaff();

    $resolvedAction = $action === '' ? 'dashboard' : $action;
    $staffRoutes = [
        'dashboard' => 'dashboard',
        // Liên hệ khách hàng
        'contacts' => 'contacts',
        'contact-reply' => 'replyContact',
        'contact-status' => 'updateContactStatus',
        'contact-delete' => 'deleteContact',
        // Hỏi/Đáp (FAQ)
        'faqs' => 'faqs',
        'faq-create' => 'faqCreate',
        'faq-store' => 'faqStore',
        'faq-edit' => 'faqEdit',
        'faq-update' => 'faqUpdate',
        'faq-delete' => 'faqDelete',
        // Phản hồi / Đánh giá sản phẩm
        'reviews' => 'reviews',
        'review-reply' => 'replyReview',
        'review-status' => 'updateReviewStatus',
        'review-delete' => 'deleteReview',
        // Kiểm duyệt bình luận bài viết
        'comments' => 'comments',
        'comment-status' => 'updateCommentStatus',
        'comment-delete' => 'deleteComment',
    ];

    if (isset($staffRoutes[$resolvedAction])) {
        require_once __DIR__ . '/Controllers/StaffController.php';
        (new StaffController($db))->{$staffRoutes[$resolvedAction]}();
        exit;
    }
}

/* --------------------------------------- Khu vực Quản trị Admin (role = 0) */
if ($controller === 'admin') {
    Permission::requireAdmin();

    $resolvedAction = $action === '' ? 'dashboard' : $action;

    // Các nghiệp vụ CSKH (contacts, faqs, reviews, comments) tách biệt hoàn toàn sang kênh Staff (role = 2).
    if (in_array($resolvedAction, ['contacts', 'contact_status', 'contact_delete', 'faqs', 'reviews', 'comments', 'comment-status', 'comment-delete'], true)) {
        Permission::deny('Nghiệp vụ giải quyết liên hệ, trả lời hỏi/đáp, phản hồi sản phẩm và duyệt bình luận thuộc thẩm quyền riêng của Nhân viên Chăm sóc khách hàng (role = 2).');
    }

    $adminRoutes = [
        // Tổng quan & Hệ thống
        'dashboard' => ['AdminController', 'dashboard'],
        'settings' => ['AdminController', 'settings'],
        'pricing' => ['AdminController', 'pricing'],
        'pricing_save' => ['AdminController', 'savePricing'],
        'pricing_delete' => ['AdminController', 'deletePricing'],
        'users' => ['AdminController', 'users'],
        'user-update' => ['AdminController', 'updateUser'],

        // Giới thiệu
        'about' => ['AboutController', 'adminIndex'],

        // Bán hàng (#3): Sản phẩm, Danh mục, Đơn hàng
        'products' => ['AdminProductController', 'index'],
        'product-create' => ['AdminProductController', 'create'],
        'product-edit' => ['AdminProductController', 'edit'],
        'product-delete' => ['AdminProductController', 'delete'],
        'product-gallery-upload' => ['AdminProductController', 'uploadGallery'],
        'product-image-delete' => ['AdminProductController', 'deleteImage'],
        'categories' => ['AdminCategoryController', 'index'],
        'category-create' => ['AdminCategoryController', 'create'],
        'category-edit' => ['AdminCategoryController', 'edit'],
        'category-delete' => ['AdminCategoryController', 'delete'],
        'orders' => ['AdminOrderController', 'index'],
        'order-show' => ['AdminOrderController', 'show'],
        'order-status' => ['AdminOrderController', 'updateStatus'],

        // Bài viết (#4)
        'posts' => ['PostController', 'adminIndex'],
        'post-create' => ['PostController', 'create'],
        'post-store' => ['PostController', 'store'],
        'post-edit' => ['PostController', 'edit'],
        'post-update' => ['PostController', 'update'],
        'post-delete' => ['PostController', 'delete'],
    ];

    if (isset($adminRoutes[$resolvedAction])) {
        [$adminClass, $adminMethod] = $adminRoutes[$resolvedAction];
        require_once __DIR__ . '/Controllers/' . $adminClass . '.php';
        (new $adminClass($db))->{$adminMethod}();
        exit;
    }
}

/* ------------------------------------------------------ Không tìm thấy 404 */
http_response_code(404);
$pageTitle = 'Không tìm thấy trang';
require __DIR__ . '/Views/Frontends/errors/404.php';
