<?php
$db = mysqli_connect('localhost', 'root', '', 'db_school');
if (!$db) die("Connect failed: " . mysqli_connect_error());

// Update tbl_classes to status = 1 where is_deleted = 'n'
mysqli_query($db, "UPDATE tbl_classes SET status = 1 WHERE is_deleted = 'n'");
echo "Updated rows: " . mysqli_affected_rows($db) . "\n";

$res = mysqli_query($db, "SELECT * FROM tbl_classes");
while ($r = mysqli_fetch_assoc($res)) {
    echo "Class: {$r['class_name']} ({$r['class_code']}) | Year: {$r['academic_year_id']} | Status: {$r['status']} | is_deleted: {$r['is_deleted']}\n";
}
