<?php 
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $pass = $_POST['password'];
        
        // 1. Kiểm tra Email tồn tại
        $checkEmail = $f->getOne("SELECT * FROM user WHERE email = '$email'");
        
        if(!$checkEmail) {
            header("Location: index.php?page=login&error=Email này chưa được đăng ký!");
            exit();
        }
        
        // 2. Kiểm tra mật khẩu (Chỉ dùng văn bản thuần theo yêu cầu)
        if($checkEmail['password'] !== $pass) {
            header("Location: index.php?page=login&error=Mật khẩu không chính xác. Vui lòng thử lại!");
            exit();
        }
        
        // Đăng nhập thành công
        $_SESSION['user'] = $checkEmail['username'];
        $_SESSION['userId'] = $checkEmail['id'];

        if(isset($_POST['remember'])){
            setcookie("username", $email, time() + (3600 * 24 * 30), "/"); 
            setcookie("password", $pass, time() + (3600 * 24 * 30), "/");
        } else {
            setcookie("username", "", time() - 3600, "/"); 
            setcookie("password", "", time() - 3600, "/");
        }
        
        header("Location: index.php?success=Chào mừng trở lại, " . $checkEmail['name'] . "!");
        exit();
    }
?>
