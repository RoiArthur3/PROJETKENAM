@extends('layouts.app')

@section('title', 'Cost Control - Suivi des Missions - KENAM SERVICES')

@section('content')
@php
    $colors = ['#4e73df','#1cc88a','#f6c23e','#36b9cc','#e74a3b','#858796','#5a5c69','#fd7e14'];
@endphp
<x-dashboard-layout title="Cost Control - Suivi des Missions" icon="fa-stopwatch" subtitle="Vue d'ensemble et gestion des coûts de missions">
    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Pointages"
            :value="$summary['total_pointages'] ?? 0"
            icon="fa-stopwatch"
            color="primary"
            subtitle="Total enregistrés"
        />

        <x-kpi-card
            title="Heures Pointées"
            :value="number_format($summary['total_hours'] ?? 0, 1, '.', ' ')"
            icon="fa-clock"
            color="success"
            subtitle="Temps de travail"
        />

        <x-kpi-card
            title="Coût Total"
            :value="number_format($summary['total_cost'] ?? 0, 0, ',', ' ')"
            icon="fa-wallet"
            color="warning"
            subtitle="FCFA"
        />

        <x-kpi-card
            title="Marge"
            :value="number_format($summary['total_margin'] ?? 0, 0, ',', ' ')"
            icon="fa-chart-line"
            :color="($summary['total_margin'] ?? 0) >= 0 ? 'info' : 'danger'"
            subtitle="FCFA"
        />
    </x-slot>

    <!-- Actions -->
    <x-slot name="actions">
        <div class="btn-group">
            <a href="{{ route('materiel.cost-control.home') }}" class="btn btn-secondary">
                <i class="fas fa-home me-1"></i>Accueil
            </a>
            <a href="{{ route('materiel.cost-control.engin.list') }}" class="btn btn-outline-info">
                <i class="fas fa-cogs me-1"></i>Module Engin
            </a>
            <a href="{{ route('materiel.cost-control.plateau.list') }}" class="btn btn-outline-warning">
                <i class="fas fa-truck me-1"></i>Module Plateau
            </a>
            <a href="{{ route('materiel.cost-control.plateau.charges.create') }}" class="btn btn-outline-primary">
                <i class="fas fa-wallet me-1"></i>Nouvelle charge
            </a>
        </div>
    </x-slot>

    <!-- Content -->
    <x-slot name="content">
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card border-start border-info border-4 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="text-info mb-2"><i class="fas fa-cogs me-2"></i>Liste de pointage Standard</h5>
                        <p class="text-muted mb-3">Consultez et saisissez les pointages des engins standards (heures, coûts et marge).</p>
                        <div class="mt-auto">
                            <a href="{{ route('materiel.cost-control.list-standard') }}" class="btn btn-info text-white">
                                Ouvrir la liste Standard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card border-start border-warning border-4 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="text-warning mb-2"><i class="fas fa-truck me-2"></i>Liste des pointages Camions Plateau</h5>
                        <p class="text-muted mb-3">Suivez les trajets, voyages et la facturation mensuelle des camions plateau.</p>
                        <div class="mt-auto">
                            <a href="{{ route('materiel.cost-control.list-camion-plateau') }}" class="btn btn-warning">
                                Ouvrir la liste Camion Plateau
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

                        <!-- Filtres de Recherche -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('materiel.cost-control.home') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">Date début</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Date fin</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="vehicle_mission_id" class="form-label">Mission</label>
                            <select class="form-select" id="vehicle_mission_id" name="vehicle_mission_id">
                                <option value="">Toutes les missions</option>
                                @foreach($missions as $mission)
                                    <option value="{{ $mission->id }}" {{ request('vehicle_mission_id') == $mission->id ? 'selected' : '' }}>
                                        {{ $mission->reference ?? 'Mission #' . $mission->id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="vehicle_id" class="form-label">Engin</label>
                            <select class="form-select" id="vehicle_id" name="vehicle_id">
                                <option value="">Tous les engins</option>
                                @foreach(collect($vehicles ?? [])->filter() as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->immatriculation ?? $vehicle->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Filtrer
                            </button>
                            <a href="{{ route('materiel.cost-control.home') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Réinitialiser
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableaux des données -->
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Pointages récents</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Engin</th>
                                        <th>Heures</th>
                                        <th>Coût</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pointages->take(5) as $pointage)
                                        <tr>
                                            <td>{{ $pointage->date_pointage->format('d/m/Y') }}</td>
                                            <td>{{ $pointage->vehicle->immatriculation ?? 'N/A' }}</td>
                                            <td>{{ number_format($pointage->quantity ?? 0, 1) }}</td>
                                            <td>{{ number_format($pointage->total_supplier_cost ?? 0, 0, ',', ' ') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-warning">Charges récentes</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Catégorie</th>
                                        <th>Montant</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($chargesByCategory->take(5) as $charge)
                                        <tr>
                                            <td>{{ $charge->derniere_entree ? \Carbon\Carbon::parse($charge->derniere_entree)->format('d/m/Y') : 'N/A' }}</td>
                                            <td>{{ $charge->category }}</td>
                                            <td>{{ number_format($charge->total, 0, ',', ' ') }}</td>
                                            <td>{{ Str::limit($charge->description ?? '', 30) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques et statistiques -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-info">Répartition des coûts par catégorie</h6>
                    </div>
                    <div class="card-body">
                        @if($chargesByCategory->count() > 0)
                            <div class="row">
                                @foreach($chargesByCategory as $index => $charge)
                                    <div class="col-md-4 mb-3">
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ ($charge->total / $chargesByCategory->sum('total')) * 100 }}%; background-color: {{ $colors[$index % count($colors)] }};"
                                                 aria-valuenow="{{ $charge->total }}" 
                                                 aria-valuemin="0" aria-valuemax="{{ $chargesByCategory->sum('total') }}">
                                                {{ $charge->category }}: {{ number_format($charge->total, 0, ',', ' ') }} FCFA
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">Aucune donnée de coût disponible</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
</x-dashboard-layout>
@endsection
