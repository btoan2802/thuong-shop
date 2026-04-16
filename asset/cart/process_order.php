<?php
ob_start();
session_start();
require("../lib/coreFunction.php");
$f=new CoreFunction();

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $name = $_POST['fullName'];
    $add = $_POST['address'];
    $phone = $_POST['phoneNumber'];
    // Tạo đơn hàng trước
    $orders = [
        'user_Id'=>$_SESSION['userId'],
        'receiverName' => $name,
        'receiverEmail'=> $email,
        'receiverPhone'=> $phone,
        'receiverAddress'=> $add,
        'order_date'=> date("Y-m-d"),
    ];
    $order_id=$f->addRecordReturnId("orders", $orders);
    // Tạo chi tiết đơn hàng và TRỪ TỒN KHO
    if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])){
        foreach($_SESSION['cart'] as $i => $pid){
            $qty_ordered = $_SESSION['amount'][$i];
            $size_ordered = $_SESSION['size'][$i];
            
            // 1. Lấy thông tin giá và tồn kho hiện tại
            $sql_p = "SELECT * FROM product WHERE id = $pid";
            $p = $f->getOne($sql_p);
            
            if(!$p) continue;
            
            $price = ($p['is_on_sale'] == 1 && $p['sale_price'] !== null) ? $p['sale_price'] : $p['price'];
            $total_p_price = $price * $qty_ordered;
            
            // 2. Tạo chi tiết đơn hàng
            $order_detail = [
                'order_id' => $order_id,
                'product_id' => $pid,
                'quantity' => $qty_ordered,
                'size' => $size_ordered,
                'price' => $price,
                'total_price' => $total_p_price,
            ];
            $f->addRecord("order_detail", $order_detail);
            
            // 3. TRỪ TỒN KHO THEO SIZE (Có kiểm tra tồn kho thực tế tại thời điểm thanh toán)
            $sizes_str = $p['sizes'];
            $sizes_arr = explode(",", $sizes_str);
            $new_sizes_list = [];
            $final_qty_processed = 0; 

            foreach($sizes_arr as $s_item){
                $s_item = trim($s_item);
                if($s_item == "") continue;
                
                $parts = explode(":", $s_item);
                $sName = trim($parts[0]);
                $sQty = isset($parts[1]) ? intval($parts[1]) : 10;
                
                if($sName == $size_ordered){
                    // Kiểm tra nếu khách mua nhiều hơn tồn kho hiện tại (do người khác mua trước)
                    $qty_to_deduct = min($qty_ordered, $sQty);
                    $sQty -= $qty_to_deduct;
                    $final_qty_processed = $qty_to_deduct;
                }
                
                $new_sizes_list[] = $sName . ":" . $sQty;
            }

            // Nếu không còn hàng để trừ, bỏ qua record này hoặc xử lý báo lỗi
            if($final_qty_processed <= 0) continue;

            // Cập nhật lại quantity thực tế vào chi tiết đơn hàng nếu nó bị thay đổi
            if($final_qty_processed != $qty_ordered) {
                $total_p_price = $price * $final_qty_processed;
                $f->setQuery("UPDATE order_detail SET quantity = $final_qty_processed, total_price = $total_p_price WHERE order_id = $order_id AND product_id = $pid AND size = '$size_ordered'");
            }

            $new_sizes_str = implode(",", $new_sizes_list);
            
            // 4. Cập nhật lại chuỗi sizes vào DB
            $sql_update = "UPDATE product SET sizes = '$new_sizes_str' WHERE id = $pid";
            $f->setQuery($sql_update);
        }
        
        // Xóa giỏ hàng sau khi đặt thành công
        unset($_SESSION['cart']);
        unset($_SESSION['amount']);
        unset($_SESSION['size']);
        $_SESSION['number_of_item'] = 0;
    }
    
    echo "<center><h1 style='margin-top:100px; color:#c9a96e; font-family:Spectral, serif;'>Đặt hàng thành công!</h1></center><br>";
    echo "<center><p style='font-size:1.2rem; color:#666;'>Cảm ơn quý khách đã tin tưởng Junia Fashion. Đơn hàng của bạn đang được xử lý.</p></center><br>";
    echo "<center><a href='../index.php' style='display:inline-block; padding:15px 30px; background:#111; color:#fff; text-decoration:none; border-radius:50px; font-weight:600;'>Quay về Trang chủ</a></center>";
}

// header("Location: index.php");
?>
