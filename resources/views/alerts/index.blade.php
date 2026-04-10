<x-dashboard-layout title="Alertes Véhicules" icon="fa-solid fa-bell">
    <x-slot name="kpis">
        <x-kpi-card title="Alertes Actives" value="{{ DB::table('vehicle_alerts')->where('traitee', 0)->count() }}" icon="fa-solid fa-bell" color="warning" />
        <x-kpi-card title="Alertes Traitées" value="{{ DB::table('vehicle_alerts')->where('traitee', 1)->count() }}" icon="fa-solid fa-check-circle" color="success" />
        <x-kpi-card title="Alertes Assurance" value="{{ DB::table('vehicle_alerts')->where('type_alerte', 'assurance')->count() }}" icon="fa-solid fa-shield-alt" color="info" />
        <x-kpi-card title="Alertes Maintenance" value="{{ DB::table('vehicle_alerts')->where('type_alerte', 'maintenance')->count() }}" icon="fa-solid fa-wrench" color="secondary" />
    </x-slot>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fa-solid fa-bell me-2"></i>
                        Alertes Véhicules
                    </h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-outline-primary" onclick="rafraichirAlertes()">
                            <i class="fa-solid fa-sync me-1"></i>
                            Rafraîchir
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtres -->
                    <form method="GET" action="{{ route('alerts.index') }}" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="type_alerte" class="form-label">Type d'alerte</label>
                            <select name="type_alerte" id="type_alerte" class="form-select">
                                <option value="">Tous</option>
                                <option value="assurance" {{ request('type_alerte') == 'assurance' ? 'selected' : '' }}>Assurance</option>
                                <option value="maintenance" {{ request('type_alerte') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="kilometrage" {{ request('type_alerte') == 'kilometrage' ? 'selected' : '' }}>Kilométrage</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="niveau_alerte" class="form-label">Niveau</label>
                            <select name="niveau_alerte" id="niveau_alerte" class="form-select">
                                <option value="">Tous</option>
                                <option value="info" {{ request('niveau_alerte') == 'info' ? 'selected' : '' }}>Information</option>
                                <option value="warning" {{ request('niveau_alerte') == 'warning' ? 'selected' : '' }}>Avertissement</option>
                                <option value="critical" {{ request('niveau_alerte') == 'critical' ? 'selected' : '' }}>Critique</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_debut" class="form-label">Date début</label>
                            <input type="date" name="date_debut" id="date_debut" class="form-control"
                                   value="{{ request('date_debut') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-search me-1"></i>
                                Filtrer
                            </button>
                            <a href="{{ route('alerts.index') }}" class="btn btn-outline-secondary ms-2">
                                <i class="fa-solid fa-undo me-1"></i>
                                Réinitialiser
                            </a>
                        </div>
                    </form>

                    <!-- Tableau des alertes -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Véhicule</th>
                                    <th>Type</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Niveau</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $alertes = DB::table('active_vehicle_alerts')
                                        ->when(request('type_alerte'), function ($query, $type) {
                                            return $query->where('va.type_alerte', $type);
                                        })
                                        ->when(request('niveau_alerte'), function ($query, $niveau) {
                                            return $query->where('va.niveau_alerte', $niveau);
                                        })
                                        ->when(request('date_debut'), function ($query, $date) {
                                            return $query->where('va.date_alerte', '>=', $date);
                                        })
                                        ->orderBy('va.date_alerte', 'desc')
                                        ->orderBy('va.niveau_alerte', 'desc')
                                        ->get();
                                @endphp
                                @forelse($alertes as $alerte)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">{{ $alerte->immatriculation }}</span>
                                        </td>
                                        <td>
                                            @switch($alerte->type_alerte)
                                                @case('assurance')
                                                    <span class="badge bg-info">Assurance</span>
                                                @break
                                                @case('maintenance')
                                                    <span class="badge bg-warning">Maintenance</span>
                                                @break
                                                @case('kilometrage')
                                                    <span class="badge bg-secondary">Kilométrage</span>
                                                @break
                                                @default
                                                    <span class="badge bg-light">{{ $alerte->type_alerte }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $alerte->message }}</td>
                                        <td>{{ \Carbon\Carbon::parse($alerte->date_alerte)->format('d/m/Y') }}</td>
                                        <td>
                                            @switch($alerte->niveau_alerte)
                                                @case('info')
                                                    <span class="badge bg-info">Information</span>
                                                @break
                                                @case('warning')
                                                    <span class="badge bg-warning">Avertissement</span>
                                                @break
                                                @case('critical')
                                                    <span class="badge bg-danger">Critique</span>
                                                @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $alerte->niveau_alerte }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($alerte->traitee)
                                                @case(0)
                                                    <span class="badge bg-warning">En attente</span>
                                                @break
                                                @case(1)
                                                    <span class="badge bg-success">Traité</span>
                                                @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $alerte->traitee }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary btn-sm" onclick="marquerCommeTraitee({{ $alerte->id }})">
                                                    <i class="fa-solid fa-check"></i>
                                                    Marquer traité
                                                </button>
                                                <button class="btn btn-outline-info btn-sm" onclick="voirDetails({{ $alerte->id }})">
                                                    <i class="fa-solid fa-eye"></i>
                                                    Détails
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fa-solid fa-bell-slash fa-3x mb-3"></i>
                                            <h5 class="mb-0">Aucune alerte trouvée</h5>
                                            <p class="text-muted">Aucune alerte n\'a été générée pour le moment.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

@push('scripts')
<script>
function rafraichirAlertes() {
    location.reload();
}

function marquerCommeTraitee(id) {
    if (confirm('Marquer cette alerte comme traitée ?')) {
        fetch(`/alerts/${id}/marquer-traite`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => console.error('Erreur:', error));
    }
}

function voirDetails(id) {
    // Ouvrir une modal ou rediriger vers la page de détails
    console.log('Voir détails de l\'alerte:', id);
    // Implémenter la logique pour voir les détails
}
</script>
@endpush
