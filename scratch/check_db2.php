<?php
$mysqli = new mysqli('localhost', 'root', '', 'db_school');

echo "\n--- tbl_students sample & parent columns ---\n";
$res = $mysqli->query("DESCRIBE tbl_students");
while ($row = $res->fetch_assoc()) {
    echo "{$row['Field']} | {$row['Type']}\n";
}

echo "\n--- tbl_periods sample data ---\n";
$res = $mysqli->query("SELECT * FROM tbl_periods");
while ($row = $res->fetch_assoc()) {
    echo json_encode($row) . "\n";
}

echo "\n--- tbl_attendance sample count ---\n";
$res = $mysqli->query("SELECT attendance_status, count(*) FROM tbl_attendance GROUP BY attendance_status");
while ($row = $res->fetch_assoc()) {
    echo json_encode($row) . "\n";
}
