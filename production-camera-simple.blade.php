@extends('layouts.app')

@section('title', 'Test Caméra | KENAM SERVICES')

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
        stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        document.getElementById('status').textContent = 'Actif';
        document.getElementById('startBtn').disabled = true;
        document.getElementById('stopBtn').disabled = false;
        log('Caméra démarrée avec succès');
    } catch (error) {
        log('ERREUR: ' + error.message);
        document.getElementById('status').textContent = 'Erreur: ' + error.message;
    }
});

document.getElementById('stopBtn').addEventListener('click', function() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        video.srcObject = null;
        stream = null;
        document.getElementById('status').textContent = 'Arrêté';
        document.getElementById('startBtn').disabled = false;
        document.getElementById('stopBtn').disabled = true;
        log('Caméra arrêtée');
    }
});

log('Test caméra initialisé');
</script>
@endsection
