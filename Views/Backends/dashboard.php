<?php
$pageTitle = 'Bảng điều khiển quản trị';
$activeAdminPage = 'dashboard';
require __DIR__ . '/../Layouts/Backend/header.php';
?>

<div class="space-y-6">
    <!-- Stat Cards (Chỉ các phân hệ thuộc thẩm quyền Quản trị viên) -->
    <section class="admin-dashboard-grid">
        <article class="rounded-2xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-medium uppercase tracking-wider text-gray-500">Sản phẩm</h2>
                <?php if (!empty($productStats['out_of_stock'])): ?>
                    <span class="rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-700">
                        <?= (int) $productStats['out_of_stock'] ?> hết hàng
                    </span>
                <?php endif; ?>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-800"><?= (int) ($productStats['total'] ?? 0) ?></p>
            <p class="mt-2 text-xs text-gray-500">
                <a href="index.php?controller=admin&amp;action=products" class="text-brand-500 hover:underline">Quản lý sản phẩm &rarr;</a>
            </p>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-medium uppercase tracking-wider text-gray-500">Đơn hàng</h2>
                <?php if (!empty($pendingOrders)): ?>
                    <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">
                        <?= (int) $pendingOrders ?> chờ xác nhận
                    </span>
                <?php endif; ?>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-800"><?= (int) ($totalOrders ?? 0) ?></p>
            <p class="mt-2 text-xs text-gray-500">
                Doanh thu: <strong><?= $escape(format_price($completedRevenue ?? 0)) ?></strong> ·
                <a href="index.php?controller=admin&amp;action=orders" class="text-brand-500 hover:underline">Quản lý đơn &rarr;</a>
            </p>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white p-5">
            <h2 class="text-xs font-medium uppercase tracking-wider text-gray-500">Nội dung &amp; Bảng giá</h2>
            <p class="mt-3 text-3xl font-bold text-gray-800"><?= (int) ($totalPosts ?? 0) ?></p>
            <p class="mt-2 text-xs text-gray-500">
                Bài viết · <?= (int) ($totalAboutSections ?? 0) ?> mục giới thiệu · <?= (int) ($totalPlans ?? 0) ?> gói giá ·
                <a href="index.php?controller=admin&amp;action=posts" class="text-brand-500 hover:underline">Quản lý &rarr;</a>
            </p>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white p-5">
            <h2 class="text-xs font-medium uppercase tracking-wider text-gray-500">Tài khoản hệ thống</h2>
            <p class="mt-3 text-3xl font-bold text-gray-800"><?= (int) ($totalUsers ?? 0) ?></p>
            <p class="mt-2 text-xs text-gray-500">
                Gồm <?= (int) ($staffCount ?? 0) ?> nhân viên CSKH ·
                <a href="index.php?controller=admin&amp;action=users" class="text-brand-500 hover:underline">Phân quyền &rarr;</a>
            </p>
        </article>
    </section>

    <!-- Recent Orders Table -->
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
            <h2 class="text-sm font-semibold text-gray-800">Đơn hàng mới nhất</h2>
            <a href="index.php?controller=admin&amp;action=orders" class="text-xs font-medium text-brand-500 hover:underline">
                Xem tất cả &rarr;
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Mã đơn</th>
                        <th class="px-5 py-3">Người nhận</th>
                        <th class="px-5 py-3">Tổng tiền</th>
                        <th class="px-5 py-3">Thời gian</th>
                        <th class="px-5 py-3">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($recentOrders)): ?>
                        <tr><td colspan="5" class="px-5 py-6 text-center text-gray-400">Chưa có đơn hàng nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td class="px-5 py-3.5 font-medium">
                                    <a class="text-brand-500 hover:underline"
                                       href="index.php?controller=admin&amp;action=order-show&amp;id=<?= (int) $order['id'] ?>">
                                        #<?= str_pad((string) $order['id'], 6, '0', STR_PAD_LEFT) ?>
                                    </a>
                                </td>
                                <td class="px-5 py-3.5 text-gray-800">
                                    <?= $escape($order['receiver_name']) ?>
                                    <?php if (empty($order['user_id'])): ?>
                                        <span class="ml-1 rounded bg-gray-100 px-1.5 py-0.5 text-[10px] text-gray-500">Khách vãng lai</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5 font-semibold"><?= $escape(format_price($order['total_amount'])) ?></td>
                                <td class="px-5 py-3.5 text-xs text-gray-400"><?= $escape(format_datetime($order['created_at'])) ?></td>
                                <td class="px-5 py-3.5 text-xs">
                                    <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-[11px] font-medium text-gray-700">
                                        <?= $escape(order_status_label($order['status'])) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../Layouts/Backend/footer.php'; ?>
