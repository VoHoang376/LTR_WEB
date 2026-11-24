<?php
$pageTitle = 'Giới thiệu';
require __DIR__ . '/../Layouts/Frontend/header.php';
?>
<header class="content-page-heading mb-4 mb-lg-5">
    <p class="text-uppercase text-primary fw-semibold mb-2">LTR Company</p>
    <h1 class="display-5 fw-bold mb-3">Giới thiệu</h1>
    <p class="lead text-secondary mb-0">Tìm hiểu thêm về công ty, tầm nhìn và sứ mệnh của chúng tôi.</p>
</header>

<?php if (!$sections): ?>
    <div class="alert alert-info" role="status">Thông tin giới thiệu đang được cập nhật.</div>
<?php else: ?>
    <div class="about-sections">
        <?php foreach ($sections as $index => $section): ?>
            <section class="about-section card border-0 shadow-sm mb-4">
                <div class="row g-0 align-items-center">
                    <?php if (!empty($section['image_url'])): ?>
                        <div class="col-lg-5">
                            <img
                                class="about-section-image"
                                src="<?= $escape(upload_url($section['image_url'], 'about')) ?>"
                                alt="<?= $escape($section['section_title']) ?>"
                                loading="lazy">
                        </div>
                    <?php endif; ?>
                    <div class="<?= !empty($section['image_url']) ? 'col-lg-7' : 'col-12' ?>">
                        <div class="card-body p-4 p-lg-5">
                            <h2 class="h3 mb-3"><?= $escape($section['section_title']) ?></h2>
                            <div class="about-rich-content"><?= $section['content'] ?></div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../Layouts/Frontend/footer.php'; ?>
