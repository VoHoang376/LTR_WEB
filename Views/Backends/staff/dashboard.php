<?php
$pageTitle = 'Bảng điều khiển CSKH';
$activeStaffPage = 'dashboard';
require __DIR__ . '/../../Layouts/Backend/staff_header.php';
?>

<div class="space-y-6">
    <section class="admin-dashboard-grid">
        <article class="rounded-2xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-medium uppercase tracking-wider text-gray-500">Liên hệ chờ xử lý</h2>
                <?php if ($unprocessedContacts > 0): ?>
                    <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">Cần trả lời</span>
                <?php endif; ?>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-800"><?= (int) $unprocessedContacts ?></p>
            <p class="mt-2 text-xs text-gray-500">
                Tổng <?= (int) $totalContacts ?> liên hệ ·
                <a href="index.php?controller=staff&amp;action=contacts&amp;status=0" class="text-brand-500 hover:underline">Xử lý ngay &rarr;</a>
            </p>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-medium uppercase tracking-wider text-gray-500">Câu hỏi FAQ mới</h2>
                <?php if ($pendingFaqs > 0): ?>
                    <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">Chờ trả lời</span>
                <?php endif; ?>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-800"><?= (int) $pendingFaqs ?></p>
            <p class="mt-2 text-xs text-gray-500">
                Tổng <?= (int) $totalFaqs ?> câu hỏi ·
                <a href="index.php?controller=staff&amp;action=faqs&amp;status=0" class="text-brand-500 hover:underline">Trả lời ngay &rarr;</a>
            </p>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-medium uppercase tracking-wider text-gray-500">Phản hồi sản phẩm</h2>
                <?php if ($unrepliedReviews > 0): ?>
                    <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700">Chưa phản hồi</span>
                <?php endif; ?>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-800"><?= (int) $unrepliedReviews ?></p>
            <p class="mt-2 text-xs text-gray-500">
                Tổng <?= (int) $totalReviews ?> đánh giá ·
                <a href="index.php?controller=staff&amp;action=reviews&amp;filter=unreplied" class="text-brand-500 hover:underline">Phản hồi &rarr;</a>
            </p>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-medium uppercase tracking-wider text-gray-500">Bình luận bài viết</h2>
                <?php if (!empty($hiddenComments)): ?>
                    <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-700"><?= (int) $hiddenComments ?> đã gỡ</span>
                <?php endif; ?>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-800"><?= (int) ($visibleComments ?? 0) ?></p>
            <p class="mt-2 text-xs text-gray-500">
                Đang hiển thị (Tổng <?= (int) $totalComments ?>) ·
                <a href="index.php?controller=staff&amp;action=comments" class="text-brand-500 hover:underline">Xem &amp; gỡ vi phạm &rarr;</a>
            </p>
        </article>
    </section>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <!-- Câu hỏi FAQ người dùng mới gửi -->
        <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <h2 class="text-sm font-semibold text-gray-800">Câu hỏi Hỏi/Đáp đang chờ trả lời</h2>
                <a href="index.php?controller=staff&amp;action=faqs&amp;status=0" class="text-xs font-medium text-brand-500 hover:underline">Xem tất cả &rarr;</a>
            </div>
            <div class="divide-y divide-gray-100">
                <?php if (empty($recentPendingFaqs)): ?>
                    <p class="px-5 py-6 text-center text-sm text-gray-400">Không có câu hỏi nào đang chờ trả lời.</p>
                <?php else: ?>
                    <?php foreach ($recentPendingFaqs as $faq): ?>
                        <div class="flex items-start justify-between gap-3 px-5 py-3.5">
                            <div>
                                <p class="text-sm font-medium text-gray-800"><?= $escape($faq['question']) ?></p>
                                <p class="mt-0.5 text-xs text-gray-400">
                                    <?= $escape($faq['asker_name'] ?: ($faq['creator_name'] ?: 'Khách')) ?> · <?= $escape(format_datetime($faq['created_at'])) ?>
                                </p>
                            </div>
                            <a href="index.php?controller=staff&amp;action=faq-edit&amp;id=<?= (int) $faq['id'] ?>"
                               class="shrink-0 rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600">
                                Trả lời
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Phản hồi sản phẩm chưa trả lời -->
        <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <h2 class="text-sm font-semibold text-gray-800">Phản hồi sản phẩm chưa trả lời</h2>
                <a href="index.php?controller=staff&amp;action=reviews&amp;filter=unreplied" class="text-xs font-medium text-brand-500 hover:underline">Xem tất cả &rarr;</a>
            </div>
            <div class="divide-y divide-gray-100">
                <?php if (empty($recentUnrepliedReviews)): ?>
                    <p class="px-5 py-6 text-center text-sm text-gray-400">Tất cả phản hồi sản phẩm đã được trả lời.</p>
                <?php else: ?>
                    <?php foreach ($recentUnrepliedReviews as $rev): ?>
                        <div class="flex items-start justify-between gap-3 px-5 py-3.5">
                            <div>
                                <p class="text-xs font-semibold text-brand-500"><?= $escape($rev['product_name']) ?> (<?= (int) $rev['rating'] ?>★)</p>
                                <p class="mt-0.5 text-sm text-gray-700"><?= $escape(excerpt($rev['comment'], 100)) ?></p>
                                <p class="mt-0.5 text-xs text-gray-400"><?= $escape($rev['author_name']) ?> · <?= $escape(format_datetime($rev['created_at'])) ?></p>
                            </div>
                            <a href="index.php?controller=staff&amp;action=reviews&amp;filter=unreplied"
                               class="shrink-0 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                Phản hồi
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- Liên hệ chưa xử lý -->
    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
            <h2 class="text-sm font-semibold text-gray-800">Liên hệ khách hàng chờ xử lý</h2>
            <a href="index.php?controller=staff&amp;action=contacts" class="text-xs font-medium text-brand-500 hover:underline">Quản lý liên hệ &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Khách hàng</th>
                        <th class="px-5 py-3">Email / SĐT</th>
                        <th class="px-5 py-3">Nội dung</th>
                        <th class="px-5 py-3">Thời gian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($recentContacts)): ?>
                        <tr><td colspan="4" class="px-5 py-6 text-center text-gray-400">Không có liên hệ nào đang chờ xử lý.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentContacts as $c): ?>
                            <tr>
                                <td class="px-5 py-3.5 font-medium text-gray-800"><?= $escape($c['fullname']) ?></td>
                                <td class="px-5 py-3.5 text-xs"><?= $escape($c['email']) ?><?= !empty($c['phone']) ? ' · ' . $escape($c['phone']) : '' ?></td>
                                <td class="px-5 py-3.5 text-xs max-w-md truncate"><?= $escape($c['message']) ?></td>
                                <td class="px-5 py-3.5 text-xs text-gray-400"><?= $escape(format_datetime($c['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php require __DIR__ . '/../../Layouts/Backend/footer.php'; ?>
