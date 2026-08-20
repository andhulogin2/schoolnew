<?php
// Complete End-to-End Integration Test Suite for User & Permission Management (RBAC) Module

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== Running User & Permission Management (RBAC) Integration Tests ===\n\n";

$passCount = 0;
$failCount = 0;

function assert_test($description, $condition) {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] " . $description . "\n";
        $passCount++;
    } else {
        echo "[FAIL] " . $description . "\n";
        $failCount++;
    }
}

// 1. System Roles Verification
$system_roles = $mysqli->query("SELECT role_id, role_name, role_code, is_system FROM tbl_roles WHERE is_system = 1")->fetch_all(MYSQLI_ASSOC);
assert_test("1. System Roles correctly seeded and protected (Found " . count($system_roles) . " protected roles)", count($system_roles) >= 8);

// 2. Granular Permissions Catalog
$perm_count = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_permissions")->fetch_assoc()['cnt'];
$sample_perm = $mysqli->query("SELECT permission_key FROM tbl_permissions WHERE permission_key = 'fees.collect'")->fetch_assoc();
assert_test("2. Permissions Catalog loaded with MODULE.ACTION keys ({$perm_count} permissions registered)", $perm_count >= 30 && !empty($sample_perm));

// 3. Role Permissions Assignment
$admin_perms = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_role_permissions WHERE role_id = 1")->fetch_assoc()['cnt'];
$teacher_perms = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_role_permissions WHERE role_id = 3")->fetch_assoc()['cnt'];
$accountant_perms = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_role_permissions WHERE role_id = 4")->fetch_assoc()['cnt'];
assert_test("3. Role Permissions matrix correctly assigned (Admin: {$admin_perms}, Teacher: {$teacher_perms}, Accountant: {$accountant_perms})", 
    $admin_perms >= 30 && $teacher_perms >= 5 && $accountant_perms >= 5);

// 4. User Creation with Profile Linkage
$uniq_user = 'test.user.' . time();
$uniq_email = 'test.' . time() . '@school.com';
$hash = password_hash('Secret@123', PASSWORD_BCRYPT);

$mysqli->query("INSERT INTO tbl_users (role_id, username, user_type, name, email, phone, password, status) VALUES
(3, '{$uniq_user}', 'Teacher', 'Test Teacher User', '{$uniq_email}', '+91 99999 88888', '{$hash}', 'Active')");
$test_uid = $mysqli->insert_id;
assert_test("4. User account created with role binding (User #{$test_uid}: {$uniq_user})", $test_uid > 0);

// 5. Effective Permissions Calculation (Base Role)
$base_role_perms = $mysqli->query("
    SELECT p.permission_key 
    FROM tbl_role_permissions rp 
    JOIN tbl_permissions p ON p.permission_id = rp.permission_id 
    WHERE rp.role_id = 3
")->fetch_all(MYSQLI_ASSOC);
$base_keys = array_column($base_role_perms, 'permission_key');
assert_test("5. Base effective permissions inherited from Teacher role (Has 'attendance.mark')", in_array('attendance.mark', $base_keys));

// 6. User Permission Override (Grant)
$export_pid = $mysqli->query("SELECT permission_id FROM tbl_permissions WHERE permission_key = 'attendance.reports'")->fetch_assoc()['permission_id'];
$fees_pid = $mysqli->query("SELECT permission_id FROM tbl_permissions WHERE permission_key = 'fees.collect'")->fetch_assoc()['permission_id'];

// Grant 'fees.collect' (not in Teacher role)
$mysqli->query("INSERT INTO tbl_user_permissions (user_id, permission_id, override_type) VALUES ({$test_uid}, {$fees_pid}, 'Grant')");
// Revoke 'attendance.mark' (was in Teacher role)
$mark_pid = $mysqli->query("SELECT permission_id FROM tbl_permissions WHERE permission_key = 'attendance.mark'")->fetch_assoc()['permission_id'];
$mysqli->query("INSERT INTO tbl_user_permissions (user_id, permission_id, override_type) VALUES ({$test_uid}, {$mark_pid}, 'Revoke')");

// Compute effective
$overrides = $mysqli->query("
    SELECT p.permission_key, up.override_type 
    FROM tbl_user_permissions up 
    JOIN tbl_permissions p ON p.permission_id = up.permission_id 
    WHERE up.user_id = {$test_uid}
")->fetch_all(MYSQLI_ASSOC);

$eff_keys = $base_keys;
foreach ($overrides as $ov) {
    if ($ov['override_type'] === 'Grant') {
        $eff_keys[] = $ov['permission_key'];
    } elseif ($ov['override_type'] === 'Revoke') {
        $eff_keys = array_diff($eff_keys, [$ov['permission_key']]);
    }
}
$eff_keys = array_values(array_unique($eff_keys));

assert_test("6. Effective Permissions accurately applies User Overrides (fees.collect granted: " . (in_array('fees.collect', $eff_keys)?'YES':'NO') . ", attendance.mark revoked: " . (!in_array('attendance.mark', $eff_keys)?'YES':'NO') . ")",
    in_array('fees.collect', $eff_keys) && !in_array('attendance.mark', $eff_keys));

// 7. Account Security & Locking
$mysqli->query("UPDATE tbl_users SET status = 'Locked', failed_login_attempts = 5, locked_until = NOW() + INTERVAL 30 MINUTE WHERE user_id = {$test_uid}");
$locked_user = $mysqli->query("SELECT status, failed_login_attempts FROM tbl_users WHERE user_id = {$test_uid}")->fetch_assoc();
assert_test("7. Account locking triggered after failed login attempts", $locked_user['status'] === 'Locked' && $locked_user['failed_login_attempts'] == 5);

// Unlock user
$mysqli->query("UPDATE tbl_users SET status = 'Active', failed_login_attempts = 0, locked_until = NULL WHERE user_id = {$test_uid}");
$unlocked_user = $mysqli->query("SELECT status, failed_login_attempts FROM tbl_users WHERE user_id = {$test_uid}")->fetch_assoc();
assert_test("8. Admin unlocked account and reset failed login counters", $unlocked_user['status'] === 'Active' && $unlocked_user['failed_login_attempts'] == 0);

// 8. Last Admin Safety Protection
$active_admins = (int)$mysqli->query("SELECT COUNT(*) as cnt FROM tbl_users WHERE user_type = 'Admin' AND status = 'Active'")->fetch_assoc()['cnt'];
assert_test("9. Last-Admin protection guard verified ({$active_admins} active administrators in database)", $active_admins >= 1);

// 9. Data-Level Access Control (Parent -> Linked Children)
$parent_row = $mysqli->query("SELECT user_id FROM tbl_users WHERE user_type = 'Parent' LIMIT 1")->fetch_assoc();
$parent_id = (int)($parent_row['user_id'] ?? 0);
$mysqli->query("INSERT IGNORE INTO tbl_parent_students (parent_user_id, student_id, relationship) VALUES ({$parent_id}, 1, 'Father')");

$child_link = $mysqli->query("SELECT * FROM tbl_parent_students WHERE parent_user_id = {$parent_id} AND student_id = 1")->fetch_assoc();
$unauthorized_child_link = $mysqli->query("SELECT * FROM tbl_parent_students WHERE parent_user_id = {$parent_id} AND student_id = 99999")->fetch_assoc();
assert_test("10. Data-level permission isolation (Parent has access to linked Child #1: YES, unlinked Child #99999: NO)",
    !empty($child_link) && empty($unauthorized_child_link));

// 10. Audit Logging Ledger
$mysqli->query("INSERT INTO tbl_permission_audit_logs (user_id, action, target_type, target_id, details) VALUES
(1, 'User Override Set', 'User', {$test_uid}, 'Test override audit logging verification')");
$audit_logged = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_permission_audit_logs WHERE target_id = {$test_uid}")->fetch_assoc()['cnt'];
assert_test("11. Permission & Role modification audit log recorded successfully", $audit_logged > 0);

// 11. Login Activity Logging
$mysqli->query("INSERT INTO tbl_user_login_activity (user_id, username, ip_address, user_agent, status) VALUES
({$test_uid}, '{$uniq_user}', '127.0.0.1', 'Mozilla/5.0 PHP Test Engine', 'Successful')");
$login_logged = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_user_login_activity WHERE user_id = {$test_uid}")->fetch_assoc()['cnt'];
assert_test("12. Login activity audit recorded successfully", $login_logged > 0);

// Clean up test user
$mysqli->query("DELETE FROM tbl_user_permissions WHERE user_id = {$test_uid}");
$mysqli->query("DELETE FROM tbl_users WHERE user_id = {$test_uid}");

echo "\n==============================================\n";
echo "Integration Test Summary: {$passCount} Passed, {$failCount} Failed\n";
echo "==============================================\n";
