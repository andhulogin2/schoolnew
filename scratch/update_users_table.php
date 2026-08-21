<?php
$conn = new mysqli('localhost', 'root', '', 'db_school');
if ($conn->connect_error) {
    die("Connect failed: " . $conn->connect_error . "\n");
}

// 1. Normalize is_deleted if NULL
$conn->query("UPDATE tbl_users SET is_deleted = 'n' WHERE is_deleted IS NULL OR is_deleted = ''");

// 2. Check indexes
$indexes = [];
$res = $conn->query("SHOW INDEX FROM tbl_users");
while ($row = $res->fetch_assoc()) {
    $indexes[$row['Key_name']][] = $row['Column_name'];
}

echo "Current indexes:\n";
print_r($indexes);

// If uk_user_email exists and is UNIQUE, drop it and create regular index idx_user_email
if (isset($indexes['uk_user_email'])) {
    $conn->query("ALTER TABLE tbl_users DROP INDEX uk_user_email");
    echo "Dropped uk_user_email\n";
}

if (!isset($indexes['idx_user_email'])) {
    $conn->query("ALTER TABLE tbl_users ADD INDEX idx_user_email (email)");
    echo "Added idx_user_email\n";
}

if (!isset($indexes['idx_user_username'])) {
    $conn->query("ALTER TABLE tbl_users ADD INDEX idx_user_username (username)");
    echo "Added idx_user_username\n";
}

echo "Index update complete.\n";
