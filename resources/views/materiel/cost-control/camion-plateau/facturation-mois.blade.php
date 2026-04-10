@extends('layouts.app')

@section('title', 'Facturation au Mois – Camion Plateau')

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
                        <div class="h5 mb-0 fw-bold">{{ number_format($totals['voyages'], 0, ',', ' ') }}</div>
                    </div>
                    <i class="fas fa-route fa-2x opacity-25 text-info"></i>
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
                        <div class="text-xs text-uppercase fw-bold text-success mb-1">Montant à Facturer</div>
                        <div class="h5 mb-0 fw-bold">{{ number_format($totals['client'], 0, ',', ' ') }}</div>
                    </div>
                    <i class="fas fa-file-invoice-dollar fa-2x opacity-25 text-success"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            @php $marginColor = $totals['margin'] >= 0 ? 'primary' : 'danger'; @endphp
            <div class="card border-start border-{{ $marginColor }} border-4 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between py-3">
                    <div>
                        <div class="text-xs text-uppercase fw-bold text-{{ $marginColor }} mb-1">Marge Brute</div>
                        <div class="h5 mb-0 fw-bold text-{{ $marginColor }}">{{ number_format($totals['margin'], 0, ',', ' ') }}</div>
                    </div>
                    <i class="fas fa-chart-line fa-2x opacity-25 text-{{ $marginColor }}"></i>
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
                    <label class="form-label form-label-sm mb-1">Mois</label>
                    <input type="month" name="mois" value="{{ request('mois') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
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
                <div class="col-md-3">
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
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="fas fa-search me-1"></i>Filtrer
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('materiel.cost-control.plateau.facturation.mois') }}"
                       class="btn btn-sm btn-outline-secondary w-100">
                        <i class="fas fa-times me-1"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Tableau facturation mensuelle --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold">
                <i class="fas fa-calendar-check me-2 text-warning"></i>Facturation Mensuelle
            </h6>
            <span class="badge bg-warning text-dark">{{ $grouped->count() }} ligne(s)</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mois</th>
                            <th>Engin</th>
                            <th class="text-center">Voyages effectués</th>
                            <th class="text-center">Seuil inclus</th>
                            <th class="text-end">Forfait (FCFA)</th>
                            <th class="text-end">Surcharge (FCFA)</th>
                            <th class="text-end text-success">Total client (FCFA)</th>
                            <th class="text-end text-danger">Coût fourn. (FCFA)</th>
                            <th class="text-end">Marge (FCFA)</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($grouped as $row)
                        @php
                            $mPct = $row['client_total'] > 0
                                ? round(($row['margin'] / $row['client_total']) * 100, 1)
                                : 0;
                            $mColor = $row['margin'] >= 0 ? 'text-primary' : 'text-danger';
                        @endphp
                        <tr>
                            <td>
                                <span class="fw-bold">{{ ucfirst($row['mois_label']) }}</span>
                            </td>
                            <td>
                                <span class="fw-bold">{{ optional($row['vehicle'])->immatriculation ?? '—' }}</span>
                                @if($row['vehicle'])
                                    <br><small class="text-muted">{{ trim(($row['vehicle']->marque ?? '') . ' ' . ($row['vehicle']->modele ?? '')) }}</small>
                                @endif
                            </td>
                            <td class="text-center fw-bold text-info">{{ number_format($row['trip_count'], 0, ',', ' ') }}</td>
                            <td class="text-center text-muted">{{ number_format($row['threshold'], 0, ',', ' ') }}</td>
                            <td class="text-end">{{ number_format($row['forfait'], 0, ',', ' ') }}</td>
                            <td class="text-end {{ $row['surcharge'] > 0 ? 'text-warning fw-bold' : 'text-muted' }}">
                                {{ number_format($row['surcharge'], 0, ',', ' ') }}
                            </td>
                            <td class="text-end text-success fw-bold">{{ number_format($row['client_total'], 0, ',', ' ') }}</td>
                            <td class="text-end text-danger fw-bold">{{ number_format($row['supp_total'], 0, ',', ' ') }}</td>
                            <td class="text-end {{ $mColor }} fw-bold">
                                {{ number_format($row['margin'], 0, ',', ' ') }}
                                <br><small class="opacity-75">{{ $mPct }}%</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block opacity-25"></i>
                                Aucune facturation mensuelle trouvée.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                    @if($grouped->isNotEmpty())
                    <tfoot class="table-dark">
                        <tr>
                            <td colspan="2" class="fw-bold">TOTAL</td>
                            <td class="text-center fw-bold">{{ number_format($totals['voyages'], 0, ',', ' ') }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-end fw-bold text-success">{{ number_format($totals['client'], 0, ',', ' ') }}</td>
                            <td class="text-end fw-bold text-danger">{{ number_format($totals['supp'], 0, ',', ' ') }}</td>
                            <td class="text-end fw-bold {{ $totals['margin'] >= 0 ? 'text-info' : 'text-danger' }}">
                                {{ number_format($totals['margin'], 0, ',', ' ') }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
