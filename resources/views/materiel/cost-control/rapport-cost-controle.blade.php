@extends('layouts.app')

@section('title', 'Rapport Cost Control - Engin | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    @php
        $exportParams = [
            'vehicle_id' => $filters['vehicle_id'] ?? null,
            'mission_id' => $filters['mission_id'] ?? null,
            'date_from' => $filters['date_from'] ?? null,
            'date_to' => $filters['date_to'] ?? null,
        ];
    @endphp
    <div class="row mb-3">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-alt me-2 text-info"></i>Rapport Cost Controle - Engin
            </h1>
            <p class="text-muted mb-0">
                Rapport ligne par ligne des pointages par mission, avec periode de mission et montants.
            </p>
        </div>
        <div class="col-auto d-flex gap-2 align-items-center">
            <a href="{{ route('materiel.cost-control.engin.list') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Retour liste
            </a>
            <a href="{{ route('materiel.cost-control.engin.rapport', array_merge($exportParams, ['export' => 'csv'])) }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-file-csv me-1"></i>Exporter Excel (CSV)
            </a>
            <a href="{{ route('materiel.cost-control.engin.rapport', array_merge($exportParams, ['export' => 'xlsx'])) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel me-1"></i>Exporter Excel (XLSX)
            </a>
            <a href="{{ route('materiel.cost-control.engin.rapport', array_merge($exportParams, ['export' => 'pdf'])) }}" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-file-pdf me-1"></i>Exporter PDF
            </a>
            <button type="button" class="btn btn-outline-dark btn-sm" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Imprimer
            </button>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h6 class="mb-0 text-primary"><i class="fas fa-filter me-2"></i>Filtres du rapport</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('materiel.cost-control.engin.rapport') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Engin</label>
                    <select name="vehicle_id" class="form-select">
                        <option value="">Tous les engins</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" @selected((string)($filters['vehicle_id'] ?? '') === (string)$v->id)>
                                {{ $v->immatriculation ?? $v->name ?? ('Engin #' . $v->id) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Mission</label>
                    <select name="mission_id" class="form-select">
                        <option value="">Toutes les missions</option>
                        @foreach($missions as $m)
                            <option value="{{ $m->id }}" @selected((string)($filters['mission_id'] ?? '') === (string)$m->id)>
                                {{ $m->reference ?? ('Mission #' . $m->id) }}
                                @if($m->vehicle)
                                    - {{ $m->vehicle->immatriculation ?? '' }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date de debut</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date de fin</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-info text-white w-100">
                        <i class="fas fa-search me-1"></i>Generer
                    </button>
                    <a href="{{ route('materiel.cost-control.engin.rapport') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Lignes pointage</div>
                    <div class="h4 mb-0 text-primary">{{ number_format($totals['lines'] ?? 0, 0, ',', ' ') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-secondary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Quantite totale</div>
                    <div class="h4 mb-0">{{ number_format($totals['quantity'] ?? 0, 2, ',', ' ') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-danger border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Cout fournisseur</div>
                    <div class="h4 mb-0 text-danger">{{ number_format($totals['supplier'] ?? 0, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Montant client</div>
                    <div class="h4 mb-0 text-success">{{ number_format($totals['client'] ?? 0, 0, ',', ' ') }} FCFA</div>
                    <div class="small mt-1 {{ ($totals['margin'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                        Marge: {{ number_format($totals['margin'] ?? 0, 0, ',', ' ') }} FCFA
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h6 class="mb-0 text-primary"><i class="fas fa-list me-2"></i>Detail ligne par ligne</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date pointage</th>
                        <th>Engin</th>
                        <th>Mission</th>
                        <th>Periode mission</th>
                        <th class="text-end">Quantite</th>
                        <th class="text-end">Cout fournisseur</th>
                        <th class="text-end">Montant client</th>
                        <th class="text-end">Marge</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pointages as $p)
                        @php
                            $mission = $p->mission;
                            $missionStart = optional($mission?->start_at)->format('d/m/Y H:i');
                            $missionEnd = optional($mission?->end_at)->format('d/m/Y H:i');
                            $margin = (float)($p->total_client_amount ?? 0) - (float)($p->total_supplier_cost ?? 0);
                        @endphp
                        <tr>
                            <td>{{ optional($p->date_pointage)->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ optional($p->vehicle)->immatriculation ?? optional($p->vehicle)->name ?? 'Engin inconnu' }}</td>
                            <td>
                                {{ $mission->reference ?? ('Mission #' . ($p->vehicle_mission_id ?? '-')) }}
                                @if(optional($mission->client)->nom || optional($mission->client)->name)
                                    <div class="small text-muted">{{ $mission->client->nom ?? $mission->client->name }}</div>
                                @endif
                            </td>
                            <td>
                                @if($missionStart || $missionEnd)
                                    <span>{{ $missionStart ?? '-' }}</span>
                                    <span class="text-muted">-></span>
                                    <span>{{ $missionEnd ?? '-' }}</span>
                                @else
                                    <span class="text-muted">Non renseignee</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format((float)($p->quantity ?? 0), 2, ',', ' ') }}</td>
                            <td class="text-end text-danger">{{ number_format((float)($p->total_supplier_cost ?? 0), 0, ',', ' ') }} FCFA</td>
                            <td class="text-end text-success">{{ number_format((float)($p->total_client_amount ?? 0), 0, ',', ' ') }} FCFA</td>
                            <td class="text-end {{ $margin >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($margin, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Aucun pointage trouve pour les filtres selectionnes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pointages instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <div class="small text-muted">{{ $pointages->total() }} ligne(s)</div>
                {{ $pointages->links() }}
            </div>
        @endif
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h6 class="mb-0 text-primary"><i class="fas fa-chart-bar me-2"></i>Synthese par engin</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Engin</th>
                        <th class="text-end">Lignes</th>
                        <th class="text-end">Quantite</th>
                        <th class="text-end">Cout fournisseur</th>
                        <th class="text-end">Montant client</th>
                        <th class="text-end">Marge</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($byVehicle as $row)
                        <tr>
                            <td>{{ $row['vehicle_label'] }}</td>
                            <td class="text-end">{{ number_format($row['lines'], 0, ',', ' ') }}</td>
                            <td class="text-end">{{ number_format($row['quantity'], 2, ',', ' ') }}</td>
                            <td class="text-end text-danger">{{ number_format($row['supplier'], 0, ',', ' ') }} FCFA</td>
                            <td class="text-end text-success">{{ number_format($row['client'], 0, ',', ' ') }} FCFA</td>
                            <td class="text-end {{ $row['margin'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($row['margin'], 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">Aucune donnee a afficher.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
