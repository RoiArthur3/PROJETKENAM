@extends('layouts.app')

@section('title', 'Test Reconnaissance Faciale | KENAM SERVICES')

@section('content')
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
let video = document.getElementById('videoElement');
let stream = null;

function startCamera() {
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function(s) {
            stream = s;
            video.srcObject = stream;
            document.getElementById('status').textContent = 'Caméra active';
            addEvent('Caméra démarrée avec succès');
        })
        .catch(function(err) {
            document.getElementById('status').textContent = 'Erreur: ' + err.message;
            addEvent('Erreur caméra: ' + err.message);
        });
}

function startRecognition() {
    if (!stream) {
        addEvent('Veuillez d\'abord démarrer la caméra');
        return;
    }
    
    addEvent('Reconnaissance faciale démarrée (simulation)');
    
    // Simulation de reconnaissance
    setInterval(() => {
        const confidence = Math.floor(Math.random() * 30) + 70;
        if (confidence > 85) {
            addEvent('Visage reconnu: Utilisateur test (' + confidence + '%)', 'success');
        } else {
            addEvent('Visage détecté mais non reconnu (' + confidence + '%)', 'warning');
        }
    }, 3000);
}

function addEvent(message, type = 'info') {
    const log = document.getElementById('eventLog');
    const time = new Date().toLocaleTimeString();
    const div = document.createElement('div');
    div.className = 'alert alert-' + (type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info') + ' py-2';
    div.innerHTML = '<small>' + time + '</small><br>' + message;
    log.insertBefore(div, log.firstChild);
    
    // Limiter le nombre d'événements
    while (log.children.length > 10) {
        log.removeChild(log.lastChild);
    }
}
</script>
@endsection
