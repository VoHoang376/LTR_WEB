<?php
/** Chi tiết đơn hàng của thành viên: trạng thái, sản phẩm, thông tin nhận hàng. */
require __DIR__ . '/../partials/header.php';

$orderCode = '#' . str_pad((string) $order['id'], 6, '0', STR_PAD_LEFT);
$status = (int) $order['status'];
$stepLabels = [
    0 => 'Chờ xác nhận',
    1 => 'Đã xác nhận',
    2 => 'Đang giao',
    3 => 'Hoàn tất',
];
?>
<div class="page-hero py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php?controller=order&amp;action=index">Đơn hàng của tôi</a></li>
                <li class="breadcrumb-item active" aria-current="page">Đơn <?= e($orderCode) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="h3 fw-bold mb-1">Đơn hàng <?= e($orderCode) ?></h1>
            <p class="text-secondary mb-0">Đặt ngày <?= e(format_datetime($order['created_at'])) ?></p>
        </div>
        <span class="status-pill text-bg-<?= e(order_status_badge($status)) ?> fs-6">
            <?= e(order_status_label($status)) ?>
        </span>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="panel mb-4">
                <div class="panel-header">Sản phẩm trong đơn</div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <caption class="visually-hidden">Chi tiết sản phẩm của đơn hàng <?= e($orderCode) ?></caption>
                        <thead>
                        <tr>
                            <th scope="col">Sản phẩm</th>
                            <th scope="col" class="text-end">Đơn giá</th>
                            <th scope="col" class="text-center">SL</th>
                            <th scope="col" class="text-end">Thành tiền</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?= e(upload_url($item['thumbnail'])) ?>"
                                             alt="<?= e($item['product_name']) ?>" width="52" height="52"
                                             class="rounded border object-fit-cover" loading="lazy">
                                        <div>
                                            <?php if (!empty($item['product_slug']) && (int) $item['product_status'] === 1): ?>
                                                <a class="fw-semibold"
                                                   href="index.php?controller=product&amp;action=show&amp;slug=<?= rawurlencode($item['product_slug']) ?>">
                                                    <?= e($item['product_name']) ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="fw-semibold"><?= e($item['product_name']) ?></span>
                                                <span class="d-block small text-secondary">Sản phẩm đã ngừng kinh doanh</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end"><?= e(format_price($item['price'])) ?></td>
                                <td class="text-center"><?= (int) $item['quantity'] ?></td>
                                <td class="text-end fw-semibold">
                                    <?= e(format_price((float) $item['price'] * (int) $item['quantity'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th scope="row" colspan="3" class="text-end">Tổng cộng</th>
                            <td class="text-end">
                                <span class="price"><?= e(format_price($order['total_amount'])) ?></span>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">Thông tin nhận hàng</div>
                <div class="panel-body">
                    <div class="row g-3 small">
                        <div class="col-sm-6">
                            <span class="d-block text-secondary">Người nhận</span>
                            <strong><?= e($order['receiver_name']) ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="d-block text-secondary">Số điện thoại</span>
                            <strong><?= e($order['receiver_phone']) ?></strong>
                        </div>
                        <div class="col-12">
                            <span class="d-block text-secondary">Địa chỉ</span>
                            <strong><?= e($order['shipping_address']) ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="d-block text-secondary">Phương thức thanh toán</span>
                            <strong><?= e(payment_method_label($order['payment_method'])) ?></strong>
                        </div>
                        <?php if (!empty($order['note'])): ?>
                            <div class="col-sm-6">
                                <span class="d-block text-secondary">Ghi chú</span>
                                <strong><?= e($order['note']) ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="panel mb-4">
                <div class="panel-header">Trạng thái đơn hàng</div>
                <div class="panel-body">
                    <?php if ($status === 4): ?>
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-x-circle me-1" aria-hidden="true"></i>
                            Đơn hàng đã được huỷ. Nếu cần hỗ trợ, vui lòng liên hệ hotline 1900 6868.
                        </div>
                    <?php else: ?>
                        <ul class="timeline mb-0">
                            <?php foreach ($stepLabels as $step => $label): ?>
                                <li class="<?= $status >= $step ? 'is-done' : '' ?>">
                                    <?= e($label) ?>
                                    <?php if ($status === $step): ?>
                                        <span class="badge badge-soft ms-1">Hiện tại</span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($canCancel): ?>
                <div class="panel mb-4">
                    <div class="panel-header">Thay đổi đơn hàng</div>
                    <div class="panel-body">
                        <p class="small text-secondary">
                            Đơn hàng đang chờ xác nhận nên bạn có thể huỷ. Sau khi huỷ, tồn kho sẽ được hoàn lại.
                        </p>
                        <form method="post" action="index.php?controller=order&amp;action=cancel"
                              data-confirm="Bạn chắc chắn muốn huỷ đơn hàng này?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-x-circle me-1" aria-hidden="true"></i>Huỷ đơn hàng
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <div class="d-grid gap-2">
                <a class="btn btn-soft" href="index.php?controller=order&amp;action=index">
                    <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Danh sách đơn hàng
                </a>
                <a class="btn btn-brand" href="index.php?controller=product&amp;action=index">
                    <i class="bi bi-bag me-1" aria-hidden="true"></i>Tiếp tục mua sắm
                </a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
