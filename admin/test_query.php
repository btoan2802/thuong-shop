<?php
require_once '../lib/coreFunction.php';
$f = new CoreFunction();

$q1 = "SELECT * FROM orders WHERE status = 'Completed'";
$res1 = $f->getAll($q1);
echo "Completed Orders: " . count($res1) . "\n";
print_r($res1);

$q2 = "SELECT d.* FROM order_detail d JOIN orders o ON o.Id = d.order_id WHERE o.status = 'Completed'";
$res2 = $f->getAll($q2);
echo "Order Details for Completed: \n";
print_r($res2);

$q3 = "SELECT SUM(d.price * d.quantity) as total FROM orders o JOIN order_detail d ON o.Id = d.order_id WHERE o.status = 'Completed'";
$res3 = $f->getOne($q3);
echo "Revenue All Time: ";
print_r($res3);

$revenueToday = $f->getOne("SELECT SUM(d.price * d.quantity) as total FROM orders o JOIN order_detail d ON o.Id = d.order_id WHERE o.status = 'Completed' AND DATE(o.order_date) = CURRENT_DATE()");
echo "Revenue Today: ";
print_r($revenueToday);

$q4 = "SELECT SUM(d.total_price) as t2 FROM orders o JOIN order_detail d ON o.Id = d.order_id WHERE o.status = 'Completed'";
$res4 = $f->getOne($q4);
var_dump($res4);

?>
