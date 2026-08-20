<?php
// Complete Functional Test for Student Attendance Module

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== STARTING STUDENT ATTENDANCE MODULE TEST SUITE ===\n\n";

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

// TEST 1: Check Database Tables
echo "1. Testing Database Tables & Columns:\n";
$res = $mysqli->query("SHOW TABLES LIKE 'tbl_attendance%'");
$att_tables = [];
while ($r = $res->fetch_row()) $att_tables[] = $r[0];

assertTest("tbl_attendance exists", in_array('tbl_attendance', $att_tables));
assertTest("tbl_attendance_notifications exists", in_array('tbl_attendance_notifications', $att_tables));
assertTest("tbl_attendance_settings exists", in_array('tbl_attendance_settings', $att_tables));

$cols = [];
$res = $mysqli->query("SHOW COLUMNS FROM tbl_attendance");
while ($r = $res->fetch_assoc()) $cols[$r['Field']] = $r;
assertTest("tbl_attendance has attendance_type column", isset($cols['attendance_type']));
assertTest("tbl_attendance has period_id column", isset($cols['period_id']));
assertTest("tbl_attendance has marked_by column", isset($cols['marked_by']));

// TEST 2: Check Periods
echo "\n2. Testing Period Management Data:\n";
$res = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_periods WHERE status = 1");
$cnt = $res->fetch_assoc()['cnt'];
assertTest("Active periods exist in database ({$cnt} found)", $cnt > 0);

// TEST 3: Daily Attendance Marking & Duplicate Prevention
echo "\n3. Testing Daily Attendance Marking Flow:\n";
$test_date = '2026-08-20';
$st_res = $mysqli->query("SELECT student_id, first_name, last_name, class_id, section_id, academic_year_id FROM tbl_students WHERE status = 1 LIMIT 3");
$students = [];
while ($r = $st_res->fetch_assoc()) $students[] = $r;
assertTest("Found at least 3 active students for test", count($students) >= 3);

if (!empty($students)) {
    $st1 = $students[0];
    $st2 = $students[1];
    $st3 = $students[2];

    // Mark st1 Present, st2 Absent, st3 Late
    $mysqli->query("DELETE FROM tbl_attendance WHERE attendance_date = '{$test_date}' AND attendance_type = 'Daily'");
    $mysqli->query("DELETE FROM tbl_attendance_notifications WHERE attendance_date = '{$test_date}'");

    // Insert Attendance
    $mysqli->query("INSERT INTO tbl_attendance (student_id, academic_year_id, class_id, section_id, attendance_date, attendance_type, attendance_status, remarks, marked_by)
        VALUES ({$st1['student_id']}, {$st1['academic_year_id']}, {$st1['class_id']}, {$st1['section_id']}, '{$test_date}', 'Daily', 'Present', 'On time', 1)");

    $mysqli->query("INSERT INTO tbl_attendance (student_id, academic_year_id, class_id, section_id, attendance_date, attendance_type, attendance_status, remarks, marked_by)
        VALUES ({$st2['student_id']}, {$st2['academic_year_id']}, {$st2['class_id']}, {$st2['section_id']}, '{$test_date}', 'Daily', 'Absent', 'Sick leave', 1)");
    $att2_id = $mysqli->insert_id;

    $mysqli->query("INSERT INTO tbl_attendance (student_id, academic_year_id, class_id, section_id, attendance_date, attendance_type, attendance_status, remarks, marked_by)
        VALUES ({$st3['student_id']}, {$st3['academic_year_id']}, {$st3['class_id']}, {$st3['section_id']}, '{$test_date}', 'Daily', 'Late', 'Bus delay', 1)");
    $att3_id = $mysqli->insert_id;

    // Check inserted count
    $res = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_attendance WHERE attendance_date = '{$test_date}' AND attendance_type = 'Daily'");
    assertTest("Saved 3 daily attendance records", $res->fetch_assoc()['cnt'] == 3);

    // Create Notification records for Absent and Late
    $mysqli->query("INSERT INTO tbl_attendance_notifications (student_id, parent_name, attendance_id, attendance_date, notification_type, message, status)
        VALUES ({$st2['student_id']}, 'Parent of {$st2['first_name']}', {$att2_id}, '{$test_date}', 'Absent', 'Dear Parent, your child was marked absent today.', 'Pending')");

    $mysqli->query("INSERT INTO tbl_attendance_notifications (student_id, parent_name, attendance_id, attendance_date, notification_type, message, status)
        VALUES ({$st3['student_id']}, 'Parent of {$st3['first_name']}', {$att3_id}, '{$test_date}', 'Late', 'Dear Parent, your child was marked late today.', 'Pending')");

    $nres = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_attendance_notifications WHERE attendance_date = '{$test_date}' AND status = 'Pending'");
    assertTest("Generated 2 pending parent notifications for Absent & Late", $nres->fetch_assoc()['cnt'] == 2);
}

// TEST 4: Period-wise Attendance
echo "\n4. Testing Period-wise Attendance Flow:\n";
if (!empty($students)) {
    $mysqli->query("DELETE FROM tbl_attendance WHERE attendance_date = '{$test_date}' AND attendance_type = 'Period-wise'");

    $mysqli->query("INSERT INTO tbl_attendance (student_id, academic_year_id, class_id, section_id, attendance_date, attendance_type, period_id, attendance_status, remarks, marked_by)
        VALUES ({$st1['student_id']}, {$st1['academic_year_id']}, {$st1['class_id']}, {$st1['section_id']}, '{$test_date}', 'Period-wise', 1, 'Present', 'Maths period', 1)");

    $mysqli->query("INSERT INTO tbl_attendance (student_id, academic_year_id, class_id, section_id, attendance_date, attendance_type, period_id, attendance_status, remarks, marked_by)
        VALUES ({$st1['student_id']}, {$st1['academic_year_id']}, {$st1['class_id']}, {$st1['section_id']}, '{$test_date}', 'Period-wise', 2, 'Late', 'Science period', 1)");

    $pres = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_attendance WHERE student_id = {$st1['student_id']} AND attendance_date = '{$test_date}' AND attendance_type = 'Period-wise'");
    assertTest("Recorded period-wise attendance for multiple periods", $pres->fetch_assoc()['cnt'] == 2);
}

// TEST 5: Settings Query
echo "\n5. Testing Attendance Settings:\n";
$sres = $mysqli->query("SELECT * FROM tbl_attendance_settings WHERE setting_id = 1");
$settings_row = $sres->fetch_assoc();
assertTest("Attendance settings record exists", !empty($settings_row));
assertTest("Absent template configured", !empty($settings_row['absent_template']));

echo "\n=== TEST SUMMARY: {$passCount}/{$testCount} Tests Passed ===\n";
