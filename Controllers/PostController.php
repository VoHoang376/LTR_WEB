<?php

require_once __DIR__ . '/../Models/PostModel.php';
require_once __DIR__ . '/../Models/CommentModel.php';

/**
 * Thành viên #4: Tin tức / Bài viết (công khai + quản trị CRUD, upload ảnh, SEO).
 */
class PostController
{
    private $db;
    private $postModel;
    private $commentModel;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->postModel = new PostModel($db);
        $this->commentModel = new CommentModel($db);
    }

    /* ====================================================== Công khai */

    /** Trang danh sách bài viết công khai (tìm kiếm + phân trang). */
    public function publicIndex()
    {
        $keyword = trim((string) ($_GET['q'] ?? $_GET['keyword'] ?? ''));
        $perPage = 6;
        $totalPosts = $this->postModel->getTotalPosts($keyword, true);
        $pagination = paginate($totalPosts, $perPage, max(1, (int) ($_GET['page'] ?? 1)));
        $posts = $this->postModel->getPosts($keyword, $pagination['perPage'], $pagination['offset'], true);

        $pageTitle = $keyword !== '' ? 'Tin tức – Tìm "' . $keyword . '"' : 'Tin tức & Bài viết';
        $metaDescription = 'Tin tức công nghệ, kinh nghiệm chọn thiết bị văn phòng và giải pháp IT từ ' . site_name() . '.';
        $currentPage = 'post';

        require __DIR__ . '/../Views/Frontends/posts/index.php';
    }

    /** Trang đọc bài viết chi tiết + gửi bình luận (yêu cầu đăng nhập). */
    public function publicShow()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $slug = trim((string) ($_GET['slug'] ?? ''));

        $post = null;
        if ($slug !== '') {
            $post = $this->postModel->getPostBySlug($slug, true);
        } elseif ($id > 0) {
            $post = $this->postModel->getPostById($id, true);
        }

        if (!$post) {
            http_response_code(404);
            $pageTitle = 'Không tìm thấy bài viết';
            require __DIR__ . '/../Views/Frontends/errors/404.php';
            return;
        }

        $commentError = null;
        $commentSuccess = flash_get('comment');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = Permission::requireMember(
                'Chỉ tài khoản Thành viên (Khách hàng) mới gửi bình luận công khai.',
                'Vui lòng đăng nhập bằng tài khoản Thành viên (Khách hàng) để viết bình luận bài viết.',
                'index.php?controller=post&action=show&slug=' . rawurlencode($post['slug'])
            );
            $content = trim((string) ($_POST['content'] ?? ''));

            if (!csrf_verify()) {
                $commentError = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
            } elseif (mb_strlen($content, 'UTF-8') < 3 || mb_strlen($content, 'UTF-8') > 2000) {
                $commentError = 'Nội dung bình luận phải có từ 3 đến 2.000 ký tự.';
            } else {
                $this->commentModel->createComment((int) $post['id'], (int) $user['id'], $content, CommentModel::STATUS_APPROVED);
                flash_set('comment', 'Bình luận của bạn đã được đăng.', 'success');
                redirect('index.php?controller=post&action=show&slug=' . rawurlencode($post['slug']) . '#comments');
            }
        }

        $comments = $this->commentModel->getCommentsByPostId((int) $post['id']);
        $recentPosts = array_values(array_filter(
            $this->postModel->getLatestPublished(5),
            static function ($item) use ($post) {
                return (int) $item['id'] !== (int) $post['id'];
            }
        ));
        $recentPosts = array_slice($recentPosts, 0, 4);

        $pageTitle = $post['title'];
        $metaDescription = !empty($post['meta_description'])
            ? $post['meta_description']
            : excerpt($post['summary'] ?: $post['content'], 160);
        $metaKeywords = $post['meta_keywords'] ?? '';
        $currentPage = 'post';

        require __DIR__ . '/../Views/Frontends/posts/show.php';
    }

    /* ======================================================= Quản trị */

    public function adminIndex()
    {
        Permission::requireAdmin();

        $keyword = trim((string) ($_GET['q'] ?? ''));
        $perPage = 10;
        $totalPosts = $this->postModel->getTotalPosts($keyword, false);
        $pagination = paginate($totalPosts, $perPage, max(1, (int) ($_GET['page'] ?? 1)));
        $posts = $this->postModel->getPosts($keyword, $pagination['perPage'], $pagination['offset'], false);

        $flash = flash_get('admin_posts');
        $pageTitle = 'Quản lý bài viết';
        $activeAdminPage = 'posts';

        require __DIR__ . '/../Views/posts/index.php';
    }

    public function create()
    {
        Permission::requireAdmin();

        $error = null;
        $post = [
            'id' => null,
            'title' => '',
            'slug' => '',
            'summary' => '',
            'content' => '',
            'thumbnail' => null,
            'meta_keywords' => '',
            'meta_description' => '',
            'status' => 1,
        ];
        $pageTitle = 'Thêm bài viết mới';
        $activeAdminPage = 'posts';

        require __DIR__ . '/../Views/posts/create.php';
    }

    public function store()
    {
        $admin = Permission::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?controller=admin&action=posts');
        }

        $post = $this->readPostInput();
        $error = $this->validatePost($post, null);

        $thumbnail = null;
        if ($error === null) {
            $uploadError = null;
            $thumbnail = upload_image($_FILES['thumbnail'] ?? null, 'posts', $uploadError);
            if ($uploadError !== null) {
                $error = $uploadError;
            }
        }

        if ($error !== null) {
            http_response_code(422);
            $pageTitle = 'Thêm bài viết mới';
            $activeAdminPage = 'posts';
            require __DIR__ . '/../Views/posts/create.php';
            return;
        }

        $this->postModel->createPost(
            (int) $admin['id'],
            $post['title'],
            $post['slug'],
            $post['summary'],
            $post['content'],
            $thumbnail,
            $post['meta_keywords'],
            $post['meta_description'],
            (int) $post['status']
        );

        flash_set('admin_posts', 'Đã thêm bài viết "' . $post['title'] . '" thành công.', 'success');
        redirect('index.php?controller=admin&action=posts');
    }

    public function edit()
    {
        Permission::requireAdmin();

        $id = (int) ($_GET['id'] ?? 0);
        $post = $this->postModel->getPostById($id, false);
        if (!$post) {
            flash_set('admin_posts', 'Không tìm thấy bài viết.', 'danger');
            redirect('index.php?controller=admin&action=posts');
        }

        $error = null;
        $pageTitle = 'Chỉnh sửa bài viết';
        $activeAdminPage = 'posts';

        require __DIR__ . '/../Views/posts/edit.php';
    }

    public function update()
    {
        Permission::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?controller=admin&action=posts');
        }

        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        $existing = $this->postModel->getPostById($id, false);
        if (!$existing) {
            flash_set('admin_posts', 'Không tìm thấy bài viết.', 'danger');
            redirect('index.php?controller=admin&action=posts');
        }

        $post = $this->readPostInput();
        $post['id'] = $id;
        $post['thumbnail'] = $existing['thumbnail'];
        $error = $this->validatePost($post, $id);

        $removeThumbnail = !empty($_POST['remove_thumbnail']);
        $newThumbnail = null;
        if ($error === null) {
            $uploadError = null;
            $newThumbnail = upload_image($_FILES['thumbnail'] ?? null, 'posts', $uploadError);
            if ($uploadError !== null) {
                $error = $uploadError;
            }
        }

        if ($error !== null) {
            http_response_code(422);
            $pageTitle = 'Chỉnh sửa bài viết';
            $activeAdminPage = 'posts';
            require __DIR__ . '/../Views/posts/edit.php';
            return;
        }

        $thumbnailParam = $newThumbnail;
        if ($newThumbnail === null && $removeThumbnail) {
            // Truyền chuỗi rỗng để PostModel::updatePost() đặt cột thumbnail = NULL.
            $thumbnailParam = '';
        }

        $this->postModel->updatePost(
            $id,
            $post['title'],
            $post['slug'],
            $post['summary'],
            $post['content'],
            $thumbnailParam,
            $post['meta_keywords'],
            $post['meta_description'],
            (int) $post['status']
        );

        if (($newThumbnail !== null || $removeThumbnail)
            && !empty($existing['thumbnail'])
            && strtolower(pathinfo($existing['thumbnail'], PATHINFO_EXTENSION)) !== 'svg') {
            delete_upload_image($existing['thumbnail'], 'posts');
        }

        flash_set('admin_posts', 'Đã cập nhật bài viết "' . $post['title'] . '" thành công.', 'success');
        redirect('index.php?controller=admin&action=posts');
    }

    public function delete()
    {
        Permission::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
            flash_set('admin_posts', 'Yêu cầu xóa không hợp lệ.', 'danger');
            redirect('index.php?controller=admin&action=posts');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $existing = $this->postModel->getPostById($id, false);
        if ($existing) {
            $this->postModel->deletePost($id);
            if (!empty($existing['thumbnail']) && strpos($existing['thumbnail'], 'news-') !== 0) {
                delete_upload_image($existing['thumbnail'], 'posts');
            }
            flash_set('admin_posts', 'Đã xóa bài viết "' . $existing['title'] . '".', 'success');
        }

        redirect('index.php?controller=admin&action=posts');
    }

    private function readPostInput()
    {
        return [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'slug' => trim((string) ($_POST['slug'] ?? '')),
            'summary' => trim((string) ($_POST['summary'] ?? '')),
            'content' => trim((string) ($_POST['content'] ?? '')),
            'meta_keywords' => trim((string) ($_POST['meta_keywords'] ?? '')),
            'meta_description' => trim((string) ($_POST['meta_description'] ?? '')),
            'status' => (string) ($_POST['status'] ?? '1'),
        ];
    }

    private function validatePost(array &$post, $exceptId)
    {
        if (!csrf_verify()) {
            return 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng tải lại trang.';
        }

        $titleLen = mb_strlen($post['title'], 'UTF-8');
        if ($titleLen < 3 || $titleLen > 255) {
            return 'Tiêu đề bài viết phải có từ 3 đến 255 ký tự.';
        }

        $slug = slugify($post['slug'] !== '' ? $post['slug'] : $post['title']);
        if (mb_strlen($slug, 'UTF-8') > 255) {
            return 'Slug không được vượt quá 255 ký tự.';
        }
        if ($this->postModel->slugExists($slug, $exceptId)) {
            return 'Đường dẫn (slug) "' . $slug . '" đã tồn tại. Vui lòng chọn slug khác.';
        }
        $post['slug'] = $slug;

        if (mb_strlen($post['content'], 'UTF-8') < 10) {
            return 'Nội dung bài viết phải có ít nhất 10 ký tự.';
        }
        if (mb_strlen($post['meta_keywords'], 'UTF-8') > 255) {
            return 'Từ khóa SEO không được vượt quá 255 ký tự.';
        }
        if (mb_strlen($post['meta_description'], 'UTF-8') > 320) {
            return 'Mô tả SEO không được vượt quá 320 ký tự.';
        }
        if (!in_array($post['status'], ['0', '1'], true)) {
            return 'Trạng thái bài viết không hợp lệ.';
        }

        return null;
    }
}
