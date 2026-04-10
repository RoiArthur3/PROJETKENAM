@extends('layouts.app')

@section('title', "Dashboard Entrepôt & Magasin - KENAM SERVICES")

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-warehouse me-2 text-primary"></i>Dashboard Entrepôt & Magasin
      </h1>
      <p class="text-muted mb-0">Vue synthèse des stocks, entrées/sorties et alertes</p>
    </div>
    <a href="{{ route('entrepot.stocks') }}" class="btn btn-primary"><i class="fas fa-boxes-stacked me-2"></i>Voir les Stocks</a>
  </div>

  <div class="row mb-4">
    <div class="col-md-3 mb-3">
      <div class="card border-start border-primary border-4 shadow-sm">
        <div class="card-body">
          <div class="text-muted small text-uppercase fw-bold">Articles</div>
          <div class="h3 mb-0 text-primary">1 245</div>
          <small class="text-muted">Références actives</small>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card border-start border-success border-4 shadow-sm">
        <div class="card-body">
          <div class="text-muted small text-uppercase fw-bold">Entrées (mois)</div>
          <div class="h3 mb-0 text-success">325</div>
          <small class="text-success"><i class="fas fa-arrow-up"></i> +12%</small>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card border-start border-warning border-4 shadow-sm">
        <div class="card-body">
          <div class="text-muted small text-uppercase fw-bold">Sorties (mois)</div>
          <div class="h3 mb-0 text-warning">298</div>
          <small class="text-warning"><i class="fas fa-exclamation-triangle"></i> À surveiller</small>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card border-start border-danger border-4 shadow-sm">
        <div class="card-body">
          <div class="text-muted small text-uppercase fw-bold">Ruptures</div>
          <div class="h3 mb-0 text-danger">12</div>
          <small class="text-danger">Seuil critique atteint</small>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8 mb-3">
      <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
          <h6 class="m-0 fw-bold text-primary"><i class="fas fa-chart-line me-2"></i>Flux Mensuels (Entrées vs Sorties)</h6>
        </div>
        <div class="card-body">
          <canvas id="fluxChart" height="90"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-4 mb-3">
      <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
          <h6 class="m-0 fw-bold text-primary"><i class="fas fa-triangle-exclamation me-2"></i>Alertes de Stock</h6>
        </div>
        <div class="card-body">
          <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Filtres à huile 10T (FH-10T)
              <span class="badge bg-danger">Rupture</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Pneus 20T (PN-20T)
              <span class="badge bg-warning text-dark">Faible</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Kits frein (KF-PL)
              <span class="badge bg-warning text-dark">Faible</span>
            </li>
          </ul>
          <a href="{{ route('entrepot.alertes') }}" class="btn btn-sm btn-outline-danger w-100 mt-3"><i class="fas fa-bell me-2"></i>Voir toutes les alertes</a>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4 mb-3">
      <div class="card shadow-sm text-center h-100">
        <div class="card-body">
          <i class="fas fa-truck-ramp-box fa-3x text-success mb-3"></i>
          <h6>Entrées Marchandises</h6>
          <p class="text-muted small">Réceptions fournisseurs</p>
          <a href="{{ route('entrepot.entrees') }}" class="btn btn-sm btn-success"><i class="fas fa-arrow-right me-2"></i>Accéder</a>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card shadow-sm text-center h-100">
        <div class="card-body">
          <i class="fas fa-dolly fa-3x text-primary mb-3"></i>
          <h6>Sorties vers Opérations</h6>
          <p class="text-muted small">Consommables, pièces, livraisons</p>
          <a href="{{ route('entrepot.sorties') }}" class="btn btn-sm btn-primary"><i class="fas fa-arrow-right me-2"></i>Accéder</a>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card shadow-sm text-center h-100">
        <div class="card-body">
          <i class="fas fa-clipboard-check fa-3x text-info mb-3"></i>
          <h6>Inventaires</h6>
          <p class="text-muted small">Contrôles réguliers et ajustements</p>
          <a href="{{ route('entrepot.inventaires') }}" class="btn btn-sm btn-info"><i class="fas fa-arrow-right me-2"></i>Accéder</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('fluxChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
    datasets: [
      { label: 'Entrées', data: [28,32,30,35,38,42,45,41,39,44,47,50], backgroundColor: '#1cc88a' },
      { label: 'Sorties', data: [25,29,28,31,36,39,41,38,35,40,43,46], backgroundColor: '#4e73df' }
    ]
  },
  options: { responsive: true, maintainAspectRatio: false }
});
</script>
@endsection
