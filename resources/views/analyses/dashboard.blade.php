@extends('layouts.app')

@section('title', 'Analyses & Reporting - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-line me-2 text-primary"></i>Analyses & Reporting
            </h1>
            <p class="text-muted mb-0">Tableaux de bord et rapports d'analyse</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('analyses.exports.ca-entrepot') }}">
                <i class="fas fa-file-csv me-2"></i>Export CA + Entrepôt (CSV)
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-file-export me-2"></i>Exporter
            </button>
            <button class="btn btn-success" onclick="genererRapport()">
                <i class="fas fa-file-pdf me-2"></i>Rapport PDF
            </button>
        </div>
    </div>

    <!-- Filtres Période -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Période</label>
                    <select class="form-select" id="periodFilter" onchange="chargerDonnees()">
                        <option value="today">Aujourd'hui</option>
                        <option value="week">Cette semaine</option>
                        <option value="month" selected>Ce mois</option>
                        <option value="quarter">Ce trimestre</option>
                        <option value="year">Cette année</option>
                        <option value="custom">Personnalisé</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Date Début</label>
                    <input type="date" class="form-control" id="dateDebut" value="{{ date('Y-m-01') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Date Fin</label>
                    <input type="date" class="form-control" id="dateFin" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" onclick="appliquerFiltres()">
                        <i class="fas fa-filter me-2"></i>Appliquer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Analytiques (données dynamiques via $kpis) -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Chiffre d'Affaires</div>
                            @php $ca = $kpis['financial']['total_revenue'] ?? 0; @endphp
                            <div class="h3 mb-0 text-primary" id="kpiCA">{{ number_format($ca, 0, ',', ' ') }} FCFA</div>
                            <div class="small text-muted mt-1">
                                Basé sur les factures de la période
                            </div>
                        </div>
                        <i class="fas fa-money-bill-wave fa-3x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Marge Brute</div>
                            @php
                                $revenu = $kpis['financial']['total_revenue'] ?? 0;
                                $depenses = $kpis['financial']['total_expenses'] ?? 0;
                                $margeBrute = max($revenu - $depenses, 0);
                            @endphp
                            <div class="h3 mb-0 text-success" id="kpiMarge">{{ number_format($margeBrute, 0, ',', ' ') }} FCFA</div>
                            <div class="small text-muted mt-1">
                                Revenus - dépenses
                            </div>
                        </div>
                        <i class="fas fa-chart-pie fa-3x text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Coût Moyen/Opération</div>
                            @php
                                $totalOps = $kpis['operations']['total'] ?? 0;
                                $coutMoyen = $totalOps > 0 ? ($depenses / $totalOps) : 0;
                            @endphp
                            <div class="h3 mb-0 text-warning" id="kpiCout">{{ number_format($coutMoyen, 0, ',', ' ') }} FCFA</div>
                            <div class="small text-muted mt-1">
                                Dépenses / nb d'opérations
                            </div>
                        </div>
                        <i class="fas fa-calculator fa-3x text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">ROI Moyen</div>
                            @php $roi = $kpis['financial']['profit_margin'] ?? 0; @endphp
                            <div class="h3 mb-0 text-info" id="kpiROI">{{ $roi }}%</div>
                            <div class="small text-muted mt-1">
                                Marge nette / CA
                            </div>
                        </div>
                        <i class="fas fa-percentage fa-3x text-info opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Entrepôt & Magasin -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-secondary border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Articles en Stock</div>
                            <div class="h3 mb-0 text-secondary">{{ $kpis['warehouse']['total_products'] ?? 0 }}</div>
                            <small class="text-muted">Références actives</small>
                        </div>
                        <i class="fas fa-warehouse fa-3x text-secondary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Valeur Stock</div>
                            @php $stockValue = $kpis['warehouse']['stock_value'] ?? 0; @endphp
                            <div class="h3 mb-0 text-success">{{ number_format($stockValue, 0, ',', ' ') }} FCFA</div>
                            <small class="text-muted">Valeur totale estimée</small>
                        </div>
                        <i class="fas fa-truck-ramp-box fa-3x text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Alertes Stock Faible</div>
                            <div class="h3 mb-0 text-primary">{{ $kpis['warehouse']['low_stock_alerts'] ?? 0 }}</div>
                            <small class="text-muted">Articles sous seuil</small>
                        </div>
                        <i class="fas fa-dolly fa-3x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-danger border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Rotation Stock</div>
                            <div class="h3 mb-0 text-danger">{{ $kpis['warehouse']['turnover_rate'] ?? 0 }}%</div>
                            <small class="text-muted">Ventes / stock moyen</small>
                        </div>
                        <i class="fas fa-triangle-exclamation fa-3x text-danger opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques Principaux -->
    <div class="row mb-4">
        <!-- Évolution CA -->
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-area me-2"></i>Évolution du Chiffre d'Affaires
                    </h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active">Mensuel</button>
                        <button type="button" class="btn btn-outline-primary">Trimestriel</button>
                        <button type="button" class="btn btn-outline-primary">Annuel</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="caChart" height="70"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Clients -->
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-trophy me-2"></i>Top 5 Clients
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3" id="topClients">
                        <div class="text-center text-muted">
                            Section analytique à connecter aux données clients.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analyses Détaillées -->
    <div class="row mb-4">
        <!-- Rentabilité par Service -->
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Rentabilité par Service
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="rentabiliteChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Entrées vs Sorties (Entrepôt) -->
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-boxes-stacked me-2"></i>Entrepôt: Entrées vs Sorties
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="entrepotFluxChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableaux de Données -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-table me-2"></i>Détails des Opérations
                    </h6>
                    <button class="btn btn-sm btn-outline-primary" onclick="exporterTableau()">
                        <i class="fas fa-download me-2"></i>Exporter CSV
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tableOperations">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Référence</th>
                                    <th>Client</th>
                                    <th>Service</th>
                                    <th>Montant</th>
                                    <th>Coût</th>
                                    <th>Marge</th>
                                    <th>ROI</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                @forelse($operations as $op)
                                    <tr>
                                        <td>{{ optional($op->created_at)->format('d/m/Y') }}</td>
                                        <td><span class="badge bg-primary">{{ $op->numero_projet ?? $op->reference ?? 'N/A' }}</span></td>
                                        <td>{{ optional($op->client)->nom ?? optional($op->client)->name ?? 'N/A' }}</td>
                                        <td><span class="badge bg-secondary">{{ $op->service ?? '-' }}</span></td>
                                        <td class="fw-bold text-success">-</td>
                                        <td class="text-danger">-</td>
                                        <td class="fw-bold text-info">-</td>
                                        <td><span class="badge bg-success">-</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Aucune opération trouvée pour cette période.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Export -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-export me-2"></i>Exporter les Données
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Format d'export</label>
                    <select class="form-select" id="exportFormat">
                        <option value="excel">Excel (.xlsx)</option>
                        <option value="csv">CSV (.csv)</option>
                        <option value="pdf">PDF (.pdf)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Données à exporter</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="exportCA" checked>
                        <label class="form-check-label" for="exportCA">Chiffre d'affaires</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="exportOperations" checked>
                        <label class="form-check-label" for="exportOperations">Opérations</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="exportClients" checked>
                        <label class="form-check-label" for="exportClients">Clients</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="exportVehicules">
                        <label class="form-check-label" for="exportVehicules">Véhicules</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="confirmerExport()">
                    <i class="fas fa-download me-2"></i>Exporter
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Les graphiques peuvent être branchés plus tard sur des données réelles.
// Pour l'instant, aucun dataset de démonstration n'est chargé.
</script>
@endsection
