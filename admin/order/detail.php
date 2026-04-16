<?php
    if (!isset($_GET['orderId'])) {
        header("Location: " . PATH_ADMIN . "page=orderList");
        exit();
    }

    $orderId = $_GET['orderId'];

    // Handle Status Update
    if (isset($_POST['update_status'])) {
        $newStatus = $_POST['status'];
        $oldStatus = $f->getOne("SELECT status FROM orders WHERE Id = $orderId")['status'] ?? '';

        // Nếu chuyển sang Cancelled và trước đó KHÔNG PHẢI Cancelled -> CỘNG LẠI KHO
        if($newStatus == 'Cancelled' && $oldStatus != 'Cancelled') {
            $items = $f->getAll("SELECT * FROM order_detail WHERE order_id = $orderId");
            foreach($items as $item) {
                $pid = $item['product_id'];
                $qty_to_restore = $item['quantity'];
                $size_to_restore = $item['size'];

                $p = $f->getOne("SELECT sizes FROM product WHERE id = $pid");
                if($p) {
                    $sizes_str = $p['sizes'];
                    $sizes_arr = explode(",", $sizes_str);
                    $new_sizes_list = [];
                    foreach($sizes_arr as $s_item) {
                        $s_item = trim($s_item);
                        if($s_item == "") continue;
                        $parts = explode(":", $s_item);
                        $sName = trim($parts[0]);
                        $sQty = isset($parts[1]) ? intval($parts[1]) : 0;

                        if($sName == $size_to_restore) {
                            $sQty += $qty_to_restore;
                        }
                        $new_sizes_list[] = $sName . ":" . $sQty;
                    }
                    $new_sizes_str = implode(",", $new_sizes_list);
                    $f->setQuery("UPDATE product SET sizes = '$new_sizes_str' WHERE id = $pid");
                }
            }
        }
        // Nếu từ Cancelled chuyển sang trạng thái khác -> TRỪ LẠI KHO (nếu cần quản lý chặt chẽ hơn)
        // Nhưng yêu cầu chỉ nói về Cancelled -> Cộng lại kho.

        // Nếu chuyển sang Completed -> ghi nhận ngày hoàn thành
        if ($newStatus == 'Completed') {
            $f->setQuery("UPDATE orders SET status = '$newStatus', completed_date = NOW() WHERE Id = $orderId");
        } else {
            // Nếu chuyển ra khỏi Completed -> xóa ngày hoàn thành
            $f->setQuery("UPDATE orders SET status = '$newStatus', completed_date = NULL WHERE Id = $orderId");
        }
        header("Location: " . PATH_ADMIN . "page=orderDetail&orderId=$orderId&success=" . urlencode("Cập nhật trạng thái thành công!"));
        exit();
    }

    $order = $f->getOne("SELECT * FROM orders WHERE Id = $orderId");
    if (!$order) {
        echo "<div class='luxury-card'>Không tìm thấy đơn hàng.</div>";
        return;
    }

    $details = $f->getAll("SELECT d.*, p.name as product_name, p.image FROM order_detail d JOIN product p ON d.product_id = p.id WHERE d.order_id = $orderId");
    
    $totalAmount = 0;
    foreach($details as $d) {
        // Ưu tiên price * quantity; fallback sang total_price nếu có
        $lineTotal = ($d['price'] * $d['quantity']) > 0
            ? ($d['price'] * $d['quantity'])
            : $d['total_price'];
        $totalAmount += $lineTotal;
    }
?>

<div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px; margin-bottom: 40px;">
    <div>
        <a href="<?= PATH_ADMIN ?>page=orderList" style="color: var(--gold); font-size: 0.9rem; text-decoration: none; display: flex; align-items: center; gap: 5px; margin-bottom: 10px;">
            <i class="fa fa-arrow-left"></i> Quay lại danh sách
        </a>
        <h1 class="page-title">Chi tiết đơn hàng #<?= $orderId ?></h1>
    </div>
    <div style="padding-top: 30px;">
        <?php
            $status = $order['status'] ?? 'Pending';
            $statusClass = 'status-' . strtolower($status);
        ?>
        <span class="status-badge <?= $statusClass ?>">
            <?= $status ?>
        </span>
    </div>
</div>

<div class="order-detail-grid">
    <!-- Order Items -->
    <div>
        <div class="luxury-card" style="padding: 0; overflow: hidden;">
            <div style="padding: 25px; border-bottom: 1px solid #f0f0f0;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.2rem;">Sản phẩm trong đơn hàng</h3>
            </div>
            <div class="table-responsive">
            <table class="table-luxury">
                <thead>
                    <tr>
                        <th style="width: 80px;">Ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Size</th>
                        <th>Giá</th>
                        <th>SL</th>
                        <th style="text-align: right;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($details as $item): ?>
                    <tr>
                        <td data-label="Ảnh">
                            <img src="../asset/images/<?= $item['image'] ?>" class="product-img-admin" alt="">
                        </td>
                        <td data-label="Sản phẩm">
                            <div style="font-weight: 500;"><?= $item['product_name'] ?></div>
                            <div style="font-size: 0.75rem; color: #999;">ID: #<?= $item['product_id'] ?></div>
                        </td>
                        <td data-label="Size">
                            <span class="badge-luxury" style="background: #fdfaf7; color: #c9a96e; border: 1px solid #c9a96e;"><?= $item['size'] ?: 'N/A' ?></span>
                        </td>
                        <td data-label="Giá"><?= number_format($item['price']) ?>đ</td>
                        <td data-label="SL"><?= $item['quantity'] ?></td>
                        <td data-label="Thành tiền" style="text-align: right; font-weight: 600; color: var(--text);">
                            <?php 
                                $lineAmt = ($item['price'] * $item['quantity']) > 0 
                                    ? ($item['price'] * $item['quantity']) 
                                    : $item['total_price'];
                                echo number_format($lineAmt);
                            ?>đ
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <div style="padding: 25px; background: var(--bg); display: flex; justify-content: flex-end; align-items: center; gap: 40px;">
                <div style="text-align: right;">
                    <div style="font-size: 0.9rem; color: #999; margin-bottom: 5px;">Tổng cộng:</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent);"><?= number_format($totalAmount) ?>đ</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Info & Status Update -->
    <div>
        <div class="luxury-card mb-20">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; margin-bottom: 25px;">Thông tin khách hàng</h3>
            <div class="info-item" style="margin-bottom: 20px;">
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #999; margin-bottom: 5px;">Họ và tên</div>
                <div style="font-weight: 600;"><?= $order['receiverName'] ?></div>
            </div>
            <div class="info-item" style="margin-bottom: 20px;">
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #999; margin-bottom: 5px;">Số điện thoại</div>
                <div style="font-weight: 500;"><?= $order['receiverPhone'] ?></div>
            </div>
            <div class="info-item" style="margin-bottom: 20px;">
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #999; margin-bottom: 5px;">Email</div>
                <div style="font-weight: 500;"><?= $order['receiverEmail'] ?></div>
            </div>
            <div class="info-item">
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #999; margin-bottom: 5px;">Địa chỉ giao hàng</div>
                <div style="font-size: 0.9rem; line-height: 1.4;"><?= $order['receiverAddress'] ?></div>
            </div>
        </div>

        <div class="luxury-card">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; margin-bottom: 25px;">Trạng thái đơn hàng</h3>
            <form action="" method="POST" class="form-luxury">
                <div class="form-group mb-4">
                    <select name="status" class="form-control py-3">
                        <option value="Pending" <?= ($order['status'] ?? '') == 'Pending' ? 'selected' : '' ?>>Chờ xử lý (Pending)</option>
                        <option value="Processing" <?= ($order['status'] ?? '') == 'Processing' ? 'selected' : '' ?>>Đang xử lý (Processing)</option>
                        <option value="Shipped" <?= ($order['status'] ?? '') == 'Shipped' ? 'selected' : '' ?>>Đang giao hàng (Shipped)</option>
                        <option value="Completed" <?= ($order['status'] ?? '') == 'Completed' ? 'selected' : '' ?>>Hoàn thành (Completed)</option>
                        <option value="Cancelled" <?= ($order['status'] ?? '') == 'Cancelled' ? 'selected' : '' ?>>Đã huỷ (Cancelled)</option>
                    </select>
                </div>
                <div class="mt-4">
                    <button type="submit" name="update_status" class="btn-luxury py-3" style="width: 100%; justify-content: center;">
                        <i class="fa fa-sync-alt"></i> CẬP NHẬT TRẠNG THÁI
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.order-detail-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
    align-items: start;
}
@media (max-width: 900px) {
    .order-detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>
