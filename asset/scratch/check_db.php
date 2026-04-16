<?php
require("lib/db.php");
$db = new Database();
$res = $db->conn->query("SELECT id, name, sizes FROM product WHERE cat_id = 5 LIMIT 3");
while($row = $res->fetch_assoc()) {
    echo $row['id'] . " | " . $row['name'] . " | Size: " . $row['sizes'] . "\n";
}
?>
