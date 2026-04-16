<?php
    $offset = 0;
    $limit = 10;
    if (isset($_GET["p"]) && $_GET["p"] > 0) {
        $offset = ($_GET["p"] - 1) * $limit;
    }

    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $where = "";
    if ($search != "") {
        $searchEsc = addslashes($search);
        $where = " WHERE o.Id LIKE '%$searchEsc%' OR o.receiverName LIKE '%$searchEsc%' OR o.receiverEmail LIKE '%$searchEsc%' OR o.receiverPhone LIKE '%$searchEsc%'";
    }

    // Lấy danh sách đơn hàng kèm tổng tiền thực tế từ order_detail
    // Ưu tiên SUM(price * quantity) để tránh total_price=0 do lỗi dữ liệu cũ
    $sql = "SELECT o.*,
                CASE
                    WHEN SUM(d.price * d.quantity) > 0 THEN SUM(d.price * d.quantity)
                    WHEN SUM(d.total_price) > 0 THEN SUM(d.total_price)
                    ELSE COALESCE(o.total_amount, 0)
                END as real_total
            FROM orders o
            LEFT JOIN order_detail d ON d.order_id = o.Id
            $where
            GROUP BY o.Id
            ORDER BY o.Id DESC
            LIMIT $offset, $limit";
    $result = $f->getAll($sql);
?>

<div class="row align-items-end mb-5">
    <div class="col-md-7">
        <h1 class="page-title mb-2">Quản lý đơn hàng</h1>
        <p style="color: #999; font-size: 0.9rem; font-weight: 500;">Danh sách các đơn hàng của khách hàng trên hệ thống.</p>
    </div>
    <div class="col-md-5">
        <form action="index.php" method="GET" class="d-flex gap-2 justify-content-md-end">
            <input type="hidden" name="page" value="orderList">
            <div style="position: relative; width: 100%; max-width: 320px;">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm mã đơn hoặc tên..." 
                       class="form-control form-control-pill" 
                       style="padding-right: 55px; border: 1.5px solid #eee; background: #fff;">
                <button type="submit" class="btn-luxury btn-circle" 
                        style="position: absolute; right: 4px; top: 4px; width: 38px; height: 38px; box-shadow: none;">
                    <i class="fa fa-search" style="font-size: 0.8rem;"></i>
                </button>
            </div>
            <?php if ($search != ""): ?>
                <a href="index.php?page=orderList" class="btn-luxury btn-circle btn-luxury-outline" title="Xoá tìm kiếm">
                    <i class="fa fa-times" style="font-size: 0.8rem;"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="luxury-card p-0" style="overflow: hidden;">
    <div class="table-responsive">
        <table class="table-luxury">
            <thead>
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th>Khách hàng</th>
                    <th>Liên hệ</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th style="width: 100px; text-align: center;">Xem</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($result) > 0): ?>
                    <?php foreach ($result as $value): ?>
                        <?php 
                            $total = $value['real_total'] ?? 0;
                            $status = $value['status'] ?? 'Pending';
                            $statusLabel = $status;
                            $statusClass = 'status-' . strtolower(str_replace(' ', '-', $status));
                            
                            // Map Vietnamese statuses to classes if needed
                            if ($status == 'Đã giao') $statusClass = 'status-completed';
                            if ($status == 'Huỷ') $statusClass = 'status-cancelled';
                            if ($status == 'Sẵn sàng') $statusClass = 'status-shipped';
                        ?>
                        <tr>
                            <td data-label="ID">
                                <span style="color: #bbb; font-weight: 600;">#<?= $value['Id'] ?></span>
                            </td>
                            <td data-label="Khách hàng">
                                <div style="font-weight: 700; color: #111;"><?= $value['receiverName'] ?></div>
                                <div style="font-size: 0.8rem; color: #999;"><?= $value['receiverEmail'] ?></div>
                            </td>
                            <td data-label="Liên hệ">
                                <div style="font-size: 0.9rem; font-weight: 500;"><?= $value['receiverPhone'] ?></div>
                                <div style="font-size: 0.75rem; color: #999; font-weight: 500;"><?= $value['receiverAddress'] ?></div>
                            </td>
                            <td data-label="Ngày đặt">
                                <span style="font-weight: 600; font-size: 0.85rem; color: #666;"><?= date('d/m/Y', strtotime($value['order_date'])) ?></span>
                            </td>
                            <td data-label="Tổng tiền">
                                <span style="font-weight: 700; color: var(--gold);"><?= number_format($total) ?>₫</span>
                            </td>
                            <td data-label="Trạng thái">
                                <span class="status-badge <?= $statusClass ?>">
                                    <?= $statusLabel ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; justify-content: center;">
                                    <a href="<?= PATH_ADMIN ?>page=orderDetail&orderId=<?= $value['Id'] ?>" class="btn-luxury btn-circle" title="Xem chi tiết">
                                        <i class="fa fa-eye" style="font-size: 0.8rem;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 60px;">
                            <i class="fa fa-shopping-bag mb-3" style="font-size: 2.5rem; color: #eee; display: block;"></i>
                            <span style="color: #bbb; font-weight: 500;"><?= $search ? "Không tìm thấy đơn hàng nào." : "Chưa có đơn hàng nào." ?></span>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row" style="margin-top: 40px; display: flex; justify-content: center;">
    <?php
        $sqlCount = "SELECT COUNT(DISTINCT o.Id) as total FROM orders o $where";
        $resCount = $f->getOne($sqlCount);
        $totalPages = ceil($resCount["total"] / $limit);
    ?>
    <?php if ($totalPages > 1): ?>
    <div class="pagination-luxury">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php $active = (isset($_GET['p']) && $_GET['p'] == $i) || (!isset($_GET['p']) && $i == 1); ?>
            <a href="<?= PATH_ADMIN ?>page=orderList&search=<?= urlencode($search) ?>&p=<?= $i ?>" class="pagination-item <?= $active ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>
