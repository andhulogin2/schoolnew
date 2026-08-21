<?php
$db = mysqli_connect('localhost', 'root', '', 'db_school');
if (!$db) die("Connect failed: " . mysqli_connect_error());

echo "=== Distinct class_ids in tbl_students ===\n";
$res = mysqli_query($db, "SELECT DISTINCT class_id FROM tbl_students");
$st_classes = [];
while ($r = mysqli_fetch_row($res)) $st_classes[] = $r[0];
echo implode(', ', $st_classes) . "\n";

echo "=== Distinct class_ids in tbl_subjects ===\n";
$res = mysqli_query($db, "SELECT DISTINCT class_id FROM tbl_subjects");
$sub_classes = [];
while ($r = mysqli_fetch_row($res)) $sub_classes[] = $r[0];
echo implode(', ', $sub_classes) . "\n";

echo "=== Class IDs currently in tbl_classes ===\n";
$res = mysqli_query($db, "SELECT class_id, class_name FROM tbl_classes");
while ($r = mysqli_fetch_assoc($res)) {
    echo "ID {$r['class_id']}: {$r['class_name']}\n";
}
