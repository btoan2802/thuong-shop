<?php
session_start();
if (!isset($_SESSION['pin_verified']) || $_SESSION['pin_verified'] !== true) {
    header("Location: verify_pin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Junia Fashion | Đăng ký Quản trị viên</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../asset/images/favicon.png">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --accent: #D4AF37;
            --accent-gradient: linear-gradient(135deg, #a67c00 0%, #bf953f 15%, #fcf6ba 40%, #b88a44 60%, #ddac17 85%, #8a6d3b 100%);
            --text: #5A4A42;
            --white: #ffffff;
            --bg-soft: #fcfaf7;
        }
        body {
            background-color: var(--bg-soft);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text);
            margin: 0;
            padding: 40px 0;
        }
        .register-card {
            background: var(--white);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.06);
            width: 100%;
            max-width: 550px;
            text-align: center;
        }
        .logo-container {
            margin-bottom: 35px;
        }
        .logo-container img {
            max-height: 60px;
            margin-bottom: 8px;
        }
        .brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 0.75rem;
            letter-spacing: 5px;
            color: var(--accent);
            text-transform: uppercase;
            font-weight: 600;
        }
        .form-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            margin-bottom: 10px;
            font-weight: 400;
        }
        .form-subtitle {
            font-size: 0.85rem;
            color: #999;
            margin-bottom: 40px;
            letter-spacing: 0.5px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-group {
            text-align: left;
            margin-bottom: 25px;
        }
        .form-label {
            display: block;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #bbb;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .form-control-luxury {
            width: 100%;
            border-radius: 30px;
            padding: 12px 25px;
            border: 1px solid #f0f0f0;
            background: #fff;
            font-size: 0.9rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
            box-sizing: border-box;
            color: var(--text);
        }
        .form-control-luxury:focus {
            border-color: var(--accent);
            background-color: #fcfcfc;
            outline: none;
            box-shadow: 0 5px 15px rgba(201, 169, 110, 0.1);
        }
        .register-btn {
            background: var(--accent-gradient);
            background-size: 200% auto;
            color: #433422;
            border: none;
            border-radius: 30px;
            padding: 16px;
            width: 100%;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            font-size: 0.85rem;
            transition: all 0.3s;
            cursor: pointer;
            margin-top: 20px;
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2);
        }
        .register-btn:hover {
            background-position: right center;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(212, 175, 55, 0.4);
        }
        .form-footer {
            margin-top: 35px;
            font-size: 0.85rem;
            color: #999;
        }
        .form-footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }
        
        /* File Input Styling */
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        .file-input-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }
        .file-input-btn {
            display: block;
            background: #f8f9fa;
            border: 1px dashed #ddd;
            padding: 12px;
            border-radius: 30px;
            color: #888;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .file-input-btn:hover {
            border-color: var(--accent);
            background: #fffcf8;
        }
    </style>
</head>
<body>

<div class="register-card">
    <div class="logo-container">
        <img src="../asset/images/junia_logo_script.png" alt="Junia Logo">
        <div class="brand-name">JUNIA FASHION</div>
    </div>
    
    <h2 class="form-title">Tạo Tài Khoản Admin</h2>
    <p class="form-subtitle">Hệ thống quản trị phong cách thượng lưu</p>
    
    <form action="doRegisterAdmin.php" method="post" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Họ và tên</label>
                <input type="text" class="form-control-luxury" placeholder="Nguyễn Văn A" name="fullname" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tên đăng nhập</label>
                <input type="text" class="form-control-luxury" placeholder="admin_junia" name="username" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Email công việc</label>
                <input type="email" class="form-control-luxury" placeholder="admin@junia.vn" name="email" required>
            </div>
            <div class="form-group">
                <label class="form-label">Số điện thoại</label>
                <input type="tel" class="form-control-luxury" placeholder="090 123 4567" name="phone" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Mật khẩu</label>
                <input type="password" class="form-control-luxury" placeholder="••••••••" name="password" required>
            </div>
            <div class="form-group">
                <label class="form-label">Xác nhận mật khẩu</label>
                <input type="password" class="form-control-luxury" placeholder="••••••••" name="confirm_password" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Giới tính</label>
                <div style="display: flex; gap: 20px; padding: 10px 0;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; cursor: pointer;">
                        <input type="radio" name="gender" value="1" checked style="accent-color: var(--accent);"> Nam
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; cursor: pointer;">
                        <input type="radio" name="gender" value="0" style="accent-color: var(--accent);"> Nữ
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Ảnh đại diện</label>
                <div class="file-input-wrapper">
                    <div class="file-input-btn" id="fileName">Tải ảnh chân dung</div>
                    <input type="file" name="avatar" onchange="if(this.files[0]) document.getElementById('fileName').innerText = this.files[0].name">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Địa chỉ thường trú</label>
            <input type="text" class="form-control-luxury" placeholder="Số nhà, Tên đường, Quận/Huyện, Thành phố" name="address" required>
        </div>
        
        <button type="submit" class="register-btn" name="submit">Đăng ký ngay</button>
    </form>
    
    <div class="form-footer">
        Đã có tài khoản quản trị? <a href="login.php">Đăng nhập</a><br>
        <a href="../index.php" style="color: #999; font-weight: 400; margin-top: 15px; display: inline-block;">Quay lại trang chủ khách hàng</a>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script>
        function showNotify(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `custom-toast toast-${type}`;
            toast.innerHTML = `
                <i class="fa ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            `;
            
            const styles = {
                position: 'fixed',
                top: '20px',
                right: '20px',
                padding: '15px 25px',
                borderRadius: '12px',
                backgroundColor: type === 'success' ? 'rgba(212, 175, 55, 0.95)' : 'rgba(180, 0, 0, 0.95)',
                color: type === 'success' ? '#111' : '#fff',
                zIndex: '9999',
                display: 'flex',
                alignItems: 'center',
                gap: '12px',
                boxShadow: '0 10px 30px rgba(0,0,0,0.2)',
                backdropFilter: 'blur(10px)',
                fontWeight: '600',
                fontSize: '0.9rem',
                transform: 'translateX(120%)',
                transition: 'all 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55)'
            };
            
            Object.assign(toast.style, styles);
            document.body.appendChild(toast);
            
            setTimeout(() => toast.style.transform = 'translateX(0)', 100);
            setTimeout(() => {
                toast.style.transform = 'translateX(120%)';
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        }

        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) showNotify(urlParams.get('success'), 'success');
            if (urlParams.has('error')) showNotify(urlParams.get('error'), 'error');
            
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    </script>
</body>
</html>
