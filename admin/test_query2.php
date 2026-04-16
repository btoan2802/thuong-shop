<?php
require_once '../lib/coreFunction.php';
$f = new CoreFunction();

$q1 = "SELECT * FROM orders ORDER BY Id DESC LIMIT 10";
$res1 = $f->getAll($q1);
print_r($res1);
?>
