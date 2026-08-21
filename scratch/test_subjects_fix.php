<?php
$db = mysqli_connect('localhost', 'root', '', 'db_school');
if (!$db) die('Connect failed: ' . mysqli_connect_error());

echo "=== Test 1: get_for_class(1, 1, 1) — via allocations ===\n";
$r = mysqli_query($db, "
    SELECT sub.subject_id, sub.subject_name, sub.subject_code, sub.subject_type,
           sub.class_id, sub.teacher_id, sub.status,
           c.class_name, s.full_name as teacher_name,
           sa.allocation_id, sa.weekly_periods_target
    FROM tbl_subject_allocations sa
    LEFT JOIN tbl_subjects sub ON sub.subject_id = sa.subject_id
    LEFT JOIN tbl_classes c ON c.class_id = sa.class_id
    LEFT JOIN tbl_staff s ON s.staff_id = sa.teacher_id
    WHERE sa.academic_year_id = 1
      AND sa.class_id = 1
      AND sa.section_id = 1
      AND sa.status = 1
      AND sub.status = 1
    ORDER BY sub.subject_name ASC
");
if (!$r) echo "Error: " . mysqli_error($db) . "\n";
else {
    $count = 0;
    while ($row = mysqli_fetch_assoc($r)) {
        echo "Subject: " . $row['subject_name'] . " (" . $row['subject_code'] . ") | ID=" . $row['subject_id'] . "\n";
        $count++;
    }
    echo "$count subjects returned.\n";
}

echo "\n=== Test 2: get_for_class(1, 88, 8) — class=88 via allocations ===\n";
$r = mysqli_query($db, "
    SELECT sa.*, sub.subject_name
    FROM tbl_subject_allocations sa
    LEFT JOIN tbl_subjects sub ON sub.subject_id = sa.subject_id
    WHERE sa.academic_year_id = 1 AND sa.class_id = 88
");
$count = 0;
while ($row = mysqli_fetch_assoc($r)) { echo implode(' | ', $row) . "\n"; $count++; }
echo "$count rows via allocations.\n";

echo "\n=== Test 2b: fallback — class_id=88 in tbl_subjects ===\n";
$r = mysqli_query($db, "SELECT subject_id, subject_name, status FROM tbl_subjects WHERE class_id=88");
$count = 0;
while ($row = mysqli_fetch_assoc($r)) { echo implode(' | ', $row) . "\n"; $count++; }
echo "$count rows via fallback.\n";

echo "\n=== Test 3: What actual classes exist in tbl_classes (first 20)? ===\n";
$r = mysqli_query($db, "SELECT class_id, class_name, status FROM tbl_classes ORDER BY class_id LIMIT 20");
while ($row = mysqli_fetch_assoc($r)) echo $row['class_id'] . " | " . $row['class_name'] . " | status=" . $row['status'] . "\n";

echo "\n=== Test 4: Route 200 check ===\n";
$ch = curl_init('http://localhost/schoolnew/timetable/builder?academic_year_id=1&class_id=1&section_id=1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$html = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "HTTP $code\n";
if (strpos($html, 'A Database Error Occurred') !== false) echo "DB ERROR found in response!\n";
elseif (strpos($html, 'Choose Subject') !== false) echo "Subject dropdown found.\n";
elseif (strpos($html, 'No subjects assigned') !== false) echo "Empty state shown (no subjects for this class).\n";
else echo "Response doesn't match expected patterns.\n";

echo "\n=== DONE ===\n";
