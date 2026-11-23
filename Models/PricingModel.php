<?php

class PricingModel
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllActive()
    {
        $statement = $this->db->query(
            'SELECT * FROM pricing_plans WHERE status = 1 ORDER BY sort_order ASC, id ASC'
        );
        return $statement->fetchAll();
    }

    public function getAll()
    {
        $statement = $this->db->query(
            'SELECT * FROM pricing_plans ORDER BY sort_order ASC, id ASC'
        );
        return $statement->fetchAll();
    }

    public function findById($id)
    {
        $statement = $this->db->prepare('SELECT * FROM pricing_plans WHERE id = :id LIMIT 1');
        $statement->execute(['id' => (int) $id]);
        return $statement->fetch();
    }

    public function create($name, $price, $billingCycle, $description, $features, $isFeatured, $sortOrder, $status)
    {
        $statement = $this->db->prepare(
            'INSERT INTO pricing_plans (name, price, billing_cycle, description, features, is_featured, sort_order, status, created_at)
             VALUES (:name, :price, :billing_cycle, :description, :features, :is_featured, :sort_order, :status, NOW())'
        );
        return $statement->execute([
            'name' => $name,
            'price' => (float) $price,
            'billing_cycle' => $billingCycle ?: 'tháng',
            'description' => $description,
            'features' => $features,
            'is_featured' => (int) $isFeatured,
            'sort_order' => (int) $sortOrder,
            'status' => (int) $status,
        ]);
    }

    public function update($id, $name, $price, $billingCycle, $description, $features, $isFeatured, $sortOrder, $status)
    {
        $statement = $this->db->prepare(
            'UPDATE pricing_plans
             SET name = :name,
                 price = :price,
                 billing_cycle = :billing_cycle,
                 description = :description,
                 features = :features,
                 is_featured = :is_featured,
                 sort_order = :sort_order,
                 status = :status
             WHERE id = :id'
        );
        return $statement->execute([
            'name' => $name,
            'price' => (float) $price,
            'billing_cycle' => $billingCycle ?: 'tháng',
            'description' => $description,
            'features' => $features,
            'is_featured' => (int) $isFeatured,
            'sort_order' => (int) $sortOrder,
            'status' => (int) $status,
            'id' => (int) $id,
        ]);
    }

    public function delete($id)
    {
        $statement = $this->db->prepare('DELETE FROM pricing_plans WHERE id = :id');
        return $statement->execute(['id' => (int) $id]);
    }
}
