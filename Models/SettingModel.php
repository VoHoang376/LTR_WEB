<?php

class SettingModel
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        $statement = $this->db->query('SELECT key_name, key_value FROM settings');
        $rows = $statement->fetchAll();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key_name']] = $row['key_value'];
        }
        return $settings;
    }

    public function get($key, $default = '')
    {
        $statement = $this->db->prepare('SELECT key_value FROM settings WHERE key_name = :key LIMIT 1');
        $statement->execute(['key' => $key]);
        $val = $statement->fetchColumn();
        return $val !== false ? $val : $default;
    }

    public function saveMultiple(array $data, $updatedBy = null)
    {
        $sql = 'INSERT INTO settings (key_name, key_value, updated_by)
                VALUES (:key_name, :key_value, :updated_by)
                ON DUPLICATE KEY UPDATE
                    key_value = VALUES(key_value),
                    updated_by = VALUES(updated_by),
                    updated_at = NOW()';
        $statement = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $statement->execute([
                'key_name' => (string) $key,
                'key_value' => (string) $value,
                'updated_by' => $updatedBy,
            ]);
        }
    }
}
