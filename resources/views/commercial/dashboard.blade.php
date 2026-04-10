@extends('layouts.app')

@section('title', 'Dashboard Commercial - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-line me-2 text-primary"></i>Dashboard Commercial
            </h1>
            <p class="text-muted mb-0">Vue globale des performances commerciales</p>
        </div>
        <button class="btn btn-primary" onclick="rafraichir()">
            <i class="fas fa-sync-alt me-2"></i>Rafraîchir
        </button>
    </div>

    @if(isset($dbError) && $dbError)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ $dbError }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- KPIs Commerciaux -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Total Clients</div>
                            <div class="h3 mb-0 text-primary">{{ number_format($stats['total_clients'], 0, ',', ' ') }}</div>
                            <small class="text-muted">Enregistrés</small>
                        </div>
                        <i class="fas fa-users fa-3x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Contrats Actifs</div>
                            <div class="h3 mb-0 text-success">{{ number_format($stats['total_contrats'], 0, ',', ' ') }}</div>
                            <small class="text-muted">En cours</small>
                        </div>
                        <i class="fas fa-file-contract fa-3x text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Devis & Proformas</div>
                            <div class="h3 mb-0 text-warning">{{ number_format($stats['total_devis'], 0, ',', ' ') }}</div>
                            <small class="text-muted">Émis</small>
                        </div>
                        <i class="fas fa-file-invoice fa-3x text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <!-- Contrats Récents -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-file-contract me-2"></i>Contrats Récents
                    </h6>
                </div>
                <div class="card-body">
                    @if($recentContracts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Client</th>
                                        <th>Montant</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentContracts as $contract)
                                    <tr>
                                        <td class="fw-bold">{{ $contract->reference }}</td>
                                        <td>{{ $contract->client_nom }}</td>
                                        <td class="text-success fw-bold">{{ number_format($contract->montant, 0, ',', ' ') }} FCFA</td>
                                        <td class="text-muted">{{ $contract->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun contrat récent</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Devis en Attente -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-warning">
                        <i class="fas fa-file-invoice me-2"></i>Devis en Attente
                    </h6>
                </div>
                <div class="card-body">
                    @if($pendingQuotes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Client</th>
                                        <th>Montant</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingQuotes as $quote)
                                    <tr>
                                        <td class="fw-bold">{{ $quote->reference }}</td>
                                        <td>{{ $quote->client_nom }}</td>
                                        <td class="text-warning fw-bold">{{ number_format($quote->montant, 0, ',', ' ') }} FCFA</td>
                                        <td class="text-muted">{{ $quote->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun devis en attente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Rapides -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-info">
                        <i class="fas fa-bolt me-2"></i>Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <a href="{{ route('commercial.devis.create') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-plus me-2"></i>Nouveau Devis
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="{{ route('commercial.contrats.create') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-plus me-2"></i>Nouveau Contrat
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="{{ route('commercial.clients.create') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-user-plus me-2"></i>Nouveau Client
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctxCA = document.getElementById('caChart').getContext('2d');
new Chart(ctxCA, {
    type: 'line',
    data: {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
        datasets: [{
            label: 'CA (M FCFA)',
            data: [28, 32, 30, 35, 38, 42],
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

const ctxPipeline = document.getElementById('pipelineChart').getContext('2d');
new Chart(ctxPipeline, {
    type: 'doughnut',
    data: {
        labels: ['Prospection', 'Négociation', 'Proposition', 'Gagné'],
        datasets: [{
            data: [15, 25, 20, 40],
            backgroundColor: ['#858796', '#f6c23e', '#36b9cc', '#1cc88a']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } }
    }
});

function rafraichir() {
    location.reload();
}
</script>
@endsection
