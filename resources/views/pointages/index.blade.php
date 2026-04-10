@extends('layouts.app')

@section('title', 'Pointage Personnel - Heures travaillées')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Pointage Personnel - Heures travaillées
                    </h1>
                    <p class="text-muted mb-0">Suivi des heures de travail des employés</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('rh.pointages-engins.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Nouveau pointage personnel
                    </a>
                    <a href="{{ route('rh.pointages-engins.dashboard') }}" class="btn btn-info">
                        <i class="fas fa-chart-line me-1"></i>
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('rh.pointages-engins.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Date début</label>
                            <input type="date" name="date_debut" class="form-control" value="{{ request('date_debut') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date fin</label>
                            <input type="date" name="date_fin" class="form-control" value="{{ request('date_fin') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Opération</label>
                            <select name="operation_id" class="form-select">
                                <option value="">Toutes les opérations</option>
                                @foreach($pointages->pluck('operation')->unique('id') as $operation)
                                    <option value="{{ $operation->id }}" {{ request('operation_id') == $operation->id ? 'selected' : '' }}>
                                        {{ $operation->nom_operation }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Chauffeur</label>
                            <select name="driver_id" class="form-select">
                                <option value="">Tous les chauffeurs</option>
                                @foreach($pointages->pluck('driver')->unique('id') as $driver)
                                    <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-filter me-1"></i>
                                    Filtrer
                                </button>
                                <a href="{{ route('rh.pointages-engins.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    Réinitialiser
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $pointages->count() }}</h4>
                            <p class="mb-0">Total pointages</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($pointages->sum(function($p) { return $p->heures_travaillees ?? 0; }), 1) }}</h4>
                            <p class="mb-0">Total heures</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-hourglass-half fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $pointages->where('statut', 'termine')->count() }}</h4>
                            <p class="mb-0">Pointages terminés</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $pointages->where('statut', 'en_cours')->count() }}</h4>
                            <p class="mb-0">En cours</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-spinner fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des pointages -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Liste des pointages
                    </h5>
                </div>
                <div class="card-body">
                    @if($pointages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Employé</th>
                                        <th>Opération</th>
                                        <th>Véhicule</th>
                                        <th>Heure début</th>
                                        <th>Heure fin</th>
                                        <th>Heures travaillées</th>
                                        <th>Km parcourus</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pointages as $pointage)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($pointage->date_pointage)->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $pointage->driver->name ?? 'N/A' }}</div>
                                                        <small class="text-muted">{{ $pointage->driver->email ?? '' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($pointage->operation)
                                                    <span class="badge bg-info">{{ $pointage->operation->nom_operation }}</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($pointage->vehicle)
                                                    <div>
                                                        <div class="fw-semibold">{{ $pointage->vehicle->immatriculation }}</div>
                                                        <small class="text-muted">{{ $pointage->vehicle->marque }} {{ $pointage->vehicle->modele }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-success">{{ $pointage->heure_debut }}</span>
                                            </td>
                                            <td>
                                                @if($pointage->heure_fin)
                                                    <span class="badge bg-danger">{{ $pointage->heure_fin }}</span>
                                                @else
                                                    <span class="badge bg-secondary">En cours</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-bold text-primary">
                                                    @php
                                                        $heures = 0;
                                                        if($pointage->heure_debut && $pointage->heure_fin) {
                                                            $debut = \Carbon\Carbon::parse($pointage->date_pointage . ' ' . $pointage->heure_debut);
                                                            $fin = \Carbon\Carbon::parse($pointage->date_pointage . ' ' . $pointage->heure_fin);
                                                            $heures = $debut->diffInHours($fin) + ($debut->diffInMinutes($fin) % 60) / 60;
                                                        }
                                                    @endphp
                                                    {{ number_format($heures, 1) }}h
                                                </div>
                                            </td>
                                            <td>
                                                @if($pointage->kilometrage_debut && $pointage->kilometrage_fin)
                                                    <div class="fw-bold text-info">
                                                        {{ number_format($pointage->kilometrage_fin - $pointage->kilometrage_debut, 1) }} km
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($pointage->statut)
                                                    @case('en_cours')
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-spinner me-1"></i>En cours
                                                        </span>
                                                        @break
                                                    @case('termine')
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>Terminé
                                                        </span>
                                                        @break
                                                    @case('valide')
                                                        <span class="badge bg-primary">
                                                            <i class="fas fa-check-circle me-1"></i>Validé
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-clock me-1"></i>{{ $pointage->statut ?? 'N/A' }}
                                                        </span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('rh.pointages-engins.show', $pointage->id) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('rh.pointages-engins.edit', $pointage->id) }}" class="btn btn-sm btn-outline-secondary" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($pointage->statut === 'en_cours')
                                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="terminerPointage({{ $pointage->id }})" title="Terminer">
                                                            <i class="fas fa-stop"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Affichage de {{ $pointages->firstItem() }} à {{ $pointages->lastItem() }} 
                                sur {{ $pointages->total() }} pointages
                            </div>
                            <div>
                                {{ $pointages->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun pointage trouvé</h5>
                            <p class="text-muted">Commencez par créer un nouveau pointage personnel.</p>
                            <a href="{{ route('rh.pointages-engins.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>
                                Nouveau pointage personnel
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function terminerPointage(id) {
    if(confirm('Êtes-vous sûr de vouloir terminer ce pointage ?')) {
        // Rediriger vers la page d'édition pour terminer
        window.location.href = '/pointages/' + id + '/edit';
    }
}

// Auto-refresh des pointages en cours toutes les 30 secondes
setInterval(function() {
    location.reload();
}, 30000);
</script>
@endsection

