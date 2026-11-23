<?php
/** Xác nhận đặt hàng thành công. */
require __DIR__ . '/../partials/header.php';

$orderCode = '#' . str_pad((string) $order['id'], 6, '0', STR_PAD_LEFT);
$paymentMethod = (int) $order['payment_method'];
?>
<div class="page-hero py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php?controller=product&amp;action=index">Sản phẩm</a></li>
                <li class="breadcrumb-item"><a href="index.php?controller=cart&amp;action=index">Giỏ hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Hoàn tất</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mt-4">
    <div class="checkout-steps">
        <span class="step is-done"><i class="bi bi-check2" aria-hidden="true"></i>Giỏ hàng</span>
        <span class="step is-done"><i class="bi bi-check2" aria-hidden="true"></i>Thông tin giao hàng</span>
        <span class="step is-current"><i class="bi bi-check2-circle" aria-hidden="true"></i>Hoàn tất</span>
    </div>

    <div class="panel mb-4">
        <div class="panel-body text-center py-4">
            <div class="empty-icon bg-success-subtle text-success mx-auto mb-3">
                <i class="bi bi-check2-circle" aria-hidden="true"></i>
            </div>
            <h1 class="h4 fw-bold mb-2">Đặt hàng thành công!</h1>
            <p class="text-secondary mb-1">
                Cảm ơn bạn đã đặt hàng tại <?= e(site_name()) ?>. Mã đơn hàng của bạn là
                <strong><?= e($orderCode) ?></strong>.
            </p>
            <p class="text-secondary mb-0">
                Chúng tôi sẽ liên hệ xác nhận đơn hàng qua số điện thoại
                <strong><?= e($order['receiver_phone']) ?></strong>.
            </p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="panel">
                <div class="panel-header">
                    <span>Thông tin đơn hàng</span>
                    <span class="badge text-bg-<?= e(order_status_badge($order['status'])) ?>">
                        <?= e(order_status_label($order['status'])) ?>
                    </span>
                </div>
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
                            <span class="d-block text-secondary">Địa chỉ giao hàng</span>
                            <strong><?= e($order['shipping_address']) ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="d-block text-secondary">Phương thức thanh toán</span>
                            <strong><?= e(payment_method_label($order['payment_method'])) ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="d-block text-secondary">Ngày đặt hàng</span>
                            <strong><?= e(format_datetime($order['created_at'])) ?></strong>
                        </div>
                        <?php if (!empty($order['note'])): ?>
                            <div class="col-12">
                                <span class="d-block text-secondary">Ghi chú</span>
                                <strong><?= e($order['note']) ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <caption class="visually-hidden">Các sản phẩm trong đơn hàng</caption>
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
                                        <span class="d-block fw-semibold"><?= e($item['product_name']) ?></span>
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
            </div>
        </div>

        <div class="col-lg-5">
            <?php if ($paymentMethod === 1): ?>
                <div class="panel mb-4">
                    <div class="panel-header">Hướng dẫn chuyển khoản</div>
                    <div class="panel-body small">
                        <p class="mb-2">Vui lòng chuyển khoản với nội dung
                            <strong><?= e($orderCode) ?></strong> tới:</p>
                        <ul class="list-unstyled mb-3">
                            <li><span class="text-secondary">Ngân hàng:</span> <strong>Vietcombank</strong></li>
                            <li><span class="text-secondary">Số tài khoản:</span> <strong>0123 456 789 012</strong></li>
                            <li><span class="text-secondary">Chủ tài khoản:</span> <strong>CONG TY LTR TECHNOLOGY</strong></li>
                            <li><span class="text-secondary">Số tiền:</span>
                                <strong class="text-danger"><?= e(format_price($order['total_amount'])) ?></strong></li>
                        </ul>
                        <p class="text-secondary mb-0">
                            Đơn hàng được xử lý sau khi chúng tôi nhận được tiền. Vui lòng giữ lại uỷ nhiệm chi.
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="panel mb-4">
                    <div class="panel-header">Thanh toán khi nhận hàng</div>
                    <div class="panel-body small">
                        <p class="mb-2">Bạn sẽ thanh toán
                            <strong class="text-danger"><?= e(format_price($order['total_amount'])) ?></strong>
                            bằng tiền mặt cho nhân viên giao hàng.</p>
                        <p class="text-secondary mb-0">Vui lòng kiểm tra kỹ sản phẩm trước khi thanh toán.</p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="d-grid gap-2">
                <a class="btn btn-brand" href="index.php?controller=order&amp;action=show&amp;id=<?= (int) $order['id'] ?>">
                    <i class="bi bi-receipt me-2" aria-hidden="true"></i>Xem chi tiết đơn hàng
                </a>
                <a class="btn btn-soft" href="index.php?controller=product&amp;action=index">
                    <i class="bi bi-bag me-2" aria-hidden="true"></i>Tiếp tục mua sắm
                </a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
