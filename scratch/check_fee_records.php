<?php
$m = new mysqli('localhost', 'root', '', 'db_school');
$res = $m->query("SELECT * FROM tbl_fee_heads");
echo "tbl_fee_heads:\n";
while($r = $res->fetch_assoc()) echo json_encode($r) . "\n";

$res = $m->query("SELECT * FROM tbl_fee_structures");
echo "\ntbl_fee_structures:\n";
while($r = $res->fetch_assoc()) echo json_encode($r) . "\n";

$res = $m->query("SELECT * FROM tbl_student_fees LIMIT 5");
echo "\ntbl_student_fees (sample):\n";
while($r = $res->fetch_assoc()) echo json_encode($r) . "\n";

$res = $m->query("SELECT * FROM tbl_fee_payments LIMIT 5");
echo "\ntbl_fee_payments (sample):\n";
while($r = $res->fetch_assoc()) echo json_encode($r) . "\n";
