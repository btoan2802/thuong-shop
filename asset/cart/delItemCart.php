<?php
if(isset($_GET['index'])) {
    $index = $_GET['index'];
    if(isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1);
        array_splice($_SESSION['amount'], $index, 1);
        array_splice($_SESSION['size'], $index, 1);
        $_SESSION["number_of_item"]--;
    }
}
header("Location: index.php?page=cart");
?>