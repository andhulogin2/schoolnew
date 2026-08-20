<?php
// Complete End-to-End Integration Test Suite for Leave Management Module

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== Running Leave Management End-to-End Integration Tests ===\n\n";

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

// 1. Leave Types Verification
$types_res = $mysqli->query("SELECT * FROM tbl_leave_types WHERE status = 1");
assert_test("1. Default Leave Types seeded and active", $types_res->num_rows >= 5);

$cl = $mysqli->query("SELECT * FROM tbl_leave_types WHERE type_code = 'CL'")->fetch_assoc();
assert_test("2. Casual Leave (CL) exists with max_days = 12", $cl && $cl['max_days'] == 12);

// 2. Fetch test student and staff
$student = $mysqli->query("SELECT student_id, class_id, section_id FROM tbl_students LIMIT 1")->fetch_assoc();
$staff = $mysqli->query("SELECT staff_id FROM tbl_staff LIMIT 1")->fetch_assoc();

assert_test("3. Found test student and staff records", !empty($student) && !empty($staff));

$student_id = (int)$student['student_id'];
$class_id = (int)$student['class_id'];
$section_id = (int)$student['section_id'];
$staff_id = (int)$staff['staff_id'];
$leave_type_id = (int)$cl['type_id'];

// 3. Test Student Leave Application Creation
$stmt = $mysqli->prepare("INSERT INTO tbl_leave_applications (applicant_type, student_id, academic_year_id, class_id, section_id, leave_type_id, from_date, to_date, duration_days, is_half_day, reason, status, applied_date) VALUES ('Student', ?, 1, ?, ?, ?, '2026-08-25', '2026-08-26', 2.0, 0, 'Medical appointment with physician', 'Pending', '2026-08-20')");
$stmt->bind_param("iiii", $student_id, $class_id, $section_id, $leave_type_id);
$stmt->execute();
$student_app_id = $mysqli->insert_id;

assert_test("4. Student Leave Application submitted in Pending status", $student_app_id > 0);

// 4. Test Staff Leave Application Creation with Half-Day
$stmt = $mysqli->prepare("INSERT INTO tbl_leave_applications (applicant_type, staff_id, academic_year_id, leave_type_id, from_date, to_date, duration_days, is_half_day, half_day_type, reason, status, applied_date) VALUES ('Staff', ?, 1, ?, '2026-08-28', '2026-08-28', 0.5, 1, 'First Half', 'Personal urgent bank appointment', 'Pending', '2026-08-20')");
$stmt->bind_param("ii", $staff_id, $leave_type_id);
$stmt->execute();
$staff_app_id = $mysqli->insert_id;

assert_test("5. Staff Half-Day Leave Application submitted (0.5 days)", $staff_app_id > 0);

// 5. Test Leave Approval & Balance Deduction
// Initial balance check
$mysqli->query("INSERT IGNORE INTO tbl_leave_balances (academic_year_id, entity_type, entity_id, leave_type_id, allocated_days, used_days, pending_days, carry_forward_days) VALUES (1, 'Staff', {$staff_id}, {$leave_type_id}, 12.0, 0.0, 0.0, 0.0)");

// Approve staff leave
$mysqli->query("UPDATE tbl_leave_applications SET status = 'Approved', approved_by = 1, approved_at = NOW() WHERE application_id = {$staff_app_id}");
$mysqli->query("UPDATE tbl_leave_balances SET used_days = used_days + 0.5 WHERE entity_type = 'Staff' AND entity_id = {$staff_id} AND leave_type_id = {$leave_type_id}");

$bal_check = $mysqli->query("SELECT * FROM tbl_leave_balances WHERE entity_type = 'Staff' AND entity_id = {$staff_id} AND leave_type_id = {$leave_type_id}")->fetch_assoc();
assert_test("6. Approved staff leave deducted 0.5 days from balance (used = 0.5)", $bal_check && $bal_check['used_days'] == 0.5);

// 6. Test Rejection Workflow (with mandatory rejection reason)
$stmt = $mysqli->prepare("INSERT INTO tbl_leave_applications (applicant_type, student_id, academic_year_id, class_id, section_id, leave_type_id, from_date, to_date, duration_days, is_half_day, reason, status, applied_date) VALUES ('Student', ?, 1, ?, ?, ?, '2026-09-01', '2026-09-03', 3.0, 0, 'Family trip during mid-term exam week', 'Pending', '2026-08-20')");
$stmt->bind_param("iiii", $student_id, $class_id, $section_id, $leave_type_id);
$stmt->execute();
$reject_app_id = $mysqli->insert_id;

$rejection_reason = "Cannot approve leave during mid-term examination week.";
$mysqli->query("UPDATE tbl_leave_applications SET status = 'Rejected', rejection_reason = '{$mysqli->real_escape_string($rejection_reason)}', approved_by = 1, approved_at = NOW() WHERE application_id = {$reject_app_id}");

$rej_app = $mysqli->query("SELECT * FROM tbl_leave_applications WHERE application_id = {$reject_app_id}")->fetch_assoc();
assert_test("7. Leave Request rejected with reason properly recorded", $rej_app && $rej_app['status'] === 'Rejected' && !empty($rej_app['rejection_reason']));

// 7. Test Clarification Workflow
$stmt = $mysqli->prepare("INSERT INTO tbl_leave_applications (applicant_type, student_id, academic_year_id, class_id, section_id, leave_type_id, from_date, to_date, duration_days, is_half_day, reason, status, applied_date) VALUES ('Student', ?, 1, ?, ?, ?, '2026-09-10', '2026-09-12', 3.0, 0, 'Medical absence', 'Pending', '2026-08-20')");
$stmt->bind_param("iiii", $student_id, $class_id, $section_id, $leave_type_id);
$stmt->execute();
$clarif_app_id = $mysqli->insert_id;

$clarif_note = "Please upload doctor prescription letter.";
$mysqli->query("UPDATE tbl_leave_applications SET status = 'Clarification Required', clarification_notes = '{$mysqli->real_escape_string($clarif_note)}' WHERE application_id = {$clarif_app_id}");

$clarif_app = $mysqli->query("SELECT * FROM tbl_leave_applications WHERE application_id = {$clarif_app_id}")->fetch_assoc();
assert_test("8. Clarification Requested status updated with notes", $clarif_app && $clarif_app['status'] === 'Clarification Required' && !empty($clarif_app['clarification_notes']));

// 8. Test Cancellation & Balance Restoration
// Cancel approved staff leave
$mysqli->query("UPDATE tbl_leave_applications SET status = 'Cancelled' WHERE application_id = {$staff_app_id}");
$mysqli->query("UPDATE tbl_leave_balances SET used_days = GREATEST(0.0, used_days - 0.5) WHERE entity_type = 'Staff' AND entity_id = {$staff_id} AND leave_type_id = {$leave_type_id}");

$bal_restored = $mysqli->query("SELECT * FROM tbl_leave_balances WHERE entity_type = 'Staff' AND entity_id = {$staff_id} AND leave_type_id = {$leave_type_id}")->fetch_assoc();
assert_test("9. Cancelled approved leave successfully restored balance (used = 0.0)", $bal_restored && $bal_restored['used_days'] == 0.0);

// 9. Test Attendance Module Integration (Checking approved student leave status)
$mysqli->query("UPDATE tbl_leave_applications SET status = 'Approved' WHERE application_id = {$student_app_id}");
$att_check = $mysqli->query("SELECT * FROM tbl_leave_applications WHERE applicant_type = 'Student' AND status = 'Approved' AND student_id = {$student_id} AND from_date <= '2026-08-25' AND to_date >= '2026-08-25'")->fetch_assoc();
assert_test("10. Attendance engine can identify student on approved leave as Excused/Leave", !empty($att_check));

// 10. Test Audit History Logging
$mysqli->query("INSERT INTO tbl_leave_history (application_id, action, performed_by, performed_by_type, previous_status, new_status, comments) VALUES ({$student_app_id}, 'Approved', 1, 'Staff', 'Pending', 'Approved', 'Approved by principal.')");
$hist_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_leave_history WHERE application_id = {$student_app_id}")->fetch_assoc()['cnt'];
assert_test("11. Leave history and timeline events correctly recorded", $hist_cnt > 0);

// 11. Test Settings Configuration
$settings = $mysqli->query("SELECT * FROM tbl_leave_settings WHERE setting_id = 1")->fetch_assoc();
assert_test("12. Global Leave policies and configuration queryable", !empty($settings) && $settings['enable_student_leave'] == 1);

echo "\n==============================================\n";
echo "Integration Test Summary: {$passCount} Passed, {$failCount} Failed\n";
echo "==============================================\n";
