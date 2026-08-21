<?php
$base_url = 'http://localhost/schoolnew/';
$routes = [
    '', 'dashboard', 'students', 'students/overview', 'students/list', 'students/register', 'students/profile',
    'students/documents', 'students/id_cards', 'students/promotion', 'students/transfers',
    'staff', 'staff/overview', 'staff/directory', 'staff/teachers', 'staff/non_teaching', 'staff/departments', 'staff/designations',
    'staff/documents', 'staff/workload', 'staff/attendance', 'staff/leave',
    'academics', 'academics/overview', 'academics/years', 'academics/classes', 'academics/sections', 'academics/subjects',
    'academics/class_teachers', 'academics/subject_teachers', 'academics/timetable', 'academics/calendar',
    'attendance', 'attendance/overview', 'attendance/daily', 'attendance/periods', 'attendance/period_wise',
    'attendance/class_attendance', 'attendance/section_attendance', 'attendance/history',
    'attendance/tracking', 'attendance/calendar', 'attendance/reports', 'attendance/notifications',
    'attendance/notification_history', 'attendance/settings',
    'examinations', 'examinations/overview', 'examinations/exams', 'examinations/types', 'examinations/schedules',
    'examinations/marks_entry', 'examinations/grades', 'examinations/calculate', 'examinations/results',
    'examinations/ranks', 'examinations/report_cards', 'examinations/progress_reports',
    'examinations/publishing', 'examinations/reports', 'examinations/settings',
    'fees', 'fees/overview', 'fees/categories', 'fees/structures', 'fees/assignments', 'fees/student_fees',
    'fees/collection', 'fees/payments', 'fees/receipts', 'fees/discounts', 'fees/due_fees',
    'fees/reminders', 'fees/reminder_history', 'fees/adjustments', 'fees/refunds', 'fees/reports', 'fees/settings',
    'timetable', 'timetable/overview', 'timetable/classes', 'timetable/teachers', 'timetable/allocations',
    'timetable/builder', 'timetable/free_periods', 'timetable/conflicts', 'timetable/publish_lock',
    'timetable/reports', 'timetable/settings',
    'homework', 'homework/overview', 'homework/assignments', 'homework/create', 'homework/types',
    'homework/subjects', 'homework/classes', 'homework/calendar', 'homework/submissions', 'homework/reports', 'homework/settings',
    'communication', 'communication/overview', 'communication/templates', 'communication/sms_templates',
    'communication/whatsapp_templates', 'communication/email_templates', 'communication/automated_notifications',
    'communication/queue', 'communication/history', 'communication/failed', 'communication/reports',
    'communication/settings', 'communication/notices', 'communication/announcements',
    'leave', 'leave/overview', 'leave/student_leave', 'leave/staff_leave', 'leave/types', 'leave/request',
    'leave/approval', 'leave/balances', 'leave/calendar', 'leave/history', 'leave/reports', 'leave/settings',
    'transport', 'transport/overview', 'transport/vehicles', 'transport/drivers', 'transport/routes',
    'transport/stops', 'transport/assignments', 'transport/bulk_assign', 'transport/fees',
    'transport/maintenance', 'transport/maintenance_history', 'transport/documents', 'transport/reports', 'transport/settings',
    'certificates', 'certificates/overview', 'certificates/requests', 'certificates/types', 'certificates/bonafide',
    'certificates/transfer_certificate', 'certificates/study_certificate', 'certificates/conduct_certificate',
    'certificates/generate', 'certificates/templates', 'certificates/documents', 'certificates/document_categories',
    'certificates/document_verification', 'certificates/history',
    'reports', 'reports/overview',
    'users', 'users/overview', 'users/list', 'users/create', 'users/details', 'users/details/1', 'users/roles',
    'users/role_permissions', 'users/role_permissions/1', 'users/permissions', 'users/user_permissions',
    'users/user_permissions/1', 'users/parents', 'users/students', 'users/teachers', 'users/staff',
    'users/login_activity', 'users/security_settings', 'users/audit_logs',
    'settings', 'settings/overview'
];

echo "Testing " . count($routes) . " system routes...\n";
$failed = 0;
foreach ($routes as $r) {
    $ch = curl_init($base_url . $r);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $html = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $eff  = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    $err = ($code !== 200 || strpos($eff, 'unauthorized') !== false
        || strpos($html, 'An uncaught Exception was encountered') !== false
        || strpos($html, 'A Database Error Occurred') !== false);

    if ($err) {
        echo "FAIL [$r]: $code | $eff\n";
        $failed++;
    }
}

if ($failed === 0) {
    echo "\nSUCCESS: ALL " . count($routes) . " ROUTES RETURNED 200 OK WITH NO ERRORS!\n";
} else {
    echo "\nFAILED: $failed routes had issues.\n";
}
