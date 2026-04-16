<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userId'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to add to cart']);
    exit();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
    $_SESSION['amount'] = array();
    $_SESSION['size'] = array();
    $_SESSION['number_of_item'] = 0;
}

require_once '../lib/coreFunction.php';
$f = new CoreFunction();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$size = isset($_GET['size']) ? $_GET['size'] : 'One Size';
$qty = isset($_GET['qty']) ? intval($_GET['qty']) : 1;

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không hợp lệ']);
    exit();
}

// Check stock logic
$sql_stock = 'SELECT sizes FROM product WHERE id = ' . $id;
$p_stock = $f->getOne($sql_stock);
$max_stock = 0;
if($p_stock) {
    $s_arr = explode(",", $p_stock['sizes'] ?? '');
    if(empty($p_stock['sizes'])) {
         $max_stock = 10; // Default if empty
    } else {
        // Nếu không gửi size lên hoặc size gửi lên không tồn tại, lấy size đầu tiên
        $first_size = trim(explode(":", $s_arr[0])[0]);
        $found_size = false;

        foreach($s_arr as $s_item) {
            $parts = explode(":", trim($s_item));
            $sName = trim($parts[0]);
            if($sName == $size) {
                $max_stock = isset($parts[1]) ? intval($parts[1]) : 10;
                $found_size = true;
                break;
            }
        }

        if(!$found_size) {
            $size = $first_size; // Chuyển sang size đầu tiên của sản phẩm
             foreach($s_arr as $s_item) {
                $parts = explode(":", trim($s_item));
                if(trim($parts[0]) == $size) {
                    $max_stock = isset($parts[1]) ? intval($parts[1]) : 10;
                    break;
                }
            }
        }
    }
}

// Find item in cart
$found_index = -1;
for($i = 0; $i < count($_SESSION['cart']); $i++) {
    if($_SESSION['cart'][$i] == $id && $_SESSION['size'][$i] == $size) {
        $found_index = $i;
        break;
    }
}

if ($found_index === -1) {
    $final_qty = min($qty, $max_stock);
    if($final_qty > 0) {
        array_push($_SESSION['cart'], $id);
        array_push($_SESSION['amount'], $final_qty);
        array_push($_SESSION['size'], $size);
        
        // Recalculate total items count in cart (sum of qty)
        $totalItems = 0;
        foreach($_SESSION['amount'] as $a) $totalItems += $a;
        $_SESSION['number_of_item'] = $totalItems;
        
        echo json_encode(['status' => 'success', 'count' => $_SESSION['number_of_item']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Sản phẩm Hiện đang hết hàng']);
    }
} else {
    $new_total = $_SESSION['amount'][$found_index] + $qty;
    $_SESSION['amount'][$found_index] = min($new_total, $max_stock);
    
    $totalItems = 0;
    foreach($_SESSION['amount'] as $a) $totalItems += $a;
    $_SESSION['number_of_item'] = $totalItems;
    
    echo json_encode(['status' => 'success', 'count' => $_SESSION['number_of_item']]);
}
?>
