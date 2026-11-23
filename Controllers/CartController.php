<?php

require_once __DIR__ . '/../Models/ProductModel.php';
require_once __DIR__ . '/../Models/CartModel.php';

/**
 * Giỏ hàng: xem, thêm, sửa số lượng, xoá.
 *
 * Các thao tác thay đổi giỏ đều là POST + CSRF. Khi gọi bằng fetch (AJAX)
 * controller trả JSON để cập nhật giao diện; khi JavaScript bị tắt thì
 * chuyển trang bình thường.
 */
class CartController
{
    private $productModel;
    private $cartModel;

    public function __construct(PDO $db)
    {
        $this->productModel = new ProductModel($db);
        $this->cartModel = new CartModel($this->productModel);
    }

    public function index()
    {
        $cart = $this->cartModel->detailed();
        $pageTitle = 'Giỏ hàng';
        $metaDescription = 'Giỏ hàng của bạn tại LTR Technology.';
        $activeNav = 'cart';

        require __DIR__ . '/../Views/Frontends/cart/index.php';
    }

    /** Thêm sản phẩm (mặc định 1) vào giỏ. */
    public function add()
    {
        $this->guardPost();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);
        if ($quantity < 1) {
            $quantity = 1;
        }

        $product = $this->productModel->findById($productId);
        if (!$product || (int) $product['status'] !== 1) {
            $this->fail('Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.');
        }

        $stock = (int) $product['stock'];
        if ($stock <= 0) {
            $this->fail('Sản phẩm "' . $product['name'] . '" đã hết hàng.');
        }

        $newQuantity = $this->cartModel->quantityOf($productId) + $quantity;
        if ($newQuantity > CartModel::MAX_QUANTITY) {
            $this->fail('Mỗi sản phẩm chỉ được đặt tối đa ' . CartModel::MAX_QUANTITY . ' sản phẩm.');
        }
        if ($newQuantity > $stock) {
            $this->fail('Sản phẩm "' . $product['name'] . '" chỉ còn ' . $stock . ' trong kho.');
        }

        $this->cartModel->add($productId, $quantity);
        $this->succeed('Đã thêm "' . $product['name'] . '" vào giỏ hàng.');
    }

    /** Cập nhật số lượng tuyệt đối của một dòng; 0 => xoá dòng. */
    public function update()
    {
        $this->guardPost();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 0);

        if (!$this->cartModel->has($productId)) {
            $this->fail('Sản phẩm không có trong giỏ hàng.');
        }

        if ($quantity <= 0) {
            $this->cartModel->remove($productId);
            $this->succeed('Đã xoá sản phẩm khỏi giỏ hàng.');
        }

        $product = $this->productModel->findById($productId);
        if (!$product || (int) $product['status'] !== 1) {
            $this->cartModel->remove($productId);
            $this->fail('Sản phẩm không còn kinh doanh nên đã được xoá khỏi giỏ hàng.');
        }
        if ($quantity > CartModel::MAX_QUANTITY) {
            $this->fail('Mỗi sản phẩm chỉ được đặt tối đa ' . CartModel::MAX_QUANTITY . ' sản phẩm.');
        }
        if ($quantity > (int) $product['stock']) {
            $this->fail('Sản phẩm "' . $product['name'] . '" chỉ còn ' . (int) $product['stock'] . ' trong kho.');
        }

        $this->cartModel->update($productId, $quantity);
        $this->succeed('Đã cập nhật số lượng.');
    }

    /** Xoá một dòng khỏi giỏ. */
    public function remove()
    {
        $this->guardPost();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $this->cartModel->remove($productId);
        $this->succeed('Đã xoá sản phẩm khỏi giỏ hàng.');
    }

    /** Xoá toàn bộ giỏ. */
    public function clear()
    {
        $this->guardPost();

        $this->cartModel->clear();
        $this->succeed('Đã xoá toàn bộ giỏ hàng.');
    }

    private function guardPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (is_ajax_request()) {
                json_response(['ok' => false, 'message' => 'Phương thức không hợp lệ.'], 405);
            }
            redirect('index.php?controller=cart&action=index');
        }

        if (!csrf_verify()) {
            if (is_ajax_request()) {
                json_response(['ok' => false, 'message' => 'Phiên làm việc đã hết hạn, vui lòng tải lại trang.'], 419);
            }
            flash_set('cart', 'Phiên làm việc đã hết hạn, vui lòng thử lại.', 'danger');
            redirect('index.php?controller=cart&action=index');
        }
    }

    /** Trạng thái giỏ hàng sau mỗi thao tác (dùng chung cho AJAX và chuyển trang). */
    private function cartPayload($ok, $message)
    {
        $cart = $this->cartModel->detailed();
        $items = [];
        foreach ($cart['items'] as $item) {
            $items[] = [
                'product_id' => (int) $item['product']['id'],
                'quantity' => (int) $item['quantity'],
                'line_total' => $item['line_total'],
                'line_total_text' => format_price($item['line_total']),
            ];
        }

        return [
            'ok' => $ok,
            'message' => $message,
            'count' => $cart['count'],
            'distinct' => $cart['distinct'],
            'subtotal' => $cart['subtotal'],
            'subtotal_text' => format_price($cart['subtotal']),
            'empty' => $cart['distinct'] === 0,
            'items' => $items,
        ];
    }

    private function succeed($message)
    {
        if (is_ajax_request()) {
            json_response($this->cartPayload(true, $message));
        }
        flash_set('cart', $message, 'success');
        redirect('index.php?controller=cart&action=index');
    }

    private function fail($message)
    {
        if (is_ajax_request()) {
            json_response($this->cartPayload(false, $message), 422);
        }
        flash_set('cart', $message, 'danger');
        // Không dùng HTTP_REFERER để tránh chuyển hướng ra ngoài website.
        redirect('index.php?controller=cart&action=index');
    }
}
