<?php

require_once __DIR__ . '/../Models/ProductModel.php';
require_once __DIR__ . '/../Models/CategoryModel.php';
require_once __DIR__ . '/../Models/CartModel.php';

/**
 * Trang sản phẩm phía người dùng: danh sách (tìm kiếm/lọc/sắp xếp/phân trang)
 * và trang chi tiết sản phẩm.
 */
class ProductController
{
    private $productModel;
    private $categoryModel;
    private $cartModel;

    public function __construct(PDO $db)
    {
        $this->productModel = new ProductModel($db);
        $this->categoryModel = new CategoryModel($db);
        $this->cartModel = new CartModel($this->productModel);
    }

    /** Danh sách sản phẩm: tìm theo từ khoá, lọc danh mục/khoảng giá, sắp xếp, phân trang. */
    public function index()
    {
        $keyword = trim((string) ($_GET['q'] ?? ''));
        $categorySlug = trim((string) ($_GET['category'] ?? ''));
        $sort = (string) ($_GET['sort'] ?? 'newest');
        if (!isset(ProductModel::SORTS[$sort])) {
            $sort = 'newest';
        }

        $minPrice = $this->numericParam('min_price');
        $maxPrice = $this->numericParam('max_price');
        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }

        $result = $this->productModel->search([
            'q' => $keyword,
            'category_slug' => $categorySlug,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'sort' => $sort,
            'status' => 1,
            'page' => max(1, (int) ($_GET['page'] ?? 1)),
            'per_page' => 9,
        ]);

        $categories = $this->categoryModel->all(true);
        $currentCategory = $categorySlug !== '' ? $this->categoryModel->findBySlug($categorySlug) : null;

        // SEO: title + meta description riêng cho trang danh sách.
        $pageTitle = 'Danh sách sản phẩm';
        if ($currentCategory) {
            $pageTitle = 'Sản phẩm – ' . $currentCategory['name'];
        }
        if ($keyword !== '') {
            $pageTitle .= ' – Tìm "' . $keyword . '"';
        }
        $metaDescription = 'Sản phẩm chính hãng tại LTR Technology: máy in, máy tính, thiết bị mạng, '
            . 'phụ kiện và camera an ninh. Bảo hành chính hãng, giao hàng toàn quốc.';
        $activeNav = 'products';

        require __DIR__ . '/../Views/Frontends/products/index.php';
    }

    /** Trang chi tiết sản phẩm theo slug. */
    public function show()
    {
        $slug = trim((string) ($_GET['slug'] ?? ''));
        $product = $slug !== '' ? $this->productModel->findBySlug($slug) : null;

        // Sản phẩm không tồn tại hoặc đang bị ẩn: trả về 404, không lộ dữ liệu.
        if (!$product || (int) $product['status'] !== 1) {
            http_response_code(404);
            $pageTitle = 'Không tìm thấy sản phẩm';
            require __DIR__ . '/../Views/Frontends/errors/404.php';

            return;
        }

        $images = $this->productModel->images($product['id']);
        $related = $this->productModel->related($product, 4);
        $quantityInCart = $this->cartModel->quantityOf($product['id']);
        $stock = (int) $product['stock'];

        $pageTitle = $product['name'];
        $metaDescription = excerpt($product['description'], 155);
        $activeNav = 'products';

        require __DIR__ . '/../Views/Frontends/products/show.php';
    }

    /** Đọc một tham số số (giá); trả về null nếu không hợp lệ. */
    private function numericParam($name)
    {
        if (!isset($_GET[$name]) || $_GET[$name] === '' || !is_numeric($_GET[$name])) {
            return null;
        }

        return max(0, (float) $_GET[$name]);
    }
}
