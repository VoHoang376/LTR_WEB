<?php require __DIR__ . '/../../Layouts/Frontend/header.php'; ?>

<!-- Header Hero Section -->
<div class="text-center py-5 bg-white border-bottom mb-5">
    <div class="container py-2">
        <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2 rounded-pill mb-3">Dịch vụ &amp; Giải pháp phần cứng</span>
        <h1 class="display-5 fw-bold mb-3">Bảng Giá Dịch Vụ Thiết Bị Công Nghệ</h1>
        <p class="lead text-secondary mx-auto" style="max-width: 680px;">
            Dịch vụ bảo trì laptop/PC, triển khai thiết bị văn phòng và cho thuê phần cứng trọn gói giúp doanh nghiệp của bạn vận hành trơn tru và tiết kiệm chi phí tối đa.
        </p>
    </div>
</div>

<div class="container mb-5">
    <!-- Pricing Cards Grid -->
    <div class="row g-4 justify-content-center align-items-stretch">
        <?php if (empty($plans)): ?>
            <div class="col-12 text-center py-5 text-secondary">
                <i class="bi bi-tag display-4 d-block mb-3 opacity-50"></i>
                <p>Hiện tại chưa có bảng giá dịch vụ nào được công bố.</p>
            </div>
        <?php else: ?>
            <?php foreach ($plans as $plan): ?>
                <?php
                    $isFeatured = (int)$plan['is_featured'] === 1;
                    $featureList = array_filter(array_map('trim', explode("\n", $plan['features'] ?? '')));
                ?>
                <div class="col-lg-4 col-md-6 d-flex">
                    <div class="card h-100 w-100 border-0 shadow-sm rounded-4 position-relative overflow-hidden <?= $isFeatured ? 'border-primary border-2 shadow' : '' ?>">
                        <?php if ($isFeatured): ?>
                            <div class="position-absolute top-0 end-0 bg-primary text-white text-uppercase fw-bold px-3 py-1 rounded-bottom-start small" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                Phổ biến nhất
                            </div>
                        <?php endif; ?>

                        <div class="card-body p-4 p-xl-5 d-flex flex-column">
                            <h3 class="h4 fw-bold mb-2"><?= $escape($plan['name']) ?></h3>
                            <p class="text-secondary small mb-4" style="min-height: 40px;">
                                <?= $escape($plan['description'] ?? '') ?>
                            </p>

                            <div class="d-flex align-items-baseline mb-4">
                                <span class="display-6 fw-bold text-primary">
                                    <?= number_format((float)$plan['price'], 0, ',', '.') ?>₫
                                </span>
                                <span class="text-secondary ms-2">/ <?= $escape($plan['billing_cycle']) ?></span>
                            </div>

                            <hr class="my-3 text-secondary-subtle">

                            <h6 class="fw-semibold text-uppercase small text-secondary mb-3" style="letter-spacing: 0.5px;">
                                Tính năng bao gồm:
                            </h6>
                            <ul class="list-unstyled mb-5 flex-grow-1">
                                <?php foreach ($featureList as $feature): ?>
                                    <li class="d-flex align-items-start mb-2.5 small">
                                        <i class="bi bi-check-circle-fill text-success me-2 mt-0.5"></i>
                                        <span><?= $escape($feature) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <a href="index.php?controller=contact"
                               class="btn <?= $isFeatured ? 'btn-primary' : 'btn-outline-primary' ?> w-100 py-2.5 fw-semibold rounded-pill mt-auto">
                                Đăng ký tư vấn ngay
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Guarantees Row -->
    <div class="row g-4 mt-5 pt-4 text-center border-top">
        <div class="col-md-4">
            <div class="p-3">
                <i class="bi bi-shield-lock display-6 text-primary mb-3 d-block"></i>
                <h5 class="fw-semibold">Bảo mật tuyệt đối</h5>
                <p class="text-secondary small mb-0">Cam kết an toàn dữ liệu khách hàng theo các tiêu chuẩn bảo mật khắt khe nhất.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3">
                <i class="bi bi-clock-history display-6 text-primary mb-3 d-block"></i>
                <h5 class="fw-semibold">Triển khai nhanh chóng</h5>
                <p class="text-secondary small mb-0">Hệ thống sẵn sàng vận hành chỉ trong 24 giờ sau khi ký hợp đồng dịch vụ.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3">
                <i class="bi bi-headset display-6 text-primary mb-3 d-block"></i>
                <h5 class="fw-semibold">Hỗ trợ kỹ thuật 24/7</h5>
                <p class="text-secondary small mb-0">Đội ngũ kỹ sư luôn túc trực hỗ trợ giải quyết sự cố tức thì mọi lúc.</p>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../Layouts/Frontend/footer.php'; ?>
