@extends('layouts.app')

@section('title', 'Surveillance en Direct - KENAM')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Panel de gauche : Flux vidéo -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4 bg-dark text-white overflow-hidden">
                <div class="card-header bg-dark border-bottom border-secondary d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-video me-2 text-primary"></i>
                        Flux en direct : {{ $device->name ?? 'Caméra Hikvision' }}
                    </h5>
                    <div>
                        <span id="connection-status" class="badge bg-secondary">Vérification...</span>
                        <span class="ms-2 small text-muted" id="last-update">Actualisé à : --:--</span>
                    </div>
                </div>
                <div class="card-body p-0 position-relative" style="min-height: 450px; background: #000;">
                    <div id="video-container" class="w-100 h-100 d-flex align-items-center justify-content-center">
                        <img id="camera-snapshot" 
                             src="{{ route('hikvision.snapshot', ['device_id' => $device->id ?? '']) }}" 
                             alt="Camera Feed" 
                             class="img-fluid"
                             style="max-height: 600px; display: none;"
                             onload="this.style.display='block'; hideLoading();"
                             onerror="showError();">
                        
                        <div id="loading-overlay" class="position-absolute top-50 start-50 translate-middle text-center">
                            <div class="spinner-border text-primary mb-2" role="status"></div>
                            <div class="small">Connexion à la caméra...</div>
                        </div>

                        <div id="error-overlay" class="position-absolute top-50 start-50 translate-middle text-center w-100 px-4" style="display: none;">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h5>Impossible de joindre la caméra</h5>
                            <p class="text-muted small">Vérifiez l'adresse IP ({{ $device->ip_address ?? env('HIKVISION_IP') }}) et les identifiants.</p>
                            <button class="btn btn-outline-light btn-sm mt-2" onclick="refreshImage()">Réessayer</button>
                        </div>
                    </div>

                    <!-- Overlay d'information en bas -->
                    <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-dark-gradient text-white d-flex justify-content-between align-items-end" style="background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                        <div>
                            <div class="badge bg-danger mb-1"><i class="fas fa-circle me-1 small"></i> REC</div>
                            <div class="small text-white-50">{{ $device->ip_address ?? '---' }} | 1080p | 1 fps (Rafraîchissement 30s)</div>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-primary" onclick="refreshImage()"><i class="fas fa-sync-alt me-1"></i>Actualiser</button>
                            <button class="btn btn-outline-light" onclick="toggleFullScreen()"><i class="fas fa-expand"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de droite : Historique récent -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-history me-2"></i>Dernières Détections
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="recent-events-list">
                        @forelse($recentEvents as $event)
                        <div class="list-group-item list-group-item-action border-0 py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    @if($event->pointage && $event->pointage->personnel && $event->pointage->personnel->photo_profil)
                                        <img src="{{ asset('storage/' . $event->pointage->personnel->photo_profil) }}" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                    @else
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-0 fw-bold small text-dark">{{ $event->pointage->personnel->nom_complet ?? 'Inconnu' }}</h6>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($event->event_time)->format('H:i') }}</small>
                                    </div>
                                    <div class="small text-muted">{{ $event->device->name ?? 'Caméra' }}</div>
                                    <div class="badge bg-success-soft text-success small mt-1">Reconnu - {{ number_format($event->confidence_score * 100, 1) }}%</div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <i class="fas fa-user-clock text-muted fa-2x mb-2 d-block"></i>
                            <p class="text-muted small">Aucun événement récent</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-light text-center py-2">
                    <a href="{{ route('rh.dashboard') }}" class="small text-decoration-none">Voir le tableau de bord</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const REFRESH_INTERVAL = 30000; // 30 secondes
    const snapshotUrl = "{{ route('hikvision.snapshot', ['device_id' => $device->id ?? '']) }}";
    const statusUrl = "{{ route('hikvision.status') }}";
    let refreshTimer;

    function hideLoading() {
        document.getElementById('loading-overlay').style.display = 'none';
        document.getElementById('error-overlay').style.display = 'none';
        updateTimestamp();
        checkStatus();
    }

    function showError() {
        document.getElementById('loading-overlay').style.display = 'none';
        document.getElementById('error-overlay').style.display = 'block';
        document.getElementById('camera-snapshot').style.display = 'none';
        
        const statusBadge = document.getElementById('connection-status');
        statusBadge.className = 'badge bg-danger';
        statusBadge.textContent = 'Hors ligne';
    }

    function refreshImage() {
        const img = document.getElementById('camera-snapshot');
        const loading = document.getElementById('loading-overlay');
        
        loading.style.display = 'block';
        // Ajouter un timestamp pour éviter le cache navigateur
        img.src = snapshotUrl + (snapshotUrl.includes('?') ? '&' : '?') + 't=' + new Date().getTime();
    }

    function updateTimestamp() {
        const now = new Date();
        document.getElementById('last-update').textContent = 'Actualisé à : ' + now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0') + ':' + now.getSeconds().toString().padStart(2, '0');
    }

    function checkStatus() {
        fetch(statusUrl)
            .then(response => response.json())
            .then(data => {
                const statusBadge = document.getElementById('connection-status');
                if (data.status === 'online') {
                    statusBadge.className = 'badge bg-success';
                    statusBadge.textContent = 'En ligne';
                } else {
                    statusBadge.className = 'badge bg-danger';
                    statusBadge.textContent = 'Hors ligne';
                }
            })
            .catch(() => {
                document.getElementById('connection-status').textContent = 'Erreur statut';
            });
    }

    function toggleFullScreen() {
        const container = document.getElementById('video-container');
        if (!document.fullscreenElement) {
            container.requestFullscreen().catch(err => {
                alert(`Erreur plein écran: ${err.message}`);
            });
        } else {
            document.exitFullscreen();
        }
    }

    // Lancer le timer de rafraîchissement
    document.addEventListener('DOMContentLoaded', () => {
        refreshTimer = setInterval(refreshImage, REFRESH_INTERVAL);
        checkStatus();
    });

</script>

<style>
    .bg-dark-gradient {
        background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.8) 100%);
    }
    .bg-success-soft {
        background-color: rgba(28, 200, 138, 0.1);
    }
    #video-container:-webkit-full-screen {
        width: 100%;
        height: 100%;
        background-color: #000;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #camera-snapshot {
        transition: opacity 0.3s ease-in-out;
    }
</style>
@endpush
