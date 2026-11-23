<?php
$pageTitle = 'Câu hỏi thường gặp';
require __DIR__ . '/../Layouts/Frontend/header.php';
?>
<header class="content-page-heading mb-4 mb-lg-5">
    <p class="text-uppercase text-primary fw-semibold mb-2">Hỗ trợ khách hàng</p>
    <h1 class="display-5 fw-bold mb-3">Câu hỏi thường gặp</h1>
    <p class="lead text-secondary mb-0">Tìm câu trả lời cho những thắc mắc thường gặp.</p>
</header>

<?php if (!$faqs): ?>
    <div class="alert alert-info" role="status">Chưa có câu hỏi thường gặp được công bố.</div>
<?php else: ?>
    <div class="faq-list" data-faq-accordion>
        <?php foreach ($faqs as $index => $faq): ?>
            <?php $answerId = 'faq-answer-' . (int) $faq['id']; ?>
            <section class="faq-item card border-0 shadow-sm mb-3">
                <?php if (!empty($faq['category'])): ?>
                    <div class="card-header border-0 bg-transparent pt-4 px-4 pb-0">
                        <span class="badge text-bg-primary"><?= $escape($faq['category']) ?></span>
                    </div>
                <?php endif; ?>
                <div class="card-body p-4">
                    <h2 class="h5 mb-0">
                        <button
                            class="faq-toggle"
                            type="button"
                            aria-expanded="false"
                            aria-controls="<?= $escape($answerId) ?>">
                            <span><?= $escape($faq['question']) ?></span>
                            <span class="faq-toggle-icon" aria-hidden="true">+</span>
                        </button>
                    </h2>
                    <div class="faq-answer text-secondary pt-3" id="<?= $escape($answerId) ?>" hidden>
                        <?= nl2br($escape($faq['answer'])) ?>
                    </div>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<script src="Public/js/faq.js" defer></script>
<?php require __DIR__ . '/../Layouts/Frontend/footer.php'; ?>
