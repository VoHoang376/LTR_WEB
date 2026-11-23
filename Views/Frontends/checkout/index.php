<?php
/** Trang thanh toán: thông tin giao hàng, phương thức thanh toán, xác nhận đơn. */
require __DIR__ . '/../partials/header.php';

$fieldError = static function ($field) use ($errors) {
    return isset($errors[$field]) ? ' is-invalid' : '';
};
?>
<div class="page-hero py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php?controller=product&amp;action=index">Sản phẩm</a></li>
                <li class="breadcrumb-item"><a href="index.php?controller=cart&amp;action=index">Giỏ hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thanh toán</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mt-4">
    <h1 class="h3 fw-bold mb-3">Thanh toán</h1>

    <div class="checkout-steps">
        <span class="step is-done"><i class="bi bi-check2" aria-hidden="true"></i>Giỏ hàng</span>
        <span class="step is-current"><i class="bi bi-2-circle" aria-hidden="true"></i>Thông tin giao hàng</span>
        <span class="step"><i class="bi bi-3-circle" aria-hidden="true"></i>Hoàn tất</span>
    </div>

    <?php if (isset($errors['form'])): ?>
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle me-1" aria-hidden="true"></i><?= e($errors['form']) ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-7">
            <form class="panel js-validate needs-validation" method="post" novalidate
                  action="index.php?controller=checkout&amp;action=index">
                <?= csrf_field() ?>
                <div class="panel-header">Thông tin người nhận</div>
                <div class="panel-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="receiver_name">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control<?= $fieldError('receiver_name') ?>" id="receiver_name"
                                   name="receiver_name" value="<?= e($old['receiver_name']) ?>"
                                   minlength="2" maxlength="100" required>
                            <div class="invalid-feedback">Vui lòng nhập họ tên (2–100 ký tự).</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="receiver_phone">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control<?= $fieldError('receiver_phone') ?>" id="receiver_phone"
                                   name="receiver_phone" value="<?= e($old['receiver_phone']) ?>"
                                   inputmode="numeric" pattern="0[0-9]{9}" maxlength="20" required>
                            <div class="invalid-feedback">Số điện thoại gồm 10 số và bắt đầu bằng 0.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="shipping_address">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control<?= $fieldError('shipping_address') ?>" id="shipping_address"
                                   name="shipping_address" value="<?= e($old['shipping_address']) ?>"
                                   minlength="5" maxlength="255" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành" required>
                            <div class="invalid-feedback">Vui lòng nhập địa chỉ đầy đủ (5–255 ký tự).</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="note">Ghi chú cho người bán</label>
                            <textarea class="form-control<?= $fieldError('note') ?>" id="note" name="note" rows="2"
                                      maxlength="255" placeholder="Ví dụ: giao giờ hành chính, gọi trước khi giao..."><?= e($old['note']) ?></textarea>
                            <div class="invalid-feedback">Ghi chú tối đa 255 ký tự.</div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <fieldset>
                        <legend class="h6 fw-bold mb-3">Phương thức thanh toán</legend>
                        <div class="d-grid gap-2">
                            <label class="payment-option" for="payment-cod">
                                <input class="form-check-input mt-1" type="radio" name="payment_method" id="payment-cod"
                                       value="0" <?= $old['payment_method'] === '0' ? 'checked' : '' ?> required>
                                <span class="payment-text">
                                    <strong class="d-block">Thanh toán khi nhận hàng (COD)</strong>
                                    <span class="small text-secondary">Bạn thanh toán bằng tiền mặt khi nhận được hàng.</span>
                                </span>
                            </label>
                            <label class="payment-option" for="payment-bank">
                                <input class="form-check-input mt-1" type="radio" name="payment_method" id="payment-bank"
                                       value="1" <?= $old['payment_method'] === '1' ? 'checked' : '' ?>>
                                <span class="payment-text">
                                    <strong class="d-block">Chuyển khoản ngân hàng</strong>
                                    <span class="small text-secondary">Chuyển khoản trước, đơn hàng được xử lý sau khi nhận tiền.</span>
                                </span>
                            </label>
                        </div>
                    </fieldset>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="submit" class="btn btn-brand btn-lg">
                            <i class="bi bi-bag-check me-2" aria-hidden="true"></i>Đặt hàng
                        </button>
                        <a class="btn btn-soft btn-lg" href="index.php?controller=cart&amp;action=index">
                            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Sửa giỏ hàng
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="panel">
                <div class="panel-header">
                    <span>Đơn hàng của bạn</span>
                    <span class="badge badge-soft"><?= (int) $cartData['count'] ?> món</span>
                </div>
                <div class="panel-body">
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($cartData['items'] as $item): ?>
                            <li class="d-flex gap-2 py-2 border-bottom">
                                <img src="<?= e(upload_url($item['product']['thumbnail'])) ?>"
                                     alt="<?= e($item['product']['name']) ?>" width="56" height="56"
                                     class="rounded border object-fit-cover" loading="lazy">
                                <div class="flex-grow-1">
                                    <span class="d-block small fw-semibold"><?= e($item['product']['name']) ?></span>
                                    <span class="small text-secondary">
                                        <?= (int) $item['quantity'] ?> × <?= e(format_price($item['price'])) ?>
                                    </span>
                                </div>
                                <span class="small fw-bold text-nowrap"><?= e(format_price($item['line_total'])) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="summary-row mt-3">
                        <span>Tạm tính</span>
                        <span><?= e(format_price($cartData['subtotal'])) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Phí vận chuyển</span>
                        <span class="text-success">Miễn phí</span>
                    </div>
                    <div class="summary-row total">
                        <span>Tổng cộng</span>
                        <span class="price"><?= e(format_price($cartData['subtotal'])) ?></span>
                    </div>

                    <p class="small text-secondary mt-3 mb-0">
                        <i class="bi bi-shield-lock me-1" aria-hidden="true"></i>
                        Thông tin của bạn được bảo mật. Giá và tồn kho được kiểm tra lại khi đặt hàng.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
