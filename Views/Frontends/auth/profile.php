<?php
$pageTitle = 'Hồ sơ cá nhân';
$avatarUrl = $user['avatar'] && $user['avatar'] !== 'default_avatar.png'
    ? 'Public/uploads/avatars/' . rawurlencode(basename($user['avatar']))
    : null;
require __DIR__ . '/../../Layouts/Frontend/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <h1 class="h3 mb-0">Hồ sơ cá nhân</h1>
            <form method="post" action="index.php?controller=auth&amp;action=logout">
                <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                <button class="btn btn-outline-danger btn-sm" type="submit">Đăng xuất</button>
            </form>
        </div>
        <?php if ($error !== null): ?>
            <div class="alert alert-danger" role="alert"><?= $escape($error) ?></div>
        <?php endif; ?>
        <?php if ($success !== null): ?>
            <div class="alert alert-success" role="status"><?= $escape($success) ?></div>
        <?php endif; ?>
        <section class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <form method="post" enctype="multipart/form-data" action="index.php?controller=auth&amp;action=profile">
                    <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                    <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <?php if ($avatarUrl !== null): ?>
                            <img class="profile-avatar rounded-circle border" src="<?= $escape($avatarUrl) ?>" alt="Ảnh đại diện">
                        <?php else: ?>
                            <div class="profile-avatar rounded-circle border bg-light d-flex align-items-center justify-content-center text-secondary" aria-label="Chưa có ảnh đại diện">
                                <?= $escape(mb_substr($user['fullname'], 0, 1, 'UTF-8')) ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <h2 class="h5 mb-1"><?= $escape($user['fullname']) ?></h2>
                            <p class="text-secondary mb-0"><?= $escape($user['email']) ?></p>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="fullname">Họ và tên</label>
                            <input class="form-control" id="fullname" type="text" name="fullname" value="<?= $escape($user['fullname']) ?>" maxlength="100" autocomplete="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="phone">Số điện thoại</label>
                            <input class="form-control" id="phone" type="tel" name="phone" value="<?= $escape($user['phone']) ?>" maxlength="20" autocomplete="tel">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="address">Địa chỉ</label>
                            <input class="form-control" id="address" type="text" name="address" value="<?= $escape($user['address']) ?>" maxlength="255" autocomplete="street-address">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="avatar">Ảnh đại diện (JPG, PNG, WebP; tối đa 2 MB)</label>
                            <input class="form-control" id="avatar" type="file" name="avatar" accept="image/jpeg,image/png,image/webp">
                        </div>
                    </div>
                    <hr class="my-4">
                    <h2 class="h5 mb-3">Đổi mật khẩu</h2>
                    <p class="form-text">Để trống các trường bên dưới nếu bạn không muốn đổi mật khẩu.</p>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="current_password">Mật khẩu hiện tại</label>
                            <input class="form-control" id="current_password" type="password" name="current_password" autocomplete="current-password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="new_password">Mật khẩu mới (ít nhất 8 ký tự)</label>
                            <input class="form-control" id="new_password" type="password" name="new_password" minlength="8" autocomplete="new-password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="password_confirmation">Nhập lại mật khẩu mới</label>
                            <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" minlength="8" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button class="btn btn-primary" type="submit">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
<?php require __DIR__ . '/../../Layouts/Frontend/footer.php'; ?>
