<?php
$mysqli = new mysqli('localhost', 'root', '', 'db_school');
$res = $mysqli->query("SELECT * FROM tbl_roles");
echo "--- tbl_roles ---\n";
while ($row = $res->fetch_assoc()) echo json_encode($row) . "\n";

$res = $mysqli->query("SELECT user_id, name, email, role_id, status FROM tbl_users");
echo "\n--- tbl_users ---\n";
while ($row = $res->fetch_assoc()) echo json_encode($row) . "\n";
