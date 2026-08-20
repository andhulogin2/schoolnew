<?php
$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== Running Attendance Module Database Migration ===\n";

// 1. Modify tbl_attendance
// Check if columns exist
$cols = [];
$res = $mysqli->query("SHOW COLUMNS FROM tbl_attendance");
while ($r = $res->fetch_assoc()) {
    $cols[$r['Field']] = $r;
}

if (!isset($cols['attendance_type'])) {
    echo "Adding attendance_type to tbl_attendance...\n";
    $mysqli->query("ALTER TABLE `tbl_attendance` ADD `attendance_type` ENUM('Daily', 'Period-wise') NOT NULL DEFAULT 'Daily' AFTER `attendance_date`");
}

if (!isset($cols['period_id'])) {
    echo "Adding period_id to tbl_attendance...\n";
    $mysqli->query("ALTER TABLE `tbl_attendance` ADD `period_id` INT UNSIGNED NULL AFTER `attendance_type`");
    $mysqli->query("ALTER TABLE `tbl_attendance` ADD KEY `idx_att_period` (`period_id`)");
}

if (!isset($cols['marked_by'])) {
    echo "Adding marked_by to tbl_attendance...\n";
    $mysqli->query("ALTER TABLE `tbl_attendance` ADD `marked_by` INT UNSIGNED NULL AFTER `remarks`");
}

// Modify attendance_status enum to include Excused
echo "Updating attendance_status enum in tbl_attendance...\n";
$mysqli->query("ALTER TABLE `tbl_attendance` MODIFY COLUMN `attendance_status` ENUM('Present', 'Absent', 'Late', 'Excused', 'Leave') NOT NULL DEFAULT 'Present'");

// Drop existing unique key uk_student_attendance_date if present and add flexible unique constraint
// Since daily vs period-wise require different uniqueness, we drop the rigid uk_student_attendance_date if it blocks period attendance
$indexes = [];
$res = $mysqli->query("SHOW INDEX FROM tbl_attendance");
while ($r = $res->fetch_assoc()) {
    $indexes[$r['Key_name']] = true;
}
if (isset($indexes['uk_student_attendance_date'])) {
    echo "Dropping legacy uk_student_attendance_date index...\n";
    $mysqli->query("ALTER TABLE `tbl_attendance` DROP INDEX `uk_student_attendance_date`");
}

// Add composite index for quick lookup and integrity
if (!isset($indexes['idx_student_date_type_period'])) {
    echo "Adding composite index idx_student_date_type_period...\n";
    $mysqli->query("ALTER TABLE `tbl_attendance` ADD KEY `idx_student_date_type_period` (`student_id`, `attendance_date`, `attendance_type`, `period_id`)");
}

// 2. Ensure tbl_periods has period_number and academic_year_id if missing
$pcols = [];
$res = $mysqli->query("SHOW COLUMNS FROM tbl_periods");
while ($r = $res->fetch_assoc()) {
    $pcols[$r['Field']] = $r;
}
if (!isset($pcols['period_number'])) {
    echo "Adding period_number to tbl_periods...\n";
    $mysqli->query("ALTER TABLE `tbl_periods` ADD `period_number` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `period_id`");
    // Sync period_number with period_order
    $mysqli->query("UPDATE `tbl_periods` SET `period_number` = `period_order` WHERE `period_number` = 1 AND `period_order` IS NOT NULL");
}
if (!isset($pcols['academic_year_id'])) {
    echo "Adding academic_year_id to tbl_periods...\n";
    $mysqli->query("ALTER TABLE `tbl_periods` ADD `academic_year_id` INT UNSIGNED NULL AFTER `status`");
}

// 3. Create tbl_attendance_notifications
echo "Creating/Verifying tbl_attendance_notifications...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_attendance_notifications` (
  `notification_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `parent_name` VARCHAR(100) NULL,
  `parent_phone` VARCHAR(20) NULL,
  `parent_email` VARCHAR(100) NULL,
  `attendance_id` INT UNSIGNED NULL,
  `attendance_date` DATE NOT NULL,
  `notification_type` ENUM('Absent', 'Late', 'Excused', 'Attendance Summary') NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('Pending', 'Sent', 'Failed') NOT NULL DEFAULT 'Pending',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`),
  KEY `idx_notif_student` (`student_id`),
  KEY `idx_notif_date` (`attendance_date`),
  KEY `idx_notif_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 4. Create tbl_attendance_settings
echo "Creating/Verifying tbl_attendance_settings...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_attendance_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `enable_present` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_absent` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_late` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_excused` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_period_attendance` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_absent_notification` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_late_notification` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_summary_notification` TINYINT(1) NOT NULL DEFAULT 1,
  `absent_template` TEXT NULL,
  `late_template` TEXT NULL,
  `excused_template` TEXT NULL,
  `summary_template` TEXT NULL,
  `notification_timing` VARCHAR(50) NOT NULL DEFAULT 'On Marking',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Insert default settings if empty
$res = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_attendance_settings");
$row = $res->fetch_assoc();
if ($row['cnt'] == 0) {
    echo "Inserting default attendance settings...\n";
    $absent_tpl = "Dear Parent, your child {student_name} was marked absent on {date}.";
    $late_tpl = "Dear Parent, your child {student_name} was marked late on {date}.";
    $excused_tpl = "Dear Parent, your child {student_name} has been excused on {date}.";
    $summary_tpl = "Attendance summary for {student_name}: Present {present_days}, Absent {absent_days}, Late {late_days}, Excused {excused_days}.";
    
    $stmt = $mysqli->prepare("INSERT INTO tbl_attendance_settings (
        setting_id, enable_present, enable_absent, enable_late, enable_excused,
        enable_period_attendance, enable_absent_notification, enable_late_notification,
        enable_summary_notification, absent_template, late_template, excused_template, summary_template, notification_timing
    ) VALUES (1, 1, 1, 1, 1, 1, 1, 1, 1, ?, ?, ?, ?, 'On Marking')");
    $stmt->bind_param("ssss", $absent_tpl, $late_tpl, $excused_tpl, $summary_tpl);
    $stmt->execute();
}

echo "=== Migration Complete! ===\n";
