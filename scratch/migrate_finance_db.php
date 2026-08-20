<?php
$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== Running Fees & Finance Database Migration ===\n";

// 1. tbl_fee_heads / Categories
echo "1. Creating/Upgrading tbl_fee_heads...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_fee_heads` (
  `fee_head_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `head_name` VARCHAR(100) NOT NULL,
  `category_code` VARCHAR(20) NULL,
  `description` TEXT NULL,
  `applicable_to` VARCHAR(50) NOT NULL DEFAULT 'All Students',
  `frequency` ENUM('One Time', 'Monthly', 'Quarterly', 'Half Yearly', 'Yearly', 'Custom') NOT NULL DEFAULT 'Yearly',
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fee_head_id`),
  UNIQUE KEY `uk_head_name` (`head_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Check and add missing columns if already exists
$cols = [];
$r = $mysqli->query("SHOW COLUMNS FROM tbl_fee_heads");
while ($row = $r->fetch_assoc()) $cols[$row['Field']] = $row;
if (!isset($cols['category_code'])) $mysqli->query("ALTER TABLE tbl_fee_heads ADD COLUMN `category_code` VARCHAR(20) NULL AFTER `head_name`");
if (!isset($cols['applicable_to'])) $mysqli->query("ALTER TABLE tbl_fee_heads ADD COLUMN `applicable_to` VARCHAR(50) NOT NULL DEFAULT 'All Students' AFTER `description`");
if (!isset($cols['frequency'])) $mysqli->query("ALTER TABLE tbl_fee_heads ADD COLUMN `frequency` ENUM('One Time', 'Monthly', 'Quarterly', 'Half Yearly', 'Yearly', 'Custom') NOT NULL DEFAULT 'Yearly' AFTER `applicable_to`");

// Seed standard categories if count is low
$res = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_fee_heads");
if ($res->fetch_assoc()['cnt'] < 4) {
    $categories = [
        ['Tuition Fee', 'TUI', 'Standard academic tuition and instruction', 'All Students', 'Quarterly'],
        ['Admission Fee', 'ADM', 'One-time registration and admission processing', 'New Students', 'One Time'],
        ['Examination Fee', 'EXAM', 'Term-end and board examination assessment fee', 'All Students', 'Half Yearly'],
        ['Laboratory & Science Fee', 'LAB', 'Practical laboratory maintenance and consumables', 'High School Students', 'Yearly'],
        ['Library & E-Resources Fee', 'LIB', 'Library book lending and digital database access', 'All Students', 'Yearly'],
        ['Sports & Physical Education', 'SPT', 'Sports ground, athletic gear, and coaching fee', 'All Students', 'Yearly'],
        ['Transport / Bus Fee', 'TRN', 'School bus pickup and drop facility', 'Transport Opted', 'Monthly'],
        ['Activity & Cultural Fee', 'ACT', 'Co-curricular workshops, clubs, and annual festival', 'All Students', 'Yearly']
    ];
    $stmt = $mysqli->prepare("INSERT IGNORE INTO tbl_fee_heads (head_name, category_code, description, applicable_to, frequency, status) VALUES (?, ?, ?, ?, ?, 1)");
    foreach ($categories as $c) {
        $stmt->bind_param("sssss", $c[0], $c[1], $c[2], $c[3], $c[4]);
        $stmt->execute();
    }
}

// 2. tbl_fee_structures
echo "2. Creating/Upgrading tbl_fee_structures...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_fee_structures` (
  `fee_structure_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fee_head_id` INT UNSIGNED NOT NULL,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `frequency` VARCHAR(50) NOT NULL DEFAULT 'Yearly',
  `due_date` DATE NOT NULL,
  `applicable_from` DATE NULL,
  `applicable_to` DATE NULL,
  `is_optional` TINYINT(1) NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fee_structure_id`),
  KEY `idx_feestruct_head` (`fee_head_id`),
  KEY `idx_feestruct_year` (`academic_year_id`),
  KEY `idx_feestruct_class` (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$cols = [];
$r = $mysqli->query("SHOW COLUMNS FROM tbl_fee_structures");
while ($row = $r->fetch_assoc()) $cols[$row['Field']] = $row;
if (!isset($cols['frequency'])) $mysqli->query("ALTER TABLE tbl_fee_structures ADD COLUMN `frequency` VARCHAR(50) NOT NULL DEFAULT 'Yearly' AFTER `amount`");
if (!isset($cols['applicable_from'])) $mysqli->query("ALTER TABLE tbl_fee_structures ADD COLUMN `applicable_from` DATE NULL AFTER `due_date`");
if (!isset($cols['applicable_to'])) $mysqli->query("ALTER TABLE tbl_fee_structures ADD COLUMN `applicable_to` DATE NULL AFTER `applicable_from`");
if (!isset($cols['is_optional'])) $mysqli->query("ALTER TABLE tbl_fee_structures ADD COLUMN `is_optional` TINYINT(1) NOT NULL DEFAULT 0 AFTER `applicable_to`");

// 3. tbl_student_fees (Fee Invoices & Student Dues)
echo "3. Creating/Upgrading tbl_student_fees...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_student_fees` (
  `student_fee_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_no` VARCHAR(50) NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `academic_year_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `class_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `section_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `fee_structure_id` INT UNSIGNED NOT NULL,
  `original_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `concession_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `final_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `paid_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `due_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `due_date` DATE NOT NULL,
  `payment_status` ENUM('Pending', 'Partially Paid', 'Paid', 'Overdue', 'Cancelled') NOT NULL DEFAULT 'Pending',
  `remarks` VARCHAR(255) NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`student_fee_id`),
  KEY `idx_sf_student` (`student_id`),
  KEY `idx_sf_structure` (`fee_structure_id`),
  KEY `idx_sf_status` (`payment_status`),
  KEY `idx_sf_duedate` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$cols = [];
$r = $mysqli->query("SHOW COLUMNS FROM tbl_student_fees");
while ($row = $r->fetch_assoc()) $cols[$row['Field']] = $row;
if (!isset($cols['invoice_no'])) $mysqli->query("ALTER TABLE tbl_student_fees ADD COLUMN `invoice_no` VARCHAR(50) NULL AFTER `student_fee_id`");
if (!isset($cols['academic_year_id'])) $mysqli->query("ALTER TABLE tbl_student_fees ADD COLUMN `academic_year_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `student_id`");
if (!isset($cols['class_id'])) $mysqli->query("ALTER TABLE tbl_student_fees ADD COLUMN `class_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `academic_year_id`");
if (!isset($cols['section_id'])) $mysqli->query("ALTER TABLE tbl_student_fees ADD COLUMN `section_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `class_id`");
if (!isset($cols['original_amount'])) $mysqli->query("ALTER TABLE tbl_student_fees ADD COLUMN `original_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `fee_structure_id`");
if (!isset($cols['discount_amount'])) $mysqli->query("ALTER TABLE tbl_student_fees ADD COLUMN `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `original_amount`");
if (!isset($cols['concession_amount'])) $mysqli->query("ALTER TABLE tbl_student_fees ADD COLUMN `concession_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `discount_amount`");
if (!isset($cols['final_amount'])) $mysqli->query("ALTER TABLE tbl_student_fees ADD COLUMN `final_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `concession_amount`");
if (!isset($cols['due_amount'])) $mysqli->query("ALTER TABLE tbl_student_fees ADD COLUMN `due_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `paid_amount`");
if (!isset($cols['remarks'])) $mysqli->query("ALTER TABLE tbl_student_fees ADD COLUMN `remarks` VARCHAR(255) NULL AFTER `payment_status`");

// Update enum on payment_status if needed
$mysqli->query("ALTER TABLE tbl_student_fees MODIFY COLUMN `payment_status` ENUM('Pending', 'Partially Paid', 'Paid', 'Overdue', 'Cancelled') NOT NULL DEFAULT 'Pending'");

// Backfill invoice numbers and amounts where missing
$mysqli->query("UPDATE tbl_student_fees SET original_amount = amount WHERE original_amount = 0.00 AND amount > 0");
$mysqli->query("UPDATE tbl_student_fees SET final_amount = (original_amount - discount_amount - concession_amount) WHERE final_amount = 0.00 AND original_amount > 0");
$mysqli->query("UPDATE tbl_student_fees SET due_amount = (final_amount - paid_amount) WHERE due_amount = 0.00 AND final_amount > paid_amount");
$mysqli->query("UPDATE tbl_student_fees SET invoice_no = CONCAT('INV-2026-', LPAD(student_fee_id, 4, '0')) WHERE invoice_no IS NULL OR invoice_no = ''");

// 4. tbl_fee_payments
echo "4. Creating/Upgrading tbl_fee_payments...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_fee_payments` (
  `payment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_fee_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `amount_paid` DECIMAL(10,2) NOT NULL,
  `payment_mode` ENUM('Cash', 'Bank Transfer', 'UPI', 'Cheque', 'Card', 'Other') NOT NULL DEFAULT 'Cash',
  `transaction_reference` VARCHAR(100) NULL,
  `payment_date` DATE NOT NULL,
  `receipt_no` VARCHAR(50) NOT NULL,
  `collected_by` INT UNSIGNED NULL,
  `remarks` VARCHAR(255) NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  UNIQUE KEY `uk_receipt_no` (`receipt_no`),
  KEY `idx_pay_fee` (`student_fee_id`),
  KEY `idx_pay_student` (`student_id`),
  KEY `idx_pay_date` (`payment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$cols = [];
$r = $mysqli->query("SHOW COLUMNS FROM tbl_fee_payments");
while ($row = $r->fetch_assoc()) $cols[$row['Field']] = $row;
if (!isset($cols['collected_by'])) $mysqli->query("ALTER TABLE tbl_fee_payments ADD COLUMN `collected_by` INT UNSIGNED NULL AFTER `receipt_no`");
if (!isset($cols['remarks'])) $mysqli->query("ALTER TABLE tbl_fee_payments ADD COLUMN `remarks` VARCHAR(255) NULL AFTER `collected_by`");

// 5. tbl_fee_discounts
echo "5. Creating tbl_fee_discounts...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_fee_discounts` (
  `discount_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `discount_type` ENUM('Percentage', 'Fixed Amount') NOT NULL DEFAULT 'Fixed Amount',
  `discount_value` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `max_discount` DECIMAL(10,2) NULL,
  `applicable_classes` TEXT NULL,
  `applicable_categories` TEXT NULL,
  `is_concession` TINYINT(1) NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`discount_id`),
  UNIQUE KEY `uk_discount_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Seed default discounts & concessions if empty
$res = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_fee_discounts");
if ($res->fetch_assoc()['cnt'] == 0) {
    echo "Seeding default discount & concession schemes...\n";
    $discounts = [
        ['Sibling Concession', 'Percentage', 15.00, 5000.00, 1],
        ['Staff Ward Scholarship', 'Percentage', 50.00, 15000.00, 1],
        ['Merit Scholarship', 'Percentage', 25.00, 8000.00, 1],
        ['Early Bird Discount', 'Fixed Amount', 1000.00, 1000.00, 0],
        ['Financial Hardship Relief', 'Percentage', 40.00, 10000.00, 1]
    ];
    $stmt = $mysqli->prepare("INSERT INTO tbl_fee_discounts (name, discount_type, discount_value, max_discount, is_concession, status) VALUES (?, ?, ?, ?, ?, 1)");
    foreach ($discounts as $d) {
        $stmt->bind_param("ssddi", $d[0], $d[1], $d[2], $d[3], $d[4]);
        $stmt->execute();
    }
}

// 6. tbl_fee_adjustments
echo "6. Creating tbl_fee_adjustments...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_fee_adjustments` (
  `adjustment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_fee_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `adjustment_type` ENUM('Waiver', 'Adjustment', 'Correction', 'Concession') NOT NULL,
  `previous_amount` DECIMAL(10,2) NOT NULL,
  `new_amount` DECIMAL(10,2) NOT NULL,
  `adjustment_amount` DECIMAL(10,2) NOT NULL,
  `reason` TEXT NOT NULL,
  `adjusted_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`adjustment_id`),
  KEY `idx_adj_fee` (`student_fee_id`),
  KEY `idx_adj_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 7. tbl_fee_refunds
echo "7. Creating tbl_fee_refunds...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_fee_refunds` (
  `refund_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_id` INT UNSIGNED NOT NULL,
  `student_fee_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `refund_amount` DECIMAL(10,2) NOT NULL,
  `refund_reason` TEXT NOT NULL,
  `refund_mode` VARCHAR(50) NOT NULL DEFAULT 'Bank Transfer',
  `approved_by` INT UNSIGNED NULL,
  `status` ENUM('Pending', 'Approved', 'Processed', 'Rejected') NOT NULL DEFAULT 'Approved',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refund_id`),
  KEY `idx_ref_pay` (`payment_id`),
  KEY `idx_ref_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 8. tbl_fee_reminders
echo "8. Creating tbl_fee_reminders...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_fee_reminders` (
  `reminder_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `parent_name` VARCHAR(100) NULL,
  `parent_phone` VARCHAR(20) NULL,
  `parent_email` VARCHAR(100) NULL,
  `student_fee_id` INT UNSIGNED NULL,
  `reminder_type` ENUM('Upcoming Due', 'Due Today', 'Overdue', 'Payment Confirmation') NOT NULL,
  `message` TEXT NOT NULL,
  `scheduled_date` DATE NOT NULL,
  `status` ENUM('Pending', 'Sent', 'Failed', 'Cancelled') NOT NULL DEFAULT 'Pending',
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reminder_id`),
  KEY `idx_rem_student` (`student_id`),
  KEY `idx_rem_fee` (`student_fee_id`),
  KEY `idx_rem_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 9. tbl_finance_settings
echo "9. Creating tbl_finance_settings...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_finance_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `currency_symbol` VARCHAR(10) NOT NULL DEFAULT '₹',
  `currency_code` VARCHAR(10) NOT NULL DEFAULT 'INR',
  `receipt_prefix` VARCHAR(20) NOT NULL DEFAULT 'REC-',
  `next_receipt_number` INT UNSIGNED NOT NULL DEFAULT 1001,
  `receipt_footer` TEXT NULL,
  `authorized_signature_title` VARCHAR(100) NOT NULL DEFAULT 'Accounts Officer',
  `allow_partial_payments` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_overpayment` TINYINT(1) NOT NULL DEFAULT 0,
  `require_transaction_ref` TINYINT(1) NOT NULL DEFAULT 0,
  `grace_period_days` INT UNSIGNED NOT NULL DEFAULT 7,
  `discount_approval_required` TINYINT(1) NOT NULL DEFAULT 1,
  `reminder_template_upcoming` TEXT NULL,
  `reminder_template_overdue` TEXT NULL,
  `reminder_template_payment` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$res = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_finance_settings");
if ($res->fetch_assoc()['cnt'] == 0) {
    echo "Inserting default finance settings...\n";
    $mysqli->query("INSERT INTO tbl_finance_settings (
        setting_id, currency_symbol, currency_code, receipt_prefix, next_receipt_number,
        receipt_footer, authorized_signature_title, allow_partial_payments, allow_overpayment,
        require_transaction_ref, grace_period_days, discount_approval_required,
        reminder_template_upcoming, reminder_template_overdue, reminder_template_payment
    ) VALUES (
        1, '₹', 'INR', 'REC-2026-', 1003,
        'Thank you for your timely fee payment. This is a computer generated official fee receipt.',
        'Senior Accounts Officer', 1, 0, 0, 7, 1,
        'Dear Parent, the fee amount of {amount} for {student_name} is due on {due_date}. Please pay to avoid late charges.',
        'Dear Parent, the fee amount of {amount} for {student_name} is overdue by {days_overdue} days. Kindly settle immediately.',
        'Payment of {amount} has been successfully received for {student_name}. Official Receipt No: {receipt_no}.'
    )");
}

// 10. tbl_finance_audit_logs
echo "10. Creating tbl_finance_audit_logs...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_finance_audit_logs` (
  `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT UNSIGNED NOT NULL,
  `details` TEXT NULL,
  `previous_value` TEXT NULL,
  `new_value` TEXT NULL,
  `reason` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_fin_action` (`action`),
  KEY `idx_fin_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$cols = [];
$r = $mysqli->query("SHOW COLUMNS FROM tbl_finance_audit_logs");
while ($row = $r->fetch_assoc()) $cols[$row['Field']] = $row;
if (!isset($cols['details'])) $mysqli->query("ALTER TABLE tbl_finance_audit_logs ADD COLUMN `details` TEXT NULL AFTER `entity_id`");

echo "=== Fees & Finance Migration Completed Successfully! ===\n";
