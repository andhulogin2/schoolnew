<?php
$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('application'));
$errorCount = 0;
$checkedCount = 0;
foreach ($dir as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $checkedCount++;
        $output = [];
        $ret = 0;
        exec('php -l ' . escapeshellarg($file->getPathname()), $output, $ret);
        if ($ret !== 0) {
            $errorCount++;
            echo "ERROR in " . $file->getPathname() . ":\n" . implode("\n", $output) . "\n";
        }
    }
}
echo "Checked $checkedCount PHP files. Total errors: $errorCount\n";
