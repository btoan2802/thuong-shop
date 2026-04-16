<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Junia Fashion | Quản trị viên</title>
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
        }
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text);
            margin: 0;
        }
        .admin-login-card {
            background: var(--white);
            padding: 60px 50px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }
        .logo-container {
            margin-bottom: 40px;
        }
        .logo-container img {
            max-height: 70px;
            margin-bottom: 10px;
        }
        .brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 0.85rem;
            letter-spacing: 4px;
            color: var(--accent);
            text-transform: uppercase;
            font-weight: 600;
        }
        .form-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            margin-bottom: 35px;
            font-weight: 400;
        }
        .form-control-luxury {
            width: 100%;
            border-radius: 30px;
            padding: 14px 25px;
            border: 1px solid #eee;
            background: #fff;
            margin-bottom: 20px;
            font-size: 0.9rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
            box-sizing: border-box;
        }
        .form-control-luxury:focus {
            border-color: var(--accent);
            background-color: #fdfdfd;
            outline: none;
        }
        .login-btn {
            background: var(--accent-gradient);
            background-size: 200% auto;
            color: #433422;
            border: none;
            border-radius: 30px;
            padding: 14px;
            width: 100%;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.85rem;
            transition: all 0.3s;
            cursor: pointer;
            margin-top: 10px;
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }
        .login-btn:hover {
            background-position: right center;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.5);
        }
        .form-footer {
            margin-top: 30px;
            font-size: 0.8rem;
            color: #999;
        }
        .form-footer a {
            color: #999;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="admin-login-card">
    <div class="logo-container">
        <img src="../asset/images/junia_logo_script.png" alt="Junia Logo">
        <div class="brand-name">JUNIA FASHION</div>
    </div>
    
    <h2 class="form-title">Đăng Nhập Quản Trị</h2>
    
    <form action="doLogin.php" method="post">
        <input type="email" class="form-control-luxury" placeholder="Email" name="email" required>
        <input type="password" class="form-control-luxury" placeholder="Mật khẩu" name="password" required>
        
        <button type="submit" class="login-btn" name="submit">Đăng nhập</button>
    </form>
    
    <div class="form-footer">
        Chưa có tài khoản? <a href="verify_pin.php" style="color: var(--accent); font-weight: 600;">Đăng ký Quản trị</a><br>
        <a href="../index.php" style="margin-top: 15px; display: inline-block;">Quay lại trang chủ</a>
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
            
            // Căn lề cực sang trọng
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
            
            // Xóa tham số trên URL cho sạch
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    </script>
</body>
</html>
