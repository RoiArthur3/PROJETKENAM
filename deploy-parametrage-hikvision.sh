#!/bin/bash

echo "=== Déploiement Solution Hikvision via Paramètres ==="
echo ""

# 1. Créer la route qui utilise la configuration des paramètres
cat >> routes/web.php << 'EOF'

// Test Hikvision via configuration des paramètres
Route::get('/hikvision/test-params', function() {
    return view('hikvision.test-params');
})->middleware('auth')->name('hikvision.test-params');

// API de test Hikvision
Route::post('/hikvision/api-test', function() {
    $ip = request('ip', config('hikvision.default_ip', '192.168.1.70'));
    $port = request('port', config('hikvision.default_port', 80));
    $protocol = request('protocol', config('hikvision.default_protocol', 'http'));
    $user = request('user', config('hikvision.default_user', 'admin'));
    $password = request('password', config('hikvision.default_password', 'Arthur@752'));
    
    $url = "{$protocol}://{$ip}:{$port}/ISAPI/System/deviceInfo";
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'method' => 'GET',
            'header' => 'Authorization: Basic ' . base64_encode($user . ':' . $password),
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false) {
        // Extraire les informations
        $serialNumber = '';
        $model = '';
        $deviceName = '';
        
        if (preg_match('/<serialNumber>([^<]+)<\/serialNumber>/', $response, $matches)) {
            $serialNumber = $matches[1];
        }
        
        if (preg_match('/<model>([^<]+)<\/model>/', $response, $matches)) {
            $model = $matches[1];
        }
        
        if (preg_match('/<deviceName>([^<]+)<\/deviceName>/', $response, $matches)) {
            $deviceName = $matches[1];
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'device_info' => [
                'ip' => $ip,
                'model' => $model,
                'serial_number' => $serialNumber,
                'device_name' => $deviceName
            ]
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Échec de connexion',
            'error' => 'Vérifiez l\'IP, les identifiants et que le service HTTP est activé'
        ]);
    }
})->name('hikvision.api-test');
EOF

echo "✅ Routes ajoutées"

# 2. Créer la vue de test
mkdir -p resources/views/hikvision

cat > resources/views/hikvision/test-params.blade.php << 'EOF'
@extends('layouts.app')

@section('title', 'Test Hikvision via Paramètres | KENAM')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cog me-2"></i>Test Hikvision via Paramètres
            </h1>
            <p class="text-muted mb-0">Utilise la configuration de /parametrage</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" onclick="testConnection()">
                <i class="fas fa-plug me-2"></i>Tester Connexion
            </button>
            <button type="button" class="btn btn-info" onclick="openParametrage()">
                <i class="fas fa-cogs me-2"></i>Ouvrir Paramètres
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6>Configuration Actuelle (depuis paramètres)</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <strong>IP:</strong><br>
                            <span class="text-primary">192.168.1.70</span>
                        </div>
                        <div class="col-6">
                            <strong>Port:</strong><br>
                            <span class="text-primary">80</span>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <strong>Utilisateur:</strong><br>
                            <span class="text-primary">admin</span>
                        </div>
                        <div class="col-6">
                            <strong>Protocole:</strong><br>
                            <span class="text-primary">HTTP</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6>Résultat du Test</h6>
                </div>
                <div class="card-body">
                    <div id="testResult">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle fa-2x mb-2"></i><br>
                            Cliquez sur "Tester Connexion" pour vérifier
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h6><i class="fas fa-lightbulb me-2"></i>Actions Recommandées</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-cogs fa-3x text-primary mb-2"></i>
                                <h6>1. Configurer dans /parametrage</h6>
                                <p class="small">Allez dans Paramètres → Reconnaissance Faciale → Configurer</p>
                                <button class="btn btn-primary btn-sm" onclick="openParametrage()">Ouvrir Paramètres</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-plug fa-3x text-success mb-2"></i>
                                <h6>2. Tester la connexion</h6>
                                <p class="small">Utilisez "Tester la connexion" dans les paramètres Hikvision</p>
                                <button class="btn btn-success btn-sm" onclick="testConnection()">Tester Maintenant</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-save fa-3x text-info mb-2"></i>
                                <h6>3. Sauvegarder</h6>
                                <p class="small">Enregistrez la configuration comme défaut</p>
                                <button class="btn btn-info btn-sm" onclick="saveConfig()">Sauvegarder Config</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function testConnection() {
    document.getElementById('testResult').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Test en cours...</div>';
    
    fetch('/hikvision/api-test', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('testResult').innerHTML = `
                <div class="alert alert-success">
                    <h5><i class="fas fa-check-circle me-2"></i>Connexion Réussie !</h5>
                    <p><strong>Appareil:</strong> ${data.device_info.device_name}</p>
                    <p><strong>Modèle:</strong> ${data.device_info.model}</p>
                    <p><strong>Numéro de série:</strong> ${data.device_info.serial_number}</p>
                    <p><strong>IP:</strong> ${data.device_info.ip}</p>
                </div>
            `;
        } else {
            document.getElementById('testResult').innerHTML = `
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Échec de Connexion</h5>
                    <p>${data.message}</p>
                    <p><strong>Solution:</strong> ${data.error}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        document.getElementById('testResult').innerHTML = `
            <div class="alert alert-warning">
                <h5><i class="fas fa-exclamation me-2"></i>Erreur de Test</h5>
                <p>Erreur: ${error.message}</p>
            </div>
        `;
    });
}

function openParametrage() {
    window.open('/parametrage', '_blank');
}

function saveConfig() {
    alert('Utilisez le bouton "Enregistrer comme défaut" dans les paramètres Hikvision');
}
</script>
@endsection
EOF

echo "✅ Vue créée"

# 3. Vider les caches
php artisan route:clear
php artisan view:clear

echo "✅ Caches vidés"
echo ""
echo "=== Déploiement terminé ==="
echo ""
echo "URL de test:"
echo "https://ksl.kenamservices.net/hikvision/test-params"
echo ""
echo "URL des paramètres:"
echo "https://ksl.kenamservices.net/parametrage"
echo ""

echo "Actions:"
echo "1. Testez la connexion via /hikvision/test-params"
echo "2. Si ça échoue, allez dans /parametrage"
echo "3. Modifiez la configuration Hikvision"
echo "4. Testez avec le bouton 'Tester la connexion'"
echo "5. Sauvegardez comme défaut"

EOF

chmod +x deploy-parametrage-hikvision.sh
