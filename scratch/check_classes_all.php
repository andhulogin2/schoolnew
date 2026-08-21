<?php
$db = mysqli_connect('localhost', 'root', '', 'db_school');
if (!$db) die("Connect failed: " . mysqli_connect_error());

$res = mysqli_query($db, "SELECT * FROM tbl_classes");
echo "Total rows in tbl_classes: " . mysqli_num_rows($res) . "\n";
while ($r = mysqli_fetch_assoc($res)) {
    echo "ID: {$r['class_id']} | Name: {$r['class_name']} | Code: {$r['class_code']} | Year: {$r['academic_year_id']} | Status: {$r['status']} | is_deleted: {$r['is_deleted']}\n";
}
