<?php
$db = mysqli_connect('localhost', 'root', '', 'db_school');
if (!$db) die('Connect failed: ' . mysqli_connect_error());

// 1. Get ALL tables
$tables_result = mysqli_query($db, "SHOW TABLES");
$tables = [];
while ($row = mysqli_fetch_row($tables_result)) $tables[] = $row[0];
echo "TOTAL TABLES: " . count($tables) . "\n\n";

// 2. For each table, check columns
echo str_pad("TABLE", 45) . str_pad("is_deleted", 15) . str_pad("TYPE", 20) . str_pad("DEFAULT", 15) . "ACTION\n";
echo str_repeat('-', 100) . "\n";

$missing = [];
$existing = [];

foreach ($tables as $table) {
    $col_result = mysqli_query($db, "SHOW COLUMNS FROM `$table`");
    $has_is_deleted = false;
    $col_type = '';
    $col_default = '';
    while ($col = mysqli_fetch_assoc($col_result)) {
        if (strtolower($col['Field']) === 'is_deleted') {
            $has_is_deleted = true;
            $col_type = $col['Type'];
            $col_default = $col['Default'] ?? 'NULL';
            break;
        }
    }
    if ($has_is_deleted) {
        $existing[] = $table;
        echo str_pad($table, 45) . str_pad("EXISTS", 15) . str_pad($col_type, 20) . str_pad($col_default, 15) . "KEEP\n";
    } else {
        $missing[] = $table;
        echo str_pad($table, 45) . str_pad("MISSING", 15) . str_pad('-', 20) . str_pad('-', 15) . "ADD\n";
    }
}

echo "\n\nSUMMARY:\n";
echo "  Tables with is_deleted: " . count($existing) . "\n";
echo "  Tables MISSING is_deleted: " . count($missing) . "\n";

echo "\n\nTABLES TO ALTER:\n";
foreach ($missing as $t) echo "  - $t\n";

echo "\n\nALTER SQL (safe, only for missing):\n";
foreach ($missing as $t) {
    echo "ALTER TABLE `$t` ADD COLUMN `is_deleted` CHAR(1) NOT NULL DEFAULT 'n' AFTER `";
    // Find last column
    $col_result = mysqli_query($db, "SHOW COLUMNS FROM `$t`");
    $last = '';
    while ($c = mysqli_fetch_assoc($col_result)) $last = $c['Field'];
    echo $last . "';\n";
}
