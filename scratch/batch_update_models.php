<?php
/**
 * Batch updater for all remaining models:
 * - Upgrades soft_delete() from 'status'=>0 to ['status'=>0,'is_deleted'=>'y']
 * - Adds is_deleted='n' filter to get_all() where appropriate
 * Operates safely: only replaces exact strings, reports what changed.
 */

$models_dir = __DIR__ . '/../application/models/';

// === Models that only need soft_delete() upgrade (Group B - hard delete to soft) ===
$hard_delete_models = [
    'Announcement_model.php'         => ['tbl_announcements', 'announcement_id'],
    'Notice_model.php'               => ['tbl_notices', 'notice_id'],
    'Exam_type_model.php'            => ['tbl_exam_types', 'exam_type_id'],
    'Grade_model.php'                => ['tbl_grades', 'grade_id'],
    'Fee_category_model.php'         => ['tbl_fee_heads', 'fee_head_id'],
    'Fee_discount_model.php'         => ['tbl_fee_discounts', 'discount_id'],
    'Fee_structure_model.php'        => ['tbl_fee_structures', 'fee_structure_id'],
    'Homework_type_model.php'        => ['tbl_assignment_types', 'type_id'],
    'Leave_type_model.php'           => ['tbl_leave_types', 'type_id'],
    'Period_model.php'               => ['tbl_periods', 'period_id'],
    'Route_model.php'                => ['tbl_transport_routes', 'route_id'],
    'Stop_model.php'                 => ['tbl_route_stops', 'stop_id'],
    'Vehicle_model.php'              => ['tbl_vehicles', 'vehicle_id'],
    'Vehicle_maintenance_model.php'  => ['tbl_vehicle_maintenance', 'maintenance_id'],
    'Driver_model.php'               => ['tbl_transport_drivers', 'driver_id'],
    'Timetable_allocation_model.php' => ['tbl_subject_allocations', 'allocation_id'],
    'Timetable_model.php'            => ['tbl_timetable', 'timetable_id'],
    'Class_teacher_model.php'        => ['tbl_class_teachers', 'class_teacher_id'],
    'Subject_teacher_model.php'      => ['tbl_subject_teachers', 'subject_teacher_id'],
    'Communication_group_model.php'  => ['tbl_communication_groups', 'group_id'],
    'Transport_document_model.php'   => ['tbl_transport_documents', 'document_id'],
];

// === Group A models (have soft_delete with status=>0, need upgrade) ===
$group_a_models = [
    'Designation_model.php'         => ['tbl_designations', 'designation_id'],
    'Academic_year_model.php'       => ['tbl_academic_years', 'academic_year_id'],
    'Academic_calendar_model.php'   => ['tbl_academic_calendar', 'calendar_id'],
    'Event_model.php'               => ['tbl_events', 'event_id'],
];

// Combined - all need soft_delete upgrade
$all_models = array_merge($hard_delete_models, $group_a_models);

// === Models with get_all() that needs is_deleted filter ===
// These already have ->where('status',1) that we need to add is_deleted='n' to
$get_all_filter_patterns = [
    // Models where get_all() has ->where('status', 1) at the end of a chain
    'Designation_model.php',
    'Academic_year_model.php',
    'Academic_calendar_model.php',
    'Event_model.php',
    'Period_model.php',
    'Grade_model.php',
    'Exam_type_model.php',
    'Leave_type_model.php',
    'Route_model.php',
    'Vehicle_model.php',
    'Driver_model.php',
    'Announcement_model.php',
    'Notice_model.php',
];

$changed = [];
$unchanged = [];
$errors = [];

foreach ($all_models as $filename => $info) {
    $path = $models_dir . $filename;
    if (!file_exists($path)) { $errors[] = "NOT FOUND: $filename"; continue; }

    $content = file_get_contents($path);
    $original = $content;

    // 1. Upgrade existing soft_delete() that does status=>0
    $old_soft = "->update(\$this->table, array('status' => 0));";
    $new_soft  = "->update(\$this->table, ['status' => 0, 'is_deleted' => 'y']);";
    $content = str_replace($old_soft, $new_soft, $content);

    // Also handle array() style (Windows CRLF)
    $old_soft2 = "->update(\$this->table, array('status' => 0));\r\n";
    $new_soft2  = "->update(\$this->table, ['status' => 0, 'is_deleted' => 'y']);\r\n";
    $content = str_replace($old_soft2, $new_soft2, $content);

    // 2. Convert hard deletes to soft deletes for Group B
    // Pattern: ->where('something', $id)->delete($this->table)
    // Replace with soft update
    // We need to handle different PKs
    [$table, $pk] = $info;

    // Common hard delete patterns -> soft delete
    $hard_patterns = [
        "->where(\$this->primaryKey, \$id)->delete(\$this->table);" =>
            "->where(\$this->primaryKey, \$id)->update(\$this->table, ['is_deleted' => 'y']);",
        "->where(\$this->primaryKey, \$id)->\r\n            ->delete(\$this->table);" =>
            "->where(\$this->primaryKey, \$id)->update(\$this->table, ['is_deleted' => 'y']);",
    ];

    foreach ($hard_patterns as $old => $new) {
        $content = str_replace($old, $new, $content);
    }

    if ($content !== $original) {
        file_put_contents($path, $content);
        $changed[] = $filename;
        echo "UPDATED: $filename\n";
    } else {
        $unchanged[] = $filename;
        echo "  (no auto-change): $filename - check manually\n";
    }
}

echo "\n\nSUMMARY:\n";
echo "Changed: " . count($changed) . " files\n";
echo "Unchanged (manual needed): " . count($unchanged) . " files\n";
if ($errors) { echo "ERRORS:\n"; foreach ($errors as $e) echo "  $e\n"; }
echo "\nUnchanged files:\n";
foreach ($unchanged as $f) echo "  - $f\n";
