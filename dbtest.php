<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli("localhost", "root", "", "nguyenthihoaithuong");
    $conn->set_charset("utf8mb4");
    echo "DB OK";
} catch (Throwable $e) {
    echo "LOI DB: " . $e->getMessage();
}
?>