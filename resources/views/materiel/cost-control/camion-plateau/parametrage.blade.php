@extends('layouts.app')

@section('title', 'Camion Plateau – Paramétrage')

@section('content')
<div class="container-fluid">

    {{-- ── Navigation --}}
    @include('materiel.cost-control.camion-plateau._nav')

    {{-- ── Flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between py-3">
                    <div>
                        <div class="text-xs text-uppercase fw-bold text-primary mb-1">Total Paramétrages</div>
                        <div class="h5 mb-0 fw-bold">{{ $params->total() }}</div>
                    </div>
                    <i class="fas fa-cog fa-2x text-gray-300 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between py-3">
                    <div>
                        <div class="text-xs text-uppercase fw-bold text-success mb-1">Actifs</div>
                        <div class="h5 mb-0 fw-bold">{{ $params->getCollection()->where('is_active', true)->count() }}</div>
                    </div>
                    <i class="fas fa-toggle-on fa-2x text-gray-300 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-start border-warning border-4 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between py-3">
                    <div>
                        <div class="text-xs text-uppercase fw-bold text-warning mb-1">Forfait Mensuel</div>
                        <div class="h5 mb-0 fw-bold">{{ $params->getCollection()->where('type_facturation','monthly')->count() }}</div>
                    </div>
                    <i class="fas fa-calendar-alt fa-2x text-gray-300 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-start border-info border-4 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between py-3">
                    <div>
                        <div class="text-xs text-uppercase fw-bold text-info mb-1">À la Tâche / Voyage</div>
                        <div class="h5 mb-0 fw-bold">{{ $params->getCollection()->where('type_facturation','trip')->count() }}</div>
                    </div>
                    <i class="fas fa-route fa-2x text-gray-300 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filtres + bouton créer --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-filter me-2"></i>Filtres</h6>
            <a href="{{ route('materiel.cost-control.plateau.parametrage.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>Nouveau paramétrage
            </a>
        </div>
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label form-label-sm mb-1">Engin</label>
                    <select name="vehicle_id" class="form-select form-select-sm">
                        <option value="">— Tous les engins —</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->immatriculation }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label form-label-sm mb-1">Client</label>
                    <select name="client_id" class="form-select form-select-sm">
                        <option value="">— Tous les clients —</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label form-label-sm mb-1">Type facturation</label>
                    <select name="type_facturation" class="form-select form-select-sm">
                        <option value="">— Tous —</option>
                        <option value="monthly" {{ request('type_facturation') === 'monthly' ? 'selected' : '' }}>Forfait Mensuel</option>
                        <option value="trip" {{ request('type_facturation') === 'trip' ? 'selected' : '' }}>À la Tâche</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="fas fa-search me-1"></i>Filtrer
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('materiel.cost-control.plateau.parametrage') }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="fas fa-times me-1"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Tableau --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-warning"></i>Règles de paramétrage</h6>
            <input type="text" id="kw" class="form-control form-control-sm w-auto" placeholder="Rechercher…" style="max-width:200px;">
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Engin</th>
                            <th>Client</th>
                            <th>Type Fact.</th>
                            <th class="text-end">Forfait Mois (FCFA)</th>
                            <th class="text-center">Seuil Voyages</th>
                            <th class="text-end">P.U. Voyage (FCFA)</th>
                            <th>Paiement Fourn.</th>
                            <th class="text-end">Coût Fourn. (FCFA)</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tbl-body">
                    @forelse($params as $p)
                        <tr>
                            <td>
                                <span class="fw-bold">{{ optional($p->vehicle)->immatriculation ?? '—' }}</span>
                                @if($p->vehicle)
                                    <br><small class="text-muted">{{ trim(($p->vehicle->marque ?? '') . ' ' . ($p->vehicle->modele ?? '')) }}</small>
                                @endif
                            </td>
                            <td>{{ optional($p->client)->nom ?? '—' }}</td>
                            <td>
                                @if($p->type_facturation === 'monthly')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-calendar me-1"></i>Forfait Mensuel</span>
                                @else
                                    <span class="badge bg-info text-dark"><i class="fas fa-route me-1"></i>À la Tâche</span>
                                @endif
                            </td>
                            <td class="text-end">{{ $p->type_facturation === 'monthly' ? number_format($p->monthly_flat_rate, 0, ',', ' ') : '—' }}</td>
                            <td class="text-center">
                                @if($p->type_facturation === 'monthly')
                                    {{ $p->monthly_trip_threshold }} voy.
                                @else —
                                @endif
                            </td>
                            <td class="text-end">
                                @if($p->type_facturation === 'monthly')
                                    {{ number_format($p->extra_trip_unit_price, 0, ',', ' ') }}
                                @else
                                    {{ number_format($p->trip_client_price, 0, ',', ' ') }}
                                @endif
                            </td>
                            <td>
                                @if($p->supplier_type_paiement === 'monthly')
                                    <span class="badge bg-secondary">Mensuel</span>
                                @else
                                    <span class="badge bg-secondary">Voyage</span>
                                @endif
                            </td>
                            <td class="text-end text-danger">
                                {{ $p->supplier_type_paiement === 'monthly'
                                    ? number_format($p->supplier_monthly_cost, 0, ',', ' ')
                                    : number_format($p->supplier_trip_cost, 0, ',', ' ') }}
                            </td>
                            <td class="text-center">
                                @if($p->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-danger">Inactif</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                <a href="{{ route('materiel.cost-control.plateau.parametrage.show', $p) }}"
                                   class="btn btn-outline-primary btn-sm me-1" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('materiel.cost-control.plateau.parametrage.edit', $p) }}"
                                   class="btn btn-outline-info btn-sm me-1" title="Modifier">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form method="POST"
                                      action="{{ route('materiel.cost-control.plateau.parametrage.destroy', $p) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Supprimer ce paramétrage ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block opacity-25"></i>
                                Aucun paramétrage enregistré.
                                <a href="{{ route('materiel.cost-control.plateau.parametrage.create') }}">Créer le premier</a>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($params->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $params->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
document.getElementById('kw').addEventListener('input', function () {
    const kw = this.value.toLowerCase();
    document.querySelectorAll('#tbl-body tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(kw) ? '' : 'none';
    });
});
</script>
@endpush
