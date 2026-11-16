<?php

class UserModel
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findByEmail($email)
    {
        $statement = $this->db->prepare(
            'SELECT id, email, password, fullname, role, status
             FROM users WHERE email = :email LIMIT 1'
        );
        $statement->execute(['email' => $email]);

        return $statement->fetch();
    }

    public function findById($id)
    {
        $statement = $this->db->prepare(
            'SELECT id, email, fullname, phone, address, avatar, role, status, created_at
             FROM users WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);

        return $statement->fetch();
    }

    public function findPasswordById($id)
    {
        $statement = $this->db->prepare('SELECT password FROM users WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $user = $statement->fetch();

        return $user ? $user['password'] : null;
    }

    public function create($email, $passwordHash, $fullname, $phone, $address)
    {
        $statement = $this->db->prepare(
            'INSERT INTO users (email, password, fullname, phone, address, role, status)
             VALUES (:email, :password, :fullname, :phone, :address, 1, 1)'
        );
        $statement->execute([
            'email' => $email,
            'password' => $passwordHash,
            'fullname' => $fullname,
            'phone' => $phone,
            'address' => $address,
        ]);

        return $this->db->lastInsertId();
    }

    public function updateProfile($id, $fullname, $phone, $address, $avatar)
    {
        $statement = $this->db->prepare(
            'UPDATE users
             SET fullname = :fullname, phone = :phone, address = :address, avatar = :avatar
             WHERE id = :id'
        );
        $statement->execute([
            'fullname' => $fullname,
            'phone' => $phone !== '' ? $phone : null,
            'address' => $address !== '' ? $address : null,
            'avatar' => $avatar,
            'id' => $id,
        ]);
    }

    public function updatePassword($id, $passwordHash)
    {
        $statement = $this->db->prepare('UPDATE users SET password = :password WHERE id = :id');
        $statement->execute([
            'password' => $passwordHash,
            'id' => $id,
        ]);
    }
}
