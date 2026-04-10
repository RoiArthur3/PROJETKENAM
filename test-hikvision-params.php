<?php

require_once 'vendor/autoload.php';

echo "=== Test Configuration Hikvision depuis Paramètres ===\n\n";

// Configuration depuis les paramètres (simulée)
$config = [
    'ip' => '192.168.1.70',
    'port' => 80,
    'protocol' => 'http',
    'user' => 'admin',
    'password' => 'Arthur@752',
    'timeout' => 30
];

echo "Configuration trouvée:\n";
echo "IP: {$config['ip']}\n";
echo "Port: {$config['port']}\n";
echo "Protocol: {$config['protocol']}\n";
echo "User: {$config['user']}\n";
echo "Password: " . str_repeat('*', strlen($config['password'])) . "\n";
echo "Timeout: {$config['timeout']}s\n\n";

// Construire l'URL
$url = "{$config['protocol']}://{$config['ip']}:{$config['port']}/ISAPI/System/deviceInfo";
echo "URL de test: $url\n\n";

// Test de connexion
$context = stream_context_create([
    'http' => [
        'timeout' => $config['timeout'],
        'method' => 'GET',
        'header' => 'Authorization: Basic ' . base64_encode($config['user'] . ':' . $config['password'])
    ]
]);

echo "Test de connexion...\n";
$response = @file_get_contents($url, false, $context);

if ($response !== false) {
    echo "✅ CONNEXION RÉUSSIE !\n";
    echo "Réponse du terminal:\n";
    echo substr($response, 0, 200) . "...\n";
    
    // Extraire le numéro de série
    if (preg_match('/<serialNumber>([^<]+)<\/serialNumber>/', $response, $matches)) {
        echo "\nNuméro de série: " . $matches[1] . "\n";
    }
    
    if (preg_match('/<model>([^<]+)<\/model>/', $response, $matches)) {
        echo "Modèle: " . $matches[1] . "\n";
    }
    
} else {
    echo "❌ CONNEXION ÉCHOUÉE\n";
    
    // Tester avec HTTPS
    $httpsUrl = "https://{$config['ip']}:{$config['port']}/ISAPI/System/deviceInfo";
    echo "\nTest avec HTTPS: $httpsUrl\n";
    
    $httpsContext = stream_context_create([
        'http' => [
            'timeout' => $config['timeout'],
            'method' => 'GET',
            'header' => 'Authorization: Basic ' . base64_encode($config['user'] . ':' . $config['password']),
            'ignore_errors' => true
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ]);
    
    $httpsResponse = @file_get_contents($httpsUrl, false, $httpsContext);
    
    if ($httpsResponse !== false) {
        echo "✅ CONNEXION HTTPS RÉUSSIE !\n";
        echo "Réponse: " . substr($httpsResponse, 0, 200) . "...\n";
    } else {
        echo "❌ CONNEXION HTTPS ÉCHOUÉE AUSSI\n";
        echo "Vérifiez:\n";
        echo "1. L'adresse IP du terminal\n";
        echo "2. Les identifiants (admin / Arthur@752)\n";
        echo "3. Que le service HTTP est activé sur le terminal\n";
    }
}

echo "\n=== Actions recommandées ===\n";
echo "1. Allez dans /parametrage\n";
echo "2. Cliquez sur 'Configurer' dans Reconnaissance Faciale\n";
echo "3. Vérifiez la Configuration Hikvision\n";
echo "4. Cliquez sur 'Tester la connexion'\n";
echo "5. Si ça échoue, modifiez l'IP ou les identifiants\n";

?>
