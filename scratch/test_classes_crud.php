<?php
$db = mysqli_connect('localhost', 'root', '', 'db_school');
if (!$db) die("Connect failed: " . mysqli_connect_error());

echo "=== CLASS MANAGEMENT CRUD & DISPLAY TEST ===\n\n";

// 1. Initial listing check
$res = mysqli_query($db, "SELECT * FROM tbl_classes WHERE is_deleted = 'n' AND status = 1");
echo "1. Active classes in DB: " . mysqli_num_rows($res) . "\n";
while ($r = mysqli_fetch_assoc($res)) {
    echo "   - Class ID {$r['class_id']}: {$r['class_name']} ({$r['class_code']}) | Year: {$r['academic_year_id']} | Capacity: {$r['capacity']}\n";
}

// 2. HTTP GET /academics/classes
echo "\n2. Testing HTTP GET /academics/classes:\n";
$ch = curl_init('http://localhost/schoolnew/academics/classes');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$html = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "   - HTTP Code: $code\n";
if (strpos($html, '0 academic class grades configured.') !== false) {
    echo "   FAILED: Still shows 0 academic class grades configured.\n";
} else {
    preg_match('/(\d+)\s+academic class grades configured\./', $html, $m);
    echo "   PASSED: Header shows " . ($m[1] ?? 'N/A') . " academic class grades configured.\n";
}

// 3. Create test class via HTTP POST
echo "\n3. Testing Add Class via HTTP POST:\n";
$postData = [
    'action'           => 'add',
    'academic_year_id' => 1,
    'class_name'       => 'Grade Test Auto',
    'class_code'       => 'CLS-TAUTO',
    'capacity'         => 35,
    'description'      => 'Automated test class'
];

$ch = curl_init('http://localhost/schoolnew/academics/classes');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$html = curl_exec($ch);
curl_close($ch);

$res = mysqli_query($db, "SELECT * FROM tbl_classes WHERE class_code = 'CLS-TAUTO'");
$test_class = mysqli_fetch_assoc($res);
if ($test_class) {
    echo "   PASSED: Created class in DB. ID: {$test_class['class_id']}, status: {$test_class['status']}, is_deleted: '{$test_class['is_deleted']}'\n";
} else {
    echo "   FAILED: Class was not created in DB.\n";
}

$test_id = $test_class['class_id'] ?? null;

if ($test_id) {
    // 4. Test Edit Class via HTTP POST
    echo "\n4. Testing Edit Class via HTTP POST:\n";
    $editData = [
        'action'           => 'edit',
        'class_id'         => $test_id,
        'academic_year_id' => 1,
        'class_name'       => 'Grade Test Auto Updated',
        'class_code'       => 'CLS-TUPD',
        'capacity'         => 38,
        'description'      => 'Updated test description'
    ];
    $ch = curl_init('http://localhost/schoolnew/academics/classes');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($editData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $html = curl_exec($ch);
    curl_close($ch);

    $res = mysqli_query($db, "SELECT * FROM tbl_classes WHERE class_id = $test_id");
    $updated_class = mysqli_fetch_assoc($res);
    if ($updated_class['class_name'] === 'Grade Test Auto Updated') {
        echo "   PASSED: Class name updated to '{$updated_class['class_name']}'\n";
    } else {
        echo "   FAILED: Class update failed. Current name: '{$updated_class['class_name']}'\n";
    }

    // 5. Test Soft Delete Class
    echo "\n5. Testing Soft Delete via /academics/delete_class/$test_id:\n";
    $ch = curl_init("http://localhost/schoolnew/academics/delete_class/$test_id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $html = curl_exec($ch);
    curl_close($ch);

    $res = mysqli_query($db, "SELECT * FROM tbl_classes WHERE class_id = $test_id");
    $deleted_class = mysqli_fetch_assoc($res);
    echo "   - DB state after delete: status = {$deleted_class['status']}, is_deleted = '{$deleted_class['is_deleted']}'\n";
    if ($deleted_class['is_deleted'] === 'y') {
        echo "   PASSED: Soft delete set is_deleted = 'y'\n";
    } else {
        echo "   FAILED: is_deleted is not 'y'\n";
    }

    // 6. Verify excluded from GET /academics/classes
    $ch = curl_init('http://localhost/schoolnew/academics/classes');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $html = curl_exec($ch);
    curl_close($ch);
    if (strpos($html, 'CLS-TUPD') === false) {
        echo "   PASSED: Soft-deleted class does NOT appear on /academics/classes\n";
    } else {
        echo "   FAILED: Soft-deleted class still visible in list.\n";
    }

    // Clean up test class
    mysqli_query($db, "DELETE FROM tbl_classes WHERE class_id = $test_id");
    echo "   - Cleaned up test class.\n";
}

echo "\n=== ALL TESTS COMPLETED ===\n";
