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
require_once __DIR__ . '/Controllers/AuthController.php';
require_once __DIR__ . '/Controllers/AboutController.php';
require_once __DIR__ . '/Controllers/FaqController.php';

// Cho Permission dùng chung kết nối PDO (Permission::requireLogin()/requireAdmin()).
Permission::setDb($db);

$controller = $_GET['controller'] ?? '';
$action = $_GET['action'] ?? '';

/* ------------------------------------------------------------------ Auth */
if ($controller === 'about' && ($action === '' || $action === 'index')) {
    (new AboutController($db))->publicIndex();
    exit;
}

if ($controller === 'faq' && ($action === '' || $action === 'index')) {
    (new FaqController($db))->publicIndex();
    exit;
}

$contentAdminRoutes = [
    'adminAbout' => [
        'index' => 'adminIndex',
        'create' => 'create',
        'store' => 'store',
        'edit' => 'edit',
        'update' => 'update',
        'delete' => 'delete',
    ],
    'adminFaq' => [
        'index' => 'adminIndex',
        'create' => 'create',
        'store' => 'store',
        'edit' => 'edit',
        'update' => 'update',
        'delete' => 'delete',
    ],
];

if (isset($contentAdminRoutes[$controller])) {
    if (!isset($contentAdminRoutes[$controller][$action])) {
        http_response_code(404);
        exit('Không tìm thấy chức năng quản trị.');
    }

    $controllerClass = $controller === 'adminAbout' ? 'AboutController' : 'FaqController';
    $contentController = new $controllerClass($db);
    $contentController->{$contentAdminRoutes[$controller][$action]}();
    exit;
}

if ($controller === 'auth') {
    $authController = new AuthController($db);
    $actions = [
        'login' => 'login',
        'register' => 'register',
        'logout' => 'logout',
        'profile' => 'profile',
    ];

    if (isset($actions[$action])) {
        $authController->{$actions[$action]}();
        exit;
    }
}

/* --------------------------------------- Phần #3: sản phẩm (trang người dùng) */
if ($controller === 'product') {
    $productActions = ['index', 'show'];
    if (in_array($action, $productActions, true)) {
        require_once __DIR__ . '/Controllers/ProductController.php';
        $productController = new ProductController($db);
        $productController->{$action}();
        exit;
    }
}

/* ------------------------------------------------------ Phần #3: giỏ hàng */
if ($controller === 'cart') {
    $cartActions = ['index', 'add', 'update', 'remove', 'clear'];
    if (in_array($action, $cartActions, true)) {
        require_once __DIR__ . '/Controllers/CartController.php';
        $cartController = new CartController($db);
        $cartController->{$action}();
        exit;
    }
}

/* ---------------------------------------------------- Phần #3: thanh toán */
if ($controller === 'checkout') {
    $checkoutActions = ['index', 'success'];
    if (in_array($action, $checkoutActions, true)) {
        require_once __DIR__ . '/Controllers/CheckoutController.php';
        $checkoutController = new CheckoutController($db);
        $checkoutController->{$action}();
        exit;
    }
}

/* ------------------------------------- Phần #3: đơn hàng của thành viên */
if ($controller === 'order') {
    $orderActions = ['index', 'show', 'cancel'];
    if (in_array($action, $orderActions, true)) {
        require_once __DIR__ . '/Controllers/OrderController.php';
        $orderController = new OrderController($db);
        $orderController->{$action}();
        exit;
    }
}

/* -------------------------------------------------------------- Quản trị */
if ($controller === 'admin') {
    Permission::requireAdmin();

    if ($action === '' || $action === 'dashboard') {
        require __DIR__ . '/Views/Backends/dashboard.php';
        exit;
    }

    // Mỗi action quản trị ứng với một lớp controller + phương thức cụ thể.
    $adminRoutes = [
        // Sản phẩm
        'products' => ['AdminProductController', 'index'],
        'product-create' => ['AdminProductController', 'create'],
        'product-edit' => ['AdminProductController', 'edit'],
        'product-delete' => ['AdminProductController', 'delete'],
        'product-gallery-upload' => ['AdminProductController', 'uploadGallery'],
        'product-image-delete' => ['AdminProductController', 'deleteImage'],
        // Danh mục sản phẩm
        'categories' => ['AdminCategoryController', 'index'],
        'category-create' => ['AdminCategoryController', 'create'],
        'category-edit' => ['AdminCategoryController', 'edit'],
        'category-delete' => ['AdminCategoryController', 'delete'],
        // Đơn hàng
        'orders' => ['AdminOrderController', 'index'],
        'order-show' => ['AdminOrderController', 'show'],
        'order-status' => ['AdminOrderController', 'updateStatus'],
    ];

    if (isset($adminRoutes[$action])) {
        [$adminClass, $adminMethod] = $adminRoutes[$action];
        require_once __DIR__ . '/Controllers/' . $adminClass . '.php';
        $adminController = new $adminClass($db);
        $adminController->{$adminMethod}();
        exit;
    }
}

/* -------------------------------------------------------- Route mặc định */
if (empty($_SESSION['user_id'])) {
    header('Location: index.php?controller=auth&action=login');
    exit;
}

header('Location: index.php?controller=auth&action=profile');
if ($controller === 'admin') {
    require_once __DIR__ . '/Controllers/AdminController.php';
    $adminController = new AdminController($db);
    $actions = [
        'dashboard' => 'dashboard',
        'contacts' => 'contacts',
        'contact_status' => 'updateContactStatus',
        'contact_delete' => 'deleteContact',
        'settings' => 'settings',
        'pricing' => 'pricing',
        'pricing_save' => 'savePricing',
        'pricing_delete' => 'deletePricing',
    ];

    $actionName = $actions[$action] ?? 'dashboard';
    $adminController->{$actionName}();
    exit;
}

if ($controller === 'pricing') {
    require_once __DIR__ . '/Controllers/PricingController.php';
    $pricingController = new PricingController($db);
    $pricingController->index();
    exit;
}

if ($controller === 'contact') {
    require_once __DIR__ . '/Models/ContactModel.php';
    require_once __DIR__ . '/Controllers/ContactController.php';
    $contactController = new ContactController($db);
    $contactController->index();
    exit;
}

// Trang chủ (mặc định)
require_once __DIR__ . '/Controllers/HomeController.php';
$homeController = new HomeController($db);
$homeController->index();
