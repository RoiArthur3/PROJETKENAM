@extends('layouts.app')

@section('title', 'Anomalies - Contrôle & Audit - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-bug me-2 text-primary"></i>Anomalies Détectées
            </h1>
            <p class="text-muted mb-0">Suivi des anomalies et actions correctives</p>
        </div>
        <a href="{{ route('controle-audit.planifs') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Signaler Anomalie
        </a>
    </div>

    <!-- KPIs -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-danger border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Anomalies critiques</div>
                            <div class="h3 mb-0 text-danger">5</div>
                            <small class="text-danger">Action immédiate requise</small>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-3x text-danger opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">En cours de traitement</div>
                            <div class="h3 mb-0 text-warning">12</div>
                            <small class="text-warning">Actions en cours</small>
                        </div>
                        <i class="fas fa-tools fa-3x text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Corrigées ce mois</div>
                            <div class="h3 mb-0 text-success">8</div>
                            <small class="text-success"><i class="fas fa-arrow-up"></i> +20%</small>
                        </div>
                        <i class="fas fa-check-circle fa-3x text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Temps moyen résolution</div>
                            <div class="h3 mb-0 text-info">3.2j</div>
                            <small class="text-muted">Objectif < 5j</small>
                        </div>
                        <i class="fas fa-clock fa-3x text-info opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des anomalies -->
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 fw-bold text-primary">Anomalies actives</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Description</th>
                            <th>Module/Service</th>
                            <th>Severité</th>
                            <th>Statut</th>
                            <th>Détecté le</th>
                            <th>Responsable</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>ANO-2024-001</strong></td>
                            <td>Erreur calcul frais transport</td>
                            <td>Module Transport</td>
                            <td><span class="badge bg-danger">Critique</span></td>
                            <td><span class="badge bg-warning">En cours</span></td>
                            <td>05/11/2024</td>
                            <td>Équipe Dev</td>
                            <td>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success">
                                    <i class="fas fa-check"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>ANO-2024-002</strong></td>
                            <td>Doublon références produits</td>
                            <td>Gestion Stock</td>
                            <td><span class="badge bg-warning">Moyenne</span></td>
                            <td><span class="badge bg-success">Résolue</span></td>
                            <td>03/11/2024</td>
                            <td>Marie Curie</td>
                            <td>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>ANO-2024-003</strong></td>
                            <td>Interface dashboard lente</td>
                            <td>Dashboard Global</td>
                            <td><span class="badge bg-info">Mineure</span></td>
                            <td><span class="badge bg-warning">En cours</span></td>
                            <td>01/11/2024</td>
                            <td>Équipe UX</td>
                            <td>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success">
                                    <i class="fas fa-check"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
