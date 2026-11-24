<?php
/** Danh sách sản phẩm – tìm kiếm, lọc danh mục/khoảng giá, sắp xếp, phân trang. */
$hasFilter = ($_GET['q'] ?? '') !== '' || ($_GET['category'] ?? '') !== ''
    || ($_GET['min_price'] ?? '') !== '' || ($_GET['max_price'] ?? '') !== '';
require __DIR__ . '/../partials/header.php';
?>
<section class="page-hero">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sản phẩm</li>
            </ol>
        </nav>
        <h1>
            <?php if ($currentCategory): ?>
                <?= e($currentCategory['name']) ?>
            <?php elseif (($_GET['q'] ?? '') !== ''): ?>
                Kết quả tìm kiếm: &ldquo;<?= e($_GET['q']) ?>&rdquo;
            <?php else: ?>
                Tất cả sản phẩm
            <?php endif; ?>
        </h1>
        <p>Tìm kiếm theo từ khoá, lọc theo danh mục và khoảng giá để chọn sản phẩm phù hợp.</p>
    </div>
</section>

<div class="container mt-4">
    <div class="row g-4">
        <aside class="col-lg-3">
            <div class="filter-panel mb-3">
                <h2 class="h6 text-uppercase text-secondary fw-bold mb-3">Danh mục</h2>
                <div class="category-pills">
                    <a class="pill<?= ($_GET['category'] ?? '') === '' ? ' active' : '' ?>"
                       href="<?= e(query_url(['category' => null, 'page' => null])) ?>">Tất cả</a>
                    <?php foreach ($categories as $category): ?>
                        <a class="pill<?= ($_GET['category'] ?? '') === $category['slug'] ? ' active' : '' ?>"
                           href="<?= e(query_url(['category' => $category['slug'], 'page' => null])) ?>">
                            <?= e($category['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <form class="filter-panel" method="get" action="index.php" role="search">
                <input type="hidden" name="controller" value="product">
                <input type="hidden" name="action" value="index">
                <?php if (($_GET['category'] ?? '') !== ''): ?>
                    <input type="hidden" name="category" value="<?= e($_GET['category']) ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label" for="filter-q">Từ khoá</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" class="form-control" id="filter-q" name="q" maxlength="100"
                               value="<?= e($_GET['q'] ?? '') ?>" placeholder="Tên sản phẩm...">
                    </div>
                </div>

                <div class="mb-3">
                    <span class="form-label d-block">Khoảng giá (đ)</span>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="visually-hidden" for="filter-min">Giá từ</label>
                            <input type="number" class="form-control" id="filter-min" name="min_price" min="0"
                                   step="100000" placeholder="Từ" value="<?= e($_GET['min_price'] ?? '') ?>">
                        </div>
                        <div class="col-6">
                            <label class="visually-hidden" for="filter-max">Giá đến</label>
                            <input type="number" class="form-control" id="filter-max" name="max_price" min="0"
                                   step="100000" placeholder="Đến" value="<?= e($_GET['max_price'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="filter-sort">Sắp xếp</label>
                    <select class="form-select" id="filter-sort" name="sort" data-auto-submit>
                        <option value="newest" <?= ($_GET['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="price_asc" <?= ($_GET['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Giá thấp → cao</option>
                        <option value="price_desc" <?= ($_GET['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Giá cao → thấp</option>
                        <option value="name_asc" <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Tên A → Z</option>
                    </select>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-brand">
                        <i class="bi bi-funnel me-1" aria-hidden="true"></i>Lọc sản phẩm
                    </button>
                    <?php if ($hasFilter): ?>
                        <a class="btn btn-soft" href="index.php?controller=product&amp;action=index">Xoá bộ lọc</a>
                    <?php endif; ?>
                </div>
            </form>
        </aside>

        <div class="col-lg-9">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <p class="mb-0 text-secondary">
                    Hiển thị <strong><?= (int) $result['from'] ?>–<?= (int) $result['to'] ?></strong>
                    trong <strong><?= (int) $result['total'] ?></strong> sản phẩm
                </p>
                <span class="badge badge-soft">
                    <i class="bi bi-shield-check me-1" aria-hidden="true"></i>Bảo hành chính hãng
                </span>
            </div>

            <?php if ($result['items'] === []): ?>
                <div class="panel empty-state">
                    <div class="empty-icon"><i class="bi bi-search" aria-hidden="true"></i></div>
                    <h2 class="h5 fw-bold">Không tìm thấy sản phẩm phù hợp</h2>
                    <p class="text-secondary mb-3">Thử từ khoá khác hoặc bỏ bớt bộ lọc để xem nhiều sản phẩm hơn.</p>
                    <a class="btn btn-brand" href="index.php?controller=product&amp;action=index">Xem tất cả sản phẩm</a>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($result['items'] as $product): ?>
                        <?php
                        $stock = (int) $product['stock'];
                        $productUrl = 'index.php?controller=product&action=show&slug=' . rawurlencode($product['slug']);
                        ?>
                        <div class="col-6 col-xl-4">
                            <article class="product-card">
                                <a class="product-thumb" href="<?= e($productUrl) ?>">
                                    <img src="<?= e(upload_url($product['thumbnail'])) ?>"
                                         alt="<?= e($product['name']) ?>" loading="lazy" width="400" height="300">
                                    <?php if ($product['category_name'] !== null): ?>
                                        <span class="product-badge"><?= e($product['category_name']) ?></span>
                                    <?php endif; ?>
                                    <?php if ($stock <= 0): ?>
                                        <span class="stock-badge text-bg-secondary">Hết hàng</span>
                                    <?php elseif ($stock < 5): ?>
                                        <span class="stock-badge text-bg-warning">Sắp hết</span>
                                    <?php else: ?>
                                        <span class="stock-badge text-bg-success">Còn hàng</span>
                                    <?php endif; ?>
                                </a>
                                <div class="card-body">
                                    <h2 class="product-name">
                                        <a href="<?= e($productUrl) ?>"><?= e($product['name']) ?></a>
                                    </h2>
                                    <p class="product-desc"><?= e(excerpt($product['description'], 80)) ?></p>
                                    <div class="mt-auto d-flex justify-content-between align-items-center gap-2 card-actions">
                                        <span class="price"><?= e(format_price($product['price'])) ?></span>
                                        <?php if (Permission::isMember()): ?>
                                            <form class="js-cart-form" method="post"
                                                  action="index.php?controller=cart&amp;action=add">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-brand btn-sm"
                                                        <?= $stock <= 0 ? 'disabled' : '' ?>>
                                                    <i class="bi bi-cart-plus me-1" aria-hidden="true"></i>Thêm
                                                </button>
                                            </form>
                                        <?php elseif (!Permission::isLoggedIn()): ?>
                                            <button type="button" class="btn btn-brand btn-sm"
                                                    <?= $stock <= 0 ? 'disabled' : '' ?>
                                                    data-require-login="Bạn chưa đăng nhập nên không thể đặt hàng. Vui lòng đăng nhập bằng tài khoản Thành viên (Khách hàng) để mua sản phẩm."
                                                    data-login-redirect="<?= e($productUrl) ?>">
                                                <i class="bi bi-cart-plus me-1" aria-hidden="true"></i>Thêm
                                            </button>
                                        <?php else: ?>
                                            <a class="btn btn-soft btn-sm" href="<?= e($productUrl) ?>">Chi tiết</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?= render_pagination($result) ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
