<?php
/** Trang đọc bài viết chi tiết + Bình luận (Phần #4). */
$fullWidth = true;
$ogImage = upload_url($post['thumbnail'], 'posts');
require __DIR__ . '/../../Layouts/Frontend/header.php';
$currentUser = Permission::user();
?>
<div class="page-hero py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="index.php?controller=post&amp;action=index">Tin tức</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= $escape($post['title']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mt-4">
    <div class="row g-4">
        <div class="col-lg-8">
            <article class="panel overflow-hidden mb-4">
                <?php if (!empty($post['thumbnail'])): ?>
                    <img src="<?= $escape(upload_url($post['thumbnail'], 'posts')) ?>"
                         alt="<?= $escape($post['title']) ?>"
                         class="w-100 object-fit-cover" style="max-height: 380px;">
                <?php endif; ?>
                <div class="panel-body p-4 p-lg-5">
                    <div class="d-flex flex-wrap gap-3 small text-secondary mb-3">
                        <span><i class="bi bi-calendar3 me-1" aria-hidden="true"></i><?= $escape(format_datetime($post['created_at'])) ?></span>
                        <?php if (!empty($post['author_name'])): ?>
                            <span><i class="bi bi-person me-1" aria-hidden="true"></i><?= $escape($post['author_name']) ?></span>
                        <?php endif; ?>
                        <span><i class="bi bi-chat-dots me-1" aria-hidden="true"></i><?= count($comments) ?> bình luận</span>
                    </div>

                    <h1 class="h2 fw-bold mb-3"><?= $escape($post['title']) ?></h1>

                    <?php if (!empty($post['summary'])): ?>
                        <p class="lead text-secondary mb-4"><?= $escape($post['summary']) ?></p>
                    <?php endif; ?>

                    <div class="fs-6 lh-lg" style="white-space: pre-line;"><?= $escape($post['content']) ?></div>

                    <?php if (!empty($post['meta_keywords'])): ?>
                        <hr class="my-4">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="small fw-semibold text-secondary"><i class="bi bi-tags me-1" aria-hidden="true"></i>Từ khóa:</span>
                            <?php foreach (array_filter(array_map('trim', explode(',', $post['meta_keywords']))) as $tag): ?>
                                <a class="badge badge-soft text-decoration-none px-2 py-1"
                                   href="index.php?controller=post&amp;action=index&amp;q=<?= rawurlencode($tag) ?>">
                                    #<?= $escape($tag) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </article>

            <!-- Bình luận -->
            <section id="comments" class="panel">
                <div class="panel-header">
                    <span><i class="bi bi-chat-left-text me-2" aria-hidden="true"></i>Bình luận (<?= count($comments) ?>)</span>
                </div>
                <div class="panel-body">
                    <?php if ($commentSuccess !== null): ?>
                        <div class="alert alert-<?= $escape($commentSuccess['type']) ?>" role="status">
                            <?= $escape($commentSuccess['message']) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($commentError !== null): ?>
                        <div class="alert alert-danger" role="alert"><?= $escape($commentError) ?></div>
                    <?php endif; ?>

                    <?php if (Permission::isMember()): ?>
                        <form method="post"
                              action="index.php?controller=post&amp;action=show&amp;slug=<?= rawurlencode($post['slug']) ?>#comments"
                              class="js-validate needs-validation mb-4" novalidate>
                            <?= csrf_field() ?>
                            <div class="mb-2">
                                <label for="comment-content" class="form-label fw-semibold">
                                    Viết bình luận với tư cách <?= $escape($currentUser['fullname']) ?>
                                </label>
                                <textarea class="form-control" id="comment-content" name="content" rows="3"
                                          minlength="3" maxlength="2000" required
                                          placeholder="Chia sẻ ý kiến hoặc đặt câu hỏi về bài viết..."></textarea>
                                <div class="invalid-feedback">Vui lòng nhập nội dung bình luận (3–2.000 ký tự).</div>
                            </div>
                            <button type="submit" class="btn btn-brand">
                                <i class="bi bi-send me-1" aria-hidden="true"></i>Gửi bình luận
                            </button>
                        </form>
                    <?php elseif ($currentUser !== null): ?>
                        <div class="alert alert-light border small mb-4">
                            Tài khoản <strong><?= $escape(Permission::roleLabel($currentUser['role'])) ?></strong> không gửi bình luận như khách hàng.
                            <?php if (Permission::isStaff()): ?>
                                <a class="ms-1 fw-semibold text-success" href="index.php?controller=staff&amp;action=comments">Kiểm duyệt bình luận tại Kênh CSKH &rarr;</a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php $currentPostUrl = 'index.php?controller=post&action=show&slug=' . rawurlencode($post['slug']) . '#comments'; ?>
                        <div class="alert alert-warning border-warning-subtle d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4" role="status">
                            <span class="small">
                                <i class="bi bi-shield-lock me-1" aria-hidden="true"></i>
                                <strong>Chưa đăng nhập:</strong> Bạn không thể viết bình luận. Vui lòng đăng nhập bằng tài khoản Thành viên để tham gia bình luận bài viết này.
                            </span>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm"
                                        data-require-login="Bạn chưa đăng nhập nên không thể viết bình luận. Vui lòng đăng nhập bằng tài khoản Thành viên (Khách hàng) để bình luận bài viết này."
                                        data-login-redirect="<?= $escape($currentPostUrl) ?>">
                                    <i class="bi bi-chat-left-text me-1" aria-hidden="true"></i>Viết bình luận
                                </button>
                                <a href="index.php?controller=auth&amp;action=login&amp;redirect=<?= rawurlencode($currentPostUrl) ?>"
                                   class="btn btn-brand btn-sm">Đăng nhập ngay</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($comments)): ?>
                        <p class="text-secondary mb-0">Chưa có bình luận nào được hiển thị. Hãy là người đầu tiên bình luận!</p>
                    <?php else: ?>
                        <div class="vstack gap-3">
                            <?php foreach ($comments as $comment): ?>
                                <div class="border rounded-3 p-3 bg-light-subtle">
                                    <div class="d-flex justify-content-between align-items-center mb-1 gap-2">
                                        <strong class="small"><?= $escape($comment['author_name']) ?></strong>
                                        <span class="text-secondary small"><?= $escape(format_datetime($comment['created_at'])) ?></span>
                                    </div>
                                    <p class="mb-0 small text-body" style="white-space: pre-line;"><?= $escape($comment['content']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <aside class="col-lg-4">
            <div class="panel mb-4">
                <div class="panel-header">Bài viết mới nhất</div>
                <div class="panel-body">
                    <?php if (empty($recentPosts)): ?>
                        <p class="small text-secondary mb-0">Chưa có bài viết khác.</p>
                    <?php else: ?>
                        <div class="vstack gap-3">
                            <?php foreach ($recentPosts as $item): ?>
                                <?php $itemUrl = 'index.php?controller=post&action=show&slug=' . rawurlencode($item['slug']); ?>
                                <a href="<?= $escape($itemUrl) ?>" class="d-flex gap-3 text-decoration-none">
                                    <img src="<?= $escape(upload_url($item['thumbnail'], 'posts')) ?>"
                                         alt="<?= $escape($item['title']) ?>"
                                         width="72" height="54" class="rounded object-fit-cover flex-shrink-0 border">
                                    <div>
                                        <span class="d-block small fw-semibold text-dark text-truncate-2"><?= $escape($item['title']) ?></span>
                                        <span class="small text-secondary"><?= $escape(format_datetime($item['created_at'])) ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="panel">
                <div class="panel-body text-center">
                    <h2 class="h6 fw-bold mb-2">Cần tư vấn giải pháp IT?</h2>
                    <p class="small text-secondary mb-3">Đội ngũ kỹ sư của chúng tôi sẵn sàng khảo sát và báo giá miễn phí.</p>
                    <a href="index.php?controller=contact" class="btn btn-brand btn-sm w-100">Liên hệ tư vấn</a>
                </div>
            </div>
        </aside>
    </div>
</div>

<?php require __DIR__ . '/../../Layouts/Frontend/footer.php'; ?>
