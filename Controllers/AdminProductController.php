<?php

require_once __DIR__ . '/../Models/ProductModel.php';
require_once __DIR__ . '/../Models/CategoryModel.php';

/**
 * Quản trị sản phẩm: xem/tìm kiếm, thêm, sửa, xóa, upload ảnh, tồn kho.
 * Mọi action đều gọi Permission::requireAdmin() trong constructor.
 */
class AdminProductController
{
    const PER_PAGE = 10;

    private $productModel;
    private $categoryModel;

    public function __construct(PDO $db)
    {
        Permission::requireAdmin();
        $this->productModel = new ProductModel($db);
        $this->categoryModel = new CategoryModel($db);
    }

    /** Danh sách sản phẩm: tìm kiếm, lọc danh mục/trạng thái, phân trang. */
    public function index()
    {
        $result = $this->productModel->search([
            'q' => trim((string) ($_GET['q'] ?? '')),
            'category_id' => (int) ($_GET['category'] ?? 0),
            'status' => $_GET['status'] ?? '',
            'sort' => (string) ($_GET['sort'] ?? 'newest'),
            'page' => max(1, (int) ($_GET['page'] ?? 1)),
            'per_page' => self::PER_PAGE,
        ]);

        $categories = $this->categoryModel->all();
        $stats = $this->productModel->stats();
        $pageTitle = 'Quản lý sản phẩm';
        $activeMenu = 'products';
        $breadcrumb = [['label' => 'Sản phẩm', 'url' => null]];

        require __DIR__ . '/../Views/Backends/products/index.php';
    }

    public function create()
    {
        $this->save(null);
    }

    public function edit()
    {
        $productId = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        $product = $this->productModel->findById($productId);
        if (!$product) {
            flash_set('admin', 'Không tìm thấy sản phẩm.', 'danger');
            redirect('index.php?controller=admin&action=products');
        }

        $this->save($product);
    }

    /** Xử lý chung form thêm/sửa. */
    private function save($product)
    {
        $productId = $product === null ? null : (int) $product['id'];
        $categories = $this->categoryModel->all();
        $errors = [];

        $old = [
            'name' => $product['name'] ?? '',
            'slug' => $product['slug'] ?? '',
            'category_id' => $product['category_id'] ?? ($categories[0]['id'] ?? ''),
            'price' => $product['price'] ?? '',
            'stock' => $product['stock'] ?? 0,
            'description' => $product['description'] ?? '',
            'specifications' => $product['specifications'] ?? '',
            'status' => $product['status'] ?? 1,
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = [
                'name' => trim((string) ($_POST['name'] ?? '')),
                'slug' => trim((string) ($_POST['slug'] ?? '')),
                'category_id' => (int) ($_POST['category_id'] ?? 0),
                'price' => trim((string) ($_POST['price'] ?? '')),
                'stock' => trim((string) ($_POST['stock'] ?? '0')),
                'description' => trim((string) ($_POST['description'] ?? '')),
                'specifications' => trim((string) ($_POST['specifications'] ?? '')),
                'status' => (string) ($_POST['status'] ?? '1'),
            ];

            if (!csrf_verify()) {
                $errors['form'] = 'Phiên gửi biểu mẫu không hợp lệ, vui lòng thử lại.';
            }

            $nameLength = mb_strlen($old['name'], 'UTF-8');
            if ($nameLength < 2 || $nameLength > 255) {
                $errors['name'] = 'Tên sản phẩm phải có từ 2 đến 255 ký tự.';
            }

            // Slug: bỏ trống thì tự sinh từ tên; kiểm tra trùng.
            $slugSource = $old['slug'] !== '' ? $old['slug'] : $old['name'];
            $slug = slugify($slugSource);
            if (mb_strlen($slug, 'UTF-8') > 255) {
                $errors['slug'] = 'Đường dẫn (slug) quá dài.';
            } elseif ($this->productModel->slugExists($slug, $productId)) {
                $errors['slug'] = 'Đường dẫn "' . $slug . '" đã được dùng cho sản phẩm khác.';
            }
            $old['slug'] = $slug;

            if (!in_array($old['category_id'], array_map('intval', array_column($categories, 'id')), true)) {
                $errors['category_id'] = 'Vui lòng chọn danh mục hợp lệ.';
            }

            $price = $this->parseMoney($old['price']);
            if ($price === null || $price < 0 || $price > 9999999999999.99) {
                $errors['price'] = 'Giá bán phải là số không âm.';
            }

            $stock = filter_var($old['stock'], FILTER_VALIDATE_INT);
            if ($stock === false || $stock < 0 || $stock > 1000000) {
                $errors['stock'] = 'Tồn kho phải là số nguyên từ 0 đến 1.000.000.';
            }

            if (!in_array($old['status'], ['0', '1'], true)) {
                $errors['status'] = 'Trạng thái không hợp lệ.';
            }

            if (mb_strlen($old['description'], 'UTF-8') > 5000) {
                $errors['description'] = 'Mô tả không được vượt quá 5.000 ký tự.';
            }
            if (mb_strlen($old['specifications'], 'UTF-8') > 5000) {
                $errors['specifications'] = 'Thông số kỹ thuật không được vượt quá 5.000 ký tự.';
            }

            $newThumbnail = null;
            if (!$errors) {
                // Ảnh đại diện: không bắt buộc, chỉ thay khi có tệp mới.
                $uploadError = null;
                $newThumbnail = upload_image($_FILES['thumbnail'] ?? null, 'products', $uploadError);
                if ($uploadError !== null) {
                    $errors['thumbnail'] = $uploadError;
                }
            }

            if (!$errors) {
                $thumbnail = $newThumbnail !== null ? $newThumbnail : ($product['thumbnail'] ?? null);
                if ($thumbnail === 'no-image.svg') {
                    // Ảnh mặc định do layout tự sinh, không lưu vào DB.
                    $thumbnail = null;
                }

                $data = [
                    'category_id' => (int) $old['category_id'],
                    'name' => $old['name'],
                    'slug' => $slug,
                    'price' => number_format((float) $price, 2, '.', ''),
                    'stock' => (int) $stock,
                    'thumbnail' => $thumbnail,
                    'description' => $old['description'] !== '' ? $old['description'] : null,
                    'specifications' => $old['specifications'] !== '' ? $old['specifications'] : null,
                    'status' => (int) $old['status'],
                ];

                if ($product === null) {
                    $productId = $this->productModel->create($data);
                    $message = 'Đã thêm sản phẩm "' . $old['name'] . '".';
                } else {
                    $this->productModel->update($productId, $data);
                    $message = 'Đã cập nhật sản phẩm "' . $old['name'] . '".';
                }

                // Thay ảnh đại diện mới thì xoá tệp cũ cho gọn ổ đĩa.
                if ($newThumbnail !== null && !empty($product['thumbnail'])
                    && $product['thumbnail'] !== 'no-image.svg') {
                    delete_upload_image($product['thumbnail'], 'products');
                }

                $this->storeGalleryImages($productId);

                flash_set('admin', $message, 'success');
                redirect('index.php?controller=admin&action=products');
            }
        }

        $images = $productId === null ? [] : $this->productModel->images($productId);
        $pageTitle = $product === null ? 'Thêm sản phẩm' : 'Sửa sản phẩm';
        $activeMenu = 'products';
        $breadcrumb = [
            ['label' => 'Sản phẩm', 'url' => 'index.php?controller=admin&action=products'],
            ['label' => $product === null ? 'Thêm mới' : 'Chỉnh sửa', 'url' => null],
        ];

        require __DIR__ . '/../Views/Backends/products/form.php';
    }

    /** Upload nhiều ảnh phụ (input name="gallery[]"). */
    private function storeGalleryImages($productId, &$warning = null)
    {
        $warning = null;
        if (empty($_FILES['gallery']) || !is_array($_FILES['gallery']['name'])) {
            return;
        }

        $sortOrder = count($this->productModel->images($productId));
        $saved = 0;
        $errors = [];

        foreach ($_FILES['gallery']['name'] as $index => $name) {
            if (($_FILES['gallery']['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $file = [
                'name' => $name,
                'type' => $_FILES['gallery']['type'][$index] ?? '',
                'tmp_name' => $_FILES['gallery']['tmp_name'][$index] ?? '',
                'error' => $_FILES['gallery']['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                'size' => $_FILES['gallery']['size'][$index] ?? 0,
            ];

            $uploadError = null;
            $filename = upload_image($file, 'products', $uploadError);
            if ($filename === null) {
                $errors[] = $uploadError;
                continue;
            }
            $this->productModel->addImage($productId, $filename, $sortOrder++);
            $saved++;
        }

        if ($saved > 0) {
            flash_set('admin_gallery', 'Đã thêm ' . $saved . ' ảnh cho sản phẩm.', 'success');
        }
        if ($errors !== []) {
            $warning = implode(' ', array_filter($errors));
        }
    }

    /** Upload ảnh phụ cho sản phẩm đã có (form riêng ở trang sửa). */
    public function uploadGallery()
    {
        $this->requirePost();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $product = $this->productModel->findById($productId);
        if (!$product) {
            flash_set('admin', 'Không tìm thấy sản phẩm.', 'danger');
            redirect('index.php?controller=admin&action=products');
        }

        $this->storeGalleryImages($productId);
        redirect('index.php?controller=admin&action=product-edit&id=' . $productId);
    }

    /** Xoá ảnh phụ khỏi thư viện ảnh. */
    public function deleteImage()
    {
        $this->requirePost();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $imageId = (int) ($_POST['image_id'] ?? 0);
        $image = $this->productModel->findImage($imageId, $productId);

        if ($image) {
            $this->productModel->deleteImage($imageId, $productId);
            delete_upload_image($image['image'], 'products');
            flash_set('admin', 'Đã xoá ảnh khỏi sản phẩm.', 'success');
        } else {
            flash_set('admin', 'Không tìm thấy ảnh cần xoá.', 'danger');
        }

        redirect('index.php?controller=admin&action=product-edit&id=' . $productId);
    }

    /**
     * Xoá sản phẩm. Sản phẩm đã phát sinh đơn hàng sẽ được chuyển sang
     * "ngừng kinh doanh" thay vì xoá cứng để giữ lịch sử đơn hàng.
     */
    public function delete()
    {
        $this->requirePost();

        $productId = (int) ($_POST['id'] ?? 0);
        $product = $this->productModel->findById($productId);
        if (!$product) {
            flash_set('admin', 'Không tìm thấy sản phẩm.', 'danger');
            redirect('index.php?controller=admin&action=products');
        }

        if ($this->productModel->countOrderLines($productId) > 0) {
            $this->productModel->setStatus($productId, 0);
            flash_set(
                'admin',
                'Sản phẩm đã phát sinh đơn hàng nên không xoá cứng. Đã chuyển sang "Ngừng kinh doanh" để giữ lịch sử đơn.',
                'warning'
            );
            redirect('index.php?controller=admin&action=products');
        }

        $images = $this->productModel->images($productId);
        $this->productModel->delete($productId);
        foreach ($images as $image) {
            delete_upload_image($image['image'], 'products');
        }
        delete_upload_image($product['thumbnail'], 'products');

        flash_set('admin', 'Đã xoá sản phẩm "' . $product['name'] . '".', 'success');
        redirect('index.php?controller=admin&action=products');
    }

    /** Chỉ nhận POST + CSRF cho các thao tác xoá. */
    private function requirePost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
            flash_set('admin', 'Yêu cầu không hợp lệ (cần POST và CSRF).', 'danger');
            redirect('index.php?controller=admin&action=products');
        }
    }

    /** Chuẩn hoá chuỗi giá: "5.290.000" / "5290000" => 5290000.0; sai định dạng => null. */
    private function parseMoney($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        if (preg_match('/^[0-9.,\s]+$/', $value) !== 1) {
            return null;
        }

        $normalized = str_replace(['.', ',', ' '], '', $value);
        if ($normalized === '' || !ctype_digit($normalized)) {
            return null;
        }

        return (float) $normalized;
    }
}
