@extends('layouts.app')

@section('title', 'Diagnostic Hikvision - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-camera me-2 text-info"></i>Diagnostic Dispositif Hikvision</h1>
    </div>

    <!-- Statistiques Base de Données -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-left-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Employés Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $dbStats['total_employees'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Employés Actifs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $dbStats['active_employees'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pointages (7j)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $dbStats['recent_pointages'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Événements Hikvision</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $dbStats['hikvision_events'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-camera fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte de statut -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-left-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Connexion</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="connection-status">Vérification...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-network-wired fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-left-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Configuration</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="config-status">Vérification...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cog fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-left-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Événements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="events-status">Vérification...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions de diagnostic -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <strong><i class="fas fa-stethoscope me-2"></i>Tests de diagnostic</strong>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <button onclick="testConnection()" class="btn btn-primary w-100">
                        <i class="fas fa-plug me-1"></i>Tester la connexion
                    </button>
                </div>
                <div class="col-md-6">
                    <button onclick="fetchEvents()" class="btn btn-info w-100">
                        <i class="fas fa-download me-1"></i>Récupérer les événements
                    </button>
                </div>
                <div class="col-md-6">
                    <button onclick="syncEmployees()" class="btn btn-success w-100">
                        <i class="fas fa-sync me-1"></i>Synchroniser les employés
                    </button>
                </div>
                <div class="col-md-6">
                    <button onclick="checkSystemInfo()" class="btn btn-warning w-100">
                        <i class="fas fa-info-circle me-1"></i>Informations système
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Résultats des tests -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <strong><i class="fas fa-clipboard-list me-2"></i>Résultats des tests</strong>
        </div>
        <div class="card-body">
            <div id="test-results" class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Cliquez sur les boutons ci-dessus pour tester le dispositif Hikvision.
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function updateStatus(elementId, status, message, type = 'info') {
    const element = document.getElementById(elementId);
    const card = element.closest('.card');
    const borderClass = type === 'success' ? 'border-left-success' :
                       type === 'warning' ? 'border-left-warning' :
                       type === 'danger' ? 'border-left-danger' : 'border-left-primary';

    element.textContent = status;
    card.className = card.className.replace(/border-left-\w+/, borderClass);
}

function updateResults(message, type = 'info') {
    const resultsDiv = document.getElementById('test-results');
    const alertClass = type === 'success' ? 'alert-success' :
                       type === 'warning' ? 'alert-warning' :
                       type === 'danger' ? 'alert-danger' : 'alert-info';

    resultsDiv.className = `alert ${alertClass}`;
    resultsDiv.innerHTML = message;
}

async function testConnection() {
    updateResults('<i class="fas fa-spinner fa-spin me-2"></i>Test de connexion en cours...', 'info');

    try {
        const response = await fetch('/api/hikvision/test-connection', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                ip: '{{ config("hikvision.default_ip", "192.168.1.70") }}',
                username: '{{ config("hikvision.default_user", "admin") }}',
                password: '{{ config("hikvision.default_password", "") }}',
                port: '{{ config("hikvision.default_port", "80") }}',
                protocol: '{{ config("hikvision.default_protocol", "http") }}'
            })
        });

        const result = await response.json();

        if (result.success) {
            updateStatus('connection-status', 'Connecté', 'success');
            updateResults('<i class="fas fa-check-circle me-2"></i>Connexion réussie au dispositif Hikvision!<br><small>IP: ' + result.ip + ', Port: ' + result.port + '</small>', 'success');
        } else {
            updateStatus('connection-status', 'Échec', 'danger');
            updateResults('<i class="fas fa-times-circle me-2"></i>Échec de connexion: ' + result.message, 'danger');
        }
    } catch (error) {
        updateStatus('connection-status', 'Erreur', 'danger');
        updateResults('<i class="fas fa-exclamation-triangle me-2"></i>Erreur lors du test: ' + error.message, 'danger');
    }
}

async function fetchEvents() {
    updateResults('<i class="fas fa-spinner fa-spin me-2"></i>Récupération des événements...', 'info');

    try {
        const response = await fetch('/api/hikvision/fetch-events', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        });

        const result = await response.json();

        if (result.success) {
            updateStatus('events-status', result.events_count + ' événements', 'success');
            updateResults('<i class="fas fa-check-circle me-2"></i>Récupération réussie: ' + result.events_count + ' événements trouvés', 'success');
        } else {
            updateStatus('events-status', 'Échec', 'danger');
            updateResults('<i class="fas fa-times-circle me-2"></i>Échec de récupération: ' + result.message, 'danger');
        }
    } catch (error) {
        updateStatus('events-status', 'Erreur', 'danger');
        updateResults('<i class="fas fa-exclamation-triangle me-2"></i>Erreur lors de la récupération: ' + error.message, 'danger');
    }
}

async function syncEmployees() {
    updateResults('<i class="fas fa-spinner fa-spin me-2"></i>Synchronisation des employés...', 'info');

    try {
        const response = await fetch('{{ route("hikvision.sync-employees") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        });

        const result = await response.json();

        if (result.success) {
            updateStatus('config-status', result.synced_count + ' synchronisés', 'success');
            updateResults('<i class="fas fa-check-circle me-2"></i>Synchronisation réussie: ' + result.synced_count + ' employés synchronisés', 'success');
        } else {
            updateStatus('config-status', 'Échec', 'danger');
            updateResults('<i class="fas fa-times-circle me-2"></i>Échec de synchronisation: ' + result.message, 'danger');
        }
    } catch (error) {
        updateStatus('config-status', 'Erreur', 'danger');
        updateResults('<i class="fas fa-exclamation-triangle me-2"></i>Erreur lors de la synchronisation: ' + error.message, 'danger');
    }
}

async function checkSystemInfo() {
    updateResults('<i class="fas fa-spinner fa-spin me-2"></i>Vérification des informations système...', 'info');

    try {
        const response = await fetch('{{ route("hikvision.test-env") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        });

        const result = await response.json();

        if (result.success) {
            updateStatus('config-status', 'OK', 'success');
            updateResults('<i class="fas fa-check-circle me-2"></i>Dispositif Hikvision accessible et fonctionnel<br><small>Version: ' + (result.version || 'N/A') + '</small>', 'success');
        } else {
            updateStatus('config-status', 'Échec', 'danger');
            updateResults('<i class="fas fa-times-circle me-2"></i>Dispositif non accessible: ' + result.message, 'danger');
        }
    } catch (error) {
        updateStatus('config-status', 'Erreur', 'danger');
        updateResults('<i class="fas fa-exclamation-triangle me-2"></i>Erreur lors de la vérification: ' + error.message, 'danger');
    }
}

// Test automatique au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    testConnection();
});
</script>
@endsection
