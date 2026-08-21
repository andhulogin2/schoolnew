<?php
$conn = new mysqli('localhost', 'root', '', 'db_school');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

echo "=== TBL_USERS STRUCTURE ===\n";
$res = $conn->query("DESCRIBE tbl_users");
while ($row = $res->fetch_assoc()) {
    echo "Field: {$row['Field']} | Type: {$row['Type']} | Null: {$row['Null']} | Key: {$row['Key']} | Default: " . var_export($row['Default'], true) . "\n";
}

echo "\n=== TBL_USERS INDEXES ===\n";
$res = $conn->query("SHOW INDEX FROM tbl_users");
while ($row = $res->fetch_assoc()) {
    echo "Index: {$row['Key_name']} | Column: {$row['Column_name']} | Non_unique: {$row['Non_unique']}\n";
}

echo "\n=== TBL_USERS ALL RECORDS ===\n";
$res = $conn->query("SELECT user_id, username, email, name, user_type, role_id, status, is_deleted FROM tbl_users");
$count = 0;
while ($row = $res->fetch_assoc()) {
    $count++;
    echo "ID: {$row['user_id']} | User: [{$row['username']}] | Email: [{$row['email']}] | Name: [{$row['name']}] | Status: {$row['status']} | is_deleted: [{$row['is_deleted']}]\n";
}
echo "Total records in tbl_users: $count\n";

echo "\n=== CHECK FOR EMPTY STRINGS IN TBL_USERS ===\n";
$res = $conn->query("SELECT user_id, username, email, is_deleted FROM tbl_users WHERE username = '' OR email = '' OR username IS NULL OR email IS NULL");
while ($row = $res->fetch_assoc()) {
    echo "Found empty/null: ID {$row['user_id']} | Username: [{$row['username']}] | Email: [{$row['email']}] | is_deleted: [{$row['is_deleted']}]\n";
}

echo "\n=== CHECK OTHER USER/AUTH/STAFF/STUDENT TABLES ===\n";
$res = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $res->fetch_row()) {
    $tables[] = $row[0];
}
echo "Tables count: " . count($tables) . "\n";
foreach ($tables as $t) {
    if (stripos($t, 'user') !== false || stripos($t, 'staff') !== false || stripos($t, 'student') !== false || stripos($t, 'auth') !== false || stripos($t, 'login') !== false || stripos($t, 'parent') !== false) {
        $countRes = $conn->query("SELECT COUNT(*) as c FROM `$t`");
        $cRow = $countRes->fetch_assoc();
        echo "Table: $t (Rows: {$cRow['c']})\n";
    }
}
