<?php
/** Quản trị – danh sách đơn hàng: tìm kiếm, lọc trạng thái, lọc ngày, phân trang. */
require __DIR__ . '/../layouts/header.php';

$statusLabels = [
    '' => 'Tất cả',
    0 => 'Chờ xác nhận',
    1 => 'Đã xác nhận',
    2 => 'Đang giao',
    3 => 'Hoàn tất',
    4 => 'Đã hủy',
];
$totalOrders = array_sum($statusCounts);
$currentStatus = (string) ($_GET['status'] ?? '');
?>
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <span class="stat-icon"><i class="bi bi-receipt" aria-hidden="true"></i></span>
            <div>
                <div class="stat-value"><?= (int) $totalOrders ?></div>
                <div class="stat-label">Tổng đơn hàng</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <span class="stat-icon text-warning bg-warning-subtle"><i class="bi bi-hourglass-split" aria-hidden="true"></i></span>
            <div>
                <div class="stat-value"><?= (int) ($statusCounts[0] ?? 0) ?></div>
                <div class="stat-label">Chờ xác nhận</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <span class="stat-icon text-primary bg-primary-subtle"><i class="bi bi-truck" aria-hidden="true"></i></span>
            <div>
                <div class="stat-value"><?= (int) (($statusCounts[1] ?? 0) + ($statusCounts[2] ?? 0)) ?></div>
                <div class="stat-label">Đang xử lý / giao</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <span class="stat-icon text-success bg-success-subtle"><i class="bi bi-cash-coin" aria-hidden="true"></i></span>
            <div>
                <div class="stat-value" style="font-size: 1rem;"><?= e(format_price($revenue)) ?></div>
                <div class="stat-label">Doanh thu hoàn tất</div>
            </div>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="card-head">
        <div>
            <h2>Danh sách đơn hàng</h2>
            <p class="small text-secondary mb-0">
                Hiển thị <?= (int) $result['from'] ?>–<?= (int) $result['to'] ?> trong <?= (int) $result['total'] ?> đơn
            </p>
        </div>
    </div>

    <div class="card-body border-bottom">
        <div class="d-flex flex-wrap gap-2 mb-3">
            <?php foreach ($statusLabels as $statusKey => $statusLabel): ?>
                <?php
                $isActive = $currentStatus === (string) $statusKey;
                $count = $statusKey === '' ? $totalOrders : ($statusCounts[(int) $statusKey] ?? 0);
                ?>
                <a class="status-pill <?= $isActive ? 'text-bg-primary' : 'text-bg-light border' ?>"
                   href="<?= e(query_url(['status' => $statusKey === '' ? null : $statusKey, 'page' => null])) ?>">
                    <?= e($statusLabel) ?>
                    <span class="ms-1 opacity-75">(<?= (int) $count ?>)</span>
                </a>
            <?php endforeach; ?>
        </div>

        <form class="filter-bar" method="get" action="index.php" role="search">
            <input type="hidden" name="controller" value="admin">
            <input type="hidden" name="action" value="orders">
            <?php if ($currentStatus !== ''): ?>
                <input type="hidden" name="status" value="<?= e($currentStatus) ?>">
            <?php endif; ?>
            <div>
                <label class="form-label" for="order-q">Từ khoá</label>
                <input type="search" class="form-control form-control-sm" id="order-q" name="q"
                       value="<?= e($_GET['q'] ?? '') ?>" placeholder="Mã đơn, tên, SĐT, email...">
            </div>
            <div>
                <label class="form-label" for="date-from">Từ ngày</label>
                <input type="date" class="form-control form-control-sm" id="date-from" name="date_from"
                       value="<?= e($_GET['date_from'] ?? '') ?>">
            </div>
            <div>
                <label class="form-label" for="date-to">Đến ngày</label>
                <input type="date" class="form-control form-control-sm" id="date-to" name="date_to"
                       value="<?= e($_GET['date_to'] ?? '') ?>">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search me-1" aria-hidden="true"></i>Tìm
                </button>
                <a class="btn btn-outline-secondary btn-sm" href="index.php?controller=admin&amp;action=orders">Xoá lọc</a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-admin align-middle">
            <caption class="visually-hidden">Danh sách đơn hàng</caption>
            <thead>
            <tr>
                <th scope="col">Mã đơn</th>
                <th scope="col">Khách hàng</th>
                <th scope="col" class="text-center">SL</th>
                <th scope="col" class="text-end">Tổng tiền</th>
                <th scope="col" class="text-center">Thanh toán</th>
                <th scope="col" class="text-center">Trạng thái</th>
                <th scope="col">Ngày đặt</th>
                <th scope="col" class="text-end">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($result['items'] === []): ?>
                <tr>
                    <td colspan="8">
                        <div class="table-empty">
                            <i class="bi bi-receipt fs-3 d-block mb-2" aria-hidden="true"></i>
                            Không tìm thấy đơn hàng nào phù hợp.
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($result['items'] as $order): ?>
                    <tr>
                        <td>
                            <a class="fw-bold text-decoration-none"
                               href="index.php?controller=admin&amp;action=order-show&amp;id=<?= (int) $order['id'] ?>">
                                #<?= str_pad((string) $order['id'], 6, '0', STR_PAD_LEFT) ?>
                            </a>
                        </td>
                        <td>
                            <span class="d-block fw-semibold"><?= e($order['receiver_name']) ?></span>
                            <span class="small text-secondary"><?= e($order['receiver_phone']) ?></span>
                        </td>
                        <td class="text-center"><?= (int) $order['total_quantity'] ?></td>
                        <td class="text-end fw-semibold text-nowrap"><?= e(format_price($order['total_amount'])) ?></td>
                        <td class="text-center small text-secondary">
                            <?= (int) $order['payment_method'] === 1 ? 'Chuyển khoản' : 'COD' ?>
                        </td>
                        <td class="text-center">
                            <span class="status-pill text-bg-<?= e(order_status_badge($order['status'])) ?>">
                                <?= e(order_status_label($order['status'])) ?>
                            </span>
                        </td>
                        <td class="small text-secondary text-nowrap"><?= e(format_datetime($order['created_at'])) ?></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary"
                               href="index.php?controller=admin&amp;action=order-show&amp;id=<?= (int) $order['id'] ?>">
                                <i class="bi bi-eye me-1" aria-hidden="true"></i>Xem
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($result['totalPages'] > 1): ?>
        <div class="card-body">
            <?= render_pagination($result) ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
