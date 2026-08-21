<?php
$db = mysqli_connect('localhost', 'root', '', 'db_school');
if (!$db) die('Connect failed: ' . mysqli_connect_error());

// 1. Subject table structure
echo "=== tbl_subjects columns ===\n";
$r = mysqli_query($db, 'DESCRIBE tbl_subjects');
while ($row = mysqli_fetch_assoc($r)) echo $row['Field'] . ' | ' . $row['Type'] . ' | NULL=' . $row['Null'] . "\n";

// 2. All active subjects count
echo "\n=== All subjects (first 20) ===\n";
$r = mysqli_query($db, 'SELECT subject_id, subject_name, subject_code, class_id, status FROM tbl_subjects LIMIT 20');
while ($row = mysqli_fetch_assoc($r)) echo implode(' | ', $row) . "\n";

// 3. Subjects for class 88
echo "\n=== Subjects for class_id=88 ===\n";
$r = mysqli_query($db, 'SELECT subject_id, subject_name, subject_code, class_id, status FROM tbl_subjects WHERE class_id=88');
$count = 0;
while ($row = mysqli_fetch_assoc($r)) { echo implode(' | ', $row) . "\n"; $count++; }
echo "$count rows found.\n";

// 4. Active subjects for class 88
echo "\n=== Active subjects (status=1) for class_id=88 ===\n";
$r = mysqli_query($db, "SELECT subject_id, subject_name, subject_code, class_id, status FROM tbl_subjects WHERE class_id=88 AND status=1");
if (!$r) echo 'Error: ' . mysqli_error($db) . "\n";
else {
    $count = 0;
    while ($row = mysqli_fetch_assoc($r)) { echo implode(' | ', $row) . "\n"; $count++; }
    echo "$count rows.\n";
}

// 5. Check allocation tables
echo "\n=== Tables with 'alloc' ===\n";
$r = mysqli_query($db, "SHOW TABLES LIKE '%alloc%'");
while ($row = mysqli_fetch_row($r)) echo $row[0] . "\n";

// 6. timetable_allocations for year=1, class=88
echo "\n=== tbl_timetable_allocations for year=1, class=88 ===\n";
$r = mysqli_query($db, 'SELECT * FROM tbl_timetable_allocations WHERE academic_year_id=1 AND class_id=88 LIMIT 20');
if (!$r) echo 'Table error: ' . mysqli_error($db) . "\n";
else {
    $count = 0;
    while ($row = mysqli_fetch_assoc($r)) { echo implode(' | ', $row) . "\n"; $count++; }
    echo "$count rows.\n";
}

// 7. Subject_model::get_all(TRUE) simulated
echo "\n=== get_all(TRUE) — called with TRUE (becomes class_id=1) ===\n";
$r = mysqli_query($db, "SELECT sub.subject_id, sub.subject_name, sub.subject_code, sub.class_id, sub.status FROM tbl_subjects sub WHERE sub.status=1 AND sub.class_id=1 ORDER BY sub.subject_name ASC");
if (!$r) echo 'Error: ' . mysqli_error($db) . "\n";
else {
    $count = 0;
    while ($row = mysqli_fetch_assoc($r)) { echo implode(' | ', $row) . "\n"; $count++; }
    echo "$count rows.\n";
}

// 8. Timetable allocation table columns
echo "\n=== tbl_timetable_allocations columns ===\n";
$r = mysqli_query($db, 'DESCRIBE tbl_timetable_allocations');
if (!$r) echo 'Error: ' . mysqli_error($db) . "\n";
else while ($row = mysqli_fetch_assoc($r)) echo $row['Field'] . ' | ' . $row['Type'] . "\n";
