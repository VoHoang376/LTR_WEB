<?php

require_once __DIR__ . '/../Models/OrderModel.php';

/**
 * Quản trị đơn hàng: danh sách (lọc trạng thái, tìm kiếm, phân trang),
 * xem chi tiết và đánh dấu trạng thái theo đúng luồng nghiệp vụ.
 */
class AdminOrderController
{
    const PER_PAGE = 10;

    private $orderModel;

    public function __construct(PDO $db)
    {
        Permission::requireAdmin();
        $this->orderModel = new OrderModel($db);
    }

    public function index()
    {
        $filters = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'status' => $_GET['status'] ?? '',
            'date_from' => trim((string) ($_GET['date_from'] ?? '')),
            'date_to' => trim((string) ($_GET['date_to'] ?? '')),
            'page' => max(1, (int) ($_GET['page'] ?? 1)),
            'per_page' => self::PER_PAGE,
        ];

        $result = $this->orderModel->search($filters);
        $statusCounts = $this->orderModel->statusCounts();
        $revenue = $this->orderModel->completedRevenue();

        $pageTitle = 'Quản lý đơn hàng';
        $activeMenu = 'orders';
        $breadcrumb = [['label' => 'Đơn hàng', 'url' => null]];

        require __DIR__ . '/../Views/Backends/orders/index.php';
    }

    public function show()
    {
        $orderId = (int) ($_GET['id'] ?? 0);
        $order = $this->orderModel->findById($orderId);
        if (!$order) {
            flash_set('admin', 'Không tìm thấy đơn hàng.', 'danger');
            redirect('index.php?controller=admin&action=orders');
        }

        $items = $this->orderModel->items($orderId);
        $allowedStatuses = OrderModel::TRANSITIONS[(int) $order['status']] ?? [];
        $pageTitle = 'Đơn hàng #' . str_pad((string) $orderId, 6, '0', STR_PAD_LEFT);
        $activeMenu = 'orders';
        $breadcrumb = [
            ['label' => 'Đơn hàng', 'url' => 'index.php?controller=admin&action=orders'],
            ['label' => 'Chi tiết', 'url' => null],
        ];

        require __DIR__ . '/../Views/Backends/orders/show.php';
    }

    /** Đổi trạng thái đơn (POST + CSRF), kiểm tra luồng trong OrderModel. */
    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
            flash_set('admin', 'Yêu cầu không hợp lệ (cần POST và CSRF).', 'danger');
            redirect('index.php?controller=admin&action=orders');
        }

        $orderId = (int) ($_POST['order_id'] ?? 0);
        $newStatus = (int) ($_POST['status'] ?? -1);

        try {
            $this->orderModel->updateStatus($orderId, $newStatus);
            flash_set(
                'admin',
                'Đã cập nhật đơn hàng #' . str_pad((string) $orderId, 6, '0', STR_PAD_LEFT)
                . ' sang "' . order_status_label($newStatus) . '".',
                'success'
            );
        } catch (RuntimeException $exception) {
            flash_set('admin', $exception->getMessage(), 'danger');
        }

        redirect('index.php?controller=admin&action=order-show&id=' . $orderId);
    }
}
