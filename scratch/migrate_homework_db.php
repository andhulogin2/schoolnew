<?php
// Database Migration Script for Homework & Assignments Module

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== Running Homework & Assignments Database Migration ===\n";

// 1. tbl_assignment_types
echo "1. Creating tbl_assignment_types...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_assignment_types` (
  `type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `uk_type_name` (`type_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$type_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_assignment_types")->fetch_assoc()['cnt'];
if ($type_cnt == 0) {
    echo "Seeding default assignment types...\n";
    $defaults = [
        ['Homework', 'Standard daily subject homework tasks'],
        ['Classwork', 'In-class exercises and problem sets'],
        ['Project', 'Term projects, research, and collaborative assignments'],
        ['Worksheet', 'Practice worksheets and practice questionnaires'],
        ['Reading', 'Assigned chapter readings and literature study'],
        ['Practical', 'Laboratory experiment reports and practical write-ups'],
        ['Activity', 'Creative activities, drawings, and extracurricular tasks']
    ];
    $stmt = $mysqli->prepare("INSERT INTO tbl_assignment_types (type_name, description, status) VALUES (?, ?, 1)");
    foreach ($defaults as $d) {
        $stmt->bind_param("ss", $d[0], $d[1]);
        $stmt->execute();
    }
}

// 2. tbl_assignments
echo "2. Creating/Upgrading tbl_assignments...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_assignments` (
  `assignment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_year_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `class_id` INT UNSIGNED NOT NULL,
  `section_id` INT UNSIGNED NOT NULL,
  `subject_id` INT UNSIGNED NOT NULL,
  `teacher_id` INT UNSIGNED NOT NULL,
  `assignment_type_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `instructions` TEXT NULL,
  `assigned_date` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  `due_time` TIME NULL,
  `max_marks` DECIMAL(5,2) NOT NULL DEFAULT 10.00,
  `allow_remarks` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_file_submission` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_text_submission` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_multiple_files` TINYINT(1) NOT NULL DEFAULT 0,
  `allow_resubmission` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_late_submission` TINYINT(1) NOT NULL DEFAULT 1,
  `target_type` ENUM('Class', 'Section', 'Individual') NOT NULL DEFAULT 'Section',
  `target_student_ids` TEXT NULL,
  `status` ENUM('Draft', 'Published', 'Active', 'Closed', 'Archived') NOT NULL DEFAULT 'Published',
  `attachments` TEXT NULL,
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`assignment_id`),
  KEY `idx_asgn_class` (`class_id`, `section_id`),
  KEY `idx_asgn_subject` (`subject_id`),
  KEY `idx_asgn_teacher` (`teacher_id`),
  KEY `idx_asgn_due` (`due_date`),
  KEY `idx_asgn_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 3. tbl_assignment_submissions
echo "3. Creating tbl_assignment_submissions...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_assignment_submissions` (
  `submission_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `assignment_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `submission_version` INT UNSIGNED NOT NULL DEFAULT 1,
  `submitted_text` TEXT NULL,
  `submitted_files` TEXT NULL,
  `submitted_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_late` TINYINT(1) NOT NULL DEFAULT 0,
  `late_duration_minutes` INT UNSIGNED NOT NULL DEFAULT 0,
  `status` ENUM('Pending', 'Submitted', 'Late', 'Reviewed', 'Returned') NOT NULL DEFAULT 'Submitted',
  `marks_obtained` DECIMAL(5,2) NULL,
  `grade` VARCHAR(10) NULL,
  `teacher_remarks` TEXT NULL,
  `correction_reason` TEXT NULL,
  `reviewed_by` INT UNSIGNED NULL,
  `reviewed_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`submission_id`),
  UNIQUE KEY `uk_asgn_student` (`assignment_id`, `student_id`),
  KEY `idx_subm_asgn` (`assignment_id`),
  KEY `idx_subm_student` (`student_id`),
  KEY `idx_subm_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 4. tbl_submission_history
echo "4. Creating tbl_submission_history...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_submission_history` (
  `history_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `submission_id` INT UNSIGNED NOT NULL,
  `assignment_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `version` INT UNSIGNED NOT NULL DEFAULT 1,
  `submitted_text` TEXT NULL,
  `submitted_files` TEXT NULL,
  `submitted_at` DATETIME NOT NULL,
  `marks_obtained` DECIMAL(5,2) NULL,
  `grade` VARCHAR(10) NULL,
  `teacher_remarks` TEXT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'Submitted',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`history_id`),
  KEY `idx_hist_submission` (`submission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 5. tbl_homework_notifications
echo "5. Creating tbl_homework_notifications...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_homework_notifications` (
  `notification_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `assignment_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `parent_name` VARCHAR(100) NULL,
  `parent_phone` VARCHAR(20) NULL,
  `parent_email` VARCHAR(100) NULL,
  `notification_type` ENUM('New Assignment', 'Upcoming Due', 'Overdue', 'Submission Received', 'Submission Reviewed', 'Returned') NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('Pending', 'Sent', 'Failed') NOT NULL DEFAULT 'Pending',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`),
  KEY `idx_hw_notif_asgn` (`assignment_id`),
  KEY `idx_hw_notif_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 6. tbl_homework_settings
echo "6. Creating tbl_homework_settings...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_homework_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `default_submission_deadline_days` INT UNSIGNED NOT NULL DEFAULT 3,
  `allow_late_submissions_default` TINYINT(1) NOT NULL DEFAULT 1,
  `max_upload_size_mb` INT UNSIGNED NOT NULL DEFAULT 10,
  `allowed_file_extensions` VARCHAR(255) NOT NULL DEFAULT 'pdf,doc,docx,jpg,jpeg,png,zip,txt',
  `enable_grading` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_parent_notifications` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$set_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_homework_settings")->fetch_assoc()['cnt'];
if ($set_cnt == 0) {
    echo "Inserting default homework settings...\n";
    $mysqli->query("INSERT INTO tbl_homework_settings (
        setting_id, default_submission_deadline_days, allow_late_submissions_default,
        max_upload_size_mb, allowed_file_extensions, enable_grading, enable_parent_notifications
    ) VALUES (
        1, 3, 1, 10, 'pdf,doc,docx,jpg,jpeg,png,zip,txt', 1, 1
    )");
}

// 7. tbl_homework_audit_logs
echo "7. Creating tbl_homework_audit_logs...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_homework_audit_logs` (
  `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT UNSIGNED NOT NULL,
  `details` TEXT NULL,
  `previous_value` TEXT NULL,
  `new_value` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_hw_action` (`action`),
  KEY `idx_hw_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Seed sample assignment if empty
$asgn_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_assignments")->fetch_assoc()['cnt'];
if ($asgn_cnt == 0) {
    echo "Inserting sample published assignment...\n";
    $mysqli->query("INSERT INTO tbl_assignments (
        academic_year_id, class_id, section_id, subject_id, teacher_id, assignment_type_id,
        title, description, instructions, assigned_date, due_date, due_time, max_marks,
        allow_remarks, allow_file_submission, allow_text_submission, allow_multiple_files,
        allow_resubmission, allow_late_submission, target_type, status, created_by
    ) VALUES (
        1, 1, 1, 1, 1, 1,
        'Quadratic Equations Practice Set',
        'Solve exercises 4.1 and 4.2 from textbook Chapter 4.',
        'Show complete working steps for all questions. Upload clear scanned PDF or image copy.',
        CURDATE(), DATE_ADD(CURDATE(), INTERVAL 4 DAY), '17:00:00', 20.00,
        1, 1, 1, 1, 1, 1, 'Section', 'Published', 1
    )");
}

echo "=== Homework & Assignments Migration Completed Successfully! ===\n";
