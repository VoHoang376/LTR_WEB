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

// Cho Permission dùng chung kết nối PDO (Permission::requireLogin()/requireAdmin()).
Permission::setDb($db);

$controller = $_GET['controller'] ?? '';
$action = $_GET['action'] ?? '';

/* ------------------------------------------------------------------ Auth */
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
