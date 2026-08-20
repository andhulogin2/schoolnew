<?php
$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("Connect Error: " . $mysqli->connect_error . "\n");
}
echo "Connected successfully to db_school!\n";
$res = $mysqli->query("SHOW TABLES");
$tables = [];
while ($row = $res->fetch_row()) {
    $tables[] = $row[0];
}
echo "Tables found (" . count($tables) . "): " . implode(', ', $tables) . "\n";

echo "\n--- tbl_attendance columns ---\n";
$res = $mysqli->query("DESCRIBE tbl_attendance");
while ($row = $res->fetch_assoc()) {
    echo "{$row['Field']} | {$row['Type']} | {$row['Null']} | {$row['Key']} | {$row['Default']}\n";
}

echo "\n--- tbl_periods columns ---\n";
$res = $mysqli->query("DESCRIBE tbl_periods");
while ($row = $res->fetch_assoc()) {
    echo "{$row['Field']} | {$row['Type']} | {$row['Null']} | {$row['Key']} | {$row['Default']}\n";
}
