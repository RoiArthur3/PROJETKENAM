@extends('layouts.app')

@section('title', 'Maintenance - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Maintenance Véhicules</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('materiel.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Matériel
            </a>
            <a href="{{ route('materiel.vehicules') }}" class="btn btn-outline-primary">
                <i class="fas fa-car me-2"></i>Véhicules
            </a>
            <a href="{{ route('materiel.carburant.index') }}" class="btn btn-outline-info">
                <i class="fas fa-gas-pump me-2"></i>Carburant
            </a>
            <a href="{{ route('materiel.rapports') }}" class="btn btn-outline-success">
                <i class="fas fa-chart-bar me-2"></i>Rapports
            </a>
            <a href="{{ route('materiel.maintenance.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle Maintenance
            </a>
        </div>
    </div>

    <!-- KPIs Maintenance -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Maintenances</h6>
                            <h3 class="mb-0">{{ $maintenances->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-primary text-white">
                                <i class="fas fa-wrench"></i>
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
                            <h6 class="text-muted mb-1">Coût Total</h6>
                            <h3 class="mb-0">{{ number_format($maintenances->sum('cout'), 0, ',', ' ') }} FCFA</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-warning text-white">
                                <i class="fas fa-money-bill-wave"></i>
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
                            <h6 class="text-muted mb-1">Terminées</h6>
                            <h3 class="mb-0">{{ $maintenances->where('statut', 'terminée')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-success text-white">
                                <i class="fas fa-check-circle"></i>
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
                            <h6 class="text-muted mb-1">En Cours</h6>
                            <h3 class="mb-0">{{ $maintenances->where('statut', 'en_cours')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-info text-white">
                                <i class="fas fa-spinner"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Maintenances -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Véhicule</th>
                            <th>Type</th>
                            <th>Kilométrage</th>
                            <th>Technicien</th>
                            <th>Coût</th>
                            <th>Statut</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($maintenances as $maintenance)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-3">
                                        MAINT
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $maintenance->reference }}</div>
                                        <div class="text-muted small">ID: {{ $maintenance->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ optional($maintenance->date_maintenance)->format('d/m/Y') }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr(($maintenance->vehicule->immatriculation ?? 'VH'), 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">
                                            {{ $maintenance->vehicule->marque ?? '' }} {{ $maintenance->vehicule->modele ?? '' }}
                                            ({{ $maintenance->vehicule->immatriculation ?? '' }})
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ ucfirst($maintenance->type) }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ number_format($maintenance->kilometrage, 0, ',', ' ') }} km</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-success text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr(($maintenance->technicien_nom ?? ('Tech ' . ($maintenance->technicien_id ?? ''))), 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $maintenance->technicien_nom ?? ('Technicien #' . ($maintenance->technicien_id ?? '-')) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">{{ number_format($maintenance->cout, 0, ',', ' ') }} FCFA</div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $maintenance->statut == 'terminée' ? 'success' : 'info' }}">
                                    {{ ucfirst($maintenance->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $maintenance->description }}">
                                    {{ $maintenance->description }}
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-outline-primary" title="Voir" href="{{ route('materiel.maintenance.show', $maintenance) }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a class="btn btn-outline-secondary" title="Modifier" href="{{ route('materiel.maintenance.edit', $maintenance) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($maintenance->statut == 'en_cours')
                                        <button class="btn btn-outline-success" title="Terminer" data-bs-toggle="modal" data-bs-target="#resultModal-{{ $maintenance->id }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-outline-info" title="Pièces">
                                        <i class="fas fa-tools"></i>
                                    </button>
                                    <button class="btn btn-outline-dark" title="Imprimer">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>

                                @if($maintenance->statut == 'en_cours')
                                    <div class="modal fade" id="resultModal-{{ $maintenance->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Résultat de maintenance - {{ $maintenance->reference }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST" action="{{ route('materiel.maintenance.result.store', $maintenance) }}">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label">Résultat *</label>
                                                                <select name="resultat" class="form-select" required>
                                                                    <option value="reparee">Réparée</option>
                                                                    <option value="partielle">Réparation partielle</option>
                                                                    <option value="a_suivre">À suivre</option>
                                                                    <option value="non_reparee">Non réparée</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Coût réel (FCFA)</label>
                                                                <input type="number" step="0.01" name="cout" class="form-control" value="{{ $maintenance->cout }}">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Durée</label>
                                                                <input type="text" name="duree" class="form-control" value="{{ $maintenance->duree }}" placeholder="Ex: 2h30">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Pièces utilisées</label>
                                                                <input type="text" name="pieces_utilisees" class="form-control" value="{{ $maintenance->pieces_utilisees }}">
                                                            </div>
                                                            <div class="col-12">
                                                                <label class="form-label">Compte-rendu / Observations</label>
                                                                <textarea name="resultat_notes" class="form-control" rows="3">{{ $maintenance->resultat_notes }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-success">Enregistrer le résultat</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-wrench fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucune maintenance trouvée</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Planifier la première maintenance
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistiques par Type -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Statistiques par Type de Maintenance</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Maintenances par Type</h6>
                            <div class="row text-center">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-info">{{ $maintenances->where('type', 'Préventive')->count() }}</h4>
                                            <p class="mb-0">Préventives</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-warning">{{ $maintenances->where('type', 'Corrective')->count() }}</h4>
                                            <p class="mb-0">Correctives</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Maintenances par Technicien</h6>
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-primary">Mécanicien Auto Pro</h4>
                                            <p class="mb-0">1 maintenance</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-success">Service Ford</h4>
                                            <p class="mb-0">1 maintenance</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-warning">Garage Nissan</h4>
                                            <p class="mb-0">1 maintenance</p>
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

    <!-- Pièces Changées -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Dernières Pièces Changées</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Maintenance</th>
                                    <th>Pièces Changées</th>
                                    <th>Date</th>
                                    <th>Coût Estimé</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>MAINT-2026-001</td>
                                    <td>Huile moteur, filtre à huile, plaquettes de frein</td>
                                    <td>{{ now()->subDays(15)->format('d/m/Y') }}</td>
                                    <td>150 000 FCFA</td>
                                </tr>
                                <tr>
                                    <td>MAINT-2026-002</td>
                                    <td>Kit de réparation boîte de vitesse</td>
                                    <td>{{ now()->subDays(5)->format('d/m/Y') }}</td>
                                    <td>350 000 FCFA</td>
                                </tr>
                                <tr>
                                    <td>MAINT-2026-003</td>
                                    <td>4 pneus, liquide de refroidissement</td>
                                    <td>{{ now()->subDays(30)->format('d/m/Y') }}</td>
                                    <td>200 000 FCFA</td>
                                </tr>
                                <tr>
                                    <td>MAINT-2026-004</td>
                                    <td>Filtres, bougies, liquides</td>
                                    <td>{{ now()->subDays(20)->format('d/m/Y') }}</td>
                                    <td>180 000 FCFA</td>
                                </tr>
                            </tbody>
                        </table>
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
@endsection
