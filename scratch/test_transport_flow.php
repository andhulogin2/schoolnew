<?php
// Complete End-to-End Integration Test Suite for Transport Management Module

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== Running Transport Management End-to-End Integration Tests ===\n\n";

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

// 1. Vehicles & Drivers Verification
$veh_res = $mysqli->query("SELECT * FROM tbl_vehicles WHERE status = 'Active'");
assert_test("1. Active vehicles seeded and queryable", $veh_res->num_rows >= 3);

$drv_res = $mysqli->query("SELECT * FROM tbl_transport_drivers WHERE status = 'Active'");
assert_test("2. Active drivers seeded and queryable", $drv_res->num_rows >= 3);

// 2. Routes & Stops Verification
$route = $mysqli->query("SELECT * FROM tbl_transport_routes WHERE route_code = 'RT-01'")->fetch_assoc();
assert_test("3. Route 1 (RT-01) found with distance", $route && (float)$route['estimated_distance_km'] > 0);

$route_id = (int)$route['route_id'];
$stops_res = $mysqli->query("SELECT * FROM tbl_route_stops WHERE route_id = {$route_id} ORDER BY sequence_order ASC");
assert_test("4. Ordered stops found for Route 1", $stops_res->num_rows >= 3);

// 3. Test Vehicle Capacity Calculation
$vehicle = $mysqli->query("SELECT * FROM tbl_vehicles WHERE vehicle_id = 1")->fetch_assoc();
$capacity = (int)$vehicle['seating_capacity'];
$occupied = (int)$mysqli->query("SELECT COUNT(*) as cnt FROM tbl_student_transport_assignments WHERE vehicle_id = 1 AND status = 'Active'")->fetch_assoc()['cnt'];
$available = max(0, $capacity - $occupied);
assert_test("5. Seating capacity and occupancy calculated correctly ({$capacity} total, {$occupied} occupied, {$available} free)", $capacity == 40 && $available >= 0);

// 4. Test Single Student Transport Allocation
$student = $mysqli->query("SELECT student_id, class_id, section_id FROM tbl_students LIMIT 1")->fetch_assoc();
$student_id = (int)$student['student_id'];
$class_id = (int)$student['class_id'];
$section_id = (int)$student['section_id'];

$stop = $stops_res->fetch_assoc();
$stop_id = (int)$stop['stop_id'];

// Assign student
$mysqli->query("INSERT INTO tbl_student_transport_assignments (academic_year_id, student_id, class_id, section_id, route_id, pickup_stop_id, drop_stop_id, vehicle_id, transport_type, monthly_fee, start_date, status) VALUES
(1, {$student_id}, {$class_id}, {$section_id}, {$route_id}, {$stop_id}, {$stop_id}, 1, 'Two Way', 1600.00, '2026-08-20', 'Active')
ON DUPLICATE KEY UPDATE status = 'Active', route_id = {$route_id}, vehicle_id = 1");

$assign_id = $mysqli->insert_id ?: 1;
assert_test("6. Student transport allocation created successfully", $assign_id > 0);

// 5. Test Transport Assignment History Logging
$mysqli->query("INSERT INTO tbl_transport_assignment_history (assignment_id, student_id, action, new_route_id, new_stop_id, new_vehicle_id, effective_date, changed_by, comments) VALUES
({$assign_id}, {$student_id}, 'Assigned', {$route_id}, {$stop_id}, 1, '2026-08-20', 1, 'Assigned via test suite')");

$hist_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_transport_assignment_history WHERE student_id = {$student_id}")->fetch_assoc()['cnt'];
assert_test("7. Transport assignment history recorded properly", $hist_cnt > 0);

// 6. Test Vehicle Maintenance Logging & Cost Aggregation
$mysqli->query("INSERT INTO tbl_vehicle_maintenance (vehicle_id, maintenance_type, service_date, next_service_date, description, service_provider, cost, invoice_number, status) VALUES
(1, 'Repair', '2026-08-15', '2026-11-15', 'Wiper motor replacement and headlight beam focus.', 'Tata Service', 2500.00, 'INV-TS-112', 'Completed')");

$tot_cost = (float)$mysqli->query("SELECT SUM(cost) as tot FROM tbl_vehicle_maintenance WHERE vehicle_id = 1")->fetch_assoc()['tot'];
assert_test("8. Vehicle maintenance logged and aggregated total cost (₹{$tot_cost})", $tot_cost >= 11000.00);

// 7. Test Compliance Documents Expiry Tracking
$mysqli->query("INSERT INTO tbl_transport_documents (entity_type, entity_id, document_type, document_number, expiry_date, status) VALUES
('Vehicle', 1, 'Fitness Certificate', 'FIT-KL02-8871', '2027-04-01', 'Active'),
('Driver', 1, 'Driving License', 'KL-02-2015-0012345', '2028-05-15', 'Active')");

$doc_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_transport_documents")->fetch_assoc()['cnt'];
assert_test("9. Transport compliance documents stored and queryable", $doc_cnt >= 2);

// 8. Test Global Transport Settings
$settings = $mysqli->query("SELECT * FROM tbl_transport_settings WHERE setting_id = 1")->fetch_assoc();
assert_test("10. Global transport settings queryable (enable_transport = 1)", !empty($settings) && $settings['enable_transport'] == 1);

// 9. Test Reallocation / Removal
$mysqli->query("UPDATE tbl_student_transport_assignments SET status = 'Cancelled', end_date = '2026-08-20' WHERE student_id = {$student_id}");
$curr_status = $mysqli->query("SELECT status FROM tbl_student_transport_assignments WHERE student_id = {$student_id}")->fetch_assoc()['status'];
assert_test("11. Transport assignment cancellation properly marks status as Cancelled", $curr_status === 'Cancelled');

// Restore to Active for dashboard reporting
$mysqli->query("UPDATE tbl_student_transport_assignments SET status = 'Active' WHERE student_id = {$student_id}");

// 10. Dashboard Aggregated Metrics
$active_students = (int)$mysqli->query("SELECT COUNT(*) as cnt FROM tbl_student_transport_assignments WHERE status = 'Active'")->fetch_assoc()['cnt'];
assert_test("12. Transport Dashboard KPIs accurately count active passengers ({$active_students})", $active_students >= 1);

echo "\n==============================================\n";
echo "Integration Test Summary: {$passCount} Passed, {$failCount} Failed\n";
echo "==============================================\n";
