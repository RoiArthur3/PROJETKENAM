@extends('layouts.app')

@section('title', 'Sauvegardes - Administration - KENAM Services')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-save text-warning mr-2"></i>
            Gestion des Sauvegardes
        </h1>
        <div class="d-flex">
            <button onclick="createBackup()" class="btn btn-success btn-sm mr-2">
                <i class="fas fa-plus mr-1"></i> Nouvelle sauvegarde
            </button>
            <button onclick="scheduleBackup()" class="btn btn-primary btn-sm">
                <i class="fas fa-clock mr-1"></i> Programmer
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <!-- Statistiques des sauvegardes -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Sauvegardes réussies
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">12</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Sauvegardes échouées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">1</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Espace utilisé
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">2.4 GB</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hdd fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Dernière sauvegarde
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <small>2h 30min</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des sauvegardes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-2"></i>
                Historique des Sauvegardes
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="backupsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Nom</th>
                            <th>Date</th>
                            <th>Taille</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Sauvegardes simulées -->
                        <tr>
                            <td>
                                <span class="badge badge-primary">
                                    <i class="fas fa-database mr-1"></i> Base de données
                                </span>
                            </td>
                            <td>backup_db_2024_01_15_14_30</td>
                            <td>15/01/2024 14:30</td>
                            <td>45.2 MB</td>
                            <td>
                                <span class="badge badge-success">
                                    <i class="fas fa-check mr-1"></i> Réussie
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary mr-1" onclick="downloadBackup('db_2024_01_15')">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger mr-1" onclick="deleteBackup('db_2024_01_15')">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" onclick="restoreBackup('db_2024_01_15')">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="badge badge-success">
                                    <i class="fas fa-folder mr-1"></i> Fichiers
                                </span>
                            </td>
                            <td>backup_files_2024_01_15_14_30</td>
                            <td>15/01/2024 14:30</td>
                            <td>1.8 GB</td>
                            <td>
                                <span class="badge badge-success">
                                    <i class="fas fa-check mr-1"></i> Réussie
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary mr-1" onclick="downloadBackup('files_2024_01_15')">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger mr-1" onclick="deleteBackup('files_2024_01_15')">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" onclick="restoreBackup('files_2024_01_15')">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="badge badge-warning">
                                    <i class="fas fa-database mr-1"></i> Complète
                                </span>
                            </td>
                            <td>backup_full_2024_01_14_23_00</td>
                            <td>14/01/2024 23:00</td>
                            <td>2.1 GB</td>
                            <td>
                                <span class="badge badge-danger">
                                    <i class="fas fa-times mr-1"></i> Échouée
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary mr-1" disabled>
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger mr-1" onclick="deleteBackup('full_2024_01_14')">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" disabled>
                                    <i class="fas fa-undo"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Configuration des sauvegardes -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs mr-2"></i>
                        Configuration des Sauvegardes
                    </h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <label for="backupType">Type de sauvegarde</label>
                            <select class="form-control" id="backupType">
                                <option value="database">Base de données uniquement</option>
                                <option value="files">Fichiers uniquement</option>
                                <option value="full">Sauvegarde complète</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="backupFrequency">Fréquence</label>
                            <select class="form-control" id="backupFrequency">
                                <option value="manual">Manuel uniquement</option>
                                <option value="daily">Quotidienne</option>
                                <option value="weekly">Hebdomadaire</option>
                                <option value="monthly">Mensuelle</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="retentionDays">Durée de rétention (jours)</label>
                            <input type="number" class="form-control" id="retentionDays" value="30" min="1" max="365">
                        </div>

                        <div class="form-group">
                            <label for="backupLocation">Emplacement de stockage</label>
                            <select class="form-control" id="backupLocation">
                                <option value="local">Serveur local</option>
                                <option value="cloud">Stockage cloud</option>
                                <option value="external">Disque externe</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Enregistrer la configuration
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sauvegarde programmée -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-clock mr-2"></i>
                        Sauvegardes Programmées
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Prochaine sauvegarde</h6>
                        <p class="text-muted mb-1">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            <strong>Demain à 02:00</strong>
                        </p>
                        <small class="text-muted">Sauvegarde complète automatique</small>
                    </div>

                    <hr>

                    <h6>Historique récent</h6>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <small class="text-muted">15/01/2024 14:30</small>
                                <p class="mb-0">Sauvegarde complète terminée avec succès</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <small class="text-muted">14/01/2024 14:30</small>
                                <p class="mb-0">Sauvegarde base de données terminée</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <small class="text-muted">14/01/2024 23:00</small>
                                <p class="mb-0">Échec de la sauvegarde complète</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <button onclick="pauseSchedule()" class="btn btn-warning btn-sm">
                            <i class="fas fa-pause mr-1"></i> Mettre en pause
                        </button>
                        <button onclick="resumeSchedule()" class="btn btn-success btn-sm">
                            <i class="fas fa-play mr-1"></i> Reprendre
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
}
</style>

<script>
// Fonctions de gestion des sauvegardes
function createBackup() {
    if (confirm('Créer une nouvelle sauvegarde ? Cette opération peut prendre du temps.')) {
        // Simulation de création de sauvegarde
        alert('Sauvegarde en cours... Cette fonctionnalité sera bientôt disponible.');
    }
}

function downloadBackup(backupId) {
    alert(`Téléchargement de la sauvegarde ${backupId}...`);
    // Implémentation du téléchargement
}

function deleteBackup(backupId) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer la sauvegarde ${backupId} ?`)) {
        alert('Sauvegarde supprimée.');
        // Implémentation de la suppression
    }
}

function restoreBackup(backupId) {
    if (confirm(`Êtes-vous sûr de vouloir restaurer la sauvegarde ${backupId} ? Cette action peut être destructive.`)) {
        alert('Restauration en cours...');
        // Implémentation de la restauration
    }
}

function scheduleBackup() {
    alert('Programmation des sauvegardes... Cette fonctionnalité sera bientôt disponible.');
}

function pauseSchedule() {
    alert('Sauvegardes mises en pause.');
}

function resumeSchedule() {
    alert('Sauvegardes reprises.');
}
</script>
@endsection
