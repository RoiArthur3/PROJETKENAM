@extends('layouts.app')

@section('title', 'Test Caméra Simple | KENAM SERVICES')

@section('content')
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
                        <button id="testBtn" class="btn btn-info">Tester Permissions</button>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Statut:</strong> <span id="status">Non démarré</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Appareils disponibles:</strong>
                        <ul id="devicesList"></ul>
                    </div>
                    
                    <video id="video" width="640" height="480" autoplay style="background: #000; width: 100%;"></video>
                    
                    <div class="mt-3">
                        <h6>Informations de diagnostic:</h6>
                        <pre id="debugInfo" style="background: #f8f9fa; padding: 10px; border-radius: 5px; max-height: 200px; overflow-y: auto;"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let video = document.getElementById('video');
let stream = null;
let debugInfo = document.getElementById('debugInfo');

function log(message) {
    console.log(message);
    debugInfo.innerHTML += new Date().toLocaleTimeString() + ': ' + message + '\n';
    debugInfo.scrollTop = debugInfo.scrollHeight;
}

document.getElementById('startBtn').addEventListener('click', async function() {
    log('Tentative de démarrage de la caméra...');
    
    try {
        // Vérifier si getUserMedia est supporté
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error('getUserMedia n\'est pas supporté par ce navigateur');
        }
        
        log('getUserMedia est supporté');
        
        // Demander l'accès à la caméra
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: true,
            audio: false 
        });
        
        log('Flux vidéo obtenu avec succès');
        
        video.srcObject = stream;
        
        video.onloadedmetadata = function() {
            log('Métadonnées vidéo chargées');
            log('Dimensions: ' + video.videoWidth + 'x' + video.videoHeight);
            document.getElementById('status').textContent = 'Actif';
            document.getElementById('startBtn').disabled = true;
            document.getElementById('stopBtn').disabled = false;
        };
        
        video.onerror = function(error) {
            log('Erreur vidéo: ' + error);
        };
        
    } catch (error) {
        log('ERREUR: ' + error.message);
        document.getElementById('status').textContent = 'Erreur: ' + error.message;
        
        // Analyse détaillée de l'erreur
        if (error.name === 'NotAllowedError') {
            log('Permission refusée par l\'utilisateur');
            log('Solution: Accordez la permission dans les paramètres du navigateur');
        } else if (error.name === 'NotFoundError') {
            log('Aucun appareil vidéo trouvé');
            log('Vérifiez que la caméra est branchée et reconnue par Windows');
        } else if (error.name === 'NotReadableError') {
            log('Caméra déjà utilisée par une autre application');
            log('Fermez les autres applications utilisant la caméra');
        } else if (error.name === 'OverconstrainedError') {
            log('Contraintes non satisfaites par la caméra');
        } else if (error.name === 'SecurityError') {
            log('Erreur de sécurité - peut être due à HTTP au lieu de HTTPS');
        }
    }
});

document.getElementById('stopBtn').addEventListener('click', function() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        video.srcObject = null;
        stream = null;
        log('Caméra arrêtée');
        document.getElementById('status').textContent = 'Arrêté';
        document.getElementById('startBtn').disabled = false;
        document.getElementById('stopBtn').disabled = true;
    }
});

document.getElementById('testBtn').addEventListener('click', async function() {
    log('Test des permissions et appareils...');
    
    try {
        // Lister les appareils disponibles
        const devices = await navigator.mediaDevices.enumerateDevices();
        log('Appareils trouvés: ' + devices.length);
        
        const videoDevices = devices.filter(device => device.kind === 'videoinput');
        log('Appareils vidéo: ' + videoDevices.length);
        
        const devicesList = document.getElementById('devicesList');
        devicesList.innerHTML = '';
        
        videoDevices.forEach((device, index) => {
            const li = document.createElement('li');
            li.textContent = `${index + 1}. ${device.label || 'Appareil ' + (index + 1)}`;
            devicesList.appendChild(li);
        });
        
        // Vérifier les permissions
        const permission = await navigator.permissions.query({ name: 'camera' });
        log('État de la permission caméra: ' + permission.state);
        
        if (permission.state === 'granted') {
            log('Permission caméra accordée');
        } else if (permission.state === 'prompt') {
            log('Permission caméra en attente (sera demandée lors de l\'utilisation)');
        } else if (permission.state === 'denied') {
            log('Permission caméra refusée');
            log('Solution: Changez les permissions dans les paramètres du navigateur');
        }
        
    } catch (error) {
        log('ERREUR lors du test: ' + error.message);
    }
});

// Informations sur le navigateur
log('Navigateur: ' + navigator.userAgent);
log('Protocole: ' + window.location.protocol);
log('HTTPS requis pour les caméras: ' + (window.location.protocol === 'https:' ? 'Oui' : 'Non'));

</script>
@endsection
