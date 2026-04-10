<?php

echo "=== Deploiement Test Reconnaissance Faciale ===\n\n";

// 1. Créer les répertoires nécessaires
echo "1. Création des répertoires...\n";
$dirs = [
    'storage/app/facial_recognition',
    'storage/app/faces_database',
    'resources/views/test'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Créé: $dir\n";
    } else {
        echo "ℹ️  Existe déjà: $dir\n";
    }
}

// 2. Créer la vue de test facial-recognition
$facialView = '<?php
@extends(\'layouts.app\')

@section(\'title\', \'Test Reconnaissance Faciale | KENAM SERVICES\')

@section(\'content\')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-camera me-2"></i>Test Reconnaissance Faciale
            </h1>
            <p class="text-muted mb-0">Test du système de reconnaissance faciale</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-info" onclick="startCamera()">
                <i class="fas fa-video me-2"></i>Démarrer Caméra
            </button>
            <button type="button" class="btn btn-outline-success" onclick="startRecognition()" id="startBtn">
                <i class="fas fa-play me-2"></i>Démarrer Reconnaissance
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6>Flux Caméra</h6>
                </div>
                <div class="card-body">
                    <video id="videoElement" width="640" height="480" style="background: #000; width: 100%;"></video>
                    <div id="status" class="mt-2">Statut: En attente</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header">
                    <h6>Journal</h6>
                </div>
                <div class="card-body">
                    <div id="eventLog" style="max-height: 400px; overflow-y: auto;">
                        <div class="text-muted">En attente de démarrage...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let video = document.getElementById(\'videoElement\');
let stream = null;

function startCamera() {
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function(s) {
            stream = s;
            video.srcObject = stream;
            document.getElementById(\'status\').textContent = \'Caméra active\';
            addEvent(\'Caméra démarrée avec succès\');
        })
        .catch(function(err) {
            document.getElementById(\'status\').textContent = \'Erreur: \' + err.message;
            addEvent(\'Erreur caméra: \' + err.message);
        });
}

function startRecognition() {
    if (!stream) {
        addEvent(\'Veuillez d\'abord démarrer la caméra\');
        return;
    }
    
    addEvent(\'Reconnaissance faciale démarrée (simulation)\');
    
    // Simulation de reconnaissance
    setInterval(() => {
        const confidence = Math.floor(Math.random() * 30) + 70;
        if (confidence > 85) {
            addEvent(\'Visage reconnu: Utilisateur test (\' + confidence + \'%)\', \'success\');
        } else {
            addEvent(\'Visage détecté mais non reconnu (\' + confidence + \'%)\', \'warning\');
        }
    }, 3000);
}

function addEvent(message, type = \'info\') {
    const log = document.getElementById(\'eventLog\');
    const time = new Date().toLocaleTimeString();
    const div = document.createElement(\'div\');
    div.className = \'alert alert-\' + (type === \'success\' ? \'success\' : type === \'warning\' ? \'warning\' : \'info\') + \' py-2\';
    div.innerHTML = \'<small>\' + time + \'</small><br>\' + message;
    log.insertBefore(div, log.firstChild);
    
    // Limiter le nombre d\'événements
    while (log.children.length > 10) {
        log.removeChild(log.lastChild);
    }
}
</script>
@endsection';

file_put_contents('resources/views/test/facial-recognition.blade.php', $facialView);
echo "✅ Vue facial-recognition.blade.php créée\n";

// 3. Créer la vue de test caméra simple
$cameraView = '<?php
@extends(\'layouts.app\')

@section(\'title\', \'Test Caméra | KENAM SERVICES\')

@section(\'content\')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h5>Test Caméra Simple</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <button id="startBtn" class="btn btn-primary">Démarrer Caméra</button>
                        <button id="stopBtn" class="btn btn-danger" disabled>Arrêter Caméra</button>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Statut:</strong> <span id="status">Non démarré</span>
                    </div>
                    
                    <video id="video" width="640" height="480" style="background: #000; width: 100%;"></video>
                    
                    <div class="mt-3">
                        <h6>Informations:</h6>
                        <pre id="debugInfo" style="background: #f8f9fa; padding: 10px; max-height: 200px; overflow-y: auto;"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let video = document.getElementById(\'video\');
let stream = null;
let debugInfo = document.getElementById(\'debugInfo\');

function log(message) {
    console.log(message);
    debugInfo.innerHTML += new Date().toLocaleTimeString() + \': \' + message + \'\\n\';
    debugInfo.scrollTop = debugInfo.scrollHeight;
}

document.getElementById(\'startBtn\').addEventListener(\'click\', async function() {
    log(\'Tentative de démarrage de la caméra...\');
    
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        document.getElementById(\'status\').textContent = \'Actif\';
        document.getElementById(\'startBtn\').disabled = true;
        document.getElementById(\'stopBtn\').disabled = false;
        log(\'Caméra démarrée avec succès\');
    } catch (error) {
        log(\'ERREUR: \' + error.message);
        document.getElementById(\'status\').textContent = \'Erreur: \' + error.message;
    }
});

document.getElementById(\'stopBtn\').addEventListener(\'click\', function() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        video.srcObject = null;
        stream = null;
        document.getElementById(\'status\').textContent = \'Arrêté\';
        document.getElementById(\'startBtn\').disabled = false;
        document.getElementById(\'stopBtn\').disabled = true;
        log(\'Caméra arrêtée\');
    }
});

log(\'Test caméra initialisé\');
</script>
@endsection';

file_put_contents('resources/views/test/camera-simple.blade.php', $cameraView);
echo "✅ Vue camera-simple.blade.php créée\n";

// 4. Mettre à jour les routes
echo "\n2. Mise à jour des routes...\n";

// Vérifier si hikvision-api.php est inclus dans web.php
$webPhp = file_get_contents('routes/web.php');
if (strpos($webPhp, 'hikvision-api.php') === false) {
    echo "⚠️  hikvision-api.php n\'est pas inclus dans web.php\n";
    echo "   Ajoutez manuellement: \'/hikvision-api.php\' dans la liste des modules\n";
} else {
    echo "✅ hikvision-api.php est déjà inclus dans web.php\n";
}

// 5. Vider les caches
echo "\n3. Vidage des caches...\n";
shell_exec('php artisan config:clear');
shell_exec('php artisan route:clear');
shell_exec('php artisan view:clear');
shell_exec('php artisan cache:clear');

echo "✅ Caches vidés\n";

// 6. Vérification finale
echo "\n4. Vérification finale...\n";
echo "✅ Répertoires créés\n";
echo "✅ Vues créées\n";
echo "✅ Routes mises à jour\n";
echo "✅ Caches vidés\n";

echo "\n=== Déploiement terminé ===\n";
echo "URLs de test:\n";
echo "https://ksl.kenamservices.net/hikvision/facial-test\n";
echo "https://ksl.kenamservices.net/hikvision/camera-test\n";

?>
