<?php
$pageTitle = 'Quản lý bảng giá';
$activeAdminPage = 'pricing';
require __DIR__ . '/../../Layouts/Backend/header.php';
?>

<div class="space-y-6">
    <!-- Notifications -->
    <?php if (!empty($success)): ?>
        <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-900/20 dark:text-green-300">
            <?= $escape($success) ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300">
            <?= $escape($error) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <!-- Form Add/Edit Plan (Left 1 col) -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03] xl:col-span-1">
            <h2 class="text-base font-semibold text-gray-800 dark:text-white mb-1">
                <?= !empty($editingPlan) ? 'Sửa gói dịch vụ #' . (int)$editingPlan['id'] : 'Thêm gói dịch vụ mới' ?>
            </h2>
            <p class="text-xs text-gray-500 mb-5">Thiết lập các thông số gói giá hiển thị trên website.</p>

            <form method="post" action="index.php?controller=admin&amp;action=pricing_save" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $escape($adminCsrfToken) ?>">
                <?php if (!empty($editingPlan)): ?>
                    <input type="hidden" name="id" value="<?= (int)$editingPlan['id'] ?>">
                <?php endif; ?>

                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300" for="name">
                        Tên gói dịch vụ <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="<?= $escape($editingPlan['name'] ?? '') ?>"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           placeholder="Ví dụ: Gói Doanh Nghiệp"
                           required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300" for="price">
                            Giá (VNĐ) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="price"
                               name="price"
                               step="1000"
                               min="0"
                               value="<?= $escape($editingPlan['price'] ?? 0) ?>"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                               required>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300" for="billing_cycle">
                            Chu kỳ thanh toán
                        </label>
                        <input type="text"
                               id="billing_cycle"
                               name="billing_cycle"
                               value="<?= $escape($editingPlan['billing_cycle'] ?? 'tháng') ?>"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                               placeholder="tháng / năm">
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300" for="description">
                        Mô tả ngắn
                    </label>
                    <input type="text"
                           id="description"
                           name="description"
                           value="<?= $escape($editingPlan['description'] ?? '') ?>"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           placeholder="Dành cho doanh nghiệp vừa và nhỏ...">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300" for="features">
                        Danh sách tính năng <span class="text-gray-400 font-normal">(Mỗi tính năng 1 dòng)</span>
                    </label>
                    <textarea id="features"
                              name="features"
                              rows="5"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                              placeholder="Hỗ trợ 24/7&#10;Sao lưu dữ liệu hàng ngày&#10;Dung lượng 100GB..."><?= $escape($editingPlan['features'] ?? '') ?></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300" for="sort_order">
                            Thứ tự sắp xếp
                        </label>
                        <input type="number"
                               id="sort_order"
                               name="sort_order"
                               value="<?= $escape($editingPlan['sort_order'] ?? 1) ?>"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300" for="status">
                            Trạng thái
                        </label>
                        <select id="status"
                                name="status"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="1" <?= (!isset($editingPlan['status']) || (int)$editingPlan['status'] === 1) ? 'selected' : '' ?>>Hiển thị</option>
                            <option value="0" <?= (isset($editingPlan['status']) && (int)$editingPlan['status'] === 0) ? 'selected' : '' ?>>Ẩn</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox"
                           id="is_featured"
                           name="is_featured"
                           value="1"
                           <?= (!empty($editingPlan['is_featured']) && (int)$editingPlan['is_featured'] === 1) ? 'checked' : '' ?>
                           class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                    <label for="is_featured" class="text-xs text-gray-700 dark:text-gray-300 select-none">
                        Đánh dấu là gói <strong class="text-brand-500">Nổi bật (Khuyên dùng)</strong>
                    </label>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="submit"
                            class="rounded-lg bg-brand-500 px-4 py-2 text-xs font-medium text-white hover:bg-brand-600 transition">
                        <?= !empty($editingPlan) ? 'Cập nhật gói giá' : 'Thêm gói giá' ?>
                    </button>
                    <?php if (!empty($editingPlan)): ?>
                        <a href="index.php?controller=admin&amp;action=pricing"
                           class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            Hủy
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Plans Table (Right 2 cols) -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03] xl:col-span-2 overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                <h2 class="text-base font-semibold text-gray-800 dark:text-white">Danh sách các gói giá</h2>
                <a href="index.php?controller=pricing" target="_blank" class="text-xs text-brand-500 hover:underline">
                    Xem trang bảng giá ngoài &nearr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-white/[0.02] dark:text-gray-400">
                        <tr>
                            <th class="px-5 py-3.5">Thứ tự</th>
                            <th class="px-5 py-3.5">Tên gói</th>
                            <th class="px-5 py-3.5">Giá / Chu kỳ</th>
                            <th class="px-5 py-3.5">Nổi bật</th>
                            <th class="px-5 py-3.5">Trạng thái</th>
                            <th class="px-5 py-3.5 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <?php if (empty($plans)): ?>
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-400">
                                    Chưa có gói dịch vụ nào. Hãy thêm gói đầu tiên ở form bên cạnh.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($plans as $p): ?>
                                <tr class="transition hover:bg-gray-50/70 dark:hover:bg-white/[0.02]">
                                    <td class="px-5 py-4 font-mono text-xs text-gray-400">#<?= (int)$p['sort_order'] ?></td>
                                    <td class="px-5 py-4">
                                        <div class="font-medium text-gray-800 dark:text-white"><?= $escape($p['name']) ?></div>
                                        <div class="text-[11px] text-gray-400 truncate max-w-xs"><?= $escape($p['description'] ?? '') ?></div>
                                    </td>
                                    <td class="px-5 py-4 font-semibold text-brand-500 whitespace-nowrap">
                                        <?= number_format((float)$p['price'], 0, ',', '.') ?>₫ <span class="text-xs font-normal text-gray-400">/ <?= $escape($p['billing_cycle']) ?></span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <?php if ((int)$p['is_featured'] === 1): ?>
                                            <span class="rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                                ★ Nổi bật
                                            </span>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <?php if ((int)$p['status'] === 1): ?>
                                            <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                Hiển thị
                                            </span>
                                        <?php else: ?>
                                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                                Ẩn
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="index.php?controller=admin&amp;action=pricing&amp;edit=<?= (int)$p['id'] ?>"
                                               class="rounded border border-gray-200 px-2.5 py-1 text-xs text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300">
                                                Sửa
                                            </a>
                                            <form method="post" action="index.php?controller=admin&amp;action=pricing_delete" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa gói giá này?');">
                                                <input type="hidden" name="csrf_token" value="<?= $escape($adminCsrfToken) ?>">
                                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                                <button type="submit" class="rounded border border-red-200 px-2.5 py-1 text-xs text-red-600 hover:bg-red-50 dark:border-red-900/50 dark:text-red-400">
                                                    Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../Layouts/Backend/footer.php'; ?>
