<?php
$pageTitle = 'Đăng nhập';
require __DIR__ . '/../../Layouts/Frontend/header.php';
?>
<section class="card auth-card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">
        <h1 class="h3 mb-4 text-center">Đăng nhập</h1>
        <?php if ($error !== null): ?>
            <div class="alert alert-danger" role="alert"><?= $escape($error) ?></div>
        <?php endif; ?>
        <form method="post" action="index.php?controller=auth&amp;action=login">
            <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email" type="email" name="email" value="<?= $escape($email) ?>" maxlength="191" autocomplete="email" required>
            </div>
            <div class="mb-4">
                <label class="form-label" for="password">Mật khẩu</label>
                <input class="form-control" id="password" type="password" name="password" autocomplete="current-password" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Đăng nhập</button>
        </form>
        <p class="mb-0 mt-4 text-center">
            Chưa có tài khoản?
            <a href="index.php?controller=auth&amp;action=register">Đăng ký</a>
        </p>
    </div>
</section>
<?php require __DIR__ . '/../../Layouts/Frontend/footer.php'; ?>
