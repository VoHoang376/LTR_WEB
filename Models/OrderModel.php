<?php

/**
 * Đơn hàng (bảng orders) và chi tiết đơn (bảng order_details).
 *
 * Nguyên tắc bảo mật:
 *   - Giá và tồn kho luôn đọc lại từ CSDL bên trong transaction, không dùng
 *     giá do client gửi lên.
 *   - Tạo đơn + chi tiết đơn + trừ tồn kho nằm trong cùng một transaction,
 *     lỗi ở bất kỳ bước nào đều rollback.
 */
class OrderModel
{
    const STATUS_PENDING = 0;   // Chờ xác nhận
    const STATUS_CONFIRMED = 1; // Đã xác nhận
    const STATUS_SHIPPING = 2;  // Đang giao
    const STATUS_COMPLETED = 3; // Hoàn tất
    const STATUS_CANCELLED = 4; // Đã hủy

    /** Luồng trạng thái hợp lệ: trạng thái hiện tại => các trạng thái được chuyển tới. */
    const TRANSITIONS = [
        self::STATUS_PENDING => [self::STATUS_CONFIRMED, self::STATUS_CANCELLED],
        self::STATUS_CONFIRMED => [self::STATUS_SHIPPING, self::STATUS_CANCELLED],
        self::STATUS_SHIPPING => [self::STATUS_COMPLETED],
        self::STATUS_COMPLETED => [],
        self::STATUS_CANCELLED => [],
    ];

    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Tạo đơn hàng trong một transaction.
     *
     * @param int   $userId   ID thành viên đặt hàng.
     * @param array $receiver receiver_name, receiver_phone, shipping_address, payment_method, note
     * @param array $items    [['product_id' => int, 'quantity' => int], ...]
     * @return int ID đơn hàng vừa tạo.
     * @throws RuntimeException khi giỏ hàng không hợp lệ hoặc thiếu tồn kho.
     */
    public function create($userId, array $receiver, array $items)
    {
        if ($items === []) {
            throw new RuntimeException('Giỏ hàng của bạn đang trống.');
        }

        $quantities = [];
        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);
            if ($productId <= 0 || $quantity <= 0) {
                throw new RuntimeException('Số lượng sản phẩm không hợp lệ.');
            }
            if ($quantity > 99) {
                throw new RuntimeException('Mỗi sản phẩm chỉ được đặt tối đa 99 sản phẩm.');
            }
            $quantities[$productId] = ($quantities[$productId] ?? 0) + $quantity;
        }

        $this->db->beginTransaction();

        try {
            $placeholders = implode(',', array_fill(0, count($quantities), '?'));
            // Khoá dòng sản phẩm để tránh hai người mua cùng lúc làm âm tồn kho.
            $statement = $this->db->prepare(
                "SELECT id, name, price, stock, status FROM products
                 WHERE id IN ($placeholders) FOR UPDATE"
            );
            $statement->execute(array_keys($quantities));

            $products = [];
            foreach ($statement->fetchAll() as $row) {
                $products[(int) $row['id']] = $row;
            }

            $lines = [];
            $total = 0.0;

            foreach ($quantities as $productId => $quantity) {
                if (!isset($products[$productId])) {
                    throw new RuntimeException('Một sản phẩm trong giỏ không còn tồn tại.');
                }
                $product = $products[$productId];
                if ((int) $product['status'] !== 1) {
                    throw new RuntimeException('Sản phẩm "' . $product['name'] . '" đã ngừng kinh doanh.');
                }
                if ((int) $product['stock'] < $quantity) {
                    throw new RuntimeException(
                        'Sản phẩm "' . $product['name'] . '" chỉ còn ' . (int) $product['stock'] . ' trong kho.'
                    );
                }

                // Giá lấy từ CSDL, KHÔNG lấy từ form/session của người dùng.
                $price = (float) $product['price'];
                $lines[] = [
                    'product_id' => $productId,
                    'product_name' => $product['name'],
                    'price' => $price,
                    'quantity' => $quantity,
                ];
                $total += round($price * $quantity, 2);
            }

            $total = round($total, 2);

            $insertOrder = $this->db->prepare(
                'INSERT INTO orders (user_id, receiver_name, receiver_phone, shipping_address,
                                     payment_method, note, total_amount, status)
                 VALUES (:user_id, :receiver_name, :receiver_phone, :shipping_address,
                         :payment_method, :note, :total_amount, :status)'
            );
            $insertOrder->execute([
                'user_id' => (int) $userId,
                'receiver_name' => $receiver['receiver_name'],
                'receiver_phone' => $receiver['receiver_phone'],
                'shipping_address' => $receiver['shipping_address'],
                'payment_method' => (int) $receiver['payment_method'],
                'note' => $receiver['note'] !== '' ? $receiver['note'] : null,
                'total_amount' => number_format($total, 2, '.', ''),
                'status' => self::STATUS_PENDING,
            ]);
            $orderId = (int) $this->db->lastInsertId();

            $insertDetail = $this->db->prepare(
                'INSERT INTO order_details (order_id, product_id, product_name, quantity, price)
                 VALUES (:order_id, :product_id, :product_name, :quantity, :price)'
            );
            $updateStock = $this->db->prepare(
                'UPDATE products SET stock = stock - :quantity
                 WHERE id = :product_id AND stock >= :quantity_check'
            );

            foreach ($lines as $line) {
                $insertDetail->execute([
                    'order_id' => $orderId,
                    'product_id' => $line['product_id'],
                    'product_name' => $line['product_name'],
                    'quantity' => $line['quantity'],
                    'price' => number_format($line['price'], 2, '.', ''),
                ]);

                $updateStock->execute([
                    'quantity' => $line['quantity'],
                    'product_id' => $line['product_id'],
                    'quantity_check' => $line['quantity'],
                ]);
                if ($updateStock->rowCount() !== 1) {
                    throw new RuntimeException('Không thể cập nhật tồn kho, vui lòng thử lại.');
                }
            }

            $this->db->commit();

            return $orderId;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }
    }

    /** Đơn hàng của một thành viên, có phân trang. */
    public function paginateByUser($userId, $page = 1, $perPage = 10)
    {
        $countStatement = $this->db->prepare('SELECT COUNT(*) FROM orders WHERE user_id = :user_id');
        $countStatement->execute(['user_id' => (int) $userId]);
        $info = paginate((int) $countStatement->fetchColumn(), $perPage, $page);

        $statement = $this->db->prepare(
            'SELECT o.id, o.total_amount, o.status, o.payment_method, o.created_at,
                    (SELECT COUNT(*) FROM order_details od WHERE od.order_id = o.id) AS item_count
             FROM orders o
             WHERE o.user_id = :user_id
             ORDER BY o.created_at DESC, o.id DESC
             LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue(':user_id', (int) $userId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $info['perPage'], PDO::PARAM_INT);
        $statement->bindValue(':offset', $info['offset'], PDO::PARAM_INT);
        $statement->execute();

        $info['items'] = $statement->fetchAll();

        return $info;
    }

    /** Đơn hàng thuộc về đúng thành viên đang đăng nhập (chặn xem đơn người khác). */
    public function findForUser($orderId, $userId)
    {
        $statement = $this->db->prepare(
            'SELECT o.*, u.fullname AS user_name, u.email AS user_email
             FROM orders o
             JOIN users u ON u.id = o.user_id
             WHERE o.id = :id AND o.user_id = :user_id LIMIT 1'
        );
        $statement->execute(['id' => (int) $orderId, 'user_id' => (int) $userId]);

        return $statement->fetch();
    }

    public function findById($orderId)
    {
        $statement = $this->db->prepare(
            'SELECT o.*, u.fullname AS user_name, u.email AS user_email
             FROM orders o
             JOIN users u ON u.id = o.user_id
             WHERE o.id = :id LIMIT 1'
        );
        $statement->execute(['id' => (int) $orderId]);

        return $statement->fetch();
    }

    /** Chi tiết đơn: kèm ảnh/slug sản phẩm hiện tại (nếu sản phẩm còn). */
    public function items($orderId)
    {
        $statement = $this->db->prepare(
            'SELECT od.order_id, od.product_id, od.product_name, od.quantity, od.price,
                    p.slug AS product_slug, p.thumbnail, p.status AS product_status
             FROM order_details od
             LEFT JOIN products p ON p.id = od.product_id
             WHERE od.order_id = :order_id
             ORDER BY od.product_name ASC'
        );
        $statement->execute(['order_id' => (int) $orderId]);

        return $statement->fetchAll();
    }

    /** Danh sách đơn cho quản trị: tìm kiếm + lọc trạng thái + phân trang. */
    public function search(array $filters = [])
    {
        $where = [];
        $params = [];

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== null) {
            $where[] = 'o.status = :status';
            $params['status'] = (int) $filters['status'];
        }

        $keyword = trim((string) ($filters['q'] ?? ''));
        if ($keyword !== '') {
            $keywordPattern = '%' . addcslashes($keyword, '\\%_') . '%';
            // PDO tắt emulate prepare nên không dùng lại cùng một tham số nhiều lần.
            if (ctype_digit($keyword)) {
                $where[] = '(o.id = :order_id OR o.receiver_name LIKE :kw_name
                             OR o.receiver_phone LIKE :kw_phone OR u.email LIKE :kw_email)';
                $params['order_id'] = (int) $keyword;
                $params['kw_name'] = $keywordPattern;
                $params['kw_phone'] = $keywordPattern;
                $params['kw_email'] = $keywordPattern;
            } else {
                $where[] = '(o.receiver_name LIKE :kw_name OR o.receiver_phone LIKE :kw_phone
                             OR u.email LIKE :kw_email OR u.fullname LIKE :kw_user)';
                $params['kw_name'] = $keywordPattern;
                $params['kw_phone'] = $keywordPattern;
                $params['kw_email'] = $keywordPattern;
                $params['kw_user'] = $keywordPattern;
            }
        }

        $dateFrom = trim((string) ($filters['date_from'] ?? ''));
        if ($dateFrom !== '') {
            $where[] = 'o.created_at >= :date_from';
            $params['date_from'] = $dateFrom . ' 00:00:00';
        }
        $dateTo = trim((string) ($filters['date_to'] ?? ''));
        if ($dateTo !== '') {
            $where[] = 'o.created_at <= :date_to';
            $params['date_to'] = $dateTo . ' 23:59:59';
        }

        $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);

        $countStatement = $this->db->prepare(
            'SELECT COUNT(*) FROM orders o JOIN users u ON u.id = o.user_id' . $whereSql
        );
        $countStatement->execute($params);
        $info = paginate(
            (int) $countStatement->fetchColumn(),
            isset($filters['per_page']) ? (int) $filters['per_page'] : 10,
            isset($filters['page']) ? (int) $filters['page'] : 1
        );

        $statement = $this->db->prepare(
            'SELECT o.id, o.user_id, o.receiver_name, o.receiver_phone, o.shipping_address,
                    o.total_amount, o.status, o.payment_method, o.created_at,
                    u.fullname AS user_name, u.email AS user_email,
                    (SELECT COALESCE(SUM(od.quantity), 0) FROM order_details od WHERE od.order_id = o.id) AS total_quantity
             FROM orders o
             JOIN users u ON u.id = o.user_id'
            . $whereSql
            . ' ORDER BY o.created_at DESC, o.id DESC LIMIT :limit OFFSET :offset'
        );
        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }
        $statement->bindValue(':limit', $info['perPage'], PDO::PARAM_INT);
        $statement->bindValue(':offset', $info['offset'], PDO::PARAM_INT);
        $statement->execute();

        $info['items'] = $statement->fetchAll();

        return $info;
    }

    /**
     * Đổi trạng thái đơn hàng theo luồng hợp lệ; hủy đơn thì hoàn lại tồn kho.
     *
     * @throws RuntimeException khi bước chuyển trạng thái không hợp lệ.
     */
    public function updateStatus($orderId, $newStatus)
    {
        $newStatus = (int) $newStatus;

        $this->db->beginTransaction();

        try {
            $statement = $this->db->prepare('SELECT id, status FROM orders WHERE id = :id FOR UPDATE');
            $statement->execute(['id' => (int) $orderId]);
            $order = $statement->fetch();

            if (!$order) {
                throw new RuntimeException('Không tìm thấy đơn hàng.');
            }

            $current = (int) $order['status'];
            $allowed = self::TRANSITIONS[$current] ?? [];
            if (!in_array($newStatus, $allowed, true)) {
                throw new RuntimeException(
                    'Không thể chuyển từ "' . order_status_label($current) . '" sang "'
                    . order_status_label($newStatus) . '".'
                );
            }

            if ($newStatus === self::STATUS_CANCELLED) {
                // Hoàn tồn kho đúng một lần khi hủy đơn.
                $restore = $this->db->prepare(
                    'UPDATE products p
                     JOIN order_details od ON od.product_id = p.id
                     SET p.stock = p.stock + od.quantity
                     WHERE od.order_id = :order_id'
                );
                $restore->execute(['order_id' => (int) $orderId]);
            }

            $update = $this->db->prepare('UPDATE orders SET status = :status WHERE id = :id');
            $update->execute(['status' => $newStatus, 'id' => (int) $orderId]);

            $this->db->commit();

            return true;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }
    }

    /** Số đơn theo từng trạng thái (hiển thị tab lọc). */
    public function statusCounts()
    {
        $counts = array_fill_keys(array_keys(self::TRANSITIONS), 0);
        $rows = $this->db->query('SELECT status, COUNT(*) AS total FROM orders GROUP BY status')->fetchAll();
        foreach ($rows as $row) {
            $counts[(int) $row['status']] = (int) $row['total'];
        }

        return $counts;
    }

    /** Doanh thu của các đơn đã hoàn tất (dùng cho báo cáo/thống kê nhanh). */
    public function completedRevenue()
    {
        $statement = $this->db->prepare(
            'SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = :status'
        );
        $statement->execute(['status' => self::STATUS_COMPLETED]);

        return (float) $statement->fetchColumn();
    }
}
