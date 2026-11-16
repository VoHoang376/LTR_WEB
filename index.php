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

$controller = $_GET['controller'] ?? '';
$action = $_GET['action'] ?? '';

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

if ($controller === 'admin' && $action === 'dashboard') {
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php?controller=auth&action=login');
        exit;
    }
    $userModel = new UserModel($db);
    $currentUser = $userModel->findById($_SESSION['user_id']);
    if (!$currentUser || (int) $currentUser['status'] !== 1) {
        $_SESSION = [];
        session_destroy();
        header('Location: index.php?controller=auth&action=login');
        exit;
    }
    if ((int) $currentUser['role'] !== 0) {
        http_response_code(403);
        exit('Bạn không có quyền truy cập trang quản trị.');
    }

    require __DIR__ . '/Views/Backends/dashboard.php';
    exit;
}

if (empty($_SESSION['user_id'])) {
    header('Location: index.php?controller=auth&action=login');
    exit;
}

header('Location: index.php?controller=auth&action=profile');