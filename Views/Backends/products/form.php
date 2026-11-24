<?php
/** Quản trị – form thêm/sửa sản phẩm. */
require __DIR__ . '/../layouts/header.php';

$isEdit = $product !== null;
$formAction = $isEdit
    ? 'index.php?controller=admin&amp;action=product-edit'
    : 'index.php?controller=admin&amp;action=product-create';
$thumbnail = $old['thumbnail'] ?? ($product['thumbnail'] ?? 'no-image.svg');
?>
<?php if (isset($errors['form'])): ?>
    <div class="alert alert-danger" role="alert">
        <i class="bi bi-exclamation-triangle me-1" aria-hidden="true"></i><?= e($errors['form']) ?>
    </div>
<?php endif; ?>

<form id="productForm" class="js-validate needs-validation" method="post" enctype="multipart/form-data"
      action="<?= $formAction ?>" novalidate>
    <?= csrf_field() ?>
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="admin-card">
                <div class="card-head">
                    <h2>Thông tin sản phẩm</h2>
                    <?php if ($isEdit): ?>
                        <a class="btn btn-sm btn-outline-secondary"
                           href="index.php?controller=product&amp;action=show&amp;slug=<?= rawurlencode($product['slug']) ?>"
                           target="_blank" rel="noopener">
                            <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>Xem trên website
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="name">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>"
                                   id="name" name="name" value="<?= e($old['name']) ?>"
                                   minlength="2" maxlength="255" required data-slug-source>
                            <div class="invalid-feedback">Tên sản phẩm phải có từ 2 đến 255 ký tự.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="slug">Đường dẫn (slug)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light small">/san-pham/</span>
                                <input type="text" class="form-control<?= isset($errors['slug']) ? ' is-invalid' : '' ?>"
                                       id="slug" name="slug" value="<?= e($old['slug']) ?>" maxlength="255"
                                       data-slug-target placeholder="tu-dong-sinh-tu-ten">
                            </div>
                            <div class="form-text<?= isset($errors['slug']) ? ' text-danger' : '' ?>">
                                <?= isset($errors['slug']) ? e($errors['slug']) : 'Bỏ trống để tự sinh từ tên sản phẩm.' ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="category_id">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select<?= isset($errors['category_id']) ? ' is-invalid' : '' ?>"
                                    id="category_id" name="category_id" required>
                                <option value="">— Chọn danh mục —</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int) $category['id'] ?>"
                                        <?= (string) $old['category_id'] === (string) $category['id'] ? 'selected' : '' ?>>
                                        <?= e($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Vui lòng chọn danh mục cho sản phẩm.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="status">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="1" <?= (string) $old['status'] === '1' ? 'selected' : '' ?>>Đang bán (hiển thị)</option>
                                <option value="0" <?= (string) $old['status'] === '0' ? 'selected' : '' ?>>Ngừng kinh doanh (ẩn)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="price">Giá bán (đ) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control<?= isset($errors['price']) ? ' is-invalid' : '' ?>"
                                   id="price" name="price"
                                   value="<?= e(is_numeric($old['price']) ? number_format((float) $old['price'], 0, ',', '.') : $old['price']) ?>"
                                   inputmode="numeric" pattern="[0-9.,\s]+" required data-price-input>
                            <div class="invalid-feedback">
                                <?= isset($errors['price']) ? e($errors['price']) : 'Chỉ nhập số, ví dụ 5290000.' ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="stock">Tồn kho <span class="text-danger">*</span></label>
                            <input type="number" class="form-control<?= isset($errors['stock']) ? ' is-invalid' : '' ?>"
                                   id="stock" name="stock" value="<?= e((string) $old['stock']) ?>"
                                   min="0" max="1000000" step="1" required>
                            <div class="invalid-feedback">
                                <?= isset($errors['stock']) ? e($errors['stock']) : 'Số nguyên từ 0 đến 1.000.000.' ?>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="description">Mô tả</label>
                            <textarea class="form-control<?= isset($errors['description']) ? ' is-invalid' : '' ?>"
                                      id="description" name="description" rows="5" maxlength="5000"><?= e($old['description']) ?></textarea>
                            <div class="invalid-feedback">Mô tả tối đa 5.000 ký tự.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="specifications">Thông số kỹ thuật</label>
                            <textarea class="form-control<?= isset($errors['specifications']) ? ' is-invalid' : '' ?>"
                                      id="specifications" name="specifications" rows="5" maxlength="5000"
                                      placeholder="Mỗi dòng một thông số, dạng Nhãn: Giá trị&#10;Ví dụ: RAM: 16GB DDR4"><?= e($old['specifications']) ?></textarea>
                            <div class="form-text">Mỗi dòng một thông số, dạng <code>Nhãn: Giá trị</code>.</div>
                            <div class="invalid-feedback">Thông số tối đa 5.000 ký tự.</div>
                        </div>
                    </div>
                </div>
                <div class="card-head border-top">
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2-circle me-1" aria-hidden="true"></i>
                            <?= $isEdit ? 'Lưu thay đổi' : 'Thêm sản phẩm' ?>
                        </button>
                        <a class="btn btn-outline-secondary" href="index.php?controller=admin&amp;action=products">Huỷ</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="admin-card mb-3">
                <div class="card-head"><h2>Ảnh đại diện</h2></div>
                <div class="card-body">
                    <div class="thumb-cell mb-3" style="width: 100%; height: 180px;">
                        <img id="thumbPreview" src="<?= e(upload_url($thumbnail)) ?>" alt="Ảnh đại diện sản phẩm"
                             width="320" height="180">
                    </div>
                    <label class="form-label" for="thumbnail">Chọn ảnh mới (JPG, PNG, WebP ≤ 2 MB)</label>
                    <input type="file" class="form-control<?= isset($errors['thumbnail']) ? ' is-invalid' : '' ?>"
                           id="thumbnail" name="thumbnail" accept="image/jpeg,image/png,image/webp"
                           data-image-input="#thumbPreview">
                    <div class="invalid-feedback<?= isset($errors['thumbnail']) ? ' d-block' : '' ?>">
                        <?= isset($errors['thumbnail']) ? e($errors['thumbnail']) : '' ?>
                    </div>
                    <div class="form-text">Để trống nếu giữ nguyên ảnh hiện tại.</div>
                    <?php if ($isEdit && !empty($product['thumbnail']) && $product['thumbnail'] !== 'no-image.svg'): ?>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="remove_thumbnail" name="remove_thumbnail" value="1"
                                   onchange="var p = document.getElementById('thumbPreview'); if (p) { p.src = this.checked ? 'Public/img/no-image.svg' : '<?= e(upload_url($thumbnail)) ?>'; }">
                            <label class="form-check-label small text-danger" for="remove_thumbnail">
                                Gỡ ảnh đại diện hiện tại (về ảnh mặc định)
                            </label>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!$isEdit): ?>
                <div class="admin-card">
                    <div class="card-head"><h2>Thư viện ảnh</h2></div>
                    <div class="card-body">
                        <label class="form-label" for="gallery">Ảnh phụ (có thể chọn nhiều)</label>
                        <input type="file" class="form-control" id="gallery" name="gallery[]"
                               accept="image/jpeg,image/png,image/webp" multiple>
                        <div class="form-text">Ảnh phụ được lưu cùng sản phẩm khi bạn bấm "Thêm sản phẩm".</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<?php if ($isEdit): ?>
    <div class="admin-card mt-3">
        <div class="card-head">
            <h2>Thư viện ảnh phụ</h2>
            <span class="badge text-bg-light border"><?= count($images) ?> ảnh</span>
        </div>
        <div class="card-body">
            <?php if ($images !== []): ?>
                <div class="gallery-grid mb-4">
                    <?php foreach ($images as $image): ?>
                        <div class="gallery-item">
                            <img src="<?= e(upload_url($image['image'])) ?>" alt="Ảnh phụ sản phẩm"
                                 loading="lazy" width="120" height="120">
                            <form method="post" action="index.php?controller=admin&amp;action=product-image-delete"
                                  data-confirm="Xoá ảnh này khỏi sản phẩm?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                <input type="hidden" name="image_id" value="<?= (int) $image['id'] ?>">
                                <button type="submit" class="btn btn-danger" title="Xoá ảnh">
                                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                                    <span class="visually-hidden">Xoá ảnh</span>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="small text-secondary">Sản phẩm chưa có ảnh phụ nào.</p>
            <?php endif; ?>

            <form class="js-validate" method="post" enctype="multipart/form-data"
                  action="index.php?controller=admin&amp;action=product-gallery-upload">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                <div class="row g-2 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label" for="gallery-edit">Thêm ảnh phụ (có thể chọn nhiều)</label>
                        <input type="file" class="form-control" id="gallery-edit" name="gallery[]"
                               accept="image/jpeg,image/png,image/webp" multiple required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-upload me-1" aria-hidden="true"></i>Tải ảnh lên
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
