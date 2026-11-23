<?php

session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
]);
session_start();

require_once __DIR__ . '/Config/database.php';
require_once __DIR__ . '/Models/UserModel.php';
require_once __DIR__ . '/Controllers/AuthController.php';
require_once __DIR__ . '/Controllers/AboutController.php';
require_once __DIR__ . '/Controllers/FaqController.php';

$controller = $_GET['controller'] ?? '';
$action = $_GET['action'] ?? '';

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