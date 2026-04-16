<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if(!isset($_SESSION['userId'])){
        echo "
        <div class='container py-5 text-center' style='margin-top: 100px;'>
            <div style='font-size: 4rem; color: #eee; margin-bottom: 30px;'><i class='fa fa-user-lock'></i></div>
            <h2 class='elegant-font' style='font-family: \"Playfair Display\", serif; margin-bottom: 20px;'>Yêu cầu đăng nhập</h2>
            <p style='color: #999; margin-bottom: 35px;'>Vui lòng đăng nhập để xem những món đồ trong giỏ hàng của bạn.</p>
            <a href='".PATH."page=login' class='btn-elegant' style='text-decoration: none;'>Đăng nhập ngay</a>
        </div>";
        exit();
    }
?>

<style>
    .cart-page-container {
        max-width: 1200px;
        margin: 100px auto;
    }
    .luxury-cart-card {
        background: #fff;
        border-radius: 50px;
        padding: 100px 80px;
        box-shadow: 0 50px 150px rgba(184, 138, 68, 0.08);
        border: 1px solid rgba(184, 138, 68, 0.1);
        position: relative;
    }
    .luxury-cart-card::after {
        content: '';
        position: absolute;
        top: -1px; left: -1px; right: -1px; bottom: -1px;
        border-radius: 50px;
        border: 1px solid rgba(212, 175, 55, 0.2);
        pointer-events: none;
        z-index: 1;
    }
    .gold-gradient-text {
        background: linear-gradient(135deg, #a67c00 0%, #bf953f 15%, #fcf6ba 40%, #b88a44 60%, #ddac17 85%, #8a6d3b 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        letter-spacing: 5px;
        text-transform: uppercase;
    }
    .cart-table {
        border-collapse: separate;
        border-spacing: 0 40px; /* Giãn cách các dòng sản phẩm cực lớn */
        margin-top: -30px;
    }
    .cart-table thead th {
        background: transparent;
        color: #B88A44;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 4px;
        border: none;
        padding: 0 20px 20px 20px;
        font-weight: 600;
        border-bottom: 1px solid #f5f5f5;
    }
    .cart-table tbody tr {
        transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .cart-table tbody tr:hover {
        transform: translateY(-5px);
    }
    .cart-table tbody td {
        padding: 0 20px;
        vertical-align: middle;
        border: none;
        color: #5A4A42;
    }
    .product-img-wrap {
        width: 120px;
        height: 160px;
        border-radius: 25px;
        overflow: hidden;
        box-shadow: 0 20px 45px rgba(0,0,0,0.12);
    }
    .product-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: 0.6s ease; }
    .cart-table tbody tr:hover .product-img-wrap img { transform: scale(1.1); }
    
    .quantity-spinner {
        display: inline-flex;
        border: 1px solid #f2e9dc;
        border-radius: 30px;
        padding: 10px 20px;
        background: #fff;
        box-shadow: inset 0 2px 5px rgba(0,0,0,0.02);
    }
    .quantity-spinner input {
        width: 50px;
        text-align: center;
        border: none;
        background: transparent;
        font-weight: 700;
        color: #433422;
        font-size: 1.1rem;
    }
    .checkout-summary {
        background: #fbfaf8;
        border-radius: 40px;
        padding: 60px;
        margin-top: 60px;
        border: 1px solid rgba(184, 138, 68, 0.05);
        box-shadow: 0 30px 70px rgba(0,0,0,0.02);
    }

    /* Mobile Refinements */
    @media (max-width: 992px) {
        .cart-page-container { margin: 60px auto; }
        .luxury-cart-card { padding: 40px 20px; border-radius: 30px; }
        .luxury-cart-card::after { border-radius: 30px; }
        .checkout-summary { padding: 30px; border-radius: 30px; }
        .product-img-wrap { width: 80px; height: 110px; border-radius: 15px; }
        .cart-table thead { display: none; }
        .cart-table tbody td { display: block; width: 100%; text-align: left !important; padding: 10px 0; }
        .cart-table tbody tr { margin-bottom: 30px; display: block; border-bottom: 2px solid #f9f9f9; padding-bottom: 20px; }
        .cart-table tbody td:first-child { display: flex; justify-content: center; }
        .quantity-spinner { margin: 10px 0; }
        .d-flex.justify-content-between.align-items-center.mt-5 { flex-direction: column; gap: 30px; }
        .d-flex.justify-content-between.align-items-center.mt-5 > div { width: 100%; text-align: center !important; }
        .checkout-summary .d-flex { gap: 10px !important; }
        .checkout-summary .d-flex span:last-child { font-size: 1.5rem !important; }
    }
</style>

<div class="container py-2">
    <div class="cart-page-container">
        <div class="text-center mb-5">
            <p style="color: var(--accent); letter-spacing: 4px; font-weight: 500; font-size: 0.8rem; text-transform: uppercase;">Mua sắm</p>
            <h1 class="gold-gradient-text" style="font-size: 2.8rem; margin-top: 10px;">Giỏ Hàng Của Bạn</h1>
            <div style="width: 60px; height: 3px; background: var(--accent); margin: 20px auto;"></div>
        </div>

        <?php if(!isset($_SESSION['cart']) || $_SESSION['number_of_item'] == 0): ?>
            <div class="luxury-cart-card text-center py-5">
                <div style="font-size: 5rem; color: #f0f0f0; margin-bottom: 30px;"><i class="fa fa-shopping-basket"></i></div>
                <h3 style="font-family: 'Playfair Display', serif; color: #999;">Giỏ hàng đang trống</h3>
                <p class="text-muted mb-5">Hãy chọn những món đồ tuyệt vời cho riêng mình!</p>
                <a href="index.php?page=product" class="btn-elegant" style="text-decoration: none;">Khám phá sản phẩm</a>
            </div>
        <?php else: ?>
            <div class="luxury-cart-card">
                <form action="<?= PATH ?>page=cart" method="post">
                    <div class="table-responsive">
                        <table class="table cart-table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Thông tin</th>
                                    <th class="text-center">Đơn giá</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Thành tiền</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $total_amount = 0;
                                if(isset($_SESSION['cart'])):
                                    foreach($_SESSION['cart'] as $idx => $pid):
                                        $current_qty = $_SESSION['amount'][$idx];
                                        $current_size = $_SESSION['size'][$idx];
                                        
                                        $sql = "SELECT * FROM product WHERE id = $pid";
                                        $p = $f->getOne($sql);
                                        
                                        if(!$p) continue;
                                        
                                        // Update logic
                                        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['qty_'.$idx])) {
                                            $new_qty = (int)$_POST['qty_'.$idx];
                                            $current_qty = ($new_qty > 0) ? $new_qty : 1;
                                            $_SESSION['amount'][$idx] = $current_qty;
                                        }
                                        
                                        $price = ($p['is_on_sale'] == 1 && $p['sale_price'] !== null) ? $p['sale_price'] : $p['price'];
                                        $subtotal = $price * $current_qty;
                                        $total_amount += $subtotal;
                            ?>
                                <tr>
                                    <td>
                                        <div class="product-img-wrap">
                                            <img src="asset/images/<?= $p['image'] ?>" alt="">
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; font-size: 1.1rem; color: #433422;"><?= $p['name'] ?></div>
                                        <?php 
                                            $hasMultipleSizes = !empty($p['sizes']) && strpos($p['sizes'], 'One Size') === false;
                                            if ($hasMultipleSizes): 
                                                $szArr = explode(',', $p['sizes']);
                                        ?>
                                            <div style="font-size: 0.85rem; color: #c9a96e; font-weight: 600; margin-top: 5px; display: inline-flex; align-items: center;">
                                                Size: 
                                                <select onchange="changeSizeCart(<?= $idx ?>, this.value)" style="border: none; outline: none; background: transparent; color: #c9a96e; font-weight: 600; margin-left: 5px; cursor: pointer; -webkit-appearance: none; appearance: none; padding-right: 15px;">
                                                    <?php foreach($szArr as $s_info): 
                                                        $s_parts = explode(':', $s_info);
                                                        $sName = trim($s_parts[0]);
                                                        if($sName != ''):
                                                    ?>
                                                        <option value="<?= htmlspecialchars($sName) ?>" <?= ($sName == $current_size) ? 'selected' : '' ?> style="color: #433422;"><?= htmlspecialchars($sName) ?></option>
                                                    <?php endif; endforeach; ?>
                                                </select>
                                                <i class="fa fa-caret-down" style="margin-left: -12px; pointer-events: none;"></i>
                                            </div>
                                        <?php else: ?>
                                            <div style="font-size: 0.85rem; color: #c9a96e; font-weight: 600; margin-top: 5px;">Size: <?= $current_size ?></div>
                                        <?php endif; ?>
                                        <div style="font-size: 0.75rem; color: #aaa; text-transform: uppercase; margin-top: 5px;">Mã: #<?= $pid ?></div>
                                    </td>
                                    <td class="text-center"><?= number_format($price) ?>đ</td>
                                    <td class="text-center">
                                        <div class="quantity-spinner" style="display: flex; align-items: center; justify-content: center; gap: 15px;">
                                            <button type="button" onclick="changeQtyCart(<?= $idx ?>, -1)" style="border: none; background: none; color: #999; cursor: pointer; font-size: 1.2rem; transition: 0.3s; padding: 0 5px;" onmouseover="this.style.color='#111'" onmouseout="this.style.color='#999'">-</button>
                                            <input type="number" id="qty_<?= $idx ?>" value="<?= $current_qty ?>" min="1" max="99" style="width: 40px; text-align: center; border: none; font-weight: 800; font-size: 1.1rem; cursor: default;" readonly>
                                            <button type="button" onclick="changeQtyCart(<?= $idx ?>, 1)" style="border: none; background: none; color: #999; cursor: pointer; font-size: 1.2rem; transition: 0.3s; padding: 0 5px;" onmouseover="this.style.color='#111'" onmouseout="this.style.color='#999'">+</button>
                                        </div>
                                    </td>
                                    <td class="text-end" style="font-weight: 700; color: var(--accent);"><?= number_format($subtotal) ?>đ</td>
                                    <td class="text-center">
                                        <a href="<?= PATH ?>page=delItemCart&index=<?= $idx ?>" style="color: #ddd; transition: 0.3s;" onmouseover="this.style.color='#ff4d4d'" onmouseout="this.style.color='#ddd'">
                                            <i class="fa fa-times-circle fa-lg"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-5">
                        <div>
                            <a href="<?= PATH ?>page=delCart" class="btn-outline" style="color: #e74c3c; border-color: #e74c3c; text-decoration: none; padding: 12px 30px; display: inline-block;">Làm trống giỏ</a>
                        </div>
                        <div class="text-end">
                            <div class="checkout-summary">
                                <div class="d-flex justify-content-between align-items-end mb-4" style="gap: 50px;">
                                    <span style="font-size: 0.8rem; letter-spacing: 2px; text-transform: uppercase; color: #999;">Tổng thanh toán</span>
                                    <span style="font-family: 'Playfair Display', serif; font-weight: 700; font-size: 2.2rem; color: #B88A44; line-height: 1;"><?= number_format($total_amount); ?>đ</span>
                                </div>
                                <a href="<?= PATH ?>page=checkout" class="btn-elegant" style="text-decoration: none; display: block; text-align: center;">Tiến hành đặt hàng</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function changeQtyCart(idx, dir) {
        let input = document.getElementById('qty_' + idx);
        let current = parseInt(input.value);
        let next = current + dir;
        
        if(next >= 1 && next <= 99) {
            // Hiệu ứng chờ nhẹ
            input.style.opacity = '0.5';
            
            // Gửi AJAX cập nhật session
            fetch('ajax/updateCartQty.php?index=' + idx + '&qty=' + next)
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success') {
                        // Load lại trang để cập nhật thành tiền và tổng tiền
                        location.reload();
                    } else {
                        input.style.opacity = '1';
                        alert('Có lỗi xảy ra: ' + data.message);
                    }
                })
                .catch(error => {
                    input.style.opacity = '1';
                    console.error('Error:', error);
                });
        }
    }

    function changeSizeCart(idx, newSize) {
        fetch('ajax/updateCartSize.php?index=' + idx + '&size=' + encodeURIComponent(newSize))
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    // Hiệu ứng thay đổi
                    location.reload();
                } else {
                    alert('Có lỗi xảy ra: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>