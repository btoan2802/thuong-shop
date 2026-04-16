<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $user = $f->getOne("SELECT * FROM user WHERE id = $id");

    if (isset($_POST['submit'])) {
        $data = [
            'username' => $_POST['username'],
            'password' => $_POST['password'],
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address'],
            'role' => $_POST['role'],
            'gender' => $_POST['gender']
        ];

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
            $filename = time() . "-" . $_FILES["avatar"]["name"];
            if (move_uploaded_file($_FILES["avatar"]["tmp_name"], "../asset/images/" . $filename)) {
                $data['avatar'] = $filename;
            }
        }

        $f->editRecord('user', $id, $data);
        header("Location: index.php?page=userList&success=Cập nhật thông tin thành công!");
    }
} else {
    header("Location: index.php?page=userList");
}
?>

<div class="mb-40">
    <h1 class="page-title">Sửa thông tin: <?= $user['name'] ?></h1>
</div>

<div class="luxury-card">
    <form action="" method="POST" enctype="multipart/form-data" class="form-luxury">
        <div class="form-grid-2col">
            <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="name" class="form-control" value="<?= $user['name'] ?>" required>
            </div>
            <div class="form-group">
                <label>Tên đăng nhập</label>
                <input type="text" name="username" class="form-control" value="<?= $user['username'] ?>" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu (Hiển thị thuần)</label>
                <input type="text" name="password" class="form-control" value="<?= $user['password'] ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= $user['email'] ?>" required>
            </div>
            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="phone" class="form-control" value="<?= $user['phone'] ?>" required>
            </div>
            <div class="form-group">
                <label>Vai trò</label>
                <select name="role" class="form-control">
                    <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label>Giới tính</label>
                <select name="gender" class="form-control">
                    <option value="1" <?= $user['gender'] == 1 ? 'selected' : '' ?>>Nam</option>
                    <option value="0" <?= $user['gender'] == 0 ? 'selected' : '' ?>>Nữ</option>
                </select>
            </div>
            <div class="form-group">
                <label>Ảnh đại diện (Để trống nếu giữ nguyên)</label>
                <input type="file" name="avatar" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label>Địa chỉ</label>
            <textarea name="address" class="form-control"><?= $user['address'] ?></textarea>
        </div>
        
        <div style="margin-top: 30px; display: flex; gap: 15px;">
            <button type="submit" name="submit" class="btn-luxury">Cập nhật</button>
            <a href="index.php?page=userList" class="btn-luxury btn-luxury-outline">Hủy bỏ</a>
        </div>
    </form>
</div>
