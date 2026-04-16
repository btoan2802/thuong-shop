<?php
require_once '../lib/coreFunction.php';
$f = new CoreFunction();

$f->setQuery("UPDATE orders SET status = 'Completed' WHERE Id = 19");
$revenueToday = $f->getOne("SELECT SUM(d.price * d.quantity) as total FROM orders o JOIN order_detail d ON o.Id = d.order_id WHERE o.status = 'Completed' AND DATE(o.order_date) = CURRENT_DATE()");
print_r($revenueToday);
?>
