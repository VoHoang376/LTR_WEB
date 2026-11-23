<?php

/**
 * Giỏ hàng lưu trong session.
 *
 * Chỉ lưu id sản phẩm và số lượng — giá luôn đọc lại từ CSDL ở mỗi lần hiển thị
 * và ở bước đặt hàng, nên người dùng không thể sửa giá qua form/session.
 */
class CartModel
{
    const MAX_QUANTITY = 99;

    private $productModel;

    public function __construct(ProductModel $productModel)
    {
        $this->productModel = $productModel;

        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /** Giỏ hàng thô: [product_id => quantity]. */
    public function raw()
    {
        return $_SESSION['cart'];
    }

    /** Tổng số lượng sản phẩm trong giỏ (hiển thị trên badge). */
    public function count()
    {
        $count = 0;
        foreach ($_SESSION['cart'] as $quantity) {
            $count += (int) $quantity;
        }

        return $count;
    }

    public function distinctCount()
    {
        return count($_SESSION['cart']);
    }

    public function has($productId)
    {
        return isset($_SESSION['cart'][(int) $productId]);
    }

    public function quantityOf($productId)
    {
        return isset($_SESSION['cart'][(int) $productId]) ? (int) $_SESSION['cart'][(int) $productId] : 0;
    }

    public function add($productId, $quantity = 1)
    {
        $productId = (int) $productId;
        $quantity = $this->clampQuantity($quantity);
        $current = $this->quantityOf($productId);

        $_SESSION['cart'][$productId] = $this->clampQuantity($current + $quantity);
    }

    public function update($productId, $quantity)
    {
        $productId = (int) $productId;
        $quantity = (int) $quantity;

        if (!isset($_SESSION['cart'][$productId])) {
            return;
        }
        if ($quantity <= 0) {
            $this->remove($productId);

            return;
        }

        $_SESSION['cart'][$productId] = $this->clampQuantity($quantity);
    }

    public function remove($productId)
    {
        unset($_SESSION['cart'][(int) $productId]);
    }

    public function clear()
    {
        $_SESSION['cart'] = [];
    }

    /**
     * Giỏ hàng kèm dữ liệu sản phẩm mới nhất từ CSDL.
     *
     * @return array{distinct:int,count:int,items:array,subtotal:float}
     */
    public function detailed()
    {
        $products = $this->productModel->findManyByIds(array_keys($_SESSION['cart']));
        $items = [];
        $subtotal = 0.0;
        $count = 0;

        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $productId = (int) $productId;
            $quantity = (int) $quantity;

            // Sản phẩm đã bị xoá/ngừng bán thì tự loại khỏi giỏ.
            if (!isset($products[$productId]) || (int) $products[$productId]['status'] !== 1) {
                unset($_SESSION['cart'][$productId]);
                continue;
            }

            $product = $products[$productId];
            $price = (float) $product['price'];
            $lineTotal = round($price * $quantity, 2);
            $subtotal += $lineTotal;
            $count += $quantity;

            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'price' => $price,
                'line_total' => $lineTotal,
                'stock' => (int) $product['stock'],
                'in_stock' => (int) $product['stock'] >= $quantity,
            ];
        }

        return [
            'distinct' => count($items),
            'count' => $count,
            'items' => $items,
            'subtotal' => round($subtotal, 2),
        ];
    }

    /** Dữ liệu tối thiểu để tạo đơn: [['product_id'=>..,'quantity'=>..], ...]. */
    public function itemsForCheckout()
    {
        $items = [];
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $items[] = ['product_id' => (int) $productId, 'quantity' => (int) $quantity];
        }

        return $items;
    }

    private function clampQuantity($quantity)
    {
        $quantity = (int) $quantity;

        return max(1, min(self::MAX_QUANTITY, $quantity));
    }
}
