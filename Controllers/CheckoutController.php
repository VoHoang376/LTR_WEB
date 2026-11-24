<?php

require_once __DIR__ . '/../Models/ProductModel.php';
require_once __DIR__ . '/../Models/CartModel.php';
require_once __DIR__ . '/../Models/OrderModel.php';

/**
 * Đặt hàng:
 * - Người chưa đăng nhập khi đặt hàng bắt buộc phải đăng nhập bằng tài khoản Thành viên (role = 1).
 * - Quản trị viên (role = 0) và Nhân viên CSKH (role = 2) bị chặn 403 (không được đặt hàng).
 * - Giá và tồn kho được OrderModel kiểm tra lại từ CSDL bên trong transaction.
 */
class CheckoutController
{
    /** Số giây tối thiểu giữa hai lần đặt hàng (chống gửi trùng). */
    const MIN_SECONDS_BETWEEN_ORDERS = 5;

    private $cartModel;
    private $orderModel;
    private $member;

    public function __construct(PDO $db)
    {
        $this->member = Permission::requireMember(
            'Chỉ tài khoản Thành viên (Khách hàng) mới có quyền đặt hàng. Tài khoản Quản trị viên và Nhân viên CSKH không thực hiện đặt hàng.',
            'Vui lòng đăng nhập bằng tài khoản Thành viên (Khách hàng) để tiến hành đặt hàng.',
            'index.php?controller=checkout&action=index'
        );

        $this->cartModel = new CartModel(new ProductModel($db));
        $this->orderModel = new OrderModel($db);
    }

    public function index()
    {
        $user = $this->member;

        $cart = $this->cartModel->detailed();
        if ($cart['distinct'] === 0) {
            flash_set('cart', 'Giỏ hàng đang trống, vui lòng chọn sản phẩm trước khi thanh toán.', 'warning');
            redirect('index.php?controller=cart&action=index');
        }

        $errors = [];
        $old = [
            'receiver_name' => (string) ($user['fullname'] ?? ''),
            'receiver_email' => (string) ($user['email'] ?? ''),
            'receiver_phone' => (string) ($user['phone'] ?? ''),
            'shipping_address' => (string) ($user['address'] ?? ''),
            'payment_method' => '0',
            'note' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = [
                'receiver_name' => trim((string) ($_POST['receiver_name'] ?? '')),
                'receiver_email' => trim((string) ($_POST['receiver_email'] ?? '')),
                'receiver_phone' => trim((string) ($_POST['receiver_phone'] ?? '')),
                'shipping_address' => trim((string) ($_POST['shipping_address'] ?? '')),
                'payment_method' => (string) ($_POST['payment_method'] ?? '0'),
                'note' => trim((string) ($_POST['note'] ?? '')),
            ];
            $errors = $this->validate($old);

            if (!csrf_verify()) {
                $errors['form'] = 'Phiên gửi biểu mẫu không hợp lệ, vui lòng thử lại.';
            }

            $lastOrderAt = (int) ($_SESSION['last_order_at'] ?? 0);
            if (!$errors && $lastOrderAt > 0 && time() - $lastOrderAt < self::MIN_SECONDS_BETWEEN_ORDERS) {
                $errors['form'] = 'Bạn vừa đặt một đơn hàng, vui lòng chờ vài giây rồi thử lại.';
            }

            if (!$errors) {
                foreach ($cart['items'] as $item) {
                    if ($item['quantity'] > $item['stock']) {
                        $errors['form'] = 'Sản phẩm "' . $item['product']['name'] . '" chỉ còn '
                            . $item['stock'] . ' trong kho. Vui lòng cập nhật giỏ hàng.';
                        break;
                    }
                }
            }

            if (!$errors) {
                try {
                    $orderId = $this->orderModel->create(
                        (int) $user['id'],
                        [
                            'receiver_name' => $old['receiver_name'],
                            'receiver_email' => $old['receiver_email'] !== '' ? $old['receiver_email'] : $user['email'],
                            'receiver_phone' => preg_replace('/[\s.\-]/', '', $old['receiver_phone']),
                            'shipping_address' => $old['shipping_address'],
                            'payment_method' => (int) $old['payment_method'],
                            'note' => $old['note'],
                        ],
                        $this->cartModel->itemsForCheckout()
                    );

                    $this->cartModel->clear();
                    $_SESSION['last_order_at'] = time();
                    $_SESSION['last_order_id'] = $orderId;

                    redirect('index.php?controller=checkout&action=success&id=' . $orderId);
                } catch (RuntimeException $exception) {
                    $errors['form'] = $exception->getMessage();
                } catch (Throwable $exception) {
                    $errors['form'] = 'Không thể tạo đơn hàng, vui lòng thử lại sau.';
                }
            }
        }

        $pageTitle = 'Thanh toán';
        $metaDescription = 'Xác nhận thông tin giao hàng và đặt hàng tại ' . site_name() . '.';
        $activeNav = 'cart';
        $cartData = $cart;

        require __DIR__ . '/../Views/Frontends/checkout/index.php';
    }

    /** Trang xác nhận sau khi đặt hàng thành công (chỉ Thành viên sở hữu đơn). */
    public function success()
    {
        $orderId = (int) ($_GET['id'] ?? 0);
        $order = $this->orderModel->findForUser($orderId, (int) $this->member['id']);
        if (!$order) {
            http_response_code(404);
            $pageTitle = 'Không tìm thấy đơn hàng';
            require __DIR__ . '/../Views/Frontends/errors/404.php';
            return;
        }

        $items = $this->orderModel->items($orderId);
        $pageTitle = 'Đặt hàng thành công';
        $metaDescription = 'Đơn hàng của bạn đã được ghi nhận.';
        $activeNav = 'cart';

        require __DIR__ . '/../Views/Frontends/checkout/success.php';
    }

    /** Validate phía server cho form thanh toán. */
    private function validate(array $old)
    {
        $errors = [];

        $nameLength = mb_strlen($old['receiver_name'], 'UTF-8');
        if ($nameLength < 2 || $nameLength > 100) {
            $errors['receiver_name'] = 'Họ tên người nhận phải có từ 2 đến 100 ký tự.';
        }

        if ($old['receiver_email'] !== '' && (strlen($old['receiver_email']) > 191 || !filter_var($old['receiver_email'], FILTER_VALIDATE_EMAIL))) {
            $errors['receiver_email'] = 'Địa chỉ email không hợp lệ.';
        }

        $phone = preg_replace('/[\s.\-]/', '', $old['receiver_phone']);
        if (!validate_phone($phone)) {
            $errors['receiver_phone'] = 'Số điện thoại không hợp lệ (10 số, bắt đầu bằng 0).';
        }

        $addressLength = mb_strlen($old['shipping_address'], 'UTF-8');
        if ($addressLength < 10 || $addressLength > 255) {
            $errors['shipping_address'] = 'Vui lòng nhập địa chỉ giao hàng chi tiết (tối thiểu 10 ký tự).';
        }

        if (!in_array($old['payment_method'], ['0', '1'], true)) {
            $errors['payment_method'] = 'Vui lòng chọn phương thức thanh toán.';
        }

        if (mb_strlen($old['note'], 'UTF-8') > 255) {
            $errors['note'] = 'Ghi chú không được vượt quá 255 ký tự.';
        }

        return $errors;
    }
}
