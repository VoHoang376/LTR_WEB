<?php

require_once __DIR__ . '/../Models/ProductModel.php';
require_once __DIR__ . '/../Models/CategoryModel.php';
require_once __DIR__ . '/../Models/CartModel.php';
require_once __DIR__ . '/../Models/ReviewModel.php';

/**
 * Trang sản phẩm phía người dùng: danh sách (tìm kiếm/lọc/sắp xếp/phân trang),
 * chi tiết sản phẩm và đánh giá/phản hồi sản phẩm.
 */
class ProductController
{
    private $productModel;
    private $categoryModel;
    private $cartModel;
    private $reviewModel;

    public function __construct(PDO $db)
    {
        $this->productModel = new ProductModel($db);
        $this->categoryModel = new CategoryModel($db);
        $this->cartModel = new CartModel($this->productModel);
        $this->reviewModel = new ReviewModel($db);
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
        $reviews = $this->reviewModel->getByProductId($product['id']);
        $ratingSummary = $this->reviewModel->productRatingSummary($product['id']);
        $reviewFlash = flash_get('review');
        $quantityInCart = Permission::isMember() ? $this->cartModel->quantityOf($product['id']) : 0;
        $stock = (int) $product['stock'];

        $pageTitle = $product['name'];
        $metaDescription = excerpt($product['description'], 155);
        $activeNav = 'products';

        require __DIR__ . '/../Views/Frontends/products/show.php';
    }

    /** Thành viên gửi đánh giá / phản hồi về sản phẩm (chỉ role = 1). */
    public function review()
    {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $redirectTarget = 'index.php?controller=product&action=index';
        if ($productId > 0) {
            $targetProduct = $this->productModel->findById($productId);
            if ($targetProduct && (int) $targetProduct['status'] === 1) {
                $redirectTarget = 'index.php?controller=product&action=show&slug=' . rawurlencode($targetProduct['slug']);
            }
        }

        $user = Permission::requireMember(
            'Chỉ tài khoản Thành viên (Khách hàng) mới có thể gửi đánh giá sản phẩm.',
            'Vui lòng đăng nhập bằng tài khoản Thành viên (Khách hàng) để gửi đánh giá và phản hồi sản phẩm.',
            $redirectTarget
        );

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
            flash_set('review', 'Yêu cầu gửi đánh giá không hợp lệ.', 'danger');
            redirect($redirectTarget);
        }

        $product = $this->productModel->findById($productId);
        if (!$product || (int) $product['status'] !== 1) {
            flash_set('review', 'Không tìm thấy sản phẩm để đánh giá.', 'danger');
            redirect('index.php?controller=product&action=index');
        }

        $rating = (int) ($_POST['rating'] ?? 5);
        $comment = trim((string) ($_POST['comment'] ?? ''));

        if ($rating < 1 || $rating > 5) {
            flash_set('review', 'Mức đánh giá phải từ 1 đến 5 sao.', 'danger');
        } elseif (mb_strlen($comment, 'UTF-8') < 5 || mb_strlen($comment, 'UTF-8') > 2000) {
            flash_set('review', 'Nội dung đánh giá phải có từ 5 đến 2.000 ký tự.', 'danger');
        } else {
            $this->reviewModel->create((int) $user['id'], $productId, $rating, $comment, ReviewModel::STATUS_APPROVED);
            flash_set('review', 'Cảm ơn bạn đã gửi đánh giá! Nhân viên CSKH sẽ sớm phản hồi nếu bạn có câu hỏi.', 'success');
        }

        redirect('index.php?controller=product&action=show&slug=' . rawurlencode($product['slug']) . '#reviews');
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
