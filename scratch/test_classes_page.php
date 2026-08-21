<?php
$ch = curl_init('http://localhost/schoolnew/academics/classes');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$html = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $code\n";
if (strpos($html, '4 academic class grades configured.') !== false) {
    echo "SUCCESS: Found '4 academic class grades configured.'\n";
} elseif (strpos($html, 'academic class grades configured.') !== false) {
    preg_match('/(\d+)\s+academic class grades configured\./', $html, $m);
    echo "Found count: " . ($m[1] ?? 'unknown') . "\n";
} else {
    echo "Count text not found.\n";
}

if (strpos($html, 'Grade 10') !== false && strpos($html, 'CLS-10') !== false) {
    echo "SUCCESS: Found Grade 10 and CLS-10 in table.\n";
} else {
    echo "Grade 10 not found.\n";
}

if (strpos($html, 'LKG') !== false && strpos($html, 'CLS-LKG') !== false) {
    echo "SUCCESS: Found LKG and CLS-LKG in table.\n";
} else {
    echo "LKG not found.\n";
}
