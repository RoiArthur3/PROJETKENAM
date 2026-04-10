@extends('layouts.app')

@section('title', 'Reporting Financier - Trésorerie - KENAM SERVICES')

@section('content')
<div class="content-wrapper">

    {{-- Navigation module --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-wallet me-2 text-success"></i>Reporting Financier — Trésorerie</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('reporting.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
            </a>
            <a href="{{ route('reporting.financier') }}" class="btn btn-success btn-sm active">
                <i class="fas fa-wallet me-1"></i>Financier
            </a>
            <a href="{{ route('reporting.operations') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-cogs me-1"></i>Opérations
            </a>
            <a href="{{ route('reporting.services') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-building me-1"></i>Services
            </a>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('reporting.financier') }}" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Période</label>
                    <select name="period" class="form-select form-select-sm" id="periodSelect">
                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Cette semaine</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Ce mois</option>
                        <option value="quarter" {{ $period == 'quarter' ? 'selected' : '' }}>Ce trimestre</option>
                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Cette année</option>
                        <option value="custom" {{ ($date_debut && $date_fin) ? 'selected' : '' }}>Personnalisé</option>
                    </select>
                </div>
                <div class="col-md-2" id="dateDebutWrap" style="{{ ($date_debut && $date_fin) ? '' : 'display:none' }}">
                    <label class="form-label small fw-bold">Date début</label>
                    <input type="date" name="date_debut" class="form-control form-control-sm" value="{{ $date_debut }}">
                </div>
                <div class="col-md-2" id="dateFinWrap" style="{{ ($date_debut && $date_fin) ? '' : 'display:none' }}">
                    <label class="form-label small fw-bold">Date fin</label>
                    <input type="date" name="date_fin" class="form-control form-control-sm" value="{{ $date_fin }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Tous</option>
                        <option value="entree" {{ $type_filter == 'entree' ? 'selected' : '' }}>Entrées</option>
                        <option value="sortie" {{ $type_filter == 'sortie' ? 'selected' : '' }}>Sorties</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Caisse</label>
                    <select name="caisse" class="form-select form-select-sm">
                        <option value="">Toutes les caisses</option>
                        @foreach($caisses as $caisse)
                            <option value="{{ $caisse->id }}" {{ $caisse_filter == $caisse->id ? 'selected' : '' }}>
                                {{ $caisse->nom ?? $caisse->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-filter me-1"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row mb-4 g-3">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-white-50 small">Total Entrées</div>
                            <h4 class="mb-0 fw-bold">{{ number_format($financial_data['total_entrees'], 0, ',', ' ') }} <small class="fs-6">FCFA</small></h4>
                            <small class="text-white-50">{{ $financial_data['nb_entrees'] }} entrée(s)</small>
                        </div>
                        <div class="ms-3"><i class="fas fa-arrow-circle-down fa-2x opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-white-50 small">Total Sorties</div>
                            <h4 class="mb-0 fw-bold">{{ number_format($financial_data['total_sorties'], 0, ',', ' ') }} <small class="fs-6">FCFA</small></h4>
                            <small class="text-white-50">{{ $financial_data['nb_depenses'] }} dép. · {{ $financial_data['nb_paiements_fourn'] }} fourn. · {{ $financial_data['nb_salaires'] }} sal.</small>
                        </div>
                        <div class="ms-3"><i class="fas fa-arrow-circle-up fa-2x opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm {{ $financial_data['solde_net'] >= 0 ? 'bg-primary' : 'bg-warning' }} text-white">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-white-50 small">Solde Net Période</div>
                            <h4 class="mb-0 fw-bold">{{ $financial_data['solde_net'] >= 0 ? '+' : '' }}{{ number_format($financial_data['solde_net'], 0, ',', ' ') }} <small class="fs-6">FCFA</small></h4>
                            <small class="text-white-50">{{ $financial_data['nb_transactions'] }} mouvement(s)</small>
                        </div>
                        <div class="ms-3"><i class="fas fa-balance-scale fa-2x opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm bg-dark text-white">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-white-50 small">Solde Total Caisses</div>
                            <h4 class="mb-0 fw-bold">{{ number_format($financial_data['solde_total_caisses'], 0, ',', ' ') }} <small class="fs-6">FCFA</small></h4>
                            <small class="text-white-50">{{ $financial_data['nb_virements'] }} virement(s)</small>
                        </div>
                        <div class="ms-3"><i class="fas fa-vault fa-2x opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Dettes fournisseurs (prestations non payées) --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-file-invoice-dollar me-2 text-danger"></i>Dettes Fournisseurs (factures non payées)</h6>
            <span class="badge bg-danger-subtle text-danger">
                {{ number_format($financial_data['dettes_fournisseurs'] ?? 0, 0, ',', ' ') }} FCFA
            </span>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 rounded border border-danger-subtle bg-danger-subtle">
                        <div class="small text-muted">Dette fournisseurs totale</div>
                        <div class="h5 mb-0 fw-bold text-danger">
                            {{ number_format($financial_data['dettes_fournisseurs'] ?? 0, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 rounded border border-warning-subtle bg-warning-subtle">
                        <div class="small text-muted">Factures impayées / partielles</div>
                        <div class="h5 mb-0 fw-bold text-warning">
                            {{ number_format($financial_data['nb_factures_impayees'] ?? 0, 0, ',', ' ') }}
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($dettes_fournisseurs) && $dettes_fournisseurs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Fournisseur</th>
                                <th>Facture</th>
                                <th>Date facture</th>
                                <th>Échéance</th>
                                <th class="text-end">Reste à payer</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dettes_fournisseurs as $dette)
                                <tr>
                                    <td class="fw-semibold">{{ $dette->fournisseur_nom ?? 'N/A' }}</td>
                                    <td>{{ $dette->reference ?: ($dette->numero_facture ?: ('FACT-' . $dette->id)) }}</td>
                                    <td>{{ $dette->date_facture ? \Carbon\Carbon::parse($dette->date_facture)->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @if($dette->date_echeance)
                                            @php
                                                $isOverdue = \Carbon\Carbon::parse($dette->date_echeance)->isPast();
                                            @endphp
                                            <span class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                                {{ \Carbon\Carbon::parse($dette->date_echeance)->format('d/m/Y') }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold text-danger">{{ number_format($dette->reste_a_payer ?? 0, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        @if($dette->statut === 'non_payee')
                                            <span class="badge bg-danger">Non payée</span>
                                        @elseif($dette->statut === 'partiellement_payee')
                                            <span class="badge bg-warning text-dark">Partielle</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $dette->statut }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-success mb-0">
                    <i class="fas fa-check-circle me-2"></i>Aucune dette fournisseur détectée.
                </div>
            @endif
        </div>
    </div>

    {{-- Graphique + Répartition --}}
    <div class="row mb-4 g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Évolution Entrées / Sorties</h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="chartEntreesSorties"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-info"></i>Répartition par Catégorie</h6>
                </div>
                <div class="card-body">
                    @if($repartition->count() > 0)
                        <div style="height: 200px;">
                            <canvas id="chartRepartition"></canvas>
                        </div>
                        <div class="mt-3">
                            @foreach($repartition as $cat => $data)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small fw-bold">{{ $cat }}</span>
                                    <div class="text-end">
                                        <span class="badge bg-success-subtle text-success">+{{ number_format($data['entrees'], 0, ',', ' ') }}</span>
                                        <span class="badge bg-danger-subtle text-danger">-{{ number_format($data['sorties'], 0, ',', ' ') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-pie fa-2x mb-2"></i>
                            <p class="mb-0">Aucune donnée</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Soldes des caisses --}}
    <div class="row mb-4 g-3">
        @foreach($caisses as $caisse)
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card shadow-sm border-start border-4 {{ $caisse->solde_actuel >= 0 ? 'border-success' : 'border-danger' }}">
                <div class="card-body py-2">
                    <div class="small text-muted">{{ $caisse->nom ?? $caisse->libelle }}</div>
                    <div class="fw-bold {{ $caisse->solde_actuel >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($caisse->solde_actuel, 0, ',', ' ') }} FCFA
                    </div>
                    <div class="text-muted" style="font-size: 0.7rem;">{{ ucfirst($caisse->type ?? 'N/A') }} · {{ $caisse->devise ?? 'XOF' }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Tableau des transactions --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-list me-2 text-secondary"></i>Tous les Mouvements de Trésorerie <span class="badge bg-secondary ms-1">{{ $transactions->count() }}</span></h6>
            <div>
                <a href="{{ route('reporting.export', ['type' => 'financial', 'format' => 'json']) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-download me-1"></i>Exporter
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" id="tableTransactions">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:110px">Date</th>
                            <th style="width:80px">Type</th>
                            <th style="width:120px">Catégorie</th>
                            <th>Référence</th>
                            <th>Libellé</th>
                            <th>Caisse</th>
                            <th class="text-end" style="width:140px">Montant</th>
                            <th>Auteur</th>
                            <th style="width:100px">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                            <tr>
                                <td class="small">{{ $t->date->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($t->is_entree)
                                        <span class="badge bg-success"><i class="fas fa-arrow-down me-1"></i>Entrée</span>
                                    @else
                                        <span class="badge bg-danger"><i class="fas fa-arrow-up me-1"></i>Sortie</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $catColors = [
                                            'Mouvement' => 'info',
                                            'Encaissement' => 'success',
                                            'Dépense' => 'danger',
                                            'Approvisionnement' => 'warning',
                                            'Virement' => 'primary',
                                            'Paiement Fournisseur' => 'dark',
                                            'Salaire' => 'secondary',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $catColors[$t->categorie] ?? 'secondary' }}-subtle text-{{ $catColors[$t->categorie] ?? 'secondary' }}">
                                        {{ $t->categorie }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $t->reference }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $t->libelle }}</span>
                                    @if($t->description)
                                        <br><small class="text-muted">{{ Str::limit($t->description, 60) }}</small>
                                    @endif
                                </td>
                                <td class="small">{{ $t->caisse }}</td>
                                <td class="text-end fw-bold {{ $t->is_entree ? 'text-success' : 'text-danger' }}">
                                    {{ $t->is_entree ? '+' : '-' }}{{ number_format($t->montant, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="small">{{ $t->auteur }}</td>
                                <td>
                                    @php
                                        $statutColors = [
                                            'validé' => 'success',
                                            'justifié' => 'success',
                                            'comptabilise' => 'success',
                                            'effectue' => 'success',
                                            'payé' => 'success',
                                            'en_attente' => 'warning',
                                            'en attente' => 'warning',
                                            'brouillon' => 'secondary',
                                            'non justifié' => 'danger',
                                            'rejetee' => 'danger',
                                            'annule' => 'danger',
                                            'echec' => 'danger',
                                        ];
                                        $color = $statutColors[$t->statut] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $t->statut)) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">Aucun mouvement de trésorerie trouvé pour cette période.</p>
                                    <small class="text-muted">Modifiez les filtres ou sélectionnez une autre période.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($transactions->count() > 0)
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="6" class="text-end">Totaux :</td>
                            <td class="text-end">
                                <span class="text-success">+{{ number_format($financial_data['total_entrees'], 0, ',', ' ') }}</span>
                                <br>
                                <span class="text-danger">-{{ number_format($financial_data['total_sorties'], 0, ',', ' ') }}</span>
                            </td>
                            <td colspan="2">
                                <span class="{{ $financial_data['solde_net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    = {{ number_format($financial_data['solde_net'], 0, ',', ' ') }} FCFA
                                </span>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle dates personnalisées
    const periodSelect = document.getElementById('periodSelect');
    const dateDebutWrap = document.getElementById('dateDebutWrap');
    const dateFinWrap = document.getElementById('dateFinWrap');
    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            const show = this.value === 'custom';
            dateDebutWrap.style.display = show ? '' : 'none';
            dateFinWrap.style.display = show ? '' : 'none';
        });
    }

    // Graphique Entrées / Sorties
    const chartData = @json($chart_data);
    if (chartData.length > 0 && document.getElementById('chartEntreesSorties')) {
        new Chart(document.getElementById('chartEntreesSorties'), {
            type: 'bar',
            data: {
                labels: chartData.map(d => d.date),
                datasets: [
                    {
                        label: 'Entrées',
                        data: chartData.map(d => parseFloat(d.entrees)),
                        backgroundColor: 'rgba(25, 135, 84, 0.7)',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 1,
                        borderRadius: 3,
                    },
                    {
                        label: 'Sorties',
                        data: chartData.map(d => parseFloat(d.sorties)),
                        backgroundColor: 'rgba(220, 53, 69, 0.7)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1,
                        borderRadius: 3,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ctx.dataset.label + ': ' + new Intl.NumberFormat('fr-FR').format(ctx.raw) + ' FCFA';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(value);
                            }
                        }
                    }
                }
            }
        });
    }

    // Graphique Répartition
    const repartition = @json($repartition);
    const repLabels = Object.keys(repartition);
    if (repLabels.length > 0 && document.getElementById('chartRepartition')) {
        const repValues = repLabels.map(k => parseFloat(repartition[k].entrees) + parseFloat(repartition[k].sorties));
        const repColors = ['#0dcaf0', '#198754', '#dc3545', '#ffc107', '#6f42c1', '#fd7e14'];
        new Chart(document.getElementById('chartRepartition'), {
            type: 'doughnut',
            data: {
                labels: repLabels,
                datasets: [{
                    data: repValues,
                    backgroundColor: repColors.slice(0, repLabels.length),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ctx.label + ': ' + new Intl.NumberFormat('fr-FR').format(ctx.raw) + ' FCFA';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
