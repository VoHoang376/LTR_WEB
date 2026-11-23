<?php

/**
 * Danh mục sản phẩm (bảng categories).
 */
class CategoryModel
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /** Tất cả danh mục, kèm số sản phẩm của mỗi danh mục. */
    public function all($onlyVisible = false)
    {
        $sql = 'SELECT c.id, c.name, c.slug, c.status,
                       (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS product_count
                FROM categories c';
        if ($onlyVisible) {
            $sql .= ' WHERE c.status = 1';
        }
        $sql .= ' ORDER BY c.name ASC';

        return $this->db->query($sql)->fetchAll();
    }

    public function findById($id)
    {
        $statement = $this->db->prepare(
            'SELECT id, name, slug, status FROM categories WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => (int) $id]);

        return $statement->fetch();
    }

    public function findBySlug($slug)
    {
        $statement = $this->db->prepare(
            'SELECT id, name, slug, status FROM categories WHERE slug = :slug LIMIT 1'
        );
        $statement->execute(['slug' => $slug]);

        return $statement->fetch();
    }

    /** Kiểm tra trùng slug; $exceptId dùng khi sửa. */
    public function slugExists($slug, $exceptId = null)
    {
        if ($exceptId === null) {
            $statement = $this->db->prepare('SELECT COUNT(*) FROM categories WHERE slug = :slug');
            $statement->execute(['slug' => $slug]);
        } else {
            $statement = $this->db->prepare('SELECT COUNT(*) FROM categories WHERE slug = :slug AND id <> :id');
            $statement->execute(['slug' => $slug, 'id' => (int) $exceptId]);
        }

        return (int) $statement->fetchColumn() > 0;
    }

    public function create($name, $slug, $status)
    {
        $statement = $this->db->prepare(
            'INSERT INTO categories (name, slug, status) VALUES (:name, :slug, :status)'
        );
        $statement->execute([
            'name' => $name,
            'slug' => $slug,
            'status' => (int) $status,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update($id, $name, $slug, $status)
    {
        $statement = $this->db->prepare(
            'UPDATE categories SET name = :name, slug = :slug, status = :status WHERE id = :id'
        );
        $statement->execute([
            'name' => $name,
            'slug' => $slug,
            'status' => (int) $status,
            'id' => (int) $id,
        ]);
    }

    public function delete($id)
    {
        $statement = $this->db->prepare('DELETE FROM categories WHERE id = :id');
        $statement->execute(['id' => (int) $id]);
    }

    /** Đếm số sản phẩm thuộc danh mục (chặn xoá danh mục đang có sản phẩm). */
    public function countProducts($id)
    {
        $statement = $this->db->prepare('SELECT COUNT(*) FROM products WHERE category_id = :id');
        $statement->execute(['id' => (int) $id]);

        return (int) $statement->fetchColumn();
    }
}
