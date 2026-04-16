<?php
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        
        // Don't allow deleted yourself
        $currentUser = $f->getOne("SELECT username FROM user WHERE id = $id");
        if($currentUser['username'] == $_SESSION['admin']){
            echo "<script>window.location.href='index.php?page=userList&error=Bạn không thể tự xóa chính mình!';</script>";
            exit();
        }

        $f->delRecord('user', $id);
        header("Location: index.php?page=userList&success=Đã xóa người dùng thành công!");
    }
?>
