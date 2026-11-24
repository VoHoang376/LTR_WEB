<?php
/**
 * Menu sidebar khu Quản trị viên (role = 0) — tách biệt hoàn toàn với CSKH (role = 2).
 */
$activeMenu = $activeMenu ?? '';

$menuGroups = [
    [
        'title' => 'Tổng quan',
        'items' => [
            [
                'key' => 'dashboard',
                'label' => 'Bảng điều khiển',
                'icon' => 'bi-speedometer2',
                'url' => 'index.php?controller=admin&amp;action=dashboard',
            ],
        ],
    ],
    [
        'title' => 'Kinh doanh & Đơn hàng',
        'items' => [
            [
                'key' => 'products',
                'label' => 'Quản lý sản phẩm',
                'icon' => 'bi-box-seam',
                'url' => 'index.php?controller=admin&amp;action=products',
            ],
            [
                'key' => 'categories',
                'label' => 'Danh mục sản phẩm',
                'icon' => 'bi-tags',
                'url' => 'index.php?controller=admin&amp;action=categories',
            ],
            [
                'key' => 'orders',
                'label' => 'Quản lý đơn hàng',
                'icon' => 'bi-receipt',
                'url' => 'index.php?controller=admin&amp;action=orders',
            ],
            [
                'key' => 'pricing',
                'label' => 'Quản lý bảng giá',
                'icon' => 'bi-card-checklist',
                'url' => 'index.php?controller=admin&amp;action=pricing',
            ],
        ],
    ],
    [
        'title' => 'Nội dung Website',
        'items' => [
            [
                'key' => 'about',
                'label' => 'Trang Giới thiệu',
                'icon' => 'bi-info-circle',
                'url' => 'index.php?controller=adminAbout&amp;action=index',
            ],
            [
                'key' => 'posts',
                'label' => 'Bài viết / Tin tức',
                'icon' => 'bi-newspaper',
                'url' => 'index.php?controller=admin&amp;action=posts',
            ],
        ],
    ],
    [
        'title' => 'Hệ thống & Phân quyền',
        'items' => [
            [
                'key' => 'users',
                'label' => 'Tài khoản & Phân quyền',
                'icon' => 'bi-people',
                'url' => 'index.php?controller=admin&amp;action=users',
            ],
            [
                'key' => 'settings',
                'label' => 'Cấu hình website',
                'icon' => 'bi-gear',
                'url' => 'index.php?controller=admin&amp;action=settings',
            ],
            [
                'key' => 'profile',
                'label' => 'Hồ sơ cá nhân',
                'icon' => 'bi-person-gear',
                'url' => 'index.php?controller=auth&amp;action=profile',
            ],
            [
                'key' => 'site',
                'label' => 'Xem website',
                'icon' => 'bi-globe2',
                'url' => 'index.php',
            ],
        ],
    ],
];
?>
<?php foreach ($menuGroups as $group): ?>
    <p class="sidebar-group-title"><?= e($group['title']) ?></p>
    <ul class="sidebar-nav">
        <?php foreach ($group['items'] as $item): ?>
            <li>
                <a class="<?= $activeMenu === $item['key'] ? 'active' : '' ?>" href="<?= $item['url'] ?>">
                    <i class="bi <?= e($item['icon']) ?>" aria-hidden="true"></i>
                    <span><?= e($item['label']) ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>
