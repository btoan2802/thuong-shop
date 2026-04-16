<?php
if (!isset($_SESSION['userId'])) {
    header("Location: index.php?page=login");
    exit();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
    $_SESSION['amount'] = array();
    $_SESSION['size'] = array();
    $_SESSION['number_of_item'] = 0;
}

$id = $_GET['id'];
$size = isset($_GET['size']) ? $_GET['size'] : 'Standard';
$qty = isset($_GET['qty']) ? intval($_GET['qty']) : 1;

// GET CURRENT ACTUAL STOCK FROM DB
$sql_stock = 'SELECT sizes FROM product WHERE id = ' . $id;
$p_stock = $f->getOne($sql_stock);
$max_stock = 0;
if($p_stock) {
    $s_arr = explode(",", $p_stock['sizes']);
    foreach($s_arr as $s_item) {
        $parts = explode(":", trim($s_item));
        $sName = trim($parts[0]);
        if($sName == $size) {
            $max_stock = isset($parts[1]) ? intval($parts[1]) : 10; // Default to 10 for legacy
            break;
        }
    }
}

// Find item in cart with same ID AND same size
$found_index = -1;
if(isset($_SESSION['cart'])){
    for($i = 0; $i < count($_SESSION['cart']); $i++) {
        if($_SESSION['cart'][$i] == $id && $_SESSION['size'][$i] == $size) {
            $found_index = $i;
            break;
        }
    }
}

if ($found_index === -1) {
    // New item: respect stock limit
    $final_qty = min($qty, $max_stock);
    if($final_qty > 0) {
        array_push($_SESSION['cart'], $id);
        array_push($_SESSION['amount'], $final_qty);
        array_push($_SESSION['size'], $size);
        $_SESSION['number_of_item']++;
    }
} else {
    // Existing item: respect stock limit for TOTAL quantity
    $new_total = $_SESSION['amount'][$found_index] + $qty;
    $_SESSION['amount'][$found_index] = min($new_total, $max_stock);
}

header("Location: index.php?page=cart");
?>
