@extends('layouts.app')

@section('title', 'Statistiques par Service - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-pie me-2 text-primary"></i>Statistiques par Service
            </h1>
            <p class="text-muted mb-0">Analyse détaillée de la performance par service</p>
        </div>
        <div>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Imprimer
            </button>
        </div>
    </div>

    <!-- Sélection Service -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <label class="form-label fw-bold">Sélectionner un service</label>
                    <select class="form-select" id="serviceSelect" onchange="chargerStats()">
                        <option value="transport">Transport</option>
                        <option value="logistique">Logistique</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="entretien">Entretien</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Service -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Opérations</div>
                    <div class="h3 mb-0 text-primary">156</div>
                    <small class="text-success"><i class="fas fa-arrow-up"></i> +12% ce mois</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">CA Généré</div>
                    <div class="h3 mb-0 text-success">25.4M</div>
                    <small class="text-success"><i class="fas fa-arrow-up"></i> +18% ce mois</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Marge Moyenne</div>
                    <div class="h3 mb-0 text-warning">32%</div>
                    <small class="text-success"><i class="fas fa-arrow-up"></i> +3% ce mois</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Satisfaction</div>
                    <div class="h3 mb-0 text-info">4.7/5</div>
                    <small class="text-success"><i class="fas fa-arrow-up"></i> +0.2 ce mois</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Évolution Mensuelle
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="evolutionChart" height="70"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-users me-2"></i>Répartition Clients
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="clientsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau Détails -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-table me-2"></i>Détails des Opérations
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Type</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>07/11/2024</td>
                                    <td>SOCIETE ABC</td>
                                    <td><span class="badge bg-primary">Transport</span></td>
                                    <td class="fw-bold text-success">850,000 FCFA</td>
                                    <td><span class="badge bg-success">Terminé</span></td>
                                </tr>
                                <tr>
                                    <td>06/11/2024</td>
                                    <td>ENTREPRISE XYZ</td>
                                    <td><span class="badge bg-primary">Transport</span></td>
                                    <td class="fw-bold text-success">620,000 FCFA</td>
                                    <td><span class="badge bg-success">Terminé</span></td>
                                </tr>
                                <tr>
                                    <td>06/11/2024</td>
                                    <td>GROUPE DELTA</td>
                                    <td><span class="badge bg-primary">Transport</span></td>
                                    <td class="fw-bold text-success">580,000 FCFA</td>
                                    <td><span class="badge bg-warning">En cours</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctxEvol = document.getElementById('evolutionChart').getContext('2d');
const evolChart = new Chart(ctxEvol, {
    type: 'line',
    data: {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov'],
        datasets: [{
            label: 'Opérations',
            data: [12, 15, 13, 18, 16, 20, 22, 19, 24, 26, 28],
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } }
    }
});

const ctxClients = document.getElementById('clientsChart').getContext('2d');
const clientsChart = new Chart(ctxClients, {
    type: 'doughnut',
    data: {
        labels: ['Nouveaux', 'Récurrents', 'VIP'],
        datasets: [{
            data: [30, 55, 15],
            backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } }
    }
});

function chargerStats() {
    console.log('Chargement stats...');
}
</script>
@endsection
