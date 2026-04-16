<?php
session_start();
require_once("../lib/coreFunction.php");
$f = new CoreFunction();

// Xử lý khi Submit mã PIN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pin_submit'])) {
    $input_pin = implode('', $_POST['pin']);
    $correct_pin = $f->getOne("SELECT setting_value FROM system_settings WHERE setting_key = 'admin_registration_pin'")['setting_value'] ?? '141620';

    if ($input_pin === $correct_pin) {
        $_SESSION['pin_verified'] = true;
        header("Location: register.php");
        exit();
    } else {
        $error = "Mã PIN không chính xác. Vui lòng thử lại!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Xác thực quyền truy cập | Junia Fashion</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../asset/images/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    
    <style>
        :root {
            --accent: #D4AF37;
            --accent-gradient: linear-gradient(135deg, #a67c00 0%, #bf953f 15%, #fcf6ba 40%, #b88a44 60%, #ddac17 85%, #8a6d3b 100%);
            --noir: #0a0a0a;
        }
        body {
            background-color: #fcfaf7;
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .pin-card {
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.06);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        .icon-lock {
            width: 70px;
            height: 70px;
            background: var(--accent-gradient);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 30px;
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.3);
        }
        .title {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            margin-bottom: 10px;
            color: #111;
        }
        .subtitle {
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 40px;
        }
        .pin-inputs {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 40px;
        }
        .pin-input {
            width: 45px;
            height: 55px;
            border: 2px solid #eee;
            border-radius: 12px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--noir);
            transition: all 0.3s;
        }
        .pin-input:focus {
            border-color: var(--accent);
            outline: none;
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.2);
            background-color: #fdfaf7;
        }
        .submit-btn {
            background: var(--accent-gradient);
            background-size: 200% auto;
            color: #222;
            border: none;
            border-radius: 30px;
            padding: 16px;
            width: 100%;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2);
        }
        .submit-btn:hover {
            background-position: right center;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(212, 175, 55, 0.4);
        }
        .error-msg {
            color: #d93025;
            font-size: 0.85rem;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .back-link {
            display: inline-block;
            margin-top: 30px;
            color: #aaa;
            text-decoration: none;
            font-size: 0.85rem;
        }
        .back-link:hover {
            color: var(--accent);
        }
    </style>
</head>
<body>

<div class="pin-card">
    <div class="icon-lock">
        <i class="fa fa-shield-halved"></i>
    </div>
    
    <h2 class="title">Bảo Mật Hệ Thống</h2>
    <p class="subtitle">Vui lòng nhập mã PIN bảo mật để tiếp tục</p>

    <?php if (isset($error)): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form method="post" id="pinForm">
        <div class="pin-inputs">
            <input type="text" name="pin[]" class="pin-input" maxlength="1" pattern="\d*" inputmode="numeric" required autofocus>
            <input type="text" name="pin[]" class="pin-input" maxlength="1" pattern="\d*" inputmode="numeric" required>
            <input type="text" name="pin[]" class="pin-input" maxlength="1" pattern="\d*" inputmode="numeric" required>
            <input type="text" name="pin[]" class="pin-input" maxlength="1" pattern="\d*" inputmode="numeric" required>
            <input type="text" name="pin[]" class="pin-input" maxlength="1" pattern="\d*" inputmode="numeric" required>
            <input type="text" name="pin[]" class="pin-input" maxlength="1" pattern="\d*" inputmode="numeric" required>
        </div>
        
        <button type="submit" name="pin_submit" class="submit-btn" id="submitBtn">Tiếp tục</button>
    </form>

    <a href="login.php" class="back-link"><i class="fa fa-arrow-left"></i> Quay lại Đăng nhập</a>
</div>

<script>
    const inputs = document.querySelectorAll('.pin-input');
    
    inputs.forEach((input, index) => {
        // Tự động chuyển qua ô tiếp theo
        input.addEventListener('input', (e) => {
            if (e.target.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        // Xử lý phím Backspace
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                inputs[index - 1].focus();
            }
        });

        // Chỉ cho phép nhập số
        input.addEventListener('keypress', (e) => {
            if (!/\d/.test(e.key)) {
                e.preventDefault();
            }
        });
    });

    // Handle paste
    document.getElementById('pinForm').addEventListener('paste', (e) => {
        const pasteData = e.clipboardData.getData('text').slice(0, 6);
        if (/^\d+$/.test(pasteData)) {
            pasteData.split('').forEach((char, i) => {
                if (inputs[i]) inputs[i].value = char;
            });
            inputs[Math.min(pasteData.length, inputs.length - 1)].focus();
        }
    });
</script>

</body>
</html>
