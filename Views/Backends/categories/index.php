<?php
/** Quản trị – danh sách danh mục sản phẩm. */
require __DIR__ . '/../layouts/header.php';
?>
<div class="admin-card">
    <div class="card-head">
        <div>
            <h2>Danh mục sản phẩm</h2>
            <p class="small text-secondary mb-0">Tổng <?= count($categories) ?> danh mục</p>
        </div>
        <a class="btn btn-primary btn-sm" href="index.php?controller=admin&amp;action=category-create">
            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Thêm danh mục
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-admin align-middle">
            <caption class="visually-hidden">Danh sách danh mục sản phẩm</caption>
            <thead>
            <tr>
                <th scope="col" style="width: 70px;">#</th>
                <th scope="col">Tên danh mục</th>
                <th scope="col">Slug</th>
                <th scope="col" class="text-center">Sản phẩm</th>
                <th scope="col" class="text-center">Trạng thái</th>
                <th scope="col" class="text-end">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($categories === []): ?>
                <tr>
                    <td colspan="6">
                        <div class="table-empty">
                            <i class="bi bi-tags fs-3 d-block mb-2" aria-hidden="true"></i>
                            Chưa có danh mục nào. Hãy thêm danh mục đầu tiên.
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($categories as $index => $category): ?>
                    <tr>
                        <td class="text-secondary"><?= $index + 1 ?></td>
                        <td class="fw-semibold"><?= e($category['name']) ?></td>
                        <td class="text-secondary small">/<?= e($category['slug']) ?></td>
                        <td class="text-center">
                            <a class="badge text-bg-light border text-decoration-none"
                               href="index.php?controller=admin&amp;action=products&amp;category=<?= (int) $category['id'] ?>">
                                <?= (int) $category['product_count'] ?>
                            </a>
                        </td>
                        <td class="text-center">
                            <?php if ((int) $category['status'] === 1): ?>
                                <span class="status-pill text-bg-success">Hiển thị</span>
                            <?php else: ?>
                                <span class="status-pill text-bg-secondary">Đang ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="index.php?controller=admin&amp;action=category-edit&amp;id=<?= (int) $category['id'] ?>"
                                   title="Sửa danh mục">
                                    <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    <span class="visually-hidden">Sửa</span>
                                </a>
                                <?php if ((int) $category['product_count'] === 0): ?>
                                    <form method="post" action="index.php?controller=admin&amp;action=category-delete"
                                          data-confirm="Xoá danh mục &quot;<?= e($category['name']) ?>&quot;?">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xoá danh mục">
                                            <i class="bi bi-trash3" aria-hidden="true"></i>
                                            <span class="visually-hidden">Xoá</span>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled
                                            title="Danh mục còn sản phẩm, không thể xoá">
                                        <i class="bi bi-lock" aria-hidden="true"></i>
                                        <span class="visually-hidden">Không thể xoá</span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
