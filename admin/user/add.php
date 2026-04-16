<?php
if (isset($_POST['submit'])) {
    $data = [
        'username' => $_POST['username'],
        'password' => $_POST['password'], // Plain text as requested
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'address' => $_POST['address'],
        'role' => $_POST['role'],
        'gender' => $_POST['gender']
    ];

    // Handle Avatar
    $data['avatar'] = 'default_avatar.png';
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $filename = time() . "-" . $_FILES["avatar"]["name"];
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], "../asset/images/" . $filename)) {
            $data['avatar'] = $filename;
        }
    }

    $f->addRecord('user', $data);
    header("Location: index.php?page=userList&success=Đã thêm người dùng mới thành công!");
}
?>

<div class="mb-40">
    <h1 class="page-title">Thêm người dùng mới</h1>
</div>

<div class="luxury-card">
    <form action="" method="POST" enctype="multipart/form-data" class="form-luxury">
        <div class="form-grid-2col">
            <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="name" class="form-control" required placeholder="Nguyễn Văn A">
            </div>
            <div class="form-group">
                <label>Tên đăng nhập</label>
                <input type="text" name="username" class="form-control" required placeholder="username">
            </div>
            <div class="form-group">
                <label>Mật khẩu (Hiển thị thuần)</label>
                <input type="text" name="password" class="form-control" required placeholder="password">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required placeholder="email@example.com">
            </div>
            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="phone" class="form-control" required placeholder="090...">
            </div>
            <div class="form-group">
                <label>Vai trò</label>
                <select name="role" class="form-control">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label>Giới tính</label>
                <select name="gender" class="form-control">
                    <option value="1">Nam</option>
                    <option value="0">Nữ</option>
                </select>
            </div>
            <div class="form-group">
                <label>Ảnh đại diện</label>
                <input type="file" name="avatar" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label>Địa chỉ</label>
            <textarea name="address" class="form-control" placeholder="Nhập địa chỉ..."></textarea>
        </div>
        
        <div style="margin-top: 30px; display: flex; gap: 15px;">
            <button type="submit" name="submit" class="btn-luxury">Lưu người dùng</button>
            <a href="index.php?page=userList" class="btn-luxury btn-luxury-outline">Hủy bỏ</a>
        </div>
    </form>
</div>
