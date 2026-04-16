<?php
require_once "../../lib/coreFunction.php";
$f = new CoreFunction();

$filter = $_POST['filter'] ?? 'all';
$orderId = $_POST['order_id'] ?? null;
$dateFilter = "";

if ($filter == 'today') {
    $dateFilter = "AND DATE(COALESCE(o.completed_date, o.order_date)) = CURRENT_DATE()";
} else if ($filter == 'month') {
    $dateFilter = "AND MONTH(COALESCE(o.completed_date, o.order_date)) = MONTH(CURRENT_DATE()) AND YEAR(COALESCE(o.completed_date, o.order_date)) = YEAR(CURRENT_DATE())";
}

// IF SHOWING DETAIL VIEW
if ($orderId) {
    $sql = "SELECT o.*, COALESCE(SUM(d.price * d.quantity), 0) as real_total 
            FROM orders o 
            LEFT JOIN order_detail d ON o.Id = d.order_id 
            WHERE o.Id = $orderId
            GROUP BY o.Id";
    $o = $f->getOne($sql);
    
    if (!$o) {
        echo "<div class='p-5 text-center'>Không tìm thấy đơn hàng.</div>";
        exit;
    }
    ?>
    <div class="p-4" style="font-family: 'Poppins', sans-serif; background: #fff;">
        <!-- Back Button -->
        <button type="button"
                style="background: #f8f8f8; border: 1px solid #eee; border-radius: 12px; padding: 10px 20px; font-weight: 600; color: #555; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 24px; cursor: pointer;"
                onclick="openRevenueModal('<?= $filter ?>')"
                onmouseover="this.style.background='#eee'"
                onmouseout="this.style.background='#f8f8f8'">
            <i class="fa fa-arrow-left" style="color: var(--gold);"></i> Quay lại danh sách
        </button>

        <!-- Order Header -->
        <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 30px; padding: 20px; background: #fdfaf7; border-radius: 16px; border: 1px solid #f0e8d0;">
            <div style="flex: 1; min-width: 140px;">
                <div style="color: #999; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 4px;">ID đơn</div>
                <div style="font-weight: 800; font-size: 1.2rem; color: #111;">#<?= $o['Id'] ?></div>
            </div>
            <div style="flex: 2; min-width: 160px;">
                <div style="color: #999; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 4px;">Khách hàng</div>
                <div style="font-weight: 700; font-size: 1.1rem; color: #111;"><?= htmlspecialchars($o['receiverName']) ?></div>
            </div>
            <div style="flex: 1; min-width: 140px; text-align: right;">
                <div style="color: #999; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 4px;">Tổng đơn hàng</div>
                <div style="font-weight: 800; font-size: 1.5rem; color: #d4af37;"><?= number_format($o['real_total']) ?>₫</div>
            </div>
        </div>

        <!-- Product Table — mobile card layout -->
        <table class="table-luxury">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th style="text-align: center; width: 70px;">SL</th>
                    <th style="text-align: right; width: 110px;">Đơn giá</th>
                    <th style="text-align: right; width: 120px;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $details = $f->getAll("SELECT d.*, p.name FROM order_detail d JOIN product p ON d.product_id = p.id WHERE d.order_id = " . $o['Id']);
                    foreach($details as $d):
                        $lineTotal = ($d['price'] * $d['quantity']) > 0 ? ($d['price'] * $d['quantity']) : $d['total_price'];
                ?>
                <tr>
                    <td data-label="Sản phẩm">
                        <div style="font-weight: 600; color: #222;"><?= htmlspecialchars($d['name']) ?></div>
                        <div style="font-size: 0.75rem; color: #888; margin-top: 2px;">Size: <?= $d['size'] ?: 'Free' ?></div>
                    </td>
                    <td data-label="SL" style="text-align: center;"><?= $d['quantity'] ?></td>
                    <td data-label="Đơn giá" style="text-align: right; color: #666;"><?= number_format($d['price']) ?>₫</td>
                    <td data-label="Thành tiền" style="text-align: right; font-weight: 700; color: #111;"><?= number_format($lineTotal) ?>₫</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    exit;
}

// ELSE SHOWING LIST VIEW
$sql = "SELECT o.*, COALESCE(SUM(d.price * d.quantity), 0) as real_total 
        FROM orders o 
        LEFT JOIN order_detail d ON o.Id = d.order_id 
        WHERE o.status = 'Completed' $dateFilter 
        GROUP BY o.Id 
        ORDER BY COALESCE(o.completed_date, o.order_date) DESC, o.Id DESC";

$orders = $f->getAll($sql);

if (count($orders) == 0) {
    echo "<div class='text-center py-5 text-muted'><i class='fa fa-folder-open mb-3' style='font-size:2rem; opacity:0.5;'></i><br>Không có đơn hàng nào trong khoảng thời gian này.</div>";
    exit;
}
?>

<div style="padding: 16px;">
    <table class="table-luxury">
        <thead>
            <tr>
                <th style="width: 80px;">Mã đơn</th>
                <th>Ngày đặt</th>
                <th>Ngày hoàn thành</th>
                <th>Tổng tiền</th>
                <th style="width: 80px; text-align: center;">Chi tiết</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders as $o): ?>
                <tr>
                    <td data-label="Mã đơn" style="font-weight: 700; color: #111;">#<?= $o['Id'] ?></td>
                    <td data-label="Ngày đặt" style="color: #666; font-size: 0.9rem;"><?= date('d/m/Y', strtotime($o['order_date'])) ?></td>
                    <td data-label="Ngày hoàn thành" style="font-size: 0.9rem; font-weight: 600;">
                        <?php if (!empty($o['completed_date'])): ?>
                            <span style="color: #27ae60;"><i class="fa fa-check-circle" style="margin-right: 4px;"></i><?= date('d/m/Y H:i', strtotime($o['completed_date'])) ?></span>
                        <?php else: ?>
                            <span style="color: #bbb;">&#8212;</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Tổng tiền" style="font-weight: 700; color: var(--gold);"><?= number_format($o['real_total']) ?>₫</td>
                    <td data-label="Chi tiết" style="text-align: center;">
                        <button type="button"
                                style="width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: var(--gold-gradient); border: none; color: #433422; cursor: pointer; box-shadow: 0 4px 10px rgba(212,175,55,0.3);"
                                onclick="showRevenueDetail(<?= $o['Id'] ?>, '<?= $filter ?>')">
                            <i class="fa fa-eye" style="font-size: 0.8rem;"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>
</div>
