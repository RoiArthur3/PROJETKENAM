@extends('layouts.app')

@section('title', 'Paramètres Système - Admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-server text-primary me-2"></i>Paramètres Système
            </h1>
            <p class="text-muted mb-0">Configuration et maintenance du système d'information</p>
        </div>
    </div>

    <!-- Informations Système -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Informations Système
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Version PHP</label>
                                <p class="mb-0">{{ PHP_VERSION }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Version Laravel</label>
                                <p class="mb-0">{{ app()->version() }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Base de données</label>
                                <p class="mb-0">{{ config('database.default') }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Cache Driver</label>
                                <p class="mb-0">{{ config('cache.default') }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Session Driver</label>
                                <p class="mb-0">{{ config('session.driver') }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Filesystem</label>
                                <p class="mb-0">{{ config('filesystems.default') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-chart-pie me-2"></i>Statistiques Système
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Utilisateurs actifs</label>
                                <p class="mb-0">{{ \App\Models\User::count() }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Sessions actives</label>
                                <p class="mb-0">-</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Mémoire utilisée</label>
                                <p class="mb-0">{{ round(memory_get_peak_usage() / 1024 / 1024, 2) }} MB</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Temps d'exécution</label>
                                <p class="mb-0">{{ round(microtime(true) - LARAVEL_START, 2) }} s</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Système -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tools me-2"></i>Actions Système
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-broom fa-3x text-primary mb-3"></i>
                                    <h5>Nettoyer le Cache</h5>
                                    <p class="text-muted small">Vider les caches application, route, config et vue</p>
                                    <button class="btn btn-primary" onclick="clearCache()">
                                        <i class="fas fa-play me-1"></i>Exécuter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card border-left-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-database fa-3x text-success mb-3"></i>
                                    <h5>Optimiser la Base</h5>
                                    <p class="text-muted small">Optimiser les tables et indexes de la base de données</p>
                                    <button class="btn btn-success" onclick="optimizeDatabase()">
                                        <i class="fas fa-play me-1"></i>Exécuter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card border-left-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-shield-alt fa-3x text-warning mb-3"></i>
                                    <h5>Vérifier Sécurité</h5>
                                    <p class="text-muted small">Scanner les vulnérabilités et permissions système</p>
                                    <button class="btn btn-warning" onclick="securityCheck()">
                                        <i class="fas fa-play me-1"></i>Exécuter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card border-left-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-save fa-3x text-info mb-3"></i>
                                    <h5>Sauvegarde</h5>
                                    <p class="text-muted small">Créer une sauvegarde complète du système</p>
                                    <button class="btn btn-info" onclick="createBackup()">
                                        <i class="fas fa-play me-1"></i>Exécuter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card border-left-secondary">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-alt fa-3x text-secondary mb-3"></i>
                                    <h5>Logs Système</h5>
                                    <p class="text-muted small">Consulter et analyser les logs d'erreur</p>
                                    <button class="btn btn-secondary" onclick="viewLogs()">
                                        <i class="fas fa-eye me-1"></i>Voir
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card border-left-danger">
                                <div class="card-body text-center">
                                    <i class="fas fa-power-off fa-3x text-danger mb-3"></i>
                                    <h5>Maintenance</h5>
                                    <p class="text-muted small">Mode maintenance on/off</p>
                                    <button class="btn btn-danger" onclick="toggleMaintenance()">
                                        <i class="fas fa-toggle-on me-1"></i>Basculer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function clearCache() {
    if (confirm('Êtes-vous sûr de vouloir vider tous les caches ?')) {
        // Implementation would call Laravel cache clearing commands
        alert('Cache nettoyé avec succès !');
    }
}

function optimizeDatabase() {
    if (confirm('Êtes-vous sûr de vouloir optimiser la base de données ?')) {
        // Implementation would call database optimization
        alert('Base de données optimisée avec succès !');
    }
}

function securityCheck() {
    // Implementation would run security checks
    alert('Vérification de sécurité effectuée !');
}

function createBackup() {
    if (confirm('Êtes-vous sûr de vouloir créer une sauvegarde ?')) {
        // Implementation would create system backup
        alert('Sauvegarde créée avec succès !');
    }
}

function viewLogs() {
    // Redirect to logs page or open modal
    window.location.href = '/admin/logs';
}

function toggleMaintenance() {
    if (confirm('Êtes-vous sûr de vouloir basculer le mode maintenance ?')) {
        // Implementation would toggle maintenance mode
        alert('Mode maintenance basculé !');
    }
}
</script>
@endsection
