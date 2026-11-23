<?php require __DIR__ . '/../../Layouts/Frontend/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <h1 class="h2 fw-bold mb-4">Liên Hệ Với Chúng Tôi</h1>

        <?php if ($error !== null): ?>
            <div class="alert alert-danger" role="alert"><?= $escape($error) ?></div>
        <?php endif; ?>
        <?php if ($success !== null): ?>
            <div class="alert alert-success" role="status"><?= $escape($success) ?></div>
        <?php endif; ?>

        <div class="row g-4 mb-5">
            <!-- Contact Info -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-semibold mb-4">Thông Tin Liên Hệ</h2>
                        <div class="d-flex mb-3">
                            <div class="contact-icon me-3">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div>
                                <h6 class="fw-semibold mb-1">Địa chỉ</h6>
                                <p class="text-secondary mb-0"><?= $escape($settings['company_address'] ?? '268 Lý Thường Kiệt, Phường 14, Quận 10, TP. Hồ Chí Minh') ?></p>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="contact-icon me-3">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <div>
                                <h6 class="fw-semibold mb-1">Điện thoại</h6>
                                <p class="text-secondary mb-0"><?= $escape($settings['company_phone'] ?? '(028) 3864 7256') ?></p>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="contact-icon me-3">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div>
                                <h6 class="fw-semibold mb-1">Email</h6>
                                <p class="text-secondary mb-0"><?= $escape($settings['company_email'] ?? 'contact@ltrcompany.vn') ?></p>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="contact-icon me-3">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div>
                                <h6 class="fw-semibold mb-1">Giờ làm việc</h6>
                                <p class="text-secondary mb-0"><?= nl2br($escape($settings['company_hours'] ?? "Thứ 2 – Thứ 6: 8:00 – 17:30\nThứ 7: 8:00 – 12:00")) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-semibold mb-4">Gửi Lời Nhắn</h2>
                        <form id="contactForm" method="post" action="index.php?controller=contact" novalidate>
                            <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="fullname">Họ và tên <span class="text-danger">*</span></label>
                                    <input class="form-control" id="fullname" type="text" name="fullname" value="<?= $escape($old['fullname']) ?>" maxlength="100" autocomplete="name" required>
                                    <div class="invalid-feedback">Vui lòng nhập họ và tên (2–100 ký tự).</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                    <input class="form-control" id="email" type="email" name="email" value="<?= $escape($old['email']) ?>" maxlength="100" autocomplete="email" required>
                                    <div class="invalid-feedback">Vui lòng nhập địa chỉ email hợp lệ.</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="phone">Số điện thoại</label>
                                    <input class="form-control" id="phone" type="tel" name="phone" value="<?= $escape($old['phone']) ?>" maxlength="20" autocomplete="tel">
                                    <div class="invalid-feedback">Số điện thoại không hợp lệ.</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="message">Lời nhắn <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="message" name="message" rows="5" maxlength="5000" required><?= $escape($old['message']) ?></textarea>
                                    <div class="invalid-feedback">Vui lòng nhập nội dung lời nhắn (ít nhất 10 ký tự).</div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="bi bi-send me-1"></i> Gửi lời nhắn
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Google Maps -->
        <section class="mb-2">
            <h2 class="h5 fw-semibold mb-3">Bản Đồ</h2>
            <div class="ratio ratio-21x9 rounded overflow-hidden shadow-sm">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4463709437565!2d106.65827117457706!3d10.77300788936764!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752ec3c161a3fb%3A0xef77cd47a1cc691e!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBCw6FjaCBraG9hIC0gxJDhuqFpIGjhu41jIFF14buRYyBnaWEgVFAuSENN!5e0!3m2!1svi!2s!4v1700000000000!5m2!1svi!2s"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    title="Bản đồ vị trí LTR Company"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </section>
    </div>
</div>

<?php require __DIR__ . '/../../Layouts/Frontend/footer.php'; ?>
