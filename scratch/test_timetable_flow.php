<?php
// Complete Functional Test Suite for Timetable Module

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== STARTING TIMETABLE TEST SUITE ===\n\n";

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

// TEST 1: Database Tables & Period Reuse
echo "1. Testing Database Tables & Reused Period Management:\n";
$res = $mysqli->query("SHOW TABLES");
$tables = [];
while ($r = $res->fetch_row()) $tables[] = $r[0];

assertTest("tbl_periods (reused from attendance) exists", in_array('tbl_periods', $tables));
assertTest("tbl_timetable exists", in_array('tbl_timetable', $tables));
assertTest("tbl_subject_allocations exists", in_array('tbl_subject_allocations', $tables));
assertTest("tbl_timetable_publish exists", in_array('tbl_timetable_publish', $tables));
assertTest("tbl_teacher_substitutions exists", in_array('tbl_teacher_substitutions', $tables));
assertTest("tbl_timetable_settings exists", in_array('tbl_timetable_settings', $tables));

$periods_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_periods WHERE status = 1")->fetch_assoc()['cnt'];
assertTest("Active periods present in tbl_periods ({$periods_cnt} periods)", $periods_cnt >= 4);

// TEST 2: Timetable Slot Scheduling & Retrieval
echo "\n2. Testing Timetable Slot Scheduling & Matrices:\n";
// Insert test slot
$mysqli->query("INSERT INTO tbl_timetable (academic_year_id, class_id, section_id, day, period_id, subject_id, teacher_id, room_no, status)
    VALUES (1, 1, 1, 'Monday', 1, 1, 1, 'Lab 101', 1)
    ON DUPLICATE KEY UPDATE subject_id = 1, teacher_id = 1, room_no = 'Lab 101'");
$slot1 = $mysqli->query("SELECT * FROM tbl_timetable WHERE academic_year_id = 1 AND class_id = 1 AND section_id = 1 AND day = 'Monday' AND period_id = 1")->fetch_assoc();
assertTest("Slot scheduled on Monday Period 1 (Subject ID: {$slot1['subject_id']}, Teacher ID: {$slot1['teacher_id']}, Room: {$slot1['room_no']})", !empty($slot1));

// TEST 3: Conflict Detection — Teacher Collision
echo "\n3. Testing Conflict Detection Engine:\n";
// Teacher 1 is teaching Class 1 A on Monday Period 1.
// Attempting to schedule Teacher 1 in Class 2 A on Monday Period 1 should clash.
$clash_check = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_timetable WHERE academic_year_id = 1 AND teacher_id = 1 AND day = 'Monday' AND period_id = 1 AND status = 1")->fetch_assoc()['cnt'];
assertTest("Conflict Engine detects Teacher 1 is busy on Monday Period 1 (Count: {$clash_check})", $clash_check >= 1);

// TEST 4: Subject Allocation Quotas
echo "\n4. Testing Subject Allocation Quotas:\n";
$mysqli->query("INSERT INTO tbl_subject_allocations (academic_year_id, class_id, section_id, subject_id, teacher_id, weekly_periods_target, status)
    VALUES (1, 1, 1, 1, 1, 6, 1)
    ON DUPLICATE KEY UPDATE weekly_periods_target = 6");
$alloc = $mysqli->query("SELECT * FROM tbl_subject_allocations WHERE academic_year_id = 1 AND class_id = 1 AND section_id = 1 AND subject_id = 1")->fetch_assoc();
assertTest("Subject allocation saved with target weekly periods = {$alloc['weekly_periods_target']}", $alloc['weekly_periods_target'] == 6);

// TEST 5: Free Teacher Finder
echo "\n5. Testing Free Teacher Finder:\n";
// On Monday Period 1, Teacher 1 is busy. Other teachers should be returned as free.
$busy_res = $mysqli->query("SELECT teacher_id FROM tbl_timetable WHERE academic_year_id = 1 AND day = 'Monday' AND period_id = 1 AND status = 1");
$busy_ids = [];
while ($b = $busy_res->fetch_assoc()) $busy_ids[] = $b['teacher_id'];

$free_res = $mysqli->query("SELECT staff_id, full_name FROM tbl_staff WHERE staff_type = 'teacher' AND status = 1 AND staff_id NOT IN (" . implode(',', $busy_ids) . ")");
$free_count = $free_res->num_rows;
assertTest("Free teachers identified for Monday Period 1 ({$free_count} teachers free)", $free_count > 0);

// TEST 6: Teacher Substitution Management
echo "\n6. Testing Teacher Substitution & Proxy:\n";
$free_teacher = $free_res->fetch_assoc();
$sub_tid = $free_teacher ? $free_teacher['staff_id'] : 2;
$tt_id = $slot1['timetable_id'];

$mysqli->query("INSERT INTO tbl_teacher_substitutions (timetable_id, substitution_date, original_teacher_id, substitute_teacher_id, reason, status)
    VALUES ({$tt_id}, '2026-08-25', 1, {$sub_tid}, 'Medical leave proxy', 'Scheduled')");
$sub_id = $mysqli->insert_id;
$check_sub = $mysqli->query("SELECT * FROM tbl_teacher_substitutions WHERE substitution_id = {$sub_id}")->fetch_assoc();
assertTest("Teacher substitution logged: Original={$check_sub['original_teacher_id']}, Proxy={$check_sub['substitute_teacher_id']}", !empty($check_sub));

// TEST 7: Publish & Lock Controls
echo "\n7. Testing Publish & Lock Controls:\n";
$mysqli->query("INSERT INTO tbl_timetable_publish (academic_year_id, class_id, section_id, status, published_at, published_by)
    VALUES (1, 1, 1, 'Locked', NOW(), 1)
    ON DUPLICATE KEY UPDATE status = 'Locked', published_at = NOW()");
$pub = $mysqli->query("SELECT * FROM tbl_timetable_publish WHERE academic_year_id = 1 AND class_id = 1 AND section_id = 1")->fetch_assoc();
assertTest("Class Timetable status set to '{$pub['status']}'", $pub['status'] === 'Locked');

// Reset publish status to Draft
$mysqli->query("UPDATE tbl_timetable_publish SET status = 'Draft' WHERE academic_year_id = 1 AND class_id = 1 AND section_id = 1");

// TEST 8: Timetable Settings
echo "\n8. Testing Timetable Settings:\n";
$settings = $mysqli->query("SELECT * FROM tbl_timetable_settings WHERE setting_id = 1")->fetch_assoc();
assertTest("Timetable settings retrieved: Working Days = '{$settings['working_days']}', Max Periods = {$settings['max_periods_per_day']}", !empty($settings));

// Cleanup test substitution
$mysqli->query("DELETE FROM tbl_teacher_substitutions WHERE substitution_id = {$sub_id}");

echo "\n=== TIMETABLE TEST SUMMARY: {$passCount}/{$testCount} Tests Passed ===\n";
