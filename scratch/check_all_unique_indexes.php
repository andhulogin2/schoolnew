<?php
$conn = new mysqli('localhost', 'root', '', 'db_school');
$tables = $conn->query("SHOW TABLES");
while ($row = $tables->fetch_row()) {
    $t = $row[0];
    $idx = $conn->query("SHOW INDEX FROM `$t` WHERE Non_unique = 0 AND Key_name != 'PRIMARY'");
    while ($i = $idx->fetch_assoc()) {
        echo "Table: $t | Unique Index: {$i['Key_name']} | Column: {$i['Column_name']}\n";
    }
}
