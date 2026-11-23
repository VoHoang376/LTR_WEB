<?php

require_once __DIR__ . '/../Models/CategoryModel.php';

/**
 * Quản trị danh mục sản phẩm (CRUD).
 */
class AdminCategoryController
{
    private $categoryModel;

    public function __construct(PDO $db)
    {
        Permission::requireAdmin();
        $this->categoryModel = new CategoryModel($db);
    }

    public function index()
    {
        $categories = $this->categoryModel->all();
        $pageTitle = 'Danh mục sản phẩm';
        $activeMenu = 'categories';
        $breadcrumb = [['label' => 'Danh mục', 'url' => null]];

        require __DIR__ . '/../Views/Backends/categories/index.php';
    }

    public function create()
    {
        $category = null;
        $this->save($category);
    }

    public function edit()
    {
        $categoryId = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        $category = $this->categoryModel->findById($categoryId);
        if (!$category) {
            flash_set('admin', 'Không tìm thấy danh mục.', 'danger');
            redirect('index.php?controller=admin&action=categories');
        }

        $this->save($category);
    }

    private function save($category)
    {
        $categoryId = $category === null ? null : (int) $category['id'];
        $errors = [];
        $old = [
            'name' => $category['name'] ?? '',
            'slug' => $category['slug'] ?? '',
            'status' => $category['status'] ?? 1,
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = [
                'name' => trim((string) ($_POST['name'] ?? '')),
                'slug' => trim((string) ($_POST['slug'] ?? '')),
                'status' => (string) ($_POST['status'] ?? '1'),
            ];

            if (!csrf_verify()) {
                $errors['form'] = 'Phiên gửi biểu mẫu không hợp lệ, vui lòng thử lại.';
            }

            $nameLength = mb_strlen($old['name'], 'UTF-8');
            if ($nameLength < 2 || $nameLength > 100) {
                $errors['name'] = 'Tên danh mục phải có từ 2 đến 100 ký tự.';
            }

            $slug = slugify($old['slug'] !== '' ? $old['slug'] : $old['name']);
            if (mb_strlen($slug, 'UTF-8') > 100) {
                $errors['slug'] = 'Đường dẫn (slug) quá dài.';
            } elseif ($this->categoryModel->slugExists($slug, $categoryId)) {
                $errors['slug'] = 'Đường dẫn "' . $slug . '" đã được dùng cho danh mục khác.';
            }
            $old['slug'] = $slug;

            if (!in_array($old['status'], ['0', '1'], true)) {
                $errors['status'] = 'Trạng thái không hợp lệ.';
            }

            if (!$errors) {
                if ($category === null) {
                    $this->categoryModel->create($old['name'], $slug, (int) $old['status']);
                    flash_set('admin', 'Đã thêm danh mục "' . $old['name'] . '".', 'success');
                } else {
                    $this->categoryModel->update($categoryId, $old['name'], $slug, (int) $old['status']);
                    flash_set('admin', 'Đã cập nhật danh mục "' . $old['name'] . '".', 'success');
                }
                redirect('index.php?controller=admin&action=categories');
            }
        }

        $pageTitle = $category === null ? 'Thêm danh mục' : 'Sửa danh mục';
        $activeMenu = 'categories';
        $breadcrumb = [
            ['label' => 'Danh mục', 'url' => 'index.php?controller=admin&action=categories'],
            ['label' => $category === null ? 'Thêm mới' : 'Chỉnh sửa', 'url' => null],
        ];

        require __DIR__ . '/../Views/Backends/categories/form.php';
    }

    /** Xoá danh mục: chặn nếu danh mục còn sản phẩm (tránh xoá dây chuyền). */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
            flash_set('admin', 'Yêu cầu không hợp lệ (cần POST và CSRF).', 'danger');
            redirect('index.php?controller=admin&action=categories');
        }

        $categoryId = (int) ($_POST['id'] ?? 0);
        $category = $this->categoryModel->findById($categoryId);
        if (!$category) {
            flash_set('admin', 'Không tìm thấy danh mục.', 'danger');
            redirect('index.php?controller=admin&action=categories');
        }

        $productCount = $this->categoryModel->countProducts($categoryId);
        if ($productCount > 0) {
            flash_set(
                'admin',
                'Danh mục "' . $category['name'] . '" còn ' . $productCount . ' sản phẩm, không thể xoá. '
                . 'Hãy chuyển sản phẩm sang danh mục khác trước.',
                'warning'
            );
            redirect('index.php?controller=admin&action=categories');
        }

        $this->categoryModel->delete($categoryId);
        flash_set('admin', 'Đã xoá danh mục "' . $category['name'] . '".', 'success');
        redirect('index.php?controller=admin&action=categories');
    }
}
