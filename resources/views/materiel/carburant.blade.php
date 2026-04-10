@extends('layouts.app')

@section('title', 'Carburant - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Gestion Carburant</h1>
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
            <a href="{{ route('materiel.rapports') }}" class="btn btn-outline-success">
                <i class="fas fa-chart-bar me-2"></i>Rapports
            </a>
            <a href="{{ route('materiel.carburant.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Plein
            </a>
        </div>
    </div>

    <!-- KPIs Carburant -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Pleins</h6>
                            <h3 class="mb-0">{{ $carburants->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-primary text-white">
                                <i class="fas fa-gas-pump"></i>
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
                            <h3 class="mb-0">{{ number_format($carburants->sum('montant_total'), 0, ',', ' ') }} FCFA</h3>
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
                            <h6 class="text-muted mb-1">Quantité Totale</h6>
                            <h3 class="mb-0">{{ $carburants->sum('quantite') }} L</h3>
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
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Consommation Moyenne</h6>
                            <h3 class="mb-0">{{ number_format($carburants->avg('consommation'), 1, ',', ' ') }} L/100km</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-success text-white">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Carburants -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Véhicule</th>
                            <th>Type Carburant</th>
                            <th>Quantité</th>
                            <th>Prix Unitaire</th>
                            <th>Montant Total</th>
                            <th>Kilométrage</th>
                            <th>Consommation</th>
                            <th>Station</th>
                            <th>Chauffeur</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($carburants as $carburant)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-3">
                                        CARB
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $carburant->reference }}</div>
                                        <div class="text-muted small">ID: {{ $carburant->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ $carburant->date->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ $carburant->date->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($carburant->vehicule, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $carburant->vehicule }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $carburant->type_carburant }}</span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $carburant->quantite }} L</div>
                            </td>
                            <td>
                                <div class="text-primary">{{ number_format($carburant->prix_unitaire, 0, ',', ' ') }} FCFA</div>
                            </td>
                            <td>
                                <div class="fw-bold text-success">{{ number_format($carburant->montant_total, 0, ',', ' ') }} FCFA</div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ number_format($carburant->kilometrage_avant, 0, ',', ' ') }} → {{ number_format($carburant->kilometrage_apres, 0, ',', ' ') }}</div>
                                    <div class="text-muted small">+{{ $carburant->kilometrage_apres - $carburant->kilometrage_avant }} km</div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $carburant->consommation }} L/100km</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-warning text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($carburant->station, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $carburant->station }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-success text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($carburant->chauffeur, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $carburant->chauffeur }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="Ticket">
                                        <i class="fas fa-receipt"></i>
                                    </button>
                                    <button class="btn btn-outline-success" title="Consommation">
                                        <i class="fas fa-chart-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center py-4">
                                <i class="fas fa-gas-pump fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucun plein de carburant trouvé</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Enregistrer le premier plein
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistiques par Station -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Statistiques par Station</h5>
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-primary">Shell Zone 4</h6>
                                    <p class="mb-0">1 plein</p>
                                    <small>42 250 FCFA</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-info">Total Marcory</h6>
                                    <p class="mb-0">1 plein</p>
                                    <small>35 750 FCFA</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-success">Shell Plateau</h6>
                                    <p class="mb-0">1 plein</p>
                                    <small>45 500 FCFA</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-warning">Total Cocody</h6>
                                    <p class="mb-0">1 plein</p>
                                    <small>26 000 FCFA</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Consommation par Véhicule -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Consommation par Véhicule</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Véhicule</th>
                                    <th>Dernier Plein</th>
                                    <th>Quantité</th>
                                    <th>Consommation</th>
                                    <th>Coût Total</th>
                                    <th>Tendance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Toyota Hilux - CI-123-ABJ</td>
                                    <td>{{ now()->subDays(3)->format('d/m/Y') }}</td>
                                    <td>65 L</td>
                                    <td>8.5 L/100km</td>
                                    <td>42 250 FCFA</td>
                                    <td><span class="badge bg-success">Normal</span></td>
                                </tr>
                                <tr>
                                    <td>Nissan Patrol - CI-456-BKE</td>
                                    <td>{{ now()->subDays(2)->format('d/m/Y') }}</td>
                                    <td>55 L</td>
                                    <td>11.0 L/100km</td>
                                    <td>35 750 FCFA</td>
                                    <td><span class="badge bg-warning">Élevé</span></td>
                                </tr>
                                <tr>
                                    <td>Mercedes Sprinter - CI-012-YMK</td>
                                    <td>{{ now()->subDays(1)->format('d/m/Y') }}</td>
                                    <td>70 L</td>
                                    <td>14.0 L/100km</td>
                                    <td>45 500 FCFA</td>
                                    <td><span class="badge bg-danger">Très élevé</span></td>
                                </tr>
                                <tr>
                                    <td>Ford Transit - CI-789-SAN</td>
                                    <td>{{ now()->format('d/m/Y') }}</td>
                                    <td>40 L</td>
                                    <td>8.0 L/100km</td>
                                    <td>26 000 FCFA</td>
                                    <td><span class="badge bg-success">Normal</span></td>
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
