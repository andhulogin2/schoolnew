<?php
$m = new mysqli('localhost', 'root', '', 'db_school');
$res = $m->query("SHOW TABLES LIKE 'tbl_exam%'");
echo "Exam tables found:\n";
while($r = $res->fetch_row()) {
    echo "- " . $r[0] . "\n";
}

$res = $m->query("DESCRIBE tbl_exams");
echo "\ntbl_exams columns:\n";
while($r = $res->fetch_assoc()) {
    echo "{$r['Field']} | {$r['Type']} | {$r['Null']} | {$r['Key']} | {$r['Default']}\n";
}
