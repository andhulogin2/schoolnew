<?php
$db = mysqli_connect('localhost', 'root', '', 'db_school');
if (!$db) die("Connect failed: " . mysqli_connect_error());

echo "=== Students class_id distribution ===\n";
$res = mysqli_query($db, "SELECT class_id, status, is_deleted, COUNT(*) as cnt FROM tbl_students GROUP BY class_id, status, is_deleted");
while ($r = mysqli_fetch_assoc($res)) {
    print_r($r);
}

echo "\n=== Sections class_id distribution ===\n";
$res = mysqli_query($db, "SELECT class_id, section_name, status, is_deleted FROM tbl_sections");
while ($r = mysqli_fetch_assoc($res)) {
    print_r($r);
}

echo "\n=== Subjects class_id distribution ===\n";
$res = mysqli_query($db, "SELECT class_id, subject_name, status, is_deleted FROM tbl_subjects");
while ($r = mysqli_fetch_assoc($res)) {
    print_r($r);
}
