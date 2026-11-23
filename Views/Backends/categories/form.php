<?php
/** Quản trị – form thêm/sửa danh mục. */
require __DIR__ . '/../layouts/header.php';

$isEdit = $category !== null;
$formAction = $isEdit
    ? 'index.php?controller=admin&amp;action=category-edit'
    : 'index.php?controller=admin&amp;action=category-create';
?>
<div class="row">
    <div class="col-lg-7">
        <form class="admin-card js-validate needs-validation" method="post" action="<?= $formAction ?>" novalidate>
            <?= csrf_field() ?>
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
            <?php endif; ?>

            <div class="card-head"><h2>Thông tin danh mục</h2></div>
            <div class="card-body">
                <?php if (isset($errors['form'])): ?>
                    <div class="alert alert-danger" role="alert"><?= e($errors['form']) ?></div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label" for="name">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>"
                           id="name" name="name" value="<?= e($old['name']) ?>"
                           minlength="2" maxlength="100" required data-slug-source>
                    <div class="invalid-feedback">Tên danh mục phải có từ 2 đến 100 ký tự.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="slug">Đường dẫn (slug)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light small">/danh-muc/</span>
                        <input type="text" class="form-control" id="slug" name="slug"
                               value="<?= e($old['slug']) ?>" maxlength="100" data-slug-target
                               placeholder="tu-dong-sinh-tu-ten">
                    </div>
                    <div class="form-text<?= isset($errors['slug']) ? ' text-danger' : '' ?>">
                        <?= isset($errors['slug']) ? e($errors['slug']) : 'Bỏ trống để tự sinh từ tên danh mục.' ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="status">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="1" <?= (string) $old['status'] === '1' ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="0" <?= (string) $old['status'] === '0' ? 'selected' : '' ?>>Đang ẩn</option>
                    </select>
                </div>
            </div>
            <div class="card-head border-top">
                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1" aria-hidden="true"></i>
                        <?= $isEdit ? 'Lưu thay đổi' : 'Thêm danh mục' ?>
                    </button>
                    <a class="btn btn-outline-secondary" href="index.php?controller=admin&amp;action=categories">Huỷ</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
