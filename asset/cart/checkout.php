<div class="checkout-light-gold py-5 mt-5">
    <div class="container" style="max-width: 1400px;">
        <!-- Elegant Header -->
        <div class="text-center mb-5 pb-4">
            <h1 style="font-family: 'Spectral', serif; font-weight: 800; font-size: 3.2rem; color: var(--noir); letter-spacing: 2px;">XÁC NHẬN TÚI ĐỒ</h1>
        </div>

        <?php
        if (session_status() == PHP_SESSION_NONE) session_start();
        if(!isset($_SESSION['user'])){
            echo "<div class='text-center py-5'><p style='color:var(--text); font-size:1.1rem; margin-bottom:30px;'>Khu vực dành riêng cho thành viên cao cấp.</p><a href='".PATH."page=login' class='btn-elegant' style='padding:15px 40px;'>Đăng nhập ngay</a></div>";
            exit();
        }
        $id = $_SESSION['userId'];
        $sql = "SELECT * FROM user WHERE id=$id";
        $user = $f->getOne($sql);
        
        if(!isset($_SESSION['cart']) || $_SESSION['number_of_item'] == 0){
            echo "<div class='text-center py-5'><p style='color:var(--text); font-size:1.1rem; margin-bottom:30px;'>Giỏ hàng của bạn đang chờ những món đồ đẳng cấp.</p><a href='".PATH."page=product' class='btn-elegant' style='padding:15px 40px;'>Tiếp tục mua sắm</a></div>";
        } else {
        ?>

        <div class="luxury-split-wrapper">
            <!-- LEFT: PREVIEW (Order Summary) -->
            <div class="luxury-preview-col">
                <div class="luxury-card-light h-100" style="background: #fffcf7; padding: 60px;">
                    <h3 class="mb-5" style="font-family: 'Spectral', serif; color: var(--noir); font-weight: 700; border-bottom: 2px solid #fdf4e3; padding-bottom: 20px; display: inline-block; letter-spacing: 1px;">Xem lại túi đồ</h3>
                    
                    <div class="preview-scroll mb-5" style="max-height: 550px; overflow-y: auto; padding-right: 20px;">
                        <?php
                            $total_amount = 0;
                            if(isset($_SESSION['cart'])):
                                foreach($_SESSION['cart'] as $idx => $pid):
                                    $current_qty = $_SESSION['amount'][$idx];
                                    $current_size = $_SESSION['size'][$idx];
                                    $sql = "SELECT * FROM product WHERE id = $pid";
                                    $p = $f->getOne($sql);
                                    if(!$p) continue;
                                    $price = ($p['is_on_sale'] == 1 && $p['sale_price'] !== null) ? $p['sale_price'] : $p['price'];
                                    $subtotal = $price * $current_qty;
                                    $total_amount += $subtotal;
                        ?>
                        <div class="d-flex align-items-center mb-5 pb-5" style="border-bottom: 1px dashed #f1e4c9; gap: 30px;">
                            <div style="width: 100px; height: 135px; border-radius: 8px; overflow: hidden; border: 1.5px solid #fdf4e3; padding: 3px; flex-shrink: 0;">
                                <img src="asset/images/<?= $p['image'] ?>" alt="" style="width: 100%; height: 100%; object-fit: cover; border-radius: 5px;">
                            </div>
                            <div style="flex-grow: 1;">
                                <h4 style="font-size: 1.1rem; color: var(--noir); margin-bottom: 8px; font-weight: 600; line-height: 1.5;"><?= $p['name'] ?></h4>
                                <div style="font-size: 0.85rem; color: #999; letter-spacing: 0.5px;">Kích cỡ: <?= $current_size ?> / Số lượng: <?= $current_qty ?></div>
                                <div style="color: var(--accent); font-weight: 700; margin-top: 10px; font-size: 1.15rem;"><?= number_format($price) ?>đ</div>
                            </div>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>

                    <div class="pt-5" style="border-top: 2px solid #fdf4e3;">
                        <div class="d-flex justify-content-between mb-3">
                            <span style="color: #888; letter-spacing: 1px;">Tạm tính</span>
                            <span style="font-weight: 600;"><?= number_format($total_amount) ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-5">
                            <span style="color: #888; letter-spacing: 1px;">Phí vận chuyển</span>
                            <span style="color: #2ecc71; font-weight: 700; text-transform: uppercase; font-size: 0.85rem;">Miễn phí</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-end pt-4">
                            <span style="font-size: 1.1rem; font-weight: 800; text-transform: uppercase; color: var(--noir); letter-spacing: 2px;">TỔNG THANH TOÁN</span>
                            <div class="text-end">
                                <span class="gold-text" style="font-size: 3.2rem; display: block; line-height: 1;"><?= number_format($total_amount) ?>đ</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 p-5 text-center" style="border: 1px solid #fdf4e3; border-radius: 20px; background: #fff;">
                        <img src="asset/images/junia_logo_gold.png" style="height: 35px; margin-bottom: 20px; opacity: 0.4;">
                        <p style="font-size: 0.8rem; color: #111; margin: 0; line-height: 2; letter-spacing: 0.5px;">Mọi đơn hàng đều được đi kèm thư cảm ơn tay và túi đựng Junia Luxury đặc trưng từ sợi lụa tái chế.</p>
                    </div>
                </div>
            </div>

            <!-- RIGHT: SHIPPING & PAYMENT (Form) -->
            <div class="luxury-form-col">
                <div class="luxury-card-light h-100" style="padding: 60px;">
                    <h3 class="mb-5" style="font-family: 'Spectral', serif; color: var(--noir); font-weight: 700; border-bottom: 2px solid #fdf4e3; padding-bottom: 20px; display: inline-block; letter-spacing: 1px;">Thông tin giao hàng</h3>
                    
                    <form action="cart/process_order.php" method="post">
                        <!-- Shipping Section -->
                        <div class="mb-5 pb-5">
                            <div class="elegant-input-group mb-5">
                                <label>Họ và Tên người nhận</label>
                                <input type="text" class="gold-input-new" name="fullName" placeholder="Tram Nguyen" value="<?=$user["name"]?>" required>
                            </div>

                            <div class="elegant-input-group mb-5">
                                <label>Địa chỉ nhận hàng</label>
                                <input type="text" class="gold-input-new" name="address" placeholder="Số nhà, đường, phường/xã..." value="<?=$user["address"]?>" required>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="elegant-input-group">
                                        <label>Số điện thoại</label>
                                        <input type="text" class="gold-input-new" name="phoneNumber" placeholder="090 123 4567" value="<?=$user["phone"]?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="elegant-input-group">
                                        <label>Email</label>
                                        <input type="email" class="gold-input-new" name="email" placeholder="example@email.com" value="<?=$user["email"]?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Section -->
                        <div class="mb-5">
                            <h4 style="font-family: 'Spectral', serif; font-size: 1.3rem; font-weight: 700; margin-bottom: 35px; color: var(--accent); letter-spacing: 1px;">Phương thức thanh toán</h4>
                            
                            <div class="pay-selection-light">
                                <label class="pay-item-gold mb-4">
                                    <input type="radio" name="payment" value="cash" checked>
                                    <div class="pay-box-gold">
                                        <div class="d-flex align-items-center gap-4">
                                            <div class="icon-gold"><i class="fa fa-truck-fast"></i></div>
                                            <div>
                                                <div class="pay-tit">Thanh toán khi nhận hàng</div>
                                                <div class="pay-sub" style="letter-spacing: 0.5px;">Tiền mặt (COD) tại địa chỉ yêu cầu</div>
                                            </div>
                                        </div>
                                        <div class="gold-radio-inner"></div>
                                    </div>
                                </label>

                                <label class="pay-item-gold">
                                    <input type="radio" name="payment" value="credit_card">
                                    <div class="pay-box-gold">
                                        <div class="d-flex align-items-center gap-4">
                                            <div class="icon-gold"><i class="fa fa-credit-card"></i></div>
                                            <div>
                                                <div class="pay-tit">Chuyển khoản Ngân hàng</div>
                                                <div class="pay-sub" style="letter-spacing: 0.5px;">Giao dịch an toàn 100% qua cổng bảo mật</div>
                                            </div>
                                        </div>
                                        <div class="gold-radio-inner"></div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="text-center pt-5">
                            <button type="submit" name="submit" class="btn-royal-gold">HOÀN TẤT ĐẶT HÀNG</button>
                            <div class="mt-5">
                                <a href="<?= PATH ?>page=product" style="font-size: 0.8rem; color: #ccc; text-transform: uppercase; letter-spacing: 4px; font-weight: 500;">Tiếp tục mua sắm</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<style>
    .luxury-split-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 40px;
        justify-content: center;
        align-items: stretch; /* Make both cards equal height if possible */
        max-width: 1300px;
        margin: 0 auto;
    }
    .luxury-preview-col, .luxury-form-col {
        flex: 1;
        min-width: 450px;
        max-width: 600px; /* Equal max width */
    }
    
    @media (max-width: 1200px) {
        .luxury-split-wrapper { gap: 30px; }
        .luxury-preview-col, .luxury-form-col {
            min-width: 400px;
        }
    }
    
    @media (max-width: 992px) {
        .luxury-split-wrapper {
            flex-direction: column;
            align-items: center;
        }
        .luxury-preview-col, .luxury-form-col {
            max-width: 100%;
            width: 100%;
        }
        .luxury-preview-col {
            order: 2;
        }
        .luxury-form-col {
            order: 1;
        }
    }

    .luxury-card-light {
        background: #fff;
        border-radius: 30px;
        border: 2px solid #fdf4e3;
        box-shadow: 0 15px 50px rgba(0,0,0,0.02);
        display: flex;
        flex-direction: column;
    }
    
    .preview-scroll {
        max-height: 480px; /* Fixed height for scroll if content exceeds */
        overflow-y: auto;
        padding-right: 15px;
        margin-bottom: 30px;
    }
    
    /* Elegant scrollbar */
    .preview-scroll::-webkit-scrollbar {
        width: 4px;
    }
    .preview-scroll::-webkit-scrollbar-track {
        background: #fdfaf5;
    }
    .preview-scroll::-webkit-scrollbar-thumb {
        background: var(--accent);
        border-radius: 10px;
    }
    
    .elegant-input-group label {
        display: block;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 3px;
        font-weight: 700;
        color: #999;
        margin-bottom: 15px;
    }
    
    .gold-input-new {
        width: 100%;
        border-radius: 0;
        border: none;
        border-bottom: 1.5px solid #eee;
        background: transparent;
        padding: 15px 0;
        font-size: 1.05rem;
        transition: all 0.4s ease;
        outline: none;
    }
    
    .gold-input-new:focus {
        border-color: var(--accent);
        padding-left: 10px;
    }

    .pay-item-gold { display: block; cursor: pointer; }
    .pay-item-gold input { display: none; }
    .pay-box-gold {
        padding: 25px 35px;
        border: 1.5px solid #f2f2f2;
        border-radius: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        transition: all 0.4s ease;
    }
    .icon-gold {
        width: 50px; height: 50px; background: #fdfaf5; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: var(--accent); font-size: 1.35rem;
    }
    .pay-tit { font-weight: 700; font-size: 1.05rem; color: var(--noir); margin-bottom: 5px; }
    .pay-sub { font-size: 0.8rem; color: #999; }
    .gold-radio-inner {
        width: 24px; height: 24px; border: 2px solid #eee; border-radius: 50%;
        position: relative;
    }
    .pay-item-gold input:checked + .pay-box-gold {
        border-color: var(--accent);
        background: #fdfaf5;
        transform: scale(1.02);
    }
    .pay-item-gold input:checked + .pay-box-gold .gold-radio-inner {
        border-color: var(--accent); background: var(--accent);
    }
    .pay-item-gold input:checked + .pay-box-gold .gold-radio-inner::after {
        content: ''; width: 8px; height: 8px; background: #fff;
        border-radius: 50%; position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -50%);
    }

    .btn-royal-gold {
        background: var(--accent-gradient);
        color: #fff; border: none; padding: 22px 100px; border-radius: 60px;
        font-weight: 800; text-transform: uppercase; letter-spacing: 5px; font-size: 1rem;
        box-shadow: 0 15px 35px rgba(212, 175, 55, 0.4); transition: all 0.4s ease; cursor: pointer;
    }
    .btn-royal-gold:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 45px rgba(212, 175, 55, 0.6);
    }
</style>

<style>
    .luxury-card-light {
        background: #fff;
        padding: 45px;
        border-radius: 20px;
        border: 2px solid #fdf4e3;
        box-shadow: 0 10px 40px rgba(0,0,0,0.02);
    }
    .gold-input {
        border-radius: 12px !important;
        border: 1.5px solid #f2f2f2 !important;
        background: #fafafa !important;
        padding: 15px 20px !important;
        font-size: 0.95rem !important;
        transition: all 0.3s ease !important;
    }
    .gold-input:focus {
        border-color: var(--accent) !important;
        background: #fff !important;
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.1) !important;
    }
    .pay-item-gold {
        display: block;
        cursor: pointer;
    }
    .pay-item-gold input {
        display: none;
    }
    .pay-box-gold {
        padding: 18px 25px;
        border: 1.5px solid #f2f2f2;
        border-radius: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        transition: all 0.3s ease;
    }
    .icon-gold {
        width: 42px;
        height: 42px;
        background: #fdfaf5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent);
        font-size: 1.1rem;
    }
    .pay-tit {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--noir);
    }
    .pay-sub {
        font-size: 0.75rem;
        color: #999;
    }
    .gold-radio-inner {
        width: 20px;
        height: 20px;
        border: 2px solid #eee;
        border-radius: 50%;
        position: relative;
    }
    .pay-item-gold input:checked + .pay-box-gold {
        border-color: var(--accent);
        background: #fdfaf5;
        transform: translateY(-2px);
    }
    .pay-item-gold input:checked + .pay-box-gold .gold-radio-inner {
        border-color: var(--accent);
        background: var(--accent);
    }
    .pay-item-gold input:checked + .pay-box-gold .gold-radio-inner::after {
        content: '';
        width: 6px; height: 6px;
        background: #fff;
        border-radius: 50%;
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
    }
    .btn-royal-gold {
        background: var(--accent-gradient);
        color: #fff;
        border: none;
        padding: 20px 80px;
        border-radius: 50px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 3px;
        font-size: 0.9rem;
        box-shadow: 0 10px 25px rgba(212, 175, 55, 0.4);
        transition: all 0.4s ease;
        cursor: pointer;
    }
    .btn-royal-gold:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 15px 35px rgba(212, 175, 55, 0.6);
    }
</style>

<style>
    .minimal-input {
        border: none !important;
        border-bottom: 1.5px solid #eee !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        font-size: 0.95rem !important;
        color: #333 !important;
        box-shadow: none !important;
    }
    .minimal-input:focus {
        border-bottom-color: var(--accent) !important;
    }
    .minimal-input ~ label {
        padding-left: 0 !important;
        color: #999 !important;
        font-size: 0.85rem;
    }
    .pay-option-item {
        display: block;
        cursor: pointer;
    }
    .pay-option-item input {
        display: none;
    }
    .pay-box-split {
        padding: 15px 25px;
        border: 1px solid #eee;
        border-radius: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        transition: var(--transition);
    }
    .radio-circle {
        width: 18px;
        height: 18px;
        border: 2px solid #ddd;
        border-radius: 50%;
        position: relative;
    }
    .pay-option-item input:checked + .pay-box-split {
        border-color: var(--accent);
        background: #fdfaf8;
    }
    .pay-option-item input:checked + .pay-box-split .radio-circle {
        border-color: var(--accent);
        background: var(--accent);
    }
    .pay-option-item input:checked + .pay-box-split .radio-circle::after {
        content: '';
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 6px; height: 6px;
        background: #fff;
        border-radius: 50%;
    }
</style>

<style>
    .checkout-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 700;
        color: var(--noir);
        margin-bottom: 12px;
        display: block;
    }
    .elegant-input-field {
        background: #fdfdfd !important;
        border: 1.5px solid #eee !important;
        border-radius: 12px !important;
        padding: 15px 20px !important;
        font-size: 0.95rem !important;
        transition: var(--transition) !important;
    }
    .elegant-input-field:focus {
        border-color: var(--accent) !important;
        background: #fff !important;
        box-shadow: var(--gold-glow) !important;
    }
    .payment-selection-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }
    .payment-box-luxury {
        cursor: pointer;
        display: block;
    }
    .payment-box-luxury input {
        display: none;
    }
    .box-content {
        padding: 20px 30px;
        border: 1.5px solid #eee;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        gap: 20px;
        background: #fff;
        transition: var(--transition);
    }
    .icon-wrap {
        width: 45px;
        height: 45px;
        background: #f9f9f9;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 1.2rem;
        transition: var(--transition);
    }
    .box-content .title {
        display: block;
        font-weight: 700;
        color: var(--noir);
        font-size: 1rem;
    }
    .box-content .desc {
        display: block;
        font-size: 0.75rem;
        color: #999;
    }
    .payment-box-luxury input:checked + .box-content {
        border-color: var(--accent);
        background: #fdfaf5;
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.1);
    }
    .payment-box-luxury input:checked + .box-content .icon-wrap {
        background: var(--accent-gradient);
        color: #fff;
    }
</style>  