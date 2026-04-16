<?php
session_start();
require_once("../lib/coreFunction.php");
if (!isset($_SESSION['pin_verified']) || $_SESSION['pin_verified'] !== true) {
    header("Location: verify_pin.php");
    exit();
}
$f = new CoreFunction();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $gender = $_POST['gender'] ?? 1;
    $address = $_POST['address'] ?? 'Chưa cập nhật';

    // Validation
    if (empty($fullname) || empty($username) || empty($email) || empty($password)) {
        header("Location: register.php?error=Vui lòng điền đầy đủ thông tin bắt buộc.");
        exit;
    }

    if ($password !== $confirm_password) {
        header("Location: register.php?error=Mật khẩu xác nhận không khớp.");
        exit;
    }

    // Check duplicates
    if ($f->getOne("SELECT id FROM user WHERE username = '$username'")) {
        header("Location: register.php?error=Tên đăng nhập này đã có người sử dụng!");
        exit;
    }

    if ($f->getOne("SELECT id FROM user WHERE email = '$email'")) {
        header("Location: register.php?error=Địa chỉ Email này đã được đăng ký!");
        exit;
    }

    if ($f->getOne("SELECT id FROM user WHERE phone = '$phone'")) {
        header("Location: register.php?error=Số điện thoại này đã tồn tại trên hệ thống!");
        exit;
    }

    // Handle Avatar
    $avatar_name = "default_avatar.png";
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $target_dir = "../asset/images/";
        $filename = time() . "-" . basename($_FILES["avatar"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
            $avatar_name = $filename;
        }
    }

    // Prepare data (Plain text password as requested)
    $data = [
        'username' => $username,
        'password' => $password,
        'name' => $fullname,
        'email' => $email,
        'phone' => $phone,
        'avatar' => $avatar_name,
        'role' => 'admin',
        'address' => $address,
        'gender' => $gender
    ];

    try {
        $f->addRecord('user', $data);
        header("Location: login.php?success=Đăng ký Admin thành công! Mời Sếp đăng nhập.");
        exit();
    } catch (Exception $e) {
        header("Location: register.php?error=Lỗi hệ thống khi tạo tài khoản Admin.");
        exit;
    }
} else {
    header("Location: register.php");
    exit;
}
?>