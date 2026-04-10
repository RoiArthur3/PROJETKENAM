@extends('layouts.app')

@section('title', 'Rapports Audit - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Rapports d'Audit</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('audit.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Audit
            </a>
            <a href="{{ route('audit.controles') }}" class="btn btn-outline-primary">
                <i class="fas fa-clipboard-check me-2"></i>Contrôles
            </a>
            <a href="{{ route('audit.alertes') }}" class="btn btn-outline-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>Alertes
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
                                <i class="fas fa-file-alt"></i>
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
                            <h6 class="text-muted mb-1">Rapports Mensuels</h6>
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
                            <h6 class="text-muted mb-1">Rapports Annuels</h6>
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
                            <h6 class="text-muted mb-1">Autres Types</h6>
                            <h3 class="mb-0">{{ $rapports->whereNotIn(['Mensuel', 'Annuel'])->count() }}</h3>
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
                            <th>Score Global</th>
                            <th>Auditeur</th>
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
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $rapport->score_global >= 90 ? 'success' : ($rapport->score_global >= 80 ? 'warning' : 'danger') }}"
                                                 style="width: {{ $rapport->score_global }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ $rapport->score_global }}%</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($rapport->auditeur, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $rapport->auditeur }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success" title="Télécharger">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="Partager">
                                        <i class="fas fa-share"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
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
                                    <i class="fas fa-clipboard-check fa-3x text-primary mb-3"></i>
                                    <h6 class="card-title">Rapport de Contrôle</h6>
                                    <p class="card-text">Générer un rapport de contrôle</p>
                                    <button class="btn btn-primary btn-sm">Générer</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar-alt fa-3x text-info mb-3"></i>
                                    <h6 class="card-title">Rapport Mensuel</h6>
                                    <p class="card-text">Générer le rapport mensuel</p>
                                    <button class="btn btn-info btn-sm">Générer</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar fa-3x text-success mb-3"></i>
                                    <h6 class="card-title">Rapport Annuel</h6>
                                    <p class="card-text">Générer le rapport annuel complet</p>
                                    <button class="btn btn-success btn-sm">Générer</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-pie fa-3x text-warning mb-3"></i>
                                    <h6 class="card-title">Rapport Trimestriel</h6>
                                    <p class="card-text">Générer le rapport trimestriel</p>
                                    <button class="btn btn-warning btn-sm">Générer</button>
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
                                    <th>Score Moyen</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Janvier 2026</td>
                                    <td>1</td>
                                    <td>4.2 MB</td>
                                    <td>85%</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Voir</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Décembre 2025</td>
                                    <td>1</td>
                                    <td>3.8 MB</td>
                                    <td>92%</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Voir</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Octobre - Décembre 2025</td>
                                    <td>1</td>
                                    <td>8.5 MB</td>
                                    <td>88%</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Voir</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Novembre 2025</td>
                                    <td>1</td>
                                    <td>2.9 MB</td>
                                    <td>78%</td>
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
                            <h6 class="text-muted">Score Global</h6>
                            <div class="row text-center">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-primary">{{ number_format($rapports->avg('score_global'), 1, ',', ' ') }}%</h4>
                                            <p class="mb-0">Moyenne</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-success">{{ $rapports->max('score_global') }}%</h4>
                                            <p class="mb-0">Maximum</p>
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
                                            <h4 class="text-primary">{{ number_format($rapports->sum('taille'), 1, ',', ' ') }} MB</h4>
                                            <p class="mb-0">Total</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-info">{{ number_format($rapports->avg('taille'), 1, ',', ' ') }} MB</h4>
                                            <p class="mb-0">Moyenne</p>
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
    // Générer le rapport PDF d'audit
    const period = document.querySelector('[data-period]')?.getAttribute('data-period') || 'month';
    const format = 'pdf';
    
    // Afficher un message de chargement
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Génération en cours...';
    btn.disabled = true;
    
    // Simuler la génération du rapport
    setTimeout(() => {
        alert('Rapport audit généré avec succès!');
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 2000);
}
</script>
@endsection
