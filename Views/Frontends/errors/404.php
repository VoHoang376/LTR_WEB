<?php
/** Trang 404 dùng chung cho các trang người dùng. */
$pageTitle = $pageTitle ?? 'Không tìm thấy trang';
require __DIR__ . '/../partials/header.php';
?>
<div class="container mt-5">
    <div class="panel empty-state">
        <div class="empty-icon"><i class="bi bi-exclamation-circle" aria-hidden="true"></i></div>
        <h1 class="h3 fw-bold">404 – Không tìm thấy nội dung</h1>
        <p class="text-secondary mb-4">
            Trang hoặc sản phẩm bạn tìm không tồn tại, đã bị xoá hoặc tạm ngừng hiển thị.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a class="btn btn-brand" href="index.php?controller=product&amp;action=index">
                <i class="bi bi-bag me-1" aria-hidden="true"></i>Xem sản phẩm
            </a>
            <a class="btn btn-soft" href="index.php?controller=cart&amp;action=index">
                <i class="bi bi-cart3 me-1" aria-hidden="true"></i>Giỏ hàng
            </a>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
