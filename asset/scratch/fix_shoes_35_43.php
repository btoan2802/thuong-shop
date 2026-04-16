<?php
require("lib/db.php");
$db = new Database();
$newSizes = '35:10,36:10,37:10,38:10,39:10,40:10,41:10,42:10,43:10';
$sql = "UPDATE product SET sizes = '$newSizes' WHERE cat_id = 5";
if($db->conn->query($sql)) {
    echo "Database updated: All shoes set to sizes 35-43 with 10 units each.\n";
} else {
    echo "Error: " . $db->conn->error . "\n";
}
?>
