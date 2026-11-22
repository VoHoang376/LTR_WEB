    </main>
    <footer class="footer-section bg-dark text-white pt-5 pb-3">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-building me-1"></i>LTR Company</h5>
                    <p class="text-white-50">Chuyên cung cấp các giải pháp công nghệ toàn diện cho doanh nghiệp. Chất lượng hàng đầu, dịch vụ tận tâm.</p>
                </div>
                <div class="col-sm-6 col-lg-2">
                    <h6 class="fw-semibold mb-3">Liên kết</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="index.php">Trang chủ</a></li>
                        <li><a href="index.php?controller=about">Giới thiệu</a></li>
                        <li><a href="index.php?controller=product">Sản phẩm</a></li>
                        <li><a href="index.php?controller=post">Tin tức</a></li>
                    </ul>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <h6 class="fw-semibold mb-3">Hỗ trợ</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="index.php?controller=contact">Liên hệ</a></li>
                        <li><a href="index.php?controller=faq">Hỏi đáp</a></li>
                        <li><a href="index.php?controller=auth&amp;action=register">Đăng ký</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6 class="fw-semibold mb-3">Liên hệ</h6>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>268 Lý Thường Kiệt, Q.10, TP.HCM</li>
                        <li class="mb-2"><i class="bi bi-telephone me-2"></i>(028) 3864 7256</li>
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i>contact@ltrcompany.vn</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="text-center text-white-50 small">
                &copy; <?= date('Y') ?> LTR Company. All rights reserved.
            </div>
        </div>
    </footer>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-WyOxWvTyr1QaMPfiPJSv76iReVthorl5uWlFtZPyK0IEB0hGzo9y5Bp9mYanr8Bu"
        crossorigin="anonymous"></script>
    <script src="Public/js/frontend.js?v=<?= file_exists(__DIR__ . '/../../../Public/js/frontend.js') ? filemtime(__DIR__ . '/../../../Public/js/frontend.js') : time() ?>"></script>
</body>
</html>
