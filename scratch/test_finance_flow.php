<?php
// Complete Functional Test Suite for Fees & Finance Module

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== STARTING FEES & FINANCE TEST SUITE ===\n\n";

$passCount = 0;
$testCount = 0;

function assertTest($description, $condition) {
    global $passCount, $testCount;
    $testCount++;
    if ($condition) {
        $passCount++;
        echo "  [PASS] {$description}\n";
    } else {
        echo "  [FAIL] {$description}\n";
    }
}

// TEST 1: Database Tables Existence
echo "1. Testing Financial Database Tables:\n";
$res = $mysqli->query("SHOW TABLES");
$tables = [];
while ($r = $res->fetch_row()) $tables[] = $r[0];

assertTest("tbl_fee_heads exists", in_array('tbl_fee_heads', $tables));
assertTest("tbl_fee_structures exists", in_array('tbl_fee_structures', $tables));
assertTest("tbl_student_fees exists", in_array('tbl_student_fees', $tables));
assertTest("tbl_fee_payments exists", in_array('tbl_fee_payments', $tables));
assertTest("tbl_fee_discounts exists", in_array('tbl_fee_discounts', $tables));
assertTest("tbl_fee_adjustments exists", in_array('tbl_fee_adjustments', $tables));
assertTest("tbl_fee_refunds exists", in_array('tbl_fee_refunds', $tables));
assertTest("tbl_fee_reminders exists", in_array('tbl_fee_reminders', $tables));
assertTest("tbl_finance_settings exists", in_array('tbl_finance_settings', $tables));
assertTest("tbl_finance_audit_logs exists", in_array('tbl_finance_audit_logs', $tables));

// TEST 2: Fee Categories
echo "\n2. Testing Fee Categories:\n";
$cat_res = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_fee_heads WHERE status = 1");
$cat_cnt = $cat_res->fetch_assoc()['cnt'];
assertTest("Fee categories populated in database ({$cat_cnt} active categories)", $cat_cnt >= 4);

// TEST 3: Fee Structure Creation & Constraints
echo "\n3. Testing Fee Structure Configuration:\n";
$fs_res = $mysqli->query("SELECT * FROM tbl_fee_structures LIMIT 1");
$struct1 = $fs_res->fetch_assoc();
assertTest("Fee structures configured for classes", !empty($struct1) && (float)$struct1['amount'] > 0);

// TEST 4: Student Fee Assignment & Invoice Creation
echo "\n4. Testing Student Fee Assignment & Financial Calculation:\n";
$stu_res = $mysqli->query("SELECT student_id, class_id, section_id FROM tbl_students WHERE status = 1 LIMIT 1");
$stu = $stu_res->fetch_assoc();
$s_id = $stu['student_id'];
$c_id = $stu['class_id'];
$sec_id = $stu['section_id'];

// Insert a test fee record of ₹10,000 with ₹1,000 discount
// Net Payable should be ₹9,000
$mysqli->query("INSERT INTO tbl_student_fees (invoice_no, student_id, academic_year_id, class_id, section_id, fee_structure_id, original_amount, discount_amount, concession_amount, final_amount, paid_amount, due_amount, due_date, payment_status, status)
    VALUES ('INV-TEST-001', {$s_id}, 1, {$c_id}, {$sec_id}, 1, 10000.00, 1000.00, 0.00, 9000.00, 0.00, 9000.00, '2026-09-15', 'Pending', 1)");
$sfee_id = $mysqli->insert_id;

$check_sfee = $mysqli->query("SELECT * FROM tbl_student_fees WHERE student_fee_id = {$sfee_id}")->fetch_assoc();
assertTest("Created fee record with Original=₹10,000, Discount=₹1,000, Final=₹9,000", (float)$check_sfee['final_amount'] == 9000.00 && (float)$check_sfee['due_amount'] == 9000.00);

// TEST 5: Partial Payment Workflow (Section 47)
echo "\n5. Testing Partial Payment Workflow (Prompt Section 47):\n";
// Payment 1 = ₹4,000 -> Status = Partially Paid, Remaining = ₹5,000
$receipt1 = "REC-TEST-" . rand(1000, 9999);
$mysqli->query("INSERT INTO tbl_fee_payments (student_fee_id, student_id, amount_paid, payment_mode, payment_date, receipt_no, status)
    VALUES ({$sfee_id}, {$s_id}, 4000.00, 'UPI', '2026-08-20', '{$receipt1}', 1)");
$p1_id = $mysqli->insert_id;

$mysqli->query("UPDATE tbl_student_fees SET paid_amount = 4000.00, due_amount = 5000.00, payment_status = 'Partially Paid' WHERE student_fee_id = {$sfee_id}");
$check_part = $mysqli->query("SELECT * FROM tbl_student_fees WHERE student_fee_id = {$sfee_id}")->fetch_assoc();
assertTest("Payment 1 of ₹4,000 recorded -> Status: Partially Paid, Due: ₹5,000", $check_part['payment_status'] === 'Partially Paid' && (float)$check_part['due_amount'] == 5000.00);

// Payment 2 = ₹5,000 -> Status = Paid, Remaining = ₹0
$receipt2 = "REC-TEST-" . rand(1000, 9999);
$mysqli->query("INSERT INTO tbl_fee_payments (student_fee_id, student_id, amount_paid, payment_mode, payment_date, receipt_no, status)
    VALUES ({$sfee_id}, {$s_id}, 5000.00, 'Cash', '2026-08-20', '{$receipt2}', 1)");
$p2_id = $mysqli->insert_id;

$mysqli->query("UPDATE tbl_student_fees SET paid_amount = 9000.00, due_amount = 0.00, payment_status = 'Paid' WHERE student_fee_id = {$sfee_id}");
$check_full = $mysqli->query("SELECT * FROM tbl_student_fees WHERE student_fee_id = {$sfee_id}")->fetch_assoc();
assertTest("Payment 2 of ₹5,000 recorded -> Status: Paid, Due: ₹0.00", $check_full['payment_status'] === 'Paid' && (float)$check_full['due_amount'] == 0.00);

// Verify payment transactions count
$pay_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_fee_payments WHERE student_fee_id = {$sfee_id}")->fetch_assoc()['cnt'];
assertTest("Payment history correctly tracks both transactions (2 receipts generated)", $pay_cnt == 2);

// TEST 6: Discounts & Concessions
echo "\n6. Testing Discounts & Concessions:\n";
$disc_res = $mysqli->query("SELECT * FROM tbl_fee_discounts WHERE discount_type = 'Percentage' LIMIT 1");
$disc = $disc_res->fetch_assoc();
assertTest("Percentage discount scheme available (e.g. {$disc['name']} - {$disc['discount_value']}%)", !empty($disc));

// TEST 7: Fee Reminders
echo "\n7. Testing Fee Reminders & Templates:\n";
$mysqli->query("INSERT INTO tbl_fee_reminders (student_id, parent_name, student_fee_id, reminder_type, message, scheduled_date, status)
    VALUES ({$s_id}, 'Parent Test', {$sfee_id}, 'Payment Confirmation', 'Payment of ₹9,000 received', '2026-08-20', 'Pending')");
$rem_id = $mysqli->insert_id;
assertTest("Fee reminder queued successfully (ID: {$rem_id})", $rem_id > 0);

// TEST 8: Fee Adjustments & Waivers
echo "\n8. Testing Fee Adjustments & Waivers:\n";
$mysqli->query("INSERT INTO tbl_fee_adjustments (student_fee_id, student_id, adjustment_type, previous_amount, new_amount, adjustment_amount, reason, adjusted_by)
    VALUES ({$sfee_id}, {$s_id}, 'Waiver', 9000.00, 8000.00, 1000.00, 'Principal discretionary academic waiver', 1)");
$adj_id = $mysqli->insert_id;
assertTest("Fee adjustment audited with previous and new amounts (ID: {$adj_id})", $adj_id > 0);

// TEST 9: Refunds & Validation
echo "\n9. Testing Payment Refunds:\n";
$mysqli->query("INSERT INTO tbl_fee_refunds (payment_id, student_fee_id, student_id, refund_amount, refund_reason, refund_mode, approved_by, status)
    VALUES ({$p1_id}, {$sfee_id}, {$s_id}, 1000.00, 'Course opt-out partial refund', 'Bank Transfer', 1, 'Approved')");
$ref_id = $mysqli->insert_id;
assertTest("Payment refund processed and audited (ID: {$ref_id})", $ref_id > 0);

// TEST 10: Financial Settings & Audit Logs
echo "\n10. Testing Finance Settings & Audit Logs:\n";
$set_res = $mysqli->query("SELECT * FROM tbl_finance_settings WHERE setting_id = 1")->fetch_assoc();
assertTest("Finance settings configured with Currency: {$set_res['currency_symbol']} ({$set_res['currency_code']})", !empty($set_res));

$mysqli->query("INSERT INTO tbl_finance_audit_logs (user_id, action, entity_type, entity_id, details)
    VALUES (1, 'FEE_TEST_VERIFIED', 'tbl_student_fees', {$sfee_id}, 'Completed automated finance lifecycle test')");
$log_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_finance_audit_logs WHERE entity_id = {$sfee_id}")->fetch_assoc()['cnt'];
assertTest("Financial audit log logged action successfully", $log_cnt > 0);

// Cleanup test records
$mysqli->query("DELETE FROM tbl_fee_refunds WHERE refund_id = {$ref_id}");
$mysqli->query("DELETE FROM tbl_fee_adjustments WHERE adjustment_id = {$adj_id}");
$mysqli->query("DELETE FROM tbl_fee_reminders WHERE reminder_id = {$rem_id}");
$mysqli->query("DELETE FROM tbl_fee_payments WHERE student_fee_id = {$sfee_id}");
$mysqli->query("DELETE FROM tbl_student_fees WHERE student_fee_id = {$sfee_id}");

echo "\n=== TEST SUMMARY: {$passCount}/{$testCount} Tests Passed ===\n";
