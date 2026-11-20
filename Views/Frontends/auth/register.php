<?php
$pageTitle = 'Đăng ký';
require __DIR__ . '/../../Layouts/Frontend/header.php';
?>
<section class="card auth-card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">
        <h1 class="h3 mb-4 text-center">Tạo tài khoản</h1>
        <?php if ($error !== null): ?>
            <div class="alert alert-danger" role="alert"><?= $escape($error) ?></div>
        <?php endif; ?>
        <form method="post" action="index.php?controller=auth&amp;action=register">
            <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
            <div class="mb-3">
                <label class="form-label" for="fullname">Họ và tên</label>
                <input class="form-control" id="fullname" type="text" name="fullname" value="<?= $escape($old['fullname']) ?>" maxlength="100" autocomplete="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email" type="email" name="email" value="<?= $escape($old['email']) ?>" maxlength="191" autocomplete="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Mật khẩu (ít nhất 8 ký tự)</label>
                <input class="form-control" id="password" type="password" name="password" minlength="8" autocomplete="new-password" required>
            </div>
            <div class="mb-4">
                <label class="form-label" for="password_confirmation">Nhập lại mật khẩu</label>
                <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" minlength="8" autocomplete="new-password" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Đăng ký</button>
        </form>
        <p class="mb-0 mt-4 text-center">
            Đã có tài khoản?
            <a href="index.php?controller=auth&amp;action=login">Đăng nhập</a>
        </p>
    </div>
</section>
<?php require __DIR__ . '/../../Layouts/Frontend/footer.php'; ?>
