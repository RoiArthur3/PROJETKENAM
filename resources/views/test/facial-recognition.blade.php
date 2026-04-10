@extends('layouts.app')

@section('title', 'Test Reconnaissance Faciale | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
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
            <button type="button" class="btn btn-outline-danger" onclick="stopRecognition()" id="stopBtn" disabled>
                <i class="fas fa-stop me-2"></i>Arrêter
            </button>
        </div>
    </div>

    <!-- Statut du système -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Statut Caméra</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="cameraStatus">Inactif</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-video fa-2x text-gray-300" id="cameraIcon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Reconnaissance</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="recognitionStatus">Arrêté</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-face fa-2x text-gray-300" id="recognitionIcon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Visages Détectés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="facesDetected">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Confiance</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="confidenceLevel">0%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Zone de test -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-video me-2"></i>Flux Caméra
                    </h6>
                </div>
                <div class="card-body">
                    <div class="position-relative">
                        <video id="videoElement" class="w-100" style="max-height: 480px; background: #000;"></video>
                        <canvas id="canvasElement" class="position-absolute top-0 start-0 w-100" style="max-height: 480px; pointer-events: none;"></canvas>
                        
                        <!-- Overlay de détection -->
                        <div id="detectionOverlay" class="position-absolute top-0 start-0 w-100 h-100" style="max-height: 480px; pointer-events: none;">
                            <!-- Les boîtes de détection seront ajoutées ici dynamiquement -->
                        </div>
                    </div>
                    
                    <!-- Contrôles -->
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Seuil de confiance</label>
                                <input type="range" class="form-range" id="confidenceThreshold" min="50" max="100" value="85">
                                <small class="text-muted">Valeur actuelle: <span id="thresholdValue">85%</span></small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mode de détection</label>
                                <select class="form-select" id="detectionMode">
                                    <option value="single">Un seul visage</option>
                                    <option value="multiple">Visages multiples</option>
                                    <option value="tracking">Suivi de visage</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Journal des Événements
                    </h6>
                </div>
                <div class="card-body">
                    <div id="eventLog" style="max-height: 400px; overflow-y: auto;">
                        <div class="text-muted text-center py-3">
                            <i class="fas fa-info-circle fa-2x mb-2 opacity-50"></i>
                            <p>En attente de démarrage...</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cog me-2"></i>Configuration
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="showDetectionBox" checked>
                        <label class="form-check-label" for="showDetectionBox">
                            Afficher les boîtes de détection
                        </label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="showConfidence" checked>
                        <label class="form-check-label" for="showConfidence">
                            Afficher le score de confiance
                        </label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="enableSound">
                        <label class="form-check-label" for="enableSound">
                            Activer les notifications sonores
                        </label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="saveImages">
                        <label class="form-check-label" for="saveImages">
                            Sauvegarder les images de détection
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.detection-box {
    position: absolute;
    border: 2px solid #00ff00;
    background: rgba(0, 255, 0, 0.1);
    pointer-events: none;
}

.detection-label {
    position: absolute;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 2px 6px;
    font-size: 12px;
    border-radius: 3px;
    white-space: nowrap;
}

.event-item {
    padding: 8px;
    border-left: 3px solid #007bff;
    margin-bottom: 8px;
    background: #f8f9fa;
    border-radius: 4px;
}

.event-item.success {
    border-left-color: #28a745;
    background: #d4edda;
}

.event-item.warning {
    border-left-color: #ffc107;
    background: #fff3cd;
}

.event-item.error {
    border-left-color: #dc3545;
    background: #f8d7da;
}

.status-active {
    color: #28a745 !important;
}

.status-inactive {
    color: #6c757d !important;
}

.status-error {
    color: #dc3545 !important;
}
</style>

<script>
let video = null;
let canvas = null;
let stream = null;
let recognitionInterval = null;
let isRecognizing = false;

document.addEventListener('DOMContentLoaded', function() {
    video = document.getElementById('videoElement');
    canvas = document.getElementById('canvasElement');
    
    // Initialiser les écouteurs d'événements
    document.getElementById('confidenceThreshold').addEventListener('input', function() {
        document.getElementById('thresholdValue').textContent = this.value + '%';
    });
    
    addEvent('info', 'Système de reconnaissance faciale initialisé');
});

async function startCamera() {
    try {
        addEvent('info', 'Démarrage de la caméra...');
        
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                width: { ideal: 640 },
                height: { ideal: 480 }
            } 
        });
        
        video.srcObject = stream;
        video.play();
        
        updateStatus('camera', 'Actif', 'success');
        addEvent('success', 'Caméra démarrée avec succès');
        
    } catch (error) {
        console.error('Erreur caméra:', error);
        updateStatus('camera', 'Erreur', 'error');
        addEvent('error', 'Erreur lors du démarrage de la caméra: ' + error.message);
    }
}

function startRecognition() {
    if (!stream) {
        addEvent('warning', 'Veuillez d\'abord démarrer la caméra');
        return;
    }
    
    isRecognizing = true;
    document.getElementById('startBtn').disabled = true;
    document.getElementById('stopBtn').disabled = false;
    
    updateStatus('recognition', 'Actif', 'success');
    addEvent('info', 'Démarrage de la reconnaissance faciale...');
    
    // Simuler la reconnaissance (remplacer par vraie reconnaissance)
    recognitionInterval = setInterval(() => {
        if (isRecognizing) {
            simulateRecognition();
        }
    }, 1000);
}

function stopRecognition() {
    isRecognizing = false;
    
    if (recognitionInterval) {
        clearInterval(recognitionInterval);
        recognitionInterval = null;
    }
    
    document.getElementById('startBtn').disabled = false;
    document.getElementById('stopBtn').disabled = true;
    
    updateStatus('recognition', 'Arrêté', 'inactive');
    addEvent('info', 'Reconnaissance faciale arrêtée');
    
    // Nettoyer les boîtes de détection
    clearDetectionBoxes();
}

function simulateRecognition() {
    // Simuler une détection de visage
    const threshold = parseInt(document.getElementById('confidenceThreshold').value);
    const confidence = Math.floor(Math.random() * 50) + 50; // 50-100%
    
    if (confidence >= threshold) {
        // Simuler une reconnaissance réussie
        const names = ['Jean Dupont', 'Marie Martin', 'Pierre Durand', 'Sophie Lefebvre'];
        const randomName = names[Math.floor(Math.random() * names.length)];
        
        updateStatus('faces', Math.floor(Math.random() * 3) + 1, 'info');
        updateStatus('confidence', confidence + '%', confidence >= threshold ? 'success' : 'warning');
        
        addEvent('success', `Visage reconnu: ${randomName} (${confidence}%)`);
        
        // Ajouter une boîte de détection
        if (document.getElementById('showDetectionBox').checked) {
            addDetectionBox(100, 50, 200, 200, randomName, confidence);
        }
        
        // Jouer un son si activé
        if (document.getElementById('enableSound').checked) {
            playBeep();
        }
    } else {
        updateStatus('confidence', confidence + '%', 'warning');
        addEvent('warning', `Visage détecté mais non reconnu (${confidence}%)`);
    }
}

function addDetectionBox(x, y, width, height, name, confidence) {
    const overlay = document.getElementById('detectionOverlay');
    
    // Supprimer les boîtes existantes
    clearDetectionBoxes();
    
    const box = document.createElement('div');
    box.className = 'detection-box';
    box.style.left = x + 'px';
    box.style.top = y + 'px';
    box.style.width = width + 'px';
    box.style.height = height + 'px';
    
    if (document.getElementById('showConfidence').checked) {
        const label = document.createElement('div');
        label.className = 'detection-label';
        label.textContent = `${name} (${confidence}%)`;
        label.style.top = (y - 25) + 'px';
        label.style.left = x + 'px';
        overlay.appendChild(label);
    }
    
    overlay.appendChild(box);
}

function clearDetectionBoxes() {
    const overlay = document.getElementById('detectionOverlay');
    overlay.innerHTML = '';
}

function updateStatus(type, value, status) {
    const statusElement = document.getElementById(type + 'Status');
    const iconElement = document.getElementById(type + 'Icon');
    
    if (statusElement) {
        statusElement.textContent = value;
        statusElement.className = 'h5 mb-0 font-weight-bold text-gray-800 status-' + status;
    }
    
    if (iconElement) {
        iconElement.className = 'fas fa-' + getIconForType(type) + ' fa-2x text-' + getColorForStatus(status);
    }
}

function getIconForType(type) {
    const icons = {
        'camera': 'video',
        'recognition': 'face',
        'faces': 'users',
        'confidence': 'chart-line'
    };
    return icons[type] || 'info-circle';
}

function getColorForStatus(status) {
    const colors = {
        'success': 'success',
        'warning': 'warning',
        'error': 'danger',
        'info': 'info',
        'inactive': 'secondary'
    };
    return colors[status] || 'secondary';
}

function addEvent(type, message) {
    const eventLog = document.getElementById('eventLog');
    const timestamp = new Date().toLocaleTimeString();
    
    const eventItem = document.createElement('div');
    eventItem.className = 'event-item ' + type;
    eventItem.innerHTML = `
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <small class="text-muted">${timestamp}</small>
                <div class="mt-1">${message}</div>
            </div>
            <i class="fas fa-${getIconForEventType(type)} text-${getColorForEventType(type)}"></i>
        </div>
    `;
    
    // Ajouter au début du journal
    eventLog.insertBefore(eventItem, eventLog.firstChild);
    
    // Limiter le nombre d'événements affichés
    while (eventLog.children.length > 50) {
        eventLog.removeChild(eventLog.lastChild);
    }
}

function getIconForEventType(type) {
    const icons = {
        'success': 'check-circle',
        'warning': 'exclamation-triangle',
        'error': 'times-circle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

function getColorForEventType(type) {
    const colors = {
        'success': 'success',
        'warning': 'warning',
        'error': 'danger',
        'info': 'primary'
    };
    return colors[type] || 'primary';
}

function playBeep() {
    // Créer un bip sonore simple
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.value = 1000;
    oscillator.type = 'sine';
    
    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
    
    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + 0.1);
}

// Nettoyer lors de la fermeture de la page
window.addEventListener('beforeunload', function() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
    if (recognitionInterval) {
        clearInterval(recognitionInterval);
    }
});
</script>
@endsection
