<?php

require_once __DIR__ . '/../Models/OrderModel.php';

/**
 * Lịch sử đơn hàng của thành viên đang đăng nhập.
 * Mọi action đều yêu cầu đăng nhập và chỉ thao tác trên đơn của chính mình.
 */
class OrderController
{
    private $orderModel;

    public function __construct(PDO $db)
    {
        Permission::requireMember('Mục "Đơn hàng của tôi" chỉ dành cho tài khoản Thành viên (Khách hàng). Quản trị viên vui lòng xem tại mục Quản lý đơn hàng.');
        $this->orderModel = new OrderModel($db);
    }

    public function index()
    {
        $result = $this->orderModel->paginateByUser(
            (int) Permission::id(),
            max(1, (int) ($_GET['page'] ?? 1)),
            8
        );

        $pageTitle = 'Đơn hàng của tôi';
        $metaDescription = 'Danh sách đơn hàng bạn đã đặt tại LTR Technology.';
        $activeNav = 'orders';

        require __DIR__ . '/../Views/Frontends/orders/index.php';
    }

    public function show()
    {
        $orderId = (int) ($_GET['id'] ?? 0);
        $order = $this->orderModel->findForUser($orderId, (int) Permission::id());
        if (!$order) {
            http_response_code(404);
            $pageTitle = 'Không tìm thấy đơn hàng';
            require __DIR__ . '/../Views/Frontends/errors/404.php';

            return;
        }

        $items = $this->orderModel->items($orderId);
        $canCancel = (int) $order['status'] === OrderModel::STATUS_PENDING;
        $pageTitle = 'Chi tiết đơn hàng #' . str_pad((string) $orderId, 6, '0', STR_PAD_LEFT);
        $metaDescription = 'Chi tiết đơn hàng tại LTR Technology.';
        $activeNav = 'orders';

        require __DIR__ . '/../Views/Frontends/orders/show.php';
    }

    /** Thành viên tự huỷ đơn khi đơn còn "Chờ xác nhận". */
    public function cancel()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
            flash_set('orders', 'Yêu cầu không hợp lệ, vui lòng thử lại.', 'danger');
            redirect('index.php?controller=order&action=index');
        }

        $orderId = (int) ($_POST['order_id'] ?? 0);
        $order = $this->orderModel->findForUser($orderId, (int) Permission::id());
        if (!$order) {
            flash_set('orders', 'Không tìm thấy đơn hàng.', 'danger');
            redirect('index.php?controller=order&action=index');
        }

        try {
            $this->orderModel->updateStatus($orderId, OrderModel::STATUS_CANCELLED);
            flash_set('orders', 'Đã huỷ đơn hàng #' . str_pad((string) $orderId, 6, '0', STR_PAD_LEFT) . '.', 'success');
        } catch (RuntimeException $exception) {
            flash_set('orders', $exception->getMessage(), 'danger');
        }

        redirect('index.php?controller=order&action=show&id=' . $orderId);
    }
}
