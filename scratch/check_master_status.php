<?php
$db = mysqli_connect('localhost', 'root', '', 'db_school');
if (!$db) die("Connect failed: " . mysqli_connect_error());

$tables = ['tbl_classes', 'tbl_sections', 'tbl_subjects', 'tbl_academic_years', 'tbl_departments', 'tbl_designations', 'tbl_staff', 'tbl_students'];

foreach ($tables as $t) {
    echo "=== Table $t ===\n";
    $res = mysqli_query($db, "SELECT COUNT(*) as total, SUM(CASE WHEN status=1 THEN 1 ELSE 0 END) as s1, SUM(CASE WHEN status=0 THEN 1 ELSE 0 END) as s0, SUM(CASE WHEN is_deleted='y' THEN 1 ELSE 0 END) as dy, SUM(CASE WHEN is_deleted='n' THEN 1 ELSE 0 END) as dn FROM `$t`");
    $r = mysqli_fetch_assoc($res);
    print_r($r);
}
