<?php
$pageTitle = 'Quản lý tài khoản & Phân quyền';
$activeAdminPage = 'users';
require __DIR__ . '/../../Layouts/Backend/header.php';
?>

<div class="space-y-6">
    <?php if (!empty($success)): ?>
        <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800"><?= $escape($success) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"><?= $escape($error) ?></div>
    <?php endif; ?>

    <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-4 text-xs text-blue-900">
        <strong>Phân quyền tách biệt 3 vai trò:</strong>
        <ul class="mt-1 list-disc pl-5 space-y-0.5">
            <li><strong>Quản trị viên (role = 0):</strong> Quản lý Sản phẩm, Danh mục, Đơn hàng, Bài viết, Giới thiệu, Bảng giá, Cấu hình &amp; Tài khoản. Không mua hàng và không xử lý kênh CSKH.</li>
            <li><strong>Nhân viên CSKH (role = 2):</strong> Giải quyết &amp; trả lời Liên hệ, Hỏi/Đáp FAQ, Phản hồi sản phẩm và Kiểm duyệt bình luận. Không mua hàng và không vào trang Quản trị hệ thống.</li>
            <li><strong>Thành viên (role = 1):</strong> Mua sắm, Giỏ hàng, Đơn hàng cá nhân, Đánh giá sản phẩm, Bình luận, Đặt câu hỏi &amp; Liên hệ.</li>
        </ul>
    </div>

    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3.5">ID</th>
                        <th class="px-4 py-3.5">Họ tên &amp; Email</th>
                        <th class="px-4 py-3.5">Vai trò hiện tại</th>
                        <th class="px-4 py-3.5">Trạng thái</th>
                        <th class="px-4 py-3.5 text-right">Cập nhật phân quyền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($users as $u): ?>
                        <?php
                        $role = (int) $u['role'];
                        $isSelf = (int) $u['id'] === $currentAdminId;
                        ?>
                        <tr>
                            <td class="px-4 py-3.5 font-mono text-xs text-gray-400">#<?= (int) $u['id'] ?></td>
                            <td class="px-4 py-3.5">
                                <div class="font-medium text-gray-800">
                                    <?= $escape($u['fullname']) ?>
                                    <?php if ($isSelf): ?><span class="text-xs text-brand-500">(Bạn)</span><?php endif; ?>
                                </div>
                                <div class="text-xs text-gray-400"><?= $escape($u['email']) ?></div>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <?php if ($role === Permission::ROLE_ADMIN): ?>
                                    <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700">Quản trị viên</span>
                                <?php elseif ($role === Permission::ROLE_STAFF): ?>
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Nhân viên CSKH</span>
                                <?php else: ?>
                                    <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700">Thành viên</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <?php if ((int) $u['status'] === 1): ?>
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Hoạt động</span>
                                <?php else: ?>
                                    <span class="rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700">Đã khóa</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <?php if ($isSelf): ?>
                                    <span class="text-xs text-gray-400">Không thể tự chỉnh sửa</span>
                                <?php else: ?>
                                    <form method="post" action="index.php?controller=admin&amp;action=user-update" class="inline-flex flex-wrap items-center justify-end gap-2">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                        <select name="role" aria-label="Vai trò tài khoản" class="rounded-lg border border-gray-300 bg-white pl-3 pr-8 py-1.5 text-xs text-gray-700">
                                            <option value="1" <?= $role === 1 ? 'selected' : '' ?>>Thành viên</option>
                                            <option value="2" <?= $role === 2 ? 'selected' : '' ?>>Nhân viên CSKH</option>
                                            <option value="0" <?= $role === 0 ? 'selected' : '' ?>>Quản trị viên</option>
                                        </select>
                                        <select name="status" aria-label="Trạng thái tài khoản" class="rounded-lg border border-gray-300 bg-white pl-3 pr-8 py-1.5 text-xs text-gray-700">
                                            <option value="1" <?= (int) $u['status'] === 1 ? 'selected' : '' ?>>Hoạt động</option>
                                            <option value="0" <?= (int) $u['status'] === 0 ? 'selected' : '' ?>>Khóa</option>
                                        </select>
                                        <button type="submit" class="rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600">Lưu</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php require __DIR__ . '/../../Layouts/Backend/footer.php'; ?>
