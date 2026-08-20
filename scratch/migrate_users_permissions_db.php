<?php
// Migration and Upgrade Script for User & Permission Management Module

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== Running User & Permission Management Database Migration ===\n";

// 1. Upgrade tbl_roles
echo "1. Upgrading tbl_roles...\n";
$role_cols = [];
$r_res = $mysqli->query("SHOW COLUMNS FROM tbl_roles");
while($r = $r_res->fetch_assoc()) $role_cols[] = $r['Field'];

if (!in_array('role_code', $role_cols)) {
    $mysqli->query("ALTER TABLE tbl_roles ADD COLUMN `role_code` VARCHAR(50) NOT NULL AFTER `role_name`");
}
if (!in_array('user_type', $role_cols)) {
    $mysqli->query("ALTER TABLE tbl_roles ADD COLUMN `user_type` VARCHAR(50) NOT NULL DEFAULT 'Staff' AFTER `role_code`");
}
if (!in_array('is_system', $role_cols)) {
    $mysqli->query("ALTER TABLE tbl_roles ADD COLUMN `is_system` TINYINT(1) NOT NULL DEFAULT 0 AFTER `description`");
}
$mysqli->query("ALTER TABLE tbl_roles MODIFY COLUMN `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active'");

// 2. Create tbl_permissions
echo "2. Creating tbl_permissions...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_permissions` (
  `permission_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` VARCHAR(50) NOT NULL,
  `action` VARCHAR(50) NOT NULL,
  `permission_key` VARCHAR(100) NOT NULL,
  `permission_name` VARCHAR(150) NOT NULL,
  `description` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `uk_perm_key` (`permission_key`),
  KEY `idx_perm_module` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 3. Create tbl_role_permissions
echo "3. Creating tbl_role_permissions...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_role_permissions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` INT UNSIGNED NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_role_perm` (`role_id`, `permission_id`),
  KEY `idx_rp_role` (`role_id`),
  KEY `idx_rp_perm` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 4. Create tbl_user_permissions (Overrides)
echo "4. Creating tbl_user_permissions...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_user_permissions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  `override_type` ENUM('Grant', 'Revoke') NOT NULL DEFAULT 'Grant',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_perm` (`user_id`, `permission_id`),
  KEY `idx_up_user` (`user_id`),
  KEY `idx_up_perm` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 5. Upgrade tbl_users
echo "5. Upgrading tbl_users...\n";
$user_cols = [];
$u_res = $mysqli->query("SHOW COLUMNS FROM tbl_users");
while($u = $u_res->fetch_assoc()) $user_cols[] = $u['Field'];

if (!in_array('username', $user_cols)) {
    $mysqli->query("ALTER TABLE tbl_users ADD COLUMN `username` VARCHAR(100) NULL AFTER `role_id`");
}
if (!in_array('user_type', $user_cols)) {
    $mysqli->query("ALTER TABLE tbl_users ADD COLUMN `user_type` VARCHAR(50) NOT NULL DEFAULT 'Admin' AFTER `username`");
}
if (!in_array('student_id', $user_cols)) {
    $mysqli->query("ALTER TABLE tbl_users ADD COLUMN `student_id` INT UNSIGNED NULL AFTER `staff_id`");
}
if (!in_array('parent_id', $user_cols)) {
    $mysqli->query("ALTER TABLE tbl_users ADD COLUMN `parent_id` INT UNSIGNED NULL AFTER `student_id`");
}
if (!in_array('avatar', $user_cols)) {
    $mysqli->query("ALTER TABLE tbl_users ADD COLUMN `avatar` VARCHAR(255) NULL AFTER `phone`");
}
if (!in_array('failed_login_attempts', $user_cols)) {
    $mysqli->query("ALTER TABLE tbl_users ADD COLUMN `failed_login_attempts` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `password`");
}
if (!in_array('locked_until', $user_cols)) {
    $mysqli->query("ALTER TABLE tbl_users ADD COLUMN `locked_until` DATETIME NULL AFTER `failed_login_attempts`");
}
if (!in_array('last_login_at', $user_cols)) {
    $mysqli->query("ALTER TABLE tbl_users ADD COLUMN `last_login_at` DATETIME NULL AFTER `locked_until`");
}
$mysqli->query("ALTER TABLE tbl_users MODIFY COLUMN `status` ENUM('Active', 'Inactive', 'Suspended', 'Locked') NOT NULL DEFAULT 'Active'");

// 6. Create tbl_parent_students
echo "6. Creating tbl_parent_students...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_parent_students` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_user_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `relationship` VARCHAR(50) NOT NULL DEFAULT 'Parent',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_parent_student` (`parent_user_id`, `student_id`),
  KEY `idx_ps_parent` (`parent_user_id`),
  KEY `idx_ps_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 7. Create tbl_user_login_activity
echo "7. Creating tbl_user_login_activity...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_user_login_activity` (
  `activity_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `username` VARCHAR(100) NOT NULL,
  `ip_address` VARCHAR(50) NULL,
  `user_agent` VARCHAR(255) NULL,
  `status` ENUM('Successful', 'Failed', 'Locked') NOT NULL DEFAULT 'Successful',
  `failure_reason` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`activity_id`),
  KEY `idx_la_user` (`user_id`),
  KEY `idx_la_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 8. Create tbl_permission_audit_logs
echo "8. Creating tbl_permission_audit_logs...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_permission_audit_logs` (
  `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `target_type` ENUM('Role', 'User', 'Permission', 'Security') NOT NULL DEFAULT 'Role',
  `target_id` INT UNSIGNED NOT NULL,
  `previous_value` TEXT NULL,
  `new_value` TEXT NULL,
  `details` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_pal_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 9. Create tbl_user_security_settings
echo "9. Creating tbl_user_security_settings...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_user_security_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `max_failed_attempts` INT UNSIGNED NOT NULL DEFAULT 5,
  `lockout_duration_minutes` INT UNSIGNED NOT NULL DEFAULT 30,
  `session_timeout_minutes` INT UNSIGNED NOT NULL DEFAULT 120,
  `password_min_length` INT UNSIGNED NOT NULL DEFAULT 8,
  `require_special_chars` TINYINT(1) NOT NULL DEFAULT 1,
  `require_numbers` TINYINT(1) NOT NULL DEFAULT 1,
  `password_expiry_days` INT UNSIGNED NOT NULL DEFAULT 90,
  `allow_concurrent_sessions` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Insert default security settings if empty
$check_sec = $mysqli->query("SELECT setting_id FROM tbl_user_security_settings WHERE setting_id = 1");
if ($check_sec->num_rows == 0) {
    $mysqli->query("INSERT INTO tbl_user_security_settings (`setting_id`, `max_failed_attempts`, `lockout_duration_minutes`, `session_timeout_minutes`, `password_min_length`, `require_special_chars`, `require_numbers`, `password_expiry_days`, `allow_concurrent_sessions`) VALUES
    (1, 5, 30, 120, 8, 1, 1, 90, 1)");
}

// 10. Seed System Roles
echo "10. Seeding System Roles & Permissions...\n";

$roles = [
    ['id' => 1, 'name' => 'Super Admin', 'code' => 'SUPER_ADMIN', 'type' => 'Admin', 'desc' => 'Unrestricted master access to all school modules and settings.', 'is_system' => 1],
    ['id' => 2, 'name' => 'Principal', 'code' => 'PRINCIPAL', 'type' => 'Principal', 'desc' => 'Executive academic leadership, staff oversight, and institutional reports.', 'is_system' => 1],
    ['id' => 3, 'name' => 'Teacher', 'code' => 'TEACHER', 'type' => 'Teacher', 'desc' => 'Classroom teaching faculty, student attendance, exams, homework.', 'is_system' => 1],
    ['id' => 4, 'name' => 'Accountant', 'code' => 'ACCOUNTANT', 'type' => 'Accountant', 'desc' => 'Fee collection, structure configuration, receipts, and financial ledger.', 'is_system' => 1],
    ['id' => 5, 'name' => 'Transport Manager', 'code' => 'TRANSPORT_MGR', 'type' => 'Transport Manager', 'desc' => 'Fleet, routes, stops, vehicle maintenance, and student bus assignments.', 'is_system' => 1],
    ['id' => 6, 'name' => 'Receptionist', 'code' => 'RECEPTIONIST', 'type' => 'Receptionist', 'desc' => 'Front desk reception, visitor inquiries, certificates, basic notices.', 'is_system' => 1],
    ['id' => 7, 'name' => 'Parent', 'code' => 'PARENT', 'type' => 'Parent', 'desc' => 'Parent portal access to monitor attendance, fee dues, marks, notices for own children.', 'is_system' => 1],
    ['id' => 8, 'name' => 'Student', 'code' => 'STUDENT', 'type' => 'Student', 'desc' => 'Student portal for assignments, class timetable, results, attendance.', 'is_system' => 1],
    ['id' => 9, 'name' => 'Librarian', 'code' => 'LIBRARIAN', 'type' => 'Librarian', 'desc' => 'Library catalog, issue/return circulation, fine collection, and inventory.', 'is_system' => 1],
];

foreach ($roles as $r) {
    $stmt = $mysqli->prepare("INSERT INTO tbl_roles (role_id, role_name, role_code, user_type, description, is_system, status) 
        VALUES (?, ?, ?, ?, ?, ?, 'Active') 
        ON DUPLICATE KEY UPDATE role_name = VALUES(role_name), role_code = VALUES(role_code), user_type = VALUES(user_type), description = VALUES(description), is_system = VALUES(is_system)");
    $stmt->bind_param("issssi", $r['id'], $r['name'], $r['code'], $r['type'], $r['desc'], $r['is_system']);
    $stmt->execute();
}

// 11. Seed Permissions Catalog (All 16 Modules)
$permissions = [
    // Dashboard
    ['Dashboard', 'view', 'dashboard.view', 'View Dashboard', 'Access primary dashboard and KPI metrics'],
    
    // Students
    ['Students', 'view', 'students.view', 'View Students', 'Browse student directory and profile data'],
    ['Students', 'create', 'students.create', 'Admit Student', 'Create new student admission'],
    ['Students', 'edit', 'students.edit', 'Edit Student', 'Modify existing student profile'],
    ['Students', 'delete', 'students.delete', 'Delete / Archive Student', 'Deactivate or delete student record'],
    ['Students', 'promote', 'students.promote', 'Promote Students', 'Perform batch academic student promotion'],
    ['Students', 'export', 'students.export', 'Export Students', 'Download student CSV/Excel exports'],

    // Staff
    ['Staff', 'view', 'staff.view', 'View Staff', 'Browse staff directory and profiles'],
    ['Staff', 'create', 'staff.create', 'Add Staff', 'Register new employee or faculty'],
    ['Staff', 'edit', 'staff.edit', 'Edit Staff', 'Update staff profile details'],
    ['Staff', 'delete', 'staff.delete', 'Delete Staff', 'Deactivate or remove staff record'],

    // Academics
    ['Academics', 'view', 'academics.view', 'View Academic Setup', 'View classes, sections, and subjects'],
    ['Academics', 'manage', 'academics.manage', 'Manage Academics', 'Create and modify classes, sections, and subjects'],

    // Attendance
    ['Attendance', 'view', 'attendance.view', 'View Attendance', 'View student attendance logs and stats'],
    ['Attendance', 'mark', 'attendance.mark', 'Mark Attendance', 'Record daily and period-wise attendance'],
    ['Attendance', 'edit', 'attendance.edit', 'Edit Attendance', 'Modify previously saved attendance records'],
    ['Attendance', 'reports', 'attendance.reports', 'Attendance Reports', 'View and export attendance analytics'],

    // Examinations
    ['Examination', 'view', 'exams.view', 'View Exams', 'View exam schedules and results'],
    ['Examination', 'create', 'exams.create', 'Create Exams', 'Create exam terms, schedules, and subject allocations'],
    ['Examination', 'marks_entry', 'exams.marks_entry', 'Enter Marks', 'Input student test marks and grades'],
    ['Examination', 'publish', 'exams.publish', 'Publish Results', 'Calculate positions and publish report cards'],
    ['Examination', 'reports', 'exams.reports', 'Exam Reports', 'Access examination reports and cards'],

    // Fees & Finance
    ['Fees', 'view', 'fees.view', 'View Fee Dashboard', 'View fee structures and student ledgers'],
    ['Fees', 'manage_structure', 'fees.manage_structure', 'Manage Fee Structure', 'Create fee heads and category fee structures'],
    ['Fees', 'assign', 'fees.assign', 'Assign Fees', 'Allocate fees to classes or students'],
    ['Fees', 'collect', 'fees.collect', 'Collect Fees', 'Record fee payments and issue receipts'],
    ['Fees', 'refund', 'fees.refund', 'Refunds & Adjustments', 'Process fee refunds and ledger adjustments'],
    ['Fees', 'reports', 'fees.reports', 'Finance Reports', 'Generate fee collection and due reports'],

    // Timetable
    ['Timetable', 'view', 'timetable.view', 'View Timetable', 'View class and teacher timetables'],
    ['Timetable', 'manage', 'timetable.manage', 'Build Timetable', 'Create, edit, and publish weekly timetables'],

    // Homework
    ['Homework', 'view', 'homework.view', 'View Assignments', 'Browse homework listings'],
    ['Homework', 'create', 'homework.create', 'Create Assignment', 'Publish new homework assignments'],
    ['Homework', 'review', 'homework.review', 'Review Submissions', 'Evaluate student submissions and grade'],

    // Communication
    ['Communication', 'view', 'communication.view', 'View Communication', 'Access notices, logs, and dashboard'],
    ['Communication', 'send', 'communication.send', 'Send Messages', 'Dispatch notices, SMS, WhatsApp, and Email'],
    ['Communication', 'manage_templates', 'communication.manage_templates', 'Manage Templates', 'Create and edit notification templates'],
    ['Communication', 'automated_rules', 'communication.automated_rules', 'Manage Automation Rules', 'Configure automated event rules'],

    // Leave Management
    ['Leave', 'view', 'leave.view', 'View Leaves', 'Browse student and staff leave requests'],
    ['Leave', 'apply', 'leave.apply', 'Apply Leave', 'Submit personal leave application'],
    ['Leave', 'approve', 'leave.approve', 'Approve / Reject Leave', 'Sanction or reject leave applications'],

    // Transport
    ['Transport', 'view', 'transport.view', 'View Transport', 'Browse vehicles, routes, and stops'],
    ['Transport', 'manage', 'transport.manage', 'Manage Transport', 'Configure fleet, maintenance, and student assignments'],

    // Certificates
    ['Certificates', 'view', 'certificates.view', 'View Certificates', 'Browse issued certificates and documents'],
    ['Certificates', 'generate', 'certificates.generate', 'Generate Certificates', 'Generate bonafide, TC, study certificates'],
    ['Certificates', 'verify_docs', 'certificates.verify_docs', 'Verify Documents', 'Approve or reject student identity documents'],

    // Reports
    ['Reports', 'view', 'reports.view', 'Access Reports', 'Access institutional consolidated reports'],

    // Users & Permissions
    ['Users', 'view', 'users.view', 'View Users', 'Browse user accounts, roles, and logs'],
    ['Users', 'create', 'users.create', 'Create Users', 'Register new user login accounts'],
    ['Users', 'edit', 'users.edit', 'Edit Users', 'Update accounts, reset passwords, change roles'],
    ['Users', 'delete', 'users.delete', 'Deactivate Users', 'Deactivate or lock user accounts'],
    ['Users', 'manage_roles', 'users.manage_roles', 'Manage Roles & Permissions', 'Configure RBAC permission matrix'],

    // School Settings
    ['Settings', 'view', 'settings.view', 'View Settings', 'View school configurations'],
    ['Settings', 'edit', 'settings.edit', 'Edit Settings', 'Modify school profile and global parameters'],
];

foreach ($permissions as $p) {
    $stmt = $mysqli->prepare("INSERT INTO tbl_permissions (module, action, permission_key, permission_name, description) 
        VALUES (?, ?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE permission_name = VALUES(permission_name), description = VALUES(description)");
    $stmt->bind_param("sssss", $p[0], $p[1], $p[2], $p[3], $p[4]);
    $stmt->execute();
}

// 12. Assign Default Permissions to Roles
// Super Admin -> ALL permissions
$all_perm_ids = [];
$pres = $mysqli->query("SELECT permission_id, permission_key FROM tbl_permissions");
while($p = $pres->fetch_assoc()) $all_perm_ids[$p['permission_key']] = (int)$p['permission_id'];

foreach ($all_perm_ids as $pid) {
    $mysqli->query("INSERT IGNORE INTO tbl_role_permissions (role_id, permission_id) VALUES (1, {$pid})");
}

// Principal -> Academic, Students, Staff, Attendance, Exams, Fees (view/reports), Timetable, Homework, Comm, Leave, Transport, Certs, Reports
$principal_keys = [
    'dashboard.view', 'students.view', 'students.create', 'students.edit', 'students.promote', 'students.export',
    'staff.view', 'staff.create', 'staff.edit', 'academics.view', 'academics.manage',
    'attendance.view', 'attendance.mark', 'attendance.edit', 'attendance.reports',
    'exams.view', 'exams.create', 'exams.marks_entry', 'exams.publish', 'exams.reports',
    'fees.view', 'fees.reports', 'timetable.view', 'timetable.manage',
    'homework.view', 'homework.create', 'homework.review',
    'communication.view', 'communication.send', 'communication.manage_templates', 'communication.automated_rules',
    'leave.view', 'leave.apply', 'leave.approve', 'transport.view', 'transport.manage',
    'certificates.view', 'certificates.generate', 'certificates.verify_docs', 'reports.view', 'settings.view'
];
foreach ($principal_keys as $k) {
    if (isset($all_perm_ids[$k])) {
        $mysqli->query("INSERT IGNORE INTO tbl_role_permissions (role_id, permission_id) VALUES (2, {$all_perm_ids[$k]})");
    }
}

// Teacher -> Students (view), Attendance (view/mark/reports), Exams (view/marks_entry), Timetable (view), Homework (view/create/review), Comm (view/send), Leave (apply)
$teacher_keys = [
    'dashboard.view', 'students.view', 'academics.view',
    'attendance.view', 'attendance.mark', 'attendance.reports',
    'exams.view', 'exams.marks_entry', 'timetable.view',
    'homework.view', 'homework.create', 'homework.review',
    'communication.view', 'communication.send', 'leave.apply'
];
foreach ($teacher_keys as $k) {
    if (isset($all_perm_ids[$k])) {
        $mysqli->query("INSERT IGNORE INTO tbl_role_permissions (role_id, permission_id) VALUES (3, {$all_perm_ids[$k]})");
    }
}

// Accountant -> Fees (All), Students (view/export), Reports (view)
$accountant_keys = [
    'dashboard.view', 'students.view', 'students.export',
    'fees.view', 'fees.manage_structure', 'fees.assign', 'fees.collect', 'fees.refund', 'fees.reports',
    'communication.view', 'communication.send', 'leave.apply'
];
foreach ($accountant_keys as $k) {
    if (isset($all_perm_ids[$k])) {
        $mysqli->query("INSERT IGNORE INTO tbl_role_permissions (role_id, permission_id) VALUES (4, {$all_perm_ids[$k]})");
    }
}

// Transport Manager -> Transport (All), Students (view), Comm (view/send)
$transport_keys = [
    'dashboard.view', 'students.view', 'transport.view', 'transport.manage',
    'communication.view', 'communication.send', 'leave.apply'
];
foreach ($transport_keys as $k) {
    if (isset($all_perm_ids[$k])) {
        $mysqli->query("INSERT IGNORE INTO tbl_role_permissions (role_id, permission_id) VALUES (5, {$all_perm_ids[$k]})");
    }
}

// Receptionist -> Students (view/create), Certificates (view/generate/verify_docs), Comm (view/send)
$reception_keys = [
    'dashboard.view', 'students.view', 'students.create',
    'certificates.view', 'certificates.generate', 'certificates.verify_docs',
    'communication.view', 'communication.send', 'leave.apply'
];
foreach ($reception_keys as $k) {
    if (isset($all_perm_ids[$k])) {
        $mysqli->query("INSERT IGNORE INTO tbl_role_permissions (role_id, permission_id) VALUES (6, {$all_perm_ids[$k]})");
    }
}

// Parent -> Portal View permissions
$parent_keys = [
    'dashboard.view', 'students.view', 'attendance.view', 'exams.view', 'fees.view', 'timetable.view', 'homework.view', 'communication.view', 'leave.apply'
];
foreach ($parent_keys as $k) {
    if (isset($all_perm_ids[$k])) {
        $mysqli->query("INSERT IGNORE INTO tbl_role_permissions (role_id, permission_id) VALUES (7, {$all_perm_ids[$k]})");
    }
}

// Student -> Student portal
$student_keys = [
    'dashboard.view', 'attendance.view', 'exams.view', 'fees.view', 'timetable.view', 'homework.view', 'communication.view'
];
foreach ($student_keys as $k) {
    if (isset($all_perm_ids[$k])) {
        $mysqli->query("INSERT IGNORE INTO tbl_role_permissions (role_id, permission_id) VALUES (8, {$all_perm_ids[$k]})");
    }
}

// 13. Ensure Initial Users have usernames and user_type updated
echo "13. Updating initial seed users...\n";
$mysqli->query("UPDATE tbl_users SET username = 'admin', user_type = 'Admin' WHERE user_id = 1");
$mysqli->query("UPDATE tbl_users SET username = 'principal', user_type = 'Principal' WHERE user_id = 2");
$mysqli->query("UPDATE tbl_users SET username = 'priya.teacher', user_type = 'Teacher' WHERE user_id = 3");
$mysqli->query("UPDATE tbl_users SET username = 'fathima.accountant', user_type = 'Accountant' WHERE user_id = 4");
$mysqli->query("UPDATE tbl_users SET username = 'sunil.reception', user_type = 'Receptionist' WHERE user_id = 5");

// Seed a Parent user and a Student user for testing
$check_parent = $mysqli->query("SELECT user_id FROM tbl_users WHERE email = 'suresh.nair@email.com'");
if ($check_parent->num_rows == 0) {
    $hash = password_hash('Parent@123', PASSWORD_BCRYPT);
    $mysqli->query("INSERT INTO tbl_users (role_id, username, user_type, name, email, phone, password, status) VALUES
    (7, 'suresh.parent', 'Parent', 'Suresh Nair', 'suresh.nair@email.com', '+91 98470 11223', '{$hash}', 'Active')");
    $parent_uid = $mysqli->insert_id;
    $mysqli->query("INSERT IGNORE INTO tbl_parent_students (parent_user_id, student_id, relationship) VALUES ({$parent_uid}, 1, 'Father')");
}

$check_student = $mysqli->query("SELECT user_id FROM tbl_users WHERE email = 'aarav.nair@email.com'");
if ($check_student->num_rows == 0) {
    $hash = password_hash('Student@123', PASSWORD_BCRYPT);
    $mysqli->query("INSERT INTO tbl_users (role_id, username, user_type, student_id, name, email, phone, password, status) VALUES
    (8, 'aarav.student', 'Student', 1, 'Aarav Nair', 'aarav.nair@email.com', '+91 98470 11223', '{$hash}', 'Active')");
}

echo "=== User & Permission Database Migration Completed Successfully! ===\n";
