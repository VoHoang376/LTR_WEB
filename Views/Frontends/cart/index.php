<?php
/** Giỏ hàng: danh sách sản phẩm, sửa số lượng (AJAX), xoá, tạm tính. */
require __DIR__ . '/../partials/header.php';
?>
<div class="page-hero py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php?controller=product&amp;action=index">Sản phẩm</a></li>
                <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="h3 fw-bold mb-0">Giỏ hàng của bạn</h1>
        <span class="text-secondary">
            <?= (int) $cart['distinct'] ?> sản phẩm · <?= (int) $cart['count'] ?> món
        </span>
    </div>

    <?php if ($cart['distinct'] === 0): ?>
        <div class="panel empty-state">
            <div class="empty-icon"><i class="bi bi-cart-x" aria-hidden="true"></i></div>
            <h2 class="h5 fw-bold">Giỏ hàng đang trống</h2>
            <p class="text-secondary mb-3">Hãy chọn vài sản phẩm phù hợp rồi quay lại thanh toán nhé.</p>
            <a class="btn btn-brand" href="index.php?controller=product&amp;action=index">
                <i class="bi bi-bag me-2" aria-hidden="true"></i>Xem sản phẩm
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="panel" data-cart-table>
                    <div class="panel-header">
                        <span>Sản phẩm trong giỏ</span>
                        <form class="js-cart-form" method="post" action="index.php?controller=cart&amp;action=clear"
                              data-confirm="Bạn chắc chắn muốn xoá toàn bộ giỏ hàng?">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash3 me-1" aria-hidden="true"></i>Xoá tất cả
                            </button>
                        </form>
                    </div>
                    <div class="panel-body py-1">
                        <?php foreach ($cart['items'] as $item): ?>
                            <?php
                            $product = $item['product'];
                            $productUrl = 'index.php?controller=product&action=show&slug=' . rawurlencode($product['slug']);
                            $maxQuantity = max(1, min(99, (int) $product['stock']));
                            ?>
                            <div class="cart-item" data-cart-row="<?= (int) $product['id'] ?>">
                                <a class="cart-item-thumb" href="<?= e($productUrl) ?>">
                                    <img src="<?= e(upload_url($product['thumbnail'])) ?>"
                                         alt="<?= e($product['name']) ?>" loading="lazy" width="96" height="96">
                                </a>

                                <div>
                                    <a class="cart-item-name" href="<?= e($productUrl) ?>"><?= e($product['name']) ?></a>
                                    <p class="small text-secondary mb-1">
                                        <?= e($product['category_name'] ?? 'Sản phẩm') ?>
                                    </p>
                                    <p class="small mb-1">
                                        Đơn giá: <span class="price"><?= e(format_price($item['price'])) ?></span>
                                    </p>
                                    <?php if (!$item['in_stock']): ?>
                                        <p class="small text-danger mb-0">
                                            <i class="bi bi-exclamation-triangle me-1" aria-hidden="true"></i>
                                            Chỉ còn <?= (int) $product['stock'] ?> sản phẩm, vui lòng giảm số lượng.
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <div class="cart-item-actions d-flex flex-column align-items-lg-end gap-2">
                                    <div class="fw-bold" data-line-total><?= e(format_price($item['line_total'])) ?></div>

                                    <form class="js-cart-form js-cart-auto" method="post"
                                          action="index.php?controller=cart&amp;action=update">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                        <div class="input-group input-group-sm qty-group" style="max-width: 150px;">
                                            <button class="btn btn-outline-secondary" type="button" data-qty-step="-1"
                                                    aria-label="Giảm số lượng">−</button>
                                            <label class="visually-hidden" for="qty-<?= (int) $product['id'] ?>">Số lượng</label>
                                            <input type="number" class="form-control text-center" id="qty-<?= (int) $product['id'] ?>"
                                                   name="quantity" value="<?= (int) $item['quantity'] ?>" min="1"
                                                   max="<?= $maxQuantity ?>" data-max="<?= (int) $product['stock'] ?>">
                                            <button class="btn btn-outline-secondary" type="button" data-qty-step="1"
                                                    aria-label="Tăng số lượng">+</button>
                                        </div>
                                        <noscript>
                                            <button type="submit" class="btn btn-sm btn-soft mt-1">Cập nhật</button>
                                        </noscript>
                                    </form>

                                    <form class="js-cart-form" method="post"
                                          action="index.php?controller=cart&amp;action=remove"
                                          data-confirm="Xoá sản phẩm này khỏi giỏ hàng?">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                        <button type="submit" class="btn btn-link btn-sm text-danger p-0">
                                            <i class="bi bi-trash3 me-1" aria-hidden="true"></i>Xoá
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="panel empty-state d-none mt-4" data-cart-empty>
                    <div class="empty-icon"><i class="bi bi-cart-x" aria-hidden="true"></i></div>
                    <h2 class="h5 fw-bold">Giỏ hàng đang trống</h2>
                    <p class="text-secondary mb-3">Bạn đã xoá hết sản phẩm khỏi giỏ.</p>
                    <a class="btn btn-brand" href="index.php?controller=product&amp;action=index">Xem sản phẩm</a>
                </div>

                <div class="mt-3">
                    <a class="btn btn-soft" href="index.php?controller=product&amp;action=index">
                        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Tiếp tục mua sắm
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="panel">
                    <div class="panel-header">Tóm tắt đơn hàng</div>
                    <div class="panel-body">
                        <div class="summary-row">
                            <span>Tạm tính</span>
                            <span data-cart-subtotal><?= e(format_price($cart['subtotal'])) ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Phí vận chuyển</span>
                            <span class="text-success">Miễn phí</span>
                        </div>
                        <div class="summary-row">
                            <span>Thanh toán</span>
                            <span class="text-secondary">COD / Chuyển khoản</span>
                        </div>
                        <div class="summary-row total">
                            <span>Tổng cộng</span>
                            <span class="price" data-cart-subtotal><?= e(format_price($cart['subtotal'])) ?></span>
                        </div>

                        <?php if (Permission::isMember()): ?>
                            <a class="btn btn-brand w-100 mt-3"
                               href="index.php?controller=checkout&amp;action=index">
                                <i class="bi bi-credit-card me-2" aria-hidden="true"></i>Tiến hành thanh toán
                            </a>
                        <?php else: ?>
                            <a class="btn btn-brand w-100 mt-3" href="index.php?controller=checkout&amp;action=index">
                                <i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i>Đăng nhập để đặt hàng
                            </a>
                            <p class="small text-secondary mt-2 mb-0">
                                Bạn cần đăng nhập bằng <strong>tài khoản Thành viên</strong> để tiến hành đặt hàng và theo dõi đơn.
                                Chưa có tài khoản? <a href="index.php?controller=auth&amp;action=register">Đăng ký ngay</a>.
                            </p>
                        <?php endif; ?>

                        <p class="small text-secondary mt-3 mb-0">
                            <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                            Giá sản phẩm được kiểm tra lại khi đặt hàng, đảm bảo đúng giá niêm yết.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
