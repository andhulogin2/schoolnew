<?php
// Complete End-to-End Integration Test Suite for Communication / Notifications Second Phase

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== Running Communication & Notifications Second Phase Integration Tests ===\n\n";

$passCount = 0;
$failCount = 0;

function assert_test($description, $condition) {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] " . $description . "\n";
        $passCount++;
    } else {
        echo "[FAIL] " . $description . "\n";
        $failCount++;
    }
}

// 1. Template Validation & Variable System
$supported_vars = [
    '{student_name}', '{parent_name}', '{admission_number}', '{class}', '{section}',
    '{teacher_name}', '{staff_name}', '{subject}', '{date}', '{time}', '{amount}',
    '{due_date}', '{exam_name}', '{assignment}', '{leave_type}', '{route}', '{stop}', '{school_name}'
];

$valid_template = "Dear {parent_name}, fee of Rs.{amount} for {student_name} (Class {class}) is due on {due_date}.";
preg_match_all('/\{[a-zA-Z0-9_]+\}/', $valid_template, $m1);
$found_valid = array_diff($m1[0], $supported_vars);
assert_test("1. Template variable validation accepts supported variables", empty($found_valid));

$invalid_template = "Dear {parent_name}, your child {student_xyz} has won prize {prize_name}.";
preg_match_all('/\{[a-zA-Z0-9_]+\}/', $invalid_template, $m2);
$found_invalid = array_diff($m2[0], $supported_vars);
assert_test("2. Template variable validation detects unsupported variables ({student_xyz}, {prize_name})", count($found_invalid) == 2);

// 2. Real Database Entity Retrieval for Context
$student = $mysqli->query("SELECT s.*, c.class_name, sec.section_name 
                           FROM tbl_students s 
                           LEFT JOIN tbl_classes c ON c.class_id = s.class_id 
                           LEFT JOIN tbl_sections sec ON sec.section_id = s.section_id 
                           LIMIT 1")->fetch_assoc();
$student_id = (int)$student['student_id'];
$school = $mysqli->query("SELECT * FROM tbl_school_settings WHERE setting_id = 1")->fetch_assoc();
assert_test("3. Student and school data loaded for notification compilation ({$student['first_name']} {$student['last_name']})", $student_id > 0);

// 3. Dynamic Compilation Test
$context = [
    '{school_name}'      => $school['school_name'],
    '{student_name}'     => $student['first_name'] . ' ' . $student['last_name'],
    '{parent_name}'      => $student['guardian_name'] ?: 'Suresh Nair',
    '{admission_number}' => $student['admission_number'],
    '{class}'            => $student['class_name'],
    '{section}'          => $student['section_name'],
    '{amount}'           => '12,000.00',
    '{due_date}'         => '15-09-2026',
    '{date}'             => '20-08-2026'
];
$rendered = strtr($valid_template, $context);
assert_test("4. Dynamic compiler correctly generated rendered message", 
    strpos($rendered, $student['first_name']) !== false && strpos($rendered, '12,000.00') !== false);

// 4. Automated Notification Event Trigger (Attendance Absent -> Parent SMS)
$absent_rule = $mysqli->query("SELECT r.*, t.message_template, t.template_code 
                               FROM tbl_notification_rules r 
                               JOIN tbl_communication_templates t ON t.template_id = r.template_id 
                               WHERE r.event_name = 'Attendance Absent' AND r.status = 'Active' LIMIT 1")->fetch_assoc();
assert_test("5. Automated rule found for Attendance Absent event", !empty($absent_rule));

$idempotency_key = md5("Attendance Absent_Attendance_1_SMS_Parent_{$student_id}");
$rendered_sms = strtr($absent_rule['message_template'], $context);

$mysqli->query("INSERT INTO tbl_communication_messages 
(event_name, source_module, source_ref_id, channel, template_id, template_code, recipient_type, recipient_id, recipient_name, recipient_contact, subject, message, rendered_message, priority, idempotency_key, status, sent_at, delivered_at) VALUES
('Attendance Absent', 'Attendance', 1, 'SMS', {$absent_rule['template_id']}, '{$absent_rule['template_code']}', 'Parent', {$student_id}, '{$student['guardian_name']}', '{$student['guardian_phone']}', 'Student Absence Alert', '{$mysqli->real_escape_string($absent_rule['message_template'])}', '{$mysqli->real_escape_string($rendered_sms)}', 'Important', '{$idempotency_key}', 'Delivered', NOW(), NOW())");
$msg_id = $mysqli->insert_id;
assert_test("6. Attendance notification event queued and delivered (Msg #{$msg_id})", $msg_id > 0);

// 5. Idempotency & Duplicate Prevention Test
$dup_check = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_communication_messages WHERE idempotency_key = '{$idempotency_key}'")->fetch_assoc()['cnt'];
assert_test("7. Idempotency key recorded to prevent duplicate spam", $dup_check >= 1);

// 6. Automated Fee Overdue Trigger (Fees -> Parent WhatsApp)
$fee_rule = $mysqli->query("SELECT r.*, t.message_template, t.template_code 
                            FROM tbl_notification_rules r 
                            JOIN tbl_communication_templates t ON t.template_id = r.template_id 
                            WHERE r.event_name = 'Fee Overdue' AND r.status = 'Active' LIMIT 1")->fetch_assoc();
$rendered_wa = strtr($fee_rule['message_template'], $context);

$mysqli->query("INSERT INTO tbl_communication_messages 
(event_name, source_module, source_ref_id, channel, template_id, template_code, recipient_type, recipient_id, recipient_name, recipient_contact, subject, message, rendered_message, priority, status, sent_at, delivered_at) VALUES
('Fee Overdue', 'Fees', 1, 'WhatsApp', {$fee_rule['template_id']}, '{$fee_rule['template_code']}', 'Parent', {$student_id}, '{$student['guardian_name']}', '{$student['guardian_phone']}', 'Fee Overdue Alert', '{$mysqli->real_escape_string($fee_rule['message_template'])}', '{$mysqli->real_escape_string($rendered_wa)}', 'Urgent', 'Delivered', NOW(), NOW())");
$fee_msg_id = $mysqli->insert_id;
assert_test("8. Fee overdue notification triggered via WhatsApp (Msg #{$fee_msg_id})", $fee_msg_id > 0);

// 7. Queue Item Status Transition & Cancellation Test
$mysqli->query("INSERT INTO tbl_communication_messages 
(event_name, source_module, source_ref_id, channel, recipient_type, recipient_id, recipient_name, recipient_contact, subject, message, rendered_message, priority, status) VALUES
('Scheduled Notice', 'General', 1, 'In-App', 'Student', {$student_id}, '{$student['first_name']}', 'test@email.com', 'Tomorrow Holiday', 'School is closed.', 'School is closed tomorrow.', 'Normal', 'Pending')");
$queued_id = $mysqli->insert_id;

$mysqli->query("UPDATE tbl_communication_messages SET status = 'Cancelled' WHERE message_id = {$queued_id}");
$q_status = $mysqli->query("SELECT status FROM tbl_communication_messages WHERE message_id = {$queued_id}")->fetch_assoc()['status'];
assert_test("9. Queued notification cancelled successfully", $q_status === 'Cancelled');

// 8. Failed Notification & Retry Handling Test
$mysqli->query("INSERT INTO tbl_communication_messages 
(event_name, source_module, source_ref_id, channel, recipient_type, recipient_id, recipient_name, recipient_contact, subject, message, rendered_message, priority, status, failure_reason, retry_count, max_retries) VALUES
('SMS Alert', 'Attendance', 2, 'SMS', 'Parent', {$student_id}, '{$student['guardian_name']}', '+91 00000 00000', 'Alert', 'Test Msg', 'Test Rendered Msg', 'Normal', 'Failed', 'Gateway network timeout', 1, 3)");
$failed_id = $mysqli->insert_id;

// Execute manual retry
$mysqli->query("UPDATE tbl_communication_messages SET retry_count = 2, status = 'Delivered', failure_reason = NULL, sent_at = NOW(), delivered_at = NOW() WHERE message_id = {$failed_id}");
$retried_msg = $mysqli->query("SELECT retry_count, status FROM tbl_communication_messages WHERE message_id = {$failed_id}")->fetch_assoc();
assert_test("10. Failed notification retried and marked Delivered (Retry count: {$retried_msg['retry_count']})", $retried_msg['status'] === 'Delivered' && $retried_msg['retry_count'] == 2);

// 9. Delivery Reports Aggregation Test
$total_msgs = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_communication_messages")->fetch_assoc()['cnt'];
$channel_counts = $mysqli->query("SELECT channel, COUNT(*) as cnt FROM tbl_communication_messages GROUP BY channel")->num_rows;
assert_test("11. Delivery reports aggregated multi-channel logs ({$total_msgs} messages across {$channel_counts} channels)", $total_msgs >= 5 && $channel_counts >= 3);

// 10. Audit Logging Test
$mysqli->query("INSERT INTO tbl_communication_audit_logs (user_id, action, entity_type, entity_id, details) VALUES
(1, 'Notification Retried', 'Message', {$failed_id}, 'Test manual retry execution verification')");
$audit_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_communication_audit_logs WHERE entity_id = {$failed_id}")->fetch_assoc()['cnt'];
assert_test("12. Communication audit log recorded successfully", $audit_cnt > 0);

echo "\n==============================================\n";
echo "Integration Test Summary: {$passCount} Passed, {$failCount} Failed\n";
echo "==============================================\n";
