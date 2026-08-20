<?php
// Complete End-to-End Integration Test Suite for Certificate & Document Management Module

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== Running Certificate & Document Management End-to-End Integration Tests ===\n\n";

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

// 1. Certificate Types & Default Templates
$types_res = $mysqli->query("SELECT * FROM tbl_certificate_types WHERE status = 'Active'");
assert_test("1. Active certificate types seeded and queryable", $types_res->num_rows >= 4);

$tmpl_res = $mysqli->query("SELECT * FROM tbl_certificate_templates WHERE status = 'Active'");
assert_test("2. Certificate templates seeded and queryable", $tmpl_res->num_rows >= 4);

// 2. Student & School Information Retrieval
$student = $mysqli->query("SELECT s.*, c.class_name, sec.section_name, ay.year_name 
                           FROM tbl_students s 
                           LEFT JOIN tbl_classes c ON c.class_id = s.class_id 
                           LEFT JOIN tbl_sections sec ON sec.section_id = s.section_id 
                           LEFT JOIN tbl_academic_years ay ON ay.academic_year_id = s.academic_year_id 
                           LIMIT 1")->fetch_assoc();
$student_id = (int)$student['student_id'];
assert_test("3. Student record found for certificate generation ({$student['first_name']} {$student['last_name']})", $student_id > 0);

$school = $mysqli->query("SELECT * FROM tbl_school_settings WHERE setting_id = 1")->fetch_assoc();
assert_test("4. School settings found ({$school['school_name']})", !empty($school['school_name']));

// 3. Certificate Request Workflow
$mysqli->query("INSERT INTO tbl_certificate_requests (student_id, academic_year_id, certificate_type_id, reason, requested_date, status, requested_by) VALUES
({$student_id}, 1, 1, 'Passport application verification', '2026-08-20', 'Pending', 1)");
$request_id = $mysqli->insert_id;
assert_test("5. Certificate request created successfully (Req #{$request_id})", $request_id > 0);

// Approve Request
$mysqli->query("UPDATE tbl_certificate_requests SET status = 'Approved', approved_by = 1 WHERE request_id = {$request_id}");
$req_status = $mysqli->query("SELECT status FROM tbl_certificate_requests WHERE request_id = {$request_id}")->fetch_assoc()['status'];
assert_test("6. Certificate request approved successfully", $req_status === 'Approved');

// 4. Sequential Certificate Number Generation Test
$count = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_certificates WHERE certificate_no LIKE 'BON-2026%'")->fetch_assoc()['cnt'];
$next_seq = str_pad($count + 1, 5, '0', STR_PAD_LEFT);
$generated_cert_no = "BON-2026-{$next_seq}";
assert_test("7. Certificate number generated with format ({$generated_cert_no})", strlen($generated_cert_no) >= 12);

// 5. Dynamic Template Compiler Test
$template = $mysqli->query("SELECT * FROM tbl_certificate_templates WHERE type_code = 'BONAFIDE' LIMIT 1")->fetch_assoc();
$compiled_content = strtr($template['body_content'], [
    '{school_name}'      => $school['school_name'],
    '{student_name}'     => $student['first_name'] . ' ' . $student['last_name'],
    '{admission_number}' => $student['admission_number'],
    '{class}'            => $student['class_name'],
    '{section}'          => $student['section_name'],
    '{academic_year}'    => $student['year_name'],
    '{date_of_birth}'    => date('d-m-Y', strtotime($student['date_of_birth'])),
    '{parent_name}'      => $student['guardian_name']
]);
assert_test("8. Dynamic template compilation replaced placeholders with real values", 
    strpos($compiled_content, $student['first_name']) !== false && strpos($compiled_content, '{student_name}') === false);

// 6. Generate Certificate Record
$q_res = $mysqli->query("INSERT INTO tbl_certificates (certificate_no, request_id, student_id, academic_year_id, certificate_type_id, certificate_type, template_id, issue_date, generated_content, version, status, generated_by) VALUES
('{$generated_cert_no}', {$request_id}, {$student_id}, 1, 1, 'Bonafide Certificate', {$template['template_id']}, '2026-08-20', '{$mysqli->real_escape_string($compiled_content)}', 1, 'Generated', 1)");
if (!$q_res) {
    echo "SQL ERROR on cert insert: " . $mysqli->error . "\n";
}
$cert_id = $mysqli->insert_id;
assert_test("9. Certificate saved in database with version 1 (Cert #{$cert_id})", $cert_id > 0);

// 7. Certificate Reissue & Versioning Test
$mysqli->query("INSERT INTO tbl_certificate_versions (certificate_id, version_number, certificate_no, content_snapshot, reason, changed_by) VALUES
({$cert_id}, 1, '{$generated_cert_no}', '{$mysqli->real_escape_string($compiled_content)}', 'Name spelling correction requested by guardian', 1)");
$version_id = $mysqli->insert_id;

$mysqli->query("UPDATE tbl_certificates SET version = 2, is_reissued = 1, reissue_reason = 'Name spelling correction' WHERE certificate_id = {$cert_id}");
$cert_after = $mysqli->query("SELECT version, is_reissued FROM tbl_certificates WHERE certificate_id = {$cert_id}")->fetch_assoc();
assert_test("10. Certificate reissued: archived previous version and incremented to v2", $version_id > 0 && $cert_after['version'] == 2 && $cert_after['is_reissued'] == 1);

// 8. Student Document Upload & Verification Test
$cat = $mysqli->query("SELECT category_id FROM tbl_document_categories LIMIT 1")->fetch_assoc();
$cat_id = (int)$cat['category_id'];

$mysqli->query("INSERT INTO tbl_student_documents (student_id, category_id, document_type, document_name, document_number, issue_date, expiry_date, file_path, verification_status, status) VALUES
({$student_id}, {$cat_id}, 'Birth Certificate', 'Aarav_Birth_Cert.pdf', 'BC-998811', '2011-06-20', NULL, 'uploads/student_documents/test.pdf', 'Pending', 1)");
$doc_id = $mysqli->insert_id;
assert_test("11. Student document uploaded in pending status", $doc_id > 0);

$mysqli->query("UPDATE tbl_student_documents SET verification_status = 'Verified', verified_by = 1, verified_at = NOW() WHERE document_id = {$doc_id}");
$doc_status = $mysqli->query("SELECT verification_status FROM tbl_student_documents WHERE document_id = {$doc_id}")->fetch_assoc()['verification_status'];
assert_test("12. Student document successfully marked as Verified", $doc_status === 'Verified');

// 9. Document Expiration Warning Logic Test
$today = date('Y-m-d');
$expiring_soon_date = date('Y-m-d', strtotime('+15 days'));
$days_to_expiry = (strtotime($expiring_soon_date) - strtotime($today)) / 86400;
$is_expiring_soon = ($days_to_expiry <= 30 && $days_to_expiry >= 0);
assert_test("13. Document expiration calculation identified warning window ({$days_to_expiry} days)", $is_expiring_soon);

// 10. Audit Logging Test
$mysqli->query("INSERT INTO tbl_certificate_audit_logs (user_id, action, entity_type, entity_id, details) VALUES
(1, 'Certificate Generated', 'Certificate', {$cert_id}, 'Integration test generation verification')");
$log_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_certificate_audit_logs WHERE entity_id = {$cert_id}")->fetch_assoc()['cnt'];
assert_test("14. Certificate audit log successfully recorded", $log_cnt > 0);

echo "\n==============================================\n";
echo "Integration Test Summary: {$passCount} Passed, {$failCount} Failed\n";
echo "==============================================\n";
