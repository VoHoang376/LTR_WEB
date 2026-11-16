<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản trị</title>
</head>
<body>
    <main>
        <h1>Khu vực quản trị</h1>
        <p>Xin chào, <?= $escape($_SESSION['fullname'] ?? '') ?>.</p>
        <p>Tài khoản của bạn có quyền quản trị.</p>
        <p><a href="index.php?controller=auth&amp;action=profile">Hồ sơ cá nhân</a></p>
        <form method="post" action="index.php?controller=auth&amp;action=logout">
            <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token']) ?>">
            <button type="submit">Đăng xuất</button>
        </form>
    </main>
</body>
</html>
