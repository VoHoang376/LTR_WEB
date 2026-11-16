<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$avatarUrl = $user['avatar'] && $user['avatar'] !== 'default_avatar.png'
    ? 'Public/uploads/avatars/' . rawurlencode(basename($user['avatar']))
    : null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hồ sơ cá nhân</title>
</head>
<body>
    <main>
        <h1>Hồ sơ cá nhân</h1>
        <?php if ($error !== null): ?>
            <p role="alert"><?= $escape($error) ?></p>
        <?php endif; ?>
        <?php if ($success !== null): ?>
            <p role="status"><?= $escape($success) ?></p>
        <?php endif; ?>
        <?php if ($avatarUrl !== null): ?>
            <img src="<?= $escape($avatarUrl) ?>" alt="Ảnh đại diện" width="120" height="120">
        <?php else: ?>
            <p>Chưa có ảnh đại diện.</p>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" action="index.php?controller=auth&amp;action=profile">
            <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
            <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
            <label>
                Họ và tên
                <input type="text" name="fullname" value="<?= $escape($user['fullname']) ?>" maxlength="100" required>
            </label>
            <label>
                Email
                <input type="email" value="<?= $escape($user['email']) ?>" disabled>
            </label>
            <label>
                Số điện thoại
                <input type="tel" name="phone" value="<?= $escape($user['phone']) ?>" maxlength="20">
            </label>
            <label>
                Địa chỉ
                <input type="text" name="address" value="<?= $escape($user['address']) ?>" maxlength="255">
            </label>
            <label>
                Ảnh đại diện (JPG, PNG, WebP; tối đa 2 MB)
                <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp">
            </label>
            <fieldset>
                <legend>Đổi mật khẩu (để trống nếu không đổi)</legend>
                <label>
                    Mật khẩu hiện tại
                    <input type="password" name="current_password" autocomplete="current-password">
                </label>
                <label>
                    Mật khẩu mới (ít nhất 8 ký tự)
                    <input type="password" name="new_password" minlength="8" autocomplete="new-password">
                </label>
                <label>
                    Nhập lại mật khẩu mới
                    <input type="password" name="password_confirmation" minlength="8" autocomplete="new-password">
                </label>
            </fieldset>
            <button type="submit">Lưu thay đổi</button>
        </form>
        <p><a href="index.php">Trang chủ</a></p>
        <form method="post" action="index.php?controller=auth&amp;action=logout">
            <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
            <button type="submit">Đăng xuất</button>
        </form>
    </main>
</body>
</html>
