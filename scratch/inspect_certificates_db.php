<?php
$m = new mysqli('localhost', 'root', '', 'db_school');
if ($m->connect_error) die("Connect failed: " . $m->connect_error);

echo "=== Inspecting Database for Certificate Module ===\n";

$tables = ['tbl_certificates', 'tbl_certificate_types', 'tbl_certificate_templates', 'tbl_certificate_requests', 'tbl_student_documents', 'tbl_document_categories', 'tbl_school_settings', 'tbl_students', 'tbl_classes', 'tbl_sections', 'tbl_academic_years'];

foreach ($tables as $t) {
    $res = $m->query("SHOW TABLES LIKE '{$t}'");
    if ($res && $res->num_rows > 0) {
        echo "Table `{$t}` exists:\n";
        $cols = $m->query("SHOW COLUMNS FROM `{$t}`");
        while ($c = $cols->fetch_assoc()) {
            echo "  - " . $c['Field'] . " (" . $c['Type'] . ")\n";
        }
    } else {
        echo "Table `{$t}` DOES NOT exist.\n";
    }
}
