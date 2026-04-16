<?php
require("lib/coreFunction.php");
$f = new CoreFunction();

// 1. Check if 'size' column exists in 'order_detail'
$res = $f->getAll("SHOW COLUMNS FROM order_detail LIKE 'size'");
if(count($res) == 0) {
    echo "Adding 'size' column to 'order_detail'...\n";
    $f->setQuery("ALTER TABLE order_detail ADD COLUMN size VARCHAR(50) AFTER quantity");
}

// 2. Check if 'total_amount' column exists in 'orders' (just in case)
$res2 = $f->getAll("SHOW COLUMNS FROM orders LIKE 'total_amount'");
if(count($res2) == 0) {
    echo "Adding 'total_amount' column to 'orders'...\n";
    $f->setQuery("ALTER TABLE orders ADD COLUMN total_amount INT DEFAULT 0 AFTER receiverAddress");
}

echo "Database updated successfully.";
?>
