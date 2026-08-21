<?php
$models = glob(__DIR__ . '/../application/models/*.php');
$error = false;
foreach ($models as $m) {
    $output = [];
    $return_var = 0;
    exec("php -l " . escapeshellarg($m), $output, $return_var);
    if ($return_var !== 0) {
        echo "LINT ERROR in $m:\n" . implode("\n", $output) . "\n";
        $error = true;
    }
}
if (!$error) {
    echo "SUCCESS: All " . count($models) . " models passed PHP syntax check with 0 errors!\n";
}
