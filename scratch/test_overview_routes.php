<?php
$routes = [
    'dashboard',
    'students',
    'students/overview',
    'staff',
    'staff/overview',
    'academics',
    'academics/overview',
    'attendance',
    'attendance/overview',
    'examinations',
    'examinations/overview',
    'fees',
    'fees/overview',
    'timetable',
    'timetable/overview',
    'homework',
    'homework/overview',
    'communication',
    'communication/overview',
    'leave',
    'leave/overview',
    'transport',
    'transport/overview',
    'certificates',
    'certificates/overview',
    'reports',
    'reports/overview',
    'users',
    'users/overview',
    'settings',
    'settings/overview'
];

$failed = 0;
foreach ($routes as $r) {
    $ch = curl_init('http://localhost/schoolnew/' . $r);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $h = curl_exec($ch);
    $c = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $u = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    $hasErr = (strpos($h, 'An uncaught Exception') !== false || strpos($h, 'A Database Error') !== false || strpos($u, 'unauthorized') !== false || $c !== 200);
    if ($hasErr) {
        echo "FAIL [$r]: $c | $u\n";
        $failed++;
    } else {
        echo "OK [$r]: $c | $u\n";
    }
}

if ($failed === 0) {
    echo "\nSUCCESS: ALL OVERVIEW ROUTES RETURNED 200 OK WITH NO ERRORS!\n";
} else {
    echo "\nFAIL: $failed overview routes failed.\n";
}
