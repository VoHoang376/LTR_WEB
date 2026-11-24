<?php
/** Chi tiết sản phẩm: gallery ảnh, giá, tồn kho, thêm vào giỏ, sản phẩm liên quan. */
$galleryImages = $images;
$mainImage = $product['thumbnail'];
$ogImage = upload_url($product['thumbnail']);
$specLines = [];

if (!empty($product['specifications'])) {
    foreach (preg_split('/\r\n|\r|\n/', $product['specifications']) as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        $parts = explode(':', $line, 2);
        $specLines[] = [
            'label' => trim($parts[0]),
            'value' => isset($parts[1]) ? trim($parts[1]) : '',
        ];
    }
}

require __DIR__ . '/../partials/header.php';
?>
<div class="page-hero py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="index.php?controller=product&amp;action=index">Sản phẩm</a></li>
                <?php if ($product['category_name'] !== null): ?>
                    <li class="breadcrumb-item">
                        <a href="<?= e(query_url(['controller' => 'product', 'action' => 'index', 'category' => $product['category_slug'], 'page' => null])) ?>">
                            <?= e($product['category_name']) ?>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page"><?= e($product['name']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mt-4">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="detail-gallery-main">
                <img id="gallery-main" src="<?= e(upload_url($mainImage)) ?>"
                     alt="<?= e($product['name']) ?>" width="800" height="600">
            </div>
            <?php if ($galleryImages !== []): ?>
                <div class="gallery-thumbs" role="group" aria-label="Thư viện ảnh sản phẩm">
                    <button type="button" class="active" data-gallery-src="<?= e(upload_url($mainImage)) ?>">
                        <img src="<?= e(upload_url($mainImage)) ?>" alt="Ảnh chính" loading="lazy" width="140" height="140">
                    </button>
                    <?php foreach ($galleryImages as $image): ?>
                        <button type="button" data-gallery-src="<?= e(upload_url($image['image'])) ?>">
                            <img src="<?= e(upload_url($image['image'])) ?>" alt="Ảnh sản phẩm <?= (int) $image['id'] ?>"
                                 loading="lazy" width="140" height="140">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-6">
            <div class="detail-summary">
                <?php if ($product['category_name'] !== null): ?>
                    <span class="badge badge-soft mb-2"><?= e($product['category_name']) ?></span>
                <?php endif; ?>
                <h1 class="h3 fw-bold mb-2"><?= e($product['name']) ?></h1>

                <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                    <span class="detail-price"><?= e(format_price($product['price'])) ?></span>
                    <?php if ($stock <= 0): ?>
                        <span class="badge text-bg-secondary">Hết hàng</span>
                    <?php elseif ($stock <= 5): ?>
                        <span class="badge text-bg-warning">Chỉ còn <?= $stock ?> sản phẩm</span>
                    <?php else: ?>
                        <span class="badge text-bg-success">Còn <?= $stock ?> sản phẩm</span>
                    <?php endif; ?>
                </div>

                <p class="text-secondary mb-0"><?= e(excerpt($product['description'], 240)) ?></p>

                <div class="detail-meta">
                    <span><i class="bi bi-upc-scan me-1" aria-hidden="true"></i>Mã: SP-<?= str_pad((string) $product['id'], 4, '0', STR_PAD_LEFT) ?></span>
                    <span><i class="bi bi-shield-check me-1" aria-hidden="true"></i>Bảo hành chính hãng</span>
                    <span><i class="bi bi-truck me-1" aria-hidden="true"></i>Giao hàng toàn quốc</span>
                    <span><i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i>Đổi trả 7 ngày</span>
                </div>

                <?php $currentProductUrl = 'index.php?controller=product&action=show&slug=' . rawurlencode($product['slug']); ?>
                <?php if (Permission::isMember()): ?>
                    <form id="add-to-cart-form" class="js-cart-form" method="post"
                          action="index.php?controller=cart&amp;action=add">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">

                        <div class="d-flex flex-wrap align-items-end gap-3 mb-3">
                            <div>
                                <label class="form-label small fw-bold text-uppercase text-secondary"
                                       for="quantity">Số lượng</label>
                                <div class="input-group qty-group" style="max-width: 160px;">
                                    <button class="btn btn-outline-secondary" type="button" data-qty-step="-1"
                                            aria-label="Giảm số lượng">−</button>
                                    <input type="number" class="form-control text-center" id="quantity" name="quantity"
                                           value="<?= max(1, $quantityInCart) ?>" min="1"
                                           max="<?= max(1, min(99, $stock)) ?>" data-max="<?= $stock ?>"
                                           <?= $stock <= 0 ? 'disabled' : '' ?>>
                                    <button class="btn btn-outline-secondary" type="button" data-qty-step="1"
                                            aria-label="Tăng số lượng">+</button>
                                </div>
                            </div>
                            <p class="mb-2 small text-secondary">
                                <?php if ($quantityInCart > 0): ?>
                                    Bạn đang có <strong><?= (int) $quantityInCart ?></strong> sản phẩm này trong giỏ.
                                <?php else: ?>
                                    Chọn số lượng rồi thêm vào giỏ hàng.
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-brand btn-lg" <?= $stock <= 0 ? 'disabled' : '' ?>>
                                <i class="bi bi-cart-plus me-2" aria-hidden="true"></i>Thêm vào giỏ hàng
                            </button>
                            <a class="btn btn-soft btn-lg" href="index.php?controller=cart&amp;action=index">
                                <i class="bi bi-cart3 me-2" aria-hidden="true"></i>Xem giỏ hàng
                            </a>
                        </div>
                    </form>
                <?php elseif (!Permission::isLoggedIn()): ?>
                    <div class="alert alert-warning border-warning-subtle small mb-3" role="status">
                        <i class="bi bi-shield-lock me-1" aria-hidden="true"></i>
                        <strong>Chưa đăng nhập:</strong> Vui lòng đăng nhập bằng <strong>tài khoản Thành viên</strong> để thêm vào giỏ hàng và đặt mua sản phẩm.
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-brand btn-lg" <?= $stock <= 0 ? 'disabled' : '' ?>
                                data-require-login="Bạn chưa đăng nhập nên không thể đặt hàng. Vui lòng đăng nhập bằng tài khoản Thành viên (Khách hàng) để thêm vào giỏ hàng và đặt mua sản phẩm."
                                data-login-redirect="<?= e($currentProductUrl) ?>">
                            <i class="bi bi-cart-plus me-2" aria-hidden="true"></i>Thêm vào giỏ hàng
                        </button>
                        <a class="btn btn-soft btn-lg" href="index.php?controller=auth&amp;action=login&amp;redirect=<?= rawurlencode($currentProductUrl) ?>">
                            <i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i>Đăng nhập để đặt hàng
                        </a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light border small mb-0">
                        <i class="bi bi-shield-lock me-1 text-primary" aria-hidden="true"></i>
                        Bạn đang đăng nhập bằng tài khoản <strong><?= e(Permission::roleLabel(Permission::role())) ?></strong>
                        (chế độ quản lý / hỗ trợ — không thực hiện đặt mua hàng).
                        <?php if (Permission::isAdmin()): ?>
                            <a class="ms-1 fw-semibold" href="index.php?controller=admin&amp;action=product-edit&amp;id=<?= (int) $product['id'] ?>">Sửa sản phẩm này &rarr;</a>
                        <?php elseif (Permission::isStaff()): ?>
                            <a class="ms-1 fw-semibold text-success" href="index.php?controller=staff&amp;action=reviews">Trả lời phản hồi sản phẩm &rarr;</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <ul class="list-unstyled small text-secondary mt-4 mb-0">
                    <li class="mb-1"><i class="bi bi-check2-circle me-2 text-success" aria-hidden="true"></i>Sản phẩm chính hãng, có hoá đơn VAT</li>
                    <li class="mb-1"><i class="bi bi-check2-circle me-2 text-success" aria-hidden="true"></i>Hỗ trợ kỹ thuật và cài đặt tận nơi</li>
                    <li><i class="bi bi-check2-circle me-2 text-success" aria-hidden="true"></i>Thanh toán COD hoặc chuyển khoản</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-lg-8">
            <div class="panel">
                <div class="panel-header">Mô tả sản phẩm</div>
                <div class="panel-body">
                    <?php if (!empty($product['description'])): ?>
                        <p class="mb-0" style="white-space: pre-line;"><?= e($product['description']) ?></p>
                    <?php else: ?>
                        <p class="text-secondary mb-0">Sản phẩm đang được cập nhật mô tả.</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($specLines !== []): ?>
                <div class="panel mt-4">
                    <div class="panel-header">Thông số kỹ thuật</div>
                    <div class="table-responsive">
                        <table class="table spec-table mb-0">
                            <caption class="visually-hidden">Thông số kỹ thuật của <?= e($product['name']) ?></caption>
                            <tbody>
                            <?php foreach ($specLines as $spec): ?>
                                <tr>
                                    <th scope="row"><?= e($spec['label']) ?></th>
                                    <td><?= e($spec['value']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Đánh giá & Phản hồi sản phẩm -->
            <section id="reviews" class="panel mt-4">
                <div class="panel-header">
                    <span><i class="bi bi-star-half text-warning me-2" aria-hidden="true"></i>Đánh giá &amp; Phản hồi sản phẩm (<?= (int) ($ratingSummary['total'] ?? 0) ?>)</span>
                    <?php if (!empty($ratingSummary['total'])): ?>
                        <span class="badge text-bg-warning"><?= number_format((float) $ratingSummary['avg'], 1) ?> / 5 ★</span>
                    <?php endif; ?>
                </div>
                <div class="panel-body">
                    <?php if (!empty($reviewFlash)): ?>
                        <div class="alert alert-<?= e($reviewFlash['type']) ?>" role="status">
                            <?= e($reviewFlash['message']) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (Permission::isMember()): ?>
                        <form method="post" action="index.php?controller=product&amp;action=review"
                              class="js-validate needs-validation border rounded-3 p-3 bg-light-subtle mb-4" novalidate>
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                            <div class="row g-2 align-items-center mb-2">
                                <div class="col-auto">
                                    <label class="form-label mb-0 small fw-semibold" for="review-rating">Đánh giá của bạn:</label>
                                </div>
                                <div class="col-auto">
                                    <select id="review-rating" name="rating" class="form-select form-select-sm" required>
                                        <option value="5" selected>★★★★★ (5 sao – Rất hài lòng)</option>
                                        <option value="4">★★★★☆ (4 sao – Hài lòng)</option>
                                        <option value="3">★★★☆☆ (3 sao – Bình thường)</option>
                                        <option value="2">★★☆☆☆ (2 sao – Chưa tốt)</option>
                                        <option value="1">★☆☆☆☆ (1 sao – Không hài lòng)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="visually-hidden" for="review-comment">Nội dung phản hồi sản phẩm</label>
                                <textarea id="review-comment" name="comment" rows="3" class="form-control"
                                          minlength="5" maxlength="2000" required
                                          placeholder="Chia sẻ trải nghiệm sử dụng hoặc đặt câu hỏi về sản phẩm này..."></textarea>
                                <div class="invalid-feedback">Vui lòng nhập nội dung đánh giá (5–2.000 ký tự).</div>
                            </div>
                            <button type="submit" class="btn btn-brand btn-sm">
                                <i class="bi bi-send me-1" aria-hidden="true"></i>Gửi đánh giá / phản hồi
                            </button>
                        </form>
                    <?php elseif (!Permission::isLoggedIn()): ?>
                        <div class="alert alert-warning border-warning-subtle d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 small" role="status">
                            <span>
                                <i class="bi bi-shield-lock me-1" aria-hidden="true"></i>
                                <strong>Chưa đăng nhập:</strong> Bạn không thể viết đánh giá hay phản hồi sản phẩm. Vui lòng đăng nhập bằng tài khoản Thành viên.
                            </span>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm"
                                        data-require-login="Bạn chưa đăng nhập nên không thể viết đánh giá sản phẩm. Vui lòng đăng nhập bằng tài khoản Thành viên (Khách hàng)."
                                        data-login-redirect="<?= e($currentProductUrl . '#reviews') ?>">
                                    Viết đánh giá
                                </button>
                                <a href="index.php?controller=auth&amp;action=login&amp;redirect=<?= rawurlencode($currentProductUrl . '#reviews') ?>"
                                   class="btn btn-brand btn-sm">Đăng nhập ngay</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($reviews)): ?>
                        <p class="text-secondary small mb-0">Chưa có đánh giá nào cho sản phẩm này.</p>
                    <?php else: ?>
                        <div class="vstack gap-3">
                            <?php foreach ($reviews as $review): ?>
                                <div class="border rounded-3 p-3">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
                                        <div>
                                            <strong class="small"><?= e($review['author_name']) ?></strong>
                                            <span class="text-warning ms-2 small"><?= str_repeat('★', (int) $review['rating']) . str_repeat('☆', 5 - (int) $review['rating']) ?></span>
                                        </div>
                                        <span class="text-secondary small"><?= e(format_datetime($review['created_at'])) ?></span>
                                    </div>
                                    <p class="mb-0 small" style="white-space: pre-line;"><?= e($review['comment']) ?></p>

                                    <?php if (!empty($review['staff_reply'])): ?>
                                        <div class="mt-2 ms-3 p-2.5 rounded-3 border-start border-3 border-success bg-success-subtle small">
                                            <div class="fw-semibold text-success mb-1">
                                                <i class="bi bi-headset me-1" aria-hidden="true"></i>
                                                Phản hồi từ Nhân viên CSKH<?= !empty($review['staff_name']) ? ' (' . e($review['staff_name']) . ')' : '' ?>
                                                <?php if (!empty($review['replied_at'])): ?>
                                                    <span class="fw-normal text-secondary ms-1">· <?= e(format_datetime($review['replied_at'])) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="mb-0 text-dark" style="white-space: pre-line;"><?= e($review['staff_reply']) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-header">Chính sách bán hàng</div>
                <div class="panel-body">
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2"><i class="bi bi-truck me-2 text-primary" aria-hidden="true"></i>Miễn phí giao hàng nội thành Hà Nội</li>
                        <li class="mb-2"><i class="bi bi-arrow-repeat me-2 text-primary" aria-hidden="true"></i>Đổi trả trong 7 ngày nếu lỗi nhà sản xuất</li>
                        <li class="mb-2"><i class="bi bi-cash-coin me-2 text-primary" aria-hidden="true"></i>Thanh toán khi nhận hàng (COD)</li>
                        <li class="mb-2"><i class="bi bi-bank me-2 text-primary" aria-hidden="true"></i>Chuyển khoản ngân hàng</li>
                        <li><i class="bi bi-telephone me-2 text-primary" aria-hidden="true"></i>Hotline tư vấn: 1900 6868</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php if ($related !== []): ?>
        <section class="mt-5">
            <h2 class="section-title">Sản phẩm cùng danh mục</h2>
            <div class="row g-3">
                <?php foreach ($related as $item): ?>
                    <?php $itemUrl = 'index.php?controller=product&action=show&slug=' . rawurlencode($item['slug']); ?>
                    <div class="col-6 col-md-3">
                        <article class="product-card">
                            <a class="product-thumb" href="<?= e($itemUrl) ?>">
                                <img src="<?= e(upload_url($item['thumbnail'])) ?>" alt="<?= e($item['name']) ?>"
                                     loading="lazy" width="400" height="300">
                            </a>
                            <div class="card-body">
                                <h3 class="product-name"><a href="<?= e($itemUrl) ?>"><?= e($item['name']) ?></a></h3>
                                <div class="mt-auto d-flex justify-content-between align-items-center gap-2 card-actions">
                                    <span class="price"><?= e(format_price($item['price'])) ?></span>
                                    <a class="btn btn-soft btn-sm" href="<?= e($itemUrl) ?>">Xem</a>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org/',
    '@type' => 'Product',
    'name' => $product['name'],
    'image' => [upload_url($mainImage)],
    'description' => excerpt($product['description'], 300),
    'sku' => 'SP-' . str_pad((string) $product['id'], 4, '0', STR_PAD_LEFT),
    'brand' => ['@type' => 'Brand', 'name' => site_name()],
    'offers' => [
        '@type' => 'Offer',
        'priceCurrency' => 'VND',
        'price' => number_format((float) $product['price'], 0, '.', ''),
        'availability' => $stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        'itemCondition' => 'https://schema.org/NewCondition',
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?>
</script>

<script>
/* Đổi ảnh lớn khi bấm ảnh nhỏ trong thư viện ảnh. */
document.querySelectorAll('.gallery-thumbs button').forEach(function (button) {
    button.addEventListener('click', function () {
        var main = document.getElementById('gallery-main');
        if (main) {
            main.src = button.getAttribute('data-gallery-src');
        }
        document.querySelectorAll('.gallery-thumbs button').forEach(function (item) {
            item.classList.remove('active');
        });
        button.classList.add('active');
    });
});
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
