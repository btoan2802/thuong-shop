<?php
  if (isset($_SESSION['userId'])){
    exit(header('Location: index.php'));
  }
?>

<!-- Override layout: hide header/footer spacing, full-screen login -->
<style>
  /* Full-screen takeover */
  body { background: #0f0d0a; }
  main { padding: 0 !important; margin: 0 !important; }
  .row.content { margin: 0 !important; padding: 0 !important; max-width: 100% !important; width: 100% !important; }
  header, footer, .main-nav, .top-header { display: none !important; }

  /* ===== LOGIN WRAPPER ===== */
  .login-fullscreen {
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: #0f0d0a;
    font-family: 'Poppins', sans-serif;
  }

  /* ===== LEFT: IMAGE PANEL ===== */
  .login-image-panel {
    position: relative;
    overflow: hidden;
    background: #1a1410;
  }
  .login-image-panel img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.85;
    transition: transform 8s ease;
  }
  .login-image-panel:hover img {
    transform: scale(1.04);
  }
  .login-image-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(15,13,10,0.3) 0%, rgba(15,13,10,0.6) 100%);
  }
  .login-brand-badge {
    position: absolute;
    top: 40px;
    left: 40px;
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 2;
  }
  .login-brand-badge img {
    height: 36px;
    width: auto;
    object-fit: contain;
    opacity: 0.95;
  }
  .login-brand-badge span {
    font-family: 'Playfair Display', serif;
    color: #fff;
    font-size: 1rem;
    letter-spacing: 4px;
    text-transform: uppercase;
    font-weight: 400;
  }
  .login-image-caption {
    position: absolute;
    bottom: 50px;
    left: 40px;
    right: 40px;
    z-index: 2;
  }
  .login-image-caption h2 {
    font-family: 'Playfair Display', serif;
    color: #fff;
    font-size: 2.2rem;
    font-weight: 700;
    line-height: 1.3;
    margin-bottom: 12px;
    text-shadow: 0 2px 20px rgba(0,0,0,0.4);
  }
  .login-image-caption p {
    color: rgba(255,255,255,0.65);
    font-size: 0.9rem;
    letter-spacing: 1px;
  }
  .caption-line {
    width: 40px;
    height: 2px;
    background: #c9a96e;
    margin-bottom: 16px;
  }

  /* ===== RIGHT: FORM PANEL ===== */
  .login-form-panel {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 60px 70px;
    background: #fff;
    position: relative;
    overflow: hidden;
  }
  .login-form-panel::before {
    content: 'J';
    font-family: 'Playfair Display', serif;
    font-size: 300px;
    color: rgba(201,169,110,0.04);
    position: absolute;
    top: -60px;
    right: -30px;
    line-height: 1;
    pointer-events: none;
    user-select: none;
  }

  .login-form-inner { position: relative; z-index: 1; max-width: 380px; width: 100%; }

  .login-eyebrow {
    font-size: 0.72rem;
    letter-spacing: 4px;
    text-transform: uppercase;
    color: #c9a96e;
    margin-bottom: 12px;
    font-weight: 500;
  }
  .login-title {
    font-family: 'Playfair Display', serif;
    font-size: 2.4rem;
    color: #1a1410;
    margin-bottom: 8px;
    font-weight: 700;
    line-height: 1.15;
  }
  .login-subtitle {
    color: #999;
    font-size: 0.88rem;
    margin-bottom: 40px;
    line-height: 1.6;
  }
  .login-divider {
    width: 36px;
    height: 2px;
    background: #c9a96e;
    margin-bottom: 30px;
  }

  /* ===== FORM FIELDS ===== */
  .login-field {
    margin-bottom: 22px;
    position: relative;
  }
  .login-field label {
    display: block;
    font-size: 0.7rem;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #bbb;
    margin-bottom: 8px;
    font-weight: 500;
    transition: color 0.2s;
  }
  .login-field:focus-within label { color: #c9a96e; }

  .login-input-wrap {
    position: relative;
  }
  .login-input-wrap i {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    color: #ccc;
    font-size: 0.85rem;
    transition: color 0.2s;
    pointer-events: none;
  }
  .login-field:focus-within .login-input-wrap i { color: #c9a96e; }

  .login-input-wrap input {
    width: 100%;
    padding: 14px 65px 14px 44px; /* Tăng padding-right để chừa chỗ cho con mắt đã xích vô */
    border: 1.5px solid #ebebeb;
    border-radius: 10px;
    font-family: 'Poppins', sans-serif;
    font-size: 0.9rem;
    color: #1a1410;
    background: #fafafa;
    outline: none;
    transition: all 0.25s;
    box-sizing: border-box;
  }
  .login-input-wrap input:focus {
    border-color: #c9a96e;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(201,169,110,0.08);
  }
  .login-input-wrap input::placeholder { color: #ccc; }

  /* Password toggle */
  .pwd-toggle {
    position: absolute;
    right: 30px; /* Đẩy sâu vào trong khung input thêm nữa */
    top: 50%;
    transform: translateY(-50%);
    color: #bbb;
    cursor: pointer;
    font-size: 1rem;
    transition: var(--transition);
    background: transparent;
    border: none;
    padding: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    width: 40px;
    height: 40px;
  }
  .pwd-toggle:hover { color: #c9a96e; }

  /* ===== OPTIONS ROW ===== */
  .login-options {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 30px;
    margin-top: -6px;
  }
  .remember-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
  }
  .remember-wrap input[type="checkbox"] {
    appearance: none;
    width: 17px;
    height: 17px;
    border: 1.5px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    position: relative;
    transition: all 0.2s;
    flex-shrink: 0;
  }
  .remember-wrap input[type="checkbox"]:checked {
    background: #c9a96e;
    border-color: #c9a96e;
  }
  .remember-wrap input[type="checkbox"]:checked::after {
    content: '✓';
    position: absolute;
    top: -1px; left: 2px;
    font-size: 11px;
    color: #fff;
    font-weight: 700;
}
  .remember-wrap span { font-size: 0.82rem; color: #888; }
  .forgot-link { font-size: 0.82rem; color: #c9a96e; text-decoration: none; }
  .forgot-link:hover { text-decoration: underline; }

  /* ===== SUBMIT BUTTON ===== */
  .login-btn {
    width: 100%;
    padding: 16px;
    background: var(--accent-gradient);
    background-size: 200% auto;
    color: #433422;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 2px;
    cursor: pointer;
    transition: var(--transition);
    box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2);
    margin-top: 10px;
    margin-bottom: 20px;
  }
  .login-btn:hover {
    background-position: right center;
    transform: translateY(-2px);
    box-shadow: 0 15px 30px rgba(212, 175, 55, 0.4);
  }
  .login-btn span { position: relative; z-index: 1; }
  .login-btn:hover { box-shadow: 0 8px 30px rgba(201,169,110,0.35); transform: translateY(-1px); }
  .login-btn:active { transform: translateY(0); }

  /* ===== REGISTER LINK ===== */
  .login-register-row {
    text-align: center;
    font-size: 0.83rem;
    color: #aaa;
    margin-top: 30px;
  }
  .login-register-row a {
    color: #c9a96e;
    font-weight: 600;
    text-decoration: none;
    border-bottom: 1px solid transparent;
    transition: border-color 0.2s;
  }
  .login-register-row a:hover { border-bottom-color: #c9a96e; }

  /* ===== BACK LINK ===== */
  .login-back {
    position: absolute;
    top: 30px;
    right: 35px;
    font-size: 0.78rem;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: #bbb;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: color 0.2s;
  }
  .login-back:hover { color: #c9a96e; }

  /* ===== RESPONSIVE ===== */
  @media (max-width: 900px) {
    .login-fullscreen { grid-template-columns: 1fr; }
    .login-image-panel { display: none; }
    .login-form-panel { padding: 80px 20px 50px 20px; min-height: 100vh; justify-content: center; }
    .login-form-inner { max-width: 100%; }
    .login-options { flex-wrap: wrap; gap: 15px; }
    .forgot-link { width: 100%; text-align: left; }
    .login-back { top: 20px; right: 20px; }
  }

  /* ===== ERROR ALERT (REFINED) ===== */
  .login-alert {
    background: rgba(192, 57, 43, 0.05);
    border: 1px solid rgba(192, 57, 43, 0.2);
    color: #c0392b;
    border-radius: 12px;
    padding: 15px 20px;
    font-size: 0.85rem;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 12px;
    animation: slideIn 0.5s ease;
  }
  @keyframes slideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>

<!-- SweetAlert2 for Premium Notifications -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Google Fonts preload -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<div class="login-fullscreen">
  <!-- LEFT: Image Panel -->
  <div class="login-image-panel">
    <img src="asset/images/login_bg.png" alt="Junia Fashion">
    <div class="login-image-overlay"></div>

    <div class="login-brand-badge">
      <img src="asset/images/junia_logo_white.png" alt="Logo">
      <span>Junia Fashion</span>
    </div>

    <div class="login-image-caption">
      <div class="caption-line"></div>
      <h2>Phong cách<br>định nghĩa bạn.</h2>
      <p>Bộ sưu tập thời trang cao cấp — Mùa xuân 2025</p>
    </div>
  </div>

  <!-- RIGHT: Form Panel -->
  <div class="login-form-panel">
    <a href="<?=PATH?>" class="login-back">
      <i class="fa fa-arrow-left"></i> Trang chủ
    </a>

    <div class="login-form-inner">
      <p class="login-eyebrow">Chào mừng trở lại</p>
      <h1 class="login-title">Đăng nhập</h1>
      <div class="login-divider"></div>
      <p class="login-subtitle">Mời bạn đăng nhập để tiếp tục khám phá bộ sưu tập mới nhất.</p>

      <?php if (isset($_GET['error'])): ?>
      <div class="login-alert">
        <div style="width: 20px; height: 20px; background: #c0392b; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.6rem;">
            <i class="fa fa-times"></i>
        </div>
        <span style="font-weight: 500;"><?= htmlspecialchars($_GET['error']) ?></span>
      </div>
      
      <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '<span style="font-family: \'Playfair Display\', serif; font-size: 1.5rem;">Cửa sổ thông báo</span>',
                html: '<p style="font-size: 0.95rem; color: #666;"><?= htmlspecialchars($_GET['error']) ?></p>',
                icon: 'error',
                iconColor: '#c9a96e',
                confirmButtonText: 'ĐÃ HIỂU',
                confirmButtonColor: '#1a1410',
                background: '#fff',
                backdrop: `rgba(26, 20, 16, 0.4)`,
                customClass: {
                    popup: 'luxury-swal-popup',
                    confirmButton: 'luxury-swal-btn'
                }
            });
        });
      </script>
      <style>
        .luxury-swal-popup { border-radius: 20px !important; border: 1px solid #c9a96e !important; padding: 40px !important; }
        .luxury-swal-btn { border-radius: 30px !important; padding: 12px 40px !important; font-weight: 600 !important; letter-spacing: 2px !important; }
      </style>
      <?php endif; ?>

      <form action="<?=PATH?>page=doLogin" method="POST" id="formLogin" autocomplete="off">

        <div class="login-field">
          <label>Địa chỉ Email</label>
          <div class="login-input-wrap">
            <i class="fa fa-envelope"></i>
            <input type="email" name="email" placeholder="you@example.com"
              value="<?php if(isset($_COOKIE['username'])) echo htmlspecialchars($_COOKIE['username']); ?>" required>
          </div>
        </div>

        <div class="login-field">
          <label>Mật khẩu</label>
          <div class="login-input-wrap">
            <i class="fa fa-lock"></i>
            <input type="password" name="password" id="loginPwd" placeholder="••••••••"
              value="<?php if(isset($_COOKIE['password'])) echo htmlspecialchars($_COOKIE['password']); ?>" required>
            <button type="button" class="pwd-toggle" onclick="togglePwd()" id="pwdEye" title="Hiện/ẩn mật khẩu">
              <i class="fa fa-eye" id="eyeIcon"></i>
            </button>
          </div>
        </div>

        <div class="login-options">
          <label class="remember-wrap">
            <input type="checkbox" name="remember" id="remember">
            <span>Ghi nhớ đăng nhập</span>
          </label>
          <a href="#" class="forgot-link">Quên mật khẩu?</a>
        </div>

        <button type="submit" class="login-btn">
          <span>Đăng nhập</span>
        </button>

        <div class="login-register-row">
          Chưa có tài khoản? <a href="<?=PATH?>page=register">Đăng ký ngay</a>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
  function togglePwd() {
    var input = document.getElementById('loginPwd');
    var icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
      input.type = 'text';
      icon.className = 'fa fa-eye-slash';
    } else {
      input.type = 'password';
      icon.className = 'fa fa-eye';
    }
  }
</script>