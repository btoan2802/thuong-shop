<?php
    require("../lib/coreFunction.php");
    $f = new CoreFunction();
    $orderId = intval($_GET['orderId']);

    // 1. Lấy trạng thái của đơn hàng (không select total_amount và payment_method vì db không có)
    $orderInfo = $f->getOne("SELECT status FROM orders WHERE Id = $orderId");
    
    // 2. Lấy danh sách sản phẩm
    $sql = "SELECT p.id as product_id, p.name as product_name, od.size, od.price, od.quantity, od.total_price 
            FROM order_detail od 
            INNER JOIN product p ON od.product_id = p.id 
            WHERE od.order_id = $orderId";
    $items = $f->getAll($sql);

    // Tính lại tổng tiền
    $totalAmount = 0;
    foreach($items as $idx => $d) {
        $lineTotal = ($d['price'] * $d['quantity']) > 0
            ? ($d['price'] * $d['quantity'])
            : $d['total_price'];
        $totalAmount += $lineTotal;
        $items[$idx]['total_price'] = $lineTotal; // chuẩn hoá dữ liệu trả về nếu cần thiết
    }

    // Trả về định dạng JSON
    echo json_encode([
        'info' => [
            'total_amount' => number_format($totalAmount),
            'payment_method' => 'Thanh toán khi nhận hàng (COD)', // Không có bảng này thì set COD
            'status' => $orderInfo ? $orderInfo['status'] : 'Unknown'
        ],
        'items' => $items
    ]);
?>