@extends('layouts.app')

@section('title', 'Dashboard Reporting - KENAM SERVICES')

@section('content')
<div class="content-wrapper">

    {{-- Navigation --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Dashboard Reporting</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('reporting.dashboard') }}" class="btn btn-primary btn-sm active">
                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
            </a>
            <a href="{{ route('reporting.financier') }}" class="btn btn-outline-success btn-sm">
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

    {{-- KPIs Opérations --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-white-50 small">Total Opérations</div>
                            <h3 class="mb-0 fw-bold">{{ $stats['total_operations'] }}</h3>
                            <small class="text-white-50">{{ $stats['operations_month'] }} ce mois · {{ $stats['operations_today'] }} aujourd'hui</small>
                        </div>
                        <div class="ms-3"><i class="fas fa-cogs fa-2x opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-white-50 small">En Attente de Validation</div>
                            <h3 class="mb-0 fw-bold">{{ $stats['operations_pending'] }}</h3>
                            <small class="text-white-50">{{ $stats['operations_approved'] }} approuvée(s) · {{ $stats['operations_rejected'] }} rejetée(s)</small>
                        </div>
                        <div class="ms-3"><i class="fas fa-hourglass-half fa-2x opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-white-50 small">Montant Opérations</div>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['montant_operations'], 0, ',', ' ') }} <small class="fs-6">FCFA</small></h3>
                            <small class="text-white-50">{{ $stats['operations_paid'] }} payée(s)</small>
                        </div>
                        <div class="ms-3"><i class="fas fa-money-bill-wave fa-2x opacity-50"></i></div>
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
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['solde_total_caisses'], 0, ',', ' ') }} <small class="fs-6">FCFA</small></h3>
                            <small class="text-white-50">{{ $stats['nb_caisses'] }} caisse(s) active(s)</small>
                        </div>
                        <div class="ms-3"><i class="fas fa-vault fa-2x opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPIs Trésorerie --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-start border-4 border-success">
                <div class="card-body py-2">
                    <div class="small text-muted">Entrées ce mois</div>
                    <div class="fw-bold text-success fs-5">+{{ number_format($stats['entrees_mois'], 0, ',', ' ') }} FCFA</div>
                    <small class="text-muted">{{ $stats['nb_encaissements_mois'] }} encaissement(s)</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-start border-4 border-danger">
                <div class="card-body py-2">
                    <div class="small text-muted">Sorties ce mois</div>
                    <div class="fw-bold text-danger fs-5">-{{ number_format($stats['sorties_mois'], 0, ',', ' ') }} FCFA</div>
                    <small class="text-muted">dont {{ number_format($stats['depenses_mois'], 0, ',', ' ') }} en dépenses</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-start border-4 border-info">
                <div class="card-body py-2">
                    <div class="small text-muted">Approvisionnements</div>
                    <div class="fw-bold text-info fs-5">{{ $stats['nb_approv_total'] }}</div>
                    <small class="text-muted">{{ $stats['nb_approv_pending'] }} en attente</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-start border-4 border-primary">
                <div class="card-body py-2">
                    <div class="small text-muted">Utilisateurs</div>
                    <div class="fw-bold text-primary fs-5">{{ $stats['nb_users'] }}</div>
                    <small class="text-muted">comptes actifs</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques + Répartition --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Évolution des Opérations (6 derniers mois)</h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="chartOpsEvolution"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-info"></i>Opérations par Statut</h6>
                </div>
                <div class="card-body">
                    @if($ops_by_status->count() > 0)
                        <div style="height: 200px;">
                            <canvas id="chartOpsStatus"></canvas>
                        </div>
                        <div class="mt-3">
                            @php
                                $statusLabels = [
                                    'pending_validation' => ['En attente', 'warning'],
                                    'approuvee' => ['Approuvée', 'success'],
                                    'payee' => ['Payée', 'primary'],
                                    'rejetee' => ['Rejetée', 'danger'],
                                    'en_cours' => ['En cours', 'info'],
                                    'termine' => ['Terminée', 'secondary'],
                                ];
                            @endphp
                            @foreach($ops_by_status as $s)
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-{{ $statusLabels[$s->statut_courant][1] ?? 'secondary' }}">
                                        {{ $statusLabels[$s->statut_courant][0] ?? ucfirst(str_replace('_', ' ', $s->statut_courant)) }}
                                    </span>
                                    <span class="small fw-bold">{{ $s->nb }} <span class="text-muted fw-normal">({{ number_format($s->total_montant, 0, ',', ' ') }} FCFA)</span></span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-pie fa-2x mb-2"></i>
                            <p class="mb-0">Aucune opération</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Soldes des caisses --}}
    @if($caisses->count() > 0)
    <div class="row g-3 mb-4">
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
    @endif

    {{-- Tableaux : Opérations récentes + Mouvements trésorerie --}}
    <div class="row g-3">
        {{-- Opérations récentes --}}
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-cogs me-2 text-primary"></i>Dernières Opérations <span class="badge bg-primary ms-1">{{ $recent_operations->count() }}</span></h6>
                    <a href="{{ route('reporting.operations') }}" class="btn btn-outline-primary btn-sm">Voir tout</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Réf.</th>
                                    <th>Titre</th>
                                    <th class="text-end">Montant</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_operations as $op)
                                    @php
                                        $opStatusColors = [
                                            'pending_validation' => 'warning',
                                            'approuvee' => 'success',
                                            'payee' => 'primary',
                                            'rejetee' => 'danger',
                                            'en_cours' => 'info',
                                            'termine' => 'secondary',
                                        ];
                                    @endphp
                                    <tr>
                                        <td class="small text-muted">{{ $op->numero_projet }}</td>
                                        <td>
                                            <span class="fw-semibold">{{ Str::limit($op->titre ?? $op->description ?? '-', 35) }}</span>
                                            @if($op->initiateur)
                                                <br><small class="text-muted">{{ $op->initiateur->name }}</small>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($op->montant ?? 0, 0, ',', ' ') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $opStatusColors[$op->statut_courant] ?? 'secondary' }}">
                                                {{ ucfirst(str_replace('_', ' ', $op->statut_courant ?? '-')) }}
                                            </span>
                                        </td>
                                        <td class="small">{{ $op->created_at ? $op->created_at->format('d/m/Y') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            Aucune opération
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Derniers mouvements trésorerie --}}
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-wallet me-2 text-success"></i>Derniers Mouvements Trésorerie</h6>
                    <a href="{{ route('reporting.financier') }}" class="btn btn-outline-success btn-sm">Voir tout</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Libellé</th>
                                    <th class="text-end">Montant</th>
                                    <th>Caisse</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($derniers_mouvements as $mvt)
                                    <tr>
                                        <td class="small">{{ $mvt->date->format('d/m H:i') }}</td>
                                        <td class="small">{{ Str::limit($mvt->libelle, 30) }}</td>
                                        <td class="text-end fw-bold {{ $mvt->is_entree ? 'text-success' : 'text-danger' }}">
                                            {{ $mvt->is_entree ? '+' : '-' }}{{ number_format($mvt->montant, 0, ',', ' ') }}
                                        </td>
                                        <td class="small">{{ $mvt->caisse }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            Aucun mouvement récent
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
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique évolution opérations
    const chartOps = @json($chart_ops);
    if (chartOps.length > 0 && document.getElementById('chartOpsEvolution')) {
        new Chart(document.getElementById('chartOpsEvolution'), {
            type: 'bar',
            data: {
                labels: chartOps.map(d => d.label),
                datasets: [
                    {
                        label: 'Nombre d\'opérations',
                        data: chartOps.map(d => d.count),
                        backgroundColor: 'rgba(13, 110, 253, 0.7)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Montant (FCFA)',
                        data: chartOps.map(d => d.montant),
                        type: 'line',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        borderWidth: 2,
                        pointRadius: 4,
                        fill: true,
                        tension: 0.3,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                if (ctx.datasetIndex === 1)
                                    return ctx.dataset.label + ': ' + new Intl.NumberFormat('fr-FR').format(ctx.raw) + ' FCFA';
                                return ctx.dataset.label + ': ' + ctx.raw;
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, position: 'left', title: { display: true, text: 'Opérations' } },
                    y1: {
                        beginAtZero: true, position: 'right', grid: { drawOnChartArea: false },
                        title: { display: true, text: 'Montant (FCFA)' },
                        ticks: { callback: function(v) { return new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v); } }
                    }
                }
            }
        });
    }

    // Graphique répartition par statut
    const opsStatus = @json($ops_by_status);
    if (opsStatus.length > 0 && document.getElementById('chartOpsStatus')) {
        const statusColors = {
            'pending_validation': '#ffc107',
            'approuvee': '#198754',
            'payee': '#0d6efd',
            'rejetee': '#dc3545',
            'en_cours': '#0dcaf0',
            'termine': '#6c757d',
        };
        new Chart(document.getElementById('chartOpsStatus'), {
            type: 'doughnut',
            data: {
                labels: opsStatus.map(s => {
                    const labels = {'pending_validation':'En attente','approuvee':'Approuvée','payee':'Payée','rejetee':'Rejetée','en_cours':'En cours','termine':'Terminée'};
                    return labels[s.statut_courant] || s.statut_courant;
                }),
                datasets: [{
                    data: opsStatus.map(s => parseInt(s.nb)),
                    backgroundColor: opsStatus.map(s => statusColors[s.statut_courant] || '#adb5bd'),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                }
            }
        });
    }
});
</script>
@endpush
