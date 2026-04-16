<?php
session_start();
require("../../lib/coreFunction.php");
$f = new CoreFunction();

if (!isset($_SESSION['admin'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $username = $_SESSION['admin'];
    $file = $_FILES['avatar'];

    // Validate file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['status' => 'error', 'message' => 'Định dạng file không hợp lệ (JPG, PNG, GIF, WEBP)']);
        exit();
    }

    $uploadDir = "../../asset/images/";
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = "avatar_" . $username . "_" . time() . "." . $extension;
    $uploadPath = $uploadDir . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // Update database
        $sql = "UPDATE user SET avatar = '$newFileName' WHERE username = '$username'";
        $f->setQuery($sql);

        echo json_encode([
            'status' => 'success', 
            'message' => 'Cập nhật avatar thành công!',
            'avatar_url' => '../asset/images/' . $newFileName
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi khi tải file lên hệ thống.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ.']);
}
?>
