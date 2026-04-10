@extends('layouts.app')

@section('title', 'Rapport de Trésorerie - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Rapport de Trésorerie</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour Dashboard
            </a>
            <a href="{{ route('comptabilite.rapports.bilan') }}" class="btn btn-outline-info">
                <i class="fas fa-balance-scale me-2"></i>Bilan
            </a>
            <a href="{{ route('comptabilite.rapports.compte-resultat') }}" class="btn btn-outline-primary">
                <i class="fas fa-calculator me-2"></i>Compte de Résultat
            </a>
        </div>
    </div>

    <!-- Période de rapport -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Période</h5>
                </div>
                <div class="card-body">
                    <select class="form-select" id="periode" onchange="chargerRapport()">
                        <option value="2024">Année 2024</option>
                        <option value="2023">Année 2023</option>
                        <option value="2022">Année 2022</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Type de rapport</h5>
                </div>
                <div class="card-body">
                    <select class="form-select" id="type_rapport" onchange="chargerRapport()">
                        <option value="mensuel">Mensuel</option>
                        <option value="trimestriel">Trimestriel</option>
                        <option value="annuel">Annuel</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Format d'export</h5>
                </div>
                <div class="card-body">
                    <select class="form-select" id="format_export" onchange="chargerRapport()">
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel</option>
                        <option value="html">HTML</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs de Trésorerie -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Solde Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">25 000 000 FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Sorties</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">8 500 000 FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Entrées</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">12 000 000 FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Flux Net</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">3 500 000 FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Répartition par caisse -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Répartition par Caisse</h5>
                </div>
                <div class="card-body">
                    <canvas id="caisseChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Répartition par Type</h5>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des mouvements -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Détail des Mouvements de Trésorerie</h5>
                    
                    <!-- Filtres -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Rechercher...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="typeFilter">
                                <option value="">Tous les types</option>
                                <option value="Encaissement">Encaissement</option>
                                <option value="Décaissement">Décaissement</option>
                                <option value="Virement">Virement</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="caisseFilter">
                                <option value="">Toutes les caisses</option>
                                <option value="Caisse Principale">Caisse Principale</option>
                                <option value="Caisse Secondaire">Caisse Secondaire</option>
                                <option value="Caisse Mobile">Caisse Mobile</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="dateFilter">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" onclick="exporterRapport()">
                                <i class="fas fa-download me-2"></i>Exporter
                            </button>
                        </div>
                    </div>

                    <!-- Tableau -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Référence</th>
                                    <th>Type</th>
                                    <th>Caisse</th>
                                    <th>Libellé</th>
                                    <th>Entrée</th>
                                    <th>Sortie</th>
                                    <th>Solde</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>30/01/2024</td>
                                    <td>ENCA-2024-001</td>
                                    <td><span class="badge bg-success">Encaissement</span></td>
                                    <td><span class="badge bg-info">Caisse Principale</span></td>
                                    <td>Vente services</td>
                                    <td class="text-success fw-bold">2 500 000</td>
                                    <td>-</td>
                                    <td class="fw-bold">15 000 000</td>
                                    <td><span class="badge bg-success">Validé</span></td>
                                </tr>
                                <tr>
                                    <td>29/01/2024</td>
                                    <td>DECA-2024-001</td>
                                    <td><span class="badge bg-danger">Décaissement</span></td>
                                    <td><span class="badge bg-info">Caisse Principale</span></td>
                                    <td>Achat fournitures</td>
                                    <td>-</td>
                                    <td class="text-danger fw-bold">500 000</td>
                                    <td class="fw-bold">12 500 000</td>
                                    <td><span class="badge bg-success">Validé</span></td>
                                </tr>
                                <tr>
                                    <td>28/01/2024</td>
                                    <td>VIR-2024-001</td>
                                    <td><span class="badge bg-primary">Virement</span></td>
                                    <td><span class="badge bg-warning">BIAO</span></td>
                                    <td>Transfert caisse</td>
                                    <td class="text-success fw-bold">3 000 000</td>
                                    <td>-</td>
                                    <td class="fw-bold">13 000 000</td>
                                    <td><span class="badge bg-warning">En attente</span></td>
                                </tr>
                                <tr>
                                    <td>27/01/2024</td>
                                    <td>ENCA-2024-002</td>
                                    <td><span class="badge bg-success">Encaissement</span></td>
                                    <td><span class="badge bg-secondary">Caisse Mobile</span></td>
                                    <td>Paiement client</td>
                                    <td class="text-success fw-bold">1 500 000</td>
                                    <td>-</td>
                                    <td class="fw-bold">10 000 000</td>
                                    <td><span class="badge bg-success">Validé</span></td>
                                </tr>
                                <tr>
                                    <td>26/01/2024</td>
                                    <td>DECA-2024-002</td>
                                    <td><span class="badge bg-danger">Décaissement</span></td>
                                    <td><span class="badge bg-info">Caisse Principale</span></td>
                                    <td>Paiement salaires</td>
                                    <td>-</td>
                                    <td class="text-danger fw-bold">2 000 000</td>
                                    <td class="fw-bold">8 500 000</td>
                                    <td><span class="badge bg-success">Validé</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Précédent</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Suivant</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique d'évolution -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Évolution de la Trésorerie</h5>
                </div>
                <div class="card-body">
                    <canvas id="evolutionChart" width="400" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique de répartition par caisse
    const caisseCtx = document.getElementById('caisseChart');
    if (caisseCtx) {
        new Chart(caisseCtx, {
            type: 'doughnut',
            data: {
                labels: ['Caisse Principale', 'Caisse Secondaire', 'Caisse Mobile', 'BIAO', 'ECOBANK'],
                datasets: [{
                    data: [15000000, 5000000, 3000000, 2000000, 5000000],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 99, 132, 0.8)'
                    ]
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

    // Graphique de répartition par type
    const typeCtx = document.getElementById('typeChart');
    if (typeCtx) {
        new Chart(typeCtx, {
            type: 'pie',
            data: {
                labels: ['Encaissements', 'Décaissements', 'Virements'],
                datasets: [{
                    data: [12000000, 8500000, 4500000],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)'
                    ]
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

    // Graphique d'évolution
    const evolutionCtx = document.getElementById('evolutionChart');
    if (evolutionCtx) {
        new Chart(evolutionCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'],
                datasets: [{
                    label: 'Solde Trésorerie',
                    data: [15000000, 18000000, 16000000, 20000000, 22000000, 19000000, 25000000, 23000000, 26000000, 24000000, 27000000, 25000000],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return (value / 1000000).toFixed(1) + 'M FCFA';
                            }
                        }
                    }
                }
            }
        });
    }

    // Fonction de recherche
    function filterTable() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const type = document.getElementById('typeFilter').value;
        const caisse = document.getElementById('caisseFilter').value;
        const date = document.getElementById('dateFilter').value;

        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            const text = cells[1].textContent.toLowerCase() + ' ' + cells[4].textContent.toLowerCase();
            const rowType = cells[2].textContent.trim();
            const rowCaisse = cells[3].textContent.trim();
            const rowDate = cells[0].textContent;

            let show = true;

            if (search && !text.includes(search)) show = false;
            if (type && !rowType.includes(type)) show = false;
            if (caisse && !rowCaisse.includes(caisse)) show = false;
            if (date && !rowDate.includes(date)) show = false;

            row.style.display = show ? '' : 'none';
        });
    }

    // Écouteurs d'événements
    document.getElementById('searchInput').addEventListener('input', filterTable);
    document.getElementById('typeFilter').addEventListener('change', filterTable);
    document.getElementById('caisseFilter').addEventListener('change', filterTable);
    document.getElementById('dateFilter').addEventListener('change', filterTable);
});

function chargerRapport() {
    const periode = document.getElementById('periode').value;
    const type = document.getElementById('type_rapport').value;
    const format = document.getElementById('format_export').value;
    
    console.log('Chargement du rapport:', { periode, type, format });
    
    // Simulation de chargement
    alert(`Rapport ${type} pour la période ${periode} en format ${format}`);
}

function exporterRapport() {
    const periode = document.getElementById('periode').value;
    const type = document.getElementById('type_rapport').value;
    const format = document.getElementById('format_export').value;
    
    console.log('Export du rapport:', { periode, type, format });
    
    // Simulation d'export
    alert(`Export du rapport en ${format} pour la période ${periode}`);
}
</script>

@endsection
