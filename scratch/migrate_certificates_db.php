<?php
// Migration and Seed script for Certificate & Document Management Module

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== Running Certificate & Document Management Database Migration ===\n";

// 1. tbl_certificate_types
echo "1. Creating tbl_certificate_types...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_certificate_types` (
  `type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_name` VARCHAR(100) NOT NULL,
  `type_code` VARCHAR(50) NOT NULL,
  `prefix` VARCHAR(20) NOT NULL DEFAULT 'CERT-',
  `description` TEXT NULL,
  `is_system` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `uk_cert_type_code` (`type_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 2. tbl_certificate_templates
echo "2. Creating tbl_certificate_templates...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_certificate_templates` (
  `template_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_name` VARCHAR(100) NOT NULL,
  `type_code` VARCHAR(50) NOT NULL,
  `header_content` TEXT NULL,
  `body_content` LONGTEXT NOT NULL,
  `footer_content` TEXT NULL,
  `logo_position` ENUM('Top-Left', 'Top-Center', 'Top-Right', 'None') NOT NULL DEFAULT 'Top-Center',
  `signature_layout` ENUM('Principal-Only', 'Principal-And-Officer', 'Officer-Only') NOT NULL DEFAULT 'Principal-Only',
  `paper_size` ENUM('A4', 'Letter', 'Legal') NOT NULL DEFAULT 'A4',
  `orientation` ENUM('Portrait', 'Landscape') NOT NULL DEFAULT 'Portrait',
  `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`template_id`),
  KEY `idx_tmpl_code` (`type_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 3. tbl_certificate_requests
echo "3. Creating tbl_certificate_requests...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_certificate_requests` (
  `request_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `academic_year_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `certificate_type_id` INT UNSIGNED NOT NULL,
  `reason` TEXT NOT NULL,
  `requested_date` DATE NOT NULL,
  `required_date` DATE NULL,
  `remarks` TEXT NULL,
  `supporting_document` VARCHAR(255) NULL,
  `status` ENUM('Draft', 'Pending', 'Under Verification', 'Correction Required', 'Approved', 'Rejected', 'Generated', 'Printed', 'Issued', 'Cancelled') NOT NULL DEFAULT 'Pending',
  `rejection_reason` TEXT NULL,
  `requested_by` INT UNSIGNED NULL,
  `verified_by` INT UNSIGNED NULL,
  `approved_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_id`),
  KEY `idx_cr_student` (`student_id`),
  KEY `idx_cr_type` (`certificate_type_id`),
  KEY `idx_cr_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 4. tbl_certificates (Upgrade / recreate if necessary)
echo "4. Upgrading tbl_certificates...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_certificates` (
  `certificate_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `certificate_no` VARCHAR(50) NOT NULL,
  `request_id` INT UNSIGNED NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `academic_year_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `certificate_type_id` INT UNSIGNED NULL,
  `certificate_type` VARCHAR(100) NOT NULL,
  `template_id` INT UNSIGNED NULL,
  `issue_date` DATE NOT NULL,
  `student_data_snapshot` JSON NULL,
  `generated_content` LONGTEXT NULL,
  `version` INT UNSIGNED NOT NULL DEFAULT 1,
  `is_reissued` TINYINT(1) NOT NULL DEFAULT 0,
  `reissue_reason` TEXT NULL,
  `remarks` TEXT NULL,
  `generated_by` INT UNSIGNED NULL,
  `issued_by` INT UNSIGNED NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'Generated',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`certificate_id`),
  UNIQUE KEY `uk_cert_no` (`certificate_no`),
  KEY `idx_cert_student` (`student_id`),
  KEY `idx_cert_req` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Add missing columns to tbl_certificates if table existed previously
$existing_cols = [];
$c_res = $mysqli->query("SHOW COLUMNS FROM tbl_certificates");
while($c = $c_res->fetch_assoc()) $existing_cols[] = $c['Field'];

if (!in_array('request_id', $existing_cols)) {
    $mysqli->query("ALTER TABLE tbl_certificates ADD COLUMN `request_id` INT UNSIGNED NULL AFTER `certificate_no`");
}
if (!in_array('academic_year_id', $existing_cols)) {
    $mysqli->query("ALTER TABLE tbl_certificates ADD COLUMN `academic_year_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `student_id`");
}
if (!in_array('certificate_type_id', $existing_cols)) {
    $mysqli->query("ALTER TABLE tbl_certificates ADD COLUMN `certificate_type_id` INT UNSIGNED NULL AFTER `academic_year_id`");
}
if (!in_array('template_id', $existing_cols)) {
    $mysqli->query("ALTER TABLE tbl_certificates ADD COLUMN `template_id` INT UNSIGNED NULL AFTER `certificate_type`");
}
if (!in_array('student_data_snapshot', $existing_cols)) {
    $mysqli->query("ALTER TABLE tbl_certificates ADD COLUMN `student_data_snapshot` JSON NULL AFTER `issue_date`");
}
if (!in_array('generated_content', $existing_cols)) {
    $mysqli->query("ALTER TABLE tbl_certificates ADD COLUMN `generated_content` LONGTEXT NULL AFTER `student_data_snapshot`");
}
if (!in_array('version', $existing_cols)) {
    $mysqli->query("ALTER TABLE tbl_certificates ADD COLUMN `version` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `generated_content`");
}
if (!in_array('is_reissued', $existing_cols)) {
    $mysqli->query("ALTER TABLE tbl_certificates ADD COLUMN `is_reissued` TINYINT(1) NOT NULL DEFAULT 0 AFTER `version`");
}
if (!in_array('reissue_reason', $existing_cols)) {
    $mysqli->query("ALTER TABLE tbl_certificates ADD COLUMN `reissue_reason` TEXT NULL AFTER `is_reissued`");
}
if (!in_array('generated_by', $existing_cols)) {
    $mysqli->query("ALTER TABLE tbl_certificates ADD COLUMN `generated_by` INT UNSIGNED NULL AFTER `remarks`");
}
if (!in_array('issued_by', $existing_cols)) {
    $mysqli->query("ALTER TABLE tbl_certificates ADD COLUMN `issued_by` INT UNSIGNED NULL AFTER `generated_by`");
}
$mysqli->query("ALTER TABLE tbl_certificates MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'Generated'");

// 5. tbl_certificate_versions
echo "5. Creating tbl_certificate_versions...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_certificate_versions` (
  `version_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `certificate_id` INT UNSIGNED NOT NULL,
  `version_number` INT UNSIGNED NOT NULL,
  `certificate_no` VARCHAR(50) NOT NULL,
  `content_snapshot` LONGTEXT NOT NULL,
  `reason` TEXT NOT NULL,
  `changed_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`version_id`),
  KEY `idx_cv_cert` (`certificate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 6. tbl_document_categories
echo "6. Creating tbl_document_categories...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_document_categories` (
  `category_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_name` VARCHAR(100) NOT NULL,
  `code` VARCHAR(50) NOT NULL,
  `description` TEXT NULL,
  `applicable_to` ENUM('All', 'Student', 'Staff') NOT NULL DEFAULT 'Student',
  `is_required` TINYINT(1) NOT NULL DEFAULT 0,
  `expiry_required` TINYINT(1) NOT NULL DEFAULT 0,
  `verification_required` TINYINT(1) NOT NULL DEFAULT 1,
  `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `uk_doc_cat_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 7. tbl_student_documents (Upgrade)
echo "7. Upgrading tbl_student_documents...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_student_documents` (
  `document_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NULL,
  `document_type` VARCHAR(100) NOT NULL,
  `document_name` VARCHAR(255) NOT NULL,
  `document_number` VARCHAR(100) NULL,
  `issue_date` DATE NULL,
  `expiry_date` DATE NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `remarks` TEXT NULL,
  `verification_status` ENUM('Pending', 'Verified', 'Rejected', 'Expired') NOT NULL DEFAULT 'Pending',
  `rejection_reason` TEXT NULL,
  `verified_by` INT UNSIGNED NULL,
  `verified_at` DATETIME NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`document_id`),
  KEY `idx_sd_student` (`student_id`),
  KEY `idx_sd_cat` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Add missing columns in tbl_student_documents if table existed
$doc_cols = [];
$d_res = $mysqli->query("SHOW COLUMNS FROM tbl_student_documents");
while($d = $d_res->fetch_assoc()) $doc_cols[] = $d['Field'];

if (!in_array('category_id', $doc_cols)) {
    $mysqli->query("ALTER TABLE tbl_student_documents ADD COLUMN `category_id` INT UNSIGNED NULL AFTER `student_id`");
}
if (!in_array('document_number', $doc_cols)) {
    $mysqli->query("ALTER TABLE tbl_student_documents ADD COLUMN `document_number` VARCHAR(100) NULL AFTER `document_name`");
}
if (!in_array('issue_date', $doc_cols)) {
    $mysqli->query("ALTER TABLE tbl_student_documents ADD COLUMN `issue_date` DATE NULL AFTER `document_number`");
}
if (!in_array('expiry_date', $doc_cols)) {
    $mysqli->query("ALTER TABLE tbl_student_documents ADD COLUMN `expiry_date` DATE NULL AFTER `issue_date`");
}
if (!in_array('verification_status', $doc_cols)) {
    $mysqli->query("ALTER TABLE tbl_student_documents ADD COLUMN `verification_status` ENUM('Pending', 'Verified', 'Rejected', 'Expired') NOT NULL DEFAULT 'Pending' AFTER `file_path`");
}
if (!in_array('rejection_reason', $doc_cols)) {
    $mysqli->query("ALTER TABLE tbl_student_documents ADD COLUMN `rejection_reason` TEXT NULL AFTER `verification_status`");
}
if (!in_array('verified_by', $doc_cols)) {
    $mysqli->query("ALTER TABLE tbl_student_documents ADD COLUMN `verified_by` INT UNSIGNED NULL AFTER `rejection_reason`");
}
if (!in_array('verified_at', $doc_cols)) {
    $mysqli->query("ALTER TABLE tbl_student_documents ADD COLUMN `verified_at` DATETIME NULL AFTER `verified_by`");
}

// 8. tbl_certificate_settings
echo "8. Creating tbl_certificate_settings...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_certificate_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `numbering_format` VARCHAR(100) NOT NULL DEFAULT '{PREFIX}{YEAR}-{NUMBER}',
  `number_sequence_length` INT UNSIGNED NOT NULL DEFAULT 5,
  `require_approval` TINYINT(1) NOT NULL DEFAULT 1,
  `require_document_verification` TINYINT(1) NOT NULL DEFAULT 0,
  `require_fee_clearance_for_tc` TINYINT(1) NOT NULL DEFAULT 1,
  `require_library_clearance_for_tc` TINYINT(1) NOT NULL DEFAULT 0,
  `require_transport_clearance_for_tc` TINYINT(1) NOT NULL DEFAULT 0,
  `principal_signature_path` VARCHAR(255) NULL,
  `authorized_signature_path` VARCHAR(255) NULL,
  `watermark_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `default_paper_size` VARCHAR(20) NOT NULL DEFAULT 'A4',
  `default_orientation` VARCHAR(20) NOT NULL DEFAULT 'Portrait',
  `document_expiry_reminder_days` INT UNSIGNED NOT NULL DEFAULT 30,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 9. tbl_certificate_audit_logs
echo "9. Creating tbl_certificate_audit_logs...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_certificate_audit_logs` (
  `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT UNSIGNED NOT NULL,
  `details` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_cert_audit_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 10. Seed Default Certificate Types & Document Categories & Templates & Settings
echo "10. Seeding default data...\n";

// Certificate Types
$type_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_certificate_types")->fetch_assoc()['cnt'];
if ($type_cnt == 0) {
    $mysqli->query("INSERT INTO tbl_certificate_types (type_name, type_code, prefix, description, is_system, status) VALUES
    ('Bonafide Certificate', 'BONAFIDE', 'BON-', 'Official verification that student is studying in this school', 1, 'Active'),
    ('Transfer Certificate', 'TC', 'TC-', 'Official leaving certificate issued when leaving the institution', 1, 'Active'),
    ('Study Certificate', 'STUDY', 'STU-', 'Official document certifying course of study and academic period', 1, 'Active'),
    ('Conduct Certificate', 'CONDUCT', 'CON-', 'Official certificate certifying character and conduct of student', 1, 'Active'),
    ('Character Certificate', 'CHARACTER', 'CHAR-', 'Official character and discipline reference certificate', 1, 'Active')");
}

// Certificate Templates
$tmpl_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_certificate_templates")->fetch_assoc()['cnt'];
if ($tmpl_cnt == 0) {
    // Bonafide Template
    $bonafide_body = "<p style='font-size: 16px; line-height: 1.8; text-align: justify;'>This is to certify that <strong>{student_name}</strong> (Admission No: <strong>{admission_number}</strong>), child of <strong>{parent_name}</strong>, is a bonafide student of <strong>{school_name}</strong> studying in <strong>{class} - {section}</strong> during the academic year <strong>{academic_year}</strong>.</p><p style='font-size: 16px; line-height: 1.8; text-align: justify;'>According to the school admission register, the date of birth of the student is <strong>{date_of_birth}</strong>. The student bears a good moral character.</p>";

    // Study Certificate Template
    $study_body = "<p style='font-size: 16px; line-height: 1.8; text-align: justify;'>This is to certify that <strong>{student_name}</strong>, Admission No: <strong>{admission_number}</strong>, has successfully pursued education at <strong>{school_name}</strong> in <strong>{class}</strong> during the academic session <strong>{academic_year}</strong>. The student was admitted on <strong>{admission_date}</strong> and has maintained satisfactory academic progress.</p>";

    // Conduct Certificate Template
    $conduct_body = "<p style='font-size: 16px; line-height: 1.8; text-align: justify;'>This is to certify that <strong>{student_name}</strong>, son/daughter of <strong>{parent_name}</strong>, was a student of Class <strong>{class}</strong> in <strong>{school_name}</strong>. During the period of study in this institution, the conduct, character, and general behavior of the student have been found to be <strong>Exemplary and Good</strong>.</p>";

    // Transfer Certificate Template
    $tc_body = "<table style='width:100%; border-collapse: collapse; font-size: 14px; margin-top: 15px;' cellpadding='8'>
      <tr><td style='width:40%; font-weight: bold;'>1. Name of the Pupil:</td><td>{student_name}</td></tr>
      <tr><td style='font-weight: bold;'>2. Admission Number:</td><td>{admission_number}</td></tr>
      <tr><td style='font-weight: bold;'>3. Father's / Guardian's Name:</td><td>{parent_name}</td></tr>
      <tr><td style='font-weight: bold;'>4. Date of Birth (as per register):</td><td>{date_of_birth}</td></tr>
      <tr><td style='font-weight: bold;'>5. Class in which pupil last studied:</td><td>{class} - {section}</td></tr>
      <tr><td style='font-weight: bold;'>6. Academic Year / Session:</td><td>{academic_year}</td></tr>
      <tr><td style='font-weight: bold;'>7. Date of Admission to School:</td><td>{admission_date}</td></tr>
      <tr><td style='font-weight: bold;'>8. Date of Pupil's Leaving:</td><td>{date_of_leaving}</td></tr>
      <tr><td style='font-weight: bold;'>9. Reason for Leaving:</td><td>{reason_for_leaving}</td></tr>
      <tr><td style='font-weight: bold;'>10. Total Working / Attendance Days:</td><td>{attendance_summary}</td></tr>
      <tr><td style='font-weight: bold;'>11. General Conduct and Character:</td><td>Good</td></tr>
      <tr><td style='font-weight: bold;'>12. School Fee Clearance:</td><td>Fully Cleared</td></tr>
    </table>";

    $mysqli->query("INSERT INTO tbl_certificate_templates (template_name, type_code, header_content, body_content, footer_content, logo_position, signature_layout, paper_size, orientation, status) VALUES
    ('Standard Bonafide Certificate', 'BONAFIDE', 'BONAFIDE CERTIFICATE', '{$mysqli->real_escape_string($bonafide_body)}', 'This certificate is issued on request for official verification purposes.', 'Top-Center', 'Principal-Only', 'A4', 'Portrait', 'Active'),
    ('Standard Transfer Certificate', 'TC', 'TRANSFER CERTIFICATE', '{$mysqli->real_escape_string($tc_body)}', 'Certified that the above entries are verified from the institution admission ledger.', 'Top-Center', 'Principal-And-Officer', 'A4', 'Portrait', 'Active'),
    ('Standard Study Certificate', 'STUDY', 'STUDY & ENROLLMENT CERTIFICATE', '{$mysqli->real_escape_string($study_body)}', 'Issued under official seal for higher studies / scholarship.', 'Top-Center', 'Principal-Only', 'A4', 'Portrait', 'Active'),
    ('Standard Conduct Certificate', 'CONDUCT', 'CERTIFICATE OF CHARACTER & CONDUCT', '{$mysqli->real_escape_string($conduct_body)}', 'We wish the student every success in all future endeavors.', 'Top-Center', 'Principal-Only', 'A4', 'Portrait', 'Active')");
}

// Document Categories
$cat_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_document_categories")->fetch_assoc()['cnt'];
if ($cat_cnt == 0) {
    $mysqli->query("INSERT INTO tbl_document_categories (category_name, code, description, applicable_to, is_required, expiry_required, verification_required, status) VALUES
    ('Birth Certificate', 'BIRTH_CERT', 'Official birth certificate issued by municipal/registrar authority', 'Student', 1, 0, 1, 'Active'),
    ('Transfer Certificate (Previous School)', 'PREV_TC', 'Transfer certificate from previous institution', 'Student', 0, 0, 1, 'Active'),
    ('Address Proof / Aadhaar Card', 'ID_PROOF', 'National identity document or residential address proof', 'All', 1, 0, 1, 'Active'),
    ('Medical & Immunization Record', 'MEDICAL_CERT', 'Medical fitness and childhood vaccination record', 'Student', 0, 1, 1, 'Active'),
    ('Passport / Visa Document', 'PASSPORT', 'International student passport / visa identification', 'Student', 0, 1, 1, 'Active'),
    ('Community / Caste Certificate', 'COMMUNITY_CERT', 'Government issued reservation or caste category proof', 'Student', 0, 0, 1, 'Active')");
}

// Settings
$set_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_certificate_settings")->fetch_assoc()['cnt'];
if ($set_cnt == 0) {
    $mysqli->query("INSERT INTO tbl_certificate_settings (setting_id, numbering_format, number_sequence_length, require_approval, require_document_verification, require_fee_clearance_for_tc, require_library_clearance_for_tc, require_transport_clearance_for_tc, watermark_enabled, default_paper_size, default_orientation, document_expiry_reminder_days) VALUES
    (1, '{PREFIX}{YEAR}-{NUMBER}', 5, 1, 0, 1, 0, 0, 1, 'A4', 'Portrait', 30)");
}

// Seed sample requests if table is empty
$req_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_certificate_requests")->fetch_assoc()['cnt'];
if ($req_cnt == 0) {
    $mysqli->query("INSERT INTO tbl_certificate_requests (student_id, academic_year_id, certificate_type_id, reason, requested_date, required_date, status, requested_by) VALUES
    (1, 1, 1, 'Passport application verification requirement', '2026-08-10', '2026-08-15', 'Approved', 1),
    (2, 1, 2, 'Relocating to another city due to parental job transfer', '2026-08-12', '2026-08-20', 'Pending', 1),
    (3, 1, 3, 'National scholarship application submission', '2026-08-14', '2026-08-22', 'Pending', 1),
    (4, 1, 4, 'Participation in state athletic championship', '2026-08-16', '2026-08-25', 'Pending', 1)");
}

echo "=== Certificate & Document Management Migration Completed Successfully! ===\n";
