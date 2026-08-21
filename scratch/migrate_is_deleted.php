<?php
/**
 * SAFE MIGRATION: Add is_deleted CHAR(1) NOT NULL DEFAULT 'n'
 * to every table in db_school that does NOT already have it.
 * Never touches a table that already has the column.
 */
$db = mysqli_connect('localhost', 'root', '', 'db_school');
if (!$db) die('Connect failed: ' . mysqli_connect_error());

$tables_result = mysqli_query($db, "SHOW TABLES");
$tables = [];
while ($row = mysqli_fetch_row($tables_result)) $tables[] = $row[0];

$added   = [];
$skipped = [];
$errors  = [];

foreach ($tables as $table) {
    // Check existing columns
    $col_result = mysqli_query($db, "SHOW COLUMNS FROM `$table`");
    $has_is_deleted = false;
    $last_col = '';
    while ($col = mysqli_fetch_assoc($col_result)) {
        $last_col = $col['Field'];
        if (strtolower($col['Field']) === 'is_deleted') {
            $has_is_deleted = true;
        }
    }

    if ($has_is_deleted) {
        $skipped[] = $table;
        continue;
    }

    // Add column after the last column
    $sql = "ALTER TABLE `$table` ADD COLUMN `is_deleted` CHAR(1) NOT NULL DEFAULT 'n' AFTER `$last_col`";
    if (mysqli_query($db, $sql)) {
        $added[] = $table;
    } else {
        $errors[] = "$table => " . mysqli_error($db);
    }
}

echo "=== SOFT DELETE MIGRATION COMPLETE ===\n\n";
echo "ADDED is_deleted to " . count($added) . " tables:\n";
foreach ($added as $t) echo "  + $t\n";

echo "\nSKIPPED (already had is_deleted): " . count($skipped) . "\n";
foreach ($skipped as $t) echo "  = $t\n";

if (!empty($errors)) {
    echo "\nERRORS (" . count($errors) . "):\n";
    foreach ($errors as $e) echo "  ERROR: $e\n";
    exit(1);
}

echo "\n\nVERIFICATION — Checking all tables now have is_deleted:\n";
$tables_result = mysqli_query($db, "SHOW TABLES");
$all_ok = true;
while ($row = mysqli_fetch_row($tables_result)) {
    $table = $row[0];
    $col_result = mysqli_query($db, "SHOW COLUMNS FROM `$table` LIKE 'is_deleted'");
    if (mysqli_num_rows($col_result) === 0) {
        echo "  MISSING: $table\n";
        $all_ok = false;
    }
}
if ($all_ok) echo "  ALL TABLES HAVE is_deleted. Migration successful!\n";
