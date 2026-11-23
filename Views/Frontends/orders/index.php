<?php
/** Danh sách đơn hàng của thành viên đang đăng nhập. */
require __DIR__ . '/../partials/header.php';
?>
<div class="page-hero py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php?controller=product&amp;action=index">Sản phẩm</a></li>
                <li class="breadcrumb-item active" aria-current="page">Đơn hàng của tôi</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="h3 fw-bold mb-0">Đơn hàng của tôi</h1>
        <span class="text-secondary">
            <?= (int) $result['total'] ?> đơn hàng
        </span>
    </div>

    <?php if ($result['items'] === []): ?>
        <div class="panel empty-state">
            <div class="empty-icon"><i class="bi bi-receipt" aria-hidden="true"></i></div>
            <h2 class="h5 fw-bold">Bạn chưa có đơn hàng nào</h2>
            <p class="text-secondary mb-3">Các đơn hàng bạn đặt sẽ được hiển thị tại đây để theo dõi trạng thái.</p>
            <a class="btn btn-brand" href="index.php?controller=product&amp;action=index">Bắt đầu mua sắm</a>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($result['items'] as $order): ?>
                <?php $orderCode = '#' . str_pad((string) $order['id'], 6, '0', STR_PAD_LEFT); ?>
                <div class="col-md-6">
                    <article class="order-card">
                        <div class="order-head">
                            <h2 class="h6 fw-bold mb-0">Đơn <?= e($orderCode) ?></h2>
                            <span class="status-pill text-bg-<?= e(order_status_badge($order['status'])) ?>">
                                <?= e(order_status_label($order['status'])) ?>
                            </span>
                        </div>
                        <p class="small text-secondary mb-1">
                            <i class="bi bi-calendar3 me-1" aria-hidden="true"></i>
                            <?= e(format_datetime($order['created_at'])) ?>
                        </p>
                        <p class="small text-secondary mb-1">
                            <i class="bi bi-box-seam me-1" aria-hidden="true"></i>
                            <?= (int) $order['item_count'] ?> sản phẩm
                        </p>
                        <p class="small text-secondary mb-3">
                            <i class="bi bi-credit-card me-1" aria-hidden="true"></i>
                            <?= e(payment_method_label($order['payment_method'])) ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <span class="price"><?= e(format_price($order['total_amount'])) ?></span>
                            <a class="btn btn-soft btn-sm"
                               href="index.php?controller=order&amp;action=show&amp;id=<?= (int) $order['id'] ?>">
                                Xem chi tiết <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>

        <?= render_pagination($result) ?>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
