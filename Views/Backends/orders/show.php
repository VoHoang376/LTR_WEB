<?php
/** Quản trị – chi tiết đơn hàng và đổi trạng thái. */
require __DIR__ . '/../layouts/header.php';

$orderCode = '#' . str_pad((string) $order['id'], 6, '0', STR_PAD_LEFT);
$status = (int) $order['status'];
$stepLabels = [
    0 => 'Chờ xác nhận',
    1 => 'Đã xác nhận',
    2 => 'Đang giao',
    3 => 'Hoàn tất',
];
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h2 class="h4 fw-bold mb-1">Đơn hàng <?= e($orderCode) ?></h2>
        <p class="text-secondary mb-0 small">
            Đặt lúc <?= e(format_datetime($order['created_at'])) ?> ·
            Khách hàng: <?= e($order['user_name']) ?> (<?= e($order['user_email']) ?>)
        </p>
    </div>
    <span class="status-pill text-bg-<?= e(order_status_badge($status)) ?> fs-6">
        <?= e(order_status_label($status)) ?>
    </span>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="admin-card mb-3">
            <div class="card-head">
                <h2>Sản phẩm trong đơn</h2>
                <span class="badge text-bg-light border"><?= count($items) ?> sản phẩm</span>
            </div>
            <div class="table-responsive">
                <table class="table table-admin align-middle">
                    <caption class="visually-hidden">Sản phẩm của đơn hàng <?= e($orderCode) ?></caption>
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
                                    <span class="thumb-cell">
                                        <img src="<?= e(upload_url($item['thumbnail'])) ?>"
                                             alt="<?= e($item['product_name']) ?>" loading="lazy" width="52" height="52">
                                    </span>
                                    <div>
                                        <span class="fw-semibold d-block"><?= e($item['product_name']) ?></span>
                                        <span class="small text-secondary">Mã sản phẩm: #<?= (int) $item['product_id'] ?></span>
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
                        <td class="text-end fw-bold"><?= e(format_price($order['total_amount'])) ?></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-head"><h2>Thông tin nhận hàng</h2></div>
            <div class="card-body">
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
                        <span class="d-block text-secondary">Tài khoản đặt hàng</span>
                        <strong><?= e($order['user_name']) ?></strong>
                    </div>
                    <?php if (!empty($order['note'])): ?>
                        <div class="col-12">
                            <span class="d-block text-secondary">Ghi chú của khách</span>
                            <strong><?= e($order['note']) ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card mb-3">
            <div class="card-head"><h2>Trạng thái đơn hàng</h2></div>
            <div class="card-body">
                <?php if ($status === 4): ?>
                    <div class="alert alert-danger mb-0 small">
                        <i class="bi bi-x-circle me-1" aria-hidden="true"></i>
                        Đơn hàng đã bị hủy. Tồn kho đã được hoàn lại.
                    </div>
                <?php else: ?>
                    <ul class="timeline-steps">
                        <?php foreach ($stepLabels as $step => $label): ?>
                            <li class="<?= $status >= $step ? 'done' : '' ?>">
                                <?= e($label) ?>
                                <?php if ($status === $step): ?>
                                    <span class="badge text-bg-primary ms-1">Hiện tại</span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if ($allowedStatuses !== []): ?>
                    <form method="post" action="index.php?controller=admin&amp;action=order-status" class="mt-3"
                          data-confirm="Cập nhật trạng thái đơn hàng?"><?= csrf_field() ?>
                        <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                        <label class="form-label" for="order-status-select">Chuyển sang trạng thái</label>
                        <select class="form-select form-select-sm mb-2" id="order-status-select" name="status" required>
                            <?php foreach ($allowedStatuses as $allowed): ?>
                                <option value="<?= (int) $allowed ?>"><?= e(order_status_label($allowed)) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i>Cập nhật trạng thái
                        </button>
                        <?php if (in_array(4, $allowedStatuses, true)): ?>
                            <p class="form-text mb-0">Chọn "Đã hủy" sẽ tự động hoàn lại tồn kho cho sản phẩm.</p>
                        <?php endif; ?>
                    </form>
                <?php else: ?>
                    <p class="small text-secondary mb-0 mt-3">Đơn hàng đã ở trạng thái kết thúc, không thể chuyển tiếp.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-head"><h2>Tóm tắt thanh toán</h2></div>
            <div class="card-body">
                <div class="order-summary-card">
                    <div class="row-line">
                        <span>Phương thức</span>
                        <span><?= e(payment_method_label($order['payment_method'])) ?></span>
                    </div>
                    <div class="row-line">
                        <span>Tổng tiền hàng</span>
                        <span><?= e(format_price($order['total_amount'])) ?></span>
                    </div>
                    <div class="row-line">
                        <span>Phí vận chuyển</span>
                        <span>Miễn phí</span>
                    </div>
                    <div class="row-line total">
                        <span>Khách phải trả</span>
                        <span><?= e(format_price($order['total_amount'])) ?></span>
                    </div>
                </div>
                <a class="btn btn-outline-secondary btn-sm w-100 mt-3"
                   href="index.php?controller=admin&amp;action=orders">
                    <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Danh sách đơn hàng
                </a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
