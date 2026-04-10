@extends('layouts.authenticated')
@section('header-title', 'Pointage Engins - Cost Control')
@section('content')
<div class="container-fluid">
    <!-- Header avec Titre et Actions -->
    <div class="row mb-4">
        <div class="col d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">
                <i class="fas fa-stopwatch me-2 text-info"></i>Pointage des Engins - Cost Control
            </h1>
            <div class="btn-group">
                <a href="{{ route('materiel.cost-control.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Nouveau Pointage
                </a>
                <a href="{{ route('materiel.cost-control.financial-entries.create') }}" class="btn btn-outline-primary">
                    <i class="fas fa-wallet me-1"></i>Nouvelle Charge
                </a>
            </div>
        </div>
    </div>

    <!-- KPIs Principaux -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-uppercase font-weight-bold opacity-75">Pointages</h6>
                    <h2 class="mb-0">{{ $summary['total_pointages'] ?? 0 }}</h2>
                    <small class="opacity-75">Total enregistrés</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-uppercase font-weight-bold opacity-75">Heures Pointées</h6>
                    <h2 class="mb-0">{{ number_format($summary['total_hours'] ?? 0, 1, '.', ' ') }}</h2>
                    <small class="opacity-75">Temps de travail</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-uppercase font-weight-bold opacity-75">Coût Total</h6>
                    <h2 class="mb-0">{{ number_format($summary['total_cost'] ?? 0, 0, ',', ' ') }}</h2>
                    <small class="opacity-75">FCFA</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card {{ ($summary['total_margin'] ?? 0) >= 0 ? 'bg-info' : 'bg-danger' }} text-white border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-uppercase font-weight-bold opacity-75">Marge</h6>
                    <h2 class="mb-0">{{ number_format($summary['total_margin'] ?? 0, 0, ',', ' ') }}</h2>
                    <small class="opacity-75">FCFA</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres de Recherche -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-filter me-2"></i>Filtres et Recherche
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('materiel.cost-control.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Mission</label>
                                <select name="vehicle_mission_id" class="form-select">
                                    <option value="">Toutes les missions</option>
                                    @foreach($missions as $mission)
                                        <option value="{{ $mission->id }}" {{ request('vehicle_mission_id') == $mission->id ? 'selected' : '' }}>
                                            {{ $mission->vehicle->immatriculation }} - {{ $mission->client->nom ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Véhicule</label>
                                <select name="vehicle_id" class="form-select">
                                    <option value="">Tous les véhicules</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->immatriculation }} - {{ $vehicle->marque }} {{ $vehicle->modele }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date début</label>
                                <input type="date" name="date_from" class="form-control"
                                       value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date fin</label>
                                <input type="date" name="date_to" class="form-control"
                                       value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="btn-group w-100">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>Filtrer
                                    </button>
                                    <a href="{{ route('materiel.cost-control.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>Réinitialiser
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableaux des Pointages et Écritures Financières -->
    <div class="row">
        <!-- Tableau des Pointages -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-clock me-2"></i>Pointages des Engins
                    </h6>
                    <a href="{{ route('materiel.cost-control.list') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-list me-1"></i>Voir tout
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($pointages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="fas fa-calendar me-1"></i>Date</th>
                                        <th><i class="fas fa-truck me-1"></i>Engin</th>
                                        <th><i class="fas fa-user me-1"></i>Chauffeur</th>
                                        <th><i class="fas fa-clock me-1"></i>Quantité</th>
                                        <th><i class="fas fa-money-bill me-1"></i>Coût</th>
                                        <th><i class="fas fa-chart-line me-1"></i>Marge</th>
                                        <th><i class="fas fa-cog me-1"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pointages as $pointage)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $pointage->date_pointage->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $pointage->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $pointage->vehicle->immatriculation ?? '-' }}</div>
                                                <small class="text-muted">{{ $pointage->vehicle->marque ?? '' }}</small>
                                            </td>
                                            <td>
                                                <div>{{ $pointage->driver->name ?? $pointage->mission->driver->nom ?? '-' }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ number_format($pointage->quantity, 1) }} {{ $pointage->unit_type }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-warning">
                                                    {{ number_format($pointage->total_supplier_cost, 0, ',', ' ') }} FCFA
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold {{ ($pointage->total_client_amount - $pointage->total_supplier_cost) >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($pointage->total_client_amount - $pointage->total_supplier_cost, 0, ',', ' ') }} FCFA
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('materiel.cost-control.show', $pointage) }}"
                                                       class="btn btn-outline-secondary btn-sm" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('materiel.cost-control.edit', $pointage) }}"
                                                       class="btn btn-outline-primary btn-sm" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('materiel.cost-control.destroy', $pointage) }}"
                                                          onsubmit="return confirm('Supprimer ce pointage ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white">
                            {{ $pointages->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun pointage enregistré</h5>
                            <p class="text-muted mb-4">Commencez par enregistrer votre premier pointage d'engin</p>
                            <a href="{{ route('materiel.cost-control.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Nouveau Pointage
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tableau des Écritures Financières -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-success">
                        <i class="fas fa-wallet me-2"></i>Écritures Financières
                    </h6>
                    <a href="{{ route('materiel.cost-control.financial-entries.create') }}" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-plus me-1"></i>Nouvelle
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($financialEntries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="fas fa-calendar me-1"></i>Date</th>
                                        <th><i class="fas fa-tag me-1"></i>Catégorie</th>
                                        <th><i class="fas fa-file-invoice me-1"></i>Libellé</th>
                                        <th><i class="fas fa-money-bill-wave me-1"></i>Montant</th>
                                        <th><i class="fas fa-chart-line me-1"></i>Type</th>
                                        <th><i class="fas fa-cog me-1"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($financialEntries as $entry)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $entry->transaction_date->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $entry->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $entry->type === 'expense' ? 'danger' : 'success' }}">
                                                    {{ $entry->category }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>{{ $entry->label }}</div>
                                                @if($entry->external_reference)
                                                    <small class="text-muted">({{ $entry->external_reference }})</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-bold {{ $entry->type === 'expense' ? 'text-danger' : 'text-success' }}">
                                                    {{ number_format($entry->amount, 0, ',', ' ') }} FCFA
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $entry->type === 'expense' ? 'danger' : 'success' }}">
                                                    {{ $entry->type === 'expense' ? 'Charge' : 'Revenu' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('materiel.cost-control.financial-entries.show', $entry) }}"
                                                       class="btn btn-outline-secondary btn-sm" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('materiel.cost-control.financial-entries.edit', $entry) }}"
                                                       class="btn btn-outline-primary btn-sm" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('materiel.cost-control.financial-entries.destroy', $entry) }}"
                                                          onsubmit="return confirm('Supprimer cette écriture ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white">
                            {{ $financialEntries->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-wallet fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune écriture financière</h5>
                            <p class="text-muted mb-4">Enregistrez des charges ou revenus supplémentaires</p>
                            <a href="{{ route('materiel.cost-control.financial-entries.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Nouvelle Écriture
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Résumé des Missions -->
    @if($missionSummaries->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold text-info">
                        <i class="fas fa-tasks me-2"></i>Résumé des Missions en Cours
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($missionSummaries as $mission)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card border-left border-4 border-{{
                                    $mission->status === 'ongoing' ? 'success' :
                                    ($mission->status === 'planned' ? 'warning' : 'secondary') }} h-100">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold">
                                            {{ $mission->vehicle->immatriculation ?? 'N/A' }}
                                        </h6>
                                        <div class="mb-2">
                                            <small class="text-muted">Client:</small>
                                            <div>{{ $mission->client->nom ?? 'N/A' }}</div>
                                        </div>
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <small class="text-muted">Pointages</small>
                                                <div class="h5 mb-0">{{ $mission->pointages->count() }}</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Coût total</small>
                                                <div class="h5 mb-0 text-warning">
                                                    {{ number_format($mission->pointages->sum('total_supplier_cost'), 0, ',', ' ') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>Détails
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
