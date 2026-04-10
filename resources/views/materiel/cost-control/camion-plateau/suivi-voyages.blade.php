@extends('layouts.app')

@section('title', 'Suivi des Voyages – Camion Plateau')

@section('content')
<div class="container-fluid">

    @include('materiel.cost-control.camion-plateau._nav')

    {{-- ── KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-start border-info border-4 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between py-3">
                    <div>
                        <div class="text-xs text-uppercase fw-bold text-info mb-1">Total Voyages</div>
                        <div class="h5 mb-0 fw-bold">{{ number_format($totals['trips'], 0, ',', ' ') }}</div>
                    </div>
                    <i class="fas fa-map-marked-alt fa-2x opacity-25 text-info"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-start border-danger border-4 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between py-3">
                    <div>
                        <div class="text-xs text-uppercase fw-bold text-danger mb-1">Coût Fournisseur</div>
                        <div class="h5 mb-0 fw-bold">{{ number_format($totals['supp'], 0, ',', ' ') }}</div>
                    </div>
                    <i class="fas fa-truck fa-2x opacity-25 text-danger"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between py-3">
                    <div>
                        <div class="text-xs text-uppercase fw-bold text-success mb-1">Montant Client</div>
                        <div class="h5 mb-0 fw-bold">{{ number_format($totals['client'], 0, ',', ' ') }}</div>
                    </div>
                    <i class="fas fa-file-invoice-dollar fa-2x opacity-25 text-success"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            @php $mColor = $totals['margin'] >= 0 ? 'primary' : 'danger'; @endphp
            <div class="card border-start border-{{ $mColor }} border-4 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between py-3">
                    <div>
                        <div class="text-xs text-uppercase fw-bold text-{{ $mColor }} mb-1">Marge Brute</div>
                        <div class="h5 mb-0 fw-bold text-{{ $mColor }}">{{ number_format($totals['margin'], 0, ',', ' ') }}</div>
                    </div>
                    <i class="fas fa-chart-bar fa-2x opacity-25 text-{{ $mColor }}"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filtres --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-filter me-2"></i>Filtres</h6>
        </div>
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label form-label-sm mb-1">De</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label form-label-sm mb-1">À</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label form-label-sm mb-1">Engin</label>
                    <select name="vehicle_id" class="form-select form-select-sm">
                        <option value="">— Tous —</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->immatriculation }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label form-label-sm mb-1">Client</label>
                    <select name="client_id" class="form-select form-select-sm">
                        <option value="">— Tous —</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label form-label-sm mb-1">Mode</label>
                    <select name="billing_mode" class="form-select form-select-sm">
                        <option value="">— Tous —</option>
                        <option value="monthly" {{ request('billing_mode') === 'monthly' ? 'selected' : '' }}>Mensuel</option>
                        <option value="trip" {{ request('billing_mode') === 'trip' ? 'selected' : '' }}>Voyage</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('materiel.cost-control.plateau.suivi-voyages') }}"
                       class="btn btn-sm btn-outline-secondary w-100">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Tableau voyages --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold">
                <i class="fas fa-route me-2 text-warning"></i>Liste des Voyages
            </h6>
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="kw" class="form-control form-control-sm" placeholder="Rechercher…" style="width:180px;">
                <span class="badge bg-secondary">{{ $voyages->total() }} voyage(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Engin</th>
                            <th>Client</th>
                            <th>N° BL</th>
                            <th>Départ</th>
                            <th>Arrivée</th>
                            <th>Trajet / Tâche</th>
                            <th>Mode</th>
                            <th class="text-center">Voyages</th>
                            <th class="text-end text-danger">Coût fourn.</th>
                            <th class="text-end text-success">Montant client</th>
                            <th class="text-end">Marge</th>
                        </tr>
                    </thead>
                    <tbody id="tbl-body">
                    @forelse($voyages as $v)
                        @php
                            $lineMargin = (float)$v->total_client_amount - (float)$v->total_supplier_cost;
                            $lineColor  = $lineMargin >= 0 ? 'text-primary' : 'text-danger';
                            $linePct    = $v->total_client_amount > 0
                                ? round(($lineMargin / $v->total_client_amount) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td class="text-nowrap">{{ optional($v->date_pointage)->format('d/m/Y') ?? '—' }}</td>
                            <td>
                                <span class="fw-bold">{{ optional($v->vehicle)->immatriculation ?? '—' }}</span>
                                @if($v->vehicle && ($v->vehicle->marque || $v->vehicle->modele))
                                    <br><small class="text-muted">{{ trim(($v->vehicle->marque ?? '') . ' ' . ($v->vehicle->modele ?? '')) }}</small>
                                @endif
                            </td>
                            <td>{{ optional($v->mission?->client)->nom ?? '—' }}</td>
                            <td><small>{{ $v->delivery_note_number ?? '—' }}</small></td>
                            <td>{{ $v->departure_location ?? '—' }}</td>
                            <td>{{ $v->arrival_location ?? '—' }}</td>
                            <td><small>{{ $v->task_label ?? '—' }}</small></td>
                            <td>
                                @if($v->billing_mode === 'monthly')
                                    <span class="badge bg-warning text-dark">Mensuel</span>
                                @else
                                    <span class="badge bg-info text-dark">Voyage</span>
                                @endif
                            </td>
                            <td class="text-center fw-bold text-info">{{ number_format($v->trip_count ?? 0, 0, ',', ' ') }}</td>
                            <td class="text-end text-danger">{{ number_format($v->total_supplier_cost ?? 0, 0, ',', ' ') }}</td>
                            <td class="text-end text-success fw-bold">{{ number_format($v->total_client_amount ?? 0, 0, ',', ' ') }}</td>
                            <td class="text-end {{ $lineColor }} fw-bold">
                                {{ number_format($lineMargin, 0, ',', ' ') }}
                                <br><small class="opacity-75">{{ $linePct }}%</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block opacity-25"></i>
                                Aucun voyage enregistré.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($voyages->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $voyages->links() }}
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
