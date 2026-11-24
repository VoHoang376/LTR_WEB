<?php

class ContactModel
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create($fullname, $email, $phone, $message)
    {
        $statement = $this->db->prepare(
            'INSERT INTO contacts (fullname, email, phone, message, status, created_at)
             VALUES (:fullname, :email, :phone, :message, 0, NOW())'
        );
        $statement->execute([
            'fullname' => $fullname,
            'email' => $email,
            'phone' => $phone !== '' ? $phone : null,
            'message' => $message,
        ]);
        return $this->db->lastInsertId();
    }

    public function getPaginated($page = 1, $perPage = 10, $statusFilter = null)
    {
        $offset = max(0, ($page - 1) * $perPage);
        $sql = 'SELECT c.*, u.fullname AS handler_name
                FROM contacts c
                LEFT JOIN users u ON c.handled_by = u.id';
        $params = [];

        if ($statusFilter !== null && $statusFilter !== '') {
            $sql .= ' WHERE c.status = :status';
            $params['status'] = (int) $statusFilter;
        }

        $sql .= ' ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset';

        $statement = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $statement->bindValue(':' . $k, $v, PDO::PARAM_INT);
        }
        $statement->bindValue(':limit', (int) $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function countAll($statusFilter = null)
    {
        $sql = 'SELECT COUNT(*) FROM contacts';
        $params = [];

        if ($statusFilter !== null && $statusFilter !== '') {
            $sql .= ' WHERE status = :status';
            $params['status'] = (int) $statusFilter;
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return (int) $statement->fetchColumn();
    }

    public function countUnprocessed()
    {
        $statement = $this->db->query('SELECT COUNT(*) FROM contacts WHERE status = 0');
        return (int) $statement->fetchColumn();
    }

    public function findById($id)
    {
        $statement = $this->db->prepare(
            'SELECT c.*, u.fullname AS handler_name
             FROM contacts c
             LEFT JOIN users u ON c.handled_by = u.id
             WHERE c.id = :id LIMIT 1'
        );
        $statement->execute(['id' => (int) $id]);
        return $statement->fetch();
    }

    public function updateStatus($id, $status, $handledBy = null)
    {
        $statement = $this->db->prepare(
            'UPDATE contacts
             SET status = :status, is_read = 1, handled_by = :handled_by
             WHERE id = :id'
        );
        return $statement->execute([
            'status' => (int) $status,
            'handled_by' => $handledBy,
            'id' => (int) $id,
        ]);
    }

    public function reply($id, $replyMessage, $handledBy)
    {
        $statement = $this->db->prepare(
            'UPDATE contacts
             SET reply_message = :reply_message,
                 replied_at = NOW(),
                 status = 1,
                 is_read = 1,
                 handled_by = :handled_by
             WHERE id = :id'
        );
        return $statement->execute([
            'reply_message' => $replyMessage,
            'handled_by' => (int) $handledBy,
            'id' => (int) $id,
        ]);
    }

    public function delete($id)
    {
        $statement = $this->db->prepare('DELETE FROM contacts WHERE id = :id');
        return $statement->execute(['id' => (int) $id]);
    }
}
