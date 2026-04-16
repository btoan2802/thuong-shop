<?php
if (!isset($_SESSION['userId']) || empty($_SESSION['userId'])) {
    exit(header("Location: index.php?page=login"));
}
require('lib/file.php');

$userId = $_SESSION['userId'];
$sql = "SELECT * FROM user WHERE id = $userId";
$result = $f->getOne($sql);

$passOldErr = $passNewErr = '';
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $image = $_FILES['avatar'];
    $pass_old = $_POST['password-old'];
    $pass_new = $_POST['password-new'];

    $a = array(
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'address' => $_POST['address'],
        'gender' => $_POST['gender'],
        'username' => $_POST['username'],
    );

    $canUpdate = true;

    if ($image['size'] != 0) {
        $u = new Upload();
        $newImageName = $u->doUpload($image);
        if ($newImageName) {
            $a['avatar'] = $newImageName;
        }
    }

    if (!empty($pass_old) || !empty($pass_new)) {
        if (empty($pass_old)) {
            $passOldErr = "Vui lòng nhập mật khẩu cũ để xác thực";
            $canUpdate = false;
        } else if ($pass_old !== $result['password']) {
            $passOldErr = "Mật khẩu cũ không chính xác";
            $canUpdate = false;
        } else if (empty($pass_new)) {
            $passNewErr = "Vui lòng nhập mật khẩu mới";
            $canUpdate = false;
        } else {
            $a['password'] = $pass_new;
        }
    }

    if ($canUpdate) {
        if ($f->editRecord("user", $userId, $a)) {
            $successMsg = "Thông tin cá nhân đã được cập nhật!";
            $result = $f->getOne($sql);
            $_SESSION['user'] = $a['username'];
        } else {
            $passOldErr = "Lỗi lưu dữ liệu.";
        }
    }
}
?>

<style>
    .profile-container {
        max-width: 900px;
        margin: 60px auto;
        perspective: 1000px;
    }

    .luxury-profile-card {
        background: #fff;
        border-radius: 30px;
        padding: 60px;
        box-shadow: 0 30px 100px rgba(90, 74, 66, 0.08);
        border: 1px solid rgba(212, 175, 55, 0.1);
        position: relative;
    }

    .profile-header-center {
        text-align: center;
        margin-bottom: 50px;
    }

    .profile-nav-tabs {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-bottom: 50px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 20px;
    }

    .profile-nav-link {
        font-family: 'Poppins', sans-serif;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #aaa;
        text-decoration: none !important;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
    }

    .profile-nav-link:hover,
    .profile-nav-link.active {
        color: var(--accent);
    }

    .profile-nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -21px;
        left: 0;
        width: 100%;
        height: 2px;
        background: var(--accent);
    }

    .avatar-center-wrapper {
        position: relative;
        width: 140px;
        height: 140px;
        margin: 0 auto 25px;
    }

    .avatar-main {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        padding: 5px;
        border: 1px solid var(--accent);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .avatar-edit-icon {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 38px;
        height: 38px;
        background: #1a1a1a;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 3px solid #fff;
        transition: all 0.3s ease;
    }

    .avatar-edit-icon:hover {
        background: var(--accent);
        transform: scale(1.1);
    }

    .form-section-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.2rem;
        color: #433422;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .form-section-title::after {
        content: '';
        height: 1px;
        background: #f0f0f0;
        flex-grow: 1;
    }

    .input-group-luxury {
        margin-bottom: 35px;
    }

    .label-luxury {
        display: block;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #999;
        margin-bottom: 12px;
        font-weight: 600;
        padding-left: 5px;
    }

    .control-luxury {
        width: 100%;
        background: #fcfcfc;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 14px 25px;
        font-size: 0.95rem;
        color: var(--text);
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .control-luxury:focus {
        background: #fff;
        border-color: var(--accent);
        outline: none;
        box-shadow: 0 8px 25px rgba(212, 175, 55, 0.08);
    }

    .gender-pill-box {
        display: flex;
        background: #f8f8f8;
        padding: 5px;
        border-radius: 15px;
    }

    .gender-pill-item {
        flex: 1;
        text-align: center;
    }

    .gender-pill-item input {
        display: none;
    }

    .gender-pill-item label {
        display: block;
        padding: 10px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 500;
        color: #777;
        transition: all 0.3s;
    }

    .gender-pill-item input:checked+label {
        background: #fff;
        color: var(--accent);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    @media (max-width: 768px) {
        .luxury-profile-card {
            padding: 40px 20px;
        }

        .profile-nav-tabs {
            gap: 15px;
        }

        .profile-nav-link {
            font-size: 0.7rem;
        }
    }
</style>

<div class="container py-4">
    <div class="profile-container">

        <?php if ($successMsg): ?>
            <div class="alert alert-success border-0 text-center mb-5"
                style="border-radius: 50px; background: #e8f7f0; color: #10b981; font-weight: 500; box-shadow: 0 5px 15px rgba(16, 185, 129, 0.1);">
                <i class="fa fa-check-circle me-2"></i> <?= $successMsg ?>
            </div>
        <?php endif; ?>

        <div class="luxury-profile-card">
            <div class="profile-header-center">
                <div class="avatar-center-wrapper">
                    <img src="asset/images/<?= htmlspecialchars($result['avatar'] ?: 'default_avatar.png') ?>"
                        id="displayAvatar" class="avatar-main">
                    <label for="fileUpload" class="avatar-edit-icon">
                        <i class="fa fa-pencil-alt"></i>
                    </label>
                </div>
                <h2
                    style="font-family: 'Playfair Display', serif; font-weight: 700; font-size: 2.2rem; margin-bottom: 5px;">
                    <?= htmlspecialchars($result['name']) ?></h2>
                <p
                    style="color: var(--accent); font-size: 0.8rem; letter-spacing: 4px; text-transform: uppercase; font-weight: 500; margin-bottom: 25px;">
                    Thành viên Elite</p>

                <div class="mb-5">
                    <a href="index.php?page=orders" class="btn-elegant"
                        style="text-decoration: none; padding: 12px 35px; display: inline-block;">
                        <i class="fa fa-history me-2"></i> Lịch sử mua hàng
                    </a>
                </div>
            </div>

            <nav class="profile-nav-tabs">
                <a href="<?= PATH ?>page=account" class="profile-nav-link active">Hồ sơ</a>
                <a href="<?= PATH ?>page=orders" class="profile-nav-link">Đơn hàng</a>
            </nav>

            <form action="index.php?page=account" method="post" enctype="multipart/form-data">
                <input type="file" name="avatar" id="fileUpload" style="display: none;" onchange="preview(this)">

                <h4 class="form-section-title">Thông tin định danh</h4>
                <div class="row">
                    <div class="col-md-7">
                        <div class="input-group-luxury">
                            <label class="label-luxury">Họ và tên đầy đủ</label>
                            <input type="text" name="name" class="control-luxury"
                                value="<?= htmlspecialchars($result['name']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group-luxury">
                            <label class="label-luxury">Giới tính</label>
                            <div class="gender-pill-box">
                                <div class="gender-pill-item">
                                    <input type="radio" name="gender" id="g-nam" value="1" <?= ($result['gender'] == 1) ? 'checked' : '' ?>>
                                    <label for="g-nam">Nam</label>
                                </div>
                                <div class="gender-pill-item">
                                    <input type="radio" name="gender" id="g-nu" value="0" <?= ($result['gender'] == 0) ? 'checked' : '' ?>>
                                    <label for="g-nu">Nữ</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="form-section-title">Liên lạc & Địa chỉ</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group-luxury">
                            <label class="label-luxury">Số điện thoại</label>
                            <input type="text" name="phone" class="control-luxury"
                                value="<?= htmlspecialchars($result['phone']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group-luxury">
                            <label class="label-luxury">Hòm thư điện tử</label>
                            <input type="email" name="email" class="control-luxury"
                                value="<?= htmlspecialchars($result['email']) ?>" required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group-luxury">
                            <label class="label-luxury">Địa chỉ thường trú</label>
                            <input type="text" name="address" class="control-luxury"
                                value="<?= htmlspecialchars($result['address'] ?: 'Chưa cập nhật') ?>" required>
                        </div>
                    </div>
                </div>

                <h4 class="form-section-title">Bảo mật tài khoản</h4>
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="input-group-luxury" style="margin-bottom: 20px;">
                            <label class="label-luxury">Tên đăng nhập</label>
                            <input type="text" name="username" class="control-luxury"
                                value="<?= htmlspecialchars($result['username']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group-luxury">
                            <label class="label-luxury">Mật khẩu hiện tại</label>
                            <input type="password" name="password-old" class="control-luxury"
                                placeholder="Xác nhận mật khẩu">
                            <?php if ($passOldErr): ?><p class="text-danger small mt-2" style="font-size:
                            0.75rem;"><?= $passOldErr ?></p><?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group-luxury">
                            <label class="label-luxury">Mật khẩu mới</label>
                            <input type="password" name="password-new" class="control-luxury"
                                placeholder="Để trống nếu không đổi">
                            <?php if ($passNewErr): ?><p class="text-danger small mt-2" style="font-size:
                            0.75rem;"><?= $passNewErr ?></p><?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-5"
                    style="display: flex; flex-direction: column; align-items: center; gap: 20px;">
                    <button type="submit" name="submit" class="btn-elegant"
                        style="width: 320px; padding: 18px 0; font-size: 0.95rem; letter-spacing: 2px;">
                        XÁC NHẬN CẬP NHẬT
                    </button>
                    <a href="<?= PATH ?>page=logout" class="btn-outline"
                        style="text-decoration: none; width: 320px; padding: 16px 0; font-size: 0.9rem; color: #5A4A42; display: flex; align-items: center; justify-content: center; letter-spacing: 1px; font-weight: 600;">
                        <i class="fa fa-sign-out-alt me-2"></i> ĐĂNG XUẤT TÀI KHOẢN
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Live preview avatar khi chọn file
function preview(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('displayAvatar').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>