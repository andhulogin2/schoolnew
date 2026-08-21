<?php
$conn = new mysqli('localhost', 'root', '', 'db_school');
// Check CSRF in config
$config_content = file_get_contents(__DIR__ . '/../application/config/config.php');
if (preg_match("/csrf_protection.*?=.*?(TRUE|true|FALSE|false)/i", $config_content, $m)) {
    echo "CSRF setting: " . $m[0] . "\n";
}

$cookie_file = __DIR__ . '/cookie.txt';
if (file_exists($cookie_file)) unlink($cookie_file);

$ch = curl_init('http://localhost/schoolnew/users/create');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
$html = curl_exec($ch);

// Find csrf token in HTML if present
if (preg_match('/name="(csrf_[^"]+)"\s+value="([^"]+)"/', $html, $matches)) {
    echo "CSRF token found: {$matches[1]} = {$matches[2]}\n";
    $csrf_name = $matches[1];
    $csrf_val = $matches[2];
} else {
    echo "No CSRF token input found.\n";
    $csrf_name = '';
    $csrf_val = '';
}

$post_data = [
    'name' => 'Duplicate Admin',
    'username' => 'admin',
    'email' => 'newadmin@school.com',
    'phone' => '1234567890',
    'role_id' => 1,
    'user_type' => 'Admin',
    'password' => 'password123'
];
if ($csrf_name) {
    $post_data[$csrf_name] = $csrf_val;
}

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
$html2 = curl_exec($ch);

echo "Response length: " . strlen($html2) . "\n";
echo "Response snippet:\n" . substr(strip_tags($html2), 0, 500) . "\n";
