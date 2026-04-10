@extends('layouts.app')

@section('title', 'Parc Auto - Dashboard')

@section('content')
<x-dashboard-layout title="Dashboard Parc Auto" icon="fa-truck">

    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Matériel roulant total"
            :value="$kpis['total'] ?? 0"
            icon="fa-truck"
            color="primary"
            subtitle="Flotte complète"
        />

        <x-kpi-card
            title="Disponibles"
            :value="$kpis['disponibles'] ?? 0"
            icon="fa-check-circle"
            color="success"
            :subtitle="($kpis['tauxUtilisation'] ?? 0) . '% de disponibilité'"
            trend="up"
            trendValue="+5% vs mois dernier"
        />

        <x-kpi-card
            title="En Maintenance"
            :value="$kpis['maintenance'] ?? 0"
            icon="fa-wrench"
            color="warning"
            subtitle="Matériel roulant en atelier"
        />

        <x-kpi-card
            title="Immobilisés"
            :value="$kpis['immobilises'] ?? 0"
            icon="fa-ban"
            color="danger"
            subtitle="Hors service"
        />
    </x-slot>

    <!-- Graphiques Principaux -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-gas-pump me-2"></i>Consommation Carburant (6 derniers mois)
                    </h6>
                    <small class="text-muted">Évolution mensuelle en FCFA</small>
                </div>
                <div class="card-body">
                    <canvas id="chartFuel" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Répartition par État
                    </h6>
                    <small class="text-muted">État actuel de la flotte</small>
                </div>
                <div class="card-body">
                    <canvas id="chartPie" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique Maintenance -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-wrench me-2"></i>Coûts de Maintenance (6 derniers mois)
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartMaint" height="70"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Activités récentes et alertes -->
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-history me-2"></i>Activités Récentes
                    </h6>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-success me-2">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                    <h6 class="mb-0">Maintenance terminée</h6>
                                </div>
                                <p class="small text-muted mb-0">Véhicule AB-123-CD • Vidange + filtres</p>
                            </div>
                            <small class="text-muted">Il y a 2h</small>
                        </div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-warning me-2">
                                        <i class="fas fa-wrench"></i>
                                    </span>
                                    <h6 class="mb-0">Entrée en atelier</h6>
                                </div>
                                <p class="small text-muted mb-0">Véhicule CD-789-EF • Réparation freins</p>
                            </div>
                            <small class="text-muted">Il y a 5h</small>
                        </div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-primary me-2">
                                        <i class="fas fa-gas-pump"></i>
                                    </span>
                                    <h6 class="mb-0">Plein de carburant</h6>
                                </div>
                                <p class="small text-muted mb-0">Véhicule EF-456-GH • 85L Gasoil</p>
                            </div>
                            <small class="text-muted">Hier</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-bell me-2"></i>Alertes & Échéances
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <!-- Alertes Visites Techniques -->
                        @forelse($alerts['expiringVisits'] ?? [] as $visit)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold text-primary">Visite Technique : {{ $visit->immatriculation }}</h6>
                                    <small class="text-muted">
                                        @if($visit->visite_tech_expiry && $visit->visite_tech_expiry->isPast())
                                            <span class="text-danger">Expirée le {{ $visit->visite_tech_expiry->format('d/m/Y') }}</span>
                                        @else
                                            Expire dans {{ now()->diffInDays($visit->visite_tech_expiry) }} jours ({{ $visit->visite_tech_expiry->format('d/m/Y') }})
                                        @endif
                                    </small>
                                </div>
                                <span class="badge bg-{{ ($visit->visite_tech_expiry && $visit->visite_tech_expiry->isPast()) ? 'danger' : 'warning' }}">
                                    {{ ($visit->visite_tech_expiry && $visit->visite_tech_expiry->isPast()) ? 'Expiré' : 'Urgent' }}
                                </span>
                            </div>
                        </div>
                        @empty
                        @endforelse

                        <!-- Alertes Assurances -->
                        @forelse($alerts['expiringAssurances'] ?? [] as $assurance)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold text-success">Assurance : {{ $assurance->immatriculation }}</h6>
                                    <small class="text-muted">
                                        @if($assurance->assurance_expiry && $assurance->assurance_expiry->isPast())
                                            <span class="text-danger">Expirée le {{ $assurance->assurance_expiry->format('d/m/Y') }}</span>
                                        @else
                                            Expire dans {{ now()->diffInDays($assurance->assurance_expiry) }} jours ({{ $assurance->assurance_expiry->format('d/m/Y') }})
                                        @endif
                                    </small>
                                </div>
                                <span class="badge bg-{{ ($assurance->assurance_expiry && $assurance->assurance_expiry->isPast()) ? 'danger' : 'warning' }}">
                                    {{ ($assurance->assurance_expiry && $assurance->assurance_expiry->isPast()) ? 'Expiré' : 'Urgent' }}
                                </span>
                            </div>
                        </div>
                        @empty
                        @endforelse

                        @if(count($alerts['expiringVisits']) == 0 && count($alerts['expiringAssurances']) == 0)
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                <p class="mb-0 text-muted">Aucune alerte critique actuelle</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-dashboard-layout>
@endsection

@push('scripts')
<script src="{{ asset('js/chart.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  // Données avec valeurs par défaut
  const defaultMonths = ['Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'];
  const defaultFuel = [850000, 920000, 880000, 950000, 1010000, 890000];
  const defaultMaint = [450000, 420000, 480000, 510000, 470000, 440000];
  const defaultPieLabels = ['Disponibles', 'En maintenance', 'Immobilisés', 'En mission'];
  const defaultPieData = [45, 5, 2, 8];

  const labels = @json($months ?? null) || defaultMonths;
  const fuel = @json($fuelPerMonth ?? null) || defaultFuel;
  const maint = @json($maintPerMonth ?? null) || defaultMaint;
  const pieLabels = @json($pieLabels ?? null) || defaultPieLabels;
  const pieData = @json($pieData ?? null) || defaultPieData;

  // Graphique Consommation Carburant
  const fuelCtx = document.getElementById('chartFuel');
  if (fuelCtx) {
    new Chart(fuelCtx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Carburant (FCFA)',
          data: fuel,
          backgroundColor: '#1cc88a',
          borderRadius: 5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'top' }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: value => value.toLocaleString('fr-FR') + ' FCFA'
            }
          }
        }
      }
    });
  }

  // Graphique Coûts de Maintenance
  const maintCtx = document.getElementById('chartMaint');
  if (maintCtx) {
    new Chart(maintCtx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Maintenance (FCFA)',
          data: maint,
          borderColor: '#ffc107',
          backgroundColor: 'rgba(255, 193, 7, 0.1)',
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'top' }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: value => value.toLocaleString('fr-FR') + ' FCFA'
            }
          }
        }
      }
    });
  }

  // Graphique Répartition par État
  const pieCtx = document.getElementById('chartPie');
  if (pieCtx) {
    new Chart(pieCtx, {
      type: 'doughnut',
      data: {
        labels: pieLabels,
        datasets: [{
          data: pieData,
          backgroundColor: ['#1cc88a', '#ffc107', '#e74a3b', '#4e73df'],
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 15,
              font: { size: 12 }
            }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return context.label + ': ' + context.parsed + ' véhicules';
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
