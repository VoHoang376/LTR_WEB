<?php

class ContentModel
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function countAboutSections()
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM about_sections')->fetchColumn();
    }

    public function getAboutSections($limit = null, $offset = 0)
    {
        $sql = 'SELECT id, section_title, content, image_url, sort_order, created_at
                FROM about_sections ORDER BY sort_order ASC, id DESC';

        if ($limit === null) {
            return $this->db->query($sql)->fetchAll();
        }

        $statement = $this->db->prepare($sql . ' LIMIT :limit OFFSET :offset');
        $statement->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function findAboutSection($id)
    {
        $statement = $this->db->prepare(
            'SELECT id, section_title, content, image_url, sort_order, created_at
             FROM about_sections WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $section = $statement->fetch();

        return $section ?: null;
    }

    public function createAboutSection($title, $content, $imagePath, $sortOrder)
    {
        $statement = $this->db->prepare(
            'INSERT INTO about_sections (section_title, content, image_url, sort_order)
             VALUES (:title, :content, :image_url, :sort_order)'
        );
        $statement->execute([
            'title' => $title,
            'content' => $content,
            'image_url' => $imagePath,
            'sort_order' => $sortOrder,
        ]);

        return $this->db->lastInsertId();
    }

    public function updateAboutSection($id, $title, $content, $imagePath, $sortOrder)
    {
        $statement = $this->db->prepare(
            'UPDATE about_sections
             SET section_title = :title, content = :content, image_url = :image_url,
                 sort_order = :sort_order
             WHERE id = :id'
        );
        $statement->execute([
            'title' => $title,
            'content' => $content,
            'image_url' => $imagePath,
            'sort_order' => $sortOrder,
            'id' => $id,
        ]);
    }

    public function deleteAboutSection($id)
    {
        $statement = $this->db->prepare('DELETE FROM about_sections WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }

    public function countFaqs($statusFilter = null)
    {
        if ($statusFilter === true) {
            $statusFilter = 1;
        }
        if ($statusFilter === null || $statusFilter === false || $statusFilter === '') {
            return (int) $this->db->query('SELECT COUNT(*) FROM faqs')->fetchColumn();
        }

        $statement = $this->db->prepare('SELECT COUNT(*) FROM faqs WHERE status = :status');
        $statement->execute(['status' => (int) $statusFilter]);

        return (int) $statement->fetchColumn();
    }

    public function countPendingFaqs()
    {
        return $this->countFaqs(0);
    }

    public function getFaqs($limit, $offset, $statusFilter = null)
    {
        $sql = 'SELECT f.id, f.created_by, f.asker_name, f.asker_email, f.category,
                       f.question, f.answer, f.answered_by, f.answered_at,
                       f.sort_order, f.status, f.created_at,
                       u.fullname AS creator_name, s.fullname AS staff_name
                FROM faqs f
                LEFT JOIN users u ON u.id = f.created_by
                LEFT JOIN users s ON s.id = f.answered_by';
        $params = [];

        if ($statusFilter !== null && $statusFilter !== '') {
            $sql .= ' WHERE f.status = :status';
            $params['status'] = (int) $statusFilter;
        }

        $sql .= ' ORDER BY (f.status = 0) DESC, f.sort_order ASC, f.id DESC LIMIT :limit OFFSET :offset';

        $statement = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value, PDO::PARAM_INT);
        }
        $statement->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', max(0, (int) $offset), PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getPublishedFaqs()
    {
        $statement = $this->db->query(
            'SELECT f.id, f.category, f.question, f.answer, f.asker_name, f.answered_at,
                    s.fullname AS staff_name
             FROM faqs f
             LEFT JOIN users s ON s.id = f.answered_by
             WHERE f.status = 1 AND f.answer IS NOT NULL AND f.answer <> ""
             ORDER BY f.sort_order ASC, f.id ASC'
        );

        return $statement->fetchAll();
    }

    /** Các câu hỏi mà thành viên đang đăng nhập đã gửi (bao gồm cả câu đang chờ phản hồi). */
    public function getUserSubmittedFaqs($userId)
    {
        $statement = $this->db->prepare(
            'SELECT f.id, f.category, f.question, f.answer, f.status, f.created_at, f.answered_at,
                    s.fullname AS staff_name
             FROM faqs f
             LEFT JOIN users s ON s.id = f.answered_by
             WHERE f.created_by = :user_id
             ORDER BY f.created_at DESC, f.id DESC'
        );
        $statement->execute(['user_id' => (int) $userId]);

        return $statement->fetchAll();
    }

    public function findFaq($id)
    {
        $statement = $this->db->prepare(
            'SELECT f.*, u.fullname AS creator_name, s.fullname AS staff_name
             FROM faqs f
             LEFT JOIN users u ON u.id = f.created_by
             LEFT JOIN users s ON s.id = f.answered_by
             WHERE f.id = :id LIMIT 1'
        );
        $statement->execute(['id' => (int) $id]);
        $faq = $statement->fetch();

        return $faq ?: null;
    }

    /** Khách hàng / Thành viên gửi câu hỏi mới (mặc định status = 0: Chờ trả lời). */
    public function createCustomerQuestion($userId, $askerName, $askerEmail, $category, $question)
    {
        $statement = $this->db->prepare(
            'INSERT INTO faqs (created_by, asker_name, asker_email, category, question, answer, sort_order, status)
             VALUES (:created_by, :asker_name, :asker_email, :category, :question, NULL, 100, 0)'
        );
        $statement->execute([
            'created_by' => $userId !== null ? (int) $userId : null,
            'asker_name' => $askerName,
            'asker_email' => $askerEmail,
            'category' => $category !== '' ? $category : 'Câu hỏi khách hàng',
            'question' => $question,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function createFaq($createdBy, $category, $question, $answer, $sortOrder, $status, $askerName = null, $askerEmail = null)
    {
        $hasAnswer = trim((string) $answer) !== '';
        $statement = $this->db->prepare(
            'INSERT INTO faqs (created_by, asker_name, asker_email, category, question, answer, answered_by, answered_at, sort_order, status)
             VALUES (:created_by, :asker_name, :asker_email, :category, :question, :answer, :answered_by, :answered_at, :sort_order, :status)'
        );
        $statement->execute([
            'created_by' => $createdBy !== null ? (int) $createdBy : null,
            'asker_name' => $askerName,
            'asker_email' => $askerEmail,
            'category' => $category !== '' ? $category : null,
            'question' => $question,
            'answer' => $hasAnswer ? $answer : null,
            'answered_by' => $hasAnswer ? (int) $createdBy : null,
            'answered_at' => $hasAnswer ? date('Y-m-d H:i:s') : null,
            'sort_order' => (int) $sortOrder,
            'status' => (int) $status,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateFaq($id, $category, $question, $answer, $sortOrder, $status, $answeredBy = null)
    {
        $hasAnswer = trim((string) $answer) !== '';
        $statement = $this->db->prepare(
            'UPDATE faqs
             SET category = :category, question = :question, answer = :answer,
                 answered_by = CASE WHEN :has_answer = 1 THEN :answered_by ELSE answered_by END,
                 answered_at = CASE WHEN :has_answer_at = 1 THEN NOW() ELSE answered_at END,
                 sort_order = :sort_order, status = :status
             WHERE id = :id'
        );
        $statement->execute([
            'category' => $category !== '' ? $category : null,
            'question' => $question,
            'answer' => $hasAnswer ? $answer : null,
            'has_answer' => $hasAnswer && $answeredBy !== null ? 1 : 0,
            'answered_by' => $answeredBy !== null ? (int) $answeredBy : null,
            'has_answer_at' => $hasAnswer && $answeredBy !== null ? 1 : 0,
            'sort_order' => (int) $sortOrder,
            'status' => (int) $status,
            'id' => (int) $id,
        ]);
    }

    public function deleteFaq($id)
    {
        $statement = $this->db->prepare('DELETE FROM faqs WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }
}
