@extends('layouts.app')

@section('title', 'Tableau de Bord - KENAM SERVICES')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.css">
<style>
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    .kpi-icon {
        font-size: 2rem;
        opacity: 0.7;
    }
    .activity-item {
        border-left: 3px solid #4e73df;
        padding-left: 15px;
        margin-bottom: 15px;
    }
    .activity-item.warning {
        border-left-color: #f6c23e;
    }
    .activity-item.success {
        border-left-color: #1cc88a;
    }
    .activity-item.danger {
        border-left-color: #e74a3b;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tableau de Bord</h1>
        <div class="d-none d-sm-inline-block">
            <span class="mr-2">{{ now()->format('d F Y') }}</span>
            <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Exporter le rapport
            </button>
        </div>
    </div>

    <!-- Cartes KPI -->
    <div class="row">
        <!-- Opérations du jour -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 card-hover">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Opérations du Jour</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">24</div>
                            <div class="mt-2 text-xs text-muted">
                                <i class="fas fa-arrow-up text-success"></i> 12% par rapport à hier
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Véhicules en cours d'utilisation -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 card-hover">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Véhicules en cours</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">18/45</div>
                            <div class="mt-2">
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 40%" 
                                         aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck-pickup fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Livraisons en attente -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 card-hover">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Livraisons en attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">8</div>
                            <div class="mt-2">
                                <span class="badge badge-warning">3 en retard</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shipping-fast fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenus du mois -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 card-hover">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Revenus du mois</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">12,450,000 FCFA</div>
                            <div class="mt-2 text-xs text-muted">
                                <i class="fas fa-arrow-up text-success"></i> 8.2% par rapport au mois dernier
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et activités -->
    <div class="row">
        <!-- Graphique des opérations -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Activité des opérations</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Options :</div>
                            <a class="dropdown-item" href="#">Mensuel</a>
                            <a class="dropdown-item" href="#">Hebdomadaire</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Exporter les données</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <div id="operationsChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Répartition par Services -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition par Services</h6>
                </div>
                <div class="card-body">
                    <div id="servicesChart" style="height: 300px;"></div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded mr-2" style="width: 12px; height: 12px;"></div>
                                <span class="small">Transport</span>
                            </div>
                            <strong class="small">45%</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded mr-2" style="width: 12px; height: 12px;"></div>
                                <span class="small">Logistique</span>
                            </div>
                            <strong class="small">30%</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning rounded mr-2" style="width: 12px; height: 12px;"></div>
                                <span class="small">Maintenance</span>
                            </div>
                            <strong class="small">15%</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-info rounded mr-2" style="width: 12px; height: 12px;"></div>
                                <span class="small">Autres</span>
                            </div>
                            <strong class="small">10%</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques Financiers -->
    <div class="row">
        <!-- Revenus vs Dépenses -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Revenus vs Dépenses (6 derniers mois)</h6>
                </div>
                <div class="card-body">
                    <div id="financialChart" style="height: 300px;"></div>
                </div>
            </div>
        </div>

        <!-- Répartition des Dépenses -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition des Dépenses</h6>
                </div>
                <div class="card-body">
                    <div id="expensesChart" style="height: 300px;"></div>
                    <div class="mt-3">
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">Carburant</span>
                                <strong class="small">4.2M FCFA</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger" style="width: 35%"></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">Maintenance</span>
                                <strong class="small">2.8M FCFA</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: 23%"></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">Salaires</span>
                                <strong class="small">3.5M FCFA</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" style="width: 29%"></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">Autres</span>
                                <strong class="small">1.5M FCFA</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-secondary" style="width: 13%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Ligne inférieure -->
<div class="row">
    <!-- Activités récentes -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Activités récentes</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" href="#">Tout afficher</a>
                        <a class="dropdown-item" href="#">Filtrer par type</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Exporter</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="activity-item">
                    <div class="d-flex justify-content-between">
                        <h6 class="font-weight-bold">Nouvelle opération créée</h6>
                        <small class="text-muted">Il y a 5 min</small>
                    </div>
                    <p class="mb-1">Livraison pour SOCIETE ABC • 12 palettes • Abidjan → Bouaké</p>
                    <div class="d-flex">
                        <span class="badge badge-primary mr-2">#OP-2023-4567</span>
                        <span class="badge badge-info">En attente</span>
                    </div>
                </div>

                <div class="activity-item success">
                    <div class="d-flex justify-content-between">
                        <h6 class="font-weight-bold">Livraison effectuée</h6>
                        <small class="text-muted">Il y a 1h</small>
                    </div>
                    <p class="mb-1">Commande #CMD-7890 livrée avec succès à Yopougon</p>
                    <div class="d-flex">
                        <span class="badge badge-success">Terminé</span>
                    </div>
                </div>

                <div class="activity-item warning">
                    <div class="d-flex justify-content-between">
                        <h6 class="font-weight-bold">Maintenance préventive</h6>
                        <small class="text-muted">Aujourd'hui, 09:30</small>
                    </div>
                    <p class="mb-1">Véhicule AB-123-CD • Vidange et révision des freins</p>
                    <div class="d-flex">
                        <span class="badge badge-warning">En cours</span>
                    </div>
                </div>

                <div class="activity-item danger">
                    <div class="d-flex justify-content-between">
                        <h6 class="font-weight-bold">Retard de livraison</h6>
                        <small class="text-muted">Hier, 18:45</small>
                    </div>
                    <p class="mb-1">Commande #CMD-7889 en retard de 2h30 • Problème mécanique</p>
                    <div class="d-flex">
                        <span class="badge badge-danger">En retard</span>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="#" class="btn btn-sm btn-link">Voir toutes les activités</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Prochaines échéances -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Prochaines échéances</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <h6 class="mb-1">Assurance véhicule AB-123-CD</h6>
                            <small class="text-muted">Expire dans 5 jours</small>
                        </div>
                        <span class="badge badge-warning badge-pill">Urgent</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <h6 class="mb-1">Contrôle technique 6 mois</h6>
                            <small class="text-muted">Véhicule CD-789-EF • 12 jours restants</small>
                        </div>
                        <span class="badge badge-info badge-pill">À planifier</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <h6 class="mb-1">Paiement fournisseur</h6>
                            <small class="text-muted">SOCIETE XYZ • 450,000 FCFA • 3 jours</small>
                        </div>
                        <span class="badge badge-primary badge-pill">En attente</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <h6 class="mb-1">Formation sécurité routière</h6>
                            <small class="text-muted">Tous les conducteurs • 15 jours</small>
                        </div>
                        <span class="badge badge-secondary badge-pill">Planifié</span>
                    </li>
                </ul>
                <div class="mt-3">
                    <button class="btn btn-sm btn-outline-primary">Voir le calendrier complet</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Indicateurs de performance -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Indicateurs de performance</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Taux de livraison à temps</span>
                        <strong>92%</strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 92%" aria-valuenow="92" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Taux d'utilisation des véhicules</span>
                        <strong>78%</strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 78%" aria-valuenow="78" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Coût moyen par km</span>
                        <strong>450 FCFA</strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Satisfaction client</span>
                        <strong>4.5/5</strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 90%" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Graphique des opérations (ApexCharts)
document.addEventListener('DOMContentLoaded', function() {
    // Graphique des opérations mensuelles
    var options = {
        series: [{
            name: 'Livraisons',
            data: [31, 40, 28, 51, 42, 82, 56, 71, 93, 69, 85, 110]
        }, {
            name: 'Retours',
            data: [11, 32, 45, 32, 34, 52, 41, 23, 15, 27, 35, 18]
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: true,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: false,
                    reset: true
                }
            }
        },
        colors: ['#4e73df', '#e74a3b'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            categories: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
        },
        tooltip: {
            x: {
                format: 'MMM'
            },
        },
        legend: {
            position: 'top'
        },
        markers: {
            size: 4,
            hover: {
                size: 6
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#operationsChart"), options);
    chart.render();

    // Graphique des stocks (Chart.js)
    var ctx = document.getElementById('stocksChart');
    if (ctx) {
        var myPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ["Pièces détachées", "Lubrifiants", "Pneus", "Outillage", "Divers"],
                datasets: [{
                    data: [35, 25, 20, 10, 10],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                    }
                },
                cutoutPercentage: 70,
            },
        });
    }

    // Mise à jour en temps réel des indicateurs
    function updateKPIs() {
        // Simulation de mises à jour en temps réel
        const kpiValues = {
            'operations': Math.floor(Math.random() * 10) + 20, // 20-30
            'vehicles': Math.floor(Math.random() * 5) + 15,    // 15-20
            'deliveries': Math.floor(Math.random() * 5) + 5,   // 5-10
            'revenue': Math.floor(Math.random() * 2000000) + 10000000 // 10-12M
        };

        // Mise à jour des valeurs
        document.querySelector('.card:first-child .h5').textContent = kpiValues.operations;
        document.querySelectorAll('.card')[1].querySelector('.h5').textContent = 
            `${kpiValues.vehicles}/45`;
        document.querySelectorAll('.card')[2].querySelector('.h5').textContent = 
            kpiValues.deliveries;
        document.querySelectorAll('.card')[3].querySelector('.h5').textContent = 
            `${(kpiValues.revenue / 1000000).toFixed(2).replace(/\./g, ',')} M FCFA`;

        // Mise à jour des barres de progression
        document.querySelectorAll('.card')[1].querySelector('.progress-bar').style.width = 
            `${Math.round((kpiValues.vehicles / 45) * 100)}%`;
    }

    // Mettre à jour les KPI toutes les 30 secondes
    setInterval(updateKPIs, 30000);
});

// Gestion des cartes interactives
document.querySelectorAll('.card-hover').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
        this.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = '0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15)';
    });
});
</script>
@endpush
        </div>
    </div>

    <!-- Recent Notifications and Quick Actions -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Notifications Récentes</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">Nouvelle opération assignée</h5>
                                <small>Il y a 2h</small>
                            </div>
                            <p class="mb-1">Opération #123 a été assignée à votre service.</p>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">Stock faible</h5>
                                <small>Il y a 4h</small>
                            </div>
                            <p class="mb-1">Le stock de carburant est faible.</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Accès Rapide</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <a href="{{ route('operations.requetes.create') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-plus fa-fw"></i> Nouvelle requête
                            </a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ route('rh.agents') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-users fa-fw"></i> Gérer Utilisateurs
                            </a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ route('fournisseurs.index') }}" class="btn btn-info btn-block">
                                <i class="fas fa-truck fa-fw"></i> Fournisseurs
                            </a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ route('stock.produits') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-warehouse fa-fw"></i> Stocks
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
    // Chart.js for Operations
    var ctx1 = document.getElementById('operationsChart').getContext('2d');
    var operationsChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
            datasets: [{
                label: 'Opérations',
                data: [145, 178, 165, 195, 210, 225, 240, 235, 255, 270, 285, 295],
                borderColor: 'rgba(40, 167, 69, 1)',
                backgroundColor: 'rgba(40, 167, 69, 0.12)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Services Distribution Chart (Doughnut)
    var ctxServices = document.getElementById('servicesChart').getContext('2d');
    var servicesChart = new Chart(ctxServices, {
        type: 'doughnut',
        data: {
            labels: ['Transport', 'Logistique', 'Maintenance', 'Autres'],
            datasets: [{
                data: [45, 30, 15, 10],
                backgroundColor: ['#28a745', '#1cc88a', '#f6c23e', '#36b9cc'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Financial Chart (Revenue vs Expenses)
    var ctxFinancial = document.getElementById('financialChart').getContext('2d');
    var financialChart = new Chart(ctxFinancial, {
        type: 'bar',
        data: {
            labels: ['Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            datasets: [{
                label: 'Revenus',
                data: [15200000, 18500000, 16800000, 19200000, 17500000, 21000000],
                backgroundColor: 'rgba(28, 200, 138, 0.8)',
                borderColor: 'rgba(28, 200, 138, 1)',
                borderWidth: 1
            }, {
                label: 'Dépenses',
                data: [12000000, 13500000, 12800000, 14200000, 13000000, 15500000],
                backgroundColor: 'rgba(231, 74, 59, 0.8)',
                borderColor: 'rgba(231, 74, 59, 1)',
                borderWidth: 1
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
                            return (value / 1000000) + 'M FCFA';
                        }
                    }
                }
            }
        }
    });

    // Expenses Distribution Chart
    var ctxExpenses = document.getElementById('expensesChart').getContext('2d');
    var expensesChart = new Chart(ctxExpenses, {
        type: 'pie',
        data: {
            labels: ['Carburant', 'Maintenance', 'Salaires', 'Autres'],
            datasets: [{
                data: [4200000, 2800000, 3500000, 1500000],
                backgroundColor: ['#e74a3b', '#f6c23e', '#36b9cc', '#858796'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Chart.js for Stocks
    var ctx2 = document.getElementById('stocksChart').getContext('2d');
    var stocksChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Service A', 'Service B', 'Service C'],
            datasets: [{
                data: [30, 50, 20],
                backgroundColor: ['#28A745', '#FFC107', '#DC3545']
            }]
        }
    });
</script>
@endsection
