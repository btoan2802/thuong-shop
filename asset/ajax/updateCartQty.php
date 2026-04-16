<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if(isset($_GET['index']) && isset($_GET['qty'])) {
        $index = (int)$_GET['index'];
        $qty = (int)$_GET['qty'];
        
        if($qty > 0 && isset($_SESSION['amount'][$index])) {
            $_SESSION['amount'][$index] = $qty;
            
            // Recalculate number_of_item
            $total_items = 0;
            foreach($_SESSION['amount'] as $a) {
                $total_items += $a;
            }
            $_SESSION['number_of_item'] = $total_items;
            
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid quantity or index']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    }
?>
