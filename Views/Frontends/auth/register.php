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
    <title>Đăng ký</title>
</head>
<body>
    <main>
        <h1>Tạo tài khoản</h1>
        <?php if ($error !== null): ?>
            <p role="alert"><?= $escape($error) ?></p>
        <?php endif; ?>
        <form method="post" action="index.php?controller=auth&amp;action=register">
            <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
            <label>
                Họ và tên
                <input type="text" name="fullname" value="<?= $escape($old['fullname']) ?>" maxlength="100" required>
            </label>
            <label>
                Email
                <input type="email" name="email" value="<?= $escape($old['email']) ?>" maxlength="191" required>
            </label>
            <label>
                Mật khẩu (ít nhất 8 ký tự)
                <input type="password" name="password" minlength="8" required>
            </label>
            <label>
                Nhập lại mật khẩu
                <input type="password" name="password_confirmation" minlength="8" required>
            </label>
            <button type="submit">Đăng ký</button>
        </form>
        <p>Đã có tài khoản? <a href="index.php?controller=auth&amp;action=login">Đăng nhập</a></p>
    </main>
</body>
</html>
