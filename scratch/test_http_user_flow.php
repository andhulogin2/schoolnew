<?php
$cookie_file = __DIR__ . '/cookie.txt';
if (file_exists($cookie_file)) unlink($cookie_file);

// 1. GET /users/create
$ch = curl_init('http://localhost/schoolnew/users/create');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
$html = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "1. GET /users/create -> HTTP $http_code\n";
if (strpos($html, 'Create New User Account') !== false) {
    echo "   [PASS] Add User page loaded successfully with form\n";
} else {
    echo "   [FAIL] Page title not found in response\n";
}

// 2. POST duplicate username 'admin'
curl_setopt($ch, CURLOPT_URL, 'http://localhost/schoolnew/users/create');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'name' => 'Duplicate Admin',
    'username' => 'admin',
    'email' => 'newadmin@school.com',
    'phone' => '1234567890',
    'role_id' => 1,
    'user_type' => 'Admin',
    'password' => 'password123'
]));
$html2 = curl_exec($ch);
echo "2. POST duplicate username 'admin' -> Response received\n";
if (strpos($html2, 'Username is already registered.') !== false) {
    echo "   [PASS] Specific error displayed: 'Username is already registered.'\n";
} else {
    echo "   [FAIL] Expected 'Username is already registered.', got different output\n";
}

// 3. POST duplicate email 'anjali.menon@gmail.com'
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'name' => 'Duplicate Email User',
    'username' => 'uniqueuser999',
    'email' => 'anjali.menon@gmail.com',
    'phone' => '1234567890',
    'role_id' => 1,
    'user_type' => 'Admin',
    'password' => 'password123'
]));
$html3 = curl_exec($ch);
echo "3. POST duplicate email 'anjali.menon@gmail.com' -> Response received\n";
if (strpos($html3, 'Email is already registered.') !== false) {
    echo "   [PASS] Specific error displayed: 'Email is already registered.'\n";
} else {
    echo "   [FAIL] Expected 'Email is already registered.', got different output\n";
}

// 4. POST both duplicate
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'name' => 'Both Duplicate',
    'username' => 'admin',
    'email' => 'anjali.menon@gmail.com',
    'phone' => '1234567890',
    'role_id' => 1,
    'user_type' => 'Admin',
    'password' => 'password123'
]));
$html4 = curl_exec($ch);
echo "4. POST duplicate username AND email -> Response received\n";
if (strpos($html4, 'Username and email are already registered.') !== false) {
    echo "   [PASS] Specific error displayed: 'Username and email are already registered.'\n";
} else {
    echo "   [FAIL] Expected 'Username and email are already registered.', got different output\n";
}

// 5. POST fresh user
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'name' => 'Fresh Test User',
    'username' => 'freshuser777',
    'email' => 'freshuser777@school.com',
    'phone' => '9998887776',
    'role_id' => 1,
    'user_type' => 'Admin',
    'password' => 'password123'
]));
$html5 = curl_exec($ch);
echo "5. POST valid fresh user -> Response received\n";
if (strpos($html5, "User account 'freshuser777' created successfully.") !== false) {
    echo "   [PASS] User created and redirected to users list with success message\n";
} else {
    echo "   [FAIL] Success flash message not found\n";
}

// Clean up fresh test user
$conn = new mysqli('localhost', 'root', '', 'db_school');
$conn->query("DELETE FROM tbl_users WHERE username = 'freshuser777'");
curl_close($ch);
if (file_exists($cookie_file)) unlink($cookie_file);
