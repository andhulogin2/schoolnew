<?php
$conn = new mysqli('localhost', 'root', '', 'db_school');
$res = $conn->query("DESCRIBE tbl_user_login_activity");
while ($row = $res->fetch_assoc()) {
    echo "Field: {$row['Field']} | Type: {$row['Type']}\n";
}
