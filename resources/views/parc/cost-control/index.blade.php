@extends('layouts.authenticated')
@section('header-title', 'Cost Control')
@section('content')
<div class="container-fluid">
    <!-- Header avec Titre et Actions -->
    <div class="row mb-4">
        <div class="col d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0"><i class="fas fa-stopwatch me-2 text-info"></i>Cost Control - Suivi des Missions</h1>
            <div class="btn-group">
                <a href="{{ route('materiel.cost-control.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Nouveau pointage
                </a>
                <a href="{{ route('materiel.cost-control.financial-entries.create') }}" class="btn btn-outline-primary">
                    <i class="fas fa-wallet me-1"></i>Nouvelle charge
                </a>
            </div>
        </div>
    </div>

    <!-- KPIs en Cartes -->
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
                    <h6 class="card-title text-uppercase font-weight-bold opacity-75">Heures <br /> Pointées</h6>
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
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Mission</label>
                    <select name="vehicle_mission_id" class="form-select">
                        <option value="">Toutes les missions</option>
                        @foreach($missions as $mission)
                            <option value="{{ $mission->id }}" @selected(request('vehicle_mission_id') == $mission->id)>
                                {{ $mission->reference }} - {{ $mission->destination }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Engin</label>
                    <select name="vehicle_id" class="form-select">
                        <option value="">Tous les engins</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" @selected(request('vehicle_id') == $vehicle->id)>
                                {{ $vehicle->immatriculation }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Du</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Au</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des Pointages -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="m-0 fw-bold text-primary">Pointages Engins</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Mission / Projet</th>
                            <th>Engin</th>
                            <th>Propriétaire</th>
                            <th>Temps</th>
                            <th>Coût</th>
                            <th>Marge</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pointages as $pointage)
                            <tr>
                                <td><strong>{{ $pointage->date_pointage?->format('d/m/Y') }}</strong></td>
                                <td>
                                    <div class="fw-bold">{{ $pointage->activity_label }}</div>
                                    <small class="text-muted">{{ $pointage->operation?->titre ?? '-' }}</small>
                                </td>
                                <td>{{ $pointage->vehicle?->immatriculation ?? '-' }}</td>
                                <td>
                                    @if($pointage->vehicle?->proprietaire === 'Kenam')
                                        <span class="badge bg-success">Kenam</span>
                                    @elseif($pointage->vehicle?->proprietaire)
                                        <span class="badge bg-info">{{ $pointage->vehicle->proprietaire }}</span>
                                    @else
                                        <span class="badge bg-secondary">Inconnu</span>
                                    @endif
                                </td>
                                <td>{{ number_format($pointage->quantity, 2, ',', ' ') }} {{ $pointage->unit_type === 'heure' ? 'h' : 'j' }}</td>
                                <td class="text-danger fw-bold">{{ number_format($pointage->total_supplier_cost, 0, ',', ' ') }}</td>
                                <td class="fw-bold {{ $pointage->gross_margin >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($pointage->gross_margin, 0, ',', ' ') }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('materiel.cost-control.show', $pointage) }}" class="btn btn-outline-secondary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('materiel.cost-control.edit', $pointage) }}" class="btn btn-outline-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('materiel.cost-control.destroy', $pointage) }}" style="display:inline;" onsubmit="return confirm('Supprimer ce pointage ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">Aucun pointage enregistré</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($pointages->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $pointages->links() }}
            </div>
        @endif
    </div>

    <!-- Tableau des Écritures Financières -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">Charges & Revenus Liés</h6>
            <a href="{{ route('materiel.cost-control.financial-entries.create') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-plus me-1"></i>Ajouter
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Mission</th>
                            <th>Nature</th>
                            <th>Source</th>
                            <th class="text-end">Montant</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($financialEntries as $entry)
                            <tr>
                                <td><strong>{{ $entry->transaction_date?->format('d/m/Y') }}</strong></td>
                                <td>
                                    <div class="fw-bold">{{ $entry->mission?->reference ?? ($entry->operation?->titre ?? 'Sans mission') }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $entry->type === 'expense' ? 'bg-danger' : 'bg-success' }}">
                                        {{ $entry->type === 'expense' ? 'Charge' : 'Revenu' }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $entry->source_label }}</small>
                                    @if($entry->external_reference)
                                        <div class="text-muted small">{{ $entry->external_reference }}</div>
                                    @endif
                                </td>
                                <td class="text-end fw-bold {{ $entry->type === 'expense' ? 'text-danger' : 'text-success' }}">
                                    {{ $entry->type === 'expense' ? '-' : '+' }}{{ number_format($entry->amount, 0, ',', ' ') }}
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('materiel.cost-control.financial-entries.show', $entry) }}" class="btn btn-outline-secondary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('materiel.cost-control.financial-entries.edit', $entry) }}" class="btn btn-outline-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('materiel.cost-control.financial-entries.destroy', $entry) }}" style="display:inline;" onsubmit="return confirm('Supprimer cette écriture ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">Aucune écriture financière</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($financialEntries->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $financialEntries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
