<?php
    if(!isset($_SESSION['userId'])){
        exit(header("Location: index.php?page=login"));
    }

    // Xử lý huỷ đơn hàng nếu có yêu cầu
    if (isset($_POST['cancelOrderId'])) {
        $cancelId = intval($_POST['cancelOrderId']);
        $uId = $_SESSION['userId'];

        // Kiểm tra đúng đơn hàng của user và trạng thái cho phép huỷ
        $checkOrder = $f->getOne("SELECT status FROM orders WHERE Id = $cancelId AND user_Id = $uId");
        
        if ($checkOrder && ($checkOrder['status'] == 'Pending' || $checkOrder['status'] == 'Processing')) {
            // 1. Cập nhật trạng thái thành Cancelled
            $f->setQuery("UPDATE orders SET status = 'Cancelled' WHERE Id = $cancelId");

            // 2. LOGIC HOÀN KHO (RESTOCK)
            $items = $f->getAll("SELECT * FROM order_detail WHERE order_id = $cancelId");
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
            echo "<script>alert('Đơn hàng #$cancelId đã được huỷ và hoàn kho thành công!'); window.location.href='index.php?page=orders';</script>";
        } else {
            echo "<script>alert('Không thể huỷ đơn hàng này ở trạng thái hiện tại.');</script>";
        }
    }
?>

<style>
    .orders-container {
        max-width: 1100px;
        margin: 80px auto;
    }
    .luxury-card-orders {
        background: #fff;
        border-radius: 40px;
        padding: 50px;
        box-shadow: 0 30px 100px rgba(90, 74, 66, 0.05);
        border: 1px solid rgba(212, 175, 55, 0.08);
    }
    .gold-gradient-text {
        background: linear-gradient(135deg, #a67c00 0%, #bf953f 15%, #fcf6ba 40%, #b88a44 60%, #ddac17 85%, #8a6d3b 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        letter-spacing: 2px;
    }
    .orders-table thead th {
        border: none;
        background: #fdfaf7;
        color: #8a6d3b;
        font-family: 'Poppins', sans-serif;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        padding: 25px;
        font-weight: 600;
        text-align: left;
    }
    .orders-table tbody td {
        padding: 30px 25px;
        vertical-align: middle;
        border-bottom: 1px solid #fcfcfc;
        color: #5A4A42;
        font-size: 0.95rem;
    }

    /* ĐỊNH DẠNG BADGE GIỐNG ADMIN */
    .status-badge {
        padding: 8px 18px;
        border-radius: 25px;
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        display: inline-block;
    }
    .status-pending { background: #fff8e6; color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }
    .status-processing { background: #ffffff; color: #111; border: 1.5px solid #d4af37; }
    .status-shipped { background: #e6fffa; color: #0d9488; border: 1px solid rgba(13, 148, 136, 0.2); }
    .status-completed { background: #f0fdf4; color: #16a34a; border: 1px solid rgba(22, 163, 74, 0.2); }
    .status-cancelled { background: #fef2f2; color: #dc2626; border: 1px solid rgba(220, 38, 38, 0.2); }

    .btn-cancel-order {
        background: #fff;
        color: #dc2626;
        border: 1px solid #dc2626;
        padding: 10px 20px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        transition: 0.3s;
        text-transform: uppercase;
    }
    .btn-cancel-order:hover {
        background: #dc2626;
        color: #fff;
    }
    .btn-cancel-order:disabled {
        opacity: 0.3;
        cursor: not-allowed;
        border-color: #ddd;
        color: #ccc;
    }

    .modal-luxury-content { border-radius: 30px !important; border: none !important; box-shadow: 0 40px 120px rgba(0,0,0,0.3) !important; }
    .modal-luxury-header { background: #111; color: #fff; padding: 35px 40px; border: none; }
</style>

<div class="container py-4">
    <div class="orders-container">
        <div class="text-center mb-5">
            <p style="color: var(--accent); letter-spacing: 4px; font-weight: 500; font-size: 0.8rem; text-transform: uppercase;">Lịch sử mua sắm</p>
            <h1 class="gold-gradient-text" style="font-size: 2.8rem; margin-top: 10px;">Đơn Hàng Của Bạn</h1>
            <div style="width: 60px; height: 3px; background: var(--accent); margin: 20px auto;"></div>
        </div>

        <div class="luxury-card-orders">
            <?php
                $userId = $_SESSION['userId'];
                $sql = "SELECT * FROM orders WHERE user_Id = $userId ORDER BY order_date DESC";
                $orders = $f->getAll($sql);
                
                if (empty($orders)):
            ?>
                <div class="text-center py-5">
                    <i class="fa fa-shopping-bag" style="font-size: 4rem; color: #eee; margin-bottom: 25px;"></i>
                    <h4 style="font-family: 'Playfair Display', serif; color: #999;">Bạn chưa có đơn hàng nào</h4>
                    <p class="text-muted">Khám phá bộ sưu tập mới nhất của chúng tôi!</p>
                    <a href="index.php?page=product" class="btn-elegant mt-4">Tiếp tục mua hàng</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table orders-table">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Ngày đặt</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $order): ?>
                                <tr>
                                    <td style="font-weight: 700;">#<?= $order['Id'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($order['order_date'])) ?></td>
                                    <td>
                                        <?php 
                                            $status = $order['status'] ?? 'Pending';
                                            $statusClass = 'status-' . strtolower($status);
                                            // Chuyển tên trạng thái sang tiếng Việt cho thân thiện
                                            $statusText = $status;
                                            if($status == 'Pending') $statusText = 'Chờ xử lý';
                                            if($status == 'Processing') $statusText = 'Đang xử lý';
                                            if($status == 'Shipped') $statusText = 'Đang giao';
                                            if($status == 'Completed') $statusText = 'Đã nhận hàng';
                                            if($status == 'Cancelled') $statusText = 'Đã hủy';
                                        ?>
                                        <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                                    </td>
                                    <td class="text-end">
                                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                            <button class="btn-elegant" onclick="showOrderDetail(<?= $order['Id'] ?>)" style="padding: 6px 15px; font-size: 0.65rem; font-weight: 700; min-width: 90px;">
                                                CHI TIẾT
                                            </button>
                                            
                                            <?php if($status == 'Pending' || $status == 'Processing'): ?>
                                            <form action="" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn huỷ đơn hàng này?');">
                                                <input type="hidden" name="cancelOrderId" value="<?= $order['Id'] ?>">
                                                <button type="submit" class="btn-cancel-order" style="padding: 6px 15px; font-size: 0.65rem;">
                                                    HUỶ ĐƠN
                                                </button>
                                            </form>
                                            <?php else: ?>
                                            <button class="btn-cancel-order" disabled title="Không thể huỷ đơn hàng ở trạng thái này" style="padding: 6px 15px; font-size: 0.65rem;">
                                                HUỶ ĐƠN
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Chi Tiết Đơn Hàng -->
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-luxury-content">
            <div class="modal-header modal-luxury-header">
                <div>
                    <p style="font-size: 0.7rem; letter-spacing: 2px; text-transform: uppercase; color: var(--accent); margin-bottom: 0;">Thông tin chi tiết</p>
                    <h4 class="modal-title" style="font-family: 'Playfair Display', serif; font-weight: 700;">ĐƠN HÀNG <span id="modalOrderId"></span></h4>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5">
                <table class="table detail-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Đơn giá</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody id="orderDetailContent"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function showOrderDetail(orderId) {
        $("#modalOrderId").text("#" + orderId);
        $("#orderDetailContent").html('<tr><td colspan="4" class="text-center py-5"><i class="fa fa-spinner fa-spin me-2"></i></td></tr>');
        var myModal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
        myModal.show();
        $.ajax({
            url: "ajax/orderDetail.php",
            type: "get",
            dataType: 'json',
            data: {orderId: orderId},
            success: function(response) {
                var html = "";
                response.items.forEach(function(item) {
                    html += `
                        <tr>
                            <td class="py-4">
                                <div style="font-weight: 600; color: #433422;">${item.product_name}</div>
                                <div style="font-size: 0.75rem; color: #c9a96e; font-weight: 600;">Size: ${item.size || 'N/A'}</div>
                            </td>
                            <td class="py-4 text-center">${item.quantity}</td>
                            <td class="py-4 text-end">${new Intl.NumberFormat().format(item.price)}đ</td>
                            <td class="py-4 text-end" style="font-weight: 700; color: var(--accent);">${new Intl.NumberFormat().format(item.total_price)}đ</td>
                        </tr>
                    `;
                });
                
                // Thêm hàng tổng tiền và thanh toán
                html += `
                    <tr style="border-top: 2px solid #111;">
                        <td colspan="2" class="py-4">
                            <div style="font-size: 0.7rem; text-transform: uppercase; color: #aaa; letter-spacing: 1px;">Phương thức thanh toán</div>
                            <div style="font-weight: 600; color: #433422;">${response.info.payment_method}</div>
                        </td>
                        <td colspan="2" class="py-4 text-end">
                            <div style="font-size: 0.7rem; text-transform: uppercase; color: #aaa; letter-spacing: 1px;">Tổng cộng</div>
                            <div style="font-size: 1.5rem; font-weight: 800; color: #111;">${response.info.total_amount}đ</div>
                        </td>
                    </tr>
                `;
                $("#orderDetailContent").html(html);
            }
        });
    }
</script>