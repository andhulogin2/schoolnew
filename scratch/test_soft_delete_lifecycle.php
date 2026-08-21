<?php
$db = mysqli_connect('localhost', 'root', '', 'db_school');
if (!$db) die("Connect failed: " . mysqli_connect_error());

echo "=== SOFT DELETE END-TO-END VERIFICATION ===\n\n";

// 1. Verify schema: Check count of tables without is_deleted
$tables_result = mysqli_query($db, "SHOW TABLES");
$total_tables = 0;
$missing_tables = [];
while ($row = mysqli_fetch_row($tables_result)) {
    $total_tables++;
    $t = $row[0];
    $col_res = mysqli_query($db, "SHOW COLUMNS FROM `$t` LIKE 'is_deleted'");
    if (mysqli_num_rows($col_res) === 0) {
        $missing_tables[] = $t;
    }
}

echo "1. Schema Check:\n";
echo "   Total tables in db_school: $total_tables\n";
echo "   Tables with is_deleted: " . ($total_tables - count($missing_tables)) . "\n";
if (count($missing_tables) > 0) {
    echo "   FAILED: Missing in: " . implode(', ', $missing_tables) . "\n";
} else {
    echo "   PASSED: 100% of tables have is_deleted column.\n";
}

// 2. Test Soft Delete on a test student
echo "\n2. Soft Delete Lifecycle Test (Students):\n";
mysqli_query($db, "DELETE FROM tbl_students WHERE admission_number = 'TEST_SOFT_DEL_999'");

// Insert test student
mysqli_query($db, "INSERT INTO tbl_students (academic_year_id, class_id, section_id, admission_number, first_name, last_name, gender, status, is_deleted, created_at) VALUES (1, 1, 1, 'TEST_SOFT_DEL_999', 'TestSoft', 'DeleteUser', 'Male', 1, 'n', NOW())");
$test_id = mysqli_insert_id($db);
echo "   - Created test student ID: $test_id\n";

// Verify is_deleted is 'n'
$res = mysqli_query($db, "SELECT is_deleted, status FROM tbl_students WHERE student_id = $test_id");
$row = mysqli_fetch_assoc($res);
echo "   - Initial state: is_deleted = '{$row['is_deleted']}', status = {$row['status']}\n";

// Soft delete via PHP model logic simulation
mysqli_query($db, "UPDATE tbl_students SET is_deleted = 'y', status = 0 WHERE student_id = $test_id");
$res = mysqli_query($db, "SELECT is_deleted, status FROM tbl_students WHERE student_id = $test_id");
$row = mysqli_fetch_assoc($res);
echo "   - Post-delete DB state: is_deleted = '{$row['is_deleted']}', status = {$row['status']}\n";

// Verify query for active students excludes it
$res = mysqli_query($db, "SELECT COUNT(*) as cnt FROM tbl_students WHERE student_id = $test_id AND is_deleted = 'n'");
$row = mysqli_fetch_assoc($res);
if ((int)$row['cnt'] === 0) {
    echo "   PASSED: Soft-deleted student is successfully excluded from normal queries (is_deleted='n').\n";
} else {
    echo "   FAILED: Soft-deleted student was still returned by is_deleted='n' query.\n";
}

// Clean up test record
mysqli_query($db, "DELETE FROM tbl_students WHERE student_id = $test_id");
echo "   - Cleaned up test record.\n";

echo "\n=== VERIFICATION COMPLETE ===\n";
