<?php
class Upload {
    function doUpload($files) {
        // Tự động tìm thư mục asset/images dù deploy ở đâu
        $target_dir = dirname(__DIR__) . "/asset/images/";

        // Tạo thư mục nếu chưa có
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $imageFileType = strtolower(pathinfo($files["name"], PATHINFO_EXTENSION));
        $allowed = ["jpg", "jpeg", "png", "gif", "webp"];

        // Kiểm tra đúng là ảnh không
        $check = getimagesize($files["tmp_name"]);
        if ($check === false) {
            return false; // Không phải ảnh
        }

        // Kiểm tra định dạng
        if (!in_array($imageFileType, $allowed)) {
            return false;
        }

        // Giới hạn 5MB
        if ($files["size"] > 5 * 1024 * 1024) {
            return false;
        }

        // Tạo tên file unique để tránh trùng
        $newName = uniqid('avatar_', true) . '.' . $imageFileType;
        $target_file = $target_dir . $newName;

        if (move_uploaded_file($files["tmp_name"], $target_file)) {
            return $newName; // Trả về tên file mới để lưu DB
        }

        return false;
    }
}
?>