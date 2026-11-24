<?php
$pageTitle = 'Trả lời & Quản lý Hỏi/Đáp (FAQ)';
$activeStaffPage = 'faqs';
require __DIR__ . '/../../Layouts/Backend/staff_header.php';
?>

<div class="space-y-6">
    <?php if (!empty($flash)): ?>
        <div class="rounded-xl border px-4 py-3 text-sm <?= $flash['type'] === 'danger' ? 'border-red-200 bg-red-50 text-red-800' : 'border-green-200 bg-green-50 text-green-800' ?>">
            <?= $escape($flash['message']) ?>
        </div>
    <?php endif; ?>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
            <a href="index.php?controller=staff&amp;action=faqs"
               class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $statusFilter === null ? 'bg-brand-500 text-white' : 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-50' ?>">
                Tất cả (<?= (int) $allCount ?>)
            </a>
            <a href="index.php?controller=staff&amp;action=faqs&amp;status=0"
               class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $statusFilter === 0 ? 'bg-amber-500 text-white' : 'border border-amber-200 bg-white text-amber-700 hover:bg-amber-50' ?>">
                Chờ trả lời (<?= (int) $pendingCount ?>)
            </a>
            <a href="index.php?controller=staff&amp;action=faqs&amp;status=1"
               class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $statusFilter === 1 ? 'bg-emerald-600 text-white' : 'border border-emerald-200 bg-white text-emerald-700 hover:bg-emerald-50' ?>">
                Đã trả lời &amp; Hiển thị
            </a>
            <a href="index.php?controller=staff&amp;action=faqs&amp;status=2"
               class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $statusFilter === 2 ? 'bg-gray-600 text-white' : 'border border-gray-200 bg-white text-gray-600 hover:bg-gray-50' ?>">
                Đã ẩn
            </a>
        </div>
        <a href="index.php?controller=staff&amp;action=faq-create"
           class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2 text-xs font-medium text-white hover:bg-brand-600">
            + Thêm FAQ mới
        </a>
    </div>

    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3.5">Câu hỏi &amp; Người gửi</th>
                        <th class="px-4 py-3.5">Chủ đề</th>
                        <th class="px-4 py-3.5">Trạng thái</th>
                        <th class="px-4 py-3.5">Người trả lời</th>
                        <th class="px-4 py-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($faqs)): ?>
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Chưa có câu hỏi nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($faqs as $faq): ?>
                            <?php $st = (int) $faq['status']; ?>
                            <tr>
                                <td class="px-4 py-3.5 max-w-md">
                                    <div class="font-medium text-gray-800"><?= $escape($faq['question']) ?></div>
                                    <div class="mt-0.5 text-xs text-gray-400">
                                        Gửi bởi: <?= $escape($faq['asker_name'] ?: ($faq['creator_name'] ?: 'Khách')) ?>
                                        <?= !empty($faq['asker_email']) ? ' (' . $escape($faq['asker_email']) . ')' : '' ?>
                                    </div>
                                    <?php if (!empty($faq['answer'])): ?>
                                        <div class="mt-1 text-xs text-gray-500 truncate">Trả lời: <?= $escape(excerpt($faq['answer'], 90)) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-xs text-gray-600"><?= $escape($faq['category'] ?: '—') ?></td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <?php if ($st === 0): ?>
                                        <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">Chờ trả lời</span>
                                    <?php elseif ($st === 1): ?>
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Đang hiển thị</span>
                                    <?php else: ?>
                                        <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Đã ẩn</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-xs text-gray-500 whitespace-nowrap">
                                    <?= $escape($faq['staff_name'] ?: '—') ?>
                                </td>
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="index.php?controller=staff&amp;action=faq-edit&amp;id=<?= (int) $faq['id'] ?>"
                                           class="rounded-lg <?= $st === 0 ? 'bg-brand-500 text-white px-3 py-1' : 'border border-gray-200 px-2.5 py-1 text-brand-500 hover:bg-gray-50' ?> text-xs font-medium">
                                            <?= $st === 0 ? 'Trả lời ngay' : 'Sửa' ?>
                                        </a>
                                        <form method="post" action="index.php?controller=staff&amp;action=faq-delete"
                                              data-confirm="Bạn có chắc muốn xóa câu hỏi này?">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int) $faq['id'] ?>">
                                            <button type="submit" class="rounded border border-red-200 px-2 py-1 text-xs text-red-600 hover:bg-red-50">Xóa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($pagination['totalPages'] > 1): ?>
            <nav class="flex items-center justify-between border-t border-gray-100 bg-gray-50 px-5 py-3" aria-label="Phân trang">
                <span class="text-xs text-gray-500">Trang <?= (int) $pagination['page'] ?> / <?= (int) $pagination['totalPages'] ?></span>
                <div class="flex gap-1">
                    <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                        <a href="<?= $escape(query_url(['page' => $i])) ?>"
                           class="rounded-lg px-3 py-1.5 text-xs font-medium <?= $i === $pagination['page'] ? 'bg-brand-500 text-white' : 'border border-gray-200 bg-white text-gray-700' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </nav>
        <?php endif; ?>
    </section>
</div>

<?php require __DIR__ . '/../../Layouts/Backend/footer.php'; ?>
