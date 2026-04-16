<?php
$countProducts = $f->getOne("SELECT COUNT(*) as total FROM product")['total'];
$countCats = $f->getOne("SELECT COUNT(*) as total FROM category")['total'];
$countUsers = $f->getOne("SELECT COUNT(*) as total FROM user")['total'];
$countOrders = $f->getOne("SELECT COUNT(*) as total FROM orders")['total'];

// Tính toán doanh thu từ order_detail thay vì total_amount của orders (bị thiếu)
$revenueAllTime = $f->getOne("SELECT SUM(d.price * d.quantity) as total FROM orders o JOIN order_detail d ON o.Id = d.order_id WHERE o.status = 'Completed'")['total'] ?? 0;
$revenueMonth = $f->getOne("SELECT SUM(d.price * d.quantity) as total FROM orders o JOIN order_detail d ON o.Id = d.order_id WHERE o.status = 'Completed' AND MONTH(o.order_date) = MONTH(CURRENT_DATE()) AND YEAR(o.order_date) = YEAR(CURRENT_DATE())")['total'] ?? 0;
$revenueToday = $f->getOne("SELECT SUM(d.price * d.quantity) as total FROM orders o JOIN order_detail d ON o.Id = d.order_id WHERE o.status = 'Completed' AND DATE(o.order_date) = CURRENT_DATE()")['total'] ?? 0;

// Lấy 5 đơn hàng mới nhất và tính luông tổng tiền thay vì lấy từ cột o.total_amount
$recentOrders = $f->getAll("
    SELECT o.*, COALESCE(SUM(d.price * d.quantity), 0) as real_total 
    FROM orders o 
    LEFT JOIN order_detail d ON o.Id = d.order_id 
    GROUP BY o.Id 
    ORDER BY o.order_date DESC LIMIT 5
");

// Lấy top 3 sản phẩm
$topProducts = $f->getAll("SELECT p.*, c.category_name FROM product p LEFT JOIN category c ON p.cat_id = c.id ORDER BY p.views DESC LIMIT 3");
?>

<style>
    /* Sử dụng Poppins cho toàn bộ con số và nhãn */
    .dashboard-numbers {
        font-family: 'Poppins', sans-serif !important;
    }

    .revenue-master-card {
        background: var(--gold-gradient);
        border-radius: 40px;
        padding: 50px;
        color: #433422;
        position: relative;
        overflow: hidden;
        box-shadow: 0 30px 60px rgba(var(--gold-rgb), 0.2);
        margin-bottom: 40px;
        text-align: center;
    }
    .revenue-master-card::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%;
        width: 200%; height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
        animation: rotateGlow 15s linear infinite;
    }
    @keyframes rotateGlow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .revenue-value-large {
        font-family: 'Poppins', sans-serif;
        font-size: 4rem;
        font-weight: 800;
        letter-spacing: -1px;
        line-height: 1;
        margin: 15px 0;
    }
    .glass-stat-card {
        background: #fff;
        border-radius: 30px;
        padding: 30px;
        border: 1px solid #f2f2f2;
        transition: var(--transition);
        height: 100%;
    }
    .glass-stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        border-color: var(--gold);
    }
    .clickable-card {
        cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .clickable-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 25px 50px rgba(var(--gold-rgb), 0.3);
    }
    .mini-label {
        font-family: 'Poppins', sans-serif;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: #999;
        font-weight: 700;
    }
    .stat-value {
        font-family: 'Poppins', sans-serif;
        font-size: 1.8rem;
        font-weight: 700;
        margin-top: 10px;
        color: var(--noir);
    }
</style>

<!-- HÀNG 1: TỔNG DOANH THU (CENTERPIECE) -->
<div class="revenue-master-card clickable-card" onclick="openRevenueModal('all')">
    <div style="position: relative; z-index: 2;">
        <span class="mini-label" style="color: #433422; opacity: 0.6;">Tổng doanh thu hệ thống</span>
        <div class="revenue-value-large"><?= number_format($revenueAllTime) ?> <span style="font-size: 1.5rem; font-weight: 600;">VND</span></div>
        <p style="font-family: 'Playfair Display', serif; font-style: italic; font-weight: 600; opacity: 0.8;">Junia Fashion Dashboard</p>
    </div>
</div>

<!-- HÀNG 2: DOANH THU NGÀY VÀ THÁNG (2 BOXES) -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="glass-stat-card clickable-card" onclick="openRevenueModal('today')">
            <div class="mini-label">Doanh thu hôm nay</div>
            <div class="stat-value" style="color: var(--gold);">+ <?= number_format($revenueToday) ?>đ</div>
            <div style="height: 3px; width: 50px; background: var(--gold); margin-top: 15px; border-radius: 10px;"></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="glass-stat-card clickable-card" onclick="openRevenueModal('month')">
            <div class="mini-label">Doanh thu tháng này</div>
            <div class="stat-value">+ <?= number_format($revenueMonth) ?>đ</div>
            <div style="height: 3px; width: 50px; background: #eee; margin-top: 15px; border-radius: 10px;"></div>
        </div>
    </div>
</div>

<!-- HÀNG 3: ĐƠN HÀNG, KHÁCH HÀNG, SẢN PHẨM (3 BOXES) -->
<div class="row g-4 mb-5 text-center">
    <div class="col-md-4">
        <div class="glass-stat-card clickable-card" onclick="location.href='index.php?page=orderList'">
            <div class="mini-label">Đơn hàng</div>
            <div class="stat-value"><?= number_format($countOrders) ?></div>
            <p style="font-size: 0.7rem; color: #bbb; margin-top: 5px;">Giao dịch thành công</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-stat-card clickable-card" onclick="location.href='index.php?page=userList'">
            <div class="mini-label">Khách hàng</div>
            <div class="stat-value"><?= number_format($countUsers) ?></div>
            <p style="font-size: 0.7rem; color: #bbb; margin-top: 5px;">Thành viên hệ thống</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-stat-card clickable-card" onclick="location.href='index.php?page=productList'">
            <div class="mini-label">Sản phẩm</div>
            <div class="stat-value"><?= number_format($countProducts) ?></div>
            <p style="font-size: 0.7rem; color: #bbb; margin-top: 5px;">Mẫu mã hiện có</p>
        </div>
    </div>
</div>

<!-- SECTION 4: RECENT & TOP (DỮ NGUYÊN BỐ CỤC LUXURY) -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="luxury-card" style="min-height: 500px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.6rem; font-weight: 700;">Giao dịch mới nhất</h3>
                <a href="<?= PATH_ADMIN ?>page=orderList" class="btn-gold" style="padding: 8px 20px; font-size: 0.65rem;">Tất cả đơn hàng</a>
            </div>
            <table class="table-luxury">
                    <thead>
                        <tr>
                            <th class="mini-label" style="padding: 15px 10px;">ID</th>
                            <th class="mini-label">Ngày</th>
                            <th class="mini-label">Khách hàng</th>
                            <th class="mini-label">Thành tiền</th>
                            <th class="mini-label">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="dashboard-numbers">
                        <?php foreach($recentOrders as $order): ?>
                        <tr>
                            <td data-label="ID" style="font-weight: 800;">#<?= $order['Id'] ?></td>
                            <td data-label="Ngày" style="font-size: 0.85rem; color: #888;"><?= date('H:i, d/m', strtotime($order['order_date'])) ?></td>
                            <td data-label="Khách hàng" style="font-weight: 600;"><?= htmlspecialchars($order['receiverName'] ?: 'Khách hàng') ?></td>
                            <td data-label="Thành tiền" style="font-weight: 700; color: var(--gold);"><?= number_format($order['real_total']) ?>đ</td>
                            <td data-label="Trạng thái">
                                <?php
                                    $status = $order['status'] ?? 'Pending';
                                    $statusClass = 'status-' . strtolower($status);
                                    $statusText = $status;
                                    if($status == 'Completed') $statusText = 'Hoàn thành';
                                    if($status == 'Pending') $statusText = 'Chờ xử lý';
                                    if($status == 'Processing') $statusText = 'Đang xử lý';
                                    if($status == 'Shipped') $statusText = 'Đang giao';
                                    if($status == 'Cancelled') $statusText = 'Đã hủy';
                                ?>
                                <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="luxury-card h-100">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.6rem; font-weight: 700; margin-bottom: 30px;">Sản phẩm nổi bật</h3>
            <?php foreach($topProducts as $p): ?>
            <div style="display: flex; align-items: center; gap: 20px; padding: 15px; border-radius: 20px; transition: 0.3s;" onmouseover="this.style.background='#fdfaf7'" onmouseout="this.style.background='transparent'">
                <img src="../asset/images/<?= $p['image'] ?>" style="width: 70px; height: 90px; object-fit: cover; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                <div style="flex: 1;">
                    <div style="font-weight: 700; color: var(--noir); line-height: 1.2; margin-bottom: 5px;"><?= $p['name'] ?></div>
                    <div class="dashboard-numbers" style="font-size: 0.85rem; color: var(--gold); font-weight: 700;"><?= number_format($p['price']) ?>đ</div>
                    <div class="dashboard-numbers" style="font-size: 0.65rem; color: #ccc; margin-top: 4px;"><i class="fa fa-fire"></i> <?= number_format($p['views']) ?> views</div>
                </div>
            </div>
            <?php endforeach; ?>
            <div style="margin-top: 35px; padding: 30px; background: var(--noir); border-radius: 25px; color: #fff; position: relative; overflow: hidden;">
                <h4 style="font-family: 'Playfair Display'; font-size: 1.1rem; margin-bottom: 10px;">Lời nhắn Admin</h4>
                <p style="font-size: 0.8rem; font-weight: 300; opacity: 0.6; line-height: 1.8;">Mỗi con số là một minh chứng cho nỗ lực. Hãy tiếp tục kiến tạo những giá trị tuyệt vời.</p>
                <i class="fa fa-quote-right" style="position: absolute; right: 20px; bottom: 20px; font-size: 2rem; opacity: 0.1;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Modal hiển thị danh sách đơn hàng cho doanh thu -->
<div class="modal fade" id="revenueOrdersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" style="max-width: min(680px, 95vw); margin: auto;">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden; max-width: 100%;">
            <div class="modal-header" style="background: var(--noir); color: #fff; padding: 18px 25px; border: none; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
                <h5 class="modal-title" style="font-family: 'Playfair Display', serif; font-size: 1.2rem; letter-spacing: 1px; margin: 0;">Danh sách đơn hàng</h5>
                <button type="button" data-bs-dismiss="modal" aria-label="Close"
                        style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); border-radius: 50%; width: 36px; height: 36px; color: #fff; font-size: 1.2rem; line-height: 1; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: background 0.2s;"
                        onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.15)'">&times;</button>
            </div>
            <div class="modal-body p-0" id="revenueModalContent" style="overflow-x: hidden;">
                <div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x" style="color: var(--gold);"></i></div>
            </div>
        </div>
    </div>
</div>

<script>
    function openRevenueModal(filterType) {
        // Show modal
        var modalEl = document.getElementById('revenueOrdersModal');
        var myModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        myModal.show();
        
        // Reset loading state
        document.getElementById('revenueModalContent').innerHTML = '<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x" style="color: var(--accent);"></i></div>';
        
        // Fetch data
        $.ajax({
            url: 'ajax/getRevenueOrders.php',
            type: 'POST',
            data: { filter: filterType },
            success: function(response) {
                document.getElementById('revenueModalContent').innerHTML = response;
            },
            error: function() {
                document.getElementById('revenueModalContent').innerHTML = '<div class="text-center py-5 text-danger"><i class="fa fa-exclamation-circle mb-3 fa-2x"></i><br>Lỗi tải dữ liệu. Vui lòng thử lại.</div>';
            }
        });
    }

    function showRevenueDetail(orderId, filterType) {
        // Loading state
        document.getElementById('revenueModalContent').innerHTML = '<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x" style="color: var(--accent);"></i></div>';
        
        // Fetch detail
        $.ajax({
            url: 'ajax/getRevenueOrders.php',
            type: 'POST',
            data: { 
                filter: filterType,
                order_id: orderId 
            },
            success: function(response) {
                document.getElementById('revenueModalContent').innerHTML = response;
            },
            error: function() {
                document.getElementById('revenueModalContent').innerHTML = '<div class="text-center py-5 text-danger"><i class="fa fa-exclamation-circle mb-3 fa-2x"></i><br>Lỗi tải chi tiết.</div>';
            }
        });
    }
</script>