@extends('layouts.app')

@section('title', 'Reporting Opérations - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Reporting Opérations
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
                                <a href="{{ route('reporting.financier') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-chart-line me-2"></i>Financier
                                </a>
                                <a href="{{ route('reporting.operations') }}" class="btn btn-warning active">
                                    <i class="fas fa-cogs me-2"></i>Opérations
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Filtres -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Type d'opération</label>
                            <select class="form-select">
                                <option>Tous les types</option>
                                <option>Transport</option>
                                <option>Maintenance</option>
                                <option>Carburant</option>
                                <option>Piéces détachées</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select">
                                <option>Tous les statuts</option>
                                <option>En cours</option>
                                <option>Terminé</option>
                                <option>Annulé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Période</label>
                            <select class="form-select">
                                <option>Cette semaine</option>
                                <option>Ce mois</option>
                                <option>Ce trimestre</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-2"></i>Filtrer
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- KPIs Opérations -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="text-white-50 mb-1">Total Opérations</h6>
                                            <h3 class="mb-0">156</h3>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-tasks fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="text-white-50 mb-1">Opérations Actives</h6>
                                            <h3 class="mb-0">42</h3>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-play fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="text-white-50 mb-1">En Attente</h6>
                                            <h3 class="mb-0">18</h3>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-clock fa-2x opacity-75"></i>
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
                                            <h6 class="text-white-50 mb-1">Taux Succès</h6>
                                            <h3 class="mb-0">87.5%</h3>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphiques -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-bar me-2"></i>
                                        Opérations par Mois
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center py-5">
                                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Graphique des opérations mensuelles</p>
                                        <small class="text-muted">Intégration Chart.js prévue</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-pie me-2"></i>
                                        Répartition par Type
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center py-5">
                                        <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Graphique circulaire des types</p>
                                        <small class="text-muted">Intégration Chart.js prévue</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des opérations -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-list me-2"></i>
                                        Liste des Opérations
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Client</th>
                                                    <th>Type</th>
                                                    <th>Véhicule</th>
                                                    <th>Date Début</th>
                                                    <th>Statut</th>
                                                    <th>Montant</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>#OP-2026-001</td>
                                                    <td>Société ABC</td>
                                                    <td><span class="badge bg-primary">Transport</span></td>
                                                    <td>Toyota Hilux</td>
                                                    <td>03/02/2026 08:00</td>
                                                    <td><span class="badge bg-success">En cours</span></td>
                                                    <td>150 000 FCFA</td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button class="btn btn-outline-primary" title="Voir">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button class="btn btn-outline-warning" title="Modifier">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>#OP-2026-002</td>
                                                    <td>Entreprise XYZ</td>
                                                    <td><span class="badge bg-warning">Maintenance</span></td>
                                                    <td>Mercedes Sprinter</td>
                                                    <td>02/02/2026 14:30</td>
                                                    <td><span class="badge bg-info">Terminé</span></td>
                                                    <td>75 000 FCFA</td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button class="btn btn-outline-primary" title="Voir">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button class="btn btn-outline-warning" title="Modifier">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>#OP-2026-003</td>
                                                    <td>Client Particulier</td>
                                                    <td><span class="badge bg-success">Carburant</span></td>
                                                    <td>Nissan Patrol</td>
                                                    <td>01/02/2026 10:15</td>
                                                    <td><span class="badge bg-secondary">En attente</span></td>
                                                    <td>25 000 FCFA</td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button class="btn btn-outline-primary" title="Voir">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button class="btn btn-outline-warning" title="Modifier">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Pagination -->
                                    <nav class="mt-3">
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
