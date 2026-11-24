<?php

/**
 * Đánh giá & Phản hồi sản phẩm (bảng reviews).
 * Thành viên gửi đánh giá, Nhân viên CSKH trả lời phản hồi & kiểm duyệt.
 */
class ReviewModel
{
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_HIDDEN = 2;

    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /** Các đánh giá được hiển thị của một sản phẩm (kèm tên khách & phản hồi CSKH). */
    public function getByProductId($productId)
    {
        $stmt = $this->db->prepare(
            'SELECT r.*, u.fullname AS author_name, s.fullname AS staff_name
             FROM reviews r
             JOIN users u ON u.id = r.user_id
             LEFT JOIN users s ON s.id = r.replied_by
             WHERE r.product_id = :product_id AND r.status = 1
             ORDER BY r.created_at DESC, r.id DESC'
        );
        $stmt->execute(['product_id' => (int) $productId]);

        return $stmt->fetchAll();
    }

    /** Điểm trung bình và tổng số đánh giá hiển thị của sản phẩm. */
    public function productRatingSummary($productId)
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) AS total, COALESCE(AVG(rating), 0) AS avg_rating
             FROM reviews
             WHERE product_id = :product_id AND status = 1'
        );
        $stmt->execute(['product_id' => (int) $productId]);
        $row = $stmt->fetch();

        return [
            'total' => (int) ($row['total'] ?? 0),
            'avg' => round((float) ($row['avg_rating'] ?? 0), 1),
        ];
    }

    /** Thành viên gửi đánh giá sản phẩm mới. */
    public function create($userId, $productId, $rating, $comment, $status = self::STATUS_APPROVED)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO reviews (user_id, product_id, rating, comment, status)
             VALUES (:user_id, :product_id, :rating, :comment, :status)'
        );
        $stmt->execute([
            'user_id' => (int) $userId,
            'product_id' => (int) $productId,
            'rating' => max(1, min(5, (int) $rating)),
            'comment' => $comment,
            'status' => (int) $status,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /** Danh sách phản hồi sản phẩm cho Nhân viên CSKH (lọc trạng thái / chưa trả lời + phân trang). */
    public function getForStaff($limit = 10, $offset = 0, $filter = 'all')
    {
        [$whereSql, $params] = $this->buildStaffWhere($filter);
        $sql = 'SELECT r.*, u.fullname AS author_name, u.email AS author_email,
                       p.name AS product_name, p.slug AS product_slug,
                       s.fullname AS staff_name
                FROM reviews r
                JOIN users u ON u.id = r.user_id
                JOIN products p ON p.id = r.product_id
                LEFT JOIN users s ON s.id = r.replied_by'
            . $whereSql
            . ' ORDER BY (r.staff_reply IS NULL OR r.staff_reply = "") DESC, r.created_at DESC, r.id DESC
                LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', max(0, (int) $offset), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countForStaff($filter = 'all')
    {
        [$whereSql, $params] = $this->buildStaffWhere($filter);
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM reviews r' . $whereSql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function countUnreplied()
    {
        return $this->countForStaff('unreplied');
    }

    public function reply($id, $replyText, $staffId)
    {
        $stmt = $this->db->prepare(
            'UPDATE reviews
             SET staff_reply = :reply, replied_by = :replied_by, replied_at = NOW(), status = 1
             WHERE id = :id'
        );

        return $stmt->execute([
            'reply' => $replyText,
            'replied_by' => (int) $staffId,
            'id' => (int) $id,
        ]);
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->db->prepare('UPDATE reviews SET status = :status WHERE id = :id');

        return $stmt->execute([
            'status' => (int) $status,
            'id' => (int) $id,
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM reviews WHERE id = :id');

        return $stmt->execute(['id' => (int) $id]);
    }

    private function buildStaffWhere($filter)
    {
        if ($filter === 'unreplied') {
            return [' WHERE (r.staff_reply IS NULL OR r.staff_reply = "")', []];
        }
        if ($filter === 'pending') {
            return [' WHERE r.status = :status', ['status' => self::STATUS_PENDING]];
        }
        if ($filter === 'approved') {
            return [' WHERE r.status = :status', ['status' => self::STATUS_APPROVED]];
        }
        if ($filter === 'hidden') {
            return [' WHERE r.status = :status', ['status' => self::STATUS_HIDDEN]];
        }

        return ['', []];
    }
}
