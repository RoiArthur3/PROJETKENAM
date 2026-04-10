@extends('layouts.app')

@section('title', 'Parc Auto - Incidents')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-triangle-exclamation text-warning me-2"></i>
            Incidents Véhicules
        </h1>
        <a href="{{ route('parc.incidents.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-1"></i> Nouveau incident
        </a>
    </div>

    <!-- Messages de succès -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-filter me-2"></i>Filtres
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('parc.incidents.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="vehicle_id" class="form-label">Véhicule</label>
                    <select name="vehicle_id" id="vehicle_id" class="form-select">
                        <option value="">Tous les véhicules</option>
                        @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                            {{ $vehicle->immatriculation }} - {{ $vehicle->marque }} {{ $vehicle->modele }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="status" class="form-label">Statut</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Tous</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Ouvert</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En cours</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Fermé</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="type" class="form-label">Type</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">Tous</option>
                        @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="from" class="form-label">Du</label>
                    <input type="date" name="from" id="from" class="form-control" value="{{ request('from') }}">
                </div>

                <div class="col-md-2">
                    <label for="to" class="form-label">Au</label>
                    <input type="date" name="to" id="to" class="form-control" value="{{ request('to') }}">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des incidents -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-list me-2"></i>Liste des incidents ({{ $incidents->total() }})
            </h6>
        </div>
        <div class="card-body">
            @if($incidents->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="10%">Date</th>
                            <th width="15%">Véhicule</th>
                            <th width="12%">Type</th>
                            <th width="10%">Sévérité</th>
                            <th width="20%">Description</th>
                            <th width="10%">Rapporté par</th>
                            <th width="10%">Statut</th>
                            <th width="10%">Coût estimé</th>
                            <th width="13%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incidents as $incident)
                        <tr>
                            <td>{{ $incident->date_incident->format('d/m/Y') }}</td>
                            <td>
                                @if($incident->vehicle)
                                <strong>{{ $incident->vehicle->immatriculation }}</strong><br>
                                <small class="text-muted">{{ $incident->vehicle->marque }} {{ $incident->vehicle->modele }}</small>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($incident->type == 'Accident')
                                <span class="badge bg-danger"><i class="fas fa-car-crash me-1"></i>{{ $incident->type }}</span>
                                @elseif($incident->type == 'Panne')
                                <span class="badge bg-warning text-dark"><i class="fas fa-wrench me-1"></i>{{ $incident->type }}</span>
                                @elseif($incident->type == 'Vol')
                                <span class="badge bg-dark"><i class="fas fa-user-secret me-1"></i>{{ $incident->type }}</span>
                                @elseif($incident->type == 'Vandalisme')
                                <span class="badge bg-secondary"><i class="fas fa-hammer me-1"></i>{{ $incident->type }}</span>
                                @else
                                <span class="badge bg-info"><i class="fas fa-exclamation-circle me-1"></i>{{ $incident->type }}</span>
                                @endif
                            </td>
                            <td>
                                @if($incident->severity == 'Critique')
                                <span class="badge bg-danger">{{ $incident->severity }}</span>
                                @elseif($incident->severity == 'Élevée')
                                <span class="badge bg-warning text-dark">{{ $incident->severity }}</span>
                                @elseif($incident->severity == 'Moyenne')
                                <span class="badge bg-info">{{ $incident->severity }}</span>
                                @else
                                <span class="badge bg-secondary">{{ $incident->severity }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $incident->description }}">
                                    {{ $incident->description }}
                                </div>
                            </td>
                            <td>
                                @if($incident->reporter)
                                <small>{{ $incident->reporter->name }}</small>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($incident->status == 'open')
                                <span class="badge bg-danger"><i class="fas fa-folder-open me-1"></i>Ouvert</span>
                                @elseif($incident->status == 'in_progress')
                                <span class="badge bg-warning text-dark"><i class="fas fa-spinner me-1"></i>En cours</span>
                                @else
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Fermé</span>
                                @endif
                            </td>
                            <td>
                                @if($incident->estimated_cost)
                                {{ number_format($incident->estimated_cost, 0, ',', ' ') }} FCFA
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('parc.incidents.show', $incident) }}" 
                                       class="btn btn-info" 
                                       title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('parc.incidents.edit', $incident) }}" 
                                       class="btn btn-warning" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('parc.incidents.destroy', $incident) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet incident ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Supprimer">
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

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Affichage de {{ $incidents->firstItem() ?? 0 }} à {{ $incidents->lastItem() ?? 0 }} sur {{ $incidents->total() }} incidents
                </div>
                <div>
                    {{ $incidents->links() }}
                </div>
            </div>
            @else
            <div class="alert alert-info text-center mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Aucun incident trouvé. <a href="{{ route('parc.incidents.create') }}">Créer un nouvel incident</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
