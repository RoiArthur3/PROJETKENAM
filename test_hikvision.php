<?php

// Test direct avec le code fourni
$ip = '192.168.1.70'; // env('HIKVISION_IP', '192.168.1.70');
$username = 'admin'; // env('HIKVISION_USER', 'admin');
$password = 'Arthur@752'; // env('HIKVISION_PASS', 'Arthur@752');

echo "Test de connexion Hikvision\n";
echo "IP: $ip\n";
echo "User: $username\n";
echo "Pass: " . str_repeat('*', strlen($password)) . "\n\n";

$url = "http://$ip/ISAPI/AccessControl/AcsEvent";
echo "URL: $url\n\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($error) {
    echo "cURL Error: $error\n";
}

echo "\n--- Response ---\n";
echo $response;
echo "\n--- End Response ---\n";

// Test avec device info
echo "\n\n=== Test Device Info ===\n";
$deviceUrl = "http://$ip/ISAPI/System/deviceInfo";

$ch2 = curl_init($deviceUrl);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_USERPWD, "$username:$password");
curl_setopt($ch2, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch2, CURLOPT_TIMEOUT, 30);
curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);

$deviceResponse = curl_exec($ch2);
$deviceHttpCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
$deviceError = curl_error($ch2);
curl_close($ch2);

echo "Device Info HTTP Code: $deviceHttpCode\n";
if ($deviceError) {
    echo "Device Info cURL Error: $deviceError\n";
}

echo "\n--- Device Response ---\n";
echo $deviceResponse;
echo "\n--- End Device Response ---\n";
