<?php

/**
 * Sản phẩm (bảng products) và thư viện ảnh phụ (bảng product_images).
 *
 * Mọi truy vấn đều dùng PDO prepared statement.
 */
class ProductModel
{
    /** Sắp xếp được phép (whitelist – không nối chuỗi từ tham số người dùng). */
    const SORTS = [
        'newest' => 'p.created_at DESC, p.id DESC',
        'price_asc' => 'p.price ASC, p.id DESC',
        'price_desc' => 'p.price DESC, p.id DESC',
        'name_asc' => 'p.name ASC, p.id DESC',
    ];

    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Tìm kiếm + lọc + sắp xếp + phân trang sản phẩm.
     *
     * $filters: q, category_slug, min_price, max_price, status, sort, page, per_page
     *
     * @return array{items:array,total:int,page:int,perPage:int,totalPages:int,offset:int,from:int,to:int}
     */
    public function search(array $filters = [])
    {
        $where = [];
        $params = [];

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== null) {
            $where[] = 'p.status = :status';
            $params['status'] = (int) $filters['status'];
        }

        $keyword = trim((string) ($filters['q'] ?? ''));
        if ($keyword !== '') {
            // PDO tắt emulate prepare nên mỗi tham số chỉ được dùng một lần.
            $where[] = '(p.name LIKE :keyword_name OR p.description LIKE :keyword_desc)';
            // Escape ký tự đại diện của LIKE để người dùng không tự thêm % _ .
            $keywordPattern = '%' . addcslashes($keyword, '\\%_') . '%';
            $params['keyword_name'] = $keywordPattern;
            $params['keyword_desc'] = $keywordPattern;
        }

        $categorySlug = trim((string) ($filters['category_slug'] ?? ''));
        if ($categorySlug !== '') {
            $where[] = 'p.category_id IN (SELECT id FROM categories WHERE slug = :category_slug)';
            $params['category_slug'] = $categorySlug;
        }

        $categoryId = isset($filters['category_id']) ? (int) $filters['category_id'] : 0;
        if ($categoryId > 0) {
            $where[] = 'p.category_id = :category_id';
            $params['category_id'] = $categoryId;
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $where[] = 'p.price >= :min_price';
            $params['min_price'] = (float) $filters['min_price'];
        }
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $where[] = 'p.price <= :max_price';
            $params['max_price'] = (float) $filters['max_price'];
        }

        $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);

        $countStatement = $this->db->prepare('SELECT COUNT(*) FROM products p' . $whereSql);
        $countStatement->execute($params);
        $total = (int) $countStatement->fetchColumn();

        $info = paginate(
            $total,
            isset($filters['per_page']) ? (int) $filters['per_page'] : 12,
            isset($filters['page']) ? (int) $filters['page'] : 1
        );

        $sort = (string) ($filters['sort'] ?? 'newest');
        $orderBy = self::SORTS[$sort] ?? self::SORTS['newest'];

        $sql = 'SELECT p.id, p.category_id, p.name, p.slug, p.price, p.stock, p.thumbnail,
                       p.description, p.specifications, p.status, p.created_at,
                       c.name AS category_name, c.slug AS category_slug
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id'
            . $whereSql
            . ' ORDER BY ' . $orderBy
            . ' LIMIT :limit OFFSET :offset';

        $statement = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }
        $statement->bindValue(':limit', $info['perPage'], PDO::PARAM_INT);
        $statement->bindValue(':offset', $info['offset'], PDO::PARAM_INT);
        $statement->execute();

        $info['items'] = $statement->fetchAll();

        return $info;
    }

    public function findById($id)
    {
        $statement = $this->db->prepare(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.id = :id LIMIT 1'
        );
        $statement->execute(['id' => (int) $id]);

        return $statement->fetch();
    }

    public function findBySlug($slug)
    {
        $statement = $this->db->prepare(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.slug = :slug LIMIT 1'
        );
        $statement->execute(['slug' => $slug]);

        return $statement->fetch();
    }

    /** Sản phẩm liên quan: cùng danh mục, đang bán, trừ chính nó. */
    public function related($product, $limit = 4)
    {
        $statement = $this->db->prepare(
            'SELECT p.id, p.name, p.slug, p.price, p.thumbnail, p.stock
             FROM products p
             WHERE p.category_id = :category_id AND p.id <> :id AND p.status = 1
             ORDER BY p.created_at DESC, p.id DESC
             LIMIT :limit'
        );
        $statement->bindValue(':category_id', (int) $product['category_id'], PDO::PARAM_INT);
        $statement->bindValue(':id', (int) $product['id'], PDO::PARAM_INT);
        $statement->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    /** Ảnh phụ của sản phẩm (thư viện ảnh ở trang chi tiết). */
    public function images($productId)
    {
        $statement = $this->db->prepare(
            'SELECT id, product_id, image, sort_order FROM product_images
             WHERE product_id = :product_id ORDER BY sort_order ASC, id ASC'
        );
        $statement->execute(['product_id' => (int) $productId]);

        return $statement->fetchAll();
    }

    public function addImage($productId, $image, $sortOrder = 0)
    {
        $statement = $this->db->prepare(
            'INSERT INTO product_images (product_id, image, sort_order)
             VALUES (:product_id, :image, :sort_order)'
        );
        $statement->execute([
            'product_id' => (int) $productId,
            'image' => $image,
            'sort_order' => (int) $sortOrder,
        ]);
    }

    public function findImage($imageId, $productId)
    {
        $statement = $this->db->prepare(
            'SELECT id, product_id, image, sort_order FROM product_images
             WHERE id = :id AND product_id = :product_id LIMIT 1'
        );
        $statement->execute(['id' => (int) $imageId, 'product_id' => (int) $productId]);

        return $statement->fetch();
    }

    public function deleteImage($imageId, $productId)
    {
        $statement = $this->db->prepare(
            'DELETE FROM product_images WHERE id = :id AND product_id = :product_id'
        );
        $statement->execute(['id' => (int) $imageId, 'product_id' => (int) $productId]);
    }

    public function create(array $data)
    {
        $statement = $this->db->prepare(
            'INSERT INTO products (category_id, name, slug, price, stock, thumbnail, description, specifications, status)
             VALUES (:category_id, :name, :slug, :price, :stock, :thumbnail, :description, :specifications, :status)'
        );
        $statement->execute([
            'category_id' => (int) $data['category_id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'price' => $data['price'],
            'stock' => (int) $data['stock'],
            'thumbnail' => $data['thumbnail'],
            'description' => $data['description'],
            'specifications' => $data['specifications'],
            'status' => (int) $data['status'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update($id, array $data)
    {
        $statement = $this->db->prepare(
            'UPDATE products SET category_id = :category_id, name = :name, slug = :slug,
                    price = :price, stock = :stock, thumbnail = :thumbnail,
                    description = :description, specifications = :specifications, status = :status
             WHERE id = :id'
        );
        $statement->execute([
            'category_id' => (int) $data['category_id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'price' => $data['price'],
            'stock' => (int) $data['stock'],
            'thumbnail' => $data['thumbnail'],
            'description' => $data['description'],
            'specifications' => $data['specifications'],
            'status' => (int) $data['status'],
            'id' => (int) $id,
        ]);
    }

    /** Xoá cứng sản phẩm (ảnh phụ tự xoá theo khoá ngoại). */
    public function delete($id)
    {
        $statement = $this->db->prepare('DELETE FROM products WHERE id = :id');
        $statement->execute(['id' => (int) $id]);
    }

    /** Bật/tắt trạng thái kinh doanh (dùng khi sản phẩm đã phát sinh đơn hàng). */
    public function setStatus($id, $status)
    {
        $statement = $this->db->prepare('UPDATE products SET status = :status WHERE id = :id');
        $statement->execute(['status' => (int) $status, 'id' => (int) $id]);
    }

    public function slugExists($slug, $exceptId = null)
    {
        if ($exceptId === null) {
            $statement = $this->db->prepare('SELECT COUNT(*) FROM products WHERE slug = :slug');
            $statement->execute(['slug' => $slug]);
        } else {
            $statement = $this->db->prepare('SELECT COUNT(*) FROM products WHERE slug = :slug AND id <> :id');
            $statement->execute(['slug' => $slug, 'id' => (int) $exceptId]);
        }

        return (int) $statement->fetchColumn() > 0;
    }

    /** Sản phẩm đã từng nằm trong đơn hàng chưa (để quyết định xoá cứng hay chỉ ẩn). */
    public function countOrderLines($id)
    {
        $statement = $this->db->prepare('SELECT COUNT(*) FROM order_details WHERE product_id = :id');
        $statement->execute(['id' => (int) $id]);

        return (int) $statement->fetchColumn();
    }

    /** Nạp nhiều sản phẩm theo danh sách id (dùng cho giỏ hàng). */
    public function findManyByIds(array $ids)
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($ids === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $statement = $this->db->prepare(
            "SELECT p.id, p.category_id, p.name, p.slug, p.price, p.stock, p.thumbnail, p.status,
                    c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.id IN ($placeholders)"
        );
        $statement->execute($ids);

        $products = [];
        foreach ($statement->fetchAll() as $row) {
            $products[(int) $row['id']] = $row;
        }

        return $products;
    }

    /** Thống kê nhanh cho trang quản trị sản phẩm. */
    public function stats()
    {
        return $this->db->query(
            "SELECT COUNT(*) AS total,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS selling,
                    SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END) AS out_of_stock
             FROM products"
        )->fetch();
    }
}
