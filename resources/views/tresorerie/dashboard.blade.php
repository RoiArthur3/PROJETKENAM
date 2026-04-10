@extends('layouts.app')

@section('title', 'Dashboard Tresorerie - KENAM SERVICES')

@section('content')
<x-dashboard-layout title="Dashboard Tresorerie" icon="fa-sack-dollar">

    <x-slot name="headerActions">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('tresorerie.caisses') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-cash-register me-1"></i>Caisses
            </a>
            <a href="{{ route('tresorerie.approvisionnements') }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-plus-circle me-1"></i>Approvisionnements
            </a>
            <a href="{{ route('tresorerie.decaissements') }}" class="btn btn-outline-warning btn-sm">
                <i class="fas fa-money-bill-wave me-1"></i>Decaissements
            </a>
            <a href="{{ route('tresorerie.virements') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-exchange-alt me-1"></i>Virements
            </a>
            <a href="{{ route('tresorerie.flux') }}" class="btn btn-outline-dark btn-sm">
                <i class="fas fa-stream me-1"></i>Flux
            </a>
            <a href="{{ route('tresorerie.bon-pour-accord') }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-check-double me-1"></i>BON POUR ACCORD
            </a>
        </div>
    </x-slot>

    <x-slot name="kpis">
        <x-kpi-card
            title="Total Caisses"
            :value="$stats['total_caisses']"
            icon="fa-cash-register"
            color="primary"
            subtitle="Caisses actives"
        />

        <x-kpi-card
            title="Solde Total"
            :value="number_format($stats['solde_total'], 0, ',', ' ') . ' FCFA'"
            icon="fa-wallet"
            color="success"
            subtitle="Disponibilite globale"
        />

        <x-kpi-card
            title="Decaissements"
            :value="number_format(abs($stats['total_decaissements']), 0, ',', ' ') . ' FCFA'"
            icon="fa-arrow-up-right-from-square"
            color="warning"
            subtitle="Sorties enregistrees"
        />

        <x-kpi-card
            title="En Attente"
            :value="$stats['en_attente']"
            icon="fa-clock"
            color="info"
            subtitle="Approvisionnements a traiter"
        />
    </x-slot>

    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-tachometer-alt me-2"></i>Indicateurs de Performance Tresorerie
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-primary">{{ $stats['total_approvisionnements'] }}</div>
                                <div class="metric-label">Approvisionnements</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-success">{{ $stats['valides'] }}</div>
                                <div class="metric-label">Approvisionnements valides</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-info">{{ $stats['total_virements'] }}</div>
                                <div class="metric-label">Virements</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-warning">{{ number_format($depensesMensuelles, 0, ',', ' ') }}</div>
                                <div class="metric-label">Depenses du mois (FCFA)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Mouvements Recents (5 derniers)
                    </h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="mouvementsTresorerieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-success">
                        <i class="fas fa-chart-pie me-2"></i>Repartition des Caisses
                    </h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="caissesRepartitionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-warning">
                        <i class="fas fa-money-bill-wave me-2"></i>Decaissements Recents
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Libelle</th>
                                    <th class="text-end">Montant</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($decaissements as $decaissement)
                                    <tr>
                                        <td><span class="badge bg-warning text-dark">{{ $decaissement->reference }}</span></td>
                                        <td>{{ $decaissement->libelle }}</td>
                                        <td class="text-end fw-bold text-danger">-{{ number_format((float) $decaissement->montant, 0, ',', ' ') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($decaissement->date_depense)->format('d/m/Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Aucun decaissement recent</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-info">
                        <i class="fas fa-plus-circle me-2"></i>Approvisionnements Recents
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Caisse</th>
                                    <th class="text-end">Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($approvisionnements as $approvisionnement)
                                    <tr>
                                        <td><span class="badge bg-primary">{{ $approvisionnement->reference }}</span></td>
                                        <td>{{ $approvisionnement->destination->nom ?? ($approvisionnement->destination->libelle ?? 'N/A') }}</td>
                                        <td class="text-end fw-bold text-success">+{{ number_format((float) $approvisionnement->montant, 0, ',', ' ') }}</td>
                                        <td>
                                            <span class="badge bg-{{ strtolower($approvisionnement->statut) === 'valide' || strtolower($approvisionnement->statut) === 'validé' ? 'success' : 'warning' }}">
                                                {{ $approvisionnement->statut }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Aucun approvisionnement recent</td>
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
@endsection

@push('styles')
<style>
.metric-card {
    text-align: center;
    padding: 1rem;
    border-radius: 8px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.metric-value {
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 0.4rem;
}

.metric-label {
    font-size: 0.875rem;
    color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mouvementsCtx = document.getElementById('mouvementsTresorerieChart');
    if (mouvementsCtx) {
        const decaissementsLabels = @json($decaissements->map(function ($item) {
            return \Carbon\Carbon::parse($item->date_depense)->format('d/m');
        })->values());

        const decaissementsData = @json($decaissements->pluck('montant')->values());
        const approData = @json($approvisionnements->pluck('montant')->values());

        new Chart(mouvementsCtx, {
            type: 'bar',
            data: {
                labels: decaissementsLabels,
                datasets: [
                    {
                        label: 'Decaissements',
                        data: decaissementsData,
                        backgroundColor: 'rgba(255, 193, 7, 0.7)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Approvisionnements',
                        data: approData,
                        backgroundColor: 'rgba(13, 202, 240, 0.7)',
                        borderColor: 'rgba(13, 202, 240, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    const caissesCtx = document.getElementById('caissesRepartitionChart');
    if (caissesCtx) {
        new Chart(caissesCtx, {
            type: 'doughnut',
            data: {
                labels: @json($caisses->pluck('nom')->values()),
                datasets: [{
                    data: @json($caisses->pluck('solde_actuel')->values()),
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6610f2', '#20c997']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>
@endpush
