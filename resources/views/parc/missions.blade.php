@extends('layouts.app')

@section('title', 'Suivi des Projets de Location Véhicules | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-truck-monster me-2 text-primary"></i>Projets de Location Véhicules & Engins</h1>
            <p class="text-muted mb-0">Gestion des locations, conducteurs et rentabilité des projets</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('materiel.missions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Nouveau Projet de location
            </a>
            <a href="{{ route('materiel.missions.export', request()->all()) }}" class="btn btn-outline-success">
                <i class="fas fa-file-excel me-1"></i>Exporter
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form class="row g-3" method="GET">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Statut</label>
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="planned" @selected(request('status') == 'planned')>En attente</option>
                        <option value="ongoing" @selected(request('status') == 'ongoing')>En cours</option>
                        <option value="done" @selected(request('status') == 'done')>Terminée</option>
                        <option value="canceled" @selected(request('status') == 'canceled')>Annulée</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Véhicule</label>
                    <select name="vehicle_id" class="form-select">
                        <option value="">Tous les véhicules</option>
                        @if(!empty($vehicles))
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" @selected(request('vehicle_id') == $vehicle->id)>{{ $vehicle->immatriculation }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Du</label>
                    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Au</label>
                    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
                    <a href="{{ route('materiel.missions.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-undo"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="missionsTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Réf / Destination</th>
                            <th>Véhicule & Chauffeur</th>
                            <th>Dates & Durée</th>
                            <th>Client / Fournisseur</th>
                            <th>Rentabilité</th>
                            <th>Statut</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($missions as $m)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-primary">{{ $m->reference }}</div>
                                <div class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $m->destination }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-light rounded p-2 me-2 text-center" style="width: 40px;">
                                        <i class="fas fa-car text-secondary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $m->vehicle->immatriculation ?? '-' }}</div>
                                        <div class="small text-muted">{{ $m->driver->name ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <div><i class="fas fa-calendar-alt me-1 opacity-50"></i>{{ optional($m->start_at)->format('d/m/Y') }}</div>
                                    <div class="text-muted"><i class="fas fa-clock me-1 opacity-50"></i>{{ $m->duration_days }} jours</div>
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    @if($m->client)
                                        <div class="text-success fw-bold"><i class="fas fa-user-tie me-1"></i>{{ $m->client->raison_sociale }}</div>
                                    @endif
                                    @if($m->supplier)
                                        <div class="text-danger"><i class="fas fa-truck-loading me-1"></i>{{ $m->supplier->raison_sociale }}</div>
                                    @else
                                        <div class="text-muted"><i class="fas fa-warehouse me-1"></i>Gestion Interne</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold {{ $m->gross_margin >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($m->gross_margin, 0, ',', ' ') }} <small>FCFA</small>
                                </div>
                                <div class="progress mt-1" style="height: 4px;">
                                    @php
                                        $percent = $m->total_client_amount > 0 ? ($m->gross_margin / $m->total_client_amount) * 100 : 0;
                                    @endphp
                                    <div class="progress-bar bg-{{ $percent > 20 ? 'success' : ($percent > 0 ? 'warning' : 'danger') }}" style="width: {{ max(0, min(100, $percent)) }}%"></div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusClass = [
                                        'planned' => 'bg-info',
                                        'ongoing' => 'bg-primary',
                                        'done' => 'bg-success',
                                        'canceled' => 'bg-secondary'
                                    ][$m->status] ?? 'bg-dark';
                                    
                                    $statusLabel = [
                                        'planned' => 'En attente',
                                        'ongoing' => 'En cours',
                                        'done' => 'Terminée',
                                        'canceled' => 'Annulée'
                                    ][$m->status] ?? $m->status;
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('materiel.missions.show', $m) }}" class="btn btn-outline-light text-dark border"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('materiel.missions.edit', $m) }}" class="btn btn-outline-light text-primary border"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('materiel.missions.destroy', $m) }}" method="POST" onsubmit="return confirm('Supprimer cette mission ?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-light text-danger border"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-truck-monster fa-3x mb-3 opacity-25"></i>
                                <p>Aucun projet de location trouvé pour cette période.</p>
                                <a href="{{ route('materiel.missions.create') }}" class="btn btn-primary btn-sm">Créer un projet de location</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($missions->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $missions->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
