@extends('layouts.app')

@section('title', ($pageTitle ?? 'Pointages Cost Control') . ' | KENAM SERVICES')

@section('content')
@php
    $currentSubmodule = $submodule ?? '';
    $isPlateau = $currentSubmodule === 'camion_plateau';
    $isEngin   = $currentSubmodule === 'engin';
    $dateFrom  = request('date_from');
    $dateTo    = request('date_to');
    $searchVal = request('search');
    $missionId = request('mission_id');
@endphp
<div class="container-fluid cost-control-list">

    {{-- ── En-tête ──────────────────────────────────────────────────────── --}}
    <div class="row mb-3">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-stopwatch me-2 text-primary"></i>{{ $pageTitle ?? 'Liste des pointages - Cost Control' }}
            </h1>
            <p class="text-muted mb-0">
                @if($isPlateau)
                    Pointages dédiés aux camions plateau avec suivi des voyages et de la facturation.
                @elseif($isEngin)
                    Pointages standards des engins avec suivi des heures et des coûts.
                @else
                    Vue consolidée des pointages standard et camion plateau.
                @endif
            </p>
        </div>
        <div class="col-auto d-flex gap-2 align-items-center">
            <a href="{{ route('materiel.cost-control.home') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-home me-1"></i>Accueil
            </a>
            @if($isPlateau)
                <a href="{{ route('materiel.cost-control.plateau.chrono.dashboard') }}" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-stopwatch me-1"></i>Pointage Chrono
                </a>
                <a href="{{ route('materiel.cost-control.plateau.projets-termines') }}" class="btn btn-outline-dark btn-sm">
                    <i class="fas fa-flag-checkered me-1"></i>Projets terminés
                </a>
            @elseif($isEngin)
                <a href="{{ route('materiel.cost-control.engin.rapport') }}" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-file-alt me-1"></i>Rapport Cost Controle
                </a>
                <a href="{{ route('materiel.cost-control.engin.projets-termines') }}" class="btn btn-outline-dark btn-sm">
                    <i class="fas fa-flag-checkered me-1"></i>Projets terminés
                </a>
            @endif
        </div>
    </div>

    {{-- ── Onglets sous-module ────────────────────────────────────────── --}}
    <div class="mb-4">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('materiel.cost-control.home') }}"
               class="btn btn-outline-secondary">
                <i class="fas fa-home me-1"></i>Tous les modules
            </a>
            <a href="{{ route('materiel.cost-control.engin.list') }}"
               class="btn {{ $isEngin ? 'btn-info text-white' : 'btn-outline-info' }}">
                <i class="fas fa-cogs me-1"></i>Engin Standard
            </a>
            <a href="{{ route('materiel.cost-control.plateau.list') }}"
               class="btn {{ $isPlateau ? 'btn-warning' : 'btn-outline-warning' }}">
                <i class="fas fa-truck me-1"></i>Camion Plateau
            </a>
        </div>
    </div>

    {{-- ── KPI Cards ──────────────────────────────────────────────────── --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase">Total pointages</div>
                            <div class="h3 mb-0 text-primary">{{ number_format($listSummary['total_pointages'] ?? 0, 0, ',', ' ') }}</div>
                        </div>
                        <i class="fas fa-stopwatch fa-2x text-primary opacity-25"></i>
                    </div>
                    @if(($listSummary['total_trips'] ?? 0) > 0)
                        <div class="small text-muted mt-1">{{ number_format($listSummary['total_trips'], 0, ',', ' ') }} voyages</div>
                    @elseif(($listSummary['total_hours'] ?? 0) > 0)
                        <div class="small text-muted mt-1">{{ number_format($listSummary['total_hours'], 1, ',', ' ') }} h / {{ number_format($listSummary['total_days'] ?? 0, 1, ',', ' ') }} j</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-danger border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase">Coût fournisseur</div>
                            <div class="h3 mb-0 text-danger">{{ number_format($listSummary['total_supplier_cost'] ?? 0, 0, ',', ' ') }}</div>
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
                            <div class="text-muted small text-uppercase">Montant à facturer</div>
                            <div class="h3 mb-0 text-success">{{ number_format($listSummary['total_client_amount'] ?? 0, 0, ',', ' ') }}</div>
                        </div>
                        <i class="fas fa-file-invoice-dollar fa-2x text-success opacity-25"></i>
                    </div>
                    <div class="small text-muted mt-1">FCFA</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            @php $globalMargin = $listSummary['total_margin'] ?? 0; @endphp
            <div class="card border-start {{ $globalMargin >= 0 ? 'border-info' : 'border-warning' }} border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase">Marge dégagée</div>
                            <div class="h3 mb-0 {{ $globalMargin >= 0 ? 'text-primary' : 'text-danger' }}">{{ number_format($globalMargin, 0, ',', ' ') }}</div>
                        </div>
                        <i class="fas fa-chart-line fa-2x {{ $globalMargin >= 0 ? 'text-info' : 'text-danger' }} opacity-25"></i>
                    </div>
                    <div class="small text-muted mt-1">FCFA
                        @if(($listSummary['total_client_amount'] ?? 0) > 0)
                            &mdash; {{ number_format($globalMargin / $listSummary['total_client_amount'] * 100, 1) }}%
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Saisie en lot (multi-engins) ───────────────────────────────────── --}}
    {{-- ══════════════════════════════════════════════════════════════════════
         SAISIE EN LOT — Pointer plusieurs engins / matériels en même temps
    ════════════════════════════════════════════════════════════════════════ --}}
    <div class="card shadow mb-4 border-top border-primary border-3" id="batch-form-card">
        <div class="card-header bg-primary text-white py-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-clipboard-list fa-lg"></i>
                <div>
                    <div class="fw-bold">Saisie des pointages — Plusieurs engins en même temps</div>
                    <small class="opacity-75">Sélectionnez la mission pour chaque engin. Les champs s'adaptent automatiquement (Standard / Camion Plateau).</small>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <span class="badge bg-light text-primary" id="row-count-badge">0 ligne(s)</span>
                <button type="button" class="btn btn-sm btn-light" id="toggle-batch-form">
                    <i class="fas fa-chevron-up" id="toggle-icon"></i>
                </button>
            </div>
        </div>

        <div id="batch-body">
        @if($errors->has('batch'))
            <div class="alert alert-danger mb-0 rounded-0 py-2 px-4">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ $errors->first('batch') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning mb-0 rounded-0 py-2 px-4">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('warning') }}
            </div>
        @endif

        <form method="POST" action="{{ $isPlateau ? route('materiel.cost-control.plateau.pointages.store') : route('materiel.cost-control.engin.pointages.store') }}" id="batch-form">
            @csrf
            <input type="hidden" name="submodule" value="{{ $currentSubmodule }}">

            {{-- ── Barre de contrôle --}}
            <div class="px-4 py-3 bg-light border-bottom d-flex flex-wrap gap-2 align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0 fw-semibold small">Date commune :</label>
                    <input type="date" id="shared-date" value="{{ date('Y-m-d') }}"
                           class="form-control form-control-sm" style="width:150px;">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="apply-date">
                        <i class="fas fa-calendar-check me-1"></i>Appliquer à toutes
                    </button>
                </div>
                <div class="ms-auto d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-success" id="add-row">
                        <i class="fas fa-plus me-1"></i>Ajouter une ligne
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-1"></i>Enregistrer tout
                    </button>
                </div>
            </div>

            {{-- ── Tableau de saisie --}}
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0" id="batch-table" style="min-width:900px;">
                    <thead class="table-light">
                        <tr>
                            <th style="width:30px" class="text-center text-muted">#</th>
                            <th style="min-width:230px">Mission (Engin + Client)</th>
                            <th style="width:130px">Date <span class="text-danger">*</span></th>
                            <th style="width:165px">Fournisseur</th>
                            {{-- Colonne adaptative --}}
                            <th class="th-engin" style="width:90px">H. début</th>
                            <th class="th-engin" style="width:90px">H. fin</th>
                            <th class="th-plateau" style="width:150px">Trajet / Tâche</th>
                            <th class="th-plateau" style="width:105px">Départ</th>
                            <th class="th-plateau" style="width:105px">Arrivée</th>
                            <th class="th-plateau" style="width:65px">BL</th>
                            <th class="th-plateau" style="width:65px">Voyages</th>
                            <th class="th-plateau" style="width:90px">Facturation</th>
                            {{-- Tarifs communs --}}
                            <th style="width:110px" class="text-end">Fourn. (FCFA)</th>
                            <th style="width:110px" class="text-end">Client (FCFA)</th>
                            <th style="width:34px"></th>
                        </tr>
                    </thead>
                    <tbody id="batch-rows">
                        {{-- Lignes injectées par JS --}}
                    </tbody>
                </table>
            </div>

            {{-- Pied vide --}}
            <div id="batch-empty" class="text-center py-4 text-muted" style="display:none;">
                <i class="fas fa-truck fa-2x opacity-25 mb-2 d-block"></i>
                Cliquez sur <strong>Ajouter une ligne</strong> pour commencer le pointage.
            </div>

            {{-- Bouton de soumission bas --}}
            <div class="px-4 py-3 border-top bg-light d-flex justify-content-end gap-2">
                <span class="text-muted small align-self-center" id="row-count-footer">0 ligne(s) à enregistrer</span>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Enregistrer tout
                </button>
            </div>
        </form>
        </div>{{-- #batch-body --}}
    </div>
    {{-- ══ FIN SAISIE EN LOT ═══════════════════════════════════════════════ --}}

    {{-- Données missions pour JS (JSON encodé) --}}
    <script id="missions-data" type="application/json">
    [
        @foreach($missions as $m)
        {
            "id": {{ $m->id }},
            "label": {!! json_encode(($m->reference ?? 'Mission #'.$m->id) . ($m->vehicle ? ' – '.$m->vehicle->immatriculation : '') . ($m->client ? ' – '.($m->client->nom ?? $m->client->name ?? '') : '')) !!},
            "submodule": "{{ $m->pointage_submodule ?? 'engin' }}",
            "billing_mode": "{{ $m->billing_mode ?? 'standard' }}",
            "supplier": {{ (float)($m->daily_supplier_price ?? 0) }},
            "client_price": {{ (float)($m->daily_client_price ?? 0) }},
            "vehicle_id": {{ $m->vehicle_id ?? 'null' }},
            "vehicle_label": {!! json_encode(optional($m->vehicle)->immatriculation ?? '') !!},
            "monthly_threshold": {{ (int)($m->monthly_trip_threshold ?? 0) }},
            "monthly_flat_rate": {{ (float)($m->monthly_flat_rate ?? 0) }}
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ]
    </script>
    {{-- Données fournisseurs pour JS --}}
    <script id="suppliers-data" type="application/json">
    [
        { "id": "kenam", "label": "Kenam (interne)" },
        @foreach($suppliers as $s)
        { "id": {{ $s->id }}, "label": {!! json_encode($s->raison_sociale ?? $s->nom ?? $s->name ?? '') !!} }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ]
    </script>

    {{-- ── Filtres avancés ─────────────────────────────────────────────── --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Filtres</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ $isPlateau ? route('materiel.cost-control.plateau.list') : route('materiel.cost-control.engin.list') }}" class="row g-3 align-items-end">
                @if($currentSubmodule)
                    <input type="hidden" name="submodule" value="{{ $currentSubmodule }}">
                @endif
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Date début</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Date fin</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Mission / Projet</label>
                    <select name="mission_id" class="form-select form-select-sm">
                        <option value="">Toutes les missions</option>
                        @foreach($missions as $m)
                            <option value="{{ $m->id }}" {{ (string) $missionId === (string) $m->id ? 'selected' : '' }}>
                                {{ $m->reference ?? ('Mission #' . $m->id) }}
                                @if($m->vehicle) – {{ $m->vehicle->immatriculation }} @endif
                                @if($m->client) – {{ $m->client->nom ?? $m->client->name ?? '' }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Recherche texte</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Engin, trajet, BL…" value="{{ $searchVal }}">
                </div>
                <div class="col-md-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i>Filtrer</button>
                    <a href="{{ $isPlateau ? route('materiel.cost-control.plateau.list') : route('materiel.cost-control.engin.list') }}" class="btn btn-outline-secondary btn-sm">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Table des pointages ──────────────────────────────────────────── --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-table me-2"></i>
                    @if($isPlateau) Pointages Camion Plateau
                    @elseif($isEngin) Pointages Engin Standard
                    @else Tous les pointages
                    @endif
                    <span class="badge bg-primary ms-2">{{ $pointages->total() }}</span>
                    <span class="badge bg-info"><i class="fas fa-hand-pointer me-1"></i>Cliquable</span>
                </h6>
                <p class="text-muted small mb-0 mt-2"><i class="fas fa-lightbulb me-1"></i>Cliquez sur une ligne pour ajouter rapidement un nouveau pointage pour cette mission</p>
            </div>
            <input type="text" id="kw" class="form-control form-control-sm" style="width:200px" placeholder="🔍 Filtrer le tableau…">
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0" id="tablePointages">
                    <thead class="table-light">
                        <tr>
                            <th style="width:90px">Date</th>
                            @if($isPlateau)
                                <th>Engin</th>
                                <th>Fournisseur</th>
                                <th>Client</th>
                                <th>N° BL</th>
                                <th>Départ → Arrivée</th>
                                <th class="text-center">Nb Voyages</th>
                                <th>Facturation</th>
                                <th class="text-end">Coût fourn.</th>
                                <th class="text-end">Montant client</th>
                                <th class="text-center">Marge</th>
                                <th style="width:80px" class="text-center">Actions</th>
                            @else
                                <th>Type</th>
                                <th>Tâche / Activité</th>
                                <th>Engin</th>
                                <th>Mission / Client</th>
                                <th class="text-center">Qté / Unité</th>
                                <th>Facturation</th>
                                <th class="text-end">Coût fourn.</th>
                                <th class="text-end">Montant client</th>
                                <th class="text-center">Marge</th>
                                <th>Statut</th>
                                <th style="width:80px" class="text-center">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pointages as $pointage)
                            @php
                                $lineMargin    = (float) ($pointage->gross_margin ?? ((float)$pointage->total_client_amount - (float)$pointage->total_supplier_cost));
                                $lineClientAmt = (float) $pointage->total_client_amount;
                                $linePct       = $lineClientAmt > 0 ? round($lineMargin / $lineClientAmt * 100, 1) : 0;
                                $lineColor     = $lineMargin >= 0 ? 'success' : 'danger';
                            @endphp
                            <tr>
                                <td class="small fw-semibold">{{ optional($pointage->date_pointage)->format('d/m/Y') }}</td>
                                @if($isPlateau)
                                    {{-- Camion Plateau columns --}}
                                    <td>
                                        <div class="fw-semibold small">{{ $pointage->vehicle->immatriculation ?? '—' }}</div>
                                        <div class="small text-muted">{{ trim(($pointage->vehicle->marque ?? '') . ' ' . ($pointage->vehicle->modele ?? '')) }}</div>
                                    </td>
                                    <td class="small">{{ $pointage->vehicle?->fournisseur?->nom ?? 'Kenam' }}</td>
                                    <td class="small">{{ $pointage->mission?->client->nom ?? $pointage->mission?->client->name ?? '—' }}</td>
                                    <td class="small text-muted">{{ $pointage->delivery_note_number ?? '—' }}</td>
                                    <td class="small">
                                        @if($pointage->departure_location || $pointage->arrival_location)
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $pointage->departure_location ?? '?' }}
                                            <i class="fas fa-arrow-right mx-1 text-muted"></i>
                                            <i class="fas fa-map-marker-alt text-success me-1"></i>{{ $pointage->arrival_location ?? '?' }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ number_format($pointage->trip_count ?? 0, 0, ',', ' ') }}</span>
                                    </td>
                                    <td class="small">
                                        {{ $pointage->billing_mode === 'monthly' ? 'Au mois' : 'Au voyage' }}
                                        @if($pointage->billing_mode === 'monthly' && ($pointage->monthly_trip_threshold ?? 0) > 0)
                                            <div class="small text-muted">Seuil: {{ $pointage->monthly_trip_threshold }} voy.</div>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold text-danger small">{{ number_format($pointage->total_supplier_cost ?? 0, 0, ',', ' ') }}</td>
                                    <td class="text-end fw-semibold text-success small">{{ number_format($pointage->total_client_amount ?? 0, 0, ',', ' ') }}</td>
                                    <td class="text-center">
                                        <div class="fw-bold text-{{ $lineColor }} small">{{ number_format($lineMargin, 0, ',', ' ') }}</div>
                                        <span class="badge bg-{{ $lineColor }}">{{ $linePct }}%</span>
                                    </td>
                                @else
                                    {{-- Engin Standard columns --}}
                                    <td>
                                        <span class="badge {{ $pointage->submodule === 'camion_plateau' ? 'bg-warning text-dark' : 'bg-info' }}">
                                            <i class="fas {{ $pointage->submodule === 'camion_plateau' ? 'fa-truck' : 'fa-cogs' }} me-1"></i>
                                            {{ $pointage->submodule === 'camion_plateau' ? 'Plateau' : 'Standard' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold small">{{ $pointage->activity_label ?? $pointage->task_label ?? '—' }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold small">{{ $pointage->vehicle->immatriculation ?? '—' }}</div>
                                        <div class="small text-muted">{{ trim(($pointage->vehicle->marque ?? '') . ' ' . ($pointage->vehicle->modele ?? '')) }}</div>
                                    </td>
                                    <td class="small">
                                        {{ $pointage->mission->reference ?? '—' }}
                                        @if($pointage->mission?->client)
                                            <div class="small text-muted">{{ $pointage->mission->client->nom ?? $pointage->mission->client->name ?? '' }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center small">
                                        @if(($pointage->trip_count ?? 0) > 0)
                                            <span class="badge bg-primary">{{ number_format($pointage->trip_count, 0, ',', ' ') }} voy.</span>
                                        @else
                                            {{ number_format($pointage->quantity ?? 0, 2, ',', ' ') }} {{ $pointage->unit_type }}
                                        @endif
                                    </td>
                                    <td class="small">
                                        {{ $pointage->billing_mode === 'monthly' ? 'Au mois' : ($pointage->billing_mode === 'trip' ? 'Au voyage' : 'Standard') }}
                                    </td>
                                    <td class="text-end fw-semibold text-danger small">{{ number_format($pointage->total_supplier_cost ?? 0, 0, ',', ' ') }} FCFA</td>
                                    <td class="text-end fw-semibold text-success small">{{ number_format($pointage->total_client_amount ?? 0, 0, ',', ' ') }} FCFA</td>
                                    <td class="text-center">
                                        <div class="fw-bold text-{{ $lineColor }} small">{{ number_format($lineMargin, 0, ',', ' ') }} FCFA</div>
                                        <span class="badge bg-{{ $lineColor }}">{{ $linePct }}%</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ ($pointage->statut ?? 'brouillon') === 'validé' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($pointage->statut ?? 'brouillon') }}
                                        </span>
                                    </td>
                                @endif
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a class="btn btn-outline-secondary" title="Voir" href="{{ $isPlateau ? route('materiel.cost-control.plateau.pointages.show', $pointage) : route('materiel.cost-control.engin.pointages.show', $pointage) }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a class="btn btn-outline-primary" title="Modifier" href="{{ $isPlateau ? route('materiel.cost-control.plateau.pointages.edit', $pointage) : route('materiel.cost-control.engin.pointages.edit', $pointage) }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ $isPlateau ? route('materiel.cost-control.plateau.pointages.destroy', $pointage) : route('materiel.cost-control.engin.pointages.destroy', $pointage) }}" onsubmit="return confirm('Supprimer ce pointage ?');"
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Supprimer"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-5">
                                    <i class="fas fa-stopwatch fa-2x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-3">Aucun pointage trouvé.</p>
                                    <a href="{{ $isPlateau ? route('materiel.cost-control.plateau.pointages.create') : route('materiel.cost-control.engin.pointages.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i>Créer un pointage
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($pointages, 'links'))
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <div class="small text-muted">{{ $pointages->total() }} pointage(s)</div>
                {{ $pointages->links() }}
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
(function () {
// ── Données chargées depuis le serveur
const MISSIONS = JSON.parse(document.getElementById('missions-data')?.textContent || '[]');
const SUPPLIERS = JSON.parse(document.getElementById('suppliers-data')?.textContent || '[]');
console.log('MISSIONS:',MISSIONS);
console.log('SUPPLIERS:', SUPPLIERS);
const missionMap = {};
MISSIONS.forEach(m => { missionMap[m.id] = m; });

let rowIndex = 0;

// ── Filtrage de la liste existante
const kw = document.getElementById('kw');
const tblTbody = document.querySelector('#tablePointages tbody');
if (kw && tblTbody) {
    kw.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        tblTbody.querySelectorAll('tr').forEach(r => {
            r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}

// ── Toggle collapse du panneau de saisie
const batchBody  = document.getElementById('batch-body');
const toggleIcon = document.getElementById('toggle-icon');
document.getElementById('toggle-batch-form')?.addEventListener('click', function () {
    const hidden = batchBody.style.display === 'none';
    batchBody.style.display = hidden ? '' : 'none';
    toggleIcon.className = hidden ? 'fas fa-chevron-up' : 'fas fa-chevron-down';
});

// ── Compteur de lignes
function updateRowCount() {
    const n = document.getElementById('batch-rows').querySelectorAll('tr').length;
    const badge   = document.getElementById('row-count-badge');
    const footer  = document.getElementById('row-count-footer');
    const empty   = document.getElementById('batch-empty');
    const tbl     = document.getElementById('batch-table');
    if (badge)  badge.textContent  = n + ' ligne(s)';
    if (footer) footer.textContent = n + ' ligne(s) à enregistrer';
    if (empty)  empty.style.display  = n === 0 ? '' : 'none';
    if (tbl)    tbl.style.display    = n === 0 ? 'none' : '';
}

// ── Construction d'une ligne de saisie
function buildRow(idx) {
    const tr = document.createElement('tr');
    tr.dataset.idx = idx;

    // Colonnes "th-plateau" et "th-engin" headers donnent une indication visuelle
    // mais les <td> dans chaque ligne gèrent leur propre visibilité

    tr.innerHTML = `
    <td class="text-center text-muted small">${idx + 1}</td>
    <td>
        <select name="rows[${idx}][vehicle_mission_id]" class="form-select form-select-sm mission-sel">
            <option value="">— Choisir une mission —</option>
            ${MISSIONS.map(m => `<option value="${m.id}" data-sub="${m.submodule}" data-billing="${m.billing_mode}" data-sup="${m.supplier}" data-cli="${m.client_price}">${escHtml(m.label)}</option>`).join('')}
        </select>
        <input type="hidden" name="rows[${idx}][submodule]"     class="inp-submodule"     value="engin">
        <input type="hidden" name="rows[${idx}][billing_mode]"  class="inp-billing"       value="standard">
    </td>
    <td>
        <input type="date" name="rows[${idx}][date_pointage]" class="form-control form-control-sm row-date" value="${todayVal()}" required>
    </td>
    <td>
        <select name="rows[${idx}][fournisseur_id]" class="form-select form-select-sm" required>
            <option value="">— Fournisseur —</option>
            ${SUPPLIERS.map(s => `<option value="${s.id}">${escHtml(s.label)}</option>`).join('')}
        </select>
    </td>
    <td class="td-engin">
        <input type="time" name="rows[${idx}][heure_debut]" class="form-control form-control-sm" placeholder="07:00">
    </td>
    <td class="td-engin">
        <input type="time" name="rows[${idx}][heure_fin]" class="form-control form-control-sm" placeholder="17:00">
    </td>
    <td class="td-plateau">
        <input type="text" name="rows[${idx}][task_label]" class="form-control form-control-sm" placeholder="Trajet / tâche…">
    </td>
    <td class="td-plateau">
        <input type="text" name="rows[${idx}][departure_location]" class="form-control form-control-sm" placeholder="Départ">
    </td>
    <td class="td-plateau">
        <input type="text" name="rows[${idx}][arrival_location]" class="form-control form-control-sm" placeholder="Arrivée">
    </td>
    <td class="td-plateau">
        <input type="text" name="rows[${idx}][delivery_note_number]" class="form-control form-control-sm" placeholder="BL-001">
    </td>
    <td class="td-plateau">
        <input type="number" name="rows[${idx}][trip_count]" class="form-control form-control-sm" min="1" step="1" value="1">
    </td>
    <td class="td-plateau">
        <select name="rows[${idx}][billing_mode_plateau]" class="form-select form-select-sm billing-sel">
            <option value="trip">Voyage</option>
            <option value="monthly">Mensuel</option>
        </select>
    </td>
    <td>
        <input type="number" name="rows[${idx}][supplier_unit_cost]" class="form-control form-control-sm text-end inp-supplier" min="0" step="1" value="0" placeholder="0">
    </td>
    <td>
        <input type="number" name="rows[${idx}][client_unit_price]" class="form-control form-control-sm text-end inp-client" min="0" step="1" value="0" placeholder="0">
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger remove-row px-2 py-1">
            <i class="fas fa-times"></i>
        </button>
    </td>`;

    // Après injection : état initial = engin (cacher plateau)
    applySubmodule(tr, 'engin');

    // Changer de mission → adapter les champs
    tr.querySelector('.mission-sel').addEventListener('change', function () {
        const m = missionMap[this.value];
        if (!m) { applySubmodule(tr, 'engin'); return; }

        tr.querySelector('.inp-submodule').value = m.submodule;
        tr.querySelector('.inp-billing').value   = m.billing_mode;
        tr.querySelector('.inp-supplier').value  = m.supplier || 0;
        tr.querySelector('.inp-client').value    = m.client_price || 0;

        // billing_mode plateau
        const billingSel = tr.querySelector('.billing-sel');
        if (billingSel) {
            billingSel.value = (m.billing_mode === 'monthly') ? 'monthly' : 'trip';
        }

        applySubmodule(tr, m.submodule);

        // Badge de type sur la ligne
        const badge = tr.querySelector('.row-type-badge');
        if (badge) {
            badge.textContent  = m.submodule === 'camion_plateau' ? 'Plateau' : 'Standard';
            badge.className    = 'row-type-badge badge ' + (m.submodule === 'camion_plateau' ? 'bg-warning text-dark' : 'bg-info text-white');
        }
    });

    // Supprimer la ligne
    tr.querySelector('.remove-row').addEventListener('click', function () {
        tr.remove();
        updateRowCount();
    });

    // billing_mode plateau → sync hidden input billing_mode
    tr.querySelector('.billing-sel')?.addEventListener('change', function () {
        tr.querySelector('.inp-billing').value = this.value;
    });

    return tr;
}

function applySubmodule(tr, submodule) {
    const isPlateau = submodule === 'camion_plateau';
    tr.querySelectorAll('.td-engin').forEach(td => {
        td.style.display = isPlateau ? 'none' : '';
        td.querySelectorAll('input,select').forEach(el => el.disabled = isPlateau);
    });
    tr.querySelectorAll('.td-plateau').forEach(td => {
        td.style.display = isPlateau ? '' : 'none';
        td.querySelectorAll('input,select').forEach(el => el.disabled = !isPlateau);
    });
    // Sync hidden submodule field
    const sub = tr.querySelector('.inp-submodule');
    if (sub) sub.value = submodule;
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function todayVal() {
    return new Date().toISOString().slice(0,10);
}

// ── Appliquer date commune
document.getElementById('apply-date')?.addEventListener('click', function () {
    const d = document.getElementById('shared-date').value;
    if (!d) return;
    document.querySelectorAll('#batch-rows .row-date').forEach(inp => inp.value = d);
});

// ── Ajouter une ligne
function addRow() {
    const tbody = document.getElementById('batch-rows');
    if (!tbody) return;
    const tr = buildRow(rowIndex++);
    tbody.appendChild(tr);
    updateRowCount();
    // Focus sur le select mission de la nouvelle ligne
    tr.querySelector('.mission-sel')?.focus();
}
document.getElementById('add-row')?.addEventListener('click', addRow);

// ── Cache les headers du tableau pour les colonnes plateau/engin selon le mode actif
// quand le tableau est mixte (onglet Tous), les deux headers restent visibles
@if($isPlateau)
document.querySelectorAll('.th-engin').forEach(th => th.style.display = 'none');
@elseif($isEngin)
document.querySelectorAll('.th-plateau').forEach(th => th.style.display = 'none');
@endif

// ── Initialiser avec 2 lignes au chargement
addRow();
addRow();

// ── Rendre les lignes de la table cliquables pour un pointage rapide
const tableBody = document.querySelector('#tablePointages tbody');
if (tableBody) {
    tableBody.addEventListener('click', function(e) {
        const row = e.target.closest('tr');
        if (!row || e.target.closest('.btn, a, checkbox')) return; // Ignorer les clics sur Actions/liens
        
        // Récupérer la première cellule pour extraire l'ID (via les données DOM ou l'attribut data-)
        // Pour cette implémentation, on va scanner la ligne pour trouver l'ID du pointage ou de la mission
        const missionCell = row.querySelector('td:nth-child(4)'); // Mission/Client column
        if (!missionCell) return;
        
        // Chercher le texte de la mission dans le DOM
        const missionText = missionCell.textContent.trim();
        
        // Trouver la mission correspondante dans MISSIONS
        const matchedMission = MISSIONS.find(m => 
            m.label.toLowerCase().includes(missionText.toLowerCase()) ||
            missionText.toLowerCase().includes(m.label.toLowerCase())
        );
        
        if (matchedMission) {
            // Ajouter une nouvelle ligne de saisie
            const newRow = buildRow(rowIndex++);
            const tbody = document.getElementById('batch-rows');
            tbody.appendChild(newRow);
            updateRowCount();
            
            // Pré-remplir la mission
            const missionSelect = newRow.querySelector('.mission-sel');
            if (missionSelect) {
                missionSelect.value = matchedMission.id;
                // Déclencher le changement pour mettre à jour les champs cachés
                missionSelect.dispatchEvent(new Event('change'));
            }
            
            // Scroller vers le formulaire de saisie
            const batchSection = document.getElementById('batch-form');
            if (batchSection) {
                batchSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            
            // Focus sur la date
            setTimeout(() => {
                newRow.querySelector('.row-date')?.focus();
            }, 300);
            
            // Visual feedback: highlight the new row
            newRow.classList.add('table-light');
            newRow.style.backgroundColor = '#e8f4f8';
            setTimeout(() => {
                newRow.style.backgroundColor = '';
            }, 1500);
        }
    });
    
    // Visual feedback: cursor pointer on hover
    tableBody.querySelectorAll('tr').forEach(row => {
        if (!row.querySelector('.btn, a')) {
            row.style.cursor = 'pointer';
        }
    });
    
    // On peut aussi ajouter des tooltips visuels (optionnel)
    tableBody.addEventListener('mouseover', function(e) {
        const row = e.target.closest('tr');
        if (row && !e.target.closest('.btn, a')) {
            row.style.backgroundColor = '#f8f9fa';
        }
    });
    tableBody.addEventListener('mouseout', function(e) {
        const row = e.target.closest('tr');
        if (row && !e.target.closest('.btn, a')) {
            row.style.backgroundColor = '';
        }
    });
}

// Ajuster billing_mode hidden sur le select plateau change
// (déjà géré dans buildRow via .billing-sel listener)

})();
}); // FIN DOMContentLoaded
</script>
@endpush

<style>
.cost-control-list .border-4 { border-width: 4px !important; }
.cost-control-list .card-header h6 { letter-spacing: .2px; }
.cost-control-list .table th { font-size:.78rem; white-space:nowrap; }
.cost-control-list .table td { font-size:.82rem; }
</style>
@endsection