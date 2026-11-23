<?php
/**
 * Menu sidebar khu quản trị.
 *
 * Quy ước nhóm: mỗi người chỉ thêm nhóm menu của phần mình vào file này.
 * Phần #3 (Sản phẩm · Danh mục · Đơn hàng) hiện là nhóm đang hoạt động.
 */
$activeMenu = $activeMenu ?? '';

$menuGroups = [
    [
        'title' => 'Bán hàng',
        'items' => [
            [
                'key' => 'products',
                'label' => 'Sản phẩm',
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
                'label' => 'Đơn hàng',
                'icon' => 'bi-receipt',
                'url' => 'index.php?controller=admin&amp;action=orders',
            ],
        ],
    ],
    [
        'title' => 'Hệ thống',
        'items' => [
            [
                'key' => 'dashboard',
                'label' => 'Tổng quan',
                'icon' => 'bi-speedometer2',
                'url' => 'index.php?controller=admin&amp;action=dashboard',
            ],
            [
                'key' => 'site',
                'label' => 'Xem website',
                'icon' => 'bi-globe2',
                'url' => 'index.php?controller=product&amp;action=index',
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
