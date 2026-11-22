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
}
