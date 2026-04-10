@extends('layouts.app')

@section('title', 'Configuration Hikvision')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-video text-danger"></i>
                        Configuration Hikvision
                    </h5>
                    <div>
                        <button type="button" class="btn btn-info btn-sm" onclick="testConnection()">
                            <i class="fas fa-plug"></i> Tester la connexion
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="runDiagnostic()">
                            <i class="fas fa-stethoscope"></i> Diagnostic
                        </button>
                        <button type="button" class="btn btn-success btn-sm" onclick="syncEmployees()">
                            <i class="fas fa-sync"></i> Synchroniser
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('hikvision.config.save') }}" id="hikvisionForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="hikvision_ip" class="form-label">Adresse IP de la caméra</label>
                                    <input type="text" class="form-control" id="hikvision_ip" name="hikvision_ip" 
                                           value="{{ $config['ip'] }}" required>
                                    <div class="form-text">Ex: 192.168.1.70</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="hikvision_port" class="form-label">Port</label>
                                    <input type="number" class="form-control" id="hikvision_port" name="hikvision_port" 
                                           value="{{ $config['port'] }}" min="1" max="65535" required>
                                    <div class="form-text">Port HTTP (généralement 80 ou 8080)</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="hikvision_username" class="form-label">Nom d'utilisateur</label>
                                    <input type="text" class="form-control" id="hikvision_username" name="hikvision_username" 
                                           value="{{ $config['username'] }}" required>
                                    <div class="form-text">Utilisateur administrateur de la caméra</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="hikvision_password" class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" id="hikvision_password" name="hikvision_password" 
                                           value="{{ $config['password'] }}" required>
                                    <div class="form-text">Mot de passe de l'administrateur</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="timeout" class="form-label">Timeout (secondes)</label>
                                    <input type="number" class="form-control" id="timeout" name="timeout" 
                                           value="{{ $config['timeout'] }}" min="1" max="300">
                                    <div class="form-text">Délai d'attente pour la connexion</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="endpoint" class="form-label">Endpoint API</label>
                                    <input type="text" class="form-control" id="endpoint" name="endpoint" 
                                           value="{{ $config['endpoint'] }}">
                                    <div class="form-text">Chemin de l'API des événements</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('parametrage.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Sauvegarder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Status -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card" id="statusCard" style="display: none;">
                <div class="card-header">
                    <h6 class="mb-0">Statut de la connexion</h6>
                </div>
                <div class="card-body" id="statusContent">
                    <!-- Le contenu sera inséré dynamiquement -->
                </div>
            </div>
        </div>
    </div>

    <!-- Section Diagnostic -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card" id="diagnosticCard" style="display: none;">
                <div class="card-header">
                    <h6 class="mb-0">Diagnostic complet</h6>
                </div>
                <div class="card-body" id="diagnosticContent">
                    <!-- Le contenu sera inséré dynamiquement -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function testConnection() {
    const formData = new FormData(document.getElementById('hikvisionForm'));
    
    fetch('{{ route("hikvision.test") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        showStatus(data);
    })
    .catch(error => {
        console.error('Erreur:', error);
        showStatus({
            success: false,
            message: 'Erreur lors du test de connexion'
        });
    });
}

function runDiagnostic() {
    const formData = new FormData(document.getElementById('hikvisionForm'));
    
    fetch('{{ route("hikvision.diagnostic.run") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        showDiagnostic(data);
    })
    .catch(error => {
        console.error('Erreur:', error);
        showDiagnostic({
            error: 'Erreur lors du diagnostic: ' + error.message
        });
    });
}

function syncEmployees() {
    if (!confirm('Lancer la synchronisation des employés avec la caméra Hikvision ?')) {
        return;
    }
    
    fetch('{{ route("hikvision.sync") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Synchronisation lancée avec succès!\n' + JSON.stringify(data.results, null, 2));
        } else {
            alert('Erreur lors de la synchronisation: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la synchronisation: ' + error.message);
    });
}

function showStatus(data) {
    const statusCard = document.getElementById('statusCard');
    const statusContent = document.getElementById('statusContent');
    
    let html = '';
    if (data.success) {
        html = `
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> ${data.message}
            </div>
            <div class="row">
                <div class="col-md-6">
                    <strong>Code HTTP:</strong> ${data.http_code}
                </div>
                <div class="col-md-6">
                    <strong>Statut:</strong> <span class="badge bg-success">Connecté</span>
                </div>
            </div>
        `;
        if (data.response) {
            html += `
                <div class="mt-3">
                    <strong>Réponse:</strong>
                    <pre class="bg-light p-2 small">${data.response}</pre>
                </div>
            `;
        }
    } else {
        html = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> ${data.message}
            </div>
            <div class="row">
                <div class="col-md-6">
                    <strong>Code HTTP:</strong> ${data.http_code || 'N/A'}
                </div>
                <div class="col-md-6">
                    <strong>Statut:</strong> <span class="badge bg-danger">Non connecté</span>
                </div>
            </div>
        `;
    }
    
    statusContent.innerHTML = html;
    statusCard.style.display = 'block';
}

function showDiagnostic(data) {
    const diagnosticCard = document.getElementById('diagnosticCard');
    const diagnosticContent = document.getElementById('diagnosticContent');
    
    let html = '';
    
    if (data.error) {
        html = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> ${data.error}
            </div>
        `;
    } else {
        // Configuration
        html += `
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6>Configuration</h6>
                    <table class="table table-sm">
                        <tr><td>IP:</td><td>${data.config.ip}</td></tr>
                        <tr><td>Port:</td><td>${data.config.port}</td></tr>
                        <tr><td>Utilisateur:</td><td>${data.config.username}</td></tr>
                        <tr><td>Mot de passe:</td><td>${data.config.password}</td></tr>
                        <tr><td>Timeout:</td><td>${data.config.timeout}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Environnement</h6>
                    <table class="table table-sm">
                        <tr><td>PHP:</td><td>${data.environment.php_version}</td></tr>
                        <tr><td>cURL:</td><td>${data.environment.curl_enabled ? 'Activé' : 'Désactivé'}</td></tr>
                        <tr><td>allow_url_fopen:</td><td>${data.environment.allow_url_fopen}</td></tr>
                    </table>
                </div>
            </div>
        `;
        
        // Connectivité
        if (data.connectivity) {
            const conn = data.connectivity;
            const connStatus = conn.success ? 'success' : 'danger';
            html += `
                <div class="row">
                    <div class="col-12">
                        <h6>Test de connectivité</h6>
                        <div class="alert alert-${connStatus}">
                            <i class="fas fa-${conn.success ? 'check' : 'times'}-circle"></i> ${conn.message}
                        </div>
                    </div>
                </div>
            `;
        }
    }
    
    diagnosticContent.innerHTML = html;
    diagnosticCard.style.display = 'block';
}
</script>
@endpush
