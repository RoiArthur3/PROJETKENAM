@extends('layouts.app')

@section('title', 'Rapports Magasin - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Rapports du Magasin</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('magasin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Magasin
            </a>
            <a href="{{ route('magasin.inventaire') }}" class="btn btn-outline-primary">
                <i class="fas fa-boxes me-2"></i>Inventaire
            </a>
            <a href="{{ route('magasin.entrees') }}" class="btn btn-outline-success">
                <i class="fas fa-arrow-down me-2"></i>Entrées
            </a>
            <a href="{{ route('magasin.sorties') }}" class="btn btn-outline-danger">
                <i class="fas fa-arrow-up me-2"></i>Sorties
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
                            <h6 class="text-muted mb-1">Rapports Trimestriels</h6>
                            <h3 class="mb-0">{{ $rapports->where('type', 'Trimestriel')->count() }}</h3>
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
                                    <div class="fw-bold">{{ $rapport->date->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ $rapport->date->format('H:i') }}</div>
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
                            <td colspan="7" class="text-center py-4">
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
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar-alt fa-3x text-info mb-3"></i>
                                    <h6 class="card-title">Rapport Mensuel</h6>
                                    <p class="card-text">Générer le rapport mensuel du stock</p>
                                    <button class="btn btn-info btn-sm">Générer</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar fa-3x text-success mb-3"></i>
                                    <h6 class="card-title">Rapport Annuel</h6>
                                    <p class="card-text">Générer le rapport annuel complet</p>
                                    <button class="btn btn-success btn-sm">Générer</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-pie fa-3x text-warning mb-3"></i>
                                    <h6 class="card-title">Rapport d'Inventaire</h6>
                                    <p class="card-text">Générer le rapport d'inventaire</p>
                                    <button class="btn btn-warning btn-sm">Générer</button>
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
    // Générer le rapport PDF du magasin
    const period = document.querySelector('[data-period]')?.getAttribute('data-period') || 'month';
    const format = 'pdf';
    
    // Afficher un message de chargement
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Génération en cours...';
    btn.disabled = true;
    
    // Simuler la génération du rapport
    setTimeout(() => {
        alert('Rapport magasin généré avec succès!');
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
            title: 'Rapport Magasin',
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
