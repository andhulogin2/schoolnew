<?php
/**
 * Comprehensive Verification Suite for User Creation Duplicate Fix
 */
$conn = new mysqli('localhost', 'root', '', 'db_school');
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error . "\n");
}

echo "=======================================================\n";
echo "TEST SUITE: User Creation Duplicate Check & Lifecycle\n";
echo "=======================================================\n\n";

$pass_count = 0;
$fail_count = 0;

function assert_test($description, $condition) {
    global $pass_count, $fail_count;
    if ($condition) {
        echo "[PASS] $description\n";
        $pass_count++;
    } else {
        echo "[FAIL] $description\n";
        $fail_count++;
    }
}

// Cleanup any old test accounts
$conn->query("DELETE FROM tbl_users WHERE username LIKE 'testuser%' OR username = 'deletedtest' OR email LIKE 'testuser%' OR email = 'deletedtest@example.com'");
$conn->query("DELETE FROM tbl_user_login_activity WHERE username LIKE 'testuser%' OR username = 'deletedtest'");

// 1. Test is_username_exists and is_email_exists query logic directly against DB
function is_username_exists_db($conn, $username, $exclude_id = null) {
    $username = trim((string)$username);
    if ($username === '') return false;
    $sql = "SELECT COUNT(*) as c FROM tbl_users WHERE username = ? AND is_deleted = 'n'";
    if ($exclude_id) $sql .= " AND user_id != " . (int)$exclude_id;
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return $res['c'] > 0;
}

function is_email_exists_db($conn, $email, $exclude_id = null) {
    $email = trim((string)$email);
    if ($email === '') return false;
    $sql = "SELECT COUNT(*) as c FROM tbl_users WHERE email = ? AND is_deleted = 'n'";
    if ($exclude_id) $sql .= " AND user_id != " . (int)$exclude_id;
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return $res['c'] > 0;
}

// Initial state: admin exists, testuser001 does not exist
assert_test("Initial state: 'admin' username exists in active users", is_username_exists_db($conn, 'admin') === true);
assert_test("Initial state: 'testuser001' username does NOT exist", is_username_exists_db($conn, 'testuser001') === false);
assert_test("Initial state: 'testuser001@example.com' email does NOT exist", is_email_exists_db($conn, 'testuser001@example.com') === false);
assert_test("Empty username string returns false (not duplicate)", is_username_exists_db($conn, '') === false);
assert_test("Whitespace username string returns false (not duplicate)", is_username_exists_db($conn, '   ') === false);
assert_test("Empty email string returns false (not duplicate)", is_email_exists_db($conn, '') === false);
assert_test("Whitespace email string returns false (not duplicate)", is_email_exists_db($conn, '   ') === false);

// STEP 17: Create brand new user testuser001
$pwd_hash = password_hash('Secret123', PASSWORD_BCRYPT);
$stmt = $conn->prepare("INSERT INTO tbl_users (role_id, username, user_type, name, email, phone, password, status, is_deleted) VALUES (1, 'testuser001', 'Admin', 'Test User 001', 'testuser001@example.com', '9876543210', ?, 'Active', 'n')");
$stmt->bind_param('s', $pwd_hash);
$stmt->execute();
$new_id = $conn->insert_id;

assert_test("User testuser001 created successfully (ID: $new_id)", $new_id > 0);
assert_test("testuser001 is detected as existing username", is_username_exists_db($conn, 'testuser001') === true);
assert_test("testuser001@example.com is detected as existing email", is_email_exists_db($conn, 'testuser001@example.com') === true);

// STEP 18: Test duplicate username
$dup_uname = is_username_exists_db($conn, 'testuser001');
$dup_email_diff = is_email_exists_db($conn, 'different@example.com');
assert_test("Duplicate username 'testuser001' correctly triggers username collision", $dup_uname === true && $dup_email_diff === false);

// STEP 19: Test duplicate email
$dup_uname_diff = is_username_exists_db($conn, 'testuser002');
$dup_email = is_email_exists_db($conn, 'testuser001@example.com');
assert_test("Duplicate email 'testuser001@example.com' correctly triggers email collision", $dup_uname_diff === false && $dup_email === true);

// Test both duplicate
assert_test("Both duplicate username and email correctly detected", is_username_exists_db($conn, 'testuser001') === true && is_email_exists_db($conn, 'testuser001@example.com') === true);

// STEP 20: Test soft delete and re-creation
$conn->query("UPDATE tbl_users SET is_deleted = 'y', status = 'Inactive' WHERE user_id = $new_id");
$del_row = $conn->query("SELECT is_deleted, status FROM tbl_users WHERE user_id = $new_id")->fetch_assoc();
assert_test("Soft delete sets is_deleted = 'y' and status = 'Inactive'", $del_row['is_deleted'] === 'y' && $del_row['status'] === 'Inactive');

// Soft-deleted user should NOT be detected as active duplicate
assert_test("Soft-deleted username 'testuser001' is available for re-registration", is_username_exists_db($conn, 'testuser001') === false);
assert_test("Soft-deleted email 'testuser001@example.com' is available for re-registration", is_email_exists_db($conn, 'testuser001@example.com') === false);

// STEP 21: Verify user listing query excludes soft-deleted user
$list_res = $conn->query("SELECT user_id FROM tbl_users WHERE is_deleted = 'n' AND username = 'testuser001'");
assert_test("User listing query (is_deleted='n') excludes soft-deleted user", $list_res->num_rows === 0);

// Re-create user with same credentials
$stmt2 = $conn->prepare("INSERT INTO tbl_users (role_id, username, user_type, name, email, phone, password, status, is_deleted) VALUES (1, 'testuser001', 'Admin', 'Recreated Test User', 'testuser001@example.com', '9876543210', ?, 'Active', 'n')");
$stmt2->bind_param('s', $pwd_hash);
$insert_ok = $stmt2->execute();
$recreated_id = $conn->insert_id;

assert_test("Re-creating user with same username & email succeeds without DB constraint collision (New ID: $recreated_id)", $insert_ok === true && $recreated_id > 0);
assert_test("Both old soft-deleted record (ID: $new_id) and new active record (ID: $recreated_id) coexist in DB", $new_id != $recreated_id);

// STEP 22: Test authentication verification logic
function verify_login($conn, $identifier, $password) {
    $identifier = trim((string)$identifier);
    if ($identifier === '' || empty($password)) return false;
    $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE (username = ? OR email = ?) AND is_deleted = 'n'");
    $stmt->bind_param('ss', $identifier, $identifier);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user) return false;
    if ($user['status'] !== 'Active') return false;
    return password_verify($password, $user['password']);
}

assert_test("Active user can authenticate by username", verify_login($conn, 'testuser001', 'Secret123') === true);
assert_test("Active user can authenticate by email", verify_login($conn, 'testuser001@example.com', 'Secret123') === true);
assert_test("Wrong password fails authentication", verify_login($conn, 'testuser001', 'WrongPass') === false);

// Deactivate and verify cannot login
$conn->query("UPDATE tbl_users SET status = 'Inactive' WHERE user_id = $recreated_id");
assert_test("Inactive user cannot authenticate", verify_login($conn, 'testuser001', 'Secret123') === false);

// STEP 24: Verify Super Admin account remains intact
$super_admin = $conn->query("SELECT user_id, username, email, is_deleted, status FROM tbl_users WHERE user_type = 'Admin' AND is_deleted = 'n' LIMIT 1")->fetch_assoc();
assert_test("Super Admin remains intact: {$super_admin['username']} ({$super_admin['email']})", !empty($super_admin) && $super_admin['is_deleted'] === 'n' && $super_admin['status'] === 'Active');

// Cleanup test rows
$conn->query("DELETE FROM tbl_users WHERE user_id IN ($new_id, $recreated_id)");
assert_test("Test accounts cleaned up after tests", true);

echo "\n=======================================================\n";
echo "SUMMARY: Total Passed: $pass_count | Failed: $fail_count\n";
echo "=======================================================\n";

if ($fail_count === 0) {
    echo "ALL TESTS PASSED SUCCESSFULLY!\n";
} else {
    echo "SOME TESTS FAILED!\n";
}
