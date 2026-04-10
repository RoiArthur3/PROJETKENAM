@extends('layouts.app')

@section('title', 'Véhicules - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Parc Automobile</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('materiel.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Matériel
            </a>
            <a href="{{ route('materiel.maintenance.index') }}" class="btn btn-outline-warning">
                <i class="fas fa-wrench me-2"></i>Maintenance
            </a>
            <a href="{{ route('materiel.carburant.index') }}" class="btn btn-outline-info">
                <i class="fas fa-gas-pump me-2"></i>Carburant
            </a>
            <a href="{{ route('materiel.rapports') }}" class="btn btn-outline-success">
                <i class="fas fa-chart-bar me-2"></i>Rapports
            </a>
            <button class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Véhicule
            </button>
        </div>
    </div>

    <!-- KPIs Véhicules -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Véhicules</h6>
                            <h3 class="mb-0">{{ $vehicules->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-primary text-white">
                                <i class="fas fa-car"></i>
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
                            <h6 class="text-muted mb-1">Actifs</h6>
                            <h3 class="mb-0">{{ $vehicules->where('statut', 'actif')->count() }}</h3>
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
                            <h6 class="text-muted mb-1">En Maintenance</h6>
                            <h3 class="mb-0">{{ $vehicules->where('statut', 'maintenance')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-warning text-white">
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
                            <h6 class="text-muted mb-1">Kilométrage Total</h6>
                            <h3 class="mb-0">{{ number_format($vehicules->sum('kilometrage'), 0, ',', ' ') }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-info text-white">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Véhicules -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Immatriculation</th>
                            <th>Véhicule</th>
                            <th>Type</th>
                            <th>Année</th>
                            <th>Kilométrage</th>
                            <th>Chauffeur</th>
                            <th>Carburant</th>
                            <th>Prochaine Maintenance</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicules as $vehicule)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $vehicule->immatriculation }}</div>
                                <div class="text-muted small">ID: {{ $vehicule->id }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-3">
                                        {{ strtoupper(substr($vehicule->marque, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $vehicule->marque }} {{ $vehicule->modele }}</div>
                                        <div class="text-muted small">{{ $vehicule->type }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $vehicule->type }}</span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $vehicule->annee }}</div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ number_format($vehicule->kilometrage, 0, ',', ' ') }} km</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($vehicule->chauffeur, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $vehicule->chauffeur }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $vehicule->carburant_actuel / $vehicule->capacite_reservoir > 0.3 ? 'success' : ($vehicule->carburant_actuel / $vehicule->capacite_reservoir > 0.15 ? 'warning' : 'danger') }}"
                                                 style="width: {{ ($vehicule->carburant_actuel / $vehicule->capacite_reservoir) * 100 }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ $vehicule->carburant_actuel }}/{{ $vehicule->capacite_reservoir }}L</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-bold">{{ $vehicule->prochaine_maintenance->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ $vehicule->prochaine_maintenance->diffForHumans() }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $vehicule->statut == 'actif' ? 'success' : 'warning' }}">
                                    {{ ucfirst($vehicule->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" title="Maintenance">
                                        <i class="fas fa-wrench"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="Carburant">
                                        <i class="fas fa-gas-pump"></i>
                                    </button>
                                    <button class="btn btn-outline-success" title="Historique">
                                        <i class="fas fa-history"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-car fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucun véhicule trouvé</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Ajouter le premier véhicule
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
                    <h5 class="card-title mb-4">Répartition par Type de Véhicule</h5>
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-primary">Pickup</h6>
                                    <p class="mb-0">Toyota Hilux</p>
                                    <small>1 véhicule - 45 000 km</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-info">SUV</h6>
                                    <p class="mb-0">Nissan Patrol</p>
                                    <small>1 véhicule - 62 000 km</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-success">Utilitaire</h6>
                                    <p class="mb-0">Ford Transit</p>
                                    <small>1 véhicule - 28 000 km</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-warning">Fourgon</h6>
                                    <p class="mb-0">Mercedes Sprinter</p>
                                    <small>1 véhicule - 85 000 km</small>
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
@endsection
