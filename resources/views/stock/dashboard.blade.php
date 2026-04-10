@extends('layouts.app')

@section('title', 'Stock - Dashboard')

@section('content')
<x-dashboard-layout title="Dashboard Stock & Magasin" icon="fa-warehouse">

    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Valeur Totale Stock"
            value="{{ number_format($totalValue, 0, ',', ' ') }} FCFA"
            icon="fa-coins"
            color="primary"
            subtitle="Valeur globale"
            trend="up"
            trendValue="{{ $totalValue > 0 ? '+12% vs mois dernier' : '0%' }}"
        />

        <x-kpi-card
            title="Matériels en Stock"
            value="{{ $totalProducts }}"
            icon="fa-boxes"
            color="success"
            subtitle="Références actives"
        />

        <x-kpi-card
            title="Alertes Stock"
            value="{{ $lowStockCount + $criticalStockCount }}"
            icon="fa-exclamation-triangle"
            color="{{ $criticalStockCount > 0 ? 'danger' : 'warning' }}"
            subtitle="{{ $criticalStockCount }} critiques, {{ $lowStockCount }} basses"
        />

        <x-kpi-card
            title="Mouvements (jour)"
            value="{{ $recentMovements->count() }}"
            icon="fa-exchange-alt"
            color="info"
            subtitle="Derniers mouvements"
        />
    </x-slot>

    <!-- Graphiques Principaux -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Mouvements de Stock (30 derniers jours)
                    </h6>
                    <small class="text-muted">Entrées et sorties quotidiennes</small>
                </div>
                <div class="card-body">
                    <canvas id="mouvementsChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-star me-2"></i>Top Matériels
                    </h6>
                    <small class="text-muted">Les plus mouvementés</small>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-bold">Casque de chantier</span>
                            <span class="small text-muted">85 mvts</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 85%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-bold">Gants de protection</span>
                            <span class="small text-muted">70 mvts</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 70%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-bold">Vis à bois 4x40</span>
                            <span class="small text-muted">55 mvts</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 55%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-bold">Ciment 50kg</span>
                            <span class="small text-muted">40 mvts</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 40%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes et Mouvements -->
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-exclamation-triangle me-2"></i>Alertes Stock
                    </h6>
                    <a href="{{ url('/stock/alertes') }}" class="btn btn-sm btn-outline-warning">Voir tout</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Matériel</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-center">Min</th>
                                    <th>État</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Casque de chantier</strong></td>
                                    <td class="text-center"><span class="badge bg-danger">0</span></td>
                                    <td class="text-center">20</td>
                                    <td><span class="badge bg-danger">Rupture</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Gants de protection</strong></td>
                                    <td class="text-center"><span class="badge bg-warning text-dark">15</span></td>
                                    <td class="text-center">50</td>
                                    <td><span class="badge bg-warning text-dark">Stock faible</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Lunettes de sécurité</strong></td>
                                    <td class="text-center"><span class="badge bg-warning text-dark">8</span></td>
                                    <td class="text-center">30</td>
                                    <td><span class="badge bg-warning text-dark">Stock faible</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-history me-2"></i>Derniers Mouvements
                    </h6>
                    <a href="{{ url('/stock/mouvements') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Produit</th>
                                    <th class="text-end">Qté</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentMovements as $movement)
                                    <tr>
                                        <td><small>{{ $movement->created_at->format('d/m H:i') }}</small></td>
                                        <td>
                                            <span class="badge bg-{{
                                                $movement->type === 'entree' ? 'success' :
                                                ($movement->type === 'sortie' ? 'danger' : 'info')
                                            }}">
                                                {{ ucfirst($movement->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $movement->produit->name ?? $movement->produit_nom ?? 'N/A' }}</td>
                                        <td class="text-end {{
                                            $movement->type === 'entree' ? 'text-success' :
                                            ($movement->type === 'sortie' ? 'text-danger' : '')
                                        }} fw-bold">
                                            {{ $movement->type === 'entree' ? '+' : ($movement->type === 'sortie' ? '-' : '') }}{{ $movement->quantite }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Aucun mouvement récent</td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  // Données avec valeurs par défaut
  const defaultLabels = ['1 Oct', '5 Oct', '10 Oct', '15 Oct', '20 Oct', '25 Oct', '30 Oct'];
  const defaultEntrees = [12, 19, 15, 25, 22, 30, 28];
  const defaultSorties = [8, 15, 12, 18, 20, 25, 22];

  const labels = @json($labels ?? null) || defaultLabels;
  const entrees = @json($entrees ?? null) || defaultEntrees;
  const sorties = @json($sorties ?? null) || defaultSorties;

  // Graphique Mouvements de Stock
  const ctx = document.getElementById('mouvementsChart');
  if (ctx) {
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Entrées',
            data: entrees,
            borderColor: '#1cc88a',
            backgroundColor: 'rgba(28, 200, 138, 0.1)',
            tension: 0.4,
            fill: true
          },
          {
            label: 'Sorties',
            data: sorties,
            borderColor: '#e74a3b',
            backgroundColor: 'rgba(231, 74, 59, 0.1)',
            tension: 0.4,
            fill: true
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
            beginAtZero: true,
            ticks: {
              callback: value => value + ' unités'
            }
          }
        }
      }
    });
  }
});
</script>
@endpush
@endsection
