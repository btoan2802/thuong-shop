<?php 
    session_start();
    include("../lib/coreFunction.php");
    $f = new CoreFunction();

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $pass = $_POST['password'];
        
        // 1. Kiểm tra Email và Quyền hạn
        $checkAdmin = $f->getOne("SELECT * FROM user WHERE email = '$email'");
        
        if(!$checkAdmin) {
            header("Location: login.php?error=Email quản trị không tồn tại!");
            exit();
        }
        
        if($checkAdmin['role'] !== 'admin') {
            header("Location: login.php?error=Tài khoản này không có quyền quản trị!");
            exit();
        }
        
        // 2. Kiểm tra mật khẩu (Chỉ dùng văn bản thuần theo yêu cầu)
        if($checkAdmin['password'] !== $pass) {
            header("Location: login.php?error=Mật khẩu quản trị không chính xác!");
            exit();
        }
        
        // Đăng nhập thành công
        $_SESSION['admin'] = $checkAdmin['username'];
        header("Location: index.php?success=Chào mừng Sếp trở lại làm việc!");
        exit();
    }
?>