<?php
$conn = new mysqli('localhost', 'root', '', 'db_school');
$conn->query("DELETE FROM tbl_users WHERE username LIKE 'newuser%' OR username LIKE 'testuser%'");
echo "Cleaned up test accounts. Remaining users:\n";
$res = $conn->query("SELECT user_id, username, email, is_deleted, status FROM tbl_users");
while ($r = $res->fetch_assoc()) {
    echo "ID: {$r['user_id']} | Username: {$r['username']} | Email: {$r['email']} | Status: {$r['status']} | is_deleted: {$r['is_deleted']}\n";
}
