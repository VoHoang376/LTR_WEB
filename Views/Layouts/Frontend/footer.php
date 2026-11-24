<?php
$settings = isset($settings) && is_array($settings) && $settings !== [] ? $settings : site_settings();
$companyName = !empty($settings['company_name']) ? $settings['company_name'] : site_name();
$frontendJsVer = file_exists(__DIR__ . '/../../../Public/js/frontend.js')
    ? filemtime(__DIR__ . '/../../../Public/js/frontend.js')
    : 1;
$cartJsVer = file_exists(__DIR__ . '/../../../Public/js/cart.js')
    ? filemtime(__DIR__ . '/../../../Public/js/cart.js')
    : 1;
?>
</main>

<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand">
                    <span class="brand-mark" aria-hidden="true"><i class="bi bi-box-seam-fill"></i></span>
                    <span class="brand-text"><?= e($companyName) ?><span>Thiết bị &amp; giải pháp</span></span>
                </div>
                <p class="mb-3">
                    <?= e($settings['company_intro'] ?? 'Chuyên cung cấp giải pháp công nghệ, thiết bị văn phòng và camera an ninh chính hãng cho doanh nghiệp.') ?>
                </p>
                <p class="mb-1 small">
                    <i class="bi bi-geo-alt-fill me-2" aria-hidden="true"></i><?= e($settings['company_address'] ?? '268 Lý Thường Kiệt, Q.10, TP. Hồ Chí Minh') ?>
                </p>
                <p class="mb-1 small">
                    <i class="bi bi-telephone-fill me-2" aria-hidden="true"></i><?= e($settings['company_phone'] ?? '(028) 3864 7256') ?>
                </p>
                <p class="mb-0 small">
                    <i class="bi bi-envelope-fill me-2" aria-hidden="true"></i><?= e($settings['company_email'] ?? 'contact@ltr.vn') ?>
                </p>
            </div>

            <div class="col-6 col-lg-2">
                <h2>Khám phá</h2>
                <ul>
                    <li><a href="index.php">Trang chủ</a></li>
                    <li><a href="index.php?controller=about">Giới thiệu</a></li>
                    <li><a href="index.php?controller=product&amp;action=index">Sản phẩm</a></li>
                    <li><a href="index.php?controller=pricing">Bảng giá</a></li>
                    <li><a href="index.php?controller=post&amp;action=index">Tin tức</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-2">
                <h2>Hỗ trợ</h2>
                <ul>
                    <li><a href="index.php?controller=faq">Hỏi/Đáp (FAQ)</a></li>
                    <li><a href="index.php?controller=contact">Liên hệ</a></li>
                    <?php if (Permission::isMember()): ?>
                        <li><a href="index.php?controller=cart&amp;action=index">Giỏ hàng</a></li>
                        <li><a href="index.php?controller=order&amp;action=index">Đơn hàng của tôi</a></li>
                    <?php elseif (!Permission::isLoggedIn()): ?>
                        <li>
                            <a href="index.php?controller=cart&amp;action=index"
                               data-require-login="Bạn chưa đăng nhập nên không thể sử dụng giỏ hàng hay đặt hàng. Vui lòng đăng nhập bằng tài khoản Thành viên (Khách hàng)."
                               data-login-redirect="index.php?controller=cart&amp;action=index">Giỏ hàng</a>
                        </li>
                    <?php elseif (Permission::isAdmin()): ?>
                        <li><a href="index.php?controller=admin&amp;action=dashboard">Trang quản trị</a></li>
                    <?php elseif (Permission::isStaff()): ?>
                        <li><a href="index.php?controller=staff&amp;action=dashboard">Kênh CSKH</a></li>
                    <?php endif; ?>
                    <li><a href="index.php?controller=auth&amp;action=profile">Hồ sơ cá nhân</a></li>
                </ul>
            </div>

            <div class="col-lg-4">
                <h2>Giờ làm việc &amp; Thanh toán</h2>
                <ul>
                    <li><i class="bi bi-clock me-2" aria-hidden="true"></i><?= e($settings['company_hours'] ?? 'Thứ 2 – Thứ 6: 8:00 – 17:30 | Thứ 7: 8:00 – 12:00') ?></li>
                    <li><i class="bi bi-shield-check me-2" aria-hidden="true"></i>Sản phẩm chính hãng, xuất hóa đơn VAT</li>
                    <li><i class="bi bi-truck me-2" aria-hidden="true"></i>Giao hàng &amp; hỗ trợ lắp đặt tận nơi</li>
                </ul>
                <div class="d-flex gap-2 mt-3">
                    <span class="badge text-bg-light text-dark"><i class="bi bi-credit-card-2-front me-1" aria-hidden="true"></i>COD</span>
                    <span class="badge text-bg-light text-dark"><i class="bi bi-bank me-1" aria-hidden="true"></i>Chuyển khoản</span>
                </div>
            </div>
        </div>

        <div class="footer-bottom d-flex flex-wrap justify-content-between gap-2">
            <span>&copy; <?= date('Y') ?> <?= e($companyName) ?>. All rights reserved.</span>
            <span>Bài tập lớn Lập trình Web – HK1 2026-2027 (Native PHP MVC)</span>
        </div>
    </div>
</footer>

<?php if (!Permission::isLoggedIn()): ?>
<div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-labelledby="loginRequiredModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning-subtle text-warning"
                          style="width: 40px; height: 40px;">
                        <i class="bi bi-shield-lock-fill fs-5" aria-hidden="true"></i>
                    </span>
                    <h2 class="modal-title h5 fw-bold mb-0" id="loginRequiredModalLabel">Yêu cầu đăng nhập</h2>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body pt-3 pb-2">
                <p class="mb-2 text-body" id="loginRequiredMessage">
                    Vui lòng đăng nhập bằng tài khoản Thành viên (Khách hàng) để sử dụng chức năng này.
                </p>
                <p class="small text-secondary mb-0">
                    Chức năng đặt hàng, viết bình luận, đánh giá sản phẩm và đặt câu hỏi Hỏi/Đáp chỉ dành cho tài khoản Thành viên đã đăng nhập.
                </p>
            </div>
            <div class="modal-footer border-0 pt-2">
                <button type="button" class="btn btn-soft" data-bs-dismiss="modal">Để sau</button>
                <a id="loginRequiredRegisterBtn" href="index.php?controller=auth&amp;action=register" class="btn btn-outline-primary">
                    <i class="bi bi-person-plus me-1" aria-hidden="true"></i>Đăng ký
                </a>
                <a id="loginRequiredLoginBtn" href="index.php?controller=auth&amp;action=login" class="btn btn-brand">
                    <i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i>Đăng nhập ngay
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container"></div>

<script src="Public/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="Public/js/validate.js"></script>
<script src="Public/js/cart.js?v=<?= (int) $cartJsVer ?>"></script>
<script src="Public/js/frontend.js?v=<?= (int) $frontendJsVer ?>"></script>
</body>
</html>
