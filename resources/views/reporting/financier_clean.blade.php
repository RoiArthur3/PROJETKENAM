@extends('layouts.app')

@section('title', 'Reporting Financier - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Reporting Financier
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Navigation rapide -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="btn-group" role="group">
                                <a href="{{ route('reporting.dashboard') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                                <a href="{{ route('reporting.financier') }}" class="btn btn-success active">
                                    <i class="fas fa-chart-line me-2"></i>Financier
                                </a>
                                <a href="{{ route('reporting.operations') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-cogs me-2"></i>Opérations
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Filtres -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Période</label>
                            <select class="form-select">
                                <option>Ce mois</option>
                                <option>Mois dernier</option>
                                <option>Ce trimestre</option>
                                <option>Cette année</option>
                                <option>Personnalisé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date début</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date fin</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-2"></i>Filtrer
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- KPIs Financiers -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="text-white-50 mb-1">Revenus Totaux</h6>
                                            <h3 class="mb-0">2 450 000 FCFA</h3>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-arrow-up fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="text-white-50 mb-1">Dépenses Totales</h6>
                                            <h3 class="mb-0">1 230 000 FCFA</h3>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-arrow-down fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="text-white-50 mb-1">Bénéfice Net</h6>
                                            <h3 class="mb-0">1 220 000 FCFA</h3>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-balance-scale fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="text-white-50 mb-1">Marge Bénéfice</h6>
                                            <h3 class="mb-0">49.8%</h3>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-percentage fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphique et Tableau -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-area me-2"></i>
                                        Évolution des Revenus
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center py-5">
                                        <i class="fas fa-chart-area fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Graphique d'évolution des revenus</p>
                                        <small class="text-muted">Intégration Chart.js prévue</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-pie-chart me-2"></i>
                                        Répartition des Dépenses
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center py-5">
                                        <i class="fas fa-pie-chart fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Graphique circulaire des dépenses</p>
                                        <small class="text-muted">Intégration Chart.js prévue</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des transactions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-list me-2"></i>
                                        Transactions Récentes
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Type</th>
                                                    <th>Description</th>
                                                    <th>Montant</th>
                                                    <th>Statut</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>03/02/2026</td>
                                                    <td><span class="badge bg-success">Revenu</span></td>
                                                    <td>Facture #2026-001</td>
                                                    <td class="text-success">+250 000 FCFA</td>
                                                    <td><span class="badge bg-success">Payé</span></td>
                                                </tr>
                                                <tr>
                                                    <td>02/02/2026</td>
                                                    <td><span class="badge bg-danger">Dépense</span></td>
                                                    <td>Carburant véhicule</td>
                                                    <td class="text-danger">-45 000 FCFA</td>
                                                    <td><span class="badge bg-success">Payé</span></td>
                                                </tr>
                                                <tr>
                                                    <td>01/02/2026</td>
                                                    <td><span class="badge bg-success">Revenu</span></td>
                                                    <td>Facture #2026-002</td>
                                                    <td class="text-success">+180 000 FCFA</td>
                                                    <td><span class="badge bg-warning">En attente</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
