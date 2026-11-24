<?php

/**
 * Tin tức / Bài viết (bảng posts – Thành viên #4).
 */
class PostModel
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /** Danh sách bài viết (tìm theo từ khoá, lọc trạng thái, phân trang). */
    public function getPosts($keyword = '', $limit = 10, $offset = 0, $onlyPublished = false)
    {
        $where = [];
        $params = [];

        if ($onlyPublished) {
            $where[] = 'p.status = 1';
        }

        $keyword = trim((string) $keyword);
        if ($keyword !== '') {
            $pattern = '%' . addcslashes($keyword, '\\%_') . '%';
            $where[] = '(p.title LIKE :kw_title OR p.summary LIKE :kw_summary OR p.meta_keywords LIKE :kw_meta)';
            $params['kw_title'] = $pattern;
            $params['kw_summary'] = $pattern;
            $params['kw_meta'] = $pattern;
        }

        $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);
        $sql = 'SELECT p.*, u.fullname AS author_name,
                       (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id AND c.status = 1) AS comment_count
                FROM posts p
                LEFT JOIN users u ON u.id = p.user_id'
            . $whereSql
            . ' ORDER BY p.created_at DESC, p.id DESC
                LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', max(0, (int) $offset), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** Đếm tổng số bài viết khớp điều kiện tìm kiếm/lọc. */
    public function getTotalPosts($keyword = '', $onlyPublished = false)
    {
        $where = [];
        $params = [];

        if ($onlyPublished) {
            $where[] = 'status = 1';
        }

        $keyword = trim((string) $keyword);
        if ($keyword !== '') {
            $pattern = '%' . addcslashes($keyword, '\\%_') . '%';
            $where[] = '(title LIKE :kw_title OR summary LIKE :kw_summary OR meta_keywords LIKE :kw_meta)';
            $params['kw_title'] = $pattern;
            $params['kw_summary'] = $pattern;
            $params['kw_meta'] = $pattern;
        }

        $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM posts' . $whereSql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /** Bài viết mới nhất cho Trang chủ. */
    public function getLatestPublished($limit = 3)
    {
        return $this->getPosts('', (int) $limit, 0, true);
    }

    public function getPostById($id, $onlyPublished = false)
    {
        $sql = 'SELECT p.*, u.fullname AS author_name
                FROM posts p
                LEFT JOIN users u ON u.id = p.user_id
                WHERE p.id = :id';
        if ($onlyPublished) {
            $sql .= ' AND p.status = 1';
        }
        $sql .= ' LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => (int) $id]);

        return $stmt->fetch() ?: null;
    }

    public function getPostBySlug($slug, $onlyPublished = false)
    {
        $sql = 'SELECT p.*, u.fullname AS author_name
                FROM posts p
                LEFT JOIN users u ON u.id = p.user_id
                WHERE p.slug = :slug';
        if ($onlyPublished) {
            $sql .= ' AND p.status = 1';
        }
        $sql .= ' LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => (string) $slug]);

        return $stmt->fetch() ?: null;
    }

    public function slugExists($slug, $exceptId = null)
    {
        if ($exceptId === null) {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM posts WHERE slug = :slug');
            $stmt->execute(['slug' => $slug]);
        } else {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM posts WHERE slug = :slug AND id <> :id');
            $stmt->execute(['slug' => $slug, 'id' => (int) $exceptId]);
        }

        return (int) $stmt->fetchColumn() > 0;
    }

    public function createPost($userId, $title, $slug, $summary, $content, $thumbnail, $metaKeywords, $metaDescription, $status = 1)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO posts (user_id, title, slug, summary, content, thumbnail, meta_keywords, meta_description, status)
             VALUES (:user_id, :title, :slug, :summary, :content, :thumbnail, :meta_keywords, :meta_description, :status)'
        );
        $stmt->execute([
            'user_id' => $userId !== null ? (int) $userId : null,
            'title' => $title,
            'slug' => $slug,
            'summary' => $summary !== '' ? $summary : null,
            'content' => $content,
            'thumbnail' => $thumbnail !== '' ? $thumbnail : null,
            'meta_keywords' => $metaKeywords !== '' ? $metaKeywords : null,
            'meta_description' => $metaDescription !== '' ? $metaDescription : null,
            'status' => (int) $status,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updatePost($id, $title, $slug, $summary, $content, $thumbnail, $metaKeywords, $metaDescription, $status = 1)
    {
        $sql = 'UPDATE posts
                SET title = :title,
                    slug = :slug,
                    summary = :summary,
                    content = :content,
                    meta_keywords = :meta_keywords,
                    meta_description = :meta_description,
                    status = :status';
        $params = [
            'title' => $title,
            'slug' => $slug,
            'summary' => $summary !== '' ? $summary : null,
            'content' => $content,
            'meta_keywords' => $metaKeywords !== '' ? $metaKeywords : null,
            'meta_description' => $metaDescription !== '' ? $metaDescription : null,
            'status' => (int) $status,
            'id' => (int) $id,
        ];

        if ($thumbnail !== null) {
            $sql .= ', thumbnail = :thumbnail';
            $params['thumbnail'] = $thumbnail !== '' ? $thumbnail : null;
        }
        $sql .= ' WHERE id = :id';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    public function deletePost($id)
    {
        $stmt = $this->db->prepare('DELETE FROM posts WHERE id = :id');

        return $stmt->execute(['id' => (int) $id]);
    }
}
