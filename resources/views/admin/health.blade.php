@extends('layouts.app')

@section('title', 'Santé Système - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-heartbeat me-2 text-success"></i>Santé Système
            </h1>
            <p class="text-muted mb-0">Monitoring et diagnostics du système en production</p>
        </div>
        <div>
            <button type="button" class="btn btn-outline-primary" onclick="refreshHealth()">
                <i class="fas fa-sync-alt me-1"></i>Actualiser
            </button>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Indicateur global de santé -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                État général du système
                            </div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Statut global
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            @php
                                $overallStatus = collect($health)->pluck('status');
                                $isHealthy = !$overallStatus->contains('unhealthy');
                                $hasWarnings = $overallStatus->contains('warning');
                            @endphp
                            @if($isHealthy && !$hasWarnings)
                                <span class="badge bg-success fs-6 px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>Sain
                                </span>
                            @elseif($hasWarnings)
                                <span class="badge bg-warning fs-6 px-3 py-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Avertissements
                                </span>
                            @else
                                <span class="badge bg-danger fs-6 px-3 py-2">
                                    <i class="fas fa-times-circle me-1"></i>Problèmes détectés
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métriques système -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Temps de réponse
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $health['performance']['metrics']['load_time'] ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Requêtes DB
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $health['performance']['metrics']['queries_count'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Utilisateurs actifs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $health['performance']['metrics']['active_users'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Erreurs récentes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $health['performance']['metrics']['recent_errors'] ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails par composant -->
    <div class="row">
        <!-- Informations système -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-server me-2"></i>Informations Système
                    </h6>
                </div>
                <div class="card-body">
                    @php $sys = $health['system'] @endphp
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <strong>PHP:</strong> {{ $sys['php_version'] }}
                            </div>
                            <div class="mb-3">
                                <strong>Laravel:</strong> {{ $sys['laravel_version'] }}
                            </div>
                            <div class="mb-3">
                                <strong>Serveur:</strong> {{ $sys['server'] }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <strong>Environnement:</strong> {{ $sys['environment'] }}
                            </div>
                            <div class="mb-3">
                                <strong>Debug:</strong>
                                <span class="badge {{ $sys['debug_mode'] === 'Activé' ? 'bg-warning' : 'bg-success' }}">
                                    {{ $sys['debug_mode'] }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <strong>Fuseau:</strong> {{ $sys['timezone'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Base de données -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-database me-2"></i>Base de Données
                    </h6>
                </div>
                <div class="card-body">
                    @php $db = $health['database'] @endphp
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span><strong>Statut:</strong></span>
                        <span class="badge {{ $db['status'] === 'healthy' ? 'bg-success' : 'bg-danger' }}">
                            {{ $db['message'] }}
                        </span>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <strong>Base:</strong> {{ $db['database'] }}
                            </div>
                            <div class="mb-3">
                                <strong>Tables:</strong> {{ $db['tables'] }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <strong>Réponse:</strong> {{ $db['response_time'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stockage -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-hdd me-2"></i>Stockage & Permissions
                    </h6>
                </div>
                <div class="card-body">
                    @php $storage = $health['storage'] @endphp
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span><strong>Permissions:</strong></span>
                        <span class="badge {{ $storage['status'] === 'healthy' ? 'bg-success' : 'bg-warning' }}">
                            {{ $storage['writable_paths'] }}/{{ $storage['total_paths'] }} OK
                        </span>
                    </div>
                    <p class="text-sm text-muted mb-0">{{ $storage['message'] }}</p>
                </div>
            </div>
        </div>

        <!-- Cache -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-memory me-2"></i>Cache & Mémoire
                    </h6>
                </div>
                <div class="card-body">
                    @php $cache = $health['cache']; $memory = $health['memory'] @endphp
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <strong>Cache:</strong>
                                <span class="badge {{ $cache['status'] === 'healthy' ? 'bg-success' : 'bg-danger' }} ms-2">
                                    {{ $cache['message'] }}
                                </span>
                            </div>
                            <div class="mb-2">
                                <strong>Driver:</strong> {{ $cache['driver'] }}
                            </div>
                            <div class="mb-2">
                                <strong>Réponse:</strong> {{ $cache['response_time'] }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <strong>Mémoire:</strong>
                                <span class="badge {{ $memory['status'] === 'healthy' ? 'bg-success' : 'bg-warning' }} ms-2">
                                    {{ $memory['message'] }}
                                </span>
                            </div>
                            <div class="mb-2">
                                <strong>Utilisation:</strong> {{ $memory['current_usage'] }}
                            </div>
                            <div class="mb-2">
                                <strong>Limit:</strong> {{ $memory['limit'] }}
                            </div>
                            <div class="mb-2">
                                <strong>% Usage:</strong> {{ $memory['usage_percent'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Espace disque -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Espace Disque
                    </h6>
                </div>
                <div class="card-body">
                    @php $disk = $health['disk'] @endphp
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span><strong>Statut:</strong></span>
                        <span class="badge {{ $disk['status'] === 'healthy' ? 'bg-success' : ($disk['status'] === 'warning' ? 'bg-warning' : 'bg-danger') }}">
                            {{ $disk['message'] }}
                        </span>
                    </div>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="text-xs text-muted">Total</div>
                            <div class="h6 mb-0">{{ $disk['total'] }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-xs text-muted">Utilisé</div>
                            <div class="h6 mb-0">{{ $disk['used'] }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-xs text-muted">Libre</div>
                            <div class="h6 mb-0">{{ $disk['free'] }}</div>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar {{ $disk['usage_percent'] > 90 ? 'bg-danger' : ($disk['usage_percent'] > 80 ? 'bg-warning' : 'bg-success') }}"
                             style="width: {{ $disk['usage_percent'] }}%">
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">{{ $disk['usage_percent'] }} utilisé</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs me-2"></i>Services
                    </h6>
                </div>
                <div class="card-body">
                    @php $services = $health['services'] @endphp
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span><strong>Statut global:</strong></span>
                        <span class="badge {{ $services['status'] === 'healthy' ? 'bg-success' : 'bg-warning' }}">
                            {{ $services['message'] }}
                        </span>
                    </div>
                    @foreach($services['services'] as $name => $service)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-capitalize">{{ $name }}:</span>
                            <span class="badge {{ $service['status'] === 'healthy' ? 'bg-success' : ($service['status'] === 'warning' ? 'bg-warning' : 'bg-danger') }}">
                                {{ $service['message'] }}
                            </span>
                        </div>
                        @if(isset($service['driver']) && $service['driver'] !== 'N/A')
                            <small class="text-muted ms-3 mb-2 d-block">
                                <strong>Driver:</strong> {{ $service['driver'] }}
                                @if(isset($service['scheduled_tasks']))
                                    | <strong>Tâches:</strong> {{ $service['scheduled_tasks'] }}
                                @endif
                                @if(isset($service['backup_count']))
                                    | <strong>Sauvegardes:</strong> {{ $service['backup_count'] }}
                                @endif
                            </small>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Gestion des blocages système -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-left-danger shadow h-100">
                    <div class="card-header bg-danger text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-lock me-2"></i>Contrôle des accès système
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Blocages actifs -->
                        <div class="mb-4">
                            <h6 class="text-danger mb-3">Blocages actifs</h6>
                            <div id="activeLocks" class="row">
                                <!-- Les blocages seront chargés via AJAX -->
                            </div>
                        </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let lockData = {
    modules: {},
    users: {}
};

function refreshHealth() {
    window.location.reload();
}

function clearCache() {
    if (confirm('Voulez-vous vraiment vider tous les caches de l\'application ?')) {
        fetch('{{ route("admin.clear-cache") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cache vidé avec succès !');
                refreshHealth();
            } else {
                alert('Erreur lors du nettoyage du cache');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du nettoyage du cache');
        });
    }
}

// Gestion des blocages système
function refreshLocks() {
    fetch('{{ route("admin.systeme.locks.index") }}')
        .then(response => response.json())
        .then(data => {
            lockData = data;
            displayLocks(data.locks);
            updateTargetOptions();
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement des blocages');
        });
}

function displayLocks(locks) {
    const container = document.getElementById('activeLocks');

    if (locks.length === 0) {
        container.innerHTML = '<div class="col-12"><p class="text-muted">Aucun blocage actif</p></div>';
        return;
    }

    container.innerHTML = locks.map(lock => `
        <div class="col-md-6 mb-3">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-danger">${lock.lock_type_label}</h6>
                            <p class="card-text">
                                <strong>Cible:</strong> ${lock.target_description}<br>
                                <strong>Raison:</strong> ${lock.reason}<br>
                                <small class="text-muted">
                                    Par ${lock.locker.name} le ${new Date(lock.locked_at).toLocaleString()}
                                </small>
                            </p>
                        </div>
                        <button class="btn btn-sm btn-success" onclick="unlockSpecific('${lock.lock_type}', '${lock.target || ''}')">
                            <i class="fas fa-unlock"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function updateTargetOptions() {
    const lockType = document.getElementById('lockType').value;
    const targetSelect = document.getElementById('lockTarget');

    targetSelect.innerHTML = '<option value="">Sélectionner...</option>';

    if (lockType === 'module_access') {
        Object.entries(lockData.modules).forEach(([code, module]) => {
            targetSelect.innerHTML += `<option value="${code}">${module.label}</option>`;
        });
        targetSelect.disabled = false;
    } else if (lockType === 'user_access') {
        lockData.users.forEach(user => {
            targetSelect.innerHTML += `<option value="${user.id}">${user.name} (${user.email})</option>`;
        });
        targetSelect.disabled = false;
    } else {
        targetSelect.disabled = true;
    }
}

function lockSystem() {
    const lockType = document.getElementById('lockType').value;
    const target = document.getElementById('lockTarget').value;
    const reason = document.getElementById('lockReason').value.trim();

    if (!reason) {
        alert('Veuillez spécifier une raison pour le blocage');
        return;
    }

    if ((lockType === 'module_access' || lockType === 'user_access') && !target) {
        alert('Veuillez sélectionner une cible pour ce type de blocage');
        return;
    }

    if (!confirm(`Êtes-vous sûr de vouloir bloquer cet accès ?\n\nType: ${lockType}\nCible: ${target || 'Système entier'}\nRaison: ${reason}`)) {
        return;
    }

    fetch('{{ route("admin.systeme.lock") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            lock_type: lockType,
            target: target || null,
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Accès bloqué avec succès !');
            document.getElementById('lockReason').value = '';
            refreshLocks();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du blocage');
    });
}

function unlockSpecific(lockType, target) {
    if (!confirm('Êtes-vous sûr de vouloir débloquer cet accès ?')) {
        return;
    }

    fetch('{{ route("admin.systeme.unlock") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            lock_type: lockType,
            target: target
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Accès débloqué avec succès !');
            refreshLocks();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du déblocage');
    });
}

function unlockAll() {
    if (!confirm('Êtes-vous sûr de vouloir débloquer TOUS les accès système ?')) {
        return;
    }

    // Débloquer tous les types de blocage
    const promises = [
        { lock_type: 'full_access', target: null },
        { lock_type: 'module_access', target: null },
        { lock_type: 'user_access', target: null }
    ].map(lock =>
        fetch('{{ route("admin.systeme.unlock") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(lock)
        })
    );

    Promise.all(promises)
        .then(responses => Promise.all(responses.map(r => r.json())))
        .then(results => {
            const successCount = results.filter(r => r.success).length;
            alert(`${successCount} blocage(s) débloqué(s) avec succès !`);
            refreshLocks();
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du déblocage massif');
        });
}

// Écouter les changements de type de blocage
document.getElementById('lockType').addEventListener('change', updateTargetOptions);

// Charger les blocages au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    refreshLocks();
});

// Auto-refresh toutes les 30 secondes
setInterval(() => {
    refreshLocks();
}, 30000);
</script>
@endpush

@endsection
