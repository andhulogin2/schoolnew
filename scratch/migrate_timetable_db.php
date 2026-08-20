<?php
// Database Migration Script for Timetable Module

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== Running Timetable Database Migration ===\n";

// 1. Check / Upgrade tbl_timetable
echo "1. Checking/Upgrading tbl_timetable...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_timetable` (
  `timetable_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NOT NULL,
  `section_id` INT UNSIGNED NOT NULL,
  `day` ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday') NOT NULL,
  `period_id` INT UNSIGNED NOT NULL,
  `subject_id` INT UNSIGNED NOT NULL,
  `teacher_id` INT UNSIGNED NOT NULL,
  `room_no` VARCHAR(50) NULL,
  `is_locked` TINYINT(1) NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`timetable_id`),
  UNIQUE KEY `uk_class_day_period` (`academic_year_id`, `class_id`, `section_id`, `day`, `period_id`),
  KEY `idx_tt_teacher` (`teacher_id`),
  KEY `idx_tt_period` (`period_id`),
  KEY `idx_tt_subject` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Check if room_no exists
$cols = [];
$r = $mysqli->query("SHOW COLUMNS FROM tbl_timetable");
while ($row = $r->fetch_assoc()) $cols[$row['Field']] = $row;
if (!isset($cols['room_no'])) $mysqli->query("ALTER TABLE tbl_timetable ADD COLUMN `room_no` VARCHAR(50) NULL AFTER `teacher_id`");
if (!isset($cols['is_locked'])) $mysqli->query("ALTER TABLE tbl_timetable ADD COLUMN `is_locked` TINYINT(1) NOT NULL DEFAULT 0 AFTER `room_no`");

// 2. tbl_subject_allocations
echo "2. Creating tbl_subject_allocations...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_subject_allocations` (
  `allocation_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NOT NULL,
  `section_id` INT UNSIGNED NOT NULL,
  `subject_id` INT UNSIGNED NOT NULL,
  `teacher_id` INT UNSIGNED NULL,
  `weekly_periods_target` INT UNSIGNED NOT NULL DEFAULT 5,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`allocation_id`),
  UNIQUE KEY `uk_class_sec_sub` (`academic_year_id`, `class_id`, `section_id`, `subject_id`),
  KEY `idx_alloc_teacher` (`teacher_id`),
  KEY `idx_alloc_subject` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 3. tbl_timetable_publish
echo "3. Creating tbl_timetable_publish...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_timetable_publish` (
  `publish_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NOT NULL,
  `section_id` INT UNSIGNED NOT NULL,
  `status` ENUM('Draft', 'Published', 'Locked') NOT NULL DEFAULT 'Draft',
  `published_at` DATETIME NULL,
  `published_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`publish_id`),
  UNIQUE KEY `uk_tt_publish_class` (`academic_year_id`, `class_id`, `section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 4. tbl_teacher_substitutions
echo "4. Creating tbl_teacher_substitutions...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_teacher_substitutions` (
  `substitution_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `timetable_id` INT UNSIGNED NOT NULL,
  `substitution_date` DATE NOT NULL,
  `original_teacher_id` INT UNSIGNED NOT NULL,
  `substitute_teacher_id` INT UNSIGNED NOT NULL,
  `reason` VARCHAR(255) NULL,
  `status` ENUM('Scheduled', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Scheduled',
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`substitution_id`),
  KEY `idx_sub_tt` (`timetable_id`),
  KEY `idx_sub_date` (`substitution_date`),
  KEY `idx_sub_orig` (`original_teacher_id`),
  KEY `idx_sub_subst` (`substitute_teacher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 5. tbl_timetable_settings
echo "5. Creating tbl_timetable_settings...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_timetable_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `working_days` VARCHAR(255) NOT NULL DEFAULT 'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
  `max_periods_per_day` INT UNSIGNED NOT NULL DEFAULT 8,
  `max_consecutive_periods` INT UNSIGNED NOT NULL DEFAULT 3,
  `allow_teacher_overlap` TINYINT(1) NOT NULL DEFAULT 0,
  `auto_publish` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Seed default settings if empty
$res = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_timetable_settings");
if ($res->fetch_assoc()['cnt'] == 0) {
    echo "Inserting default timetable settings...\n";
    $mysqli->query("INSERT INTO tbl_timetable_settings (
        setting_id, working_days, max_periods_per_day, max_consecutive_periods, allow_teacher_overlap, auto_publish
    ) VALUES (
        1, 'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday', 8, 3, 0, 0
    )");
}

// Seed sample subject allocations for Class 10 A if empty
$alloc_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_subject_allocations")->fetch_assoc()['cnt'];
if ($alloc_cnt == 0) {
    echo "Inserting default subject allocations...\n";
    $subs = $mysqli->query("SELECT subject_id FROM tbl_subjects WHERE status = 1 LIMIT 5");
    $teachers = $mysqli->query("SELECT staff_id FROM tbl_staff WHERE staff_type = 'teacher' AND status = 1 LIMIT 5");
    $t_arr = [];
    while ($t = $teachers->fetch_assoc()) $t_arr[] = $t['staff_id'];
    
    $i = 0;
    while ($s = $subs->fetch_assoc()) {
        $tid = $t_arr[$i % count($t_arr)] ?? 1;
        $mysqli->query("INSERT IGNORE INTO tbl_subject_allocations (academic_year_id, class_id, section_id, subject_id, teacher_id, weekly_periods_target, status)
            VALUES (1, 1, 1, {$s['subject_id']}, {$tid}, 6, 1)");
        $i++;
    }
}

echo "=== Timetable Migration Completed Successfully! ===\n";
