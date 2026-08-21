<?php
$db = mysqli_connect('localhost', 'root', '', 'db_school');
if (!$db) die('Connect failed: ' . mysqli_connect_error());

$tables_result = mysqli_query($db, "SHOW TABLES");
$tables = [];
while ($row = mysqli_fetch_row($tables_result)) $tables[] = $row[0];

$missing = [];
$existing = [];
$table_info = [];

foreach ($tables as $table) {
    $col_result = mysqli_query($db, "SHOW COLUMNS FROM `$table`");
    $has = false; $type = ''; $default = ''; $last_col = '';
    while ($col = mysqli_fetch_assoc($col_result)) {
        $last_col = $col['Field'];
        if (strtolower($col['Field']) === 'is_deleted') {
            $has = true; $type = $col['Type']; $default = $col['Default'] ?? 'NULL';
        }
    }
    $table_info[$table] = ['has' => $has, 'type' => $type, 'default' => $default, 'last_col' => $last_col];
    if ($has) $existing[] = $table;
    else $missing[] = $table;
}

echo "TOTAL TABLES: " . count($tables) . "\n";
echo "WITH is_deleted: " . count($existing) . "\n";
echo "MISSING is_deleted: " . count($missing) . "\n\n";

echo "=== EXISTING is_deleted columns ===\n";
foreach ($existing as $t) {
    echo "  $t => type=" . $table_info[$t]['type'] . " default=" . $table_info[$t]['default'] . "\n";
}

echo "\n=== MISSING (need ALTER) ===\n";
foreach ($missing as $t) {
    echo "  $t\n";
}
