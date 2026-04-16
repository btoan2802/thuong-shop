<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if(isset($_GET['index']) && isset($_GET['size'])) {
        $index = (int)$_GET['index'];
        $size = $_GET['size'];
        
        if(isset($_SESSION['size'][$index])) {
            $_SESSION['size'][$index] = $size;
            
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid index']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    }
?>
