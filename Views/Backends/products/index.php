<?php
/** Quản trị – danh sách sản phẩm: tìm kiếm, lọc, phân trang, thao tác nhanh. */
require __DIR__ . '/../layouts/header.php';
?>
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <span class="stat-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
            <div>
                <div class="stat-value"><?= (int) ($stats['total'] ?? 0) ?></div>
                <div class="stat-label">Tổng sản phẩm</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <span class="stat-icon text-success bg-success-subtle"><i class="bi bi-check2-circle" aria-hidden="true"></i></span>
            <div>
                <div class="stat-value"><?= (int) ($stats['selling'] ?? 0) ?></div>
                <div class="stat-label">Đang bán</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <span class="stat-icon text-danger bg-danger-subtle"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i></span>
            <div>
                <div class="stat-value"><?= (int) ($stats['out_of_stock'] ?? 0) ?></div>
                <div class="stat-label">Hết hàng</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <span class="stat-icon"><i class="bi bi-tags" aria-hidden="true"></i></span>
            <div>
                <div class="stat-value"><?= count($categories) ?></div>
                <div class="stat-label">Danh mục</div>
            </div>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="card-head">
        <div>
            <h2>Danh sách sản phẩm</h2>
            <p class="small text-secondary mb-0">
                Hiển thị <?= (int) $result['from'] ?>–<?= (int) $result['to'] ?> trong <?= (int) $result['total'] ?> sản phẩm
            </p>
        </div>
        <a class="btn btn-primary btn-sm" href="index.php?controller=admin&amp;action=product-create">
            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Thêm sản phẩm
        </a>
    </div>

    <div class="card-body border-bottom">
        <form class="filter-bar" method="get" action="index.php" role="search">
            <input type="hidden" name="controller" value="admin">
            <input type="hidden" name="action" value="products">
            <div>
                <label class="form-label" for="admin-q">Từ khoá</label>
                <input type="search" class="form-control form-control-sm" id="admin-q" name="q"
                       value="<?= e($_GET['q'] ?? '') ?>" placeholder="Tên sản phẩm...">
            </div>
            <div>
                <label class="form-label" for="admin-category">Danh mục</label>
                <select class="form-select form-select-sm" id="admin-category" name="category">
                    <option value="">Tất cả danh mục</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int) $category['id'] ?>"
                            <?= (string) ($_GET['category'] ?? '') === (string) $category['id'] ? 'selected' : '' ?>>
                            <?= e($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" for="admin-status">Trạng thái</label>
                <select class="form-select form-select-sm" id="admin-status" name="status">
                    <option value="">Tất cả</option>
                    <option value="1" <?= ($_GET['status'] ?? '') === '1' ? 'selected' : '' ?>>Đang bán</option>
                    <option value="0" <?= ($_GET['status'] ?? '') === '0' ? 'selected' : '' ?>>Ngừng kinh doanh</option>
                </select>
            </div>
            <div>
                <label class="form-label" for="admin-sort">Sắp xếp</label>
                <select class="form-select form-select-sm" id="admin-sort" name="sort">
                    <option value="newest" <?= ($_GET['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                    <option value="price_asc" <?= ($_GET['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                    <option value="price_desc" <?= ($_GET['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                    <option value="name_asc" <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Tên A–Z</option>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search me-1" aria-hidden="true"></i>Tìm
                </button>
                <a class="btn btn-outline-secondary btn-sm" href="index.php?controller=admin&amp;action=products">Xoá lọc</a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-admin align-middle">
            <caption class="visually-hidden">Danh sách sản phẩm trong hệ thống</caption>
            <thead>
            <tr>
                <th scope="col">Sản phẩm</th>
                <th scope="col">Danh mục</th>
                <th scope="col" class="text-end">Giá bán</th>
                <th scope="col" class="text-center">Tồn kho</th>
                <th scope="col" class="text-center">Trạng thái</th>
                <th scope="col">Ngày tạo</th>
                <th scope="col" class="text-end">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($result['items'] === []): ?>
                <tr>
                    <td colspan="7">
                        <div class="table-empty">
                            <i class="bi bi-inbox fs-3 d-block mb-2" aria-hidden="true"></i>
                            Không tìm thấy sản phẩm nào phù hợp.
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($result['items'] as $product): ?>
                    <?php $stock = (int) $product['stock']; ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="thumb-cell">
                                    <img src="<?= e(upload_url($product['thumbnail'])) ?>"
                                         alt="<?= e($product['name']) ?>" loading="lazy" width="52" height="52">
                                </span>
                                <div>
                                    <span class="fw-semibold d-block"><?= e($product['name']) ?></span>
                                    <span class="small text-secondary">/<?= e($product['slug']) ?></span>
                                </div>
                            </div>
                        </td>
                        <td><?= e($product['category_name'] ?? '—') ?></td>
                        <td class="text-end fw-semibold text-nowrap"><?= e(format_price($product['price'])) ?></td>
                        <td class="text-center">
                            <?php if ($stock <= 0): ?>
                                <span class="status-pill text-bg-danger">Hết hàng</span>
                            <?php elseif ($stock < 5): ?>
                                <span class="status-pill text-bg-warning"><?= $stock ?> (sắp hết)</span>
                            <?php else: ?>
                                <span class="status-pill text-bg-light border"><?= $stock ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ((int) $product['status'] === 1): ?>
                                <span class="status-pill text-bg-success">Đang bán</span>
                            <?php else: ?>
                                <span class="status-pill text-bg-secondary">Ngừng bán</span>
                            <?php endif; ?>
                        </td>
                        <td class="small text-secondary text-nowrap"><?= e(format_datetime($product['created_at'])) ?></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="index.php?controller=admin&amp;action=product-edit&amp;id=<?= (int) $product['id'] ?>"
                                   title="Sửa sản phẩm">
                                    <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    <span class="visually-hidden">Sửa</span>
                                </a>
                                <form method="post" action="index.php?controller=admin&amp;action=product-delete"
                                      data-confirm="Xoá sản phẩm &quot;<?= e($product['name']) ?>&quot;?">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xoá sản phẩm">
                                        <i class="bi bi-trash3" aria-hidden="true"></i>
                                        <span class="visually-hidden">Xoá</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($result['totalPages'] > 1): ?>
        <div class="card-body">
            <?= render_pagination($result) ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
