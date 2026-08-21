<?php
$db = mysqli_connect('localhost', 'root', '', 'db_school');
if (!$db) die('Connect failed: ' . mysqli_connect_error());

// 1. Subject allocation table structure
echo "=== tbl_subject_allocations columns ===\n";
$r = mysqli_query($db, 'DESCRIBE tbl_subject_allocations');
while ($row = mysqli_fetch_assoc($r)) echo $row['Field'] . ' | ' . $row['Type'] . ' | Default=' . $row['Default'] . "\n";

// 2. Sample allocation data
echo "\n=== tbl_subject_allocations sample (first 20 rows) ===\n";
$r = mysqli_query($db, 'SELECT * FROM tbl_subject_allocations LIMIT 20');
while ($row = mysqli_fetch_assoc($r)) echo implode(' | ', $row) . "\n";

// 3. Allocations for class=88
echo "\n=== Allocations for class_id=88 ===\n";
$r = mysqli_query($db, 'SELECT * FROM tbl_subject_allocations WHERE class_id=88 LIMIT 20');
if (!$r) echo 'Error: ' . mysqli_error($db) . "\n";
else {
    $count = 0;
    while ($row = mysqli_fetch_assoc($r)) { echo implode(' | ', $row) . "\n"; $count++; }
    echo "$count rows.\n";
}

// 4. Allocations for academic_year_id=1
echo "\n=== Allocations for academic_year_id=1 (first 20) ===\n";
$r = mysqli_query($db, 'SELECT * FROM tbl_subject_allocations WHERE academic_year_id=1 LIMIT 20');
if (!$r) echo 'Error: ' . mysqli_error($db) . "\n";
else {
    $count = 0;
    while ($row = mysqli_fetch_assoc($r)) { echo implode(' | ', $row) . "\n"; $count++; }
    echo "$count rows.\n";
}

// 5. What distinct classes have subjects in tbl_subjects?
echo "\n=== Distinct class_ids in tbl_subjects ===\n";
$r = mysqli_query($db, 'SELECT class_id, COUNT(*) as cnt FROM tbl_subjects GROUP BY class_id');
while ($row = mysqli_fetch_assoc($r)) echo "class_id=" . $row['class_id'] . " => " . $row['cnt'] . " subjects\n";

// 6. What class is class_id=88 anyway?
echo "\n=== tbl_classes for class_id=88 and nearby ===\n";
$r = mysqli_query($db, 'SELECT * FROM tbl_classes WHERE class_id BETWEEN 80 AND 95');
while ($row = mysqli_fetch_assoc($r)) echo implode(' | ', $row) . "\n";

// 7. Timetable allocation model
echo "\n=== Timetable_allocation_model table ===\n";
$r = mysqli_query($db, "SHOW TABLES LIKE '%timetable%'");
while ($row = mysqli_fetch_row($r)) echo $row[0] . "\n";

// 8. Try subject allocations join with subjects
echo "\n=== Subjects via tbl_subject_allocations for class=88 ===\n";
$r = mysqli_query($db, "
    SELECT sa.*, sub.subject_name, sub.subject_code 
    FROM tbl_subject_allocations sa
    JOIN tbl_subjects sub ON sub.subject_id = sa.subject_id
    WHERE sa.class_id = 88
    LIMIT 20
");
if (!$r) echo 'Error: ' . mysqli_error($db) . "\n";
else {
    $count = 0;
    while ($row = mysqli_fetch_assoc($r)) { echo implode(' | ', $row) . "\n"; $count++; }
    echo "$count rows.\n";
}
