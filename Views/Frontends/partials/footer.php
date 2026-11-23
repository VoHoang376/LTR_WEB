<?php
/** Khung dưới của giao diện người dùng: footer, toast, script. */
?>
</main>

<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand">
                    <span class="brand-mark" aria-hidden="true"><i class="bi bi-box-seam-fill"></i></span>
                    <span class="brand-text"><?= e(site_name()) ?><span>Thiết bị &amp; giải pháp</span></span>
                </div>
                <p class="mb-3">
                    Nhà phân phối thiết bị văn phòng, máy tính và giải pháp an ninh cho doanh nghiệp.
                    Sản phẩm chính hãng, bảo hành đầy đủ, hỗ trợ kỹ thuật tận nơi.
                </p>
                <p class="mb-0 small">
                    <i class="bi bi-telephone-fill me-2" aria-hidden="true"></i>Hotline: 1900 6868
                </p>
                <p class="mb-0 small">
                    <i class="bi bi-envelope-fill me-2" aria-hidden="true"></i>contact@ltr.vn
                </p>
            </div>

            <div class="col-6 col-lg-2">
                <h2>Danh mục</h2>
                <ul>
                    <li><a href="index.php?controller=product&amp;action=index">Tất cả sản phẩm</a></li>
                    <li><a href="index.php?controller=product&amp;action=index&amp;category=may-tinh-laptop">Máy tính &amp; Laptop</a></li>
                    <li><a href="index.php?controller=product&amp;action=index&amp;category=thiet-bi-mang">Thiết bị mạng</a></li>
                    <li><a href="index.php?controller=product&amp;action=index&amp;category=camera-an-ninh">Camera an ninh</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-2">
                <h2>Tài khoản</h2>
                <ul>
                    <li><a href="index.php?controller=cart&amp;action=index">Giỏ hàng</a></li>
                    <li><a href="index.php?controller=order&amp;action=index">Đơn hàng của tôi</a></li>
                    <li><a href="index.php?controller=auth&amp;action=profile">Hồ sơ cá nhân</a></li>
                    <li><a href="index.php?controller=auth&amp;action=login">Đăng nhập</a></li>
                </ul>
            </div>

            <div class="col-lg-4">
                <h2>Hỗ trợ khách hàng</h2>
                <ul>
                    <li><i class="bi bi-clock me-2" aria-hidden="true"></i>Thứ 2 – Thứ 7: 8:00 – 17:30</li>
                    <li><i class="bi bi-geo-alt me-2" aria-hidden="true"></i>Số 1 Đại Cồ Việt, Hai Bà Trưng, Hà Nội</li>
                    <li><i class="bi bi-truck me-2" aria-hidden="true"></i>Miễn phí giao hàng nội thành Hà Nội</li>
                </ul>
                <div class="d-flex gap-2 mt-3">
                    <span class="badge text-bg-light text-dark"><i class="bi bi-credit-card-2-front me-1" aria-hidden="true"></i>COD</span>
                    <span class="badge text-bg-light text-dark"><i class="bi bi-bank me-1" aria-hidden="true"></i>Chuyển khoản</span>
                </div>
            </div>
        </div>

        <div class="footer-bottom d-flex flex-wrap justify-content-between gap-2">
            <span>&copy; <?= date('Y') ?> <?= e(site_name()) ?>. Bài tập lớn Lập trình Web – HK1 2026-2027.</span>
            <span>Phần #3: Sản phẩm · Giỏ hàng · Đơn hàng</span>
        </div>
    </div>
</footer>

<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container"></div>

<script src="Public/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="Public/js/validate.js"></script>
<script src="Public/js/cart.js"></script>
</body>
</html>
