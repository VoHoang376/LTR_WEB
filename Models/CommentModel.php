<?php

/**
 * Bình luận bài viết (bảng comments – Thành viên #4).
 * Trạng thái: 0 = Chờ duyệt, 1 = Đã duyệt, 2 = Đã ẩn.
 */
class CommentModel
{
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_HIDDEN = 2;

    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /** Các bình luận đã duyệt của một bài viết (kèm tên & avatar người bình luận). */
    public function getCommentsByPostId($postId)
    {
        $stmt = $this->db->prepare(
            'SELECT c.id, c.post_id, c.user_id, c.content, c.status, c.created_at,
                    u.fullname AS author_name, u.avatar AS author_avatar
            FROM comments c
            JOIN users u ON u.id = c.user_id
            WHERE c.post_id = :post_id AND c.status = 1
            ORDER BY c.created_at DESC, c.id DESC'
        );
        $stmt->execute(['post_id' => (int) $postId]);

        return $stmt->fetchAll();
    }

    public function createComment($postId, $userId, $content, $status = self::STATUS_APPROVED)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO comments (post_id, user_id, content, status)
            VALUES (:post_id, :user_id, :content, :status)'
        );
        $stmt->execute([
            'post_id' => (int) $postId,
            'user_id' => (int) $userId,
            'content' => $content,
            'status' => (int) $status,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /** Danh sách bình luận cho trang quản trị (lọc trạng thái + phân trang). */
    public function getAllComments($limit = 15, $offset = 0, $statusFilter = null)
    {
        $sql = 'SELECT c.*, p.title AS post_title, p.slug AS post_slug,
                        u.fullname AS author_name, u.email AS author_email
                FROM comments c
                JOIN posts p ON p.id = c.post_id
                JOIN users u ON u.id = c.user_id';
        $params = [];

        if ($statusFilter !== null && $statusFilter !== '') {
            $sql .= ' WHERE c.status = :status';
            $params['status'] = (int) $statusFilter;
        }

        $sql .= ' ORDER BY c.created_at DESC, c.id DESC LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', max(0, (int) $offset), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countAll($statusFilter = null)
    {
        $sql = 'SELECT COUNT(*) FROM comments';
        $params = [];

        if ($statusFilter !== null && $statusFilter !== '') {
            $sql .= ' WHERE status = :status';
            $params['status'] = (int) $statusFilter;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function countHidden()
    {
        return $this->countAll(self::STATUS_HIDDEN);
    }

    public function countVisible()
    {
        return $this->countAll(self::STATUS_APPROVED);
    }

    public function updateCommentStatus($id, $status)
    {
        $stmt = $this->db->prepare('UPDATE comments SET status = :status WHERE id = :id');

        return $stmt->execute([
            'status' => (int) $status,
            'id' => (int) $id,
        ]);
    }

    public function deleteComment($id)
    {
        $stmt = $this->db->prepare('DELETE FROM comments WHERE id = :id');

        return $stmt->execute(['id' => (int) $id]);
    }
}
