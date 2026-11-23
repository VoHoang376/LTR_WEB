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
                    <?php $itemUrl = 'index.php?controller=product&amp;action=show&amp;slug=' . rawurlencode($item['slug']); ?>
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
