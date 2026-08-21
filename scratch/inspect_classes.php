<?php
$db = mysqli_connect('localhost', 'root', '', 'db_school');
if (!$db) die("Connect failed: " . mysqli_connect_error());

echo "=== 1. Structure of tbl_classes ===\n";
$res = mysqli_query($db, "DESCRIBE tbl_classes");
while ($r = mysqli_fetch_assoc($res)) {
    echo "{$r['Field']} | {$r['Type']} | Null={$r['Null']} | Default={$r['Default']}\n";
}

echo "\n=== 2. All rows in tbl_classes ===\n";
$res = mysqli_query($db, "SELECT * FROM tbl_classes");
while ($r = mysqli_fetch_assoc($res)) {
    print_r($r);
}

echo "\n=== 3. Active academic year in tbl_academic_years ===\n";
$res = mysqli_query($db, "SELECT * FROM tbl_academic_years");
while ($r = mysqli_fetch_assoc($res)) {
    print_r($r);
}
