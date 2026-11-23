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

    public function countFaqs($publishedOnly = false)
    {
        $sql = 'SELECT COUNT(*) FROM faqs';
        if ($publishedOnly) {
            $sql .= ' WHERE status = 1';
        }

        return (int) $this->db->query($sql)->fetchColumn();
    }

    public function getFaqs($limit, $offset)
    {
        $statement = $this->db->prepare(
            'SELECT id, created_by, category, question, answer, sort_order, status, created_at
             FROM faqs ORDER BY sort_order ASC, id DESC LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getPublishedFaqs()
    {
        $statement = $this->db->query(
            'SELECT id, category, question, answer
             FROM faqs WHERE status = 1 ORDER BY sort_order ASC, id ASC'
        );

        return $statement->fetchAll();
    }

    public function findFaq($id)
    {
        $statement = $this->db->prepare(
            'SELECT id, created_by, category, question, answer, sort_order, status, created_at
             FROM faqs WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $faq = $statement->fetch();

        return $faq ?: null;
    }

    public function createFaq($createdBy, $category, $question, $answer, $sortOrder, $status)
    {
        $statement = $this->db->prepare(
            'INSERT INTO faqs (created_by, category, question, answer, sort_order, status)
             VALUES (:created_by, :category, :question, :answer, :sort_order, :status)'
        );
        $statement->execute([
            'created_by' => $createdBy,
            'category' => $category !== '' ? $category : null,
            'question' => $question,
            'answer' => $answer,
            'sort_order' => $sortOrder,
            'status' => $status,
        ]);

        return $this->db->lastInsertId();
    }

    public function updateFaq($id, $category, $question, $answer, $sortOrder, $status)
    {
        $statement = $this->db->prepare(
            'UPDATE faqs
             SET category = :category, question = :question, answer = :answer,
                 sort_order = :sort_order, status = :status
             WHERE id = :id'
        );
        $statement->execute([
            'category' => $category !== '' ? $category : null,
            'question' => $question,
            'answer' => $answer,
            'sort_order' => $sortOrder,
            'status' => $status,
            'id' => $id,
        ]);
    }

    public function deleteFaq($id)
    {
        $statement = $this->db->prepare('DELETE FROM faqs WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }
}
