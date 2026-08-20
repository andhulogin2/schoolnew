<?php
$m = new mysqli('localhost', 'root', '', 'db_school');

$res = $m->query("SELECT COUNT(*) FROM tbl_students WHERE status = 1");
echo "Active Students: " . $res->fetch_row()[0] . "\n";

$res = $m->query("SELECT COUNT(*) FROM tbl_classes WHERE status = 1");
echo "Active Classes: " . $res->fetch_row()[0] . "\n";

$res = $m->query("SELECT COUNT(*) FROM tbl_subjects WHERE status = 1");
echo "Active Subjects: " . $res->fetch_row()[0] . "\n";

$res = $m->query("SELECT COUNT(*) FROM tbl_staff WHERE status = 1");
echo "Active Staff: " . $res->fetch_row()[0] . "\n";

$res = $m->query("SELECT * FROM tbl_academic_years");
echo "\nAcademic Years:\n";
while($r = $res->fetch_assoc()) echo json_encode($r) . "\n";
