@extends('layouts.app')

@section('title', 'Dashboard Missions | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-project-diagram me-2 text-primary"></i>Dashboard Missions Logistiques
            </h1>
            <p class="text-muted mb-0">Vue synthèse des missions, avancement, coûts et alertes</p>
        </div>
        <a href="{{ route('projets.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nouvelle Mission</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Missions Actives</div>
                            <div class="h3 mb-0 text-primary">{{ $stats['en_cours'] ?? 0 }}</div>
                            <div class="d-flex align-items-center mt-1">
                                <small class="text-success me-1">
                                    <i class="fas fa-arrow-up"></i> +12%
                                </small>
                                <small class="text-muted">vs mois dernier</small>
                            </div>
                        </div>
                        <div class="text-primary opacity-25">
                            <i class="fas fa-tasks fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Taux de Réussite</div>
                            <div class="h3 mb-0 text-success">{{ round(($stats['termines'] ?? 0) / max(1, ($stats['total'] ?? 1)) * 100) }}%</div>
                            <div class="d-flex align-items-center mt-1">
                                <small class="text-success me-1">
                                    <i class="fas fa-arrow-up"></i> +5%
                                </small>
                                <small class="text-muted">vs trimestre</small>
                            </div>
                        </div>
                        <div class="text-success opacity-25">
                            <i class="fas fa-check-circle fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Coût Logistique Total</div>
                            <div class="h3 mb-0 text-warning">{{ isset($stats['budget_total']) ? round($stats['budget_total'] / 1000000, 1) . 'M' : '0M' }} FCFA</div>
                            <div class="d-flex align-items-center mt-1">
                                <small class="text-warning me-1">
                                    <i class="fas fa-minus"></i> Stable
                                </small>
                                <small class="text-muted">ce mois</small>
                            </div>
                        </div>
                        <div class="text-warning opacity-25">
                            <i class="fas fa-coins fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Délai Moyen</div>
                            <div class="h3 mb-0 text-info">{{ $stats['delai_moyen'] ?? 0 }} jours</div>
                            <div class="d-flex align-items-center mt-1">
                                <small class="text-success me-1">
                                    <i class="fas fa-arrow-down"></i> -2j
                                </small>
                                <small class="text-muted">vs moyenne</small>
                            </div>
                        </div>
                        <div class="text-info opacity-25">
                            <i class="fas fa-calendar fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Secondaires -->
    <div class="row mb-4">
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body p-3">
                    <div class="text-muted small text-uppercase fw-bold">En Planification</div>
                    <div class="h4 mb-0 text-primary">{{ $stats['planification'] ?? 0 }}</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-primary" style="width: 45%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body p-3">
                    <div class="text-muted small text-uppercase fw-bold">En Attente</div>
                    <div class="h4 mb-0 text-info">{{ $stats['en_attente'] ?? 0 }}</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-info" style="width: 20%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body p-3">
                    <div class="text-muted small text-uppercase fw-bold">En Retard</div>
                    <div class="h4 mb-0 text-danger">{{ $stats['retard'] ?? 0 }}</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-danger" style="width: 8%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body p-3">
                    <div class="text-muted small text-uppercase fw-bold">Budget Utilisé</div>
                    <div class="h4 mb-0 text-warning">{{ isset($stats['budget_reel']) ? round($stats['budget_reel'] / 1000000, 1) : '0' }}M</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: 67%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body p-3">
                    <div class="text-muted small text-uppercase fw-bold">Total Missions</div>
                    <div class="h4 mb-0 text-secondary">{{ $stats['total'] ?? 0 }}</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-secondary" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body p-3">
                    <div class="text-muted small text-uppercase fw-bold">Efficacité</div>
                    <div class="h4 mb-0 text-success">94%</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: 94%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques principaux -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-chart-area me-2"></i>Évolution des Missions par Statut
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 350px;">
                        <canvas id="missionsEvolutionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Répartition des Missions par Statut
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="statutMissionPieChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="row text-center small">
                            <div class="col-6 mb-2">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-circle text-primary me-2"></i>
                                    <span>Planification: <strong>{{ $stats['planification'] ?? 0 }}</strong></span>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-circle text-success me-2"></i>
                                    <span>En cours: <strong>{{ $stats['en_cours'] ?? 0 }}</strong></span>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-circle text-warning me-2"></i>
                                    <span>Clôturées: <strong>{{ $stats['termines'] ?? 0 }}</strong></span>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-circle text-info me-2"></i>
                                    <span>En attente: <strong>{{ $stats['en_attente'] ?? 0 }}</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Diagramme de Gantt - Timeline des Missions -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-project-diagram me-2"></i>Timeline des Missions - Diagramme de Gantt
                    </h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" onclick="changeGanttView('month')">Mois</button>
                        <button type="button" class="btn btn-outline-primary" onclick="changeGanttView('quarter')">Trimestre</button>
                        <button type="button" class="btn btn-outline-primary" onclick="changeGanttView('year')">Année</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="gantt-container" style="position: relative; height: 400px; overflow-x: auto;">
                        <canvas id="ganttChart"></canvas>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <div class="small text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Timeline interactive des missions avec dates de début et de fin prévues
                        </div>
                        <button class="btn btn-sm btn-outline-primary" onclick="exportGantt()">
                            <i class="fas fa-download me-1"></i>Exporter Gantt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="fas fa-truck fa-3x text-success mb-3"></i>
                    <h6>Engins Affectés</h6>
                    <p class="text-muted small">Suivi des engins par mission</p>
                    <a href="{{ route('projets.index') }}" class="btn btn-sm btn-success"><i class="fas fa-arrow-right me-2"></i>Accéder</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                    <h6>Fournisseurs</h6>
                    <p class="text-muted small">Liste et performance fournisseurs</p>
                    <a href="{{ route('projets.index') }}" class="btn btn-sm btn-primary"><i class="fas fa-arrow-right me-2"></i>Accéder</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="fas fa-clipboard-check fa-3x text-info mb-3"></i>
                    <h6>Pointages</h6>
                    <p class="text-muted small">Suivi des heures/jours par mission</p>
                    <a href="{{ route('projets.index') }}" class="btn btn-sm btn-info"><i class="fas fa-arrow-right me-2"></i>Accéder</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="fas fa-user-cog fa-3x text-success mb-3"></i>
                    <h6>Affectation Ressources</h6>
                    <p class="text-muted small">Gérer véhicules & personnel</p>
                    <a href="{{ route('projets.index') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-arrow-right me-2"></i>Accéder
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
<script>
// Données générales
const months = @json($months ?? []);
const opsPerMonth = @json($opsPerMonth ?? []);
const amountPerMonth = @json($amountPerMonth ?? []);

// Données pour le camembert
const statutData = {
    labels: ['Planification', 'En cours', 'Clôturées', 'En attente'],
    datasets: [{
        data: [
            {{ $stats['planification'] ?? 0 }},
            {{ $stats['en_cours'] ?? 0 }},
            {{ $stats['termines'] ?? 0 }},
            {{ $stats['en_attente'] ?? 0 }}
        ],
        backgroundColor: [
            '#0d6efd', // Primaire
            '#198754', // Succès
            '#ffc107', // Warning
            '#17a2b8'  // Info
        ],
        borderWidth: 2,
        borderColor: '#fff'
    }]
};

// Données pour le Gantt
const ganttData = @json($ganttData ?? []);

// Graphique principal
const ctxProjets = document.getElementById('missionsEvolutionChart');
if (ctxProjets) {
    new Chart(ctxProjets, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Missions créées',
                data: opsPerMonth,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Montant (FCFA)',
                data: amountPerMonth,
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                },
            }
        }
    });
}

// Camembert - Répartition des missions par statut
const ctxPie = document.getElementById('statutMissionPieChart');
if (ctxPie) {
    new Chart(ctxPie, {
        type: 'pie',
        data: statutData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

// Diagramme de Gantt
const ctxGantt = document.getElementById('ganttChart');
if (ctxGantt) {
    // Utiliser les données réelles ou données de démonstration si vide
    const ganttChartData = ganttData.length > 0 ? ganttData : [
        {
            titre: 'Mission A',
            statut: 'en_cours',
            date_debut: '2026-01-15',
            date_fin: '2026-02-20',
            responsable: 'Agent A'
        },
        {
            titre: 'Mission B',
            statut: 'planification',
            date_debut: '2026-02-01',
            date_fin: '2026-03-15',
            responsable: 'Agent B'
        },
        {
            titre: 'Mission C',
            statut: 'en_cours',
            date_debut: '2026-01-20',
            date_fin: '2026-02-28',
            responsable: 'Agent C'
        }
    ];

    new Chart(ctxGantt, {
        type: 'bar',
        data: {
            labels: ganttChartData.map(item => item.titre),
            datasets: [{
                label: 'Timeline',
                data: ganttChartData.map(item => {
                    const debut = new Date(item.date_debut);
                    const fin = new Date(item.date_fin);
                    return {
                        x: [debut.getTime(), fin.getTime()],
                        y: item.titre
                    };
                }),
                backgroundColor: ganttChartData.map(item => {
                    switch(item.statut) {
                        case 'en_cours': return 'rgba(25, 135, 84, 0.8)';
                        case 'terminee': return 'rgba(13, 110, 253, 0.8)';
                        case 'planification': return 'rgba(255, 193, 7, 0.8)';
                        default: return 'rgba(108, 117, 125, 0.8)';
                    }
                }),
                borderWidth: 1,
                borderColor: '#fff',
                barPercentage: 0.6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    type: 'linear',
                    position: 'bottom',
                    min: function(context) {
                        const dates = ganttChartData.flatMap(item => [
                            new Date(item.date_debut).getTime(),
                            new Date(item.date_fin).getTime()
                        ]);
                        const minDate = new Date(Math.min(...dates));
                        return minDate.getTime() - (7 * 24 * 60 * 60 * 1000); // 7 jours avant
                    },
                    max: function(context) {
                        const dates = ganttChartData.flatMap(item => [
                            new Date(item.date_debut).getTime(),
                            new Date(item.date_fin).getTime()
                        ]);
                        const maxDate = new Date(Math.max(...dates));
                        return maxDate.getTime() + (7 * 24 * 60 * 60 * 1000); // 7 jours après
                    },
                    ticks: {
                        callback: function(value) {
                            return new Date(value).toLocaleDateString('fr-FR', {
                                day: '2-digit',
                                month: 'short'
                            });
                        }
                    },
                    title: {
                        display: true,
                        text: 'Timeline',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        },
                        label: function(context) {
                            const item = ganttChartData[context.dataIndex];
                            return [
                                'Début: ' + new Date(item.date_debut).toLocaleDateString('fr-FR'),
                                'Fin: ' + new Date(item.date_fin).toLocaleDateString('fr-FR'),
                                'Statut: ' + item.statut,
                                'Responsable: ' + item.responsable,
                                'Durée: ' + Math.ceil((new Date(item.date_fin) - new Date(item.date_debut)) / (1000 * 60 * 60 * 24)) + ' jours'
                            ];
                        }
                    }
                }
            }
        }
    });
}

// Fonctions pour le Gantt
function changeGanttView(view) {
    const ctxGantt = document.getElementById('ganttChart');
    const chart = Chart.getChart(ctxGantt);

    if (chart && ganttData.length > 0) {
        let filteredData = [...ganttData];
        const now = new Date();

        switch(view) {
            case 'month':
                const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);
                const monthEnd = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                filteredData = ganttData.filter(item => {
                    const debut = new Date(item.date_debut);
                    const fin = new Date(item.date_fin);
                    return (debut <= monthEnd && fin >= monthStart);
                });
                break;
            case 'quarter':
                const quarterStart = new Date(now.getFullYear(), Math.floor(now.getMonth() / 3) * 3, 1);
                const quarterEnd = new Date(now.getFullYear(), Math.floor(now.getMonth() / 3) * 3 + 3, 0);
                filteredData = ganttData.filter(item => {
                    const debut = new Date(item.date_debut);
                    const fin = new Date(item.date_fin);
                    return (debut <= quarterEnd && fin >= quarterStart);
                });
                break;
            case 'year':
                const yearStart = new Date(now.getFullYear(), 0, 1);
                const yearEnd = new Date(now.getFullYear(), 11, 31);
                filteredData = ganttData.filter(item => {
                    const debut = new Date(item.date_debut);
                    const fin = new Date(item.date_fin);
                    return (debut <= yearEnd && fin >= yearStart);
                });
                break;
        }

        // Mettre à jour le graphique
        chart.data.labels = filteredData.map(item => item.titre);
        chart.data.datasets[0].data = filteredData.map(item => {
            const debut = new Date(item.date_debut);
            const fin = new Date(item.date_fin);
            return {
                x: [debut.getTime(), fin.getTime()],
                y: item.titre
            };
        });
        chart.data.datasets[0].backgroundColor = filteredData.map(item => {
            switch(item.statut) {
                case 'en_cours': return 'rgba(25, 135, 84, 0.8)';
                case 'terminee': return 'rgba(13, 110, 253, 0.8)';
                case 'planification': return 'rgba(255, 193, 7, 0.8)';
                default: return 'rgba(108, 117, 125, 0.8)';
            }
        });
        chart.update();
    }

    // Mettre à jour les boutons actifs
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
}

function exportGantt() {
    const canvas = document.getElementById('ganttChart');
    if (canvas) {
        const link = document.createElement('a');
        link.download = 'gantt-diagramme-' + new Date().toISOString().split('T')[0] + '.png';
        link.href = canvas.toDataURL();
        link.click();
    }
}
</script>
