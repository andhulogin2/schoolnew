<?php
// Database Migration Script for Transport Management Module

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== Running Transport Management Module Database Migration ===\n";

// 1. tbl_vehicles
echo "1. Creating tbl_vehicles...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_vehicles` (
  `vehicle_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `vehicle_number` VARCHAR(50) NOT NULL,
  `registration_number` VARCHAR(50) NOT NULL,
  `vehicle_type` ENUM('School Bus', 'Van', 'Mini Bus', 'Other') NOT NULL DEFAULT 'School Bus',
  `manufacturer` VARCHAR(100) NULL,
  `model` VARCHAR(100) NULL,
  `manufacturing_year` YEAR NULL,
  `seating_capacity` INT UNSIGNED NOT NULL DEFAULT 40,
  `vehicle_color` VARCHAR(50) NULL DEFAULT 'Yellow',
  `registration_date` DATE NULL,
  `registration_expiry` DATE NULL,
  `insurance_number` VARCHAR(100) NULL,
  `insurance_expiry` DATE NULL,
  `fitness_number` VARCHAR(100) NULL,
  `fitness_expiry` DATE NULL,
  `pollution_number` VARCHAR(100) NULL,
  `pollution_expiry` DATE NULL,
  `permit_number` VARCHAR(100) NULL,
  `permit_expiry` DATE NULL,
  `assigned_driver_id` INT UNSIGNED NULL,
  `assigned_route_id` INT UNSIGNED NULL,
  `status` ENUM('Active', 'Inactive', 'Maintenance', 'Retired') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`vehicle_id`),
  UNIQUE KEY `uk_vehicle_reg` (`registration_number`),
  KEY `idx_veh_driver` (`assigned_driver_id`),
  KEY `idx_veh_route` (`assigned_route_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 2. tbl_transport_drivers
echo "2. Creating tbl_transport_drivers...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_transport_drivers` (
  `driver_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `driver_name` VARCHAR(100) NOT NULL,
  `staff_id` INT UNSIGNED NULL,
  `photo` VARCHAR(255) NULL,
  `dob` DATE NULL,
  `phone` VARCHAR(20) NOT NULL,
  `alternate_phone` VARCHAR(20) NULL,
  `address` TEXT NULL,
  `license_number` VARCHAR(50) NOT NULL,
  `license_type` VARCHAR(50) NOT NULL DEFAULT 'Heavy Commercial Vehicle (HCV)',
  `license_issue_date` DATE NULL,
  `license_expiry_date` DATE NOT NULL,
  `experience_years` INT UNSIGNED NOT NULL DEFAULT 5,
  `assigned_vehicle_id` INT UNSIGNED NULL,
  `status` ENUM('Active', 'Inactive', 'On Leave', 'Suspended') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`driver_id`),
  UNIQUE KEY `uk_driver_license` (`license_number`),
  KEY `idx_driver_staff` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 3. tbl_transport_routes
echo "3. Creating/Upgrading tbl_transport_routes...\n";
$mysqli->query("DROP TABLE IF EXISTS `tbl_transport_routes`");
$mysqli->query("CREATE TABLE `tbl_transport_routes` (
  `route_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `route_name` VARCHAR(100) NOT NULL,
  `route_code` VARCHAR(50) NOT NULL,
  `description` TEXT NULL,
  `start_point` VARCHAR(100) NOT NULL,
  `end_point` VARCHAR(100) NOT NULL,
  `estimated_distance_km` DECIMAL(5,2) NOT NULL DEFAULT 15.00,
  `estimated_travel_time_min` INT UNSIGNED NOT NULL DEFAULT 45,
  `assigned_vehicle_id` INT UNSIGNED NULL,
  `assigned_driver_id` INT UNSIGNED NULL,
  `status` ENUM('Active', 'Inactive', 'Suspended') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`route_id`),
  UNIQUE KEY `uk_route_code` (`route_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 4. tbl_route_stops
echo "4. Creating tbl_route_stops...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_route_stops` (
  `stop_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `route_id` INT UNSIGNED NOT NULL,
  `stop_name` VARCHAR(100) NOT NULL,
  `stop_code` VARCHAR(50) NULL,
  `sequence_order` INT UNSIGNED NOT NULL DEFAULT 1,
  `pickup_time` TIME NOT NULL DEFAULT '07:30:00',
  `drop_time` TIME NOT NULL DEFAULT '15:30:00',
  `landmark` VARCHAR(150) NULL,
  `distance_km` DECIMAL(5,2) NOT NULL DEFAULT 2.00,
  `fare_amount` DECIMAL(10,2) NOT NULL DEFAULT 1200.00,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`stop_id`),
  KEY `idx_stop_route` (`route_id`),
  KEY `idx_stop_seq` (`route_id`, `sequence_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 5. tbl_student_transport_assignments
echo "5. Creating tbl_student_transport_assignments...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_student_transport_assignments` (
  `assignment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_year_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `student_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NULL,
  `section_id` INT UNSIGNED NULL,
  `route_id` INT UNSIGNED NOT NULL,
  `pickup_stop_id` INT UNSIGNED NOT NULL,
  `drop_stop_id` INT UNSIGNED NOT NULL,
  `vehicle_id` INT UNSIGNED NOT NULL,
  `transport_type` ENUM('Two Way', 'One Way', 'Pickup Only', 'Drop Only') NOT NULL DEFAULT 'Two Way',
  `monthly_fee` DECIMAL(10,2) NOT NULL DEFAULT 1500.00,
  `start_date` DATE NOT NULL,
  `end_date` DATE NULL,
  `status` ENUM('Active', 'Suspended', 'Cancelled') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`assignment_id`),
  UNIQUE KEY `uk_student_trans_assign` (`academic_year_id`, `student_id`, `status`),
  KEY `idx_ta_route` (`route_id`),
  KEY `idx_ta_vehicle` (`vehicle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 6. tbl_transport_assignment_history
echo "6. Creating tbl_transport_assignment_history...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_transport_assignment_history` (
  `history_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `assignment_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `action` VARCHAR(50) NOT NULL,
  `previous_route_id` INT UNSIGNED NULL,
  `new_route_id` INT UNSIGNED NULL,
  `previous_stop_id` INT UNSIGNED NULL,
  `new_stop_id` INT UNSIGNED NULL,
  `previous_vehicle_id` INT UNSIGNED NULL,
  `new_vehicle_id` INT UNSIGNED NULL,
  `effective_date` DATE NOT NULL,
  `changed_by` INT UNSIGNED NULL,
  `comments` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`history_id`),
  KEY `idx_tah_student` (`student_id`),
  KEY `idx_tah_assign` (`assignment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 7. tbl_vehicle_maintenance
echo "7. Creating tbl_vehicle_maintenance...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_vehicle_maintenance` (
  `maintenance_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `vehicle_id` INT UNSIGNED NOT NULL,
  `maintenance_type` ENUM('Routine Service', 'Engine', 'Tyres', 'Brake', 'Electrical', 'Insurance', 'Fitness', 'Cleaning', 'Repair', 'Other') NOT NULL DEFAULT 'Routine Service',
  `service_date` DATE NOT NULL,
  `next_service_date` DATE NOT NULL,
  `description` TEXT NOT NULL,
  `service_provider` VARCHAR(150) NOT NULL,
  `cost` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `invoice_number` VARCHAR(100) NULL,
  `remarks` VARCHAR(255) NULL,
  `document_file` VARCHAR(255) NULL,
  `status` ENUM('Completed', 'Scheduled', 'Overdue') NOT NULL DEFAULT 'Completed',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`maintenance_id`),
  KEY `idx_vm_vehicle` (`vehicle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 8. tbl_transport_documents
echo "8. Creating tbl_transport_documents...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_transport_documents` (
  `document_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `entity_type` ENUM('Vehicle', 'Driver') NOT NULL DEFAULT 'Vehicle',
  `entity_id` INT UNSIGNED NOT NULL,
  `document_type` ENUM('Registration', 'Insurance', 'Fitness Certificate', 'Pollution Certificate', 'Permit', 'Driving License', 'ID Proof', 'Medical Certificate', 'Police Verification', 'Other') NOT NULL DEFAULT 'Registration',
  `document_number` VARCHAR(100) NULL,
  `issue_date` DATE NULL,
  `expiry_date` DATE NULL,
  `file_path` VARCHAR(255) NULL,
  `status` ENUM('Active', 'Expiring Soon', 'Expired') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`document_id`),
  KEY `idx_td_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 9. tbl_transport_settings
echo "9. Creating tbl_transport_settings...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_transport_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `enable_transport` TINYINT(1) NOT NULL DEFAULT 1,
  `enforce_capacity` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_capacity_override` TINYINT(1) NOT NULL DEFAULT 0,
  `default_monthly_fee` DECIMAL(10,2) NOT NULL DEFAULT 1500.00,
  `fee_frequency` VARCHAR(50) NOT NULL DEFAULT 'Monthly',
  `maintenance_reminder_days` INT UNSIGNED NOT NULL DEFAULT 15,
  `document_expiry_reminder_days` INT UNSIGNED NOT NULL DEFAULT 30,
  `driver_license_reminder_days` INT UNSIGNED NOT NULL DEFAULT 30,
  `allow_one_way` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_pickup_only` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_drop_only` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_bulk_assignment` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$st_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_transport_settings")->fetch_assoc()['cnt'];
if ($st_cnt == 0) {
    echo "Inserting default transport settings...\n";
    $mysqli->query("INSERT INTO tbl_transport_settings (setting_id) VALUES (1)");
}

// 10. tbl_transport_audit_logs
echo "10. Creating tbl_transport_audit_logs...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_transport_audit_logs` (
  `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT UNSIGNED NOT NULL,
  `details` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_trans_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Seed Initial Vehicles, Drivers, Routes, and Stops if empty
$r_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_transport_routes")->fetch_assoc()['cnt'];
if ($r_cnt == 0) {
    echo "Seeding default Transport Fleet data...\n";

    // Drivers
    $mysqli->query("INSERT IGNORE INTO tbl_transport_drivers (driver_id, driver_name, phone, license_number, license_type, license_expiry_date, experience_years, status) VALUES
    (1, 'Ramesh Kumar', '9847123456', 'KL-02-2015-0012345', 'Heavy Commercial Vehicle (HCV)', '2028-05-15', 12, 'Active'),
    (2, 'Suresh Babu', '9847654321', 'KL-02-2017-0054321', 'Heavy Commercial Vehicle (HCV)', '2027-11-20', 8, 'Active'),
    (3, 'Mohan Das', '9847987654', 'KL-02-2019-0098765', 'Medium Passenger Vehicle', '2026-10-10', 6, 'Active')");

    $d1 = 1; $d2 = 2; $d3 = 3;

    // Routes
    $mysqli->query("INSERT INTO tbl_transport_routes (route_id, route_name, route_code, description, start_point, end_point, estimated_distance_km, estimated_travel_time_min, assigned_driver_id, status) VALUES
    (1, 'Route 1 - Punalur Express', 'RT-01', 'Primary northern highway route covering Punalur and Kalluvathukkal', 'Punalur Town', 'School Campus', 22.50, 45, {$d1}, 'Active'),
    (2, 'Route 2 - Anchal Bypass', 'RT-02', 'Southern township route through Anchal market and bypass junction', 'Anchal Main Gate', 'School Campus', 18.00, 35, {$d2}, 'Active'),
    (3, 'Route 3 - Valakom Shuttle', 'RT-03', 'Suburban feeder route covering Valakom and surrounding suburbs', 'Valakom Junction', 'School Campus', 12.00, 25, {$d3}, 'Active')");

    $r1 = 1; $r2 = 2; $r3 = 3;

    // Vehicles
    $mysqli->query("INSERT INTO tbl_vehicles (vehicle_id, vehicle_number, registration_number, vehicle_type, manufacturer, model, manufacturing_year, seating_capacity, vehicle_color, registration_date, registration_expiry, insurance_number, insurance_expiry, fitness_number, fitness_expiry, pollution_number, pollution_expiry, permit_number, permit_expiry, assigned_driver_id, assigned_route_id, status) VALUES
    (1, 'Bus 01', 'KL-02-AB-1234', 'School Bus', 'Tata Motors', 'Starbus Ultra 40', 2022, 40, 'School Yellow', '2022-04-10', '2037-04-09', 'POL-INS-98721', '2027-04-15', 'FIT-KL02-8871', '2027-04-01', 'POL-KL-44321', '2026-12-31', 'PRM-KL-9921', '2027-03-31', {$d1}, {$r1}, 'Active'),
    (2, 'Bus 02', 'KL-02-CD-5678', 'School Bus', 'Ashok Leyland', 'Sunshine 42', 2023, 42, 'School Yellow', '2023-06-15', '2038-06-14', 'POL-INS-66542', '2027-06-20', 'FIT-KL02-9982', '2027-06-10', 'POL-KL-55432', '2027-01-15', 'PRM-KL-8832', '2027-06-01', {$d2}, {$r2}, 'Active'),
    (3, 'Van 01', 'KL-02-EF-9012', 'Van', 'Force Motors', 'Traveller 18', 2021, 18, 'School Yellow', '2021-08-01', '2036-07-31', 'POL-INS-33219', '2026-09-15', 'FIT-KL02-6651', '2026-09-01', 'POL-KL-11982', '2026-11-20', 'PRM-KL-4421', '2026-08-30', {$d3}, {$r3}, 'Active')
    ON DUPLICATE KEY UPDATE assigned_driver_id = VALUES(assigned_driver_id), assigned_route_id = VALUES(assigned_route_id)");

    // Link vehicle to driver and route
    $mysqli->query("UPDATE tbl_transport_drivers SET assigned_vehicle_id = 1 WHERE driver_id = 1");
    $mysqli->query("UPDATE tbl_transport_drivers SET assigned_vehicle_id = 2 WHERE driver_id = 2");
    $mysqli->query("UPDATE tbl_transport_drivers SET assigned_vehicle_id = 3 WHERE driver_id = 3");
    $mysqli->query("UPDATE tbl_transport_routes SET assigned_vehicle_id = 1 WHERE route_id = 1");
    $mysqli->query("UPDATE tbl_transport_routes SET assigned_vehicle_id = 2 WHERE route_id = 2");
    $mysqli->query("UPDATE tbl_transport_routes SET assigned_vehicle_id = 3 WHERE route_id = 3");

    // Stops for Route 1
    $mysqli->query("INSERT INTO tbl_route_stops (route_id, stop_name, stop_code, sequence_order, pickup_time, drop_time, landmark, distance_km, fare_amount) VALUES
    ({$r1}, 'Punalur Junction', 'PNL-01', 1, '07:15:00', '16:00:00', 'Opposite Municipal Office', 0.00, 1600.00),
    ({$r1}, 'Kalluvathukkal Temple', 'KVK-02', 2, '07:30:00', '15:45:00', 'Near Temple Gate', 6.50, 1500.00),
    ({$r1}, 'Chemmanthoor Bridge', 'CMB-03', 3, '07:45:00', '15:30:00', 'Bridge East Entry', 14.00, 1400.00),
    ({$r1}, 'School Campus', 'SCH-04', 4, '08:00:00', '15:15:00', 'Main Bus Bay', 22.50, 0.00)");

    // Stops for Route 2
    $mysqli->query("INSERT INTO tbl_route_stops (route_id, stop_name, stop_code, sequence_order, pickup_time, drop_time, landmark, distance_km, fare_amount) VALUES
    ({$r2}, 'Anchal Market', 'ANC-01', 1, '07:20:00', '15:55:00', 'Market Main Gate', 0.00, 1500.00),
    ({$r2}, 'Bypass Junction', 'BYP-02', 2, '07:35:00', '15:40:00', 'Traffic Island', 7.00, 1350.00),
    ({$r2}, 'School Campus', 'SCH-03', 3, '07:55:00', '15:15:00', 'Main Bus Bay', 18.00, 0.00)");

    // Maintenance Records
    $mysqli->query("INSERT INTO tbl_vehicle_maintenance (vehicle_id, maintenance_type, service_date, next_service_date, description, service_provider, cost, invoice_number, status) VALUES
    (1, 'Routine Service', '2026-07-10', '2026-10-10', 'Periodic 10,000 km general servicing, oil filter replacement, coolant top-up.', 'Tata Authorized Service Centre', 8500.00, 'INV-TT-9982', 'Completed'),
    (2, 'Tyres', '2026-06-25', '2026-12-25', 'Replaced two front radial tyres and wheel alignment.', 'MRF Tyres & Service', 16000.00, 'INV-MRF-4412', 'Completed'),
    (3, 'Routine Service', '2026-08-01', '2026-08-25', 'Quarterly brake pad inspection and clutch fluid bleeding.', 'AutoCare Express', 3500.00, 'INV-AC-1021', 'Scheduled')");
}

echo "=== Transport Management Module Database Migration Completed Successfully! ===\n";
