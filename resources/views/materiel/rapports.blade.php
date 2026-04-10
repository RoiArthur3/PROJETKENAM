@extends('layouts.app')

@section('title', 'Rapports Matériel - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Rapports du Matériel</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('materiel.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Matériel
            </a>
            <a href="{{ route('materiel.vehicules') }}" class="btn btn-outline-primary">
                <i class="fas fa-car me-2"></i>Véhicules
            </a>
            <a href="{{ route('materiel.maintenance.index') }}" class="btn btn-outline-warning">
                <i class="fas fa-wrench me-2"></i>Maintenance
            </a>
            <a href="{{ route('materiel.carburant.index') }}" class="btn btn-outline-info">
                <i class="fas fa-gas-pump me-2"></i>Carburant
            </a>
            <button class="btn btn-primary" onclick="generateReport()">
                <i class="fas fa-file-pdf me-2"></i>Générer un Rapport
            </button>
        </div>
    </div>

    <!-- KPIs Rapports -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Rapports</h6>
                            <h3 class="mb-0">{{ $rapports->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-primary text-white">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Mensuels</h6>
                            <h3 class="mb-0">{{ $rapports->where('type', 'Mensuel')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-info text-white">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Annuel</h6>
                            <h3 class="mb-0">{{ $rapports->where('type', 'Annuel')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-success text-white">
                                <i class="fas fa-calendar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Générés</h6>
                            <h3 class="mb-0">{{ $rapports->where('statut', 'généré')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-warning text-white">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Rapports -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Période</th>
                            <th>Date Génération</th>
                            <th>Statut</th>
                            <th>Fichier</th>
                            <th>Taille</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rapports as $rapport)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $rapport->titre }}</div>
                                <div class="text-muted small">ID: {{ $rapport->id }}</div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $rapport->type == 'Mensuel' ? 'info' : ($rapport->type == 'Annuel' ? 'success' : 'warning') }}">
                                    {{ $rapport->type }}
                                </span>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $rapport->periode }}">
                                    {{ $rapport->periode }}
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ $rapport->date_generation->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ $rapport->date_generation->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    {{ ucfirst($rapport->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    <div class="text-truncate" style="max-width: 150px;" title="{{ $rapport->fichier }}">
                                        {{ $rapport->fichier }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-primary">{{ $rapport->taille }}</div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir" onclick="viewReport('{{ $rapport->id }}')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success" title="Télécharger" onclick="downloadReport('{{ $rapport->id }}', '{{ $rapport->fichier }}')">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="Partager" onclick="shareReport('{{ $rapport->id }}')">
                                        <i class="fas fa-share"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" title="Supprimer" onclick="deleteReport('{{ $rapport->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucun rapport trouvé</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-file-pdf me-2"></i>Générer le premier rapport
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Actions Rapides -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Générer un Nouveau Rapport</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-car fa-3x text-primary mb-3"></i>
                                    <h6 class="card-title">Rapport Véhicules</h6>
                                    <p class="card-text">Rapport mensuel du parc</p>
                                    <button class="btn btn-primary btn-sm">Générer</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-wrench fa-3x text-warning mb-3"></i>
                                    <h6 class="card-title">Rapport Maintenance</h6>
                                    <p class="card-text">Rapport trimestriel des maintenances</p>
                                    <button class="btn btn-warning btn-sm">Générer</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-gas-pump fa-3x text-info mb-3"></i>
                                    <h6 class="card-title">Rapport Carburant</h6>
                                    <p class="card-text">Rapport mensuel du carburant</p>
                                    <button class="btn btn-info btn-sm">Générer</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                                    <h6 class="card-title">Rapport Annuel</h6>
                                    <p class="card-text">Rapport annuel complet</p>
                                    <button class="btn btn-success btn-sm">Générer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historique des Rapports -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Historique des Rapports</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Période</th>
                                    <th>Rapports Générés</th>
                                    <th>Taille Totale</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Janvier 2026</td>
                                    <td>1</td>
                                    <td>3.2 MB</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Voir</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Décembre 2025</td>
                                    <td>1</td>
                                    <td>1.9 MB</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Voir</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Octobre - Décembre 2025</td>
                                    <td>1</td>
                                    <td>2.8 MB</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Voir</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Année 2025</td>
                                    <td>1</td>
                                    <td>6.5 MB</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Voir</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques de Génération -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Statistiques de Génération</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-muted">Rapports par Type</h6>
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-info">{{ $rapports->where('type', 'Mensuel')->count() }}</h4>
                                            <p class="mb-0">Mensuels</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-success">{{ $rapports->where('type', 'Annuel')->count() }}</h4>
                                            <p class="mb-0">Annuels</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-warning">{{ $rapports->where('type', 'Trimestriel')->count() }}</h4>
                                            <p class="mb-0">Trimestriels</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Taille des Rapports</h6>
                            <div class="row text-center">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            @php
                                                $totalTaille = $rapports->sum(function($rapport) {
                                                    return (float) preg_replace('/[^0-9.]/', '', $rapport->taille);
                                                });
                                            @endphp
                                            <h4 class="text-primary">{{ number_format($totalTaille, 1, ',', ' ') }} MB</h4>
                                            <p class="mb-0">Total</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            @php
                                                $moyenneTaille = $rapports->avg(function($rapport) {
                                                    return (float) preg_replace('/[^0-9.]/', '', $rapport->taille);
                                                });
                                            @endphp
                                            <h4 class="text-info">{{ number_format($moyenneTaille, 1, ',', ' ') }} MB</h4>
                                            <p class="mb-0">Moyenne</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Fréquence de Génération</h6>
                            <div class="row text-center">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-success">{{ $rapports->where('date_generation', '>=', now()->subDays(30))->count() }}</h4>
                                            <p class="mb-0">30 derniers jours</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-warning">{{ $rapports->where('date_generation', '>=', now()->subDays(7))->count() }}</h4>
                                            <p class="mb-0">7 derniers jours</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>

<script>
function generateReport() {
    // Générer le rapport PDF
    const period = document.querySelector('[data-period]')?.getAttribute('data-period') || 'month';
    const format = 'pdf';
    
    // Afficher un message de chargement
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Génération en cours...';
    btn.disabled = true;
    
    // Simuler la génération du rapport
    setTimeout(() => {
        alert('Rapport matériel généré avec succès!');
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 2000);
}

function viewReport(reportId) {
    // Ouvrir le rapport en PDF
    window.open('#', '_blank');
}

function downloadReport(reportId, filename) {
    // Télécharger le rapport
    const link = document.createElement('a');
    link.href = '/rapports/' + reportId + '/download';
    link.download = filename || 'rapport.pdf';
    link.click();
}

function shareReport(reportId) {
    // Partager le rapport
    const shareText = 'Rapport disponible';
    if (navigator.share) {
        navigator.share({
            title: 'Rapport Matériel',
            text: shareText
        });
    } else {
        alert('Rapport ID: ' + reportId + '\n\nLien de partage : ' + window.location.origin + '/rapports/' + reportId);
    }
}

function deleteReport(reportId) {
    if (confirm('Confirmer la suppression de ce rapport ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/rapports/' + reportId + '/delete';
        form.innerHTML = '<input type="hidden" name="_method" value="DELETE">';
        form.innerHTML += '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
