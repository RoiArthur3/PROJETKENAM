@extends('layouts.app')

@section('title', 'Dashboard Pointage Chrono | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    {{-- ── En-tête ────────────────────────────────────────────────────────── --}}
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-stopwatch me-2 text-primary"></i>Pointage Chronométrique
            </h1>
            <p class="text-muted mb-0">Clic Début / Clic Fin - Calcul automatique des heures</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('materiel.cost-control.plateau.chrono.start-form') }}" class="btn btn-lg btn-success">
                <i class="fas fa-play-circle me-2"></i>Démarrer un pointage
            </a>
        </div>
    </div>

    {{-- ── KPIs du jour ────────────────────────────────────────────────────── --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-2">Pointages du jour</h6>
                    <h3 class="text-primary">{{ $summary['today_pointages'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-2">Heures travaillées</h6>
                    <h3 class="text-info">{{ number_format($summary['today_hours'] ?? 0, 1, ',', '') }} h</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-2">Coût fournisseur</h6>
                    <h3 class="text-warning">{{ number_format($summary['today_cost'] ?? 0, 0, ',', ' ') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start {{ ($summary['today_margin'] ?? 0) >= 0 ? 'border-success' : 'border-danger' }} border-4 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-2">Marge</h6>
                    <h3 class="{{ ($summary['today_margin'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($summary['today_margin'] ?? 0, 0, ',', ' ') }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Pointages en cours ──────────────────────────────────────────────── --}}
    @if($activePointages->count() > 0)
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning bg-gradient py-3">
            <h5 class="mb-0"><i class="fas fa-hourglass-start me-2"></i>Pointages en cours ({{ $activePointages->count() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tâche</th>
                            <th>Véhicule</th>
                            <th>Chauffeur</th>
                            <th>Départ</th>
                            <th>Durée</th>
                            <th>Heures estimées</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activePointages as $pointage)
                        <tr class="align-middle">
                            <td>
                                <strong>{{ $pointage->task_label }}</strong><br>
                                <small class="text-muted">{{ $pointage->departure_location }}</small>
                            </td>
                            <td>{{ $pointage->vehicle?->immatriculation }}</td>
                            <td>{{ $pointage->driver?->nom }} {{ $pointage->driver?->prenoms }}</td>
                            <td>{{ \Carbon\Carbon::createFromTimeString($pointage->heure_depart)->format('H:i') }}</td>
                            <td>
                                <span class="badge bg-info" id="duration-{{ $pointage->id }}">Calculating...</span>
                            </td>
                            <td>
                                <span id="hours-{{ $pointage->id }}">0 h</span>
                            </td>
                            <td>
                                <form action="{{ route('materiel.cost-control.plateau.chrono.stop', $pointage) }}" method="POST" class="d-inline chrono-stop-form">
                                    @csrf
                                    <input type="hidden" name="arrival_location" value="">
                                    <button type="submit" class="btn btn-sm btn-success" title="Arrêter">
                                        <i class="fas fa-stop-circle"></i>
                                    </button>
                                </form>
                                <form action="{{ route('materiel.cost-control.plateau.chrono.cancel', $pointage) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Annuler">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Aucun pointage en cours</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Derniers pointages du jour ──────────────────────────────────────── --}}
    <div class="card shadow-sm">
        <div class="card-header bg-primary bg-gradient py-3">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Pointages du {{ $today }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tâche</th>
                            <th>Véhicule</th>
                            <th>Chauffeur</th>
                            <th>Heures</th>
                            <th>Coût Fournisseur</th>
                            <th>Montant Client</th>
                            <th>Marge</th>
                            <th>État</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayPointages as $pointage)
                        <tr class="align-middle">
                            <td>
                                <strong>{{ $pointage->task_label }}</strong><br>
                                <small class="text-muted">{{ $pointage->departure_location }}</small>
                            </td>
                            <td>{{ $pointage->vehicle?->immatriculation }}</td>
                            <td>{{ $pointage->driver?->nom }} {{ $pointage->driver?->prenoms }}</td>
                            <td>{{ number_format($pointage->quantity ?? 0, 2, ',', '') }} h</td>
                            <td>{{ number_format($pointage->total_supplier_cost ?? 0, 0, ',', ' ') }}</td>
                            <td>{{ number_format($pointage->total_client_amount ?? 0, 0, ',', ' ') }}</td>
                            <td class="{{ ($pointage->total_client_amount - $pointage->total_supplier_cost) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($pointage->total_client_amount - $pointage->total_supplier_cost, 0, ',', ' ') }}
                            </td>
                            <td>
                                @if($pointage->statut === 'en-cours')
                                    <span class="badge bg-warning">En cours</span>
                                @elseif($pointage->statut === 'termine')
                                    <span class="badge bg-success">Terminé</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Aucun pointage aujourd'hui</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Mise à jour en temps réel des durées
document.addEventListener('DOMContentLoaded', function() {
    const activeIds = {!! json_encode($activePointages->pluck('id')) !!};
    
    setInterval(function() {
        activeIds.forEach(id => {
            const depart = document.querySelector(`[data-pointage-start-${id}]`)?.dataset[`pointageStart${id}`];
            if (depart) {
                const start = new Date(depart);
                const now = new Date();
                const minutes = Math.floor((now - start) / 60000);
                const hours = minutes / 60;
                
                const durationEl = document.getElementById(`duration-${id}`);
                const hoursEl = document.getElementById(`hours-${id}`);
                
                if (durationEl) durationEl.textContent = minutes + ' min';
                if (hoursEl) hoursEl.textContent = hours.toFixed(2) + ' h';
            }
        });
    }, 5000); // Mise à jour toutes les 5 secondes
});
</script>
@endsection
