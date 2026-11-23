<?php
$pageTitle = 'Cấu hình website';
$activeAdminPage = 'settings';
require __DIR__ . '/../../Layouts/Backend/header.php';
?>

<div class="max-w-4xl space-y-6">
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

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">Thông tin chung doanh nghiệp</h2>
        <p class="text-xs text-gray-500 mb-6">Các thông tin dưới đây sẽ hiển thị công khai trên Trang Chủ, Trang Liên Hệ và Chân trang.</p>

        <form method="post" action="index.php?controller=admin&amp;action=settings" enctype="multipart/form-data" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= $escape($adminCsrfToken) ?>">
            <input type="hidden" name="MAX_FILE_SIZE" value="2097152">

            <!-- Logo & Favicon Upload -->
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 rounded-xl bg-gray-50/70 p-4 border border-gray-100 dark:border-gray-800 dark:bg-white/[0.02]">
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300" for="company_logo">
                        Logo thương hiệu (PNG, JPG, WebP; tối đa 2 MB)
                    </label>
                    <input type="file"
                           id="company_logo"
                           name="company_logo"
                           accept="image/png,image/jpeg,image/webp"
                           class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-brand-500 file:text-white hover:file:bg-brand-600 cursor-pointer">
                    <?php if (!empty($settings['company_logo'])): ?>
                        <div class="mt-2.5 flex items-center justify-between rounded-lg border border-gray-200 bg-white p-2 dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center gap-2">
                                <img src="Public/uploads/company/<?= $escape($settings['company_logo']) ?>"
                                     alt="Logo hiện tại"
                                     class="h-8 w-auto max-w-[120px] object-contain">
                                <span class="text-[11px] text-gray-500 dark:text-gray-400">Logo đang dùng</span>
                            </div>
                            <label class="flex items-center gap-1.5 text-xs text-red-600 hover:text-red-700 cursor-pointer select-none">
                                <input type="checkbox" name="remove_logo" value="1" class="h-3.5 w-3.5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span>Xóa logo</span>
                            </label>
                        </div>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300" for="company_favicon">
                        Favicon website (ICO, PNG; tối đa 2 MB)
                    </label>
                    <input type="file"
                           id="company_favicon"
                           name="company_favicon"
                           accept="image/x-icon,image/png,image/vnd.microsoft.icon"
                           class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-brand-500 file:text-white hover:file:bg-brand-600 cursor-pointer">
                    <?php if (!empty($settings['company_favicon'])): ?>
                        <div class="mt-2.5 flex items-center justify-between rounded-lg border border-gray-200 bg-white p-2 dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center gap-2">
                                <img src="Public/uploads/company/<?= $escape($settings['company_favicon']) ?>"
                                     alt="Favicon hiện tại"
                                     class="h-6 w-6 object-contain">
                                <span class="text-[11px] text-gray-500 dark:text-gray-400">Favicon đang dùng</span>
                            </div>
                            <label class="flex items-center gap-1.5 text-xs text-red-600 hover:text-red-700 cursor-pointer select-none">
                                <input type="checkbox" name="remove_favicon" value="1" class="h-3.5 w-3.5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span>Xóa favicon</span>
                            </label>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300" for="company_name">
                        Tên công ty / Doanh nghiệp <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="company_name"
                           name="company_name"
                           value="<?= $escape($settings['company_name'] ?? 'LTR Company') ?>"
                           class="w-full rounded-lg border border-gray-300 px-3.5 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           required>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300" for="company_slogan">
                        Khẩu hiệu / Slogan
                    </label>
                    <input type="text"
                           id="company_slogan"
                           name="company_slogan"
                           value="<?= $escape($settings['company_slogan'] ?? 'Giải pháp công nghệ toàn diện cho doanh nghiệp') ?>"
                           class="w-full rounded-lg border border-gray-300 px-3.5 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300" for="company_phone">
                        Hotline / Số điện thoại
                    </label>
                    <input type="text"
                           id="company_phone"
                           name="company_phone"
                           value="<?= $escape($settings['company_phone'] ?? '(028) 3864 7256') ?>"
                           class="w-full rounded-lg border border-gray-300 px-3.5 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300" for="company_email">
                        Email liên hệ
                    </label>
                    <input type="email"
                           id="company_email"
                           name="company_email"
                           value="<?= $escape($settings['company_email'] ?? 'contact@ltrcompany.vn') ?>"
                           class="w-full rounded-lg border border-gray-300 px-3.5 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300" for="company_address">
                    Địa chỉ trụ sở
                </label>
                <input type="text"
                       id="company_address"
                       name="company_address"
                       value="<?= $escape($settings['company_address'] ?? '268 Lý Thường Kiệt, Phường 14, Quận 10, TP. Hồ Chí Minh') ?>"
                       class="w-full rounded-lg border border-gray-300 px-3.5 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300" for="company_hours">
                    Giờ làm việc
                </label>
                <input type="text"
                       id="company_hours"
                       name="company_hours"
                       value="<?= $escape($settings['company_hours'] ?? 'Thứ 2 – Thứ 6: 8:00 – 17:30 | Thứ 7: 8:00 – 12:00') ?>"
                       class="w-full rounded-lg border border-gray-300 px-3.5 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300" for="company_intro">
                    Giới thiệu ngắn trên Trang Chủ
                </label>
                <textarea id="company_intro"
                          name="company_intro"
                          rows="3"
                          class="w-full rounded-lg border border-gray-300 px-3.5 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white"><?= $escape($settings['company_intro'] ?? 'Chúng tôi cung cấp các giải pháp công nghệ hàng đầu, giúp doanh nghiệp vận hành hiệu quả và phát triển bền vững.') ?></textarea>
            </div>

            <div class="pt-3">
                <button type="submit"
                        class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 focus:outline-none transition">
                    Lưu cấu hình
                </button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../../Layouts/Backend/footer.php'; ?>
