@extends('layouts.app')

@section('title', 'Rapports & Statistiques Commerciales - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-bar me-2 text-primary"></i>Rapports & Statistiques
            </h1>
            <p class="text-muted mb-0">Analyses des performances commerciales</p>
        </div>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print me-2"></i>Imprimer
        </button>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Période</label>
                    <select class="form-select">
                        <option>Ce mois</option>
                        <option>Ce trimestre</option>
                        <option>Cette année</option>
                        <option>Personnalisé</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Agent commercial</label>
                    <select class="form-select">
                        <option>Tous</option>
                        <option>Kouassi Jean-Marc</option>
                        <option>Diallo Fatou</option>
                        <option>Koné Aminata</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Région</label>
                    <select class="form-select">
                        <option>Toutes</option>
                        <option>Abidjan</option>
                        <option>Intérieur</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Générer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">CA par Mois (FCFA)</h6>
                </div>
                <div class="card-body">
                    <canvas id="caChart" height="80"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">CA par Région</h6>
                </div>
                <div class="card-body">
                    <canvas id="regionChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableaux -->
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">Top Agents</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Agent</th>
                                <th>CA</th>
                                <th>Contrats</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Kouassi Jean-Marc</td>
                                <td class="fw-bold text-success">125M FCFA</td>
                                <td><span class="badge bg-primary">15</span></td>
                            </tr>
                            <tr>
                                <td>Diallo Fatou</td>
                                <td class="fw-bold text-success">98M FCFA</td>
                                <td><span class="badge bg-primary">12</span></td>
                            </tr>
                            <tr>
                                <td>Koné Aminata</td>
                                <td class="fw-bold text-success">85M FCFA</td>
                                <td><span class="badge bg-primary">10</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">Top Clients</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Client</th>
                                <th>CA</th>
                                <th>Contrats</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>NESTLE CI</td>
                                <td class="fw-bold text-success">150M FCFA</td>
                                <td><span class="badge bg-primary">8</span></td>
                            </tr>
                            <tr>
                                <td>BOLLORE CI</td>
                                <td class="fw-bold text-success">120M FCFA</td>
                                <td><span class="badge bg-primary">6</span></td>
                            </tr>
                            <tr>
                                <td>PETROCI</td>
                                <td class="fw-bold text-success">95M FCFA</td>
                                <td><span class="badge bg-primary">5</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctxCA = document.getElementById('caChart').getContext('2d');
new Chart(ctxCA, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
        datasets: [{
            label: 'CA (M FCFA)',
            data: [28, 32, 30, 35, 38, 42, 45, 40, 48, 52, 50, 55],
            backgroundColor: '#4e73df'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

const ctxRegion = document.getElementById('regionChart').getContext('2d');
new Chart(ctxRegion, {
    type: 'pie',
    data: {
        labels: ['Abidjan', 'Bouaké', 'San-Pédro', 'Yamoussoukro', 'Autres'],
        datasets: [{
            data: [65, 15, 10, 5, 5],
            backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#36b9cc', '#858796']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
@endsection
