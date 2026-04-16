<?php
class Database {
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $dbname = 'nguyenthihoaithuong';
    public $conn;

    function __construct() {
        $this->conn = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->dbname
        );

        if ($this->conn->connect_error) {
            die("Kết nối Database thất bại: " . $this->conn->connect_error . ". Vui lòng kiểm tra XAMPP và đảm bảo đã bật MySQL!");
        }

        // Set charset để hiển thị tiếng Việt đúng
        $this->conn->set_charset("utf8mb4");
        $this->conn->query("SET NAMES utf8mb4");
    }
}
?>