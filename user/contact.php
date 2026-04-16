<div class="container py-5">
    <div class="section-title text-center mb-5">
        <p class="elegant-font" style="color: var(--accent); text-transform: uppercase; letter-spacing: 3px; font-size: 0.9rem;">Kết nối với Junia</p>
        <h2>Thông Tin Liên Hệ</h2>
    </div>

    <div class="contact-wrapper">
        <!-- Info Panel -->
        <div class="contact-info-panel">
            <h2 class="elegant-font">Chào bạn!</h2>
            <p style="color: rgba(255,255,255,0.7); margin-bottom: 40px;">Chúng tôi luôn sẵn sàng lắng nghe ý kiến và giải đáp mọi thắc mắc của bạn.</p>
            
            <div class="contact-item">
                <i class="fa-solid fa-location-dot"></i>
                <div>
                    <strong style="display: block; letter-spacing: 1px;">ĐỊA CHỈ</strong>
                    <span style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">123 Fashion Street, Quận 1, TP. Hồ Chí Minh</span>
                </div>
            </div>

            <div class="contact-item">
                <i class="fa-solid fa-phone"></i>
                <div>
                    <strong style="display: block; letter-spacing: 1px;">ĐIỆN THOẠI</strong>
                    <span style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">(028) 123 4567 - 033 497 2658</span>
                </div>
            </div>

            <div class="contact-item">
                <i class="fa-solid fa-envelope"></i>
                <div>
                    <strong style="display: block; letter-spacing: 1px;">EMAIL</strong>
                    <span style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">contact@juniafashion.vn</span>
                </div>
            </div>

            <div class="social-links">
                <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-pinterest-p"></i></a>
                <a href="#"><i class="fa-brands fa-tiktok"></i></a>
            </div>
        </div>

        <!-- Form Panel -->
        <div class="contact-form-panel">
            <h3 class="elegant-font mb-4" style="text-align: center;">Gửi tin nhắn</h3>
            <form action="<?=PATH?>page=contact" method="POST">
                <div class="floating-input-group">
                    <input type="text" name="id" required>
                    <label>Mã khách hàng</label>
                </div>
                <div class="floating-input-group">
                    <input type="text" name="fullname" required>
                    <label>Họ và tên</label>
                </div>
                <div class="floating-input-group">
                    <input type="email" name="email" required>
                    <label>Địa chỉ Email</label>
                </div>
                <div class="floating-input-group">
                    <textarea name="contact" rows="4" required></textarea>
                    <label>Nội dung cần hỗ trợ</label>
                </div>
                <div style="text-align: center;">
                    <button type="submit" class="btn-elegant px-5 py-3" style="border-radius: 30px; min-width: 250px;">Gửi Tin Nhắn Ngay</button>
                </div>
            </form>
        </div>
    </div>

    <?php if($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="mt-5 p-4 text-center" style="background: var(--white); border-radius: 15px; box-shadow: var(--shadow); border: 1px dashed var(--accent);">
            <i class="fa-solid fa-circle-check mb-3" style="font-size: 2rem; color: #4CAF50;"></i>
            <h4 class="elegant-font">Cảm ơn bạn!</h4>
            <p style="color: #666;">Yêu cầu liên hệ của bạn (#<?= htmlspecialchars($_POST['id']) ?>) đã được gửi thành công. Chúng tôi sẽ phản hồi sớm nhất!</p>
        </div>
    <?php endif; ?>
</div>