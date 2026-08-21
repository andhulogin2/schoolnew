<?php
require_once __DIR__ . '/../index.php';
$CI =& get_instance();
$CI->load->database();

echo "=== TABLES IN DB ===\n";
$tables = $CI->db->list_tables();
print_r($tables);

echo "\n=== TBL_USERS STRUCTURE ===\n";
$fields = $CI->db->field_data('tbl_users');
foreach ($fields as $field) {
    echo "{$field->name} ({$field->type}, max_len: {$field->max_length}, primary: {$field->primary_key})\n";
}

echo "\n=== TBL_USERS INDEXES ===\n";
$indexes = $CI->db->query("SHOW INDEX FROM tbl_users")->result();
foreach ($indexes as $idx) {
    echo "Index: {$idx->Key_name} | Column: {$idx->Column_name} | Non_unique: {$idx->Non_unique}\n";
}

echo "\n=== TBL_USERS ALL RECORDS ===\n";
$users = $CI->db->query("SELECT user_id, username, email, user_type, role_id, status, is_deleted FROM tbl_users")->result();
foreach ($users as $u) {
    echo "ID: {$u->user_id} | User: [{$u->username}] | Email: [{$u->email}] | Type: {$u->user_type} | Status: {$u->status} | is_deleted: [{$u->is_deleted}]\n";
}

echo "\n=== TOTAL USERS COUNT: " . count($users) . " ===\n";

