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
require_once __DIR__ . '/Controllers/PostController.php';

Permission::setDb($db);
(new PostController($db))->publicShow();
