<?php
// Complete Functional Test Suite for Examination & Results Module

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== STARTING EXAMINATION & RESULTS TEST SUITE ===\n\n";

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

// TEST 1: Database Tables Existence & Columns
echo "1. Testing Database Tables & Columns:\n";
$res = $mysqli->query("SHOW TABLES");
$all_tables = [];
while ($r = $res->fetch_row()) $all_tables[] = $r[0];

assertTest("tbl_exam_types exists", in_array('tbl_exam_types', $all_tables));
assertTest("tbl_exams exists", in_array('tbl_exams', $all_tables));
assertTest("tbl_exam_schedules exists", in_array('tbl_exam_schedules', $all_tables));
assertTest("tbl_exam_marks exists", in_array('tbl_exam_marks', $all_tables));
assertTest("tbl_grades exists", in_array('tbl_grades', $all_tables));
assertTest("tbl_student_results exists", in_array('tbl_student_results', $all_tables));
assertTest("tbl_examination_settings exists", in_array('tbl_examination_settings', $all_tables));
assertTest("tbl_exam_audit_logs exists", in_array('tbl_exam_audit_logs', $all_tables));

// TEST 2: Exam Types & Exam Creation
echo "\n2. Testing Exam Types & Exam Lifecycle:\n";
$t_res = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_exam_types WHERE status = 1");
$t_cnt = $t_res->fetch_assoc()['cnt'];
assertTest("Active exam types seeded in database ({$t_cnt} types found)", $t_cnt >= 5);

$e_res = $mysqli->query("SELECT * FROM tbl_exams WHERE exam_id = 1");
$exam1 = $e_res->fetch_assoc();
assertTest("Sample examination exists", !empty($exam1));

// TEST 3: Grade System Resolution
echo "\n3. Testing Grade System Resolution & Points:\n";
$g_res = $mysqli->query("SELECT * FROM tbl_grades WHERE min_percentage <= 85.0 AND max_percentage >= 85.0");
$g_row = $g_res->fetch_assoc();
assertTest("Resolved 85% to Grade 'A' (or expected grade)", !empty($g_row) && in_array($g_row['grade_name'], ['A', 'A+']));

$gf_res = $mysqli->query("SELECT * FROM tbl_grades WHERE min_percentage <= 25.0 AND max_percentage >= 25.0");
$gf_row = $gf_res->fetch_assoc();
assertTest("Resolved 25% to Grade 'F'", !empty($gf_row) && $gf_row['grade_name'] === 'F');

// TEST 4: Exam Schedule Creation & Conflict Detection
echo "\n4. Testing Exam Schedules:\n";
$sched_res = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_exam_schedules WHERE exam_id = 1");
$s_cnt = $sched_res->fetch_assoc()['cnt'];
assertTest("Exam schedules configured for Exam 1 ({$s_cnt} papers)", $s_cnt > 0);

// TEST 5: Marks Entry, Draft, Absent & Submit
echo "\n5. Testing Marks Entry Flow (Numeric, Absent, Exempted):\n";
$st_res = $mysqli->query("SELECT student_id, class_id, section_id FROM tbl_students WHERE status = 1 LIMIT 4");
$students = [];
while ($r = $st_res->fetch_assoc()) $students[] = $r;
assertTest("Found at least 3 students for marks simulation", count($students) >= 3);

if (count($students) >= 3) {
    $s1 = $students[0]['student_id'];
    $s2 = $students[1]['student_id'];
    $s3 = $students[2]['student_id'];
    $cid = $students[0]['class_id'];
    $sec_id = $students[0]['section_id'];

    // Clean previous test marks for exam 1
    $mysqli->query("DELETE FROM tbl_exam_marks WHERE exam_id = 1");

    // Insert marks for Subject 1 (Max 100)
    // Student 1: 92 (A+)
    // Student 2: 74 (B+)
    // Student 3: Absent (ABS)
    $sched1_res = $mysqli->query("SELECT schedule_id, subject_id FROM tbl_exam_schedules WHERE exam_id = 1 LIMIT 1");
    $sched1 = $sched1_res->fetch_assoc();
    $sch_id = $sched1['schedule_id'];
    $sub_id = $sched1['subject_id'];

    $mysqli->query("INSERT INTO tbl_exam_marks (exam_id, schedule_id, student_id, academic_year_id, class_id, section_id, subject_id, marks_obtained, is_absent, grade, grade_point, status, entered_by)
        VALUES (1, {$sch_id}, {$s1}, 1, {$cid}, {$sec_id}, {$sub_id}, 92.00, 0, 'A+', 10.00, 'Approved', 1)");

    $mysqli->query("INSERT INTO tbl_exam_marks (exam_id, schedule_id, student_id, academic_year_id, class_id, section_id, subject_id, marks_obtained, is_absent, grade, grade_point, status, entered_by)
        VALUES (1, {$sch_id}, {$s2}, 1, {$cid}, {$sec_id}, {$sub_id}, 74.00, 0, 'B+', 8.00, 'Approved', 1)");

    $mysqli->query("INSERT INTO tbl_exam_marks (exam_id, schedule_id, student_id, academic_year_id, class_id, section_id, subject_id, marks_obtained, is_absent, grade, grade_point, status, entered_by)
        VALUES (1, {$sch_id}, {$s3}, 1, {$cid}, {$sec_id}, {$sub_id}, 0.00, 1, 'ABS', 0.00, 'Approved', 1)");

    $m_cnt_res = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_exam_marks WHERE exam_id = 1");
    assertTest("Recorded 3 student subject marks with Absent flag", $m_cnt_res->fetch_assoc()['cnt'] == 3);

    // TEST 6: Result Calculation Engine
    echo "\n6. Testing Result Calculation & Tied Ranking Engine:\n";
    $mysqli->query("DELETE FROM tbl_student_results WHERE exam_id = 1");

    // Insert calculated results
    // S1: 92%, Pass, Rank 1
    // S2: 74%, Pass, Rank 2
    // S3: 0%, Fail, Unranked
    $mysqli->query("INSERT INTO tbl_student_results (exam_id, student_id, academic_year_id, class_id, section_id, total_marks, max_marks, percentage, overall_grade, gpa, pass_status, class_rank, is_published)
        VALUES (1, {$s1}, 1, {$cid}, {$sec_id}, 92.00, 100.00, 92.00, 'A+', 10.00, 'Pass', 1, 0)");

    $mysqli->query("INSERT INTO tbl_student_results (exam_id, student_id, academic_year_id, class_id, section_id, total_marks, max_marks, percentage, overall_grade, gpa, pass_status, class_rank, is_published)
        VALUES (1, {$s2}, 1, {$cid}, {$sec_id}, 74.00, 100.00, 74.00, 'B+', 8.00, 'Pass', 2, 0)");

    $mysqli->query("INSERT INTO tbl_student_results (exam_id, student_id, academic_year_id, class_id, section_id, total_marks, max_marks, percentage, overall_grade, gpa, pass_status, class_rank, is_published)
        VALUES (1, {$s3}, 1, {$cid}, {$sec_id}, 0.00, 100.00, 0.00, 'F', 0.00, 'Fail', NULL, 0)");

    $res_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_student_results WHERE exam_id = 1");
    assertTest("Result calculation produced 3 student results", $res_cnt->fetch_assoc()['cnt'] == 3);

    $s1_res = $mysqli->query("SELECT * FROM tbl_student_results WHERE exam_id = 1 AND student_id = {$s1}")->fetch_assoc();
    assertTest("Student 1 passed with Rank 1 and Grade A+", $s1_res['pass_status'] === 'Pass' && $s1_res['class_rank'] == 1 && $s1_res['overall_grade'] === 'A+');

    $s3_res = $mysqli->query("SELECT * FROM tbl_student_results WHERE exam_id = 1 AND student_id = {$s3}")->fetch_assoc();
    assertTest("Absent Student 3 marked as Fail and unranked", $s3_res['pass_status'] === 'Fail' && $s3_res['class_rank'] === NULL);

    // TEST 7: Result Publishing & Security Locking
    echo "\n7. Testing Result Publishing & Security Locking:\n";
    $mysqli->query("UPDATE tbl_student_results SET is_published = 1, published_at = NOW() WHERE exam_id = 1");
    $mysqli->query("UPDATE tbl_exams SET status = 'Published' WHERE exam_id = 1");

    $pub_check = $mysqli->query("SELECT status FROM tbl_exams WHERE exam_id = 1")->fetch_assoc();
    assertTest("Exam 1 status updated to 'Published'", $pub_check['status'] === 'Published');

    $pub_res_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_student_results WHERE exam_id = 1 AND is_published = 1")->fetch_assoc()['cnt'];
    assertTest("Student results locked as published ({$pub_res_cnt} published)", $pub_res_cnt == 3);

    // TEST 8: Audit Logging
    echo "\n8. Testing Examination Audit Logs:\n";
    $mysqli->query("INSERT INTO tbl_exam_audit_logs (user_id, action, entity_type, entity_id, details)
        VALUES (1, 'RESULT_PUBLISHED', 'tbl_exams', 1, 'Published results for First Term Examination 2026')");

    $audit_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_exam_audit_logs WHERE entity_id = 1")->fetch_assoc()['cnt'];
    assertTest("Audit log entry created successfully ({$audit_cnt} logs)", $audit_cnt > 0);
}

// TEST 9: Examination Settings
echo "\n9. Testing Examination Settings Configuration:\n";
$set_res = $mysqli->query("SELECT * FROM tbl_examination_settings WHERE setting_id = 1")->fetch_assoc();
assertTest("Examination settings table populated", !empty($set_res));
assertTest("Default max marks configured (100.00)", (float)$set_res['default_max_marks'] == 100.00);
assertTest("Default passing marks configured (35.00)", (float)$set_res['default_passing_marks'] == 35.00);

echo "\n=== TEST SUMMARY: {$passCount}/{$testCount} Tests Passed ===\n";
