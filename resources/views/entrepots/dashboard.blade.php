@extends('layouts.app')

@section('title', 'Dashboard Entrepôts - KENAM SERVICES')

@php
    // Récupérer les données des entrepôts
    $entrepots = \App\Models\Entrepot::all();

    // Calculer les statistiques
    $stats = [
        'total_entrepots' => $entrepots->count(),
        'capacite_totale' => $entrepots->sum('capacite'),
        'capacite_utilisee' => $entrepots->sum('capacite_utilisee'),
        'taux_occupation_moyen' => $entrepots->avg('taux_occupation'),
        'en_alerte' => 0,
        'entrepots_alertes' => collect()
    ];

    // Calculer le taux d'occupation moyen
    if ($stats['capacite_totale'] > 0) {
        $stats['taux_occupation_moyen'] = ($stats['capacite_utilisee'] / $stats['capacite_totale']) * 100;
    }

    // Identifier les entrepôts en alerte
    $stats['entrepots_alertes'] = $entrepots->filter(function($entrepot) {
        $taux = ($entrepot->capacite > 0) ? ($entrepot->capacite_utilisee / $entrepot->capacite) * 100 : 0;
        $entrepot->taux_occupation = $taux;
        return $taux > 85; // Alert si plus de 85% d'occupation
    });

    $stats['en_alerte'] = $stats['entrepots_alertes']->count();

    // Données pour les KPIs
    $kpis = [
        'total' => $stats['total_entrepots'],
        'disponibles' => $entrepots->where('statut', 'actif')->count(),
        'pleins' => $stats['entrepots_alertes']->count(),
        'taux_occupation' => round($stats['taux_occupation_moyen'], 1)
    ];

    // Données pour les transferts récents
    $transfertsRecents = collect([
        (object)[
            'reference' => 'TRF-2026-001',
            'source' => 'Entrepôt Principal',
            'destination' => 'Entrepôt Nord',
            'produit' => 'Ordinateur Dell XPS',
            'quantite' => 25,
            'date' => '20/01/2026',
            'statut' => 'Livré'
        ],
        (object)[
            'reference' => 'TRF-2026-002',
            'source' => 'Entrepôt Principal',
            'destination' => 'Entrepôt Sud',
            'produit' => 'Imprimante HP',
            'quantite' => 15,
            'date' => '19/01/2026',
            'statut' => 'En Transit'
        ],
        (object)[
            'reference' => 'TRF-2026-003',
            'source' => 'Entrepôt Nord',
            'destination' => 'Entrepôt Central',
            'produit' => 'Bureau Réglable',
            'quantite' => 20,
            'date' => '18/01/2026',
            'statut' => 'En Attente'
        ]
    ]);
@endphp

@section('content')
<x-dashboard-layout title="Dashboard Entrepôts" icon="fa-warehouse" subtitle="Gestion des stocks et entrepôts">

    <!-- Actions Header -->
    <x-slot name="headerActions">
        <div class="d-flex gap-2">
            <a href="{{ route('entrepots.list') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-2"></i>Liste des Entrepôts
            </a>
            <a href="{{ route('entrepots.stock') }}" class="btn btn-outline-primary">
                <i class="fas fa-boxes me-2"></i>Gestion des Stocks
            </a>
            <a href="{{ route('entrepots.transferts') }}" class="btn btn-outline-info">
                <i class="fas fa-exchange-alt me-2"></i>Transferts
            </a>
            <a href="{{ route('entrepots.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Créer un Entrepôt
            </a>
        </div>
    </x-slot>

    <!-- KPIs Principaux -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Total Entrepôts"
            :value="$kpis['total'] ?? 0"
            icon="fa-warehouse"
            color="primary"
            subtitle="Infrastructure complète"
        />

        <x-kpi-card
            title="Disponibles"
            :value="$kpis['disponibles'] ?? 0"
            icon="fa-check-circle"
            color="success"
            subtitle="Entrepôts opérationnels"
            trend="up"
            trendValue="+2 cette semaine"
        />

        <x-kpi-card
            title="Capacité Totale"
            :value="number_format($stats['capacite_totale'], 0, ',', ' ')"
            icon="fa-database"
            color="info"
            subtitle="Unités de stockage"
        />

        <x-kpi-card
            title="Taux Occupation"
            :value="$kpis['taux_occupation'] . '%'"
            icon="fa-chart-pie"
            color="warning"
            subtitle="Moyenne globale"
            trend="{{ $stats['taux_occupation_moyen'] > 80 ? 'up' : 'down' }}"
            trendValue="{{ $stats['taux_occupation_moyen'] > 80 ? 'Attention' : 'Normal' }}"
        />
    </x-slot>

    <!-- Actions rapides -->
    <x-slot name="headerActions">
        <div class="d-flex gap-2">
            <a href="{{ route('entrepots.list') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-list me-1"></i>Liste Entrepôts
            </a>
            <a href="{{ route('entrepots.stock') }}" class="btn btn-outline-info btn-sm">
                <i class="fas fa-boxes me-1"></i>Stock
            </a>
            <a href="{{ route('entrepots.transferts') }}" class="btn btn-outline-warning btn-sm">
                <i class="fas fa-exchange-alt me-1"></i>Transferts
            </a>
            <a href="{{ route('entrepots.rapports') }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-chart-bar me-1"></i>Rapports
            </a>
        </div>
    </x-slot>

    <!-- Alertes Entrepôts -->
    @if($stats['en_alerte'] > 0)
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-warning text-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-exclamation-triangle me-2"></i>Alertes des Entrepôts
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($stats['entrepots_alertes'] as $entrepot)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center p-3 border rounded bg-warning bg-opacity-10">
                                <div class="avatar-circle bg-warning text-white me-3">
                                    <i class="fas fa-exclamation"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $entrepot->nom }}</div>
                                    <div class="text-muted small">
                                        Taux d'occupation: {{ number_format($entrepot->taux_occupation, 1, ',', ' ') }}%
                                    </div>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar bg-warning" style="width: {{ min($entrepot->taux_occupation, 100) }}%"></div>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Métriques Détaillées -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-tachometer-alt me-2"></i>Indicateurs de Performance des Entrepôts
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-primary">{{ $stats['total_entrepots'] }}</div>
                                <div class="metric-label">Entrepôts Total</div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-success">{{ number_format($stats['capacite_totale'], 0, ',', ' ') }}</div>
                                <div class="metric-label">Capacité Totale</div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-info">{{ number_format($stats['capacite_utilisee'], 0, ',', ' ') }}</div>
                                <div class="metric-label">Capacité Utilisée</div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-info" style="width: {{ $stats['capacite_totale'] > 0 ? min(($stats['capacite_utilisee'] / $stats['capacite_totale']) * 100, 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-warning">{{ number_format($stats['taux_occupation_moyen'], 1, ',', ' ') }}%</div>
                                <div class="metric-label">Taux Occupation</div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-warning" style="width: {{ min($stats['taux_occupation_moyen'], 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-danger">{{ $stats['en_alerte'] }}</div>
                                <div class="metric-label">Entrepôts en Alerte</div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-danger" style="width: {{ $stats['total_entrepots'] > 0 ? min(($stats['en_alerte'] / $stats['total_entrepots']) * 100, 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-success">{{ $kpis['disponibles'] }}</div>
                                <div class="metric-label">Entrepôts Disponibles</div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: {{ $stats['total_entrepots'] > 0 ? min(($kpis['disponibles'] / $stats['total_entrepots']) * 100, 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et Statistiques -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Mouvements de Stock (30 derniers jours)
                    </h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="mouvementsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-success">
                        <i class="fas fa-chart-pie me-2"></i>Répartition par Entrepôt
                    </h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="entrepotChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières Activités -->
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-info">
                        <i class="fas fa-exchange-alt me-2"></i>Derniers Transferts
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Source</th>
                                    <th>Destination</th>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transfertsRecents as $transfert)
                                <tr>
                                    <td><span class="badge bg-primary">{{ $transfert->reference }}</span></td>
                                    <td>{{ $transfert->source }}</td>
                                    <td>{{ $transfert->destination }}</td>
                                    <td>{{ $transfert->produit }}</td>
                                    <td>{{ $transfert->quantite }}</td>
                                    <td>{{ $transfert->date }}</td>
                                    <td>
                                        @switch($transfert->statut)
                                            @case('Livré')
                                                <span class="badge bg-success">{{ $transfert->statut }}</span>
                                            @break
                                            @case('En Transit')
                                                <span class="badge bg-warning">{{ $transfert->statut }}</span>
                                            @break
                                            @case('En Attente')
                                                <span class="badge bg-info">{{ $transfert->statut }}</span>
                                            @break
                                            @default
                                                <span class="badge bg-secondary">{{ $transfert->statut }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Aucun transfert récent</td>
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
                    <h6 class="m-0 fw-bold text-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Alertes de Stock par Entrepôt
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Entrepôt</th>
                                    <th>Produits en Alertes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($stats['en_alerte'] > 0)
                                    @foreach($stats['entrepots_alertes'] as $entrepot)
                                    <tr>
                                        <td>{{ $entrepot->nom }}</td>
                                        <td>
                                            <span class="badge bg-warning">Stock critique</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-warning" onclick="voirDetails({{ $entrepot->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td colspan="3" class="text-center text-success">
                                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                                        <p class="mb-0">Aucune alerte de stock</p>
                                    </td>
                                </tr>
                                @endif
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
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.metric-value {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.metric-label {
    font-size: 0.875rem;
    color: #6c757d;
}

.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: bold;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Initialisation des graphiques
    document.addEventListener('DOMContentLoaded', function() {
        // Graphique des mouvements de stock
        const mouvementsCtx = document.getElementById('mouvementsChart');
        if (mouvementsCtx) {
            new Chart(mouvementsCtx, {
                type: 'line',
                data: {
                    labels: ['21 Déc', '28 Déc', '4 Jan', '11 Jan', '18 Jan', '25 Jan', '1 Fév', '8 Fév', '15 Fév', '22 Fév', '1 Mar'],
                    datasets: [{
                        label: 'Entrées',
                        data: [120, 150, 180, 140, 200, 170, 190, 210, 180, 220, 195],
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Sorties',
                        data: [100, 130, 160, 120, 180, 150, 170, 190, 160, 200, 175],
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
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

        // Graphique de répartition par entrepôt
        const entrepotCtx = document.getElementById('entrepotChart');
        if (entrepotCtx) {
            new Chart(entrepotCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($entrepots->pluck('nom')->toArray()),
                    datasets: [{
                        data: @json($entrepots->pluck('produits_count')->toArray()),
                        backgroundColor: [
                            '#007bff',
                            '#28a745',
                            '#ffc107',
                            '#dc3545',
                            '#6610f2',
                            '#20c997'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }
    });

    // Fonction pour voir les détails
    function voirDetails(entrepotId) {
        console.log('Voir détails de l\'entrepôt:', entrepotId);
        // Implémenter la logique pour afficher les détails
    }
</script>
@endpush
