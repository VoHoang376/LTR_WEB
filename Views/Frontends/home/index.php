<?php require __DIR__ . '/../../Layouts/Frontend/header.php'; ?>

<!-- Hero Carousel -->
<section id="hero">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="hero-slide hero-slide-1 d-flex align-items-center">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-8">
                                <h1 class="display-4 fw-bold text-white mb-3">Giải Pháp Công Nghệ Toàn Diện</h1>
                                <p class="lead text-white-75 mb-4">Chúng tôi cung cấp các giải pháp công nghệ hàng đầu, giúp doanh nghiệp của bạn vận hành hiệu quả và phát triển bền vững.</p>
                                <a href="index.php?controller=contact" class="btn btn-light btn-lg me-2">
                                    <i class="bi bi-telephone me-1"></i> Liên hệ ngay
                                </a>
                                <a href="index.php?controller=product" class="btn btn-outline-light btn-lg">
                                    <i class="bi bi-grid me-1"></i> Xem sản phẩm
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide hero-slide-2 d-flex align-items-center">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-8">
                                <h2 class="display-4 fw-bold text-white mb-3">Sản Phẩm Chất Lượng Cao</h2>
                                <p class="lead text-white-75 mb-4">Đa dạng sản phẩm chính hãng với chất lượng vượt trội, đáp ứng mọi nhu cầu của khách hàng và doanh nghiệp.</p>
                                <a href="index.php?controller=product" class="btn btn-light btn-lg">
                                    <i class="bi bi-cart me-1"></i> Khám phá ngay
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide hero-slide-3 d-flex align-items-center">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-8">
                                <h2 class="display-4 fw-bold text-white mb-3">Dịch Vụ Tận Tâm</h2>
                                <p class="lead text-white-75 mb-4">Đội ngũ chuyên gia giàu kinh nghiệm, luôn sẵn sàng hỗ trợ và tư vấn cho bạn mọi lúc mọi nơi.</p>
                                <a href="index.php?controller=about" class="btn btn-light btn-lg">
                                    <i class="bi bi-info-circle me-1"></i> Tìm hiểu thêm
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Trước</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Sau</span>
        </button>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Tại Sao Chọn Chúng Tôi?</h2>
            <p class="text-secondary">Chúng tôi cam kết mang đến giá trị tốt nhất cho khách hàng</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="text-center feature-card p-4">
                    <div class="feature-icon mx-auto mb-3">
                        <i class="bi bi-award"></i>
                    </div>
                    <h5 class="fw-semibold">Chất Lượng Cao</h5>
                    <p class="text-secondary mb-0">Sản phẩm chính hãng, đạt tiêu chuẩn quốc tế, kiểm định nghiêm ngặt.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="text-center feature-card p-4">
                    <div class="feature-icon mx-auto mb-3">
                        <i class="bi bi-tag"></i>
                    </div>
                    <h5 class="fw-semibold">Giá Cả Hợp Lý</h5>
                    <p class="text-secondary mb-0">Cam kết mức giá cạnh tranh nhất thị trường, nhiều ưu đãi hấp dẫn.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="text-center feature-card p-4">
                    <div class="feature-icon mx-auto mb-3">
                        <i class="bi bi-headset"></i>
                    </div>
                    <h5 class="fw-semibold">Hỗ Trợ 24/7</h5>
                    <p class="text-secondary mb-0">Đội ngũ tư vấn viên luôn sẵn sàng giải đáp mọi thắc mắc của bạn.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="text-center feature-card p-4">
                    <div class="feature-icon mx-auto mb-3">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h5 class="fw-semibold">Bảo Hành Uy Tín</h5>
                    <p class="text-secondary mb-0">Chính sách bảo hành dài hạn, đổi trả nhanh chóng và minh bạch.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section id="products" class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <h2 class="fw-bold mb-0">Sản Phẩm Nổi Bật</h2>
            <a href="index.php?controller=product" class="btn btn-outline-primary">Xem tất cả <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            <div class="col-sm-6 col-lg-3">
                <article class="card product-card h-100 border-0 shadow-sm">
                    <div class="product-img-placeholder placeholder-1 d-flex align-items-center justify-content-center">
                        <i class="bi bi-laptop display-1 text-white opacity-75"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Laptop Văn Phòng</h5>
                        <p class="text-secondary small mb-2">Hiệu năng mạnh mẽ, thiết kế thanh lịch, pin trâu.</p>
                        <p class="text-primary fw-bold mb-0">15.990.000₫</p>
                    </div>
                </article>
            </div>
            <div class="col-sm-6 col-lg-3">
                <article class="card product-card h-100 border-0 shadow-sm">
                    <div class="product-img-placeholder placeholder-2 d-flex align-items-center justify-content-center">
                        <i class="bi bi-printer display-1 text-white opacity-75"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Máy In Đa Năng</h5>
                        <p class="text-secondary small mb-2">In, scan, copy chất lượng cao, kết nối Wi-Fi tiện lợi.</p>
                        <p class="text-primary fw-bold mb-0">4.590.000₫</p>
                    </div>
                </article>
            </div>
            <div class="col-sm-6 col-lg-3">
                <article class="card product-card h-100 border-0 shadow-sm">
                    <div class="product-img-placeholder placeholder-3 d-flex align-items-center justify-content-center">
                        <i class="bi bi-display display-1 text-white opacity-75"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Màn Hình 27 inch</h5>
                        <p class="text-secondary small mb-2">Độ phân giải 4K, tấm nền IPS, màu sắc chính xác.</p>
                        <p class="text-primary fw-bold mb-0">7.490.000₫</p>
                    </div>
                </article>
            </div>
            <div class="col-sm-6 col-lg-3">
                <article class="card product-card h-100 border-0 shadow-sm">
                    <div class="product-img-placeholder placeholder-4 d-flex align-items-center justify-content-center">
                        <i class="bi bi-headphones display-1 text-white opacity-75"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Tai Nghe Bluetooth</h5>
                        <p class="text-secondary small mb-2">Chống ồn chủ động, âm thanh Hi-Res, pin 30 giờ.</p>
                        <p class="text-primary fw-bold mb-0">2.890.000₫</p>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>

<!-- Latest News -->
<section id="news" class="py-5 bg-white">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <h2 class="fw-bold mb-0">Tin Tức Mới Nhất</h2>
            <a href="index.php?controller=post" class="btn btn-outline-primary">Xem tất cả <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <article class="card news-card h-100 border-0 shadow-sm">
                    <div class="news-img-placeholder news-placeholder-1 d-flex align-items-center justify-content-center">
                        <i class="bi bi-newspaper display-1 text-white opacity-75"></i>
                    </div>
                    <div class="card-body">
                        <small class="text-secondary"><i class="bi bi-calendar3 me-1"></i>20/11/2026</small>
                        <h5 class="card-title mt-2">Ra Mắt Dòng Sản Phẩm Mới 2027</h5>
                        <p class="text-secondary">Chúng tôi vui mừng giới thiệu dòng sản phẩm công nghệ mới nhất, đáp ứng xu hướng thị trường hiện đại...</p>
                    </div>
                    <div class="card-footer bg-transparent border-0 pb-3">
                        <a href="index.php?controller=post" class="text-primary text-decoration-none fw-medium">Đọc tiếp <i class="bi bi-arrow-right"></i></a>
                    </div>
                </article>
            </div>
            <div class="col-md-4">
                <article class="card news-card h-100 border-0 shadow-sm">
                    <div class="news-img-placeholder news-placeholder-2 d-flex align-items-center justify-content-center">
                        <i class="bi bi-graph-up-arrow display-1 text-white opacity-75"></i>
                    </div>
                    <div class="card-body">
                        <small class="text-secondary"><i class="bi bi-calendar3 me-1"></i>15/11/2026</small>
                        <h5 class="card-title mt-2">Kết Quả Kinh Doanh Quý III/2026</h5>
                        <p class="text-secondary">Doanh thu quý III tăng trưởng 25% so với cùng kỳ năm ngoái, khẳng định vị thế trên thị trường...</p>
                    </div>
                    <div class="card-footer bg-transparent border-0 pb-3">
                        <a href="index.php?controller=post" class="text-primary text-decoration-none fw-medium">Đọc tiếp <i class="bi bi-arrow-right"></i></a>
                    </div>
                </article>
            </div>
            <div class="col-md-4">
                <article class="card news-card h-100 border-0 shadow-sm">
                    <div class="news-img-placeholder news-placeholder-3 d-flex align-items-center justify-content-center">
                        <i class="bi bi-people display-1 text-white opacity-75"></i>
                    </div>
                    <div class="card-body">
                        <small class="text-secondary"><i class="bi bi-calendar3 me-1"></i>10/11/2026</small>
                        <h5 class="card-title mt-2">Hội Thảo Công Nghệ Số 2026</h5>
                        <p class="text-secondary">LTR Company đồng tổ chức hội thảo về chuyển đổi số cho doanh nghiệp vừa và nhỏ tại TP.HCM...</p>
                    </div>
                    <div class="card-footer bg-transparent border-0 pb-3">
                        <a href="index.php?controller=post" class="text-primary text-decoration-none fw-medium">Đọc tiếp <i class="bi bi-arrow-right"></i></a>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section id="cta" class="cta-section py-5 text-white text-center">
    <div class="container py-3">
        <h2 class="fw-bold mb-3">Bạn Cần Tư Vấn?</h2>
        <p class="lead mb-4 mx-auto" style="max-width: 600px;">Liên hệ với chúng tôi ngay hôm nay để được tư vấn miễn phí và nhận báo giá tốt nhất cho doanh nghiệp của bạn.</p>
        <a href="index.php?controller=contact" class="btn btn-light btn-lg fw-semibold">
            <i class="bi bi-envelope me-2"></i>Liên hệ ngay
        </a>
    </div>
</section>

<?php require __DIR__ . '/../../Layouts/Frontend/footer.php'; ?>
