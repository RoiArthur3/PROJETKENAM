@extends('layouts.app')

@section('title', 'Projets terminés - Fiches de pointage | KENAM SERVICES')

@section('content')
<div class="container-fluid projets-termines">

    {{-- ── En-tête ──────────────────────────────────────────────────────── --}}
    <div class="row mb-3">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-flag-checkered me-2 text-dark"></i>Projets terminés
            </h1>
            <p class="text-muted mb-0">Missions clôturées — fiches de pointage et montants à facturer.</p>
        </div>
        <div class="col-auto d-flex gap-2 align-items-center">
            <a href="{{ route('materiel.cost-control.plateau.list') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour aux pointages
            </a>
        </div>
    </div>

    {{-- ── KPI ─────────────────────────────────────────────────────────── --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-dark border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase">Projets terminés</div>
                            <div class="h3 mb-0">{{ number_format($totals['missions'] ?? 0, 0, ',', ' ') }}</div>
                        </div>
                        <i class="fas fa-flag-checkered fa-2x text-dark opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-danger border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase">Coût total fournisseur</div>
                            <div class="h3 mb-0 text-danger">{{ number_format($totals['supplier'] ?? 0, 0, ',', ' ') }}</div>
                        </div>
                        <i class="fas fa-hand-holding-usd fa-2x text-danger opacity-25"></i>
                    </div>
                    <div class="small text-muted mt-1">FCFA</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase">Montant total à facturer</div>
                            <div class="h3 mb-0 text-success">{{ number_format($totals['client'] ?? 0, 0, ',', ' ') }}</div>
                        </div>
                        <i class="fas fa-file-invoice-dollar fa-2x text-success opacity-25"></i>
                    </div>
                    <div class="small text-muted mt-1">FCFA</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            @php $gm = $totals['margin'] ?? 0; @endphp
            <div class="card border-start {{ $gm >= 0 ? 'border-info' : 'border-warning' }} border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase">Marge globale</div>
                            <div class="h3 mb-0 {{ $gm >= 0 ? 'text-primary' : 'text-danger' }}">{{ number_format($gm, 0, ',', ' ') }}</div>
                        </div>
                        <i class="fas fa-chart-line fa-2x {{ $gm >= 0 ? 'text-info' : 'text-danger' }} opacity-25"></i>
                    </div>
                    <div class="small text-muted mt-1">FCFA
                        @if(($totals['client'] ?? 0) > 0)
                            &mdash; {{ number_format($gm / $totals['client'] * 100, 1) }}%
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filtres ─────────────────────────────────────────────────────── --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Filtres</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('materiel.cost-control.plateau.completed-projects') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Date fin (début)</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Date fin (fin)</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Type de module</label>
                    <select name="submodule" class="form-select form-select-sm">
                        <option value="">Tous</option>
                        <option value="engin" {{ $submodule === 'engin' ? 'selected' : '' }}>Engin Standard</option>
                        <option value="camion_plateau" {{ $submodule === 'camion_plateau' ? 'selected' : '' }}>Camion Plateau</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Recherche (réf., client, engin…)</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Réf. mission, immatriculation…" value="{{ $search }}">
                </div>
                <div class="col-md-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i>Filtrer</button>
                    <a href="{{ route('materiel.cost-control.plateau.completed-projects') }}" class="btn btn-outline-secondary btn-sm">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Onglets sous-module ─────────────────────────────────────────── --}}
    <div class="mb-4 d-flex gap-2">
        <a href="{{ route('materiel.cost-control.plateau.projets-termines', array_filter(['date_from'=>$dateFrom,'date_to'=>$dateTo,'search'=>$search])) }}"
           class="btn {{ !$submodule || $submodule === 'camion_plateau' ? 'btn-secondary' : 'btn-outline-secondary' }}">
            <i class="fas fa-truck me-1"></i>Camion Plateau
        </a>
        <a href="{{ route('materiel.cost-control.engin.projets-termines', array_filter(['date_from'=>$dateFrom,'date_to'=>$dateTo,'search'=>$search])) }}"
           class="btn {{ $submodule === 'engin' ? 'btn-info text-white' : 'btn-outline-info' }}">
            <i class="fas fa-cogs me-1"></i>Engin Standard
        </a>
    </div>

    {{-- ── Table missions ──────────────────────────────────────────────── --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-dark">
                <i class="fas fa-list me-2"></i>Missions clôturées
                <span class="badge bg-dark ms-2">{{ $missions->total() }}</span>
            </h6>
            <input type="text" id="kw" class="form-control form-control-sm" style="width:200px" placeholder="🔍 Filtrer le tableau…">
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0" id="tableProjects">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Type</th>
                            <th>Engin</th>
                            <th>Client</th>
                            <th>Destination</th>
                            <th class="text-center">Fin le</th>
                            <th class="text-center">Nb pointages</th>
                            <th class="text-end">Coût fourn.</th>
                            <th class="text-end">Montant client</th>
                            <th class="text-center">Marge</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($missions as $mission)
                            @php
                                $mc = $mission->ptg_margin_pct ?? 0;
                                $mcClass = ($mission->ptg_margin ?? 0) >= 0 ? 'success' : 'danger';
                                $profile = $mission->pointage_submodule ?? 'engin';
                            @endphp
                            <tr>
                                <td class="fw-semibold small">
                                    {{ $mission->reference ?? ('Mission #' . $mission->id) }}
                                    @if($mission->invoice_reference)
                                        <div class="small text-muted">Fact: {{ $mission->invoice_reference }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $profile === 'camion_plateau' ? 'bg-warning text-dark' : 'bg-info' }}">
                                        <i class="fas {{ $profile === 'camion_plateau' ? 'fa-truck' : 'fa-cogs' }} me-1"></i>
                                        {{ $profile === 'camion_plateau' ? 'Plateau' : 'Standard' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold small">{{ $mission->vehicle->immatriculation ?? '—' }}</div>
                                    <div class="small text-muted">{{ trim(($mission->vehicle->marque ?? '') . ' ' . ($mission->vehicle->modele ?? '')) }}</div>
                                </td>
                                <td class="small">{{ $mission->client->nom ?? $mission->client->name ?? '—' }}</td>
                                <td class="small">{{ $mission->destination ?? '—' }}</td>
                                <td class="text-center small">{{ optional($mission->end_at)->format('d/m/Y') ?? '—' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $mission->ptg_count ?? 0 }}</span>
                                </td>
                                <td class="text-end fw-semibold text-danger small">
                                    {{ number_format($mission->ptg_supplier_total ?? 0, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="text-end fw-semibold text-success small">
                                    {{ number_format($mission->ptg_client_total ?? 0, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="text-center">
                                    <div class="fw-bold text-{{ $mcClass }} small">{{ number_format($mission->ptg_margin ?? 0, 0, ',', ' ') }} FCFA</div>
                                    <span class="badge bg-{{ $mcClass }}">{{ $mc }}%</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('materiel.cost-control.plateau.fiche-pointage', $mission) }}"
                                           class="btn btn-outline-dark" title="Fiche de pointage" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="{{ route('materiel.cost-control.plateau.list', ['mission_id' => $mission->id]) }}"
                                           class="btn btn-outline-primary" title="Voir les pointages">
                                            <i class="fas fa-list"></i>
                                        </a>
                                        <a href="{{ route('materiel.missions.show', $mission) }}"
                                           class="btn btn-outline-secondary" title="Détail mission">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5">
                                    <i class="fas fa-flag-checkered fa-2x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">Aucun projet terminé trouvé.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($missions, 'links'))
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <div class="small text-muted">{{ $missions->total() }} projet(s) terminé(s)</div>
                {{ $missions->links() }}
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const kw = document.getElementById('kw');
    const tbody = document.querySelector('#tableProjects tbody');
    if (kw && tbody) {
        kw.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            tbody.querySelectorAll('tr').forEach(r => {
                r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush

<style>
.projets-termines .border-4 { border-width: 4px !important; }
.projets-termines .table th { font-size:.78rem; white-space:nowrap; }
.projets-termines .table td { font-size:.82rem; }
</style>
@endsection
