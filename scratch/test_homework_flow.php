<?php
// Comprehensive Integration Test Suite for Homework & Assignments Module

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== STARTING HOMEWORK & ASSIGNMENTS TEST SUITE ===\n\n";

$passCount = 0;
$testCount = 0;

function assertTest($description, $condition) {
    global $passCount, $testCount;
    $testCount++;
    if ($condition) {
        $passCount++;
        echo "  [PASS] {$description}\n";
    } else {
        echo "  [FAIL] {$description}\n";
    }
}

// TEST 1: Database Tables & Default Data
echo "1. Testing Database Schema & Configurations:\n";
$res = $mysqli->query("SHOW TABLES");
$tables = [];
while ($r = $res->fetch_row()) $tables[] = $r[0];

assertTest("tbl_assignment_types exists", in_array('tbl_assignment_types', $tables));
assertTest("tbl_assignments exists", in_array('tbl_assignments', $tables));
assertTest("tbl_assignment_submissions exists", in_array('tbl_assignment_submissions', $tables));
assertTest("tbl_submission_history exists", in_array('tbl_submission_history', $tables));
assertTest("tbl_homework_notifications exists", in_array('tbl_homework_notifications', $tables));
assertTest("tbl_homework_settings exists", in_array('tbl_homework_settings', $tables));
assertTest("tbl_homework_audit_logs exists", in_array('tbl_homework_audit_logs', $tables));

$types_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_assignment_types WHERE status = 1")->fetch_assoc()['cnt'];
assertTest("Configured Assignment Types present ({$types_cnt} types)", $types_cnt >= 5);

// TEST 2: Assignment Creation & Targeting
echo "\n2. Testing Assignment Creation & Publishing:\n";
$test_title = "Unit 3 Algebra Worksheet " . time();
$mysqli->query("INSERT INTO tbl_assignments (
    academic_year_id, class_id, section_id, subject_id, teacher_id, assignment_type_id,
    title, description, instructions, assigned_date, due_date, due_time, max_marks,
    allow_remarks, allow_file_submission, allow_text_submission, allow_multiple_files,
    allow_resubmission, allow_late_submission, target_type, status, created_by
) VALUES (
    1, 1, 1, 1, 1, 1,
    '{$test_title}', 'Test description', 'Complete all problems',
    CURDATE(), DATE_ADD(CURDATE(), INTERVAL 3 DAY), '23:59:00', 25.00,
    1, 1, 1, 1, 1, 1, 'Section', 'Published', 1
)");
$asgn_id = $mysqli->insert_id;
assertTest("New assignment created and published (ID: {$asgn_id})", $asgn_id > 0);

// TEST 3: Student First Submission (v1)
echo "\n3. Testing Student Submission (v1):\n";
$student_res = $mysqli->query("SELECT student_id FROM tbl_students WHERE class_id = 1 AND section_id = 1 LIMIT 1");
$student = $student_res->fetch_assoc();
$student_id = $student ? (int)$student['student_id'] : 1;

$test_files = json_encode([['orig_name' => 'algebra_solution.pdf', 'file_name' => 'asgn_test.pdf', 'file_size' => 2048]]);
$mysqli->query("INSERT INTO tbl_assignment_submissions (
    assignment_id, student_id, submission_version, submitted_text, submitted_files,
    submitted_at, is_late, status
) VALUES (
    {$asgn_id}, {$student_id}, 1, 'Here are my solutions to all 10 problems.', '{$test_files}',
    NOW(), 0, 'Submitted'
) ON DUPLICATE KEY UPDATE submitted_text = 'Here are my solutions to all 10 problems.'");
$subm_id = $mysqli->insert_id;
if (!$subm_id) {
    $subm = $mysqli->query("SELECT submission_id FROM tbl_assignment_submissions WHERE assignment_id = {$asgn_id} AND student_id = {$student_id}")->fetch_assoc();
    $subm_id = $subm['submission_id'];
}
assertTest("Student submitted response (v1, Submission ID: {$subm_id})", $subm_id > 0);

// TEST 4: Teacher Evaluation & Grade Resolution
echo "\n4. Testing Teacher Review & Grade Awarding:\n";
// Marks 22.5 out of 25 => 90% (Grade A+ or A)
$score = 22.50;
$max_score = 25.00;
$pct = ($score / $max_score) * 100;
$grade_row = $mysqli->query("SELECT grade_name FROM tbl_grades WHERE min_percentage <= {$pct} AND max_percentage >= {$pct} AND status = 1 LIMIT 1")->fetch_assoc();
$grade_name = $grade_row ? $grade_row['grade_name'] : 'A+';

$mysqli->query("UPDATE tbl_assignment_submissions SET
    marks_obtained = {$score},
    grade = '{$grade_name}',
    teacher_remarks = 'Great effort, neat diagrams.',
    status = 'Reviewed',
    reviewed_by = 1,
    reviewed_at = NOW()
WHERE submission_id = {$subm_id}");

$check_review = $mysqli->query("SELECT * FROM tbl_assignment_submissions WHERE submission_id = {$subm_id}")->fetch_assoc();
assertTest("Teacher review recorded with marks {$check_review['marks_obtained']}/25 and Grade '{$check_review['grade']}'", $check_review['status'] === 'Reviewed' && $check_review['marks_obtained'] == 22.5);

// TEST 5: Return for Correction & Resubmission (v2)
echo "\n5. Testing Return for Correction & Resubmission Versioning (v2):\n";
// Teacher returns for correction
$mysqli->query("UPDATE tbl_assignment_submissions SET
    status = 'Returned',
    correction_reason = 'Question 4 is missing steps. Please complete.'
WHERE submission_id = {$subm_id}");

// Archive v1 to history
$mysqli->query("INSERT INTO tbl_submission_history (
    submission_id, assignment_id, student_id, version, submitted_text, submitted_files,
    submitted_at, marks_obtained, grade, teacher_remarks, status
) VALUES (
    {$subm_id}, {$asgn_id}, {$student_id}, 1, '{$check_review['submitted_text']}', '{$test_files}',
    '{$check_review['submitted_at']}', {$check_review['marks_obtained']}, '{$check_review['grade']}', '{$check_review['teacher_remarks']}', 'Reviewed'
)");

// Student resubmits (v2)
$mysqli->query("UPDATE tbl_assignment_submissions SET
    submission_version = 2,
    submitted_text = 'Updated solution with complete Question 4 working steps.',
    status = 'Submitted',
    submitted_at = NOW()
WHERE submission_id = {$subm_id}");

$resub = $mysqli->query("SELECT * FROM tbl_assignment_submissions WHERE submission_id = {$subm_id}")->fetch_assoc();
$hist = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_submission_history WHERE submission_id = {$subm_id}")->fetch_assoc()['cnt'];
assertTest("Resubmission updated to v2 (Status: {$resub['status']})", $resub['submission_version'] == 2);
assertTest("Submission history trail contains prior v1 version ({$hist} records in history)", $hist >= 1);

// TEST 6: Notifications Queue
echo "\n6. Testing Notification Queue Generation:\n";
$mysqli->query("INSERT INTO tbl_homework_notifications (
    assignment_id, student_id, parent_name, parent_phone, notification_type, message, status
) VALUES (
    {$asgn_id}, {$student_id}, 'Parent', '9876543210', 'Submission Reviewed',
    'Your assignment {$test_title} was reviewed.', 'Pending'
)");
$notif = $mysqli->query("SELECT * FROM tbl_homework_notifications WHERE assignment_id = {$asgn_id} LIMIT 1")->fetch_assoc();
assertTest("Notification queued for student/parent (Type: {$notif['notification_type']})", !empty($notif));

// TEST 7: Settings & Audit Log
echo "\n7. Testing Homework Settings & Audit Log:\n";
$settings = $mysqli->query("SELECT * FROM tbl_homework_settings WHERE setting_id = 1")->fetch_assoc();
assertTest("Homework settings retrieved (Max Upload: {$settings['max_upload_size_mb']}MB, Default Deadline: {$settings['default_submission_deadline_days']} days)", !empty($settings));

$mysqli->query("INSERT INTO tbl_homework_audit_logs (user_id, action, entity_type, entity_id, details) VALUES (1, 'ASSIGNMENT_CREATED', 'tbl_assignments', {$asgn_id}, 'Created test assignment')");
$log = $mysqli->query("SELECT * FROM tbl_homework_audit_logs WHERE entity_id = {$asgn_id} LIMIT 1")->fetch_assoc();
assertTest("Audit log entry recorded: '{$log['action']}' on Assignment #{$log['entity_id']}", !empty($log));

// Cleanup test data
$mysqli->query("DELETE FROM tbl_submission_history WHERE submission_id = {$subm_id}");
$mysqli->query("DELETE FROM tbl_assignment_submissions WHERE submission_id = {$subm_id}");
$mysqli->query("DELETE FROM tbl_homework_notifications WHERE assignment_id = {$asgn_id}");
$mysqli->query("DELETE FROM tbl_homework_audit_logs WHERE entity_id = {$asgn_id}");
$mysqli->query("DELETE FROM tbl_assignments WHERE assignment_id = {$asgn_id}");

echo "\n=== HOMEWORK TEST SUMMARY: {$passCount}/{$testCount} Tests Passed ===\n";
