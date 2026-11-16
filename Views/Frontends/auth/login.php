<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập</title>
</head>
<body>
    <main>
        <h1>Đăng nhập</h1>
        <?php if ($error !== null): ?>
            <p role="alert"><?= $escape($error) ?></p>
        <?php endif; ?>
        <form method="post" action="index.php?controller=auth&amp;action=login">
            <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
            <label>
                Email
                <input type="email" name="email" value="<?= $escape($email) ?>" maxlength="191" required>
            </label>
            <label>
                Mật khẩu
                <input type="password" name="password" required>
            </label>
            <button type="submit">Đăng nhập</button>
        </form>
        <p>Chưa có tài khoản? <a href="index.php?controller=auth&amp;action=register">Đăng ký</a></p>
    </main>
</body>
</html>
