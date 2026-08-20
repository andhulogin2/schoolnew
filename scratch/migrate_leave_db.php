<?php
// Database Migration Script for Leave Management Module

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== Running Leave Management Module Database Migration ===\n";

// 1. tbl_leave_types
echo "1. Creating tbl_leave_types...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_leave_types` (
  `type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_name` VARCHAR(100) NOT NULL,
  `type_code` VARCHAR(20) NOT NULL,
  `applicable_to` ENUM('Students', 'Staff', 'Both') NOT NULL DEFAULT 'Both',
  `description` VARCHAR(255) NULL,
  `max_days` INT UNSIGNED NOT NULL DEFAULT 12,
  `requires_document` TINYINT(1) NOT NULL DEFAULT 0,
  `requires_approval` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_half_day` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_carry_forward` TINYINT(1) NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `uk_leave_code` (`type_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$lt_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_leave_types")->fetch_assoc()['cnt'];
if ($lt_cnt == 0) {
    echo "Seeding default leave types...\n";
    $types = [
        ['Casual Leave', 'CL', 'Staff', 'General casual leave for personal reasons', 12, 0, 1, 1, 1],
        ['Sick / Medical Leave', 'SL', 'Both', 'Leave taken due to illness or medical appointment', 10, 1, 1, 1, 0],
        ['Earned Leave', 'EL', 'Staff', 'Annual accrued privilege leave', 15, 0, 1, 0, 1],
        ['Family Function Leave', 'FFL', 'Students', 'Leave for attending family weddings or ceremonies', 5, 0, 1, 1, 0],
        ['Maternity Leave', 'ML', 'Staff', 'Maternity leave for female staff members', 90, 1, 1, 0, 0],
        ['Paternity Leave', 'PL', 'Staff', 'Paternity leave for male staff members', 15, 1, 1, 0, 0],
        ['Emergency Leave', 'EML', 'Both', 'Unplanned emergency or bereavement leave', 5, 0, 1, 1, 0]
    ];
    $stmt = $mysqli->prepare("INSERT INTO tbl_leave_types (type_name, type_code, applicable_to, description, max_days, requires_document, requires_approval, allow_half_day, allow_carry_forward, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
    foreach ($types as $t) {
        $stmt->bind_param("ssssiiiii", $t[0], $t[1], $t[2], $t[3], $t[4], $t[5], $t[6], $t[7], $t[8]);
        $stmt->execute();
    }
}

// 2. tbl_leave_applications (Universal Leave Applications)
echo "2. Creating tbl_leave_applications...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_leave_applications` (
  `application_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `applicant_type` ENUM('Student', 'Staff') NOT NULL DEFAULT 'Student',
  `student_id` INT UNSIGNED NULL,
  `staff_id` INT UNSIGNED NULL,
  `academic_year_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `class_id` INT UNSIGNED NULL,
  `section_id` INT UNSIGNED NULL,
  `leave_type_id` INT UNSIGNED NOT NULL,
  `from_date` DATE NOT NULL,
  `to_date` DATE NOT NULL,
  `duration_days` DECIMAL(4,1) NOT NULL DEFAULT 1.0,
  `is_half_day` TINYINT(1) NOT NULL DEFAULT 0,
  `half_day_type` ENUM('Full Day', 'First Half', 'Second Half') NOT NULL DEFAULT 'Full Day',
  `reason` TEXT NOT NULL,
  `attachment` VARCHAR(255) NULL,
  `status` ENUM('Draft', 'Pending', 'Clarification Required', 'Approved', 'Rejected', 'Cancelled', 'Completed') NOT NULL DEFAULT 'Pending',
  `rejection_reason` TEXT NULL,
  `clarification_notes` TEXT NULL,
  `approved_by` INT UNSIGNED NULL,
  `approved_at` DATETIME NULL,
  `applied_date` DATE NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`application_id`),
  KEY `idx_leave_app_type` (`applicant_type`),
  KEY `idx_leave_app_student` (`student_id`),
  KEY `idx_leave_app_staff` (`staff_id`),
  KEY `idx_leave_app_dates` (`from_date`, `to_date`),
  KEY `idx_leave_app_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 3. tbl_leave_balances
echo "3. Creating tbl_leave_balances...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_leave_balances` (
  `balance_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_year_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `entity_type` ENUM('Student', 'Staff') NOT NULL DEFAULT 'Staff',
  `entity_id` INT UNSIGNED NOT NULL,
  `leave_type_id` INT UNSIGNED NOT NULL,
  `allocated_days` DECIMAL(4,1) NOT NULL DEFAULT 12.0,
  `used_days` DECIMAL(4,1) NOT NULL DEFAULT 0.0,
  `pending_days` DECIMAL(4,1) NOT NULL DEFAULT 0.0,
  `carry_forward_days` DECIMAL(4,1) NOT NULL DEFAULT 0.0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`balance_id`),
  UNIQUE KEY `uk_leave_balance` (`academic_year_id`, `entity_type`, `entity_id`, `leave_type_id`),
  KEY `idx_lb_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 4. tbl_leave_history
echo "4. Creating tbl_leave_history...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_leave_history` (
  `history_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `application_id` INT UNSIGNED NOT NULL,
  `action` VARCHAR(50) NOT NULL,
  `performed_by` INT UNSIGNED NULL,
  `performed_by_type` VARCHAR(50) NOT NULL DEFAULT 'Staff',
  `previous_status` VARCHAR(50) NULL,
  `new_status` VARCHAR(50) NOT NULL,
  `comments` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`history_id`),
  KEY `idx_lh_app` (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 5. tbl_leave_settings
echo "5. Creating tbl_leave_settings...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_leave_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `enable_student_leave` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_staff_leave` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_half_day` TINYINT(1) NOT NULL DEFAULT 1,
  `working_days_only` TINYINT(1) NOT NULL DEFAULT 1,
  `student_approval_workflow` VARCHAR(100) NOT NULL DEFAULT 'Class Teacher -> Principal',
  `staff_approval_workflow` VARCHAR(100) NOT NULL DEFAULT 'Department Head -> Principal',
  `enable_balance_tracking` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_carry_forward` TINYINT(1) NOT NULL DEFAULT 1,
  `max_carry_forward_days` INT UNSIGNED NOT NULL DEFAULT 5,
  `require_document_default` TINYINT(1) NOT NULL DEFAULT 0,
  `max_file_size_mb` INT UNSIGNED NOT NULL DEFAULT 10,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$set_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_leave_settings")->fetch_assoc()['cnt'];
if ($set_cnt == 0) {
    echo "Inserting default leave settings...\n";
    $mysqli->query("INSERT INTO tbl_leave_settings (
        setting_id, enable_student_leave, enable_staff_leave, enable_half_day,
        working_days_only, student_approval_workflow, staff_approval_workflow,
        enable_balance_tracking, allow_carry_forward, max_carry_forward_days,
        require_document_default, max_file_size_mb
    ) VALUES (
        1, 1, 1, 1, 1, 'Class Teacher -> Principal', 'Department Head -> Principal', 1, 1, 5, 0, 10
    )");
}

// 6. tbl_leave_audit_logs
echo "6. Creating tbl_leave_audit_logs...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_leave_audit_logs` (
  `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT UNSIGNED NOT NULL,
  `details` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_leave_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

echo "=== Leave Management Module Database Migration Completed Successfully! ===\n";
