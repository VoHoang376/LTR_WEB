<?php
/** Trang danh sách Tin tức / Bài viết (Phần #4). */
$fullWidth = true;
require __DIR__ . '/../../Layouts/Frontend/header.php';
?>
<section class="page-hero">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tin tức</li>
            </ol>
        </nav>
        <h1>
            <?= $keyword !== '' ? 'Kết quả tìm kiếm tin tức: &ldquo;' . $escape($keyword) . '&rdquo;' : 'Tin Tức &amp; Kiến Thức Công Nghệ' ?>
        </h1>
        <p>Cập nhật xu hướng thiết bị văn phòng, giải pháp hạ tầng mạng và kinh nghiệm triển khai IT cho doanh nghiệp.</p>
    </div>
</section>

<div class="container mt-4">
    <form method="get" action="index.php" class="filter-panel mb-4" role="search">
        <input type="hidden" name="controller" value="post">
        <input type="hidden" name="action" value="index">
        <div class="row g-2 align-items-center">
            <div class="col-md-9">
                <label class="visually-hidden" for="news-search">Tìm kiếm bài viết</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search" aria-hidden="true"></i></span>
                    <input type="search" class="form-control" id="news-search" name="q" maxlength="100"
                           value="<?= $escape($keyword) ?>" placeholder="Tìm kiếm theo tiêu đề, tóm tắt hoặc từ khóa...">
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-brand flex-grow-1">Tìm kiếm</button>
                <?php if ($keyword !== ''): ?>
                    <a href="index.php?controller=post&amp;action=index" class="btn btn-soft">Xóa</a>
                <?php endif; ?>
            </div>
        </div>
    </form>

    <?php if (empty($posts)): ?>
        <div class="panel empty-state">
            <div class="empty-icon"><i class="bi bi-newspaper" aria-hidden="true"></i></div>
            <h2 class="h5 fw-bold">Không tìm thấy bài viết phù hợp</h2>
            <p class="text-secondary mb-3">Thử từ khóa khác hoặc quay lại xem tất cả bài viết.</p>
            <a class="btn btn-brand" href="index.php?controller=post&amp;action=index">Xem tất cả bài viết</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($posts as $post): ?>
                <?php $postUrl = 'index.php?controller=post&action=show&slug=' . rawurlencode($post['slug']); ?>
                <div class="col-md-6 col-lg-4">
                    <article class="card news-card h-100 border-0 shadow-sm">
                        <a href="<?= $escape($postUrl) ?>" class="d-block overflow-hidden" style="height: 200px; background: #f1f5f9;">
                            <img src="<?= $escape(upload_url($post['thumbnail'], 'posts')) ?>"
                                 alt="<?= $escape($post['title']) ?>"
                                 class="w-100 h-100 object-fit-cover" loading="lazy">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex flex-wrap gap-3 small text-secondary mb-2">
                                <span><i class="bi bi-calendar3 me-1" aria-hidden="true"></i><?= $escape(format_datetime($post['created_at'])) ?></span>
                                <span><i class="bi bi-chat-dots me-1" aria-hidden="true"></i><?= (int) ($post['comment_count'] ?? 0) ?> bình luận</span>
                            </div>
                            <h2 class="h5 fw-bold mb-2">
                                <a href="<?= $escape($postUrl) ?>" class="text-dark"><?= $escape($post['title']) ?></a>
                            </h2>
                            <p class="text-secondary small mb-3 flex-grow-1">
                                <?= $escape(excerpt($post['summary'] ?: $post['content'], 135)) ?>
                            </p>
                            <a href="<?= $escape($postUrl) ?>" class="fw-semibold text-primary">
                                Đọc chi tiết <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>

        <?= render_pagination($pagination) ?>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../../Layouts/Frontend/footer.php'; ?>
