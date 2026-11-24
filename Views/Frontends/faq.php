<?php
$pageTitle = 'Câu hỏi thường gặp';
require __DIR__ . '/../Layouts/Frontend/header.php';
?>
<header class="content-page-heading mb-4 mb-lg-5">
    <p class="text-uppercase text-primary fw-semibold mb-2">Hỗ trợ khách hàng</p>
    <h1 class="display-5 fw-bold mb-3">Hỏi / Đáp (FAQ)</h1>
    <p class="lead text-secondary mb-0">Tra cứu câu trả lời nhanh hoặc gửi câu hỏi mới để Nhân viên Chăm sóc khách hàng hỗ trợ trực tiếp.</p>
</header>

<div class="row g-4">
    <div class="col-lg-7">
        <h2 class="h5 fw-bold mb-3">Các câu hỏi thường gặp</h2>
        <?php if (!$faqs): ?>
            <div class="alert alert-info" role="status">Chưa có câu hỏi thường gặp được công bố.</div>
        <?php else: ?>
            <div class="faq-list" data-faq-accordion>
                <?php foreach ($faqs as $faq): ?>
                    <?php $answerId = 'faq-answer-' . (int) $faq['id']; ?>
                    <section class="faq-item card border-0 shadow-sm mb-3">
                        <?php if (!empty($faq['category'])): ?>
                            <div class="card-header border-0 bg-transparent pt-4 px-4 pb-0">
                                <span class="badge text-bg-primary"><?= $escape($faq['category']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="card-body p-4">
                            <h3 class="h6 mb-0">
                                <button
                                    class="faq-toggle"
                                    type="button"
                                    aria-expanded="false"
                                    aria-controls="<?= $escape($answerId) ?>">
                                    <span><?= $escape($faq['question']) ?></span>
                                    <span class="faq-toggle-icon" aria-hidden="true">+</span>
                                </button>
                            </h3>
                            <div class="faq-answer text-secondary pt-3 mt-3" id="<?= $escape($answerId) ?>" hidden>
                                <p class="mb-2"><?= nl2br($escape($faq['answer'])) ?></p>
                                <?php if (!empty($faq['staff_name'])): ?>
                                    <p class="small text-success mb-0">
                                        <i class="bi bi-headset me-1" aria-hidden="true"></i>
                                        Giải đáp bởi: <?= $escape($faq['staff_name']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($myQuestions)): ?>
            <section class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">
                        <i class="bi bi-clock-history text-primary me-1" aria-hidden="true"></i>Câu hỏi bạn đã gửi
                    </h2>
                    <div class="vstack gap-3">
                        <?php foreach ($myQuestions as $q): ?>
                            <?php $st = (int) $q['status']; ?>
                            <div class="border rounded-3 p-3">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
                                    <strong class="small"><?= $escape($q['question']) ?></strong>
                                    <?php if ($st === 0): ?>
                                        <span class="badge text-bg-warning">Chờ CSKH phản hồi</span>
                                    <?php elseif ($st === 1): ?>
                                        <span class="badge text-bg-success">Đã trả lời</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary">Đã ẩn</span>
                                    <?php endif; ?>
                                </div>
                                <div class="small text-secondary mb-2">
                                    Gửi lúc <?= $escape(format_datetime($q['created_at'])) ?>
                                    <?= !empty($q['category']) ? ' · Chủ đề: ' . $escape($q['category']) : '' ?>
                                </div>
                                <?php if (!empty($q['answer'])): ?>
                                    <div class="p-2.5 rounded-3 bg-success-subtle border-start border-3 border-success small">
                                        <strong class="text-success d-block mb-1">
                                            Phản hồi CSKH<?= !empty($q['staff_name']) ? ' (' . $escape($q['staff_name']) . ')' : '' ?>:
                                        </strong>
                                        <span style="white-space: pre-line;"><?= $escape($q['answer']) ?></span>
                                    </div>
                                <?php else: ?>
                                    <p class="small text-secondary fst-italic mb-0">Câu hỏi đang trong hàng đợi của bộ phận Chăm sóc khách hàng...</p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <div class="col-lg-5">
        <section id="ask-question" class="card border-0 shadow-sm sticky-top" style="top: 90px;">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-2">
                    <i class="bi bi-chat-square-dots text-primary me-1" aria-hidden="true"></i>Đặt câu hỏi mới
                </h2>
                <p class="small text-secondary mb-3">
                    Không tìm thấy câu trả lời bạn cần? Hãy gửi câu hỏi mới, nhân viên CSKH sẽ giải đáp và cập nhật phản hồi cho bạn.
                </p>

                <?php if (!empty($faqFlash)): ?>
                    <div class="alert alert-<?= $escape($faqFlash['type']) ?> small" role="status">
                        <?= $escape($faqFlash['message']) ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($faqError)): ?>
                    <div class="alert alert-danger small" role="alert">
                        <?= $escape($faqError) ?>
                    </div>
                <?php endif; ?>

                <?php if (Permission::isMember()): ?>
                    <form method="post" action="index.php?controller=faq&amp;action=ask" class="js-validate needs-validation" novalidate>
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold" for="asker_name">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="asker_name" name="asker_name"
                                   value="<?= $escape($faqOld['asker_name'] ?? '') ?>" minlength="2" maxlength="100" required>
                            <div class="invalid-feedback">Vui lòng nhập họ tên (2–100 ký tự).</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold" for="asker_email">Email nhận phản hồi <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="asker_email" name="asker_email"
                                   value="<?= $escape($faqOld['asker_email'] ?? '') ?>" maxlength="191" required>
                            <div class="invalid-feedback">Vui lòng nhập địa chỉ email hợp lệ.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold" for="faq_category">Chủ đề</label>
                            <select class="form-select" id="faq_category" name="category">
                                <?php $selectedCat = $faqOld['category'] ?? ''; ?>
                                <option value="Đặt hàng &amp; Giao nhận" <?= $selectedCat === 'Đặt hàng & Giao nhận' ? 'selected' : '' ?>>Đặt hàng &amp; Giao nhận</option>
                                <option value="Bảo hành" <?= $selectedCat === 'Bảo hành' ? 'selected' : '' ?>>Bảo hành</option>
                                <option value="Thanh toán" <?= $selectedCat === 'Thanh toán' ? 'selected' : '' ?>>Thanh toán</option>
                                <option value="Hỗ trợ kỹ thuật" <?= $selectedCat === 'Hỗ trợ kỹ thuật' ? 'selected' : '' ?>>Hỗ trợ kỹ thuật</option>
                                <option value="Tư vấn sản phẩm" <?= $selectedCat === 'Tư vấn sản phẩm' ? 'selected' : '' ?>>Tư vấn sản phẩm</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold" for="faq_question">Câu hỏi của bạn <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="faq_question" name="question" rows="4"
                                      minlength="10" maxlength="255" required
                                      placeholder="Nhập thắc mắc của bạn (tối thiểu 10 ký tự)..."><?= $escape($faqOld['question'] ?? '') ?></textarea>
                            <div class="invalid-feedback">Câu hỏi phải có từ 10 đến 255 ký tự.</div>
                        </div>
                        <button type="submit" class="btn btn-brand w-100">
                            <i class="bi bi-send me-1" aria-hidden="true"></i>Gửi câu hỏi chờ phản hồi
                        </button>
                    </form>
                <?php elseif (!Permission::isLoggedIn()): ?>
                    <div class="alert alert-warning border-warning-subtle small mb-3" role="status">
                        <i class="bi bi-shield-lock me-1" aria-hidden="true"></i>
                        <strong>Chưa đăng nhập:</strong> Bạn không thể đặt câu hỏi Hỏi/Đáp mới. Vui lòng đăng nhập bằng <strong>tài khoản Thành viên</strong> để gửi câu hỏi và theo dõi phản hồi từ bộ phận CSKH.
                    </div>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-brand"
                                data-require-login="Bạn chưa đăng nhập nên không thể đặt câu hỏi Hỏi/Đáp. Vui lòng đăng nhập bằng tài khoản Thành viên (Khách hàng) để gửi câu hỏi và nhận phản hồi từ CSKH."
                                data-login-redirect="index.php?controller=faq&amp;action=index#ask-question">
                            <i class="bi bi-chat-square-dots me-1" aria-hidden="true"></i>Đặt câu hỏi mới
                        </button>
                        <div class="d-flex gap-2">
                            <a href="index.php?controller=auth&amp;action=login&amp;redirect=<?= rawurlencode('index.php?controller=faq&action=index#ask-question') ?>"
                               class="btn btn-outline-primary btn-sm flex-fill">Đăng nhập ngay</a>
                            <a href="index.php?controller=auth&amp;action=register&amp;redirect=<?= rawurlencode('index.php?controller=faq&action=index#ask-question') ?>"
                               class="btn btn-soft btn-sm flex-fill">Đăng ký</a>
                        </div>
                    </div>
                <?php elseif (Permission::isStaff()): ?>
                    <div class="alert alert-success small mb-0">
                        <p class="mb-2">Bạn đang đăng nhập với vai trò <strong>Nhân viên CSKH</strong>.</p>
                        <a href="index.php?controller=staff&amp;action=faqs&amp;status=0" class="btn btn-success btn-sm">
                            Mở danh sách câu hỏi chờ trả lời &rarr;
                        </a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light border small mb-0">
                        Tài khoản <strong>Quản trị viên</strong> không gửi câu hỏi khách hàng. Nghiệp vụ trả lời FAQ thuộc về Nhân viên CSKH.
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<script src="Public/js/faq.js" defer></script>
<?php require __DIR__ . '/../Layouts/Frontend/footer.php'; ?>
