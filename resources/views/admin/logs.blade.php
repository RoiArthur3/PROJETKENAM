@extends('layouts.app')

@section('title', 'Logs Système - Administration - KENAM Services')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-alt text-danger mr-2"></i>
            Logs du Système
        </h1>
        <div class="d-flex">
            <button onclick="refreshLogs()" class="btn btn-primary btn-sm mr-2">
                <i class="fas fa-sync-alt mr-1"></i> Actualiser
            </button>
            <button onclick="clearLogs()" class="btn btn-danger btn-sm">
                <i class="fas fa-trash mr-1"></i> Vider les logs
            </button>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i>
                Filtres
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-control" id="logLevel">
                        <option value="">Tous les niveaux</option>
                        <option value="emergency">Emergency</option>
                        <option value="alert">Alert</option>
                        <option value="critical">Critical</option>
                        <option value="error">Error</option>
                        <option value="warning">Warning</option>
                        <option value="notice">Notice</option>
                        <option value="info">Info</option>
                        <option value="debug">Debug</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" id="logSearch" placeholder="Rechercher dans les logs...">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="logDate">
                </div>
                <div class="col-md-3">
                    <button onclick="filterLogs()" class="btn btn-primary btn-block">
                        <i class="fas fa-search mr-1"></i> Filtrer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des logs -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-2"></i>
                Derniers Logs Système ({{ count($logs) }} entrées)
            </h6>
        </div>
        <div class="card-body">
            @if(count($logs) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="logsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Niveau</th>
                                <th>Message</th>
                                <th>Date/Heure</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr class="log-row" data-level="{{ $log->level }}">
                                    <td>
                                        <span class="badge badge-{{ $log->level === 'error' ? 'danger' : ($log->level === 'warning' ? 'warning' : ($log->level === 'info' ? 'info' : 'secondary')) }}">
                                            <i class="fas fa-{{ $log->level === 'error' ? 'exclamation-triangle' : ($log->level === 'warning' ? 'exclamation-circle' : ($log->level === 'info' ? 'info-circle' : 'bug')) }} mr-1"></i>
                                            {{ ucfirst($log->level) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="log-message">{{ $log->message }}</div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                                        </small>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" data-log-id="{{ $log->id ?? 'temp-' . $loop->index }}" onclick="viewLogDetails(this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun log trouvé</h5>
                    <p class="text-muted">Les logs système apparaîtront ici lorsqu'il y aura de l'activité.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Statistiques des logs -->
    <div class="row mt-4">
        <div class="col-lg-3 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Erreurs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $logs->where('level', 'error')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Avertissements
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $logs->where('level', 'warning')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Informations
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $logs->where('level', 'info')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-info-circle fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ count($logs) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour les détails du log -->
<div class="modal fade" id="logDetailsModal" tabindex="-1" role="dialog" aria-labelledby="logDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logDetailsModalLabel">Détails du Log</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="logDetailsContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
// Fonctions pour la gestion des logs
function refreshLogs() {
    location.reload();
}

function filterLogs() {
    const level = document.getElementById('logLevel').value.toLowerCase();
    const search = document.getElementById('logSearch').value.toLowerCase();
    const rows = document.querySelectorAll('.log-row');

    rows.forEach(row => {
        const rowLevel = row.dataset.level.toLowerCase();
        const message = row.querySelector('.log-message').textContent.toLowerCase();
        const levelMatch = !level || rowLevel === level;
        const searchMatch = !search || message.includes(search);

        row.style.display = levelMatch && searchMatch ? '' : 'none';
    });
}

function viewLogDetails(button) {
    const logId = button.getAttribute('data-log-id');
    // Simulation des détails du log
    const details = `
        <div class="row">
            <div class="col-md-6">
                <strong>ID du log:</strong><br>
                <code>${logId}</code>
            </div>
            <div class="col-md-6">
                <strong>Niveau:</strong><br>
                <span class="badge badge-danger">Error</span>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-12">
                <strong>Message complet:</strong><br>
                <pre class="bg-light p-3 rounded">Une erreur s'est produite lors de l'accès à la base de données. Veuillez vérifier la connexion.</pre>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <strong>Utilisateur:</strong><br>
                {{ auth()->user()->name }}
            </div>
            <div class="col-md-6">
                <strong>IP:</strong><br>
                127.0.0.1
            </div>
        </div>
    `;

    document.getElementById('logDetailsContent').innerHTML = details;
    $('#logDetailsModal').modal('show');
}

function clearLogs() {
    if (confirm('Êtes-vous sûr de vouloir vider tous les logs ? Cette action est irréversible.')) {
        alert('Fonctionnalité en cours de développement...');
        // Implémentation du vidage des logs
    }
}

// Recherche en temps réel
document.getElementById('logSearch').addEventListener('input', filterLogs);
document.getElementById('logLevel').addEventListener('change', filterLogs);
</script>
@endsection
