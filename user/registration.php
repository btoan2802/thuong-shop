<?php
if (isset($_POST['submit'])) {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];

    // Kiểm tra kết nối CSDL
    if (!$f->conn) {
        header("Location: index.php?page=register&error=Không thể kết nối CSDL!");
        exit;
    }

    // Kiểm tra mật khẩu
    if ($password !== $confirm_password) {
        header("Location: index.php?page=register&error=Mật khẩu xác nhận không khớp!");
        exit;
    }

    // Kiểm tra trùng lặp
    if ($f->getOne("SELECT id FROM user WHERE username = '$username'")) {
        header("Location: index.php?page=register&error=Tên đăng nhập đã tồn tại!");
        exit;
    }
    if ($f->getOne("SELECT id FROM user WHERE email = '$email'")) {
        header("Location: index.php?page=register&error=Email này đã được sử dụng!");
        exit;
    }
    if ($f->getOne("SELECT id FROM user WHERE phone = '$phone'")) {
        header("Location: index.php?page=register&error=Số điện thoại này đã được đăng ký!");
        exit;
    }
    if ($f->getOne("SELECT id FROM user WHERE username = '$username'")) {
        header("Location: index.php?page=register&error=Tên đăng nhập này đã có người sử dụng!");
        exit;
    }

    // Xử lý ảnh đại diện
    $avatar_name = "default_avatar.png"; // Ảnh mặc định bạn vừa gửi
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $target_dir = "asset/images/";
        $filename = time() . "-" . basename($_FILES["avatar"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
            $avatar_name = $filename;
        }
    }

    // Lưu vào CSDL
    $data = [
        'username' => $username,
        'password' => $password,
        'name' => $fullname,
        'gender' => $gender,
        'email' => $email,
        'phone' => $phone,
        'avatar' => $avatar_name,
        'address' => $address,
        'role' => 'user'
    ];

    try {
        $f->addRecord('user', $data);
        header("Location: index.php?page=login&success=Đăng ký thành công! Vui lòng đăng nhập.");
        exit();
    } catch (Exception $e) {
        header("Location: index.php?page=register&error=Lỗi hệ thống khi đăng ký!");
        exit();
    }
}
?>
<div class="container py-5">
    <div class="registration-card">
        <div class="section-title text-center mb-5">
            <p class="elegant-font gold-text" style="text-transform: uppercase; letter-spacing: 4px; font-size: 0.85rem; margin-bottom: 5px;">Trở thành hội viên</p>
            <h2 class="mb-5">Đăng Ký Tài Khoản</h2>
        </div>

        <form action="<?=PATH?>page=register" method="POST" enctype="multipart/form-data" class="mt-5">
            <div class="form-row-luxury">
                <div class="form-group-luxury">
                    <label>Họ và tên</label>
                    <input type="text" name="fullname" placeholder="Nhập họ và tên..." required>
                </div>
                <div class="form-group-luxury">
                    <label>Giới tính</label>
                    <div class="gender-selection">
                        <label class="radio-container">Nam
                            <input type="radio" name="gender" value="1" checked>
                            <span class="checkmark"></span>
                        </label>
                        <label class="radio-container">Nữ
                            <input type="radio" name="gender" value="0">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-row-luxury">
                <div class="form-group-luxury">
                    <label>Ngày sinh</label>
                    <input type="date" name="birthday" required>
                </div>
                <div class="form-group-luxury">
                    <label>Số điện thoại</label>
                    <input type="tel" name="phone" placeholder="Nhập số điện thoại..." required>
                </div>
            </div>

            <div class="form-row-luxury">
                <div class="form-group-luxury">
                    <label>Địa chỉ Email</label>
                    <input type="email" name="email" placeholder="example@gmail.com" required>
                </div>
                <div class="form-group-luxury">
                    <label>Địa chỉ thường trú</label>
                    <input type="text" name="address" placeholder="Nhập địa chỉ của bạn..." required>
                </div>
            </div>

            <div class="form-row-luxury">
                <div class="form-group-luxury">
                    <label>Tên đăng nhập</label>
                    <input type="text" name="username" placeholder="Nhập tên đăng nhập..." required>
                </div>
                <div class="form-group-luxury">
                    <label>Ảnh đại diện</label>
                    <div class="file-custom">
                        <input type="file" name="avatar" id="avatar" onchange="updateFileName(this)">
                        <label for="avatar" id="file-label"><i class="fa fa-upload"></i> Chọn ảnh đại diện</label>
                    </div>
                </div>
            </div>

            <div class="form-row-luxury">
                <div class="form-group-luxury">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" id="regPwd" placeholder="••••••••" required>
                    <button type="button" class="pwd-toggle-luxury" onclick="toggleRegPwd('regPwd', 'regEye1')" id="regEye1">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
                <div class="form-group-luxury">
                    <label>Xác nhận mật khẩu</label>
                    <input type="password" name="confirm_password" id="confirmPwd" placeholder="••••••••" required>
                    <button type="button" class="pwd-toggle-luxury" onclick="toggleRegPwd('confirmPwd', 'regEye2')" id="regEye2">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="text-center mt-5">
                <button type="submit" class="btn-elegant px-5 py-3" name="submit">Đăng ký thành viên</button>
                <p class="mt-5" style="color: #888; font-size: 0.9rem; letter-spacing: 0.5px;">
                    Đã có tài khoản? <a href="<?=PATH?>page=login" class="gold-text" style="text-decoration: none; font-weight: 600; display: inline;">Đăng nhập ngay</a>
                </p>
            </div>
        </form>
    </div>
</div>

<script>
function updateFileName(input) {
    const label = document.getElementById('file-label');
    if (input.files && input.files.length > 0) {
        label.innerHTML = '<i class="fa fa-check"></i> ' + input.files[0].name;
    }
}

function toggleRegPwd(inputId, btnId) {
    const input = document.getElementById(inputId);
    const icon = document.querySelector(`#${btnId} i`);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fa fa-eye';
    }
}
</script>